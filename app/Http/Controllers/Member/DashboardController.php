<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Payment;
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
])->where(
    'user_id',
    $user->id
)->first();




        /*
        |--------------------------------------------------------------------------
        | PROFILE STATUS
        |--------------------------------------------------------------------------
        |
        | Possible states:
        |
        | null / draft    = Not yet completed profile
        | submitted       = Application awaiting approval
        | approved        = Full member dashboard
        |
        */

        $profileStatus = $profile
            ? $profile->status
            : 'draft';


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ACCESS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Only an APPROVED profile can see the full
        | membership dashboard.
        |
        */

        $isApproved = (
            $profile &&
            $profile->status === 'approved'
        );

$membershipCard = null;

if ($isApproved && $profile) {
    $membershipCard = $profile->membershipCard;
}


        /*
        |--------------------------------------------------------------------------
        | MEMBER DASHBOARD STATUS
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
        | MEMBERSHIP INFORMATION
        |--------------------------------------------------------------------------
        */

        $membershipCategory = null;

        $membershipPaymentDate = null;

        $membershipExpirationDate = null;

        $membershipIsExpired = false;


        /*
        |--------------------------------------------------------------------------
        | ONLY PREPARE MEMBERSHIP DETAILS FOR APPROVED MEMBERS
        |--------------------------------------------------------------------------
        |
        | This prevents unapproved users from receiving/displaying
        | membership information on the dashboard.
        |
        */

        if ($isApproved && $membershipPayment) {

            /*
            |--------------------------------------------------------------------------
            | PAYMENT DATE
            |--------------------------------------------------------------------------
            */

            $membershipPaymentDate =
                $membershipPayment->updated_at;


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP EXPIRATION
            |--------------------------------------------------------------------------
            |
            | Membership expires on December 31 of the
            | same calendar year.
            |
            */

            $membershipExpirationDate =
                Carbon::parse(
                    $membershipPaymentDate
                )
                ->endOfYear()
                ->endOfDay();


            /*
            |--------------------------------------------------------------------------
            | CHECK EXPIRATION
            |--------------------------------------------------------------------------
            */

            $membershipIsExpired =
                now()->greaterThan(
                    $membershipExpirationDate
                );


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP CATEGORY
            |--------------------------------------------------------------------------
            */

            if (method_exists(
                $membershipPayment,
                'membershipCategory'
            )) {

                $membershipCategory =
                    $membershipPayment->membershipCategory;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER NAME
        |--------------------------------------------------------------------------
        |
        | Only approved members need the full member name
        | for the dashboard.
        |
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
        | Currently fixed at ₦0.00.
        |
        */

        $outstandingBalance = 0.00;


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP FEE
        |--------------------------------------------------------------------------
        |
        | Only expose the membership fee to approved members.
        |
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

            $membershipAnnualStatus = 'Outstanding';

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
                'membershipCategory',
                'membershipPaymentDate',
                'membershipExpirationDate',
                'membershipIsExpired',
                'membershipPaymentStatus',
                'membershipAnnualStatus',
                'memberName',
                'outstandingBalance',
                'membershipFee',
                'membershipCard',
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
