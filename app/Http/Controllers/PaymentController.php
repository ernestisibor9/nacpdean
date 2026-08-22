<?php

namespace App\Http\Controllers;

use App\Models\MembershipCategory;
use App\Models\Payment;
use App\Models\MembershipCategoryFee;
use App\Models\MemberFee;
use App\Models\MemberProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        */

        elseif (
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
        */

        else {

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
        */

        elseif (
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
                empty(
                    $paystackData['data']['authorization_url']
                )
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
}
