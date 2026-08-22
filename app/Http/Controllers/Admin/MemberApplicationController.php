<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberApplicationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ALL MEMBER APPLICATIONS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $applications = MemberProfile::with([
            'user',
            'membershipCategory',
            'user.payments' => function ($query) {
                $query->where('status', 'paid')
                    ->latest();
            }
        ])
            ->latest()
            ->get();

        return view(
            'admin.members.index',
            compact('applications')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW APPLICATION
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $application = MemberProfile::with([
            'user',
            'membershipCategory',
            'user.payments' => function ($query) {
                $query->where('status', 'paid')
                    ->latest();
            }
        ])->findOrFail($id);

        return view(
            'admin.members.show',
            compact('application')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE APPLICATION
    |--------------------------------------------------------------------------
    */

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {

            /*
        |--------------------------------------------------------------------------
        | GET APPLICATION
        |--------------------------------------------------------------------------
        */

            $application = MemberProfile::with([
                'user',
                'membershipCategory'
            ])
                ->lockForUpdate()
                ->findOrFail($id);


            /*
        |--------------------------------------------------------------------------
        | PREVENT APPROVING AN ALREADY APPROVED MEMBER
        |--------------------------------------------------------------------------
        |
        | If the member is already approved, do not change the
        | membership number.
        |
        */

            if ($application->status === 'approved') {

                return back()->with(
                    'error',
                    'This member has already been approved. Membership Number: ' .
                        ($application->membership_number ?? 'Not assigned')
                );
            }


            /*
        |--------------------------------------------------------------------------
        | MAKE SURE PAYMENT WAS MADE
        |--------------------------------------------------------------------------
        */

            $hasPaid = $application->user
                ->payments()
                ->where('payment_type', 'membership')
                ->where('status', 'paid')
                ->exists();


            if (!$hasPaid) {

                return back()->with(
                    'error',
                    'This member cannot be approved because payment has not been completed.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | MAKE SURE MEMBERSHIP CATEGORY EXISTS
        |--------------------------------------------------------------------------
        */

            if (!$application->membershipCategory) {

                return back()->with(
                    'error',
                    'Membership category has not been selected for this member.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | GET CATEGORY CODE
        |--------------------------------------------------------------------------
        */

            $categoryCode = strtoupper(
                trim($application->membershipCategory->code)
            );


            /*
        |--------------------------------------------------------------------------
        | GET MEMBER TYPE
        |--------------------------------------------------------------------------
        |
        | Regular:
        |   Exporter
        |   Supplier
        |   Dealer
        |   Producer
        |
        | Affiliate:
        |   RCG
        |
        */

            $memberType = strtolower(
                trim($application->membershipCategory->member_type)
            );


            /*
        |--------------------------------------------------------------------------
        | VALIDATE MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

            $allowedCategories = [
                'EXP',
                'SLR',
                'DEA',
                'PRD',
                'RCG',
            ];


            if (!in_array($categoryCode, $allowedCategories, true)) {

                return back()->with(
                    'error',
                    'Invalid membership category code: ' .
                        $categoryCode
                );
            }


            /*
        |--------------------------------------------------------------------------
        | VALIDATE MEMBER TYPE
        |--------------------------------------------------------------------------
        */

            $allowedMemberTypes = [
                'regular',
                'affiliate',
            ];


            if (!in_array($memberType, $allowedMemberTypes, true)) {

                return back()->with(
                    'error',
                    'Invalid member type: ' .
                        $memberType
                );
            }


            /*
        |--------------------------------------------------------------------------
        | RCG MUST BE AFFILIATE
        |--------------------------------------------------------------------------
        |
        | According to the membership structure:
        |
        | RCG = Affiliate
        |
        */

            if ($categoryCode === 'RCG' && $memberType !== 'affiliate') {

                return back()->with(
                    'error',
                    'RCG membership must have member type "affiliate".'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | REGULAR CATEGORIES MUST BE REGULAR MEMBERS
        |--------------------------------------------------------------------------
        |
        | EXP, SLR, DEA and PRD are regular membership categories.
        |
        */

            if (
                in_array(
                    $categoryCode,
                    ['EXP', 'SLR', 'DEA', 'PRD'],
                    true
                )
                && $memberType !== 'regular'
            ) {

                return back()->with(
                    'error',
                    'This membership category must have member type "regular".'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | EXISTING MEMBERSHIP NUMBER MUST NEVER CHANGE
        |--------------------------------------------------------------------------
        |
        | If the member already has a membership number, retain it.
        |
        */

            if (!empty($application->membership_number)) {

                $existingMembershipNumber =
                    $application->membership_number;


                $application->update([

                    'status' =>
                    'approved',

                    'approved_at' =>
                    now(),

                    'rejection_reason' =>
                    null,

                    'admin_comments' =>
                    null,

                ]);


                /*
            |--------------------------------------------------------------------------
            | UPDATE USER MEMBER TYPE
            |--------------------------------------------------------------------------
            |
            | Keep users.member_type synchronized with the
            | membership category.
            |
            */

                $application->user->update([

                    'member_type' =>
                    $memberType,

                ]);


                return redirect()
                    ->route(
                        'admin.members.show',
                        $application->id
                    )
                    ->with(
                        'success',
                        'Member application approved successfully. Existing Membership Number retained: ' .
                            $existingMembershipNumber
                    );
            }


            /*
        |--------------------------------------------------------------------------
        | GET 3-LETTER STATE CODE
        |--------------------------------------------------------------------------
        |
        | Examples:
        |
        | Lagos       -> LAG
        | Kaduna      -> KAD
        | Oyo         -> OYO
        | Rivers      -> RIV
        | Delta       -> DEL
        | Edo         -> EDO
        | Abuja / FCT -> FCT
        |
        */

            $stateCode = $this->getStateCode(
                $application->state
            );


            /*
        |--------------------------------------------------------------------------
        | INVALID STATE
        |--------------------------------------------------------------------------
        */

            if (!$stateCode) {

                return back()->with(
                    'error',
                    'Invalid Nigerian state. Please correct the member state before approval.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | MAKE SURE STATE CODE IS EXACTLY 3 LETTERS
        |--------------------------------------------------------------------------
        */

            $stateCode = strtoupper(
                trim($stateCode)
            );


            if (!preg_match('/^[A-Z]{3}$/', $stateCode)) {

                return back()->with(
                    'error',
                    'Invalid state code. The state code must contain exactly 3 letters.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | GENERATE SEQUENTIAL MEMBERSHIP NUMBER
        |--------------------------------------------------------------------------
        |
        | The sequence is independent for each membership category.
        |
        | Exporter:
        |   0001, 0002, 0003...
        |
        | RCG:
        |   0001, 0002, 0003...
        |
        | Supplier:
        |   0001, 0002, 0003...
        |
        | Dealer:
        |   0001, 0002, 0003...
        |
        | Producer:
        |   0001, 0002, 0003...
        |
        */

            $lastNumber = MemberProfile::where(
                'membership_category_id',
                $application->membership_category_id
            )
                ->whereNotNull('membership_number')
                ->lockForUpdate()
                ->get()
                ->map(function ($profile) {

                    /*
                |--------------------------------------------------------------------------
                | EXTRACT LAST 4 DIGITS
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | NMN-EXP-LAG-0007
                |
                | returns:
                |
                | 7
                |
                |
                | Example:
                |
                | NMN-RCG-EXP-LAG-0007
                |
                | also returns:
                |
                | 7
                |
                */

                    if (
                        preg_match(
                            '/(\d{4})$/',
                            $profile->membership_number,
                            $matches
                        )
                    ) {

                        return (int) $matches[1];
                    }


                    return 0;
                })
                ->max();


            /*
        |--------------------------------------------------------------------------
        | NEXT SEQUENCE NUMBER
        |--------------------------------------------------------------------------
        */

            $nextNumber =
                ((int) $lastNumber) + 1;


            /*
        |--------------------------------------------------------------------------
        | FORMAT SEQUENCE
        |--------------------------------------------------------------------------
        |
        | 1    -> 0001
        | 9    -> 0009
        | 25   -> 0025
        | 125  -> 0125
        | 999  -> 0999
        | 1000 -> 1000
        |
        */

            $sequence = str_pad(
                $nextNumber,
                4,
                '0',
                STR_PAD_LEFT
            );


            /*
        |--------------------------------------------------------------------------
        | GENERATE MEMBERSHIP NUMBER
        |--------------------------------------------------------------------------
        |
        | REGULAR EXPORTER:
        |
        | NMN-EXP-LAG-0001
        |
        | RCG AFFILIATE EXPORTER:
        |
        | NMN-RCG-EXP-LAG-0001
        |
        | SUPPLIER:
        |
        | NMN-SLR-LAG-0001
        |
        | DEALER:
        |
        | NMN-DEA-LAG-0001
        |
        | PRODUCER:
        |
        | NMN-PRD-LAG-0001
        |
        */

            if ($categoryCode === 'RCG') {

                /*
            |--------------------------------------------------------------------------
            | RCG EXPORTER
            |--------------------------------------------------------------------------
            */

                $membershipNumber =
                    'NMN-RCG-EXP-' .
                    $stateCode .
                    '-' .
                    $sequence;
            } elseif ($categoryCode === 'EXP') {

                /*
            |--------------------------------------------------------------------------
            | REGULAR / DIRECT EXPORTER
            |--------------------------------------------------------------------------
            */

                $membershipNumber =
                    'NMN-EXP-' .
                    $stateCode .
                    '-' .
                    $sequence;
            } else {

                /*
            |--------------------------------------------------------------------------
            | SUPPLIER / DEALER / PRODUCER
            |--------------------------------------------------------------------------
            */

                $membershipNumber =
                    'NMN-' .
                    $categoryCode .
                    '-' .
                    $stateCode .
                    '-' .
                    $sequence;
            }


            /*
        |--------------------------------------------------------------------------
        | MAKE SURE GENERATED MEMBERSHIP NUMBER DOES NOT EXIST
        |--------------------------------------------------------------------------
        */

            $duplicate = MemberProfile::where(
                'membership_number',
                $membershipNumber
            )->exists();


            if ($duplicate) {

                return back()->with(
                    'error',
                    'The generated membership number already exists. Please try the approval again.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | APPROVE APPLICATION
        |--------------------------------------------------------------------------
        |
        | This is the first time this member is receiving
        | a membership number.
        |
        */

            $application->update([

                'membership_number' =>
                $membershipNumber,

                'status' =>
                'approved',

                'approved_at' =>
                now(),

                'rejection_reason' =>
                null,

                'admin_comments' =>
                null,

            ]);


            /*
        |--------------------------------------------------------------------------
        | UPDATE USER MEMBER TYPE
        |--------------------------------------------------------------------------
        |
        | This keeps users.member_type synchronized:
        |
        | regular   -> regular
        | affiliate -> affiliate
        |
        */

            $application->user->update([

                'member_type' =>
                $memberType,

            ]);


            /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

            return redirect()
                ->route(
                    'admin.members.show',
                    $application->id
                )
                ->with(
                    'success',
                    'Member application approved successfully. Membership Number: ' .
                        $membershipNumber
                );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT APPLICATION
    |--------------------------------------------------------------------------
    */

    public function reject(Request $request, $id)
    {
        $request->validate([

            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],

        ]);


        $application =
            MemberProfile::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | DO NOT REJECT AN ALREADY APPROVED MEMBER
        |--------------------------------------------------------------------------
        |
        | If your workflow does not allow an approved member to be rejected,
        | keep this protection.
        |
        */

        if ($application->status === 'approved') {

            return back()->with(
                'error',
                'An approved member cannot be rejected.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REJECT APPLICATION
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We DO NOT clear membership_number here.
        |
        | If the member already had a membership number from an earlier
        | approval, that number remains permanently attached to the member.
        |
        */

        $application->update([

            'status' =>
            'rejected',

            'rejection_reason' =>
            $request->rejection_reason,

            'approved_at' =>
            null,

        ]);


        return redirect()
            ->route(
                'admin.members.show',
                $application->id
            )
            ->with(
                'success',
                'Member application rejected.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE CONTROL NUMBER
    |--------------------------------------------------------------------------
    |
    | Exporter format:
    |
    | CN-001-N
    | CN-002-N
    | CN-003-N
    |
    |--------------------------------------------------------------------------
    */

    private function generateControlNumber()
    {
        $lastControlNumber = MemberProfile::whereNotNull(
            'membership_number'
        )
            ->lockForUpdate()
            ->get()
            ->map(function ($profile) {

                /*
                |--------------------------------------------------------------------------
                | EXTRACT CONTROL NUMBER
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | NMN-CN-001-N-EXP-LAG-0001
                |
                | Extract:
                |
                | 001
                |
                */

                if (
                    preg_match(
                        '/NMN-CN-(\d+)-N-/',
                        $profile->membership_number,
                        $matches
                    )
                ) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max();


        $nextControlNumber =
            ((int) $lastControlNumber) + 1;


        return 'CN-' .
            str_pad(
                $nextControlNumber,
                3,
                '0',
                STR_PAD_LEFT
            ) .
            '-N';
    }


    /*
    |--------------------------------------------------------------------------
    | GET 3-LETTER STATE CODE
    |--------------------------------------------------------------------------
    |
    | The member manually enters the state.
    |
    | We normalize the value before checking it.
    |
    | Examples:
    |
    | Lagos
    | lagos
    | LAGOS
    |  Lagos
    | Lagos
    |
    | All become:
    |
    | LAG
    |
    |--------------------------------------------------------------------------
    */

    private function getStateCode($state)
    {
        if (!$state) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE STATE
        |--------------------------------------------------------------------------
        */

        $state = trim($state);


        $state = preg_replace(
            '/\s+/',
            ' ',
            $state
        );


        $normalizedState =
            strtolower($state);


        /*
        |--------------------------------------------------------------------------
        | NIGERIAN STATE CODES
        |--------------------------------------------------------------------------
        |
        | All codes are exactly 3 characters.
        |
        */

        $stateCodes = [

            'abia' =>
            'ABI',

            'adamawa' =>
            'ADA',

            'akwa ibom' =>
            'AKW',

            'anambra' =>
            'ANA',

            'bauchi' =>
            'BAU',

            'bayelsa' =>
            'BAY',

            'benue' =>
            'BEN',

            'borno' =>
            'BOR',

            'cross river' =>
            'CRO',

            'delta' =>
            'DEL',

            'ebonyi' =>
            'EBO',

            'edo' =>
            'EDO',

            'ekiti' =>
            'EKT',

            'enugu' =>
            'ENU',

            'gombe' =>
            'GOM',

            'imo' =>
            'IMO',

            'jigawa' =>
            'JIG',

            'kaduna' =>
            'KAD',

            'kano' =>
            'KAN',

            'katsina' =>
            'KAT',

            'kebbi' =>
            'KEB',

            'kogi' =>
            'KOG',

            'kwara' =>
            'KWA',

            'lagos' =>
            'LAG',

            'nasarawa' =>
            'NAS',

            'niger' =>
            'NIG',

            'ogun' =>
            'OGU',

            'ondo' =>
            'OND',

            'osun' =>
            'OSU',

            'oyo' =>
            'OYO',

            'plateau' =>
            'PLA',

            'rivers' =>
            'RIV',

            'sokoto' =>
            'SOK',

            'taraba' =>
            'TAR',

            'yobe' =>
            'YOB',

            'zamfara' =>
            'ZAM',

            'federal capital territory' =>
            'FCT',

            'fct' =>
            'FCT',

        ];


        /*
        |--------------------------------------------------------------------------
        | RETURN STATE CODE
        |--------------------------------------------------------------------------
        */

        return $stateCodes[$normalizedState] ?? null;
    }
}
