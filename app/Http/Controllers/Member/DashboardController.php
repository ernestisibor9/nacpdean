<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\GeneratedDocument;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MEMBER DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP REGISTRATION PAYMENT
        |--------------------------------------------------------------------------
        */

        $membershipPayment = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->where('payment_type', 'membership')
            ->latest()
            ->first();

        $hasPaid = $membershipPayment !== null;


        /*
        |--------------------------------------------------------------------------
        | GET MEMBER PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::with([
            'membershipCard',
            'membershipCategory',
        ])
            ->where('user_id', $user->id)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | PROFILE STATUS
        |--------------------------------------------------------------------------
        */

        $profileStatus = $profile
            ? $profile->status
            : 'draft';


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ACCESS
        |--------------------------------------------------------------------------
        */

        $isApproved = (
            $profile &&
            $profile->status === 'approved'
        );


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP
        |--------------------------------------------------------------------------
        |
        | Get the member's latest membership record.
        |
        */

        $membership = null;

        if ($isApproved) {

            $membership = Membership::where(
                'user_id',
                $user->id
            )
                ->latest('id')
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        |
        | For an approved member, the profile's membership category is
        | authoritative.
        |
        */

        $membershipCategory = null;

        if ($isApproved && $profile) {

            $membershipCategory = $profile->membershipCategory;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CARD
        |--------------------------------------------------------------------------
        */

        $membershipCard = null;

        if ($isApproved && $profile) {

            $membershipCard = $profile->membershipCard;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP DEBIT TRANSACTION
        |--------------------------------------------------------------------------
        |
        | The membership ID card is only valid when the membership
        | debit transaction has been paid.
        |
        | A membership debit has no payment_item_id.
        |
        */

        $membershipDebitTransaction = null;

        $membershipDebitNotPaid = false;

        if ($isApproved) {

            $membershipDebitTransaction = Transaction::where(
                'user_id',
                $user->id
            )
                ->where(
                    'type',
                    'debit'
                )
                ->whereNull(
                    'payment_item_id'
                )
                ->latest('id')
                ->first();


            $membershipDebitNotPaid =
                $membershipDebitTransaction &&
                $membershipDebitTransaction->status === 'not paid';
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP INFORMATION
        |--------------------------------------------------------------------------
        */

        $membershipPaymentDate = null;

        $membershipExpirationDate = null;

        $membershipIsExpired = false;

        $membershipIsActive = false;


        /*
        |--------------------------------------------------------------------------
        | ONLY PREPARE MEMBERSHIP DETAILS FOR APPROVED MEMBERS
        |--------------------------------------------------------------------------
        */

        if ($isApproved) {

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP PAYMENT DATE
            |--------------------------------------------------------------------------
            */

            if ($membershipPayment) {

                $membershipPaymentDate =
                    $membershipPayment->updated_at;
            }


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP EXPIRATION
            |--------------------------------------------------------------------------
            |
            | NACPDEAN membership is ANNUAL and CALENDAR-YEAR based.
            |
            | Example:
            |
            | Issued: 09 Sep 2026
            | Expires: 31 Dec 2026
            |
            | It does NOT expire after 12 months.
            |
            */

            if ($membership && $membership->expires_at) {

                $membershipExpirationDate =
                    Carbon::parse(
                        $membership->expires_at
                    )->endOfDay();

            } elseif ($membershipPaymentDate) {

                /*
                |--------------------------------------------------------------------------
                | FALLBACK
                |--------------------------------------------------------------------------
                |
                | If the membership record does not have expires_at yet,
                | calculate the expiry from the membership payment year.
                |
                */

                $membershipExpirationDate =
                    Carbon::parse(
                        $membershipPaymentDate
                    )
                        ->endOfYear()
                        ->endOfDay();
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK MEMBERSHIP EXPIRATION
            |--------------------------------------------------------------------------
            */

            $membershipIsExpired =
                !$membershipExpirationDate
                ? false
                : now()->greaterThan(
                    $membershipExpirationDate
                );


            /*
            |--------------------------------------------------------------------------
            | CHECK MEMBERSHIP ACTIVE STATUS
            |--------------------------------------------------------------------------
            |
            | Membership must:
            |
            | 1. Exist
            | |2. Have status = active
            | |3. Not have passed its expiration date
            |
            */

            $membershipIsActive =
                $membership
                && $membership->status === 'active'
                && (
                    !$membershipExpirationDate
                    || now()->lessThanOrEqualTo(
                        $membershipExpirationDate
                    )
                );
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER DOCUMENTS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Only retrieve documents when the member has an ACTIVE membership.
        |
        | This means expired members will not receive document cards on
        | the dashboard.
        |
        */

        $generatedDocuments = collect();

        if (
            $isApproved &&
            $membershipCategory &&
            $membershipIsActive
        ) {

            /*
            |--------------------------------------------------------------------------
            | GET DOCUMENT IDS CONFIGURED FOR THIS CATEGORY
            |--------------------------------------------------------------------------
            |
            | membership_category_documents is the source of truth.
            |
            */

            $categoryDocumentIds = $membershipCategory
                ->documents()
                ->where(
                    'documents.is_active',
                    true
                )
                ->orderBy(
                    'membership_category_documents.sort_order'
                )
                ->orderBy(
                    'documents.id'
                )
                ->pluck('documents.id');


            /*
            |--------------------------------------------------------------------------
            | GET ONLY THIS MEMBER'S GENERATED DOCUMENTS
            |--------------------------------------------------------------------------
            */

            if ($categoryDocumentIds->isNotEmpty()) {

                $generatedDocuments = GeneratedDocument::with([
                    'document',
                ])
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->whereIn(
                        'document_id',
                        $categoryDocumentIds
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'issued_at',
                        'desc'
                    )
                    ->get();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE STATUS / DASHBOARD STATUS
        |--------------------------------------------------------------------------
        */

        if (!$hasPaid) {

            $dashboardStatus = 'payment_required';

        } elseif (!$profile || $profile->status === 'draft') {

            $dashboardStatus = 'profile_incomplete';

        } elseif ($profile->status === 'submitted') {

            $dashboardStatus = 'awaiting_approval';

        } elseif ($profile->status === 'approved') {

            $dashboardStatus = 'approved';

        } else {

            $dashboardStatus = 'profile_incomplete';
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER NAME
        |--------------------------------------------------------------------------
        */

        $memberName = 'Member';

        if ($isApproved && $profile) {

            $nameParts = array_filter([

                $profile->first_name,

                $profile->middle_name,

                $profile->surname,

            ]);


            if (!empty($nameParts)) {

                $memberName =
                    implode(' ', $nameParts);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER STAGE
        |--------------------------------------------------------------------------
        */

        if (!$hasPaid) {

            $memberStage = 1;

        } elseif ($isApproved) {

            $memberStage = 3;

        } else {

            $memberStage = 2;
        }


        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING BALANCE
        |--------------------------------------------------------------------------
        |
        | Outstanding Balance =
        |
        | SUM(DEBIT) - SUM(CREDIT)
        |
        */

        $totalDebit = Transaction::where(
            'user_id',
            $user->id
        )
            ->where(
                'type',
                'debit'
            )
            ->sum('amount');


        $totalCredit = Transaction::where(
            'user_id',
            $user->id
        )
            ->where(
                'type',
                'credit'
            )
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | FINAL OUTSTANDING BALANCE
        |--------------------------------------------------------------------------
        */

        $outstandingBalance = max(
            0,
            (float) $totalDebit - (float) $totalCredit
        );


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP FEE
        |--------------------------------------------------------------------------
        */

        $membershipFee =
            ($isApproved && $membershipPayment)
            ? $membershipPayment->amount
            : 0;


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        $membershipPaymentStatus =
            $membershipPayment
            ? 'Paid'
            : 'Outstanding';


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP ANNUAL STATUS
        |--------------------------------------------------------------------------
        */

        if (!$membershipPayment) {

            $membershipAnnualStatus = 'Outstanding';

        } elseif ($membershipIsExpired) {

            $membershipAnnualStatus = 'Expired';

        } else {

            $membershipAnnualStatus = 'Active';
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'member.index',
            compact(
                'memberStage',
                'profile',
                'profileStatus',
                'dashboardStatus',
                'isApproved',
                'hasPaid',
                'membershipPayment',
                'membership',
                'membershipCategory',
                'membershipPaymentDate',
                'membershipExpirationDate',
                'membershipIsExpired',
                'membershipIsActive',
                'membershipPaymentStatus',
                'membershipAnnualStatus',
                'memberName',
                'totalDebit',
                'totalCredit',
                'outstandingBalance',
                'membershipFee',
                'membershipCard',
                'membershipDebitTransaction',
                'membershipDebitNotPaid',
                'generatedDocuments',
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER LOGOUT
    |--------------------------------------------------------------------------
    */

    public function MemberLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
