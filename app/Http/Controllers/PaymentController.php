<?php

namespace App\Http\Controllers;

use App\Models\MembershipCategory;
use App\Models\Payment;
use App\Models\MembershipCategoryFee;
use App\Models\MemberFee;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\OperationalRightsDocumentService;

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

        /*
        |--------------------------------------------------------------------------
        | GET MEMBER PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        /*
        |--------------------------------------------------------------------------
        | CHECK APPROVAL
        |--------------------------------------------------------------------------
        */

        $isApproved =
            $profile &&
            $profile->status === 'approved';

        /*
        |--------------------------------------------------------------------------
        | GET MEMBER TYPE
        |--------------------------------------------------------------------------
        */

        $memberType =
            $profile->member_type
            ?? $user->member_type
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | CHECK ACTIVE ANNUAL MEMBERSHIP
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Only a PAID membership payment makes annual membership active.
        |
        | member_fee payments such as:
        |
        | - ID Card
        | - Certificate
        | - Penalty
        | - Other charges
        |
        | MUST NOT make annual membership active.
        |
        */

        $hasActiveAnnualMembership = Payment::where(
            'user_id',
            $user->id
        )
            ->where(
                'payment_type',
                'membership'
            )
            ->where(
                'status',
                'paid'
            )
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | GET LATEST PAID ANNUAL MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        */

        $annualMembershipPayment = Payment::where(
            'user_id',
            $user->id
        )
            ->where(
                'payment_type',
                'membership'
            )
            ->where(
                'status',
                'paid'
            )
            ->latest('paid_at')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE MEMBERSHIP CATEGORIES
        |--------------------------------------------------------------------------
        |
        | Only needed when the member has not yet selected a category.
        |
        */

        $categories = collect();

        if (
            !$profile ||
            !$profile->membership_category_id
        ) {

            $categoriesQuery =
                MembershipCategory::where(
                    'status',
                    true
                );

            /*
            |--------------------------------------------------------------------------
            | ENFORCE MEMBER TYPE
            |--------------------------------------------------------------------------
            */

            if ($memberType) {

                $categoriesQuery->where(
                    'member_type',
                    $memberType
                );
            } else {

                $categoriesQuery->whereRaw(
                    '1 = 0'
                );
            }

            $categories =
                $categoriesQuery
                ->with([
                    'fees' => function ($query) {

                        $query->where(
                            'status',
                            true
                        );
                    },
                ])
                ->orderBy('name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENT MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

        $currentCategory = null;

        if (
            $profile &&
            $profile->membership_category_id
        ) {

            $currentCategory =
                MembershipCategory::where(
                    'id',
                    $profile->membership_category_id
                )
                ->where(
                    'status',
                    true
                )
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER FEES
        |--------------------------------------------------------------------------
        |
        | Additional fees are only shown after approval.
        |
        */

        $memberFees = collect();

        if ($isApproved) {

            $memberFees =
                MemberFee::where(
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
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENT MEMBERSHIP FEE
        |--------------------------------------------------------------------------
        */

        $membershipFee = null;

        if ($currentCategory) {

            $membershipFee =
                MembershipCategoryFee::where(
                    'membership_category_id',
                    $currentCategory->id
                )
                ->where(
                    'status',
                    true
                )
                ->whereIn(
                    'fee_type',
                    [
                        'existing',
                        'standard',
                    ]
                )
                ->orderByRaw(
                    "CASE
                            WHEN fee_type = 'existing' THEN 1
                            WHEN fee_type = 'standard' THEN 2
                            ELSE 3
                        END"
                )
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN PAYMENT PAGE
        |--------------------------------------------------------------------------
        */

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
                'hasActiveAnnualMembership',
                'annualMembershipPayment'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function initialize(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'payment_option' => [
                'required',
                'string',
            ],

            'membership_category_id' => [
                'nullable',
                'integer',
                'exists:membership_categories,id',
            ],

        ]);

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | GET PROFILE
        |--------------------------------------------------------------------------
        */

        $profile =
            MemberProfile::where(
                'user_id',
                $user->id
            )->first();

        /*
        |--------------------------------------------------------------------------
        | APPROVAL
        |--------------------------------------------------------------------------
        */

        $isApproved =
            $profile &&
            $profile->status === 'approved';

        /*
        |--------------------------------------------------------------------------
        | MEMBER TYPE
        |--------------------------------------------------------------------------
        */

        $memberType =
            $profile->member_type
            ?? $user->member_type
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | VARIABLES
        |--------------------------------------------------------------------------
        */

        $paymentType = null;

        $feeType = null;

        $amount = null;

        $membershipCategory = null;

        $membershipCategoryFee = null;

        $memberFee = null;

        $paymentOption =
            $request->payment_option;


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        */

        if (
            $paymentOption === 'membership'
        ) {

            $paymentType =
                'membership';

            /*
            |--------------------------------------------------------------------------
            | DETERMINE CATEGORY
            |--------------------------------------------------------------------------
            */

            if (
                $profile &&
                $profile->membership_category_id
            ) {

                $categoryId =
                    $profile->membership_category_id;
            } else {

                if (
                    !$request->membership_category_id
                ) {

                    return back()
                        ->withErrors([
                            'membership_category_id' =>
                            'Please select your membership category.',
                        ])
                        ->withInput();
                }

                $categoryId =
                    $request->membership_category_id;
            }

            /*
            |--------------------------------------------------------------------------
            | GET CATEGORY
            |--------------------------------------------------------------------------
            */

            $membershipCategory =
                MembershipCategory::where(
                    'id',
                    $categoryId
                )
                ->where(
                    'status',
                    true
                )
                ->first();

            if (!$membershipCategory) {

                return back()
                    ->withErrors([
                        'membership_category_id' =>
                        'The selected membership category is not available.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | SERVER-SIDE MEMBER TYPE CHECK
            |--------------------------------------------------------------------------
            */

            if (
                !$memberType ||
                strtolower(
                    $membershipCategory->member_type
                ) !== strtolower($memberType)
            ) {

                return back()
                    ->withErrors([
                        'membership_category_id' =>
                        'The selected membership category is not valid for your member type.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | DETERMINE FEE TYPE
            |--------------------------------------------------------------------------
            */

            if (
                !$profile ||
                !$profile->membership_category_id
            ) {

                $feeType =
                    'new';
            } else {

                $feeType =
                    'existing';
            }

            /*
            |--------------------------------------------------------------------------
            | FIND FEE
            |--------------------------------------------------------------------------
            */

            $membershipCategoryFee =
                MembershipCategoryFee::where(
                    'membership_category_id',
                    $membershipCategory->id
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

            /*
            |--------------------------------------------------------------------------
            | FALLBACK TO STANDARD FOR NEW MEMBER
            |--------------------------------------------------------------------------
            */

            if (
                !$membershipCategoryFee &&
                $feeType === 'new'
            ) {

                $membershipCategoryFee =
                    MembershipCategoryFee::where(
                        'membership_category_id',
                        $membershipCategory->id
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

                if ($membershipCategoryFee) {

                    $feeType =
                        'standard';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK TO STANDARD FOR EXISTING MEMBER
            |--------------------------------------------------------------------------
            */

            if (
                !$membershipCategoryFee &&
                $feeType === 'existing'
            ) {

                $membershipCategoryFee =
                    MembershipCategoryFee::where(
                        'membership_category_id',
                        $membershipCategory->id
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

                if ($membershipCategoryFee) {

                    $feeType =
                        'standard';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | FEE NOT FOUND
            |--------------------------------------------------------------------------
            */

            if (!$membershipCategoryFee) {

                return back()
                    ->withErrors([
                        'membership_category_id' =>
                        'The membership fee for the selected category is not available.',
                    ])
                    ->withInput();
            }

            /*
            |--------------------------------------------------------------------------
            | AMOUNT
            |--------------------------------------------------------------------------
            */

            $amount =
                $membershipCategoryFee->amount;
        }


        /*
        |--------------------------------------------------------------------------
        | ADDITIONAL MEMBER FEE
        |--------------------------------------------------------------------------
        */ elseif (
            Str::startsWith(
                $paymentOption,
                'member_fee_'
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | ONLY APPROVED MEMBERS
            |--------------------------------------------------------------------------
            */

            if (!$isApproved) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'Additional fees are only available after your membership application has been approved.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | MEMBER MUST HAVE CATEGORY
            |--------------------------------------------------------------------------
            */

            if (
                !$profile ||
                !$profile->membership_category_id
            ) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'Your membership category could not be found. Please contact the administrator.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GET EXISTING MEMBER CATEGORY
            |--------------------------------------------------------------------------
            */

            $membershipCategory =
                MembershipCategory::where(
                    'id',
                    $profile->membership_category_id
                )
                ->where(
                    'status',
                    true
                )
                ->first();

            if (!$membershipCategory) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'Your membership category is no longer available. Please contact the administrator.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | MEMBER FEE ID
            |--------------------------------------------------------------------------
            */

            $memberFeeId =
                (int) Str::after(
                    $paymentOption,
                    'member_fee_'
                );

            if (!$memberFeeId) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'Invalid payment option.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GET EXACT MEMBER FEE
            |--------------------------------------------------------------------------
            */

            $memberFee =
                MemberFee::where(
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

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'This fee is no longer available for payment.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DUE DATE
            |--------------------------------------------------------------------------
            */

            if (
                $memberFee->due_date &&
                $memberFee->due_date <
                now()->toDateString()
            ) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'This fee has passed its due date.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | GET CATEGORY FEE
            |--------------------------------------------------------------------------
            |
            | payments.membership_category_fee_id is required in your
            | current database structure.
            |
            */

            $membershipCategoryFee =
                MembershipCategoryFee::where(
                    'membership_category_id',
                    $membershipCategory->id
                )
                ->where(
                    'status',
                    true
                )
                ->whereIn(
                    'fee_type',
                    [
                        'existing',
                        'standard',
                    ]
                )
                ->orderByRaw(
                    "CASE
                            WHEN fee_type = 'existing' THEN 1
                            WHEN fee_type = 'standard' THEN 2
                            ELSE 3
                        END"
                )
                ->first();

            /*
            |--------------------------------------------------------------------------
            | CATEGORY FEE MUST EXIST
            |--------------------------------------------------------------------------
            */

            if (!$membershipCategoryFee) {

                return back()
                    ->withErrors([
                        'payment_option' =>
                        'Your membership category fee could not be found. Please contact the administrator.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | PAYMENT DETAILS
            |--------------------------------------------------------------------------
            */

            $paymentType =
                'member_fee';

            $feeType =
                $memberFee->fee_type;

            $amount =
                $memberFee->amount;
        }


        /*
        |--------------------------------------------------------------------------
        | INVALID PAYMENT OPTION
        |--------------------------------------------------------------------------
        */ else {

            return back()
                ->withErrors([
                    'payment_option' =>
                    'Invalid payment option selected.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE AMOUNT
        |--------------------------------------------------------------------------
        */

        if (
            $amount === null ||
            (float) $amount <= 0
        ) {

            return back()
                ->withErrors([
                    'payment_option' =>
                    'The selected payment amount is invalid.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | DETERMINE PAYMENT CATEGORY
        |--------------------------------------------------------------------------
        */

        $paymentMembershipCategoryId =
            $membershipCategory
            ? $membershipCategory->id
            : null;

        /*
        |--------------------------------------------------------------------------
        | FINAL CATEGORY CHECK
        |--------------------------------------------------------------------------
        */

        if (!$paymentMembershipCategoryId) {

            return back()
                ->withErrors([
                    'payment' =>
                    'Your membership category could not be determined. Please contact the administrator.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | FINAL CATEGORY FEE CHECK
        |--------------------------------------------------------------------------
        */

        if (!$membershipCategoryFee) {

            return back()
                ->withErrors([
                    'payment' =>
                    'Your membership category fee could not be determined. Please contact the administrator.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | PREVENT DUPLICATE PENDING PAYMENT
        |--------------------------------------------------------------------------
        |
        | IMPORTANT FIX:
        |
        | We DO NOT simply check:
        |
        | payment_type = membership / member_fee
        |
        | because that would allow an unrelated pending payment to block
        | another payment.
        |
        | Example:
        |
        | Pending membership payment
        |          +
        | ID Card payment
        |
        | The ID Card payment MUST still be allowed.
        |
        */

        $existingPendingPaymentQuery =
            Payment::where(
                'user_id',
                $user->id
            )
            ->where(
                'status',
                'pending'
            );


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        |
        | Prevent another pending membership payment for the same
        | membership category.
        |
        */

        if (
            $paymentType === 'membership'
        ) {

            $existingPendingPaymentQuery
                ->where(
                    'payment_type',
                    'membership'
                )
                ->where(
                    'membership_category_id',
                    $paymentMembershipCategoryId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER FEE PAYMENT
        |--------------------------------------------------------------------------
        |
        | Prevent another pending payment ONLY for the same member fee.
        |
        | This means:
        |
        | Pending ID Card
        |       ↓
        | Cannot start another ID Card payment
        |
        | But:
        |
        | Pending ID Card
        |       ↓
        | Certificate payment is allowed
        |
        | Pending Membership
        |       ↓
        | ID Card payment is allowed
        |
        */ elseif (
            $paymentType === 'member_fee'
        ) {

            $existingPendingPaymentQuery
                ->where(
                    'payment_type',
                    'member_fee'
                )
                ->where(
                    'member_fee_id',
                    $memberFee->id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FIND EXISTING PENDING PAYMENT
        |--------------------------------------------------------------------------
        */

        $existingPendingPayment =
            $existingPendingPaymentQuery
            ->latest()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | BLOCK DUPLICATE
        |--------------------------------------------------------------------------
        */

        if ($existingPendingPayment) {

            return redirect(
                route('payment.index')
            )->withErrors([
                'payment' =>
                'You already have a pending payment for this payment obligation. Please complete that payment before starting another one.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | UNIQUE PAYMENT REFERENCE
        |--------------------------------------------------------------------------
        */

        $reference =
            'NACP-' .
            strtoupper(
                Str::random(20)
            );


        /*
        |--------------------------------------------------------------------------
        | CREATE PAYMENT
        |--------------------------------------------------------------------------
        */

        $payment =
            Payment::create([

                /*
                |--------------------------------------------------------------------------
                | USER
                |--------------------------------------------------------------------------
                */

                'user_id' =>
                $user->id,

                /*
                |--------------------------------------------------------------------------
                | MEMBERSHIP CATEGORY
                |--------------------------------------------------------------------------
                */

                'membership_category_id' =>
                $paymentMembershipCategoryId,

                /*
                |--------------------------------------------------------------------------
                | MEMBERSHIP CATEGORY FEE
                |--------------------------------------------------------------------------
                */

                'membership_category_fee_id' =>
                $membershipCategoryFee->id,

                /*
                |--------------------------------------------------------------------------
                | MEMBER FEE
                |--------------------------------------------------------------------------
                |
                | This is NULL for annual membership.
                |
                | For:
                |
                | ID Card
                | Certificate
                | Penalty
                |
                | it stores the exact MemberFee ID.
                |
                */

                'member_fee_id' =>
                $memberFee
                    ? $memberFee->id
                    : null,

                /*
                |--------------------------------------------------------------------------
                | PAYMENT TYPE
                |--------------------------------------------------------------------------
                */

                'payment_type' =>
                $paymentType,

                /*
                |--------------------------------------------------------------------------
                | FEE TYPE
                |--------------------------------------------------------------------------
                */

                'fee_type' =>
                $feeType,

                /*
                |--------------------------------------------------------------------------
                | AMOUNT
                |--------------------------------------------------------------------------
                */

                'amount' =>
                $amount,

                /*
                |--------------------------------------------------------------------------
                | REFERENCE
                |--------------------------------------------------------------------------
                */

                'reference' =>
                $reference,

                /*
                |--------------------------------------------------------------------------
                | GATEWAY
                |--------------------------------------------------------------------------
                */

                'gateway' =>
                'paystack',

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                'status' =>
                'pending',
            ]);


        /*
        |--------------------------------------------------------------------------
        | PAYSTACK AMOUNT
        |--------------------------------------------------------------------------
        */

        $amountInKobo =
            (int) round(
                ((float) $amount) * 100
            );


        /*
        |--------------------------------------------------------------------------
        | CALLBACK
        |--------------------------------------------------------------------------
        */

        $callbackUrl =
            route('payment.callback');


        /*
        |--------------------------------------------------------------------------
        | INITIALIZE PAYSTACK
        |--------------------------------------------------------------------------
        */

        try {

            $response =
                Http::withToken(
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
                        $amountInKobo,

                        'reference' =>
                        $reference,

                        'currency' =>
                        'NGN',

                        'callback_url' =>
                        $callbackUrl,

                        /*
                            |--------------------------------------------------------------------------
                            | PAYSTACK METADATA
                            |--------------------------------------------------------------------------
                            */

                        'metadata' => [

                            'payment_id' =>
                            $payment->id,

                            'user_id' =>
                            $user->id,

                            'payment_type' =>
                            $paymentType,

                            'fee_type' =>
                            $feeType,

                            /*
                                |--------------------------------------------------------------------------
                                | MEMBER FEE ID
                                |--------------------------------------------------------------------------
                                */

                            'member_fee_id' =>
                            $memberFee
                                ? $memberFee->id
                                : null,

                            /*
                                |--------------------------------------------------------------------------
                                | MEMBERSHIP CATEGORY
                                |--------------------------------------------------------------------------
                                */

                            'membership_category_id' =>
                            $paymentMembershipCategoryId,

                            /*
                                |--------------------------------------------------------------------------
                                | MEMBERSHIP CATEGORY FEE
                                |--------------------------------------------------------------------------
                                */

                            'membership_category_fee_id' =>
                            $membershipCategoryFee->id,
                        ],
                    ]
                );


            /*
            |--------------------------------------------------------------------------
            | PAYSTACK FAILURE
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {

                Log::error(
                    'Paystack initialization failed',
                    [
                        'payment_id' =>
                        $payment->id,

                        'reference' =>
                        $reference,

                        'response' =>
                        $response->json(),
                    ]
                );

                $payment->update([
                    'status' =>
                    'failed',
                ]);

                return back()
                    ->withErrors([
                        'payment' =>
                        'Unable to initialize payment with Paystack.',
                    ])
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | PAYSTACK RESPONSE
            |--------------------------------------------------------------------------
            */

            $paystackData =
                $response->json();


            /*
            |--------------------------------------------------------------------------
            | AUTHORIZATION URL CHECK
            |--------------------------------------------------------------------------
            */

            if (
                empty($paystackData['data']['authorization_url'])
            ) {

                Log::error(
                    'Paystack authorization URL missing',
                    [
                        'payment_id' =>
                        $payment->id,

                        'reference' =>
                        $reference,

                        'response' =>
                        $paystackData,
                    ]
                );

                $payment->update([
                    'status' =>
                    'failed',
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
            | REDIRECT TO PAYSTACK
            |--------------------------------------------------------------------------
            */

            return redirect(
                $paystackData['data']['authorization_url']
            );
        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG EXCEPTION
            |--------------------------------------------------------------------------
            */

            Log::error(
                'Paystack payment initialization exception',
                [
                    'payment_id' =>
                    $payment->id,

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

            /*
            |--------------------------------------------------------------------------
            | MARK PAYMENT FAILED
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'status' =>
                'failed',
            ]);

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
    | PAYSTACK CALLBACK
    |--------------------------------------------------------------------------
    */

    public function callback(Request $request)
    {
        Log::info(
            'PAYSTACK CALLBACK HIT',
            [
                'url' =>
                $request->fullUrl(),

                'reference' =>
                $request->query('reference'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | GET REFERENCE
        |--------------------------------------------------------------------------
        */

        $reference =
            $request->query('reference');


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
        | FIND PAYMENT
        |--------------------------------------------------------------------------
        */

        $payment =
            Payment::where(
                'reference',
                $reference
            )->first();


        if (!$payment) {

            Log::error(
                'PAYSTACK CALLBACK PAYMENT NOT FOUND',
                [
                    'reference' =>
                    $reference,
                ]
            );

            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'Payment record could not be found.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY WITH PAYSTACK
        |--------------------------------------------------------------------------
        */

        try {

            $response =
                Http::withToken(
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


            /*
            |--------------------------------------------------------------------------
            | VERIFY HTTP FAILURE
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | PAYSTACK DATA
            |--------------------------------------------------------------------------
            */

            $data =
                $response->json();


            /*
            |--------------------------------------------------------------------------
            | PAYMENT SUCCESS
            |--------------------------------------------------------------------------
            */

            if (
                ($data['status'] ?? false) === true &&
                ($data['data']['status'] ?? null) === 'success'
            ) {

                /*
                |--------------------------------------------------------------------------
                | VERIFY AMOUNT
                |--------------------------------------------------------------------------
                */

                $paidAmount =
                    (int) (
                        $data['data']['amount'] ?? 0
                    );

                $expectedAmount =
                    (int) round(
                        ((float) $payment->amount) * 100
                    );


                /*
                |--------------------------------------------------------------------------
                | AMOUNT MISMATCH
                |--------------------------------------------------------------------------
                */

                if (
                    $paidAmount !==
                    $expectedAmount
                ) {

                    Log::critical(
                        'PAYSTACK AMOUNT MISMATCH',
                        [
                            'payment_id' =>
                            $payment->id,

                            'reference' =>
                            $reference,

                            'expected' =>
                            $expectedAmount,

                            'received' =>
                            $paidAmount,
                        ]
                    );

                    $payment->update([

                        'status' =>
                        'failed',

                        'gateway_status' =>
                        'amount_mismatch',

                        'gateway_response' =>
                        $data,
                    ]);

                    return redirect()
                        ->route('payment.index')
                        ->withErrors([
                            'payment' =>
                            'The payment amount could not be verified.',
                        ]);
                }


                /*
                |--------------------------------------------------------------------------
                | MARK PAYMENT PAID
                |--------------------------------------------------------------------------
                */

                $payment->update([

                    'status' =>
                    'paid',

                    'gateway_transaction_id' =>
                    $data['data']['id'] ?? null,

                    'gateway_status' =>
                    $data['data']['status'] ?? null,

                    'gateway_response' =>
                    $data,

                    'paid_at' =>
                    now(),

                    'verified_at' =>
                    now(),
                ]);


                /*
                |--------------------------------------------------------------------------
                | MARK MEMBER FEE PAID
                |--------------------------------------------------------------------------
                |
                | This applies only to:
                |
                | payment_type = member_fee
                |
                */

                if (
                    $payment->payment_type ===
                    'member_fee'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | FIRST: GET MEMBER FEE FROM PAYMENT RECORD
                    |--------------------------------------------------------------------------
                    |
                    | This is now the safest method because member_fee_id
                    | is stored directly in the payments table.
                    |
                    */

                    $memberFeeId =
                        $payment->member_fee_id
                        ?? null;


                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK TO PAYSTACK METADATA
                    |--------------------------------------------------------------------------
                    */

                    if (!$memberFeeId) {

                        $memberFeeId =
                            $this->getMemberFeeIdFromMetadata(
                                $data
                            );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE MEMBER FEE
                    |--------------------------------------------------------------------------
                    */

                    if ($memberFeeId) {

                        $memberFee =
                            MemberFee::where(
                                'id',
                                $memberFeeId
                            )
                            ->where(
                                'user_id',
                                $payment->user_id
                            )
                            ->first();


                        if ($memberFee) {

                            $memberFee->update([

                                'status' =>
                                'paid',

                                'paid_at' =>
                                now(),

                                'reference' =>
                                $reference,
                            ]);
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT SUCCESS
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | Payment success does NOT automatically approve
                | membership.
                |
                | Membership approval remains an admin process.
                |
                */

                return redirect()
                    ->route('payment.success');
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT FAILED
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'status' =>
                'failed',

                'gateway_status' =>
                $data['data']['status'] ?? null,

                'gateway_response' =>
                $data,
            ]);


            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'Payment was not successful.',
                ]);
        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG CALLBACK EXCEPTION
            |--------------------------------------------------------------------------
            */

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
    | GET MEMBER FEE ID FROM PAYSTACK METADATA
    |--------------------------------------------------------------------------
    */

    private function getMemberFeeIdFromMetadata(
        array $data
    ) {
        return
            $data['data']['metadata']['member_fee_id']
            ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT SUCCESS PAGE
    |--------------------------------------------------------------------------
    */

    public function success()
    {
        $payment =
            Payment::where(
                'user_id',
                Auth::id()
            )
            ->where(
                'status',
                'paid'
            )
            ->latest()
            ->first();


        if (!$payment) {

            return redirect()
                ->route('payment.index')
                ->withErrors([
                    'payment' =>
                    'No successful payment was found.',
                ]);
        }


        return view(
            'member.payment.success',
            compact('payment')
        );
    }



    public function additionalPayments()
    {
        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | ONLY APPROVED MEMBERS
    |--------------------------------------------------------------------------
    */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();

        if (
            !$profile ||
            $profile->status !== 'approved'
        ) {

            return redirect()
                ->route('member.index')
                ->with(
                    'error',
                    'Additional payments are only available to approved members.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | GET MEMBER CATEGORY
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


        /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEMS
    |--------------------------------------------------------------------------
    |
    | Items with NULL membership_category_id are available
    | to all approved members.
    |
    | Category-specific items will only appear for the
    | matching member category.
    |
    */

        $paymentItems = PaymentItem::where(
            'is_active',
            true
        )
            ->where(function ($query) use ($membership) {

                $query->whereNull(
                    'membership_category_id'
                );

                if ($membership) {

                    $query->orWhere(
                        'membership_category_id',
                        $membership->membership_category_id
                    );
                }
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | RECENT PAYMENTS
    |--------------------------------------------------------------------------
    */

        $recentPayments = Payment::with(
            'paymentItem'
        )
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'payment_type',
                '!=',
                'membership'
            )
            ->latest()
            ->limit(10)
            ->get();


        return view(
            'member.payment.additional',
            compact(
                'paymentItems',
                'recentPayments'
            )
        );
    }


    public function initializeAdditionalPayment(
        Request $request
    ) {

        $request->validate([

            'payment_item_id' => [
                'required',
                'integer',
                'exists:payment_items,id',
            ],

        ]);


        $user = Auth::user();


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
            $profile->status !== 'approved'
        ) {

            return back()->with(
                'error',
                'Only approved members can make this payment.'
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

            return back()->with(
                'error',
                'Active membership could not be found.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

        $paymentItem = PaymentItem::where(
            'id',
            $request->payment_item_id
        )
            ->where(
                'is_active',
                true
            )
            ->first();


        if (!$paymentItem) {

            return back()->with(
                'error',
                'The selected payment item is no longer available.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | CATEGORY CHECK
    |--------------------------------------------------------------------------
    */

        if (
            $paymentItem->membership_category_id !== null
            &&
            $paymentItem->membership_category_id
            != $membership->membership_category_id
        ) {

            return back()->with(
                'error',
                'This payment option is not available for your membership category.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDATE AMOUNT
    |--------------------------------------------------------------------------
    |
    | The amount comes from the database.
    |
    | Never trust the amount sent from the browser.
    |
    */

        $amount = (float) $paymentItem->amount;


        if ($amount <= 0) {

            return back()->with(
                'error',
                'The selected payment item does not have a valid fee configured yet.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | PAYSTACK REFERENCE
    |--------------------------------------------------------------------------
    */

        $reference =
            'NACP-' .
            strtoupper(
                Str::random(16)
            );


        /*
    |--------------------------------------------------------------------------
    | CREATE PENDING PAYMENT
    |--------------------------------------------------------------------------
    */

        $payment = Payment::create([

            'user_id' =>
            $user->id,

            'payment_item_id' =>
            $paymentItem->id,

            'membership_category_id' =>
            $membership->membership_category_id,

            'payment_type' =>
            $paymentItem->type,

            'fee_type' =>
            $paymentItem->type,

            'description' =>
            $paymentItem->name,

            'amount' =>
            $amount,

            'reference' =>
            $reference,

            'payment_reference' =>
            $reference,

            'paystack_reference' =>
            $reference,

            'gateway' =>
            'paystack',

            'status' =>
            'pending',

        ]);

        /*
    |--------------------------------------------------------------------------
    | PAYSTACK
    |--------------------------------------------------------------------------
    |
    | Paystack expects the amount in kobo.
    |
    */

        $response = Http::withToken(
            config('services.paystack.secret_key')
        )
            ->acceptJson()
            ->post(
                'https://api.paystack.co/transaction/initialize',
                [

                    'email' =>
                    $user->email,

                    'amount' =>
                    (int) round(
                        $amount * 100
                    ),

                    'reference' =>
                    $reference,

                    'callback_url' =>
                    route(
                        'payment.additional.callback'
                    ),

                    'metadata' => [

                        'payment_id' =>
                        $payment->id,

                        'payment_item_id' =>
                        $paymentItem->id,

                        'user_id' =>
                        $user->id,

                        'payment_type' =>
                        $paymentItem->type,

                    ],

                ]
            );


        /*
    |--------------------------------------------------------------------------
    | PAYSTACK FAILED
    |--------------------------------------------------------------------------
    */

        if (!$response->successful()) {

            $payment->update([

                'status' =>
                'failed',

            ]);


            return back()->with(
                'error',
                'Unable to initialize Paystack payment. Please try again.'
            );
        }


        $data =
            $response->json();


        if (
            !isset(
                $data['status']
            )
            ||
            !$data['status']
        ) {

            $payment->update([

                'status' =>
                'failed',

            ]);


            return back()->with(
                'error',
                $data['message']
                    ?? 'Paystack payment initialization failed.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | REDIRECT TO PAYSTACK
    |--------------------------------------------------------------------------
    */

        return redirect(
            $data['data']['authorization_url']
        );
    }


    public function additionalPaymentCallback(
        Request $request
    ) {

        $reference =
            $request->query('reference');


        if (!$reference) {

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment reference was not provided.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VERIFY WITH PAYSTACK
    |--------------------------------------------------------------------------
    |
    | NEVER trust only the browser callback.
    |
    */

        $response = Http::withToken(
            config('services.paystack.secret_key')
        )
            ->acceptJson()
            ->get(
                'https://api.paystack.co/transaction/verify/' .
                    urlencode($reference)
            );


        if (!$response->successful()) {

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Unable to verify payment with Paystack.'
                );
        }


        $data =
            $response->json();


        if (
            !isset(
                $data['status']
            )
            ||
            !$data['status']
        ) {

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Paystack payment verification failed.'
                );
        }


        $transaction =
            $data['data'];


        /*
    |--------------------------------------------------------------------------
    | FIND PAYMENT
    |--------------------------------------------------------------------------
    */

        $payment = Payment::with(
            'paymentItem'
        )
            ->where(
                'paystack_reference',
                $reference
            )
            ->first();


        if (!$payment) {

            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment record could not be found.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | PREVENT DOUBLE PROCESSING
    |--------------------------------------------------------------------------
    */

        if ($payment->status === 'paid') {

            return redirect()
                ->route('payment.additional')
                ->with(
                    'success',
                    'This payment has already been confirmed.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VERIFY STATUS
    |--------------------------------------------------------------------------
    */

        if (
            ($transaction['status'] ?? null)
            !== 'success'
        ) {

            $payment->update([

                'status' =>
                'failed',

            ]);


            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment was not successful.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VERIFY AMOUNT
    |--------------------------------------------------------------------------
    |
    | Paystack returns kobo.
    |
    */

        $expectedAmount =
            (int) round(
                ((float) $payment->amount) * 100
            );


        $paidAmount =
            (int) (
                $transaction['amount']
                ?? 0
            );


        if ($expectedAmount !== $paidAmount) {

            $payment->update([

                'status' =>
                'failed',

            ]);


            return redirect()
                ->route('payment.additional')
                ->with(
                    'error',
                    'Payment amount verification failed.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | MARK PAYMENT AS PAID
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $payment,
            $transaction
        ) {

            /*
    |--------------------------------------------------------------------------
    | MARK PAYMENT AS PAID
    |--------------------------------------------------------------------------
    */

            $payment->update([

                'status' =>
                'paid',

                'paystack_reference' =>
                $transaction['reference']
                    ?? $payment->paystack_reference,

                'paid_at' =>
                now(),

                'verified_at' =>
                now(),

                'gateway_transaction_id' =>
                $transaction['id']
                    ?? null,

                'gateway_status' =>
                $transaction['status']
                    ?? null,

                'gateway_response' =>
                $transaction,

            ]);
        });


        /*
|--------------------------------------------------------------------------
| GENERATE OPERATIONAL RIGHTS DOCUMENT
|--------------------------------------------------------------------------
|
| Only certificate payment items generate an Operational Rights
| Document.
|
*/

        $documentCodes = [

            'CHARCOAL_LIFTING',

            'CHARCOAL_LIFTING_RCG',

            'CHARCOAL_DEALING_SUPPLIER',

            'CHARCOAL_DEALING_DEALER',

            'CHARCOAL_PRODUCING',

        ];


        if (
            $payment->paymentItem &&
            in_array(
                $payment->paymentItem->code,
                $documentCodes,
                true
            )
        ) {

            try {

                $documentService =
                    app(
                        OperationalRightsDocumentService::class
                    );

                $document =
                    $documentService->generate(
                        $payment
                    );

                Log::info(
                    'OPERATIONAL RIGHTS DOCUMENT GENERATED',
                    [

                        'payment_id' =>
                        $payment->id,

                        'document_id' =>
                        $document->id,

                        'document_number' =>
                        $document->document_number,

                        'document_type' =>
                        $document->document_type,

                    ]
                );
            } catch (\Throwable $e) {

                /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | Payment has already been confirmed.
        |
        | We do NOT change the payment back to failed simply because
        | document generation encountered a problem.
        |
        */

                Log::error(
                    'OPERATIONAL RIGHTS DOCUMENT GENERATION FAILED',
                    [

                        'payment_id' =>
                        $payment->id,

                        'payment_item_id' =>
                        $payment->payment_item_id,

                        'payment_item_code' =>
                        $payment->paymentItem->code
                            ?? null,

                        'error' =>
                        $e->getMessage(),

                        'file' =>
                        $e->getFile(),

                        'line' =>
                        $e->getLine(),

                    ]
                );
            }
        }


        /*
    |--------------------------------------------------------------------------
    | FUTURE DOCUMENT GENERATION HOOK
    |--------------------------------------------------------------------------
    |
    | We deliberately do NOT generate certificates here yet.
    |
    | Later:
    |
    | certificate payment
    |       ↓
    | OperationalRightsDocument
    |
    | penalty payment
    |       ↓
    | PaymentReceipt
    |
    | afforestation payment
    |       ↓
    | application
    |       ↓
    | approval
    |       ↓
    | receipt + transit pass
    |
    */

        return redirect()
            ->route(
                'payment.additional'
            )
            ->with(
                'success',
                $payment->paymentItem->name .
                    ' payment was successful.'
            );
    }
}
