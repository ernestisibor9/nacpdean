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
use App\Models\OperationalRightsDocument;
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
        // $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | ADMIN PAYING ON BEHALF OF A MEMBER
    |--------------------------------------------------------------------------
    |
    | If the admin initiated the payment from the admin panel, we swap
    | the "effective user" to the member. The admin remains authenticated,
    | but everything below uses $user = the member.
    |
    */

        $payingOnBehalfOf = null;
        $effectiveUser    = Auth::user();

        if (
            Auth::user()->role === 'admin' &&
            session()->has('admin_paying_for_member_id')
        ) {
            $memberId = (int) session('admin_paying_for_member_id');

            $member = User::where('id', $memberId)
                ->where('role', 'member')
                ->first();

            if ($member) {
                $effectiveUser    = $member;
                $payingOnBehalfOf = $member;
            } else {
                // Member gone — clear the flag
                session()->forget([
                    'admin_paying_for_member_id',
                    'admin_paying_return_url',
                ]);
            }
        }

        $user = $effectiveUser;

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
                'hasActiveAnnualMembership',
                'payingOnBehalfOf'
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

        // $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | ADMIN PAYING ON BEHALF OF A MEMBER
    |--------------------------------------------------------------------------
    */

        $effectiveUser = Auth::user();

        if (
            Auth::user()->role === 'admin' &&
            session()->has('admin_paying_for_member_id')
        ) {
            $memberId = (int) session('admin_paying_for_member_id');

            $member = User::where('id', $memberId)
                ->where('role', 'member')
                ->first();

            if ($member) {
                $effectiveUser = $member;
            }
        }

        $user = $effectiveUser;

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

            // if (
            //     !Auth::check() ||
            //     (int) $debit->user_id !==
            //     (int) Auth::id()
            // ) {
            //     Log::critical(
            //         'PAYSTACK USER MISMATCH',
            //         [
            //             'transaction_id' =>
            //             $debit->id,

            //             'transaction_user_id' =>
            //             $debit->user_id,

            //             'authenticated_user_id' =>
            //             Auth::id(),

            //             'reference' =>
            //             $reference,
            //         ]
            //     );

            //     abort(403);
            // }

            /*
|--------------------------------------------------------------------------
| USER SECURITY — allow admin-on-behalf
|--------------------------------------------------------------------------
*/

            $isAdminOnBehalf =
                Auth::check() &&
                Auth::user()->role === 'admin' &&
                session()->has('admin_paying_for_member_id') &&
                (int) session('admin_paying_for_member_id') === (int) $debit->user_id;

            if (
                !$isAdminOnBehalf &&
                (
                    !Auth::check() ||
                    (int) $debit->user_id !== (int) Auth::id()
                )
            ) {
                Log::critical(
                    'PAYSTACK USER MISMATCH',
                    [
                        'transaction_id'      => $debit->id,
                        'transaction_user_id' => $debit->user_id,
                        'authenticated_user_id' => Auth::id(),
                        'reference'           => $reference,
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

            /*
|--------------------------------------------------------------------------
| ADMIN ON-BEHALF RETURN
|--------------------------------------------------------------------------
|
| If the payment was initiated by an admin on behalf of a member,
| clear the session flag and send the admin back to their member list
| instead of the member payment success page.
|
*/

            if ($isAdminOnBehalf) {
                $adminReturnUrl = session(
                    'admin_paying_return_url',
                    route('admin.member.index')
                );

                session()->forget([
                    'admin_paying_for_member_id',
                    'admin_paying_return_url',
                ]);

                return redirect($adminReturnUrl)
                    ->with(
                        'success',
                        'Membership fee paid successfully on behalf of the member.'
                    );
            }

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
    | LOAD PAYMENT ITEM + ALL GENERATION DOCUMENTS
    |--------------------------------------------------------------------------
    */

        $payment->loadMissing([
            'paymentItem.documents' => function ($query) {
                $query
                    ->where(
                        'documents.is_active',
                        true
                    )
                    ->wherePivot(
                        'generate_after_payment',
                        true
                    )
                    ->with([
                        'fields' => function ($query) {
                            $query
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        },
                    ]);
            },
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
    | Membership documents are handled by the membership approval workflow.
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
    | GENERATION DOCUMENTS
    |--------------------------------------------------------------------------
    */

        $documents =
            $paymentItem->documents
            ->filter(
                function ($document) {
                    return $document->is_active &&
                        (bool) $document->pivot->generate_after_payment;
                }
            )
            ->values();

        /*
    |--------------------------------------------------------------------------
    | NO DOCUMENTS
    |--------------------------------------------------------------------------
    */

        if ($documents->isEmpty()) {
            Log::info(
                'DOCUMENT GENERATION SKIPPED - NO DOCUMENTS ATTACHED',
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
    | MULTIPLE DOCUMENTS
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Afforestation Receipt
    | +
    | Traceability Transit Pass
    |
    | Both use the SAME payment.
    | Both use the SAME credit transaction.
    |
    */

        if ($documents->count() > 1) {
            $generatedDocuments =
                $this->documentGenerationService->generateMultiple(
                    $payment,
                    $credit,
                    $payment->document_field_values ?? []
                );

            foreach (
                $generatedDocuments as $generatedDocument
            ) {
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
                        $generatedDocument->document_id,

                        'document_code' =>
                        $generatedDocument->document?->code,

                        'credit_transaction_id' =>
                        $credit->id,

                        'debit_transaction_id' =>
                        $credit->debit_transaction_id,

                        'user_id' =>
                        $payment->user_id,
                    ]
                );
            }

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | SINGLE DOCUMENT
    |--------------------------------------------------------------------------
    |
    | Existing single-document payment items continue using the existing
    | generation method.
    |
    */

        $generatedDocument =
            $this->documentGenerationService->generate(
                $payment,
                $credit,
                $payment->document_field_values ?? []
            );

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
                    $generatedDocument->document_id,

                    'document_code' =>
                    $generatedDocument->document?->code,

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
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'No successful payment was found.',
                ]);
        }

        $debit = $credit->debitTransaction;

        $balance = $this->getBalance($user->id);

        /*
    |--------------------------------------------------------------------------
    | PROFILE STATUS
    |--------------------------------------------------------------------------
    |
    | Determines which call-to-action the success page should show.
    |
    | No profile record  → "Complete Application Form"
    | Has profile record → "View Your Documents"
    |
    */

        $hasProfile = MemberProfile::query()
            ->where('user_id', $user->id)
            ->exists();

        return view(
            'member.payment.success',
            compact(
                'credit',
                'debit',
                'balance',
                'hasProfile'
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
    |
    | users.membership_category_id remains authoritative.
    |
    */

        $categoryId =
            (int) $user->membership_category_id;

        /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEMS
    |--------------------------------------------------------------------------
    |
    | Payment item eligibility is now determined through:
    |
    | payment_item_categories
    |
    | NOT:
    |
    | payment_items.membership_category_id
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
            ->whereHas(
                'membershipCategories',
                function ($query) use ($categoryId) {
                    $query->where(
                        'membership_categories.id',
                        $categoryId
                    )
                        ->where(
                            'membership_categories.status',
                            true
                        );
                }
            )
            ->with([
                'membershipCategories',
                'documents' => function ($query) {
                    $query
                        ->where(
                            'documents.is_active',
                            true
                        )
                        ->wherePivot(
                            'generate_after_payment',
                            true
                        )
                        ->with([
                            'fields' => function ($query) {
                                $query
                                    ->orderBy('sort_order')
                                    ->orderBy('id');
                            },
                        ]);
                },
            ])
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
        Log::info('RAW ADDITIONAL PAYMENT REQUEST', [
            'user_id' => Auth::id(),
            'payment_item_id' => $request->input('payment_item_id'),
            'document_field_values' => $request->input(
                'document_field_values',
                []
            ),
            'all_request' => $request->all(),
        ]);

        $request->validate([
            'payment_item_id' => [
                'required',
                'integer',
                'exists:payment_items,id'
            ],
            'transaction_id' => [
                'nullable',
                'integer',
                'exists:transactions,id'
            ],
            'renewal_document_id' => [
                'nullable',
                'integer',
                'exists:generated_documents,id'
            ],
            'document_field_values' => [
                'nullable',
                'array'
            ],
        ]);

        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Payment Email
    |--------------------------------------------------------------------------
    |
    | Email is NOT required by the NACPDEAN application.
    |
    | Paystack requires an email address when initializing a transaction.
    | If the user has an email, use it. Otherwise, generate an internal
    | email address based on the user's ID.
    |
    |--------------------------------------------------------------------------
    */

        $paymentEmail = $user->email;

        if (!$paymentEmail) {
            $paymentEmail = 'user' . $user->id . '@nacpdean.org';
        }

        /*
    |--------------------------------------------------------------------------
    | 1. Check user membership eligibility
    |--------------------------------------------------------------------------
    */

        if ($errorResponse = $this->validateUserEligibility($user)) {
            return $errorResponse;
        }

        $categoryId = (int) $user->membership_category_id;

        /*
    |--------------------------------------------------------------------------
    | 2. Validate renewal document (if supplied)
    |--------------------------------------------------------------------------
    */

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

        /*
    |--------------------------------------------------------------------------
    | 3. Verify and load the payment item
    |--------------------------------------------------------------------------
    */

        $paymentItem = $this->getAndVerifyPaymentItem(
            $request->payment_item_id,
            $categoryId,
            $user->id,
            $renewalDocument?->id
        );

        if (!$paymentItem) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected payment item is not available for your membership category.',
                ], 422);
            }

            return back()->with(
                'error',
                'The selected payment item is not available for your membership category.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | 4. Verify membership category consistency
    |--------------------------------------------------------------------------
    */

        if (
            $errorResponse = $this->verifyActiveMembershipCategory(
                $user->id,
                $categoryId,
                $paymentItem->id
            )
        ) {
            return $errorResponse;
        }

        /*
    |--------------------------------------------------------------------------
    | 5. Check payment item amount
    |--------------------------------------------------------------------------
    */

        $amount = (float) $paymentItem->amount;

        if ($amount <= 0) {
            Log::error('INVALID ADDITIONAL PAYMENT ITEM AMOUNT', [
                'user_id' => $user->id,
                'payment_item_id' => $paymentItem->id,
                'amount' => $paymentItem->amount,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected payment item has an invalid amount.',
                ], 422);
            }

            return back()->with(
                'error',
                'The selected payment item has an invalid amount.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | 6. Process and validate document fields
    |--------------------------------------------------------------------------
    */

        $submittedDocumentFieldValues =
            $request->input('document_field_values', []) ?? [];

        $this->logDocumentFieldInfo(
            $user->id,
            $paymentItem,
            $renewalDocument?->id,
            $submittedDocumentFieldValues
        );

        $fieldValidationResult =
            $this->processAndValidateDocumentFields(
                $paymentItem,
                $submittedDocumentFieldValues,
                $user->id,
                $renewalDocument?->id
            );

        if ($fieldValidationResult instanceof RedirectResponse) {
            return $fieldValidationResult;
        }

        $documentFieldValues = $fieldValidationResult;

        /*
    |--------------------------------------------------------------------------
    | 7. Generate Paystack reference
    |--------------------------------------------------------------------------
    */

        $paystackReference = 'NACP-' . strtoupper(
            Str::random(16)
        );

        /*
    |--------------------------------------------------------------------------
    | 8. Find or create the debit transaction and Payment
    |--------------------------------------------------------------------------
    */

        $debit = null;
        $payment = null;

        DB::beginTransaction();

        try {
            if ($request->filled('transaction_id')) {
                $debit = Transaction::query()
                    ->where('id', $request->transaction_id)
                    ->where('user_id', $user->id)
                    ->where('payment_item_id', $paymentItem->id)
                    ->where('type', 'debit')
                    ->lockForUpdate()
                    ->first();

                if (!$debit) {
                    DB::rollBack();

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'The selected payment transaction could not be verified.',
                        ], 422);
                    }

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'The selected payment transaction could not be verified.'
                        );
                }

                if ($debit->status === 'paid') {
                    DB::rollBack();

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This payment transaction has already been completed.',
                        ], 422);
                    }

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'This payment transaction has already been completed.'
                        );
                }

                if ((float) $debit->amount !== $amount) {
                    DB::rollBack();

                    Log::critical('ADDITIONAL PAYMENT AMOUNT MISMATCH', [
                        'user_id' => $user->id,
                        'payment_item_id' => $paymentItem->id,
                        'transaction_id' => $debit->id,
                        'transaction_amount' => $debit->amount,
                        'payment_item_amount' => $amount,
                    ]);

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'The payment amount could not be verified.',
                        ], 422);
                    }

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'The payment amount could not be verified.'
                        );
                }
            } else {
                $debit = Transaction::query()
                    ->where('user_id', $user->id)
                    ->where('payment_item_id', $paymentItem->id)
                    ->where('type', 'debit')
                    ->where('status', 'not paid')
                    ->where('amount', $paymentItem->amount)
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (!$debit) {
                    $debit = Transaction::create([
                        'user_id' => $user->id,
                        'payment_item_id' => $paymentItem->id,
                        'debit_transaction_id' => null,
                        'narration' => $paymentItem->name,
                        'type' => 'debit',
                        'status' => 'not paid',
                        'amount' => $paymentItem->amount,
                        'transaction_id' => null,
                        'gateway' => null,
                    ]);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 9. Find or create the local Payment record
        |--------------------------------------------------------------------------
        */

            $payment = Payment::query()
                ->where('user_id', $user->id)
                ->where('payment_item_id', $paymentItem->id)
                ->where('status', 'pending')
                ->where('amount', $paymentItem->amount)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'payment_item_id' => $paymentItem->id,
                    'membership_category_id' => $categoryId,
                    'payment_type' => 'additional',
                    'fee_type' => 'additional',
                    'amount' => $paymentItem->amount,
                    'description' => $paymentItem->name,
                    'payment_reference' => $paystackReference,
                    'reference' => $paystackReference,
                    'document_field_values' => $documentFieldValues,
                    'gateway' => 'paystack',
                    'status' => 'pending',
                    'renewal_document_id' => $renewalDocument?->id,
                ]);
            } else {
                $payment->update([
                    'membership_category_id' => $categoryId,
                    'payment_type' => 'additional',
                    'fee_type' => 'additional',
                    'description' => $paymentItem->name,
                    'payment_reference' => $paystackReference,
                    'reference' => $paystackReference,
                    'document_field_values' => $documentFieldValues,
                    'renewal_document_id' => $renewalDocument?->id,
                    'status' => 'pending',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | 10. Store Paystack reference on the debit transaction
        |--------------------------------------------------------------------------
        */

            $debit->update([
                'gateway' => [
                    'provider' => 'paystack',
                    'reference' => $paystackReference,
                    'payment_id' => $payment->id,
                ],
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ADDITIONAL PAYMENT PREPARATION FAILED', [
                'user_id' => $user->id,
                'payment_item_id' => $paymentItem->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to prepare this payment. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to prepare this payment. Please try again.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | 11. Initialize Paystack
    |--------------------------------------------------------------------------
    */

        try {
            $paystackSecretKey = config('services.paystack.secret_key');

            if (!$paystackSecretKey) {
                throw new \RuntimeException(
                    'Paystack secret key is not configured.'
                );
            }

            $callbackUrl = route('payment.callback');

            $response = Http::withToken($paystackSecretKey)
                ->acceptJson()
                ->post(
                    'https://api.paystack.co/transaction/initialize',
                    [
                        'email' => $paymentEmail,
                        'amount' => (int) round($amount * 100),
                        'reference' => $paystackReference,
                        'callback_url' => $callbackUrl,
                        'metadata' => [
                            'payment_id' => $payment->id,
                            'transaction_id' => $debit->id,
                            'payment_item_id' => $paymentItem->id,
                            'payment_option' => 'additional',
                            'user_id' => $user->id,
                            'membership_category_id' => $categoryId,
                            'renewal_document_id' => $renewalDocument?->id,
                        ],
                    ]
                );

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'Paystack initialization failed: ' .
                        $response->body()
                );
            }

            $responseData = $response->json();

            if (
                !isset($responseData['status']) ||
                $responseData['status'] !== true ||
                empty($responseData['data']['authorization_url'])
            ) {
                throw new \RuntimeException(
                    'Paystack did not return a valid authorization URL.'
                );
            }

            $payment->update([
                'paystack_reference' => $paystackReference,
                'paystack_authorization_url' =>
                $responseData['data']['authorization_url'],
                'gateway_status' => 'initialized',
                'gateway_response' => $responseData,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment initialized successfully.',
                    'authorization_url' =>
                    $responseData['data']['authorization_url'],
                    'reference' => $paystackReference,
                ]);
            }

            return redirect()->away(
                $responseData['data']['authorization_url']
            );
        } catch (\Throwable $e) {
            Log::error(
                'ADDITIONAL PAYMENT PAYSTACK INITIALIZATION FAILED',
                [
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'payment_item_id' => $paymentItem->id,
                    'transaction_id' => $debit->id,
                    'error' => $e->getMessage(),
                ]
            );

            $payment->update([
                'status' => 'failed',
                'gateway_status' => 'initialization_failed',
                'gateway_response' => [
                    'error' => $e->getMessage(),
                ],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Unable to initialize payment with Paystack. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to initialize payment with Paystack. Please try again.'
                );
        }
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
    protected function getAndVerifyPaymentItem(
        int $paymentItemId,
        int $categoryId,
        int $userId,
        ?int $renewalDocumentId
    ): ?PaymentItem {
        $paymentItem = PaymentItem::query()
            ->where(
                'id',
                $paymentItemId
            )
            ->where(
                'is_active',
                true
            )
            ->whereNotIn(
                'type',
                ['membership']
            )
            ->whereHas(
                'membershipCategories',
                function ($query) use ($categoryId) {
                    $query->where(
                        'membership_categories.id',
                        $categoryId
                    )
                        ->where(
                            'membership_categories.status',
                            true
                        );
                }
            )
            ->with([
                'membershipCategories',
                'documents' => function ($query) {
                    $query
                        ->where(
                            'documents.is_active',
                            true
                        )
                        ->wherePivot(
                            'generate_after_payment',
                            true
                        )
                        ->with([
                            'fields' => function ($query) {
                                $query
                                    ->orderBy('sort_order')
                                    ->orderBy('id');
                            },
                        ]);
                },
            ])
            ->first();

        if (!$paymentItem) {
            Log::warning(
                'INVALID ADDITIONAL PAYMENT ITEM',
                [
                    'user_id' =>
                    $userId,

                    'user_membership_category_id' =>
                    $categoryId,

                    'payment_item_id' =>
                    $paymentItemId,

                    'renewal_document_id' =>
                    $renewalDocumentId,
                ]
            );

            return null;
        }

        /*
    |--------------------------------------------------------------------------
    | REMOVE DOCUMENTS THAT ARE NOT ACTIVE / GENERATION ENABLED
    |--------------------------------------------------------------------------
    */

        $paymentItem->setRelation(
            'documents',
            $paymentItem->documents
                ->filter(
                    function ($document) {
                        return $document->is_active &&
                            (bool) $document->pivot->generate_after_payment;
                    }
                )
                ->values()
        );

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
    protected function logDocumentFieldInfo(
        int $userId,
        PaymentItem $paymentItem,
        ?int $renewalDocumentId,
        array $submittedValues
    ): void {
        Log::info(
            'ADDITIONAL PAYMENT DOCUMENT FIELD VALUES RECEIVED',
            [
                'user_id' =>
                $userId,

                'payment_item_id' =>
                $paymentItem->id,

                'renewal_document_id' =>
                $renewalDocumentId,

                'documents' =>
                $paymentItem->documents
                    ->map(
                        function ($document) use (
                            $submittedValues
                        ) {
                            return [
                                'document_id' =>
                                $document->id,

                                'document_code' =>
                                $document->code,

                                'document_name' =>
                                $document->name,

                                'submitted_values' =>
                                $submittedValues[$document->code] ?? [],

                                'manual_fields' =>
                                $document->fields
                                    ->where(
                                        'is_system',
                                        false
                                    )
                                    ->map(
                                        function ($field) {
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
                                        }
                                    )
                                    ->values()
                                    ->all(),
                            ];
                        }
                    )
                    ->values()
                    ->all(),

                'submitted_document_field_values' =>
                $submittedValues,
            ]
        );
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
        /*
    |--------------------------------------------------------------------------
    | NO DOCUMENTS
    |--------------------------------------------------------------------------
    */

        if ($paymentItem->documents->isEmpty()) {
            if (!empty($submittedValues)) {
                Log::warning(
                    'DOCUMENT FIELDS SUBMITTED FOR PAYMENT ITEM WITHOUT DOCUMENTS',
                    [
                        'user_id' =>
                        $userId,

                        'payment_item_id' =>
                        $paymentItem->id,

                        'renewal_document_id' =>
                        $renewalDocumentId,

                        'submitted_values' =>
                        $submittedValues,
                    ]
                );

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'This payment does not accept document fields.'
                    );
            }

            return [];
        }

        /*
    |--------------------------------------------------------------------------
    | NORMALIZE DOCUMENT CODES
    |--------------------------------------------------------------------------
    */

        $documentsByCode = [];

        foreach ($paymentItem->documents as $document) {
            $documentsByCode[$document->code] = $document;
        }

        /*
    |--------------------------------------------------------------------------
    | VERIFY DOCUMENT KEYS
    |--------------------------------------------------------------------------
    |
    | Every submitted document code must belong to this payment item.
    |
    */

        foreach (
            array_keys($submittedValues)
            as $submittedDocumentCode
        ) {
            if (
                !isset(
                    $documentsByCode[$submittedDocumentCode]
                )
            ) {
                Log::warning(
                    'UNAUTHORIZED DOCUMENT SUBMITTED',
                    [
                        'user_id' =>
                        $userId,

                        'payment_item_id' =>
                        $paymentItem->id,

                        'document_code' =>
                        $submittedDocumentCode,

                        'renewal_document_id' =>
                        $renewalDocumentId,
                    ]
                );

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'An invalid document was submitted.'
                    );
            }
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDATE EACH DOCUMENT
    |--------------------------------------------------------------------------
    */

        $validatedDocuments = [];

        foreach (
            $paymentItem->documents as $document
        ) {
            $documentCode =
                $document->code;

            $documentSubmittedValues =
                $submittedValues[$documentCode] ?? [];

            if (
                !is_array(
                    $documentSubmittedValues
                )
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "Invalid field data supplied for {$document->name}."
                    );
            }

            /*
        |--------------------------------------------------------------------------
        | MANUAL FIELDS
        |--------------------------------------------------------------------------
        */

            $manualFields =
                $document->fields
                ->where(
                    'is_system',
                    false
                )
                ->values();

            /*
        |--------------------------------------------------------------------------
        | SYSTEM-ONLY DOCUMENT
        |--------------------------------------------------------------------------
        */

            if ($manualFields->isEmpty()) {
                if (
                    !empty($documentSubmittedValues)
                ) {
                    Log::warning(
                        'SYSTEM DOCUMENT FIELDS SUBMITTED BY MEMBER',
                        [
                            'user_id' =>
                            $userId,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'document_id' =>
                            $document->id,

                            'document_code' =>
                            $documentCode,

                            'submitted_keys' =>
                            array_keys(
                                $documentSubmittedValues
                            ),
                        ]
                    );

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "The {$document->name} document does not accept manually supplied fields."
                        );
                }

                $validatedDocuments[$documentCode] = [];

                continue;
            }

            /*
        |--------------------------------------------------------------------------
        | ALLOWED FIELD KEYS
        |--------------------------------------------------------------------------
        */

            $allowedFieldKeys =
                $manualFields
                ->pluck('field_key')
                ->values()
                ->all();

            /*
        |--------------------------------------------------------------------------
        | VERIFY SUBMITTED FIELD KEYS
        |--------------------------------------------------------------------------
        */

            foreach (
                array_keys(
                    $documentSubmittedValues
                ) as $submittedFieldKey
            ) {
                if (
                    !in_array(
                        $submittedFieldKey,
                        $allowedFieldKeys,
                        true
                    )
                ) {
                    Log::warning(
                        'UNAUTHORIZED DOCUMENT FIELD SUBMITTED',
                        [
                            'user_id' =>
                            $userId,

                            'payment_item_id' =>
                            $paymentItem->id,

                            'document_id' =>
                            $document->id,

                            'document_code' =>
                            $documentCode,

                            'field_key' =>
                            $submittedFieldKey,
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
        | VALIDATE FIELDS
        |--------------------------------------------------------------------------
        */

            $validatedFieldValues = [];

            foreach (
                $manualFields as $field
            ) {
                $fieldKey =
                    $field->field_key;

                $value =
                    $documentSubmittedValues[$fieldKey] ?? null;

                if (
                    is_string($value) &&
                    trim($value) === ''
                ) {
                    $value = null;
                }

                /*
            |--------------------------------------------------------------------------
            | REQUIRED
            |--------------------------------------------------------------------------
            */

                if (
                    $field->is_required &&
                    ($value === null || $value === '')
                ) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "{$field->label} is required for {$document->name}."
                        );
                }

                if ($value === null) {
                    continue;
                }

                /*
            |--------------------------------------------------------------------------
            | TYPE VALIDATION
            |--------------------------------------------------------------------------
            */

                $validationError =
                    $this->validateSingleFieldValue(
                        $field,
                        $value
                    );

                if ($validationError) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "{$document->name}: {$validationError}"
                        );
                }

                $validatedFieldValues[$fieldKey] = $value;
            }

            $validatedDocuments[$documentCode] = $validatedFieldValues;
        }

        /*
    |--------------------------------------------------------------------------
    | RETURN DOCUMENT-CODE-KEYED DATA
    |--------------------------------------------------------------------------
    */

        return $validatedDocuments;
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


    /**
     * Validate required system fields that must exist before payment.
     */
    protected function validateRequiredSystemDocumentFields(
        PaymentItem $paymentItem,
        int $userId
    ): ?RedirectResponse {
        $documents = $paymentItem->documents()
            ->where('documents.is_active', true)
            ->wherePivot('generate_after_payment', true)
            ->with([
                'fields' => function ($query) {
                    $query->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->get();

        $membership = Membership::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (!$membership) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'You must have an active membership before making this payment.'
                );
        }

        foreach ($documents as $document) {
            $requiredSystemFields = $document->fields
                ->where('is_system', true)
                ->where('is_required', true);

            foreach ($requiredSystemFields as $field) {
                $fieldKey = strtolower(trim($field->field_key));

                switch ($fieldKey) {
                    case 'seller_membership_no':
                    case 'membership_no':
                    case 'membership_number':
                        if (!$membership->membership_number) {
                            return back()
                                ->withInput()
                                ->with(
                                    'error',
                                    'Your membership number is not available. Please contact the administrator.'
                                );
                        }

                        break;

                    case 'lifting_right_no':
                        $liftingRight = OperationalRightsDocument::query()
                            ->where('user_id', $userId)
                            ->where('membership_id', $membership->id)
                            ->where('status', 'active')
                            ->whereIn('document_type', [
                                'charcoal_lifting',
                                'charcoal_lifting_rcg',
                            ])
                            ->whereDate(
                                'expires_at',
                                '>=',
                                now()->toDateString()
                            )
                            ->latest('id')
                            ->first();

                        if (!$liftingRight) {
                            return back()
                                ->withInput()
                                ->with(
                                    'error',
                                    'You need an active Charcoal Lifting Right before paying for this afforestation compliance receipt.'
                                );
                        }

                        break;

                    case 'seller_member_name':
                    case 'seller_name':
                    case 'member_name':
                        $profile = MemberProfile::query()
                            ->where('user_id', $userId)
                            ->first();

                        $fullName = trim(
                            (string) (
                                $profile?->full_name
                                ?? $profile?->name
                                ?? $membership->user?->name
                            )
                        );

                        if (!$fullName) {
                            return back()
                                ->withInput()
                                ->with(
                                    'error',
                                    'Your member name is not available. Please update your profile.'
                                );
                        }

                        break;

                    case 'seller_phone':
                    case 'phone':
                    case 'member_phone':
                        $profile = MemberProfile::query()
                            ->where('user_id', $userId)
                            ->first();

                        $phone = $profile?->phone
                            ?? $membership->user?->phone;

                        if (!$phone) {
                            return back()
                                ->withInput()
                                ->with(
                                    'error',
                                    'Your phone number is required. Please update your profile.'
                                );
                        }

                        break;

                    /*
                 * These values are generated after successful payment.
                 * They do not need to exist before Paystack initialization.
                 */
                    case 'receipt_no':
                    case 'receipt_number':
                    case 'document_number':
                    case 'transit_no':
                    case 'tracking_code':
                    case 'authentication_code':
                    case 'verification_code':
                    case 'verification_url':
                    case 'amount_paid':
                    case 'amount_in_figure':
                    case 'payment_date':
                    case 'payment_time':
                    case 'document_id':
                    case 'document_code':
                    case 'document_name':
                        break;
                }
            }
        }

        return null;
    }



    public function getAdditionalPaymentFields(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'payment_item_id' => ['required', 'integer'],
            ]);

            $categoryId = $user->membership_category_id;

            if (!$categoryId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your membership category could not be determined.',
                ], 422);
            }

            $paymentItem = PaymentItem::query()
                ->where('id', $request->payment_item_id)
                ->where('is_active', true)
                ->whereNotIn('type', ['membership'])
                ->whereHas('membershipCategories', function ($query) use ($categoryId) {
                    $query->where('membership_categories.id', $categoryId)
                        ->where('membership_categories.status', true);
                })
                ->with([
                    'membershipCategories',
                    'documents' => function ($query) {
                        $query->where('documents.is_active', true)
                            ->wherePivot('generate_after_payment', true)
                            ->with([
                                'fields' => function ($query) {
                                    $query->orderBy('sort_order')
                                        ->orderBy('id');
                                },
                            ]);
                    },
                ])
                ->first();

            if (!$paymentItem) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The selected payment item is not available for your membership category.',
                ], 404);
            }

            $documents = $paymentItem->documents
                ->sortBy(function ($document) {
                    return [
                        $document->pivot->is_primary ? 0 : 1,
                        $document->id,
                    ];
                })
                ->values();

            if ($documents->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'has_document' => false,
                    'document' => null,
                    'documents' => [],
                    'fields' => [],
                    'seller' => null,
                ]);
            }

            $documentsResponse = $documents->map(function ($document) {

                $manualFields = $document->fields
                    ->filter(function ($field) {
                        return !$field->is_system;
                    })
                    ->values()
                    ->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'field_key' => $field->field_key,
                            'label' => $field->label,
                            'field_type' => $field->field_type,
                            'section' => $field->section,
                            'placeholder' => $field->placeholder,
                            'default_value' => $field->default_value,
                            'options' => $field->options,
                            'is_required' => (bool) $field->is_required,
                            'is_system' => (bool) $field->is_system,
                            'sort_order' => $field->sort_order,
                        ];
                    })
                    ->values();

                return [
                    'id' => $document->id,
                    'name' => $document->name,
                    'code' => $document->code,
                    'description' => $document->description,
                    'type' => $document->type,
                    'requires_form' => (bool) $document->requires_form,
                    'is_primary' => (bool) $document->pivot->is_primary,
                    'generate_after_payment' => (bool) $document->pivot->generate_after_payment,
                    'fields' => $manualFields,
                ];
            })->values();

            $profile = MemberProfile::where('user_id', $user->id)
                ->latest('id')
                ->first();

            $membership = Membership::where('user_id', $user->id)
                ->where('membership_category_id', $categoryId)
                ->latest('id')
                ->first();

            $sellerName = null;

            if ($profile) {
                $sellerName = trim(
                    collect([
                        $profile->first_name ?? null,
                        $profile->middle_name ?? null,
                        $profile->last_name ?? null,
                    ])->filter()->implode(' ')
                );
            }

            if (!$sellerName) {
                $sellerName = $user->name;
            }

            $sellerMembershipNumber = $membership?->membership_number;

            $sellerPhone =
                $profile?->phone ??
                $user->phone ??
                null;

            $sellerDealingRightNo = null;

            if ($membership) {
                $operationalRight = OperationalRightsDocument::where('user_id', $user->id)
                    ->where('membership_id', $membership->id)
                    ->where('status', 'active')
                    ->whereIn('document_type', [
                        'charcoal_dealing_supplier',
                        'charcoal_dealing_dealer',
                    ])
                    ->whereDate('expires_at', '>=', now()->toDateString())
                    ->latest('id')
                    ->first();

                $sellerDealingRightNo =
                    $operationalRight?->document_number;
            }

            return response()->json([
                'status' => 'success',

                'has_document' => true,

                /*
            |--------------------------------------------------------------------------
            | Backward-compatible single document response
            |--------------------------------------------------------------------------
            |
            | Existing JavaScript may still read:
            |
            | data.document
            | data.fields
            |
            | We keep those fields pointing to the primary document.
            |--------------------------------------------------------------------------
            */

                'document' => $documentsResponse->first(),

                'fields' => $documentsResponse->first()['fields'] ?? [],

                /*
            |--------------------------------------------------------------------------
            | New multi-document response
            |--------------------------------------------------------------------------
            */

                'documents' => $documentsResponse,

                /*
            |--------------------------------------------------------------------------
            | Seller/member information
            |--------------------------------------------------------------------------
            */

                'seller' => [
                    'name' => $sellerName ?: 'N/A',
                    'membership_number' => $sellerMembershipNumber ?: 'N/A',
                    'dealing_right_number' => $sellerDealingRightNo ?: 'N/A',
                    'phone' => $sellerPhone ?: 'N/A',
                ],

                'payment_item' => [
                    'id' => $paymentItem->id,
                    'name' => $paymentItem->name,
                    'code' => $paymentItem->code,
                    'amount' => $paymentItem->amount,
                ],
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Unable to load additional payment document fields.',
                [
                    'user_id' => Auth::id(),
                    'payment_item_id' => $request->payment_item_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to load document information.',
            ], 500);
        }
    }
}
