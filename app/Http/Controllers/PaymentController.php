<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipCategoryFee;
use App\Models\MemberFee;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Transaction;
use App\Models\GeneratedDocument;
use App\Models\User;
use App\Services\DocumentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DOCUMENT GENERATION SERVICE
    |--------------------------------------------------------------------------
    */

    protected DocumentGenerationService $documentGenerationService;

    public function __construct(
        DocumentGenerationService $documentGenerationService
    ) {
        $this->documentGenerationService =
            $documentGenerationService;
    }

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

        $balance = $this->getBalance(
            $user->id
        );

        /*
        |--------------------------------------------------------------------------
        | UNPAID DEBITS
        |--------------------------------------------------------------------------
        */

        $unpaidDebits = Transaction::with(
            'paymentItem'
        )
            ->where(
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
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CURRENT MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        |
        | users.membership_category_id is authoritative.
        |
        */

        $currentCategory = null;

        if ($user->membership_category_id !== null) {
            $currentCategory = MembershipCategory::where(
                'id',
                $user->membership_category_id
            )
                ->where(
                    'status',
                    true
                )
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
            \Carbon\Carbon::parse(
                $membership->expires_at
            )->isFuture();

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

            $membershipFee =
                MembershipCategoryFee::where(
                    'membership_category_id',
                    $currentCategory->id
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

            if (!$membershipFee) {
                $membershipFee =
                    MembershipCategoryFee::where(
                        'membership_category_id',
                        $currentCategory->id
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
                ->where(
                    'status',
                    'unpaid'
                )
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
    | INITIALIZE PAYMENT
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
        | CREATE / UPDATE LOCAL PAYMENT RECORD
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
                        ->where(
                            'id',
                            $debit->id
                        )
                        ->where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'type',
                            'debit'
                        )
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
                    | PAYMENT ITEM
                    |--------------------------------------------------------------------------
                    */

                    $paymentItem =
                        $lockedDebit->paymentItem;

                    /*
                    |--------------------------------------------------------------------------
                    | MEMBERSHIP INFORMATION
                    |--------------------------------------------------------------------------
                    */

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

                        if (
                            $membershipCategoryId !== null
                        ) {
                            $categoryFee =
                                MembershipCategoryFee::where(
                                    'membership_category_id',
                                    $membershipCategoryId
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
                                $categoryFee =
                                    MembershipCategoryFee::where(
                                        'membership_category_id',
                                        $membershipCategoryId
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
                    | GENERATE FRESH PAYSTACK REFERENCE
                    |--------------------------------------------------------------------------
                    */

                    $reference = null;

                    for (
                        $attempt = 1;
                        $attempt <= 5;
                        $attempt++
                    ) {
                        $candidate =
                            'NACP-' .
                            strtoupper(
                                bin2hex(
                                    random_bytes(16)
                                )
                            );

                        if (
                            !Payment::where(
                                'reference',
                                $candidate
                            )->exists()
                        ) {
                            $reference =
                                $candidate;

                            break;
                        }
                    }

                    if (!$reference) {
                        throw new \RuntimeException(
                            'Unable to generate a unique payment reference.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SAVE PAYSTACK REFERENCE ON DEBIT
                    |--------------------------------------------------------------------------
                    */

                    $gateway =
                        is_array(
                            $lockedDebit->gateway
                        )
                        ? $lockedDebit->gateway
                        : [];

                    $gateway['paystack'] =
                        $reference;

                    $lockedDebit->update([
                        'gateway' =>
                            $gateway,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | FIND LOCAL PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    $paymentQuery = Payment::where(
                        'user_id',
                        $user->id
                    )
                        ->where(
                            'amount',
                            $amount
                        )
                        ->where(
                            'payment_type',
                            $paymentType
                        )
                        ->whereIn(
                            'status',
                            [
                                'pending',
                                'failed',
                            ]
                        );

                    if ($lockedDebit->payment_item_id !== null) {
                        $paymentQuery->where(
                            'payment_item_id',
                            $lockedDebit->payment_item_id
                        );
                    } else {
                        $paymentQuery->whereNull(
                            'payment_item_id'
                        );
                    }

                    if ($paymentType === 'member_fee') {
                        $paymentQuery->where(
                            'member_fee_id',
                            $memberFeeId
                        );
                    }

                    $payment = $paymentQuery
                        ->latest('id')
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
                        /*
                        |--------------------------------------------------------------------------
                        | REUSE LOCAL PAYMENT
                        |--------------------------------------------------------------------------
                        */

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

                            'paid_at' =>
                                null,

                            'verified_at' =>
                                null,
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
            $response = null;

            $data = null;

            $maxAttempts = 3;

            for (
                $attempt = 1;
                $attempt <= $maxAttempts;
                $attempt++
            ) {
                /*
                |--------------------------------------------------------------------------
                | GENERATE NEW REFERENCE ON RETRY
                |--------------------------------------------------------------------------
                */

                if ($attempt > 1) {
                    $newReference = null;

                    for (
                        $referenceAttempt = 1;
                        $referenceAttempt <= 5;
                        $referenceAttempt++
                    ) {
                        $candidate =
                            'NACP-' .
                            strtoupper(
                                bin2hex(
                                    random_bytes(16)
                                )
                            );

                        if (
                            !Payment::where(
                                'reference',
                                $candidate
                            )->exists()
                        ) {
                            $newReference =
                                $candidate;

                            break;
                        }
                    }

                    if (!$newReference) {
                        throw new \RuntimeException(
                            'Unable to generate a new unique Paystack reference.'
                        );
                    }

                    $reference =
                        $newReference;

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE DEBIT GATEWAY REFERENCE
                    |--------------------------------------------------------------------------
                    */

                    $gateway =
                        is_array(
                            $debit->gateway
                        )
                        ? $debit->gateway
                        : [];

                    $gateway['paystack'] =
                        $reference;

                    $debit->update([
                        'gateway' =>
                            $gateway,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE PAYMENT REFERENCE
                    |--------------------------------------------------------------------------
                    */

                    $payment->update([
                        'payment_reference' =>
                            $reference,

                        'paystack_reference' =>
                            $reference,

                        'reference' =>
                            $reference,

                        'gateway_transaction_id' =>
                            null,

                        'gateway_status' =>
                            'pending',

                        'status' =>
                            'pending',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | SEND TO PAYSTACK
                |--------------------------------------------------------------------------
                */

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
                                $user->email
                                ?: sprintf(
                                    'member%d@%s',
                                    $user->id,
                                    config(
                                        'services.paystack.placeholder_email_domain',
                                        'no-email.local'
                                    )
                                ),

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

                $data =
                    $response->json();

                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                if (
                    $response->successful() &&
                    ($data['status'] ?? false) === true
                ) {
                    break;
                }

                /*
                |--------------------------------------------------------------------------
                | DUPLICATE REFERENCE
                |--------------------------------------------------------------------------
                */

                if (
                    ($data['code'] ?? null) ===
                    'duplicate_reference'
                ) {
                    Log::warning(
                        'PAYSTACK DUPLICATE REFERENCE - RETRYING',
                        [
                            'transaction_id' =>
                                $debit->id,

                            'payment_id' =>
                                $payment->id,

                            'reference' =>
                                $reference,

                            'attempt' =>
                                $attempt,
                        ]
                    );

                    continue;
                }

                break;
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL RESPONSE CHECK
            |--------------------------------------------------------------------------
            */

            if (
                !$response ||
                !$response->successful() ||
                ($data['status'] ?? false) !== true
            ) {
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
                            $data,
                    ]
                );

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
                            ?? 'Unable to initialize payment with Paystack.',
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
            | SAVE PAYSTACK INITIALIZATION RESPONSE
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
            | REDIRECT MEMBER TO PAYSTACK
            |--------------------------------------------------------------------------
            */

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
            return Transaction::with(
                'paymentItem'
            )
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
            | PAYMENT ITEM LINK
            |--------------------------------------------------------------------------
            */

            $memberFeePaymentItem =
                PaymentItem::where(
                    'name',
                    $memberFee->name
                )->first();

            if ($memberFeePaymentItem) {
                $debit = Transaction::with(
                    'paymentItem'
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

            return Transaction::with(
                'paymentItem'
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

        $user = User::find(
            $userId
        );

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

        $category =
            MembershipCategory::where(
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

        $membership =
            Membership::where(
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

        $categoryFee =
            MembershipCategoryFee::where(
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
            $categoryFee =
                MembershipCategoryFee::where(
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

        return Transaction::with(
            'paymentItem'
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

            $data =
                $response->json();

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
            | PAYSTACK EXTERNAL TRANSACTION ID
            |--------------------------------------------------------------------------
            |
            | This is Paystack's own transaction ID.
            |
            | It belongs in:
            |
            | payments.gateway_transaction_id
            |
            | It does NOT belong in:
            |
            | transactions.transaction_id
            |
            */

            $paystackTransactionId =
                $paystackTransaction['id']
                ?? null;

            /*
            |--------------------------------------------------------------------------
            | FIND LOCAL PAYMENT
            |--------------------------------------------------------------------------
            */

            $payment = Payment::where(
                'reference',
                $reference
            )->first();

            /*
            |--------------------------------------------------------------------------
            | FIND DEBIT USING OUR INTERNAL DATABASE ID
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
            | FALLBACK: LOCAL PAYMENT RECORD
            |--------------------------------------------------------------------------
            */

            if (!$debit && $payment) {
                $debitQuery = Transaction::with(
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
                    );

                if ($payment->payment_item_id !== null) {
                    $debitQuery->where(
                        'payment_item_id',
                        $payment->payment_item_id
                    );
                } else {
                    $debitQuery->whereNull(
                        'payment_item_id'
                    );
                }

                $debit = $debitQuery
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
            | SETTLE FINANCIAL LEDGER
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | DocumentGenerationService is NOT called inside this
            | transaction.
            |
            | The financial settlement must commit first.
            |
            */

            $result = DB::transaction(
                function () use (
                    $debit,
                    $paystackTransaction,
                    $paystackTransactionId,
                    $reference
                ) {
                    $lockedDebit =
                        Transaction::with(
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

                    /*
                    |--------------------------------------------------------------------------
                    | FIND / LOCK PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    $payment =
                        Payment::where(
                            'reference',
                            $reference
                        )
                            ->lockForUpdate()
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PAYMENT IF NECESSARY
                    |--------------------------------------------------------------------------
                    */

                    if (!$payment) {
                        $user =
                            User::find(
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
                                $lockedDebit->payment_item_id
                                ? 'additional'
                                : 'membership',

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
                                $paystackTransactionId,

                            'gateway_status' =>
                                'success',

                            'status' =>
                                'pending',
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FIND EXISTING CREDIT
                    |--------------------------------------------------------------------------
                    |
                    | debit_transaction_id is the authoritative relationship.
                    |
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE LOCAL PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    $payment->update([
                        'paystack_reference' =>
                            $reference,

                        'reference' =>
                            $reference,

                        'gateway_transaction_id' =>
                            $paystackTransactionId,

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

                    /*
                    |--------------------------------------------------------------------------
                    | ALREADY SETTLED
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $lockedDebit->status === 'paid' &&
                        $existingCredit
                    ) {
                        $gateway =
                            is_array(
                                $lockedDebit->gateway
                            )
                            ? $lockedDebit->gateway
                            : [];

                        $gateway['paystack'] =
                            $reference;

                        /*
                        |--------------------------------------------------------------------------
                        | MAKE SURE DEBIT IS CORRECT
                        |--------------------------------------------------------------------------
                        */

                        $lockedDebit->update([
                            'status' =>
                                'paid',

                            'gateway' =>
                                $gateway,
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | MAKE SURE CREDIT IS CORRECT
                        |--------------------------------------------------------------------------
                        */

                        $existingCredit->update([
                            'status' =>
                                'paid',

                            'transaction_id' =>
                                null,

                            'gateway' => [
                                'paystack' =>
                                    $reference,
                            ],
                        ]);

                        return [
                            'debit' =>
                                $lockedDebit,

                            'credit' =>
                                $existingCredit,

                            'payment' =>
                                $payment,

                            'newly_settled' =>
                                false,
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CREDIT ALREADY EXISTS BUT DEBIT WAS NOT PAID
                    |--------------------------------------------------------------------------
                    */

                    if ($existingCredit) {
                        $gateway =
                            is_array(
                                $lockedDebit->gateway
                            )
                            ? $lockedDebit->gateway
                            : [];

                        $gateway['paystack'] =
                            $reference;

                        /*
                        |--------------------------------------------------------------------------
                        | CREDIT MUST BE PAID
                        |--------------------------------------------------------------------------
                        */

                        $existingCredit->update([
                            'status' =>
                                'paid',

                            'transaction_id' =>
                                null,

                            'gateway' => [
                                'paystack' =>
                                    $reference,
                            ],
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | DEBIT MUST BE PAID
                        |--------------------------------------------------------------------------
                        */

                        $lockedDebit->update([
                            'status' =>
                                'paid',

                            'gateway' =>
                                $gateway,
                        ]);

                        return [
                            'debit' =>
                                $lockedDebit,

                            'credit' =>
                                $existingCredit,

                            'payment' =>
                                $payment,

                            'newly_settled' =>
                                false,
                        ];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE CREDIT TRANSACTION
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | transaction_id = NULL
                    |
                    | debit_transaction_id = internal debit ID
                    |
                    | Paystack external ID is stored in:
                    |
                    | payments.gateway_transaction_id
                    |
                    */

                    $credit = Transaction::create([
                        'user_id' =>
                            $lockedDebit->user_id,

                        'payment_item_id' =>
                            $lockedDebit->payment_item_id,

                        /*
                        |--------------------------------------------------------------------------
                        | AUTHORITATIVE LEDGER LINK
                        |--------------------------------------------------------------------------
                        */

                        'debit_transaction_id' =>
                            $lockedDebit->id,

                        'narration' =>
                            'Payment received - ' .
                            $lockedDebit->narration,

                        'type' =>
                            'credit',

                        'status' =>
                            'paid',

                        'amount' =>
                            $lockedDebit->amount,

                        /*
                        |--------------------------------------------------------------------------
                        | NEVER STORE PAYSTACK ID HERE
                        |--------------------------------------------------------------------------
                        */

                        'transaction_id' =>
                            null,

                        'gateway' => [
                            'paystack' =>
                                $reference,
                        ],
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE DEBIT
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | RETURN FINANCIAL RECORDS
                    |--------------------------------------------------------------------------
                    |
                    | DO NOT GENERATE DOCUMENT HERE.
                    |
                    | The outer DB transaction must commit first.
                    |
                    */

                    return [
                        'debit' =>
                            $lockedDebit,

                        'credit' =>
                            $credit,

                        'payment' =>
                            $payment,

                        'newly_settled' =>
                            true,
                    ];
                }
            );

            /*
            |--------------------------------------------------------------------------
            | FINANCIAL TRANSACTION HAS NOW COMMITTED
            |--------------------------------------------------------------------------
            |
            | At this point:
            |
            | Payment = paid
            | Debit   = paid
            | Credit  = paid
            |
            | It is now safe to generate the document.
            |
            */

            try {
                $this->processSuccessfulPayment(
                    $result['payment'],
                    $result['credit']
                );
            } catch (\Throwable $documentException) {
                /*
                |--------------------------------------------------------------------------
                | DOCUMENT GENERATION MUST NOT ROLLBACK PAYMENT
                |--------------------------------------------------------------------------
                */

                Log::error(
                    'DOCUMENT GENERATION FAILED AFTER SUCCESSFUL PAYMENT',
                    [
                        'payment_id' =>
                            $result['payment']->id,

                        'payment_item_id' =>
                            $result['payment']->payment_item_id,

                        'credit_transaction_id' =>
                            $result['credit']->id,

                        'debit_transaction_id' =>
                            $result['credit']->debit_transaction_id,

                        'user_id' =>
                            $result['payment']->user_id,

                        'reference' =>
                            $reference,

                        'error' =>
                            $documentException->getMessage(),

                        'file' =>
                            $documentException->getFile(),

                        'line' =>
                            $documentException->getLine(),
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            if ($result['newly_settled']) {
                return redirect()
                    ->route(
                        'payment.success'
                    )
                    ->with(
                        'success',
                        'Payment was successful.'
                    );
            }

            return redirect()
                ->route(
                    'payment.success'
                )
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
                ->route(
                    'payment.index'
                )
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
    |
    | This method runs AFTER the financial DB transaction has committed.
    |
    | Financial flow:
    |
    | Payment = paid
    | Debit   = paid
    | Credit  = paid
    |
    | Then:
    |
    | Payment + Credit
    |        ↓
    | DocumentGenerationService
    |        ↓
    | GeneratedDocument
    |
    */

    private function processSuccessfulPayment(
        Payment $payment,
        Transaction $credit
    ): void {
        /*
        |--------------------------------------------------------------------------
        | BASIC VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($payment->status !== 'paid') {
            throw new \RuntimeException(
                'Cannot generate document because payment is not marked as paid.'
            );
        }

        if ($credit->type !== 'credit') {
            throw new \RuntimeException(
                'Cannot generate document because the supplied transaction is not a credit.'
            );
        }

        if ($credit->status !== 'paid') {
            throw new \RuntimeException(
                'Cannot generate document because the credit transaction is not paid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREDIT → DEBIT RELATIONSHIP
        |--------------------------------------------------------------------------
        */

        if (!$credit->debit_transaction_id) {
            throw new \RuntimeException(
                'Credit transaction is not linked to a debit transaction.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD DEBIT
        |--------------------------------------------------------------------------
        */

        $debit = Transaction::with(
            'paymentItem'
        )
            ->where(
                'id',
                $credit->debit_transaction_id
            )
            ->where(
                'type',
                'debit'
            )
            ->first();

        if (!$debit) {
            throw new \RuntimeException(
                'The debit transaction linked to the credit could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE DEBIT
        |--------------------------------------------------------------------------
        */

        if (
            (int) $debit->user_id !==
            (int) $payment->user_id
        ) {
            throw new \RuntimeException(
                'The debit transaction does not belong to the payment user.'
            );
        }

        if ($debit->status !== 'paid') {
            throw new \RuntimeException(
                'The debit transaction is not marked as paid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM MATCH
        |--------------------------------------------------------------------------
        */

        if (
            $payment->payment_item_id !== null &&
            $debit->payment_item_id !== null &&
            (int) $payment->payment_item_id !==
            (int) $debit->payment_item_id
        ) {
            throw new \RuntimeException(
                'Payment item does not match the settled debit transaction.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD PAYMENT ITEM + DOCUMENT
        |--------------------------------------------------------------------------
        */

        $payment->loadMissing([
            'paymentItem.document',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM
        |--------------------------------------------------------------------------
        */

        $paymentItem =
            $payment->paymentItem;

        /*
        |--------------------------------------------------------------------------
        | NO PAYMENT ITEM
        |--------------------------------------------------------------------------
        |
        | Membership payments may not necessarily use a PaymentItem.
        |
        */

        if (!$paymentItem) {
            Log::info(
                'DOCUMENT GENERATION SKIPPED - NO PAYMENT ITEM',
                [
                    'payment_id' =>
                        $payment->id,

                    'credit_transaction_id' =>
                        $credit->id,

                    'debit_transaction_id' =>
                        $credit->debit_transaction_id,

                    'user_id' =>
                        $payment->user_id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Membership documents are NOT generated here.
        |
        | They are generated through the membership approval workflow.
        |
        */

        if (
            $payment->payment_type ===
            'membership'
        ) {
            Log::info(
                'MEMBERSHIP PAYMENT COMPLETED - DOCUMENT GENERATION DEFERRED',
                [
                    'payment_id' =>
                        $payment->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'credit_transaction_id' =>
                        $credit->id,

                    'debit_transaction_id' =>
                        $credit->debit_transaction_id,

                    'user_id' =>
                        $payment->user_id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NO DOCUMENT ATTACHED
        |--------------------------------------------------------------------------
        |
        | A PaymentItem may legitimately have no document.
        |
        */

        if (!$paymentItem->document) {
            Log::info(
                'DOCUMENT GENERATION SKIPPED - NO DOCUMENT ATTACHED',
                [
                    'payment_id' =>
                        $payment->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'credit_transaction_id' =>
                        $credit->id,

                    'debit_transaction_id' =>
                        $credit->debit_transaction_id,

                    'user_id' =>
                        $payment->user_id,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT GENERATION
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We pass:
        |
        | 1. The exact Payment record settled by Paystack.
        | 2. The exact CREDIT transaction created by the ledger.
        |
        | DocumentGenerationService is responsible for resolving:
        |
        | SYSTEM fields
        | MANUAL fields
        | Document template
        | GeneratedDocument
        |
        */

        $generatedDocument =
            $this->documentGenerationService->generate(
                $payment,
                $credit
            );

        /*
        |--------------------------------------------------------------------------
        | SUCCESS LOG
        |--------------------------------------------------------------------------
        */

        if ($generatedDocument) {
            Log::info(
                'DOCUMENT GENERATED AFTER SUCCESSFUL PAYMENT',
                [
                    'generated_document_id' =>
                        $generatedDocument->id,

                    'payment_id' =>
                        $payment->id,

                    'payment_item_id' =>
                        $paymentItem->id,

                    'document_id' =>
                        $paymentItem->document->id,

                    'document_code' =>
                        $paymentItem->document->code,

                    'credit_transaction_id' =>
                        $credit->id,

                    'debit_transaction_id' =>
                        $credit->debit_transaction_id,

                    'user_id' =>
                        $payment->user_id,
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
                ->route(
                    'payment.index'
                )
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







    // ADDITIONAL PAYMENT METHODS CAN BE ADDED HERE IN THE FUTURE, SUCH AS PAYPAL, STRIPE, ETC.
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



    /**
     * Initialize an additional payment or document renewal request.
     */
    public function initializeAdditional(Request $request)
    {
        $request->validate([
            'payment_item_id' => ['required', 'integer', 'exists:payment_items,id'],
            'transaction_id' => ['nullable', 'integer', 'exists:transactions,id'],
            'renewal_document_id' => ['nullable', 'integer', 'exists:generated_documents,id'],
            'document_field_values' => ['nullable', 'array'],
        ]);

        $user = Auth::user();

        // 1. Check user membership eligibility
        if ($errorResponse = $this->validateUserEligibility($user)) {
            return $errorResponse;
        }

        $categoryId = (int) $user->membership_category_id;

        // 2. Validate renewal document (if supplied)
        $renewalDocument = null;
        if ($request->filled('renewal_document_id')) {
            $renewalResult = $this->validateRenewalDocument(
                $request->renewal_document_id,
                $request->payment_item_id,
                $user
            );

            if ($renewalResult instanceof RedirectResponse) {
                return $renewalResult;
            }

            $renewalDocument = $renewalResult;
        }

        // 3. Verify and load the payment item
        $paymentItem = $this->getAndVerifyPaymentItem(
            $request->payment_item_id,
            $categoryId,
            $user->id,
            $renewalDocument?->id
        );

        if (!$paymentItem) {
            return back()->with('error', 'The selected payment item is not available for your membership category.');
        }

        // 4. Verify membership category consistency
        if ($errorResponse = $this->verifyActiveMembershipCategory($user->id, $categoryId, $paymentItem->id)) {
            return $errorResponse;
        }

        // 5. Check payment item amount
        $amount = (float) $paymentItem->amount;
        if ($amount <= 0) {
            Log::error('INVALID ADDITIONAL PAYMENT ITEM AMOUNT', [
                'user_id' => $user->id,
                'payment_item_id' => $paymentItem->id,
                'amount' => $paymentItem->amount,
            ]);

            return back()->with('error', 'The selected payment item has an invalid amount.');
        }

        // 6. Process and validate document fields
        $submittedDocumentFieldValues = $request->input('document_field_values', []) ?? [];

        $this->logDocumentFieldInfo(
            $user->id,
            $paymentItem,
            $renewalDocument?->id,
            $submittedDocumentFieldValues
        );

        $fieldValidationResult = $this->processAndValidateDocumentFields(
            $paymentItem,
            $submittedDocumentFieldValues,
            $user->id,
            $renewalDocument?->id
        );

        if ($fieldValidationResult instanceof RedirectResponse) {
            return $fieldValidationResult;
        }

        $documentFieldValues = $fieldValidationResult;

        // Proceed with payment initialization pipeline using $paymentItem, $amount, $renewalDocument, and $documentFieldValues...
    }

    /**
     * Validate user auth state and profile status.
     */
    protected function validateUserEligibility($user): ?RedirectResponse
    {
        if (!$user || $user->membership_category_id === null) {
            return back()->with('error', 'Only members can make additional payments.');
        }

        $profile = MemberProfile::where('user_id', $user->id)->first();

        if (!$profile || strtolower(trim($profile->status ?? '')) !== 'approved') {
            return back()->with('error', 'Only approved members can make additional payments.');
        }

        $membership = Membership::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$membership) {
            return back()->with('error', 'No active membership was found for your account.');
        }

        return null;
    }

    /**
     * Validate existing generated document for renewal.
     */
    protected function validateRenewalDocument(int $renewalDocumentId, int $submittedPaymentItemId, $user): GeneratedDocument|RedirectResponse
    {
        $renewalDocument = GeneratedDocument::query()
            ->with([
                'document',
                'transaction.paymentItem.renewalPaymentItem',
            ])
            ->where('id', $renewalDocumentId)
            ->where('user_id', $user->id)
            ->first();

        if (!$renewalDocument) {
            Log::warning('INVALID DOCUMENT RENEWAL REQUEST', [
                'user_id' => $user->id,
                'renewal_document_id' => $renewalDocumentId,
                'payment_item_id' => $submittedPaymentItemId,
            ]);

            return back()->with('error', 'The document you are trying to renew could not be found.');
        }

        if (!$renewalDocument->expires_at || !$renewalDocument->expires_at->isPast()) {
            return back()->with('error', 'This document does not need to be renewed yet.');
        }

        if ($renewalDocument->status === 'revoked') {
            return back()->with('error', 'A revoked document cannot be renewed.');
        }

        $originalTransaction = $renewalDocument->transaction;

        if (!$originalTransaction || !$originalTransaction->paymentItem) {
            Log::error('RENEWAL DOCUMENT ORIGINAL PAYMENT ITEM NOT FOUND', [
                'user_id' => $user->id,
                'generated_document_id' => $renewalDocument->id,
            ]);

            return back()->with('error', 'The renewal configuration for this document could not be found.');
        }

        $originalPaymentItem = $originalTransaction->paymentItem;

        if (!$originalPaymentItem->is_renewable) {
            return back()->with('error', 'This document is not configured for renewal.');
        }

        $configuredRenewalPaymentItem = $originalPaymentItem->renewalPaymentItem;

        if (!$configuredRenewalPaymentItem) {
            Log::warning('RENEWAL PAYMENT ITEM NOT CONFIGURED', [
                'user_id' => $user->id,
                'generated_document_id' => $renewalDocument->id,
                'original_payment_item_id' => $originalPaymentItem->id,
            ]);

            return back()->with('error', 'No renewal payment item has been configured for this document.');
        }

        if ((int) $configuredRenewalPaymentItem->id !== (int) $submittedPaymentItemId) {
            Log::critical('INVALID RENEWAL PAYMENT ITEM SUBMITTED', [
                'user_id' => $user->id,
                'generated_document_id' => $renewalDocument->id,
                'submitted_payment_item_id' => $submittedPaymentItemId,
                'expected_renewal_payment_item_id' => $configuredRenewalPaymentItem->id,
            ]);

            return back()->with('error', 'The selected renewal payment item is not valid for this document.');
        }

        return $renewalDocument;
    }

    /**
     * Retrieve and load relations for the target PaymentItem.
     */
    protected function getAndVerifyPaymentItem(int $paymentItemId, int $categoryId, int $userId, ?int $renewalDocumentId): ?PaymentItem
    {
        $paymentItem = PaymentItem::where('id', $paymentItemId)
            ->where('is_active', true)
            ->whereNotIn('type', ['membership'])
            ->whereNotNull('membership_category_id')
            ->where('membership_category_id', $categoryId)
            ->first();

        if (!$paymentItem) {
            Log::warning('INVALID ADDITIONAL PAYMENT ITEM', [
                'user_id' => $userId,
                'user_membership_category_id' => $categoryId,
                'payment_item_id' => $paymentItemId,
                'renewal_document_id' => $renewalDocumentId,
            ]);

            return null;
        }

        $paymentItem->load([
            'document' => function ($query) {
                $query->where('is_active', true)
                    ->with([
                        'fields' => function ($query) {
                            $query->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ]);
            },
        ]);

        return $paymentItem;
    }

    /**
     * Verify that active membership matches expected category ID.
     */
    protected function verifyActiveMembershipCategory(int $userId, int $categoryId, int $paymentItemId): ?RedirectResponse
    {
        $membership = Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$membership || (int) $membership->membership_category_id !== $categoryId) {
            Log::critical('ADDITIONAL PAYMENT MEMBERSHIP CATEGORY MISMATCH', [
                'user_id' => $userId,
                'user_membership_category_id' => $categoryId,
                'membership_category_id' => $membership?->membership_category_id,
                'payment_item_id' => $paymentItemId,
            ]);

            return back()->with('error', 'Your membership category could not be verified.');
        }

        return null;
    }

    /**
     * Log initial context for incoming manual field payloads.
     */
    protected function logDocumentFieldInfo(int $userId, PaymentItem $paymentItem, ?int $renewalDocumentId, array $submittedValues): void
    {
        Log::info('ADDITIONAL PAYMENT DOCUMENT FIELD VALUES RECEIVED', [
            'user_id' => $userId,
            'payment_item_id' => $paymentItem->id,
            'renewal_document_id' => $renewalDocumentId,
            'document_id' => $paymentItem->document?->id,
            'submitted_document_field_values' => $submittedValues,
            'manual_fields' => $paymentItem->document
                ? $paymentItem->document->fields
                    ->where('is_system', false)
                    ->map(fn ($field) => [
                        'field_key' => $field->field_key,
                        'label' => $field->label,
                        'field_type' => $field->field_type,
                        'is_required' => $field->is_required,
                        'default_value' => $field->default_value,
                    ])
                    ->values()
                    ->all()
                : [],
        ]);
    }

    /**
     * Validate custom manual input values for a document payment.
     */
    protected function processAndValidateDocumentFields(
        PaymentItem $paymentItem,
        array $submittedValues,
        int $userId,
        ?int $renewalDocumentId
    ): array|RedirectResponse {
        if (!$paymentItem->document) {
            return [];
        }

        $manualFields = $paymentItem->document->fields
            ->where('is_system', false)
            ->values();

        // System-only document check
        if ($manualFields->isEmpty()) {
            if (!empty($submittedValues)) {
                Log::warning('SYSTEM DOCUMENT FIELDS SUBMITTED BY MEMBER', [
                    'user_id' => $userId,
                    'payment_item_id' => $paymentItem->id,
                    'renewal_document_id' => $renewalDocumentId,
                    'submitted_keys' => array_keys($submittedValues),
                ]);

                return back()
                    ->withInput()
                    ->with('error', 'This document does not accept manually supplied fields.');
            }

            return [];
        }

        // Verify key authorization
        $allowedFieldKeys = $manualFields->pluck('field_key')->values()->all();

        foreach (array_keys($submittedValues) as $submittedKey) {
            if (!in_array($submittedKey, $allowedFieldKeys, true)) {
                Log::warning('UNAUTHORIZED DOCUMENT FIELD SUBMITTED', [
                    'user_id' => $userId,
                    'payment_item_id' => $paymentItem->id,
                    'renewal_document_id' => $renewalDocumentId,
                    'field_key' => $submittedKey,
                ]);

                return back()
                    ->withInput()
                    ->with('error', 'An invalid document field was submitted.');
            }
        }

        // Validate values by type
        $documentFieldValues = [];

        foreach ($manualFields as $field) {
            $fieldKey = $field->field_key;
            $value = $submittedValues[$fieldKey] ?? null;

            if (is_string($value) && trim($value) === '') {
                $value = null;
            }

            if ($field->is_required && ($value === null || $value === '')) {
                return back()
                    ->withInput()
                    ->with('error', "{$field->label} is required.");
            }

            if ($value === null) {
                continue;
            }

            $validationError = $this->validateSingleFieldValue($field, $value);
            if ($validationError) {
                return back()->withInput()->with('error', $validationError);
            }

            $documentFieldValues[$fieldKey] = $value;
        }

        return $documentFieldValues;
    }

    /**
     * Validate an individual field based on type rules.
     */
    protected function validateSingleFieldValue($field, &$value): ?string
    {
        switch ($field->field_type) {
            case 'text':
            case 'textarea':
                if (!is_string($value)) {
                    return "{$field->label} must be text.";
                }
                $value = trim($value);
                break;

            case 'number':
                if (!is_numeric($value)) {
                    return "{$field->label} must be a valid number.";
                }
                $value = (string) $value;
                break;

            case 'date':
                if (!is_string($value)) {
                    return "{$field->label} must be a valid date.";
                }
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $value);
                    if (!$date || $date->format('Y-m-d') !== $value) {
                        return "{$field->label} must be a valid date.";
                    }
                } catch (\Throwable $e) {
                    return "{$field->label} must be a valid date.";
                }
                break;

            case 'time':
                if (!is_string($value)) {
                    return "{$field->label} must be a valid time.";
                }
                try {
                    $time = Carbon::createFromFormat('H:i', $value);
                    if (!$time || $time->format('H:i') !== $value) {
                        return "{$field->label} must be a valid time.";
                    }
                } catch (\Throwable $e) {
                    return "{$field->label} must be a valid time.";
                }
                break;

            case 'select':
                $options = is_array($field->options) ? $field->options : [];
                $allowedOptions = [];

                foreach ($options as $option) {
                    if (is_string($option) || is_numeric($option)) {
                        $allowedOptions[] = (string) $option;
                        continue;
                    }

                    if (is_array($option) && array_key_exists('value', $option)) {
                        $allowedOptions[] = (string) $option['value'];
                    }
                }

                if (!in_array((string) $value, $allowedOptions, true)) {
                    return "The selected value for {$field->label} is invalid.";
                }
                break;
        }

        return null;
    }
}
