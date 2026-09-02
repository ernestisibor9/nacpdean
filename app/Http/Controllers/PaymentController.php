<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipCategoryFee;
use App\Models\MemberFee;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Document;
use App\Models\GeneratedDocument;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\DocumentGenerationService;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAYMENT PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        $isApproved =
            $profile &&
            strtolower(trim($profile->status ?? '')) === 'approved';

        $memberType =
            $profile?->member_type
            ?? $user->member_type
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | BALANCE
        |--------------------------------------------------------------------------
        */

        $balance = $this->getBalance($user->id);

        /*
        |--------------------------------------------------------------------------
        | UNPAID DEBITS
        |--------------------------------------------------------------------------
        */

        $unpaidDebits = Transaction::with('paymentItem')
            ->where('user_id', $user->id)
            ->where('type', 'debit')
            ->where('status', 'not paid')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CURRENT MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | users.membership_category_id is authoritative.
        |
        | If membership_category_id != NULL,
        | the user is a member.
        |
        */

        $currentCategory = null;

        if ($user->membership_category_id !== null) {
            $currentCategory = MembershipCategory::where(
                'id',
                $user->membership_category_id
            )
                ->where('status', true)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY SELECTION
        |--------------------------------------------------------------------------
        |
        | Category is already determined from the user.
        |
        */

        $categories = collect();

        /*
        |--------------------------------------------------------------------------
        | CURRENT MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $membership = Membership::where(
            'user_id',
            $user->id
        )
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE ANNUAL MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $hasActiveAnnualMembership =
            $membership &&
            $membership->status === 'active' &&
            $membership->expires_at &&
            \Carbon\Carbon::parse($membership->expires_at)->isFuture();

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP FEE - DISPLAY ONLY
        |--------------------------------------------------------------------------
        */

        $membershipFee = null;

        if ($currentCategory) {
            $feeType = $membership
                ? 'existing'
                : 'new';

            $membershipFee = MembershipCategoryFee::where(
                'membership_category_id',
                $currentCategory->id
            )
                ->where('status', true)
                ->where('fee_type', $feeType)
                ->first();

            if (!$membershipFee) {
                $membershipFee = MembershipCategoryFee::where(
                    'membership_category_id',
                    $currentCategory->id
                )
                    ->where('status', true)
                    ->where('fee_type', 'standard')
                    ->first();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER FEES
        |--------------------------------------------------------------------------
        */

        $memberFees = collect();

        if ($isApproved) {
            $memberFees = MemberFee::where(
                'user_id',
                $user->id
            )
                ->where('status', 'unpaid')
                ->where(function ($query) {
                    $query
                        ->whereNull('due_date')
                        ->orWhereDate(
                            'due_date',
                            '>=',
                            now()->toDateString()
                        );
                })
                ->latest()
                ->get();
        }

        return view(
            'member.payment.index',
            compact(
                'categories',
                'profile',
                'isApproved',
                'memberFees',
                'membershipFee',
                'currentCategory',
                'memberType',
                'balance',
                'unpaidDebits',
                'membership',
                'hasActiveAnnualMembership'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE NORMAL PAYMENT
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | This method NEVER creates a debit transaction.
    |
    | The debit must already exist in transactions.
    |
    */

    public function initialize(Request $request)
    {
        $request->validate([
            'payment_option' => [
                'required',
                'string',
            ],

            'transaction_id' => [
                'nullable',
                'integer',
                'exists:transactions,id',
            ],
        ]);

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | FIND EXISTING UNPAID DEBIT
        |--------------------------------------------------------------------------
        */

        $debit = $this->findUnpaidDebit(
            $user->id,
            $request
        );

        if (!$debit) {
            return back()
                ->withErrors([
                    'payment' =>
                    'No unpaid payment was found for this request. Please refresh the page and try again.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE PAYMENT RECORD
        |--------------------------------------------------------------------------
        */

        try {
            $paymentData = DB::transaction(
                function () use (
                    $debit,
                    $user,
                    $request
                ) {
                    $lockedDebit = Transaction::with(
                        'paymentItem'
                    )
                        ->where('id', $debit->id)
                        ->where('user_id', $user->id)
                        ->where('type', 'debit')
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedDebit) {
                        throw new \RuntimeException(
                            'Payment transaction could not be found.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ONLY UNPAID DEBITS CAN BE INITIALIZED
                    |--------------------------------------------------------------------------
                    */

                    if ($lockedDebit->status !== 'not paid') {
                        throw new \RuntimeException(
                            'This payment has already been processed.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | AMOUNT
                    |--------------------------------------------------------------------------
                    */

                    $amount = (float) $lockedDebit->amount;

                    if ($amount <= 0) {
                        throw new \RuntimeException(
                            'The payment amount is invalid.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PAYSTACK REFERENCE
                    |--------------------------------------------------------------------------
                    */

                    $gateway = is_array($lockedDebit->gateway)
                        ? $lockedDebit->gateway
                        : [];

                    $reference = $gateway['paystack'] ?? null;

                    if (!$reference) {
                        $reference =
                            'NACP-' .
                            strtoupper(Str::random(20));

                        $gateway['paystack'] = $reference;

                        $lockedDebit->update([
                            'gateway' => $gateway,
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT INFORMATION
                    |--------------------------------------------------------------------------
                    */

                    $paymentItem = $lockedDebit->paymentItem;

                    $membershipCategoryId =
                        $user->membership_category_id;

                    $membershipCategoryFeeId = null;
                    $memberFeeId = null;

                    $paymentType = 'membership';
                    $feeType = 'standard';

                    /*
                    |--------------------------------------------------------------------------
                    | MEMBER FEE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        Str::startsWith(
                            $request->payment_option,
                            'member_fee_'
                        )
                    ) {
                        $memberFeeId = (int) Str::after(
                            $request->payment_option,
                            'member_fee_'
                        );

                        if (!$memberFeeId) {
                            throw new \RuntimeException(
                                'Invalid member fee.'
                            );
                        }

                        $memberFee = MemberFee::where(
                            'id',
                            $memberFeeId
                        )
                            ->where(
                                'user_id',
                                $user->id
                            )
                            ->where(
                                'status',
                                'unpaid'
                            )
                            ->first();

                        if (!$memberFee) {
                            throw new \RuntimeException(
                                'The selected member fee is not available.'
                            );
                        }

                        $paymentType = 'member_fee';

                        $feeType =
                            $memberFee->fee_type
                            ?? 'standard';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | MEMBERSHIP / RENEWAL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        in_array(
                            $request->payment_option,
                            [
                                'membership',
                                'renewal',
                            ],
                            true
                        )
                    ) {
                        $paymentType = 'membership';

                        $membership = Membership::where(
                            'user_id',
                            $user->id
                        )
                            ->latest()
                            ->first();

                        if (
                            $request->payment_option ===
                            'renewal'
                        ) {
                            $feeType = 'existing';
                        } elseif (!$membership) {
                            $feeType = 'new';
                        } else {
                            $feeType = 'existing';
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | FIND CATEGORY FEE
                        |--------------------------------------------------------------------------
                        */

                        if ($membershipCategoryId !== null) {
                            $categoryFee =
                                MembershipCategoryFee::where(
                                    'membership_category_id',
                                    $membershipCategoryId
                                )
                                ->where('status', true)
                                ->where('fee_type', $feeType)
                                ->first();

                            if (!$categoryFee) {
                                $categoryFee =
                                    MembershipCategoryFee::where(
                                        'membership_category_id',
                                        $membershipCategoryId
                                    )
                                    ->where('status', true)
                                    ->where(
                                        'fee_type',
                                        'standard'
                                    )
                                    ->first();
                            }

                            if ($categoryFee) {
                                $membershipCategoryFeeId =
                                    $categoryFee->id;
                            }
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DESCRIPTION
                    |--------------------------------------------------------------------------
                    */

                    $description =
                        $lockedDebit->narration;

                    if ($paymentItem) {
                        $description =
                            $paymentItem->name;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FIND PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    $payment = Payment::where(
                        'reference',
                        $reference
                    )
                        ->lockForUpdate()
                        ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    if (!$payment) {
                        $payment = Payment::create([
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem?->id,

                            'membership_category_id' =>
                            $membershipCategoryId,

                            'membership_category_fee_id' =>
                            $membershipCategoryFeeId,

                            'member_fee_id' =>
                            $memberFeeId,

                            'payment_type' =>
                            $paymentType,

                            'fee_type' =>
                            $feeType,

                            'amount' =>
                            $amount,

                            'description' =>
                            $description,

                            'payment_reference' =>
                            $reference,

                            'paystack_reference' =>
                            $reference,

                            'reference' =>
                            $reference,

                            'gateway' =>
                            'paystack',

                            'gateway_transaction_id' =>
                            null,

                            'gateway_status' =>
                            'pending',

                            'status' =>
                            'pending',
                        ]);
                    } else {
                        $payment->update([
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem?->id,

                            'membership_category_id' =>
                            $membershipCategoryId,

                            'membership_category_fee_id' =>
                            $membershipCategoryFeeId,

                            'member_fee_id' =>
                            $memberFeeId,

                            'payment_type' =>
                            $paymentType,

                            'fee_type' =>
                            $feeType,

                            'amount' =>
                            $amount,

                            'description' =>
                            $description,

                            'payment_reference' =>
                            $reference,

                            'paystack_reference' =>
                            $reference,

                            'gateway' =>
                            'paystack',

                            'gateway_status' =>
                            'pending',

                            'status' =>
                            'pending',
                        ]);
                    }

                    return [
                        'debit' =>
                        $lockedDebit,

                        'payment' =>
                        $payment,

                        'reference' =>
                        $reference,

                        'amount' =>
                        $amount,
                    ];
                }
            );

            $debit =
                $paymentData['debit'];

            $payment =
                $paymentData['payment'];

            $reference =
                $paymentData['reference'];

            $amount =
                $paymentData['amount'];
        } catch (\Throwable $e) {
            Log::error(
                'PAYMENT INITIALIZATION LOCK FAILED',
                [
                    'user_id' =>
                    $user->id,

                    'transaction_id' =>
                    $debit->id,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),
                ]
            );

            return back()
                ->withErrors([
                    'payment' =>
                    $e->getMessage(),
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | PAYSTACK INITIALIZATION
        |--------------------------------------------------------------------------
        */

        try {
            $response = Http::withToken(
                config(
                    'services.paystack.secret_key'
                )
            )
                ->acceptJson()
                ->post(
                    config(
                        'services.paystack.url'
                    ) . '/transaction/initialize',
                    [
                        'email' =>
                        $user->email,

                        'amount' =>
                        (int) round(
                            $amount * 100
                        ),

                        'reference' =>
                        $reference,

                        'currency' =>
                        'NGN',

                        'callback_url' =>
                        route(
                            'payment.callback'
                        ),

                        'metadata' => [
                            'transaction_id' =>
                            $debit->id,

                            'payment_id' =>
                            $payment->id,

                            'payment_item_id' =>
                            $debit->payment_item_id,

                            'payment_option' =>
                            $request->payment_option,

                            'user_id' =>
                            $user->id,

                            'membership_category_id' =>
                            $user->membership_category_id,
                        ],
                    ]
                );

            if (!$response->successful()) {
                Log::error(
                    'PAYSTACK INITIALIZATION FAILED',
                    [
                        'transaction_id' =>
                        $debit->id,

                        'payment_id' =>
                        $payment->id,

                        'reference' =>
                        $reference,

                        'response' =>
                        $response->json(),
                    ]
                );

                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $response->json(),
                ]);

                return back()
                    ->withErrors([
                        'payment' =>
                        'Unable to initialize payment with Paystack.',
                    ])
                    ->withInput();
            }

            $data = $response->json();

            if (
                ($data['status'] ?? false) !== true
            ) {
                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $data,
                ]);

                return back()
                    ->withErrors([
                        'payment' =>
                        $data['message']
                            ?? 'Paystack payment initialization failed.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | AUTHORIZATION URL
            |--------------------------------------------------------------------------
            */

            $authorizationUrl =
                $data['data']['authorization_url']
                ?? null;

            if (!$authorizationUrl) {
                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $data,
                ]);

                return back()
                    ->withErrors([
                        'payment' =>
                        'Paystack did not return a payment URL.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE PAYSTACK RESPONSE
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'paystack_authorization_url' =>
                $authorizationUrl,

                'gateway_status' =>
                'initialized',

                'gateway_response' =>
                $data,

                'status' =>
                'pending',
            ]);

            return redirect(
                $authorizationUrl
            );
        } catch (\Throwable $e) {
            Log::error(
                'PAYSTACK INITIALIZATION EXCEPTION',
                [
                    'transaction_id' =>
                    $debit->id,

                    'payment_id' =>
                    $payment->id,

                    'reference' =>
                    $reference,

                    'error' =>
                    $e->getMessage(),
                ]
            );

            try {
                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',
                ]);
            } catch (\Throwable $updateException) {
                Log::error(
                    'PAYMENT FAILED STATUS UPDATE ERROR',
                    [
                        'payment_id' =>
                        $payment->id,

                        'error' =>
                        $updateException->getMessage(),
                    ]
                );
            }

            return back()
                ->withErrors([
                    'payment' =>
                    'An error occurred while connecting to Paystack.',
                ])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FIND UNPAID DEBIT
    |--------------------------------------------------------------------------
    */

    private function findUnpaidDebit(
        int $userId,
        Request $request
    ): ?Transaction {
        /*
        |--------------------------------------------------------------------------
        | DIRECT TRANSACTION ID
        |--------------------------------------------------------------------------
        */

        if ($request->filled('transaction_id')) {
            return Transaction::with('paymentItem')
                ->where(
                    'id',
                    $request->transaction_id
                )
                ->where(
                    'user_id',
                    $userId
                )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'not paid'
                )
                ->first();
        }

        $paymentOption =
            $request->payment_option;

        /*
        |--------------------------------------------------------------------------
        | MEMBER FEE
        |--------------------------------------------------------------------------
        */

        if (
            Str::startsWith(
                $paymentOption,
                'member_fee_'
            )
        ) {
            $memberFeeId = (int) Str::after(
                $paymentOption,
                'member_fee_'
            );

            if (!$memberFeeId) {
                return null;
            }

            $memberFee = MemberFee::where(
                'id',
                $memberFeeId
            )
                ->where(
                    'user_id',
                    $userId
                )
                ->where(
                    'status',
                    'unpaid'
                )
                ->first();

            if (!$memberFee) {
                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | PAYMENT ITEM LINK IS PREFERRED
            |--------------------------------------------------------------------------
            */

            $memberFeePaymentItem = PaymentItem::where(
                'name',
                $memberFee->name
            )->first();

            if ($memberFeePaymentItem) {
                $debit = Transaction::with('paymentItem')
                    ->where(
                        'user_id',
                        $userId
                    )
                    ->where(
                        'type',
                        'debit'
                    )
                    ->where(
                        'status',
                        'not paid'
                    )
                    ->where(
                        'payment_item_id',
                        $memberFeePaymentItem->id
                    )
                    ->latest()
                    ->first();

                if ($debit) {
                    return $debit;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK TO AMOUNT + NARRATION
            |--------------------------------------------------------------------------
            */

            return Transaction::with('paymentItem')
                ->where(
                    'user_id',
                    $userId
                )
                ->where(
                    'type',
                    'debit'
                )
                ->where(
                    'status',
                    'not paid'
                )
                ->where(
                    'amount',
                    $memberFee->amount
                )
                ->where(function ($query) use (
                    $memberFee
                ) {
                    $query
                        ->where(
                            'narration',
                            'like',
                            '%' .
                                $memberFee->name .
                                '%'
                        )
                        ->orWhere(
                            'narration',
                            'like',
                            '%' .
                                $memberFee->fee_type .
                                '%'
                        );
                })
                ->latest()
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP / RENEWAL
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $paymentOption,
                [
                    'membership',
                    'renewal',
                ],
                true
            )
        ) {
            return null;
        }

        $user = User::find($userId);

        if (!$user) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $categoryId =
            $user->membership_category_id;

        if ($categoryId === null) {
            Log::warning(
                'MEMBERSHIP CATEGORY MISSING FROM USER',
                [
                    'user_id' =>
                    $userId,
                ]
            );

            return null;
        }

        $category = MembershipCategory::where(
            'id',
            $categoryId
        )
            ->where(
                'status',
                true
            )
            ->first();

        if (!$category) {
            Log::warning(
                'MEMBERSHIP CATEGORY NOT FOUND',
                [
                    'user_id' =>
                    $userId,

                    'membership_category_id' =>
                    $categoryId,
                ]
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $membership = Membership::where(
            'user_id',
            $userId
        )
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | FEE TYPE
        |--------------------------------------------------------------------------
        */

        if ($paymentOption === 'renewal') {
            $feeType = 'existing';
        } elseif (!$membership) {
            $feeType = 'new';
        } else {
            $feeType = 'existing';
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY FEE
        |--------------------------------------------------------------------------
        */

        $categoryFee = MembershipCategoryFee::where(
            'membership_category_id',
            $category->id
        )
            ->where(
                'status',
                true
            )
            ->where(
                'fee_type',
                $feeType
            )
            ->first();

        if (!$categoryFee) {
            $categoryFee = MembershipCategoryFee::where(
                'membership_category_id',
                $category->id
            )
                ->where(
                    'status',
                    true
                )
                ->where(
                    'fee_type',
                    'standard'
                )
                ->first();
        }

        if (!$categoryFee) {
            Log::warning(
                'MEMBERSHIP CATEGORY FEE NOT FOUND',
                [
                    'user_id' =>
                    $userId,

                    'membership_category_id' =>
                    $category->id,

                    'fee_type' =>
                    $feeType,
                ]
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | FIND EXISTING UNPAID DEBIT
        |--------------------------------------------------------------------------
        */

        return Transaction::with('paymentItem')
            ->where(
                'user_id',
                $userId
            )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'status',
                'not paid'
            )
            ->where(
                'amount',
                $categoryFee->amount
            )
            ->where(
                'narration',
                'like',
                '%' .
                    $category->name .
                    '%'
            )
            ->latest()
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | PAYSTACK CALLBACK
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request)
    {
        $reference =
            $request->query('reference');

        Log::info(
            'PAYSTACK CALLBACK HIT',
            [
                'reference' =>
                $reference,

                'user_id' =>
                Auth::id(),
            ]
        );

        if (!$reference) {
            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'No Paystack payment reference was received.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VERIFY PAYSTACK
        |--------------------------------------------------------------------------
        */

        try {
            $response = Http::withToken(
                config(
                    'services.paystack.secret_key'
                )
            )
                ->acceptJson()
                ->get(
                    config(
                        'services.paystack.url'
                    ) .
                        '/transaction/verify/' .
                        urlencode($reference)
                );

            if (!$response->successful()) {
                Log::error(
                    'PAYSTACK VERIFY HTTP FAILED',
                    [
                        'reference' =>
                        $reference,

                        'status' =>
                        $response->status(),

                        'body' =>
                        $response->body(),
                    ]
                );

                return redirect()
                    ->route('payment.index')
                    ->withErrors([
                        'payment' =>
                        'Unable to verify your payment with Paystack.',
                    ]);
            }

            $data = $response->json();

            if (
                ($data['status'] ?? false) !== true ||
                ($data['data']['status'] ?? null) !== 'success'
            ) {
                return redirect()
                    ->route('payment.index')
                    ->withErrors([
                        'payment' =>
                        'Payment was not successful.',
                    ]);
            }

            $paystackTransaction =
                $data['data'];

            /*
            |--------------------------------------------------------------------------
            | FIND PAYMENT
            |--------------------------------------------------------------------------
            */

            $payment = Payment::where(
                'reference',
                $reference
            )->first();

            /*
            |--------------------------------------------------------------------------
            | FIND DEBIT USING OUR DATABASE ID
            |--------------------------------------------------------------------------
            */

            $debit = null;

            $metadataTransactionId =
                $paystackTransaction['metadata']['transaction_id']
                ?? null;

            if ($metadataTransactionId) {
                $debit = Transaction::with(
                    'paymentItem'
                )
                    ->where(
                        'id',
                        $metadataTransactionId
                    )
                    ->where(
                        'type',
                        'debit'
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK: GATEWAY JSON
            |--------------------------------------------------------------------------
            */

            if (!$debit) {
                $debit = Transaction::with(
                    'paymentItem'
                )
                    ->where(
                        'type',
                        'debit'
                    )
                    ->whereJsonContains(
                        'gateway->paystack',
                        $reference
                    )
                    ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK: PAYMENT ITEM / PAYMENT RECORD
            |--------------------------------------------------------------------------
            */

            if (!$debit && $payment) {
                $debit = Transaction::with(
                    'paymentItem'
                )
                    ->where(
                        'user_id',
                        $payment->user_id
                    )
                    ->where(
                        'type',
                        'debit'
                    )
                    ->where(
                        'status',
                        'not paid'
                    )
                    ->where(
                        'amount',
                        $payment->amount
                    )
                    ->where(
                        'payment_item_id',
                        $payment->payment_item_id
                    )
                    ->latest()
                    ->first();
            }

            if (!$debit) {
                Log::critical(
                    'PAYSTACK DEBIT NOT FOUND',
                    [
                        'reference' =>
                        $reference,

                        'payment_id' =>
                        $payment?->id,

                        'paystack_transaction' =>
                        $paystackTransaction,
                    ]
                );

                return redirect()
                    ->route('payment.index')
                    ->withErrors([
                        'payment' =>
                        'The payment transaction could not be found.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | USER SECURITY
            |--------------------------------------------------------------------------
            */

            if (
                !Auth::check() ||
                (int) $debit->user_id !==
                (int) Auth::id()
            ) {
                Log::critical(
                    'PAYSTACK USER MISMATCH',
                    [
                        'transaction_id' =>
                        $debit->id,

                        'transaction_user_id' =>
                        $debit->user_id,

                        'authenticated_user_id' =>
                        Auth::id(),

                        'reference' =>
                        $reference,
                    ]
                );

                abort(403);
            }

            /*
            |--------------------------------------------------------------------------
            | CURRENCY
            |--------------------------------------------------------------------------
            */

            $currency = strtoupper(
                (string) (
                    $paystackTransaction['currency']
                    ?? ''
                )
            );

            if ($currency !== 'NGN') {
                return redirect()
                    ->route('payment.index')
                    ->withErrors([
                        'payment' =>
                        'The payment currency could not be verified.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | AMOUNT
            |--------------------------------------------------------------------------
            */

            $expectedAmount = (int) round(
                ((float) $debit->amount) * 100
            );

            $paidAmount = (int) (
                $paystackTransaction['amount']
                ?? 0
            );

            if ($expectedAmount !== $paidAmount) {
                Log::critical(
                    'PAYSTACK AMOUNT MISMATCH',
                    [
                        'transaction_id' =>
                        $debit->id,

                        'reference' =>
                        $reference,

                        'expected_amount' =>
                        $expectedAmount,

                        'paid_amount' =>
                        $paidAmount,
                    ]
                );

                return redirect()
                    ->route('payment.index')
                    ->withErrors([
                        'payment' =>
                        'The payment amount could not be verified.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SETTLE LEDGER
            |--------------------------------------------------------------------------
            */

            $result = DB::transaction(
                function () use (
                    $debit,
                    $paystackTransaction,
                    $reference
                ) {
                    $lockedDebit = Transaction::with(
                        'paymentItem'
                    )
                        ->where(
                            'id',
                            $debit->id
                        )
                        ->where(
                            'type',
                            'debit'
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedDebit) {
                        throw new \RuntimeException(
                            'Debit transaction could not be found.'
                        );
                    }

                    $payment = Payment::where(
                        'reference',
                        $reference
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$payment) {
                        $user = User::find(
                            $lockedDebit->user_id
                        );

                        if (!$user) {
                            throw new \RuntimeException(
                                'Payment user could not be found.'
                            );
                        }

                        $payment = Payment::create([
                            'user_id' =>
                            $lockedDebit->user_id,

                            'payment_item_id' =>
                            $lockedDebit->payment_item_id,

                            'membership_category_id' =>
                            $user->membership_category_id,

                            'membership_category_fee_id' =>
                            null,

                            'member_fee_id' =>
                            null,

                            'payment_type' =>
                            'membership',

                            'fee_type' =>
                            'standard',

                            'amount' =>
                            $lockedDebit->amount,

                            'description' =>
                            $lockedDebit->narration,

                            'payment_reference' =>
                            $reference,

                            'paystack_reference' =>
                            $reference,

                            'reference' =>
                            $reference,

                            'gateway' =>
                            'paystack',

                            'gateway_transaction_id' =>
                            null,

                            'gateway_status' =>
                            'success',

                            'status' =>
                            'pending',
                        ]);
                    }

                    $existingCredit =
                        Transaction::where(
                            'debit_transaction_id',
                            $lockedDebit->id
                        )
                        ->where(
                            'type',
                            'credit'
                        )
                        ->lockForUpdate()
                        ->first();

                    $payment->update([
                        'paystack_reference' =>
                        $reference,

                        'reference' =>
                        $reference,

                        'gateway_transaction_id' =>
                        null,

                        'gateway_status' =>
                        $paystackTransaction['status']
                            ?? 'success',

                        'gateway_response' =>
                        $paystackTransaction,

                        'status' =>
                        'paid',

                        'paid_at' =>
                        now(),

                        'verified_at' =>
                        now(),
                    ]);

                    if (
                        $lockedDebit->status === 'paid' &&
                        $existingCredit
                    ) {
                        return [
                            'credit' =>
                            $existingCredit,

                            'payment' =>
                            $payment,

                            'newly_settled' =>
                            false,
                        ];
                    }

                    if ($existingCredit) {
                        $gateway =
                            is_array(
                                $lockedDebit->gateway
                            )
                            ? $lockedDebit->gateway
                            : [];

                        $gateway['paystack'] =
                            $reference;

                        $lockedDebit->update([
                            'status' =>
                            'paid',

                            'gateway' =>
                            $gateway,
                        ]);

                        return [
                            'credit' =>
                            $existingCredit,

                            'payment' =>
                            $payment,

                            'newly_settled' =>
                            false,
                        ];
                    }

                    $credit = Transaction::create([
                        'user_id' =>
                        $lockedDebit->user_id,

                        'payment_item_id' =>
                        $lockedDebit->payment_item_id,

                        'debit_transaction_id' =>
                        $lockedDebit->id,

                        'narration' =>
                        'Payment received - ' .
                            $lockedDebit->narration,

                        'type' =>
                        'credit',

                        'status' =>
                        null,

                        'amount' =>
                        $lockedDebit->amount,

                        'transaction_id' =>
                        null,

                        'gateway' => [
                            'paystack' =>
                            $reference,
                        ],
                    ]);

                    $gateway =
                        is_array(
                            $lockedDebit->gateway
                        )
                        ? $lockedDebit->gateway
                        : [];

                    $gateway['paystack'] =
                        $reference;

                    $lockedDebit->update([
                        'status' =>
                        'paid',

                        'gateway' =>
                        $gateway,
                    ]);

                    $this->processSuccessfulPayment(
                        $lockedDebit,
                        $credit,
                        $paystackTransaction,
                        $reference
                    );

                    return [
                        'credit' =>
                        $credit,

                        'payment' =>
                        $payment,

                        'newly_settled' =>
                        true,
                    ];
                }
            );

            if ($result['newly_settled']) {
                return redirect()
                    ->route('payment.success')
                    ->with(
                        'success',
                        'Payment was successful.'
                    );
            }

            return redirect()
                ->route('payment.success')
                ->with(
                    'success',
                    'Payment has already been processed successfully.'
                );
        } catch (\Throwable $e) {
            Log::error(
                'PAYSTACK CALLBACK EXCEPTION',
                [
                    'reference' =>
                    $reference,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),
                ]
            );

            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'An error occurred while verifying your payment.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS SUCCESSFUL PAYMENT
    |--------------------------------------------------------------------------
    */

    private function processSuccessfulPayment(
        Transaction $debit,
        Transaction $credit,
        array $paystackTransaction,
        string $reference
    ): void {
        $paymentItem =
            $debit->paymentItem;

        if (!$paymentItem) {
            Log::info(
                'SUCCESSFUL PAYMENT WITHOUT PAYMENT ITEM',
                [
                    'transaction_id' =>
                    $debit->id,

                    'credit_id' =>
                    $credit->id,

                    'reference' =>
                    $reference,
                ]
            );

            return;
        }

        $code = strtoupper(
            trim(
                (string) $paymentItem->code
            )
        );

        $type = strtolower(
            trim(
                (string) $paymentItem->type
            )
        );

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'membership' ||
            Str::contains(
                strtolower(
                    (string) $debit->narration
                ),
                [
                    'membership',
                    'renewal',
                ]
            )
        ) {
            Log::info(
                'MEMBERSHIP PAYMENT COMPLETED',
                [
                    'transaction_id' =>
                    $debit->id,

                    'credit_transaction_id' =>
                    $credit->id,

                    'user_id' =>
                    $debit->user_id,

                    'membership_category_id' =>
                    User::where(
                        'id',
                        $debit->user_id
                    )->value(
                        'membership_category_id'
                    ),

                    'amount' =>
                    $debit->amount,

                    'reference' =>
                    $reference,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OPERATIONAL RIGHTS
        |--------------------------------------------------------------------------
        */

        $documentCodes = [
            'CLR',
            'CLR-RCG',
            'CDR-SLR',
            'CDR-DEA',
            'CPR',
        ];

        if (
            in_array(
                $code,
                $documentCodes,
                true
            )
        ) {
            Log::info(
                'OPERATIONAL RIGHTS PAYMENT COMPLETED',
                [
                    'transaction_id' =>
                    $debit->id,

                    'credit_transaction_id' =>
                    $credit->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'payment_item_code' =>
                    $code,

                    'reference' =>
                    $reference,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BALANCE API
    |--------------------------------------------------------------------------
    */

    public function balance()
    {
        $user = Auth::user();

        $balance = $this->getBalance(
            $user->id
        );

        return response()->json([
            'balance' =>
            number_format(
                $balance,
                2,
                '.',
                ''
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET BALANCE
    |--------------------------------------------------------------------------
    |
    | BALANCE = TOTAL CREDITS - TOTAL DEBITS
    |
    */

    private function getBalance(
        int $userId
    ): float {
        $credits = Transaction::where(
            'user_id',
            $userId
        )
            ->where(
                'type',
                'credit'
            )
            ->sum('amount');

        $debits = Transaction::where(
            'user_id',
            $userId
        )
            ->where(
                'type',
                'debit'
            )
            ->sum('amount');

        return round(
            (float) $credits -
                (float) $debits,
            2
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT SUCCESS PAGE
    |--------------------------------------------------------------------------
    */

    public function success()
    {
        $user = Auth::user();

        $credit = Transaction::with([
            'paymentItem',
            'debitTransaction',
        ])
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'type',
                'credit'
            )
            ->whereNotNull(
                'debit_transaction_id'
            )
            ->latest()
            ->first();

        if (!$credit) {
            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'No successful payment was found.',
                ]);
        }

        $debit =
            $credit->debitTransaction;

        $balance =
            $this->getBalance(
                $user->id
            );

        return view(
            'member.payment.success',
            compact(
                'credit',
                'debit',
                'balance'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADDITIONAL PAYMENTS PAGE
    |--------------------------------------------------------------------------
    |
    | IMPORTANT MEMBER RULE:
    |
    | A user is a member when:
    |
    | users.membership_category_id != NULL
    |
    | For members:
    |
    | payment_items.membership_category_id
    | MUST EXACTLY MATCH
    | users.membership_category_id
    |
    | NULL payment-item categories are NOT included.
    |
    */

    /*
|--------------------------------------------------------------------------
| ADDITIONAL PAYMENTS PAGE
|--------------------------------------------------------------------------
|
| ONLY APPROVED MEMBERS WITH ACTIVE MEMBERSHIP.
|
| MEMBER RULE:
|
| users.membership_category_id != NULL
|
| AND
|
| payment_items.membership_category_id
| =
| users.membership_category_id
|
| NULL payment-item categories are NEVER included.
|
| IMPORTANT:
|
| A payment item does NOT need to already have a transaction.
| The transaction/debit will be created by initializeAdditional()
| when the member chooses to pay.
|
*/

    /*
|--------------------------------------------------------------------------
| ADDITIONAL PAYMENTS PAGE
|--------------------------------------------------------------------------
|
| ONLY approved members with active membership.
|
| MEMBER RULE:
|
| users.membership_category_id != NULL
|
| AND
|
| payment_items.membership_category_id
| =
| users.membership_category_id
|
| NULL payment-item categories are NOT included.
|
*/

    public function additionalPayments()
    {
        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | MEMBER CHECK
    |--------------------------------------------------------------------------
    */

        if ($user->membership_category_id === null) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Only members can access additional payments.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | APPROVED MEMBER
    |--------------------------------------------------------------------------
    */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        if (
            !$profile ||
            strtolower(trim($profile->status ?? '')) !== 'approved'
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Only approved members can access additional payments.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | ACTIVE MEMBERSHIP
    |--------------------------------------------------------------------------
    */

        $membership = Membership::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'active'
            )
            ->latest()
            ->first();

        if (!$membership) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'No active membership was found for your account.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    |
    | users.membership_category_id is authoritative.
    |
    */

        $categoryId = $user->membership_category_id;

        /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEMS
    |--------------------------------------------------------------------------
    |
    | STRICT CATEGORY MATCH.
    |
    | payment_items.membership_category_id
    | MUST equal
    | users.membership_category_id
    |
    | NULL categories are deliberately excluded.
    |
    */

        $paymentItems = PaymentItem::where(
            'is_active',
            true
        )
            ->whereNotIn(
                'type',
                ['membership']
            )
            ->whereNotNull(
                'membership_category_id'
            )
            ->where(
                'membership_category_id',
                $categoryId
            )
            ->orderBy(
                'name'
            )
            ->get();

        /*
    |--------------------------------------------------------------------------
    | EXISTING UNPAID DEBITS
    |--------------------------------------------------------------------------
    |
    | We load these only so the Blade page can know whether
    | an unpaid transaction already exists.
    |
    | IMPORTANT:
    |
    | initializeAdditional() can create a debit when one
    | does not already exist.
    |
    */

        $unpaidDebits = Transaction::where(
            'user_id',
            $user->id
        )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'status',
                'not paid'
            )
            ->whereNotNull(
                'payment_item_id'
            )
            ->get()
            ->keyBy(
                'payment_item_id'
            );

        /*
    |--------------------------------------------------------------------------
    | RECENT PAYMENTS
    |--------------------------------------------------------------------------
    */

        $recentPayments = Transaction::with([
            'paymentItem',
            'debitTransaction',
        ])
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'type',
                'credit'
            )
            ->whereNotNull(
                'debit_transaction_id'
            )
            ->latest()
            ->limit(10)
            ->get();

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'member.payment.additional-payment',
            compact(
                'paymentItems',
                'unpaidDebits',
                'recentPayments',
                'membership'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADDITIONAL PAYMENT PAGE
    |--------------------------------------------------------------------------
    |
    | Only approved members with active membership.
    |
    | STRICT MEMBER FILTER:
    |
    | users.membership_category_id != NULL
    |
    | AND
    |
    | payment_items.membership_category_id
    | =
    | users.membership_category_id
    |
    | NULL payment-item categories are excluded.
    |
    */

    public function additional()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | MEMBER CHECK
        |--------------------------------------------------------------------------
        */

        if ($user->membership_category_id === null) {
            return redirect()
                ->route('member.member_dashboard')
                ->with(
                    'error',
                    'Only members can access additional payments.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | APPROVAL
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        if (
            !$profile ||
            strtolower(
                trim(
                    $profile->status ?? ''
                )
            ) !== 'approved'
        ) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Only approved members can access additional payments.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVE MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $membership = Membership::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'active'
            )
            ->latest()
            ->first();

        if (!$membership) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'No active membership was found for your account.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $categoryId =
            $user->membership_category_id;

        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEMS
        |--------------------------------------------------------------------------
        |
        | STRICT MATCH ONLY.
        |
        | DO NOT include:
        |
        | membership_category_id = NULL
        |
        | ONLY include:
        |
        | payment_items.membership_category_id
        | =
        | users.membership_category_id
        |
        */

        $paymentItems = PaymentItem::where(
            'is_active',
            true
        )
            ->whereNotIn(
                'type',
                ['membership']
            )
            ->whereNotNull(
                'membership_category_id'
            )
            ->where(
                'membership_category_id',
                $categoryId
            )
            ->orderBy(
                'name'
            )
            ->get();

        return view(
            'member.payment.additional-payment',
            compact(
                'paymentItems',
                'membership'
            )
        );
    }


    /*
|--------------------------------------------------------------------------
| INITIALIZE ADDITIONAL PAYMENT
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| The debit transaction does NOT have to exist beforehand.
|
| FLOW:
|
| 1. Validate payment item.
| 2. Verify member.
| 3. Verify approved profile.
| 4. Verify active membership.
| 5. Verify payment item belongs to user's category.
| 6. Find existing unpaid debit.
| 7. If no debit exists, CREATE ONE.
| 8. Create/update Payment record.
| 9. Initialize Paystack.
|
*/
    public function initializeAdditional(Request $request)
    {
        $request->validate([
            'payment_item_id' => [
                'required',
                'integer',
                'exists:payment_items,id',
            ],

            'transaction_id' => [
                'nullable',
                'integer',
                'exists:transactions,id',
            ],

            'document_field_values' => [
                'nullable',
                'array',
            ],
        ]);

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | MEMBER CHECK
    |--------------------------------------------------------------------------
    */

        if (!$user || $user->membership_category_id === null) {
            return back()
                ->with(
                    'error',
                    'Only members can make additional payments.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | APPROVED PROFILE
    |--------------------------------------------------------------------------
    */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        if (
            !$profile ||
            strtolower(trim($profile->status ?? '')) !== 'approved'
        ) {
            return back()
                ->with(
                    'error',
                    'Only approved members can make additional payments.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | ACTIVE MEMBERSHIP
    |--------------------------------------------------------------------------
    */

        $membership = Membership::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'active'
            )
            ->latest()
            ->first();

        if (!$membership) {
            return back()
                ->with(
                    'error',
                    'No active membership was found for your account.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | USER MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

        $categoryId = (int) $user->membership_category_id;

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYMENT ITEM
    |--------------------------------------------------------------------------
    |
    | SECURITY:
    |
    | The payment item MUST:
    |
    | - exist
    | - be active
    | - NOT be a membership payment
    | - have a membership category
    | - belong to the user's exact membership category
    |
    */

        $paymentItem = PaymentItem::where(
            'id',
            $request->payment_item_id
        )
            ->where(
                'is_active',
                true
            )
            ->whereNotIn(
                'type',
                ['membership']
            )
            ->whereNotNull(
                'membership_category_id'
            )
            ->where(
                'membership_category_id',
                $categoryId
            )
            ->first();

        if (!$paymentItem) {

            Log::warning(
                'INVALID ADDITIONAL PAYMENT ITEM',
                [
                    'user_id' =>
                    $user->id,

                    'user_membership_category_id' =>
                    $categoryId,

                    'payment_item_id' =>
                    $request->payment_item_id,
                ]
            );

            return back()
                ->with(
                    'error',
                    'The selected payment item is not available for your membership category.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

        if (
            (int) $membership->membership_category_id !==
            $categoryId
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT MEMBERSHIP CATEGORY MISMATCH',
                [
                    'user_id' =>
                    $user->id,

                    'user_membership_category_id' =>
                    $categoryId,

                    'membership_category_id' =>
                    $membership->membership_category_id,

                    'payment_item_id' =>
                    $paymentItem->id,
                ]
            );

            return back()
                ->with(
                    'error',
                    'Your membership category could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYMENT AMOUNT
    |--------------------------------------------------------------------------
    |
    | NEVER TRUST AN AMOUNT FROM THE BROWSER.
    |
    | The authoritative amount comes directly from payment_items.amount.
    |
    */

        $amount = (float) $paymentItem->amount;

        if ($amount <= 0) {

            Log::error(
                'INVALID ADDITIONAL PAYMENT ITEM AMOUNT',
                [
                    'user_id' =>
                    $user->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'amount' =>
                    $paymentItem->amount,
                ]
            );

            return back()
                ->with(
                    'error',
                    'The selected payment item has an invalid amount.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | LOAD DOCUMENT + DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We load the document and fields directly from the database.
    |
    | Nothing about the document definition is trusted from the browser.
    |
    */

        $paymentItem->load([
            'document' => function ($query) {

                $query
                    ->where(
                        'is_active',
                        true
                    )
                    ->with([
                        'fields' => function ($query) {

                            $query
                                ->orderBy(
                                    'sort_order'
                                )
                                ->orderBy(
                                    'id'
                                );
                        },
                    ]);
            },
        ]);

        /*
    |--------------------------------------------------------------------------
    | DOCUMENT FIELD VALUES
    |--------------------------------------------------------------------------
    */

        $submittedDocumentFieldValues =
            $request->input(
                'document_field_values',
                []
            );

        Log::info(
            'ADDITIONAL PAYMENT DOCUMENT FIELD VALUES RECEIVED',
            [
                'user_id' => $user->id,

                'payment_item_id' =>
                $paymentItem->id,

                'document_id' =>
                $paymentItem->document?->id,

                'submitted_document_field_values' =>
                $submittedDocumentFieldValues,

                'manual_fields' =>
                $paymentItem->document
                    ? $paymentItem->document->fields
                    ->where('is_system', false)
                    ->map(function ($field) {
                        return [
                            'field_key' =>
                            $field->field_key,

                            'label' =>
                            $field->label,

                            'field_type' =>
                            $field->field_type,

                            'is_required' =>
                            $field->is_required,

                            'default_value' =>
                            $field->default_value,
                        ];
                    })
                    ->values()
                    ->all()
                    : [],
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | NORMALIZE NULL
    |--------------------------------------------------------------------------
    */

        if ($submittedDocumentFieldValues === null) {

            $submittedDocumentFieldValues = [];
        }

        /*
    |--------------------------------------------------------------------------
    | DOCUMENT FIELD VALIDATION
    |--------------------------------------------------------------------------
    |
    | There are three possibilities:
    |
    | 1. No document
    |      -> no fields required
    |
    | 2. Document with ONLY system fields
    |      -> no member input required
    |
    | 3. Document with manual fields
    |      -> validate submitted manual fields
    |
    */

        $documentFieldValues = [];

        if ($paymentItem->document) {

            /*
        |--------------------------------------------------------------------------
        | ONLY MANUAL FIELDS ARE ACCEPTED FROM THE MEMBER
        |--------------------------------------------------------------------------
        */

            $manualFields = $paymentItem->document->fields
                ->where(
                    'is_system',
                    false
                )
                ->values();

            /*
        |--------------------------------------------------------------------------
        | FULLY AUTOMATIC DOCUMENT
        |--------------------------------------------------------------------------
        |
        | If there are no manual fields, the member does not need
        | to submit document_field_values.
        |
        */

            if ($manualFields->isEmpty()) {

                $documentFieldValues = [];

                /*
            |--------------------------------------------------------------------------
            | SECURITY:
            |
            | If the browser nevertheless submits values for system fields,
            | reject them rather than silently accepting them.
            |--------------------------------------------------------------------------
            */

                if (!empty($submittedDocumentFieldValues)) {

                    Log::warning(
                        'SYSTEM DOCUMENT FIELDS SUBMITTED BY MEMBER',
                        [
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'submitted_keys' =>
                            array_keys(
                                $submittedDocumentFieldValues
                            ),
                        ]
                    );

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'This document does not accept manually supplied fields.'
                        );
                }
            } else {

                /*
            |--------------------------------------------------------------------------
            | GET ALLOWED MANUAL FIELD KEYS
            |--------------------------------------------------------------------------
            */

                $allowedFieldKeys =
                    $manualFields
                    ->pluck('field_key')
                    ->values()
                    ->all();

                /*
            |--------------------------------------------------------------------------
            | REJECT UNAUTHORIZED FIELD KEYS
            |--------------------------------------------------------------------------
            |
            | This prevents a member from submitting:
            |
            | member_name
            | membership_number
            | payment_amount
            | document_number
            | tracking_code
            | etc.
            |
            | when those fields are system-controlled.
            |--------------------------------------------------------------------------
            */

                foreach (
                    array_keys(
                        $submittedDocumentFieldValues
                    ) as $submittedKey
                ) {

                    if (
                        !in_array(
                            $submittedKey,
                            $allowedFieldKeys,
                            true
                        )
                    ) {

                        Log::warning(
                            'UNAUTHORIZED DOCUMENT FIELD SUBMITTED',
                            [
                                'user_id' =>
                                $user->id,

                                'payment_item_id' =>
                                $paymentItem->id,

                                'field_key' =>
                                $submittedKey,
                            ]
                        );

                        return back()
                            ->withInput()
                            ->with(
                                'error',
                                'An invalid document field was submitted.'
                            );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | VALIDATE EACH MANUAL FIELD
            |--------------------------------------------------------------------------
            */

                foreach ($manualFields as $field) {

                    $fieldKey =
                        $field->field_key;

                    $value =
                        $submittedDocumentFieldValues[$fieldKey] ?? null;

                    /*
                |--------------------------------------------------------------------------
                | NORMALIZE EMPTY STRINGS
                |--------------------------------------------------------------------------
                */

                    if (
                        is_string($value) &&
                        trim($value) === ''
                    ) {

                        $value = null;
                    }

                    /*
                |--------------------------------------------------------------------------
                | REQUIRED FIELD
                |--------------------------------------------------------------------------
                */

                    if (
                        $field->is_required &&
                        (
                            $value === null ||
                            $value === ''
                        )
                    ) {

                        return back()
                            ->withInput()
                            ->with(
                                'error',
                                "{$field->label} is required."
                            );
                    }

                    /*
                |--------------------------------------------------------------------------
                | OPTIONAL EMPTY FIELD
                |--------------------------------------------------------------------------
                */

                    if ($value === null) {

                        continue;
                    }

                    /*
                |--------------------------------------------------------------------------
                | FIELD TYPE VALIDATION
                |--------------------------------------------------------------------------
                */

                    switch ($field->field_type) {

                        /*
                    |--------------------------------------------------------------------------
                    | TEXT
                    |--------------------------------------------------------------------------
                    */

                        case 'text':

                        case 'textarea':

                            if (!is_string($value)) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be text."
                                    );
                            }

                            $value =
                                trim($value);

                            break;


                        /*
                    |--------------------------------------------------------------------------
                    | NUMBER
                    |--------------------------------------------------------------------------
                    */

                        case 'number':

                            if (
                                !is_numeric($value)
                            ) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be a valid number."
                                    );
                            }

                            /*
                        |--------------------------------------------------------------------------
                        | Store a normalized numeric value
                        |--------------------------------------------------------------------------
                        */

                            $value =
                                (string) $value;

                            break;


                        /*
                    |--------------------------------------------------------------------------
                    | DATE
                    |--------------------------------------------------------------------------
                    */

                        case 'date':

                            if (
                                !is_string($value)
                            ) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be a valid date."
                                    );
                            }

                            try {

                                $date =
                                    \Carbon\Carbon::createFromFormat(
                                        'Y-m-d',
                                        $value
                                    );

                                if (
                                    !$date ||
                                    $date->format('Y-m-d') !== $value
                                ) {

                                    throw new \Exception();
                                }
                            } catch (\Throwable $e) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be a valid date."
                                    );
                            }

                            break;


                        /*
                    |--------------------------------------------------------------------------
                    | TIME
                    |--------------------------------------------------------------------------
                    */

                        case 'time':

                            if (
                                !is_string($value)
                            ) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be a valid time."
                                    );
                            }

                            try {

                                $time =
                                    \Carbon\Carbon::createFromFormat(
                                        'H:i',
                                        $value
                                    );

                                if (
                                    !$time ||
                                    $time->format('H:i') !== $value
                                ) {

                                    throw new \Exception();
                                }
                            } catch (\Throwable $e) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "{$field->label} must be a valid time."
                                    );
                            }

                            break;


                        /*
                    |--------------------------------------------------------------------------
                    | SELECT
                    |--------------------------------------------------------------------------
                    */

                        case 'select':

                            $options =
                                is_array($field->options)
                                ? $field->options
                                : [];

                            $allowedOptions = [];

                            foreach (
                                $options as $option
                            ) {

                                /*
                            |--------------------------------------------------------------------------
                            | Simple option:
                            |
                            | ["Dealer", "Supplier", "Exporter"]
                            |--------------------------------------------------------------------------
                            */

                                if (
                                    is_string($option) ||
                                    is_numeric($option)
                                ) {

                                    $allowedOptions[] =
                                        (string) $option;

                                    continue;
                                }

                                /*
                            |--------------------------------------------------------------------------
                            | Key/value option:
                            |
                            | [
                            |     ['key' => 'dealer', 'label' => 'Dealer']
                            | ]
                            |--------------------------------------------------------------------------
                            */

                                if (
                                    is_array($option)
                                ) {

                                    if (
                                        array_key_exists(
                                            'value',
                                            $option
                                        )
                                    ) {

                                        $allowedOptions[] =
                                            (string) $option['value'];
                                    } elseif (
                                        array_key_exists(
                                            'key',
                                            $option
                                        )
                                    ) {

                                        $allowedOptions[] =
                                            (string) $option['key'];
                                    }
                                }
                            }

                            if (
                                !in_array(
                                    (string) $value,
                                    $allowedOptions,
                                    true
                                )
                            ) {

                                return back()
                                    ->withInput()
                                    ->with(
                                        'error',
                                        "Invalid option selected for {$field->label}."
                                    );
                            }

                            /*
                        |--------------------------------------------------------------------------
                        | Normalize selected value
                        |--------------------------------------------------------------------------
                        */

                            $value =
                                (string) $value;

                            break;


                        /*
                    |--------------------------------------------------------------------------
                    | UNSUPPORTED FIELD TYPE
                    |--------------------------------------------------------------------------
                    */

                        default:

                            Log::critical(
                                'UNSUPPORTED DOCUMENT FIELD TYPE',
                                [
                                    'user_id' =>
                                    $user->id,

                                    'payment_item_id' =>
                                    $paymentItem->id,

                                    'document_id' =>
                                    $paymentItem->document->id,

                                    'field_key' =>
                                    $fieldKey,

                                    'field_type' =>
                                    $field->field_type,
                                ]
                            );

                            return back()
                                ->withInput()
                                ->with(
                                    'error',
                                    "The document field {$field->label} has an unsupported field type."
                                );
                    }

                    /*
                |--------------------------------------------------------------------------
                | STORE ONLY VALIDATED MANUAL FIELD
                |--------------------------------------------------------------------------
                */

                    $documentFieldValues[$fieldKey] = $value;
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | CREATE / FIND DEBIT + PAYMENT
    |--------------------------------------------------------------------------
    */

        try {

            $paymentData = DB::transaction(
                function () use (
                    $request,
                    $user,
                    $paymentItem,
                    $categoryId,
                    $amount,
                    $documentFieldValues
                ) {

                    /*
                |--------------------------------------------------------------------------
                | FIND / LOCK EXISTING UNPAID DEBIT
                |--------------------------------------------------------------------------
                */

                    $debitQuery = Transaction::where(
                        'user_id',
                        $user->id
                    )
                        ->where(
                            'type',
                            'debit'
                        )
                        ->where(
                            'status',
                            'not paid'
                        )
                        ->where(
                            'payment_item_id',
                            $paymentItem->id
                        );

                    /*
                |--------------------------------------------------------------------------
                | OPTIONAL TRANSACTION ID
                |--------------------------------------------------------------------------
                |
                | If supplied by the browser, it MUST belong to this
                | user, this payment item, and an unpaid debit.
                |
                */

                    if ($request->filled('transaction_id')) {

                        $debitQuery->where(
                            'id',
                            $request->transaction_id
                        );
                    }

                    $debit = $debitQuery
                        ->latest()
                        ->lockForUpdate()
                        ->first();

                    /*
                |--------------------------------------------------------------------------
                | CREATE DEBIT IF ONE DOES NOT EXIST
                |--------------------------------------------------------------------------
                */

                    if (!$debit) {

                        $debit = Transaction::create([
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'debit_transaction_id' =>
                            null,

                            'narration' =>
                            $paymentItem->name,

                            'type' =>
                            'debit',

                            'status' =>
                            'not paid',

                            'amount' =>
                            $amount,

                            /*
                        |--------------------------------------------------------------------------
                        | DO NOT STORE PAYSTACK NUMERIC TRANSACTION ID
                        |--------------------------------------------------------------------------
                        */

                            'transaction_id' =>
                            null,

                            /*
                        |--------------------------------------------------------------------------
                        | Paystack reference will be stored in gateway JSON.
                        |--------------------------------------------------------------------------
                        */

                            'gateway' =>
                            null,
                        ]);
                    } else {

                        /*
                    |--------------------------------------------------------------------------
                    | VERIFY EXISTING DEBIT AMOUNT
                    |--------------------------------------------------------------------------
                    */

                        if (
                            abs(
                                (float) $debit->amount -
                                    $amount
                            ) > 0.01
                        ) {

                            throw new \RuntimeException(
                                'The existing payment transaction amount does not match the payment item.'
                            );
                        }
                    }

                    /*
                |--------------------------------------------------------------------------
                | PAYSTACK REFERENCE
                |--------------------------------------------------------------------------
                */

                    $gateway = [];

                    if (is_array($debit->gateway)) {
                        $gateway = $debit->gateway;
                    }

                    /*
|--------------------------------------------------------------------------
| PAYSTACK REFERENCE
|--------------------------------------------------------------------------
|
| Every new Paystack initialization must use a fresh reference.
|
| We do NOT reuse an old Paystack reference because Paystack rejects
| duplicate transaction references.
|
*/

                    $reference =
                        'NACP-' .
                        strtoupper(
                            Str::random(20)
                        );

                    $gateway['paystack'] =
                        $reference;

                    $debit->update([
                        'gateway' =>
                        $gateway,
                    ]);

                    /*
                |--------------------------------------------------------------------------
                | PAYMENT DESCRIPTION
                |--------------------------------------------------------------------------
                */

                    $description =
                        $paymentItem->name;

                    if (
                        !empty($paymentItem->description)
                    ) {

                        $description .=
                            ' - ' .
                            $paymentItem->description;
                    }

                    /*
                |--------------------------------------------------------------------------
                | FIND PAYMENT USING OUR REFERENCE
                |--------------------------------------------------------------------------
                */

                    $payment = Payment::where(
                        'reference',
                        $reference
                    )
                        ->lockForUpdate()
                        ->first();

                    /*
                |--------------------------------------------------------------------------
                | CREATE PAYMENT
                |--------------------------------------------------------------------------
                */

                    if (!$payment) {

                        $payment = Payment::create([
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'membership_category_id' =>
                            $categoryId,

                            'membership_category_fee_id' =>
                            null,

                            'member_fee_id' =>
                            null,

                            'payment_type' =>
                            'additional',

                            'fee_type' =>
                            'standard',

                            'amount' =>
                            $amount,

                            'description' =>
                            $description,

                            'payment_reference' =>
                            $reference,

                            'paystack_reference' =>
                            $reference,

                            'reference' =>
                            $reference,

                            'gateway' =>
                            'paystack',

                            /*
                        |--------------------------------------------------------------------------
                        | Paystack numeric ID is NOT stored here.
                        |--------------------------------------------------------------------------
                        */

                            'gateway_transaction_id' =>
                            null,

                            'gateway_status' =>
                            'pending',

                            /*
                        |--------------------------------------------------------------------------
                        | STORE VALIDATED DOCUMENT VALUES
                        |--------------------------------------------------------------------------
                        */

                            'document_field_values' =>
                            $documentFieldValues,

                            'status' =>
                            'pending',
                        ]);
                    } else {

                        /*
                    |--------------------------------------------------------------------------
                    | VERIFY PAYMENT BELONGS TO THIS USER
                    |--------------------------------------------------------------------------
                    */

                        if (
                            (int) $payment->user_id !==
                            (int) $user->id
                        ) {

                            throw new \RuntimeException(
                                'Payment ownership could not be verified.'
                            );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | VERIFY PAYMENT ITEM
                    |--------------------------------------------------------------------------
                    */

                        if (
                            (int) $payment->payment_item_id !==
                            (int) $paymentItem->id
                        ) {

                            throw new \RuntimeException(
                                'Payment item could not be verified.'
                            );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | DO NOT REOPEN A SUCCESSFUL PAYMENT
                    |--------------------------------------------------------------------------
                    */

                        if (
                            strtolower(
                                trim(
                                    $payment->status ?? ''
                                )
                            ) === 'paid'
                        ) {

                            throw new \RuntimeException(
                                'This payment has already been completed.'
                            );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | UPDATE EXISTING PAYMENT
                    |--------------------------------------------------------------------------
                    */

                        $payment->update([
                            'user_id' =>
                            $user->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'membership_category_id' =>
                            $categoryId,

                            'payment_type' =>
                            'additional',

                            'fee_type' =>
                            'standard',

                            'amount' =>
                            $amount,

                            'description' =>
                            $description,

                            'payment_reference' =>
                            $reference,

                            'paystack_reference' =>
                            $reference,

                            'gateway' =>
                            'paystack',

                            'gateway_status' =>
                            'pending',

                            /*
                        |--------------------------------------------------------------------------
                        | UPDATE STORED DOCUMENT VALUES
                        |--------------------------------------------------------------------------
                        */

                            'document_field_values' =>
                            $documentFieldValues,

                            'status' =>
                            'pending',
                        ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | RETURN DATA
                |--------------------------------------------------------------------------
                */

                    return [
                        'debit' =>
                        $debit,

                        'payment' =>
                        $payment,

                        'reference' =>
                        $reference,

                        'amount' =>
                        $amount,
                    ];
                }
            );

            $debit =
                $paymentData['debit'];

            $payment =
                $paymentData['payment'];

            $reference =
                $paymentData['reference'];

            $amount =
                $paymentData['amount'];
        } catch (\Throwable $e) {

            Log::error(
                'ADDITIONAL PAYMENT TRANSACTION CREATION FAILED',
                [
                    'user_id' =>
                    $user->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),
                ]
            );

            return back()
                ->with(
                    'error',
                    'Unable to prepare this payment. Please try again.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK INITIALIZATION
    |--------------------------------------------------------------------------
    */

        try {

            $response = Http::withToken(
                config(
                    'services.paystack.secret_key'
                )
            )
                ->acceptJson()
                ->post(
                    config(
                        'services.paystack.url'
                    ) . '/transaction/initialize',
                    [
                        'email' =>
                        $user->email,

                        /*
                    |--------------------------------------------------------------------------
                    | DATABASE AMOUNT → KOBO
                    |--------------------------------------------------------------------------
                    */

                        'amount' =>
                        (int) round(
                            $amount * 100
                        ),

                        'reference' =>
                        $reference,

                        'currency' =>
                        'NGN',

                        'callback_url' =>
                        route(
                            'payment.additional.callback'
                        ),

                        'metadata' => [

                            /*
                        |--------------------------------------------------------------------------
                        | INTERNAL TRANSACTION ID
                        |
                        | NOT PAYSTACK'S NUMERIC TRANSACTION ID.
                        |--------------------------------------------------------------------------
                        */

                            'transaction_id' =>
                            $debit->id,

                            'payment_id' =>
                            $payment->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'user_id' =>
                            $user->id,

                            'membership_category_id' =>
                            $categoryId,

                            'payment_type' =>
                            'additional',
                        ],
                    ]
                );

            /*
        |--------------------------------------------------------------------------
        | HTTP FAILURE
        |--------------------------------------------------------------------------
        */

            if (!$response->successful()) {

                Log::error(
                    'ADDITIONAL PAYMENT PAYSTACK INITIALIZATION FAILED',
                    [
                        'transaction_id' =>
                        $debit->id,

                        'payment_id' =>
                        $payment->id,

                        'payment_item_id' =>
                        $paymentItem->id,

                        'reference' =>
                        $reference,

                        'status' =>
                        $response->status(),

                        'response' =>
                        $response->json(),
                    ]
                );

                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $response->json(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to initialize payment with Paystack.',
                ], 400);
            }

            $data =
                $response->json();

            /*
        |--------------------------------------------------------------------------
        | PAYSTACK RESPONSE FAILURE
        |--------------------------------------------------------------------------
        */

            if (
                ($data['status'] ?? false) !== true
            ) {

                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $data,
                ]);

                return back()
                    ->with(
                        'error',
                        $data['message']
                            ?? 'Paystack payment initialization failed.'
                    );
            }

            /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION URL
        |--------------------------------------------------------------------------
        */

            $authorizationUrl =
                $data['data']['authorization_url']
                ?? null;

            if (!$authorizationUrl) {

                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',

                    'gateway_response' =>
                    $data,
                ]);

                return back()
                    ->with(
                        'error',
                        'Paystack did not return a payment URL.'
                    );
            }

            /*
        |--------------------------------------------------------------------------
        | SAVE PAYSTACK RESPONSE
        |--------------------------------------------------------------------------
        */

            $payment->update([
                'paystack_authorization_url' =>
                $authorizationUrl,

                'gateway_status' =>
                'initialized',

                'gateway_response' =>
                $data,

                'status' =>
                'pending',
            ]);

            /*
        |--------------------------------------------------------------------------
        | REDIRECT TO PAYSTACK
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => true,
                'authorization_url' => $authorizationUrl,
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'ADDITIONAL PAYMENT PAYSTACK INITIALIZATION EXCEPTION',
                [
                    'transaction_id' =>
                    $debit->id,

                    'payment_id' =>
                    $payment->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'reference' =>
                    $reference,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),
                ]
            );

            try {

                $payment->update([
                    'gateway_status' =>
                    'failed',

                    'status' =>
                    'failed',
                ]);
            } catch (\Throwable $updateException) {

                Log::error(
                    'ADDITIONAL PAYMENT FAILED STATUS UPDATE ERROR',
                    [
                        'payment_id' =>
                        $payment->id,

                        'error' =>
                        $updateException->getMessage(),
                    ]
                );
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while connecting to Paystack.',
            ], 500);
        }
    }


    public function additionalCallback(
        Request $request,
        DocumentGenerationService $documentGenerationService
    ) {
        /*
    |--------------------------------------------------------------------------
    | GET PAYSTACK REFERENCE
    |--------------------------------------------------------------------------
    */

        $reference = $request->query('reference');

        if (!$reference) {

            Log::warning(
                'ADDITIONAL PAYMENT CALLBACK WITHOUT REFERENCE',
                [
                    'user_id' => Auth::id(),
                    'query' => $request->query(),
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment reference was not provided.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | LOG CALLBACK
    |--------------------------------------------------------------------------
    */

        Log::info(
            'ADDITIONAL PAYMENT CALLBACK HIT',
            [
                'user_id' => Auth::id(),
                'reference' => $reference,
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | FIND PAYMENT
    |--------------------------------------------------------------------------
    */

        $payment = Payment::where(
            'reference',
            $reference
        )
            ->where(
                'payment_type',
                'additional'
            )
            ->first();

        /*
    |--------------------------------------------------------------------------
    | FALLBACK TO PAYSTACK REFERENCE
    |--------------------------------------------------------------------------
    */

        if (!$payment) {

            $payment = Payment::where(
                'paystack_reference',
                $reference
            )
                ->where(
                    'payment_type',
                    'additional'
                )
                ->first();
        }

        if (!$payment) {

            Log::error(
                'ADDITIONAL PAYMENT NOT FOUND DURING CALLBACK',
                [
                    'reference' => $reference,
                    'user_id' => Auth::id(),
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment record could not be found.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
    |--------------------------------------------------------------------------
    */

        $user = Auth::user();

        if (!$user) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login to continue.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYMENT OWNERSHIP
    |--------------------------------------------------------------------------
    */

        if (
            (int) $payment->user_id !==
            (int) $user->id
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT USER MISMATCH',
                [
                    'payment_id' => $payment->id,

                    'payment_user_id' => $payment->user_id,

                    'logged_in_user_id' => $user->id,

                    'reference' => $reference,
                ]
            );

            abort(
                403,
                'Unauthorized payment access.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

        $paymentItem =
            PaymentItem::find(
                $payment->payment_item_id
            );

        if (!$paymentItem) {

            Log::error(
                'ADDITIONAL PAYMENT ITEM NOT FOUND DURING CALLBACK',
                [
                    'payment_id' => $payment->id,

                    'payment_item_id' =>
                    $payment->payment_item_id,

                    'reference' => $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment item could not be found.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYMENT ITEM CATEGORY
    |--------------------------------------------------------------------------
    */

        if (
            $paymentItem->membership_category_id === null ||
            (int) $paymentItem->membership_category_id !==
            (int) $user->membership_category_id
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT CATEGORY MISMATCH DURING CALLBACK',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'user_category_id' =>
                    $user->membership_category_id,

                    'payment_item_category_id' =>
                    $paymentItem->membership_category_id,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'This payment is not available for your membership category.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | ACTIVE MEMBERSHIP
    |--------------------------------------------------------------------------
    */

        $membership = Membership::where(
            'user_id',
            $user->id
        )
            ->where(
                'status',
                'active'
            )
            ->latest()
            ->first();

        if (!$membership) {

            Log::warning(
                'ADDITIONAL PAYMENT CALLBACK WITHOUT ACTIVE MEMBERSHIP',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'No active membership was found for your account.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

        if (
            (int) $membership->membership_category_id !==
            (int) $user->membership_category_id
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT MEMBERSHIP CATEGORY MISMATCH',
                [
                    'user_id' =>
                    $user->id,

                    'membership_id' =>
                    $membership->id,

                    'membership_category_id' =>
                    $membership->membership_category_id,

                    'user_category_id' =>
                    $user->membership_category_id,

                    'payment_id' =>
                    $payment->id,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Your membership category could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | FIND DEBIT TRANSACTION
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We identify the debit using the Paystack reference stored
    | inside the gateway JSON.
    |
    */

        $debit = Transaction::where(
            'user_id',
            $user->id
        )
            ->where(
                'type',
                'debit'
            )
            ->where(
                'payment_item_id',
                $paymentItem->id
            )
            ->whereJsonContains(
                'gateway->paystack',
                $reference
            )
            ->first();

        if (!$debit) {

            Log::error(
                'ADDITIONAL PAYMENT DEBIT NOT FOUND BY PAYSTACK REFERENCE',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment transaction could not be found.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY DEBIT OWNERSHIP
    |--------------------------------------------------------------------------
    */

        if (
            (int) $debit->user_id !==
            (int) $user->id
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT DEBIT USER MISMATCH',
                [
                    'debit_id' =>
                    $debit->id,

                    'debit_user_id' =>
                    $debit->user_id,

                    'user_id' =>
                    $user->id,

                    'reference' =>
                    $reference,
                ]
            );

            abort(
                403,
                'Unauthorized transaction access.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY DEBIT PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

        if (
            (int) $debit->payment_item_id !==
            (int) $paymentItem->id
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT DEBIT PAYMENT ITEM MISMATCH',
                [
                    'debit_id' =>
                    $debit->id,

                    'debit_payment_item_id' =>
                    $debit->payment_item_id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment transaction could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY DATABASE AMOUNT
    |--------------------------------------------------------------------------
    */

        $expectedAmount =
            (float) $paymentItem->amount;

        if ($expectedAmount <= 0) {

            Log::critical(
                'INVALID ADDITIONAL PAYMENT ITEM AMOUNT',
                [
                    'payment_item_id' =>
                    $paymentItem->id,

                    'amount' =>
                    $paymentItem->amount,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment item has an invalid amount.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYMENT AMOUNT AGAINST PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

        if (
            abs(
                (float) $payment->amount -
                    $expectedAmount
            ) > 0.01
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT AMOUNT MISMATCH',
                [
                    'payment_id' =>
                    $payment->id,

                    'payment_amount' =>
                    $payment->amount,

                    'payment_item_amount' =>
                    $expectedAmount,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment amount could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY DEBIT AMOUNT
    |--------------------------------------------------------------------------
    */

        if (
            abs(
                (float) $debit->amount -
                    $expectedAmount
            ) > 0.01
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT DEBIT AMOUNT MISMATCH',
                [
                    'debit_id' =>
                    $debit->id,

                    'debit_amount' =>
                    $debit->amount,

                    'payment_item_amount' =>
                    $expectedAmount,

                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment transaction amount could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY WITH PAYSTACK
    |--------------------------------------------------------------------------
    */

        try {

            $response = Http::withToken(
                config(
                    'services.paystack.secret_key'
                )
            )
                ->acceptJson()
                ->get(
                    config(
                        'services.paystack.url'
                    ) .
                        '/transaction/verify/' .
                        urlencode($reference)
                );
        } catch (\Throwable $e) {

            Log::error(
                'ADDITIONAL PAYMENT PAYSTACK VERIFICATION EXCEPTION',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Unable to verify your payment with Paystack. Please try again.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK HTTP FAILURE
    |--------------------------------------------------------------------------
    */

        if (!$response->successful()) {

            Log::error(
                'ADDITIONAL PAYMENT PAYSTACK VERIFICATION FAILED',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'http_status' =>
                    $response->status(),

                    'response' =>
                    $response->json(),
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Paystack could not verify this payment.'
                );
        }

        $verification =
            $response->json();

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK RESPONSE STATUS
    |--------------------------------------------------------------------------
    */

        if (
            ($verification['status'] ?? false) !== true
        ) {

            Log::error(
                'ADDITIONAL PAYMENT PAYSTACK VERIFICATION RESPONSE FAILED',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'response' =>
                    $verification,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    $verification['message']
                        ?? 'Unable to verify payment.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK DATA
    |--------------------------------------------------------------------------
    */

        $paystackData =
            $verification['data']
            ?? [];

        if (!is_array($paystackData)) {

            Log::critical(
                'ADDITIONAL PAYMENT INVALID PAYSTACK DATA',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'verification' =>
                    $verification,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Invalid payment verification response.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK TRANSACTION STATUS
    |--------------------------------------------------------------------------
    */

        $paystackStatus =
            strtolower(
                trim(
                    $paystackData['status']
                        ?? ''
                )
            );

        /*
    |--------------------------------------------------------------------------
    | VERIFY REFERENCE
    |--------------------------------------------------------------------------
    */

        $verifiedReference =
            $paystackData['reference']
            ?? null;

        if (
            !$verifiedReference ||
            !hash_equals(
                (string) $reference,
                (string) $verifiedReference
            )
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT PAYSTACK REFERENCE MISMATCH',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'expected_reference' =>
                    $reference,

                    'paystack_reference' =>
                    $verifiedReference,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The Paystack payment reference could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYMENT STATUS
    |--------------------------------------------------------------------------
    |
    | ONLY "success" can continue to the ledger.
    |
    */

        if ($paystackStatus !== 'success') {

            $payment->update([
                'gateway_status' =>
                $paystackStatus ?: 'failed',

                'status' =>
                'failed',

                'gateway_response' =>
                $verification,
            ]);

            Log::warning(
                'ADDITIONAL PAYMENT NOT SUCCESSFUL',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'reference' =>
                    $reference,

                    'paystack_status' =>
                    $paystackStatus,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Your payment was not successful.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYSTACK AMOUNT EXISTS
    |--------------------------------------------------------------------------
    */

        if (!isset($paystackData['amount'])) {

            Log::critical(
                'ADDITIONAL PAYMENT PAYSTACK AMOUNT MISSING',
                [
                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'paystack_data' =>
                    $paystackData,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The Paystack payment amount could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYSTACK AMOUNT
    |--------------------------------------------------------------------------
    |
    | Paystack amount is in KOBO.
    |
    */

        $paystackAmount =
            ((int) $paystackData['amount']) / 100;

        if (
            abs(
                $paystackAmount -
                    $expectedAmount
            ) > 0.01
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT PAYSTACK AMOUNT MISMATCH',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'expected_amount' =>
                    $expectedAmount,

                    'paystack_amount' =>
                    $paystackAmount,

                    'paystack_amount_kobo' =>
                    $paystackData['amount'],
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment amount could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYSTACK CURRENCY
    |--------------------------------------------------------------------------
    */

        $currency =
            strtoupper(
                trim(
                    $paystackData['currency']
                        ?? ''
                )
            );

        if ($currency !== 'NGN') {

            Log::critical(
                'ADDITIONAL PAYMENT CURRENCY MISMATCH',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'expected_currency' =>
                    'NGN',

                    'paystack_currency' =>
                    $currency,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The payment currency could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY PAYSTACK CUSTOMER EMAIL
    |--------------------------------------------------------------------------
    */

        $paystackEmail =
            strtolower(
                trim(
                    $paystackData['customer']['email']
                        ?? ''
                )
            );

        $userEmail =
            strtolower(
                trim(
                    $user->email
                )
            );

        if (
            $paystackEmail !== '' &&
            $userEmail !== '' &&
            !hash_equals(
                $userEmail,
                $paystackEmail
            )
        ) {

            Log::critical(
                'ADDITIONAL PAYMENT CUSTOMER EMAIL MISMATCH',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'reference' =>
                    $reference,

                    'user_email' =>
                    $userEmail,

                    'paystack_email' =>
                    $paystackEmail,
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'The Paystack customer information could not be verified.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PAYMENT SUCCESS
    |--------------------------------------------------------------------------
    |
    | At this point all payment verification checks have passed.
    |
    | NOW we update the financial ledger and generate the document.
    |
    */

        try {

            DB::transaction(
                function () use (
                    $payment,
                    $debit,
                    $user,
                    $paymentItem,
                    $reference,
                    $verification,
                    $paystackData,
                    $expectedAmount,
                    $documentGenerationService
                ) {

                    /*
                |--------------------------------------------------------------------------
                | LOCK PAYMENT
                |--------------------------------------------------------------------------
                */

                    $lockedPayment =
                        Payment::where(
                            'id',
                            $payment->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedPayment) {

                        throw new \RuntimeException(
                            'Payment could not be locked.'
                        );
                    }

                    /*
                |--------------------------------------------------------------------------
                | LOCK DEBIT
                |--------------------------------------------------------------------------
                */

                    $lockedDebit =
                        Transaction::where(
                            'id',
                            $debit->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedDebit) {

                        throw new \RuntimeException(
                            'Debit transaction could not be locked.'
                        );
                    }

                    /*
                |--------------------------------------------------------------------------
                | CHECK FOR EXISTING CREDIT
                |--------------------------------------------------------------------------
                |
                | Primary relationship:
                |
                | credit.transaction_id = debit.id
                |
                */

                    $existingCredit =
                        Transaction::where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'type',
                            'credit'
                        )
                        ->where(
                            'payment_item_id',
                            $paymentItem->id
                        )
                        ->where(
                            'transaction_id',
                            $lockedDebit->id
                        )
                        ->lockForUpdate()
                        ->first();

                    /*
                |--------------------------------------------------------------------------
                | SECONDARY DUPLICATE PROTECTION
                |--------------------------------------------------------------------------
                */

                    if (!$existingCredit) {

                        $existingCredit =
                            Transaction::where(
                                'user_id',
                                $user->id
                            )
                            ->where(
                                'type',
                                'credit'
                            )
                            ->where(
                                'payment_item_id',
                                $paymentItem->id
                            )
                            ->whereJsonContains(
                                'gateway->paystack',
                                $reference
                            )
                            ->lockForUpdate()
                            ->first();
                    }

                    /*
                |--------------------------------------------------------------------------
                | CREDIT TRANSACTION
                |--------------------------------------------------------------------------
                */

                    $credit = null;

                    /*
                |--------------------------------------------------------------------------
                | EXISTING CREDIT
                |--------------------------------------------------------------------------
                */

                    if ($existingCredit) {

                        $credit =
                            $existingCredit;

                        /*
                    |--------------------------------------------------------------------------
                    | Make sure debit is paid.
                    |--------------------------------------------------------------------------
                    */

                        if (
                            $lockedDebit->status !==
                            'paid'
                        ) {

                            $lockedDebit->update([
                                'status' =>
                                'paid',
                            ]);
                        }
                    } else {

                        /*
                    |--------------------------------------------------------------------------
                    | MARK DEBIT AS PAID
                    |--------------------------------------------------------------------------
                    */

                        $lockedDebit->update([
                            'status' =>
                            'paid',

                            'gateway' =>
                            [
                                'paystack' =>
                                $reference,
                            ],
                        ]);

                        /*
                    |--------------------------------------------------------------------------
                    | CREATE CREDIT
                    |--------------------------------------------------------------------------
                    */

                        $credit =
                            Transaction::create([

                                'user_id' =>
                                $user->id,

                                'payment_item_id' =>
                                $paymentItem->id,

                                /*
                            |--------------------------------------------------------------------------
                            | INTERNAL LINK TO ORIGINAL DEBIT
                            |--------------------------------------------------------------------------
                            */

                                'transaction_id' =>
                                $lockedDebit->id,

                                'debit_transaction_id' =>
                                $lockedDebit->id,

                                'narration' =>
                                $paymentItem->name .
                                    ' payment received',

                                'type' =>
                                'credit',

                                'status' =>
                                'paid',

                                'amount' =>
                                $expectedAmount,

                                /*
                            |--------------------------------------------------------------------------
                            | PAYSTACK REFERENCE
                            |--------------------------------------------------------------------------
                            */

                                'gateway' =>
                                [
                                    'paystack' =>
                                    $reference,
                                ],
                            ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | VERIFY CREDIT EXISTS
                |--------------------------------------------------------------------------
                */

                    if (!$credit) {

                        throw new \RuntimeException(
                            'Credit transaction could not be created or located.'
                        );
                    }

                    /*
                |--------------------------------------------------------------------------
                | GENERATE DOCUMENT
                |--------------------------------------------------------------------------
                |
                | The payment item determines the document:
                |
                | payment_items.document_id
                |              ↓
                |          documents.id
                |
                | DocumentGenerationService handles:
                |
                | - document lookup
                | - document fields
                | - system fields
                | - member information
                | - membership information
                | - lifting_right_no
                | - document number
                | - tracking code
                | - expiry date
                | - field_values
                | - duplicate protection
                |
                */
/*
|--------------------------------------------------------------------------
| UPDATE PAYMENT
|--------------------------------------------------------------------------
|
| All Paystack verification checks have passed.
|
| We now mark the payment as paid BEFORE generating the document.
|
| Everything is inside the same DB transaction, so if document
| generation fails, the payment/ledger changes will roll back.
|--------------------------------------------------------------------------
*/

$paymentCompletedAt = now();

$lockedPayment->update([
    'gateway_status' =>
    'success',

    'status' =>
    'paid',

    'paid_at' =>
    $paymentCompletedAt,

    'verified_at' =>
    $paymentCompletedAt,

    /*
    |--------------------------------------------------------------------------
    | Paystack numeric transaction ID is intentionally NOT stored
    | in the transaction_id field.
    |--------------------------------------------------------------------------
    */

    'gateway_transaction_id' =>
    null,

    'gateway_response' =>
    $verification,
]);

/*
|--------------------------------------------------------------------------
| GENERATE DOCUMENT
|--------------------------------------------------------------------------
|
| The payment is now officially marked as PAID.
|
| DocumentGenerationService will:
|
| - use the exact payment
| - use the exact credit transaction
| - resolve the attached document
| - resolve system fields
| - use validated manual fields
| - generate document number
| - generate tracking code
| - calculate expiry
| - prevent duplicate documents
|--------------------------------------------------------------------------
*/

$generatedDocument =
    $documentGenerationService->generate(
        $user,
        $lockedPayment,
        $credit,
        $lockedPayment->document_field_values ?? []
    );

                    /*
                |--------------------------------------------------------------------------
                | SUCCESS LOG
                |--------------------------------------------------------------------------
                */

                    Log::info(
                        'ADDITIONAL PAYMENT SUCCESSFULLY COMPLETED',
                        [
                            'user_id' =>
                            $user->id,

                            'payment_id' =>
                            $lockedPayment->id,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'document_id' =>
                            $generatedDocument->document_id,

                            'debit_id' =>
                            $lockedDebit->id,

                            'credit_id' =>
                            $credit->id,

                            'generated_document_id' =>
                            $generatedDocument->id,

                            'document_number' =>
                            $generatedDocument->document_number,

                            'tracking_code' =>
                            $generatedDocument->tracking_code,

                            'amount' =>
                            $expectedAmount,

                            'reference' =>
                            $reference,

                            /*
                        |--------------------------------------------------------------------------
                        | Paystack numeric ID is ONLY logged.
                        |--------------------------------------------------------------------------
                        */

                            'paystack_transaction_id' =>
                            $paystackData['id']
                                ?? null,

                            'credit_already_existed' =>
                            (bool) $existingCredit,
                        ]
                    );
                }
            );
        } catch (\Throwable $e) {

            Log::error(
                'ADDITIONAL PAYMENT LEDGER/DOCUMENT UPDATE FAILED',
                [
                    'user_id' =>
                    $user->id,

                    'payment_id' =>
                    $payment->id,

                    'debit_id' =>
                    $debit->id,

                    'payment_item_id' =>
                    $paymentItem->id,

                    'reference' =>
                    $reference,

                    'error' =>
                    $e->getMessage(),

                    'file' =>
                    $e->getFile(),

                    'line' =>
                    $e->getLine(),

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment was verified, but we could not complete the payment and document processing. Please contact support.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | SUCCESS REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('payment.additional')
            ->with(
                'success',
                'Payment successful! Your payment for "' .
                    $paymentItem->name .
                    '" has been received.'
            );
    }


    public function getAdditionalPaymentFields(Request $request)
    {
        $request->validate([
            'payment_item_id' => ['required', 'integer'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Authenticate Member
    |--------------------------------------------------------------------------
    */
        $user = Auth::user();

        if (!$user || $user->membership_category_id === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only members can access additional payment fields.'
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Verify Approved Profile
    |--------------------------------------------------------------------------
    */
        $profile = MemberProfile::where('user_id', $user->id)->first();

        if (
            !$profile ||
            strtolower(trim($profile->status ?? '')) !== 'approved'
        ) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only approved members can access additional payment fields.'
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Verify Active Membership
    |--------------------------------------------------------------------------
    */
        $membership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$membership) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active membership was found for your account.'
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Verify Membership Category
    |--------------------------------------------------------------------------
    */
        $categoryId = (int) $user->membership_category_id;

        if ((int) $membership->membership_category_id !== $categoryId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your membership category could not be verified.'
            ], 403);
        }

        /*
    |--------------------------------------------------------------------------
    | Load Payment Item Securely
    |--------------------------------------------------------------------------
    |
    | We NEVER trust the payment item supplied by the browser.
    | The item must:
    | - exist
    | - be active
    | - not be a membership payment
    | - belong to the member's category
    |
    */
        $paymentItem = PaymentItem::query()
            ->with([
                'document' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->with([
                            'fields' => function ($query) {
                                $query
                                    ->where('is_system', false)
                                    ->orderBy('sort_order')
                                    ->orderBy('id');
                            },
                        ]);
                },
            ])
            ->where('id', $request->payment_item_id)
            ->where('is_active', true)
            ->whereNotIn('type', ['membership'])
            ->whereNotNull('membership_category_id')
            ->where('membership_category_id', $categoryId)
            ->first();

        if (!$paymentItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'The selected payment item is not available for your membership category.'
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | No Document Attached
    |--------------------------------------------------------------------------
    */
        if (!$paymentItem->document) {
            return response()->json([
                'status' => 'success',
                'has_document' => false,
                'document' => null,
                'fields' => [],
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Return Manual Fields Only
    |--------------------------------------------------------------------------
    |
    | System fields are deliberately excluded.
    |
    | Examples of system fields:
    | member_name
    | membership_number
    | payment_reference
    | payment_amount
    | document_number
    | tracking_code
    |
    | These will be resolved by DocumentGenerationService after payment.
    |
    */
        $fields = $paymentItem->document->fields
            ->map(function ($field) {
                return [
                    'field_key' => $field->field_key,
                    'label' => $field->label,
                    'field_type' => $field->field_type,
                    'section' => $field->section,
                    'placeholder' => $field->placeholder,
                    'default_value' => $field->default_value,
                    'options' => $field->options,
                    'is_required' => $field->is_required,
                ];
            })
            ->values();

        return response()->json([
            'status' => 'success',
            'has_document' => true,

            'document' => [
                'id' => $paymentItem->document->id,
                'name' => $paymentItem->document->name,
                'code' => $paymentItem->document->code,
                'type' => $paymentItem->document->type,
                'requires_form' => (bool) $paymentItem->document->requires_form,
            ],

            'fields' => $fields,
        ]);
    }
}
