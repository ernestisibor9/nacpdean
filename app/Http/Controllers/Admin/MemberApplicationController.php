<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\MembershipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'membership',
            'membershipCard',
            'user.payments' => function ($query) {

                $query->where('status', 'paid')
                    ->latest();

            },
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
            'membership',
            'membershipCard',
            'user.payments' => function ($query) {

                $query->where('status', 'paid')
                    ->latest();

            },
        ])
            ->findOrFail($id);


        return view(
            'admin.members.show',
            compact('application')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE APPLICATION
    |--------------------------------------------------------------------------
    |
    | Approval creates:
    |
    | 1. Membership number
    | 2. Membership record
    | 3. Membership card
    | 4. QR verification token
    |
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
                'membershipCategory',
                'membership',
                'membershipCard',
            ])
                ->lockForUpdate()
                ->findOrFail($id);


            /*
            |--------------------------------------------------------------------------
            | ALREADY APPROVED
            |--------------------------------------------------------------------------
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
            | ONLY SUBMITTED APPLICATIONS
            |--------------------------------------------------------------------------
            */

            if ($application->status !== 'submitted') {

                return back()->with(
                    'error',
                    'Only submitted applications can be approved.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $hasPaid = $application->user
                ->payments()
                ->where(
                    'payment_type',
                    'membership'
                )
                ->where(
                    'status',
                    'paid'
                )
                ->exists();


            if (!$hasPaid) {

                return back()->with(
                    'error',
                    'This member cannot be approved because payment has not been completed.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP CATEGORY
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
            | CATEGORY CODE
            |--------------------------------------------------------------------------
            */

            $categoryCode = strtoupper(
                trim(
                    $application
                        ->membershipCategory
                        ->code
                )
            );


            /*
            |--------------------------------------------------------------------------
            | MEMBER TYPE
            |--------------------------------------------------------------------------
            */

            $memberType = strtolower(
                trim(
                    $application
                        ->membershipCategory
                        ->member_type
                )
            );


            /*
            |--------------------------------------------------------------------------
            | ALLOWED CATEGORIES
            |--------------------------------------------------------------------------
            */

            $allowedCategories = [

                'EXP',
                'SLR',
                'DEA',
                'PRD',
                'RCG',
                'NEC',

            ];


            if (!in_array(
                $categoryCode,
                $allowedCategories,
                true
            )) {

                return back()->with(
                    'error',
                    'Invalid membership category code: ' .
                    $categoryCode
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ALLOWED MEMBER TYPES
            |--------------------------------------------------------------------------
            */

            $allowedMemberTypes = [

                'regular',
                'affiliate',

            ];


            if (!in_array(
                $memberType,
                $allowedMemberTypes,
                true
            )) {

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
            */

            if (
                $categoryCode === 'RCG'
                &&
                $memberType !== 'affiliate'
            ) {

                return back()->with(
                    'error',
                    'RCG membership must have member type "affiliate".'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REGULAR MEMBERSHIP TYPES
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $categoryCode,
                    [
                        'EXP',
                        'SLR',
                        'DEA',
                        'PRD',
                    ],
                    true
                )
                &&
                $memberType !== 'regular'
            ) {

                return back()->with(
                    'error',
                    'This membership category must have member type "regular".'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EXISTING MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            if ($application->membership) {

                return back()->with(
                    'error',
                    'A membership record already exists for this application.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | STATE CODE
            |--------------------------------------------------------------------------
            */

            $stateCode = $this->getStateCode(
                $application->state
            );


            if (!$stateCode) {

                return back()->with(
                    'error',
                    'Invalid Nigerian state. Please correct the member state before approval.'
                );
            }


            $stateCode = strtoupper(
                trim($stateCode)
            );


            if (!preg_match(
                '/^[A-Z]{3}$/',
                $stateCode
            )) {

                return back()->with(
                    'error',
                    'Invalid state code. The state code must contain exactly 3 letters.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GENERATE MEMBERSHIP NUMBER
            |--------------------------------------------------------------------------
            */

            $lastNumber = MemberProfile::where(
                'membership_category_id',
                $application->membership_category_id
            )
                ->whereNotNull(
                    'membership_number'
                )
                ->lockForUpdate()
                ->get()
                ->map(function ($profile) {

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


            $nextNumber =
                ((int) $lastNumber) + 1;


            $sequence = str_pad(
                $nextNumber,
                4,
                '0',
                STR_PAD_LEFT
            );


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP NUMBER
            |--------------------------------------------------------------------------
            */

            if ($categoryCode === 'RCG') {

                $membershipNumber =
                    'NMN-RCG-EXP-' .
                    $stateCode .
                    '-' .
                    $sequence;

            } elseif ($categoryCode === 'EXP') {

                $membershipNumber =
                    'NMN-EXP-' .
                    $stateCode .
                    '-' .
                    $sequence;

            } else {

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
            | DOUBLE CHECK NUMBER
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
            | DATES
            |--------------------------------------------------------------------------
            */

            $issuedAt = now();

            $expiresAt = now()->addYear();


            /*
            |--------------------------------------------------------------------------
            | UPDATE PROFILE
            |--------------------------------------------------------------------------
            */

            $application->update([

                'membership_number' =>
                    $membershipNumber,

                'status' =>
                    'approved',

                'approved_at' =>
                    $issuedAt,

                'rejection_reason' =>
                    null,

                'admin_comment' =>
                    null,

            ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE MEMBERSHIP
            |--------------------------------------------------------------------------
            */

            $membership = Membership::create([

                'user_id' =>
                    $application->user_id,

                'member_profile_id' =>
                    $application->id,

                'membership_category_id' =>
                    $application->membership_category_id,

                'membership_number' =>
                    $membershipNumber,

                'status' =>
                    'active',

                'issued_at' =>
                    $issuedAt->toDateString(),

                'expires_at' =>
                    $expiresAt->toDateString(),

                'approved_at' =>
                    $issuedAt,

                'approved_by' =>
                    auth()->id(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | GENERATE CARD NUMBER
            |--------------------------------------------------------------------------
            */

            $cardNumber = $this->generateCardNumber();


            /*
            |--------------------------------------------------------------------------
            | GENERATE QR TOKEN
            |--------------------------------------------------------------------------
            */

            $qrToken = (string) Str::uuid();


            /*
            |--------------------------------------------------------------------------
            | CREATE MEMBERSHIP CARD
            |--------------------------------------------------------------------------
            */

            MembershipCard::create([

                'membership_id' =>
                    $membership->id,

                'member_profile_id' =>
                    $application->id,

                'card_number' =>
                    $cardNumber,

                'membership_number' =>
                    $membershipNumber,

                'membership_category_id' =>
                    $application->membership_category_id,

                'issued_at' =>
                    $issuedAt->toDateString(),

                'expires_at' =>
                    $expiresAt->toDateString(),

                'qr_token' =>
                    $qrToken,

                'status' =>
                    'active',

                'replaced_card_id' =>
                    null,

                'generated_at' =>
                    now(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | UPDATE USER MEMBER TYPE
            |--------------------------------------------------------------------------
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

    public function reject(
        Request $request,
        $id
    ) {

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
        | APPROVED MEMBER CANNOT BE REJECTED
        |--------------------------------------------------------------------------
        */

        if ($application->status === 'approved') {

            return back()->with(
                'error',
                'An approved member cannot be rejected.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REJECT
        |--------------------------------------------------------------------------
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
                'Member application rejected. The member can correct the application and submit it again.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE CARD NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateCardNumber(): string
    {
        $lastCard = MembershipCard::latest('id')
            ->lockForUpdate()
            ->first();


        $nextNumber = 1;


        if ($lastCard) {

            if (
                preg_match(
                    '/(\d+)$/',
                    $lastCard->card_number,
                    $matches
                )
            ) {

                $nextNumber =
                    ((int) $matches[1]) + 1;
            }
        }


        return 'CARD-' .
            str_pad(
                $nextNumber,
                6,
                '0',
                STR_PAD_LEFT
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GET STATE CODE
    |--------------------------------------------------------------------------
    */

    private function getStateCode($state)
    {
        if (!$state) {
            return null;
        }


        $state = trim($state);

        $state = preg_replace(
            '/\s+/',
            ' ',
            $state
        );


        $normalizedState =
            strtolower($state);


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


        return $stateCodes[$normalizedState] ?? null;
    }
}
