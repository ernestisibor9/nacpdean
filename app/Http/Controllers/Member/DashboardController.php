<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\GeneratedDocument;
use App\Models\Membership;
use App\Models\MembershipCard;
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
        |
        | The FIRST membership registration payment (used to determine
        | whether the member has completed initial signup).
        |
        */

        $membershipPayment = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereIn('payment_type', ['membership', 'membership_renewal'])
            ->latest('id')
            ->first();

        $hasPaid = $membershipPayment !== null;

        /*
        |--------------------------------------------------------------------------
        | GET MEMBER PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::with([
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
            $membership = Membership::with('membershipCategory')
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        |
        | Prefer the membership's own category relation, fall back to
        | the profile's category.
        |
        */

        $membershipCategory = null;

        if ($isApproved) {
            $membershipCategory = $membership?->membershipCategory
                ?? $profile?->membershipCategory;
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CARD
        |--------------------------------------------------------------------------
        |
        | Always read from the membership_cards table.
        |
        | The profile's membershipCard relation returns a stale record
        | after renewal, so we bypass it and query the latest active card
        | for this member's latest membership.
        |
        */

        $membershipCard = null;

        if ($isApproved && $membership) {
            $membershipCard = MembershipCard::where('membership_id', $membership->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP DEBIT TRANSACTION
        |--------------------------------------------------------------------------
        |
        | The membership ID card is only valid when the membership
        | debit transaction has been paid.
        |
        */

        $membershipDebitTransaction = null;
        $membershipDebitNotPaid    = false;

        if ($isApproved) {
            $membershipDebitTransaction = Transaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->whereNull('payment_item_id')
                ->latest('id')
                ->first();

            $membershipDebitNotPaid = $membershipDebitTransaction
                && $membershipDebitTransaction->status === 'not paid';
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP INFORMATION
        |--------------------------------------------------------------------------
        */

        $membershipPaymentDate    = null;
        $membershipExpirationDate = null;
        $membershipIsExpired      = false;
        $membershipIsActive       = false;

        if ($isApproved) {

            /*
            |------------------------------------------------------------------
            | MEMBERSHIP PAYMENT DATE
            |------------------------------------------------------------------
            |
            | Show the date of the MOST RECENT successful membership
            | or renewal payment.
            |
            */

            $latestMembershipCredit = Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->where('status', 'paid')
                ->where(function ($query) {
                    $query
                        ->where('narration', 'like', 'Membership%')
                        ->orWhere('narration', 'like', '%Membership Renewal%');
                })
                ->latest('id')
                ->first();

            if ($latestMembershipCredit) {
                $membershipPaymentDate = $latestMembershipCredit->created_at;
            } elseif ($membershipPayment) {
                $membershipPaymentDate = $membershipPayment->updated_at;
            }

            /*
            |------------------------------------------------------------------
            | MEMBERSHIP EXPIRATION
            |------------------------------------------------------------------
            |
            | NACPDEAN membership is ANNUAL and CALENDAR-YEAR based.
            |
            | Example:
            | Issued:  23 Sep 2026
            | Expires: 31 Dec 2026
            |
            | The membership record's expires_at column is authoritative.
            |
            */

            if ($membership && $membership->expires_at) {

                $membershipExpirationDate = Carbon::parse($membership->expires_at)
                    ->endOfDay();

            } elseif ($membershipPaymentDate) {

                /*
                |--------------------------------------------------------------
                | FALLBACK
                |--------------------------------------------------------------
                |
                | If the membership record has no expires_at, calculate
                | the expiry from the latest payment year.
                |
                */

                $membershipExpirationDate = Carbon::parse($membershipPaymentDate)
                    ->endOfYear()
                    ->endOfDay();
            }

            /*
            |------------------------------------------------------------------
            | EXPIRATION CHECK
            |------------------------------------------------------------------
            */

            $membershipIsExpired = $membershipExpirationDate
                ? now()->greaterThan($membershipExpirationDate)
                : false;

            /*
            |------------------------------------------------------------------
            | ACTIVE CHECK
            |------------------------------------------------------------------
            |
            | Membership is active when:
            |   1. It exists
            |   2. Its status is 'active'
            |   3. Its expiration date is in the future
            |
            */

            $membershipIsActive =
                $membership
                && $membership->status === 'active'
                && (
                    !$membershipExpirationDate
                    || now()->lessThanOrEqualTo($membershipExpirationDate)
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER DOCUMENTS
        |--------------------------------------------------------------------------
        */

        $generatedDocuments = collect();

        if ($isApproved && $membershipCategory && $membershipIsActive) {

            $categoryDocumentIds = $membershipCategory
                ->documents()
                ->where('documents.is_active', true)
                ->orderBy('membership_category_documents.sort_order')
                ->orderBy('documents.id')
                ->pluck('documents.id');

            if ($categoryDocumentIds->isNotEmpty()) {

                $generatedDocuments = GeneratedDocument::with(['document'])
                    ->where('user_id', $user->id)
                    ->whereIn('document_id', $categoryDocumentIds)
                    ->where('status', 'active')
                    ->orderBy('issued_at', 'desc')
                    ->get();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD STATUS
        |--------------------------------------------------------------------------
        */

        if (!$profile || $profile->status === 'draft') {

            $dashboardStatus = 'profile_incomplete';

        } elseif ($profile->status === 'submitted') {

            $dashboardStatus = 'awaiting_approval';

        } elseif ($profile->status === 'approved') {

            $dashboardStatus = 'approved';

        } elseif ($profile->status === 'rejected') {

            $dashboardStatus = 'profile_incomplete';

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
                $memberName = implode(' ', $nameParts);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER STAGE
        |--------------------------------------------------------------------------
        */

        $memberStage = $isApproved ? 3 : 2;

        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING BALANCE
        |--------------------------------------------------------------------------
        */

        $totalDebit = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        $totalCredit = Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        $outstandingBalance = max(
            0,
            (float) $totalDebit - (float) $totalCredit
        );

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP FEE
        |--------------------------------------------------------------------------
        */

        $membershipFee = ($isApproved && $membershipPayment)
            ? $membershipPayment->amount
            : 0;

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        $membershipPaymentStatus = $membershipPayment
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
