<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Membership;
use App\Models\MembershipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\DocumentGenerationService;
use App\Services\TransactionService;

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
    | 5. All configured membership documents
    |
    | Document generation is driven by:
    |
    | membership_category_documents
    |
    | The MembershipCard is NOT a GeneratedDocument.
    |
    */

    public function approve(
        $id,
        DocumentGenerationService $documentGenerationService
    ) {
        return DB::transaction(function () use (
            $id,
            $documentGenerationService
        ) {

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
                    $application->membershipCategory->code
                )
            );


            /*
            |--------------------------------------------------------------------------
            | MEMBER TYPE
            |--------------------------------------------------------------------------
            */

            $memberType = strtolower(
                trim(
                    $application->membershipCategory->member_type
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
            | PAYMENT
            |--------------------------------------------------------------------------
            |
            | The member must have a successful membership payment
            | for THIS membership category.
            |
            */

            $hasPaid = $application->user
                ->payments()
                ->where(
                    'payment_type',
                    'membership'
                )
                ->where(
                    'membership_category_id',
                    $application->membership_category_id
                )
                ->where(
                    'status',
                    'paid'
                )
                ->exists();


            if (!$hasPaid) {

                return back()->with(
                    'error',
                    'This member cannot be approved because payment has not been completed for the selected membership category.'
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
            | BUILD MEMBERSHIP NUMBER
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
            | DOUBLE CHECK MEMBERSHIP NUMBER
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
            | MEMBERSHIP DATES
            |--------------------------------------------------------------------------
            |
            | Membership is valid until December 31 of the current year.
            |
            */

            $issuedAt = now();

            $expiresAt = now()->endOfYear();


            /*
            |--------------------------------------------------------------------------
            | UPDATE MEMBER PROFILE
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
            | GENERATE ALL MEMBERSHIP DOCUMENTS
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | We no longer generate only the membership certificate.
            |
            | DocumentGenerationService reads the active documents
            | configured in membership_category_documents.
            |
            | Example:
            |
            | EXP:
            |   34 -> Annual Membership Receipt
            |   30 -> Charcoal Lifting Right - Exporter
            |   25 -> Membership Certificate - Regular Exporter
            |   27 -> Membership Confirmation Letter
            |
            | RCG:
            |   34 -> Annual Membership Receipt
            |   29 -> Charcoal Lifting Right - RCG
            |   26 -> Membership Certificate - RCG
            |   27 -> Membership Confirmation Letter
            |
            | SLR:
            |   34 -> Annual Membership Receipt
            |   33 -> Charcoal Dealing/Lifting Right - Supplier
            |
            | DEA:
            |   34 -> Annual Membership Receipt
            |   32 -> Charcoal Dealing Right - Dealer
            |
            | PRD:
            |   34 -> Annual Membership Receipt
            |   31 -> Producer Right
            |
            | The membership card remains separate from GeneratedDocument.
            |
            */

            $generatedDocuments =
                $documentGenerationService
                    ->generateMembershipDocuments(
                        $membership
                    );


            /*
            |--------------------------------------------------------------------------
            | LOG GENERATED DOCUMENTS
            |--------------------------------------------------------------------------
            */

            Log::info(
                'MEMBERSHIP APPROVED AND DOCUMENTS GENERATED',
                [
                    'user_id' =>
                        $application->user_id,

                    'membership_id' =>
                        $membership->id,

                    'membership_number' =>
                        $membershipNumber,

                    'membership_category_id' =>
                        $application->membership_category_id,

                    'category_code' =>
                        $categoryCode,

                    'member_type' =>
                        $memberType,

                    'generated_document_ids' =>
                        collect($generatedDocuments)
                            ->pluck('id')
                            ->values()
                            ->all(),

                    'generated_document_count' =>
                        count($generatedDocuments),
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            $message =
                'Member application approved successfully. Membership Number: ' .
                $membershipNumber .
                '. ' .
                count($generatedDocuments) .
                ' membership document(s) generated successfully.';


            return redirect()
                ->route(
                    'admin.members.show',
                    $application->id
                )
                ->with(
                    'success',
                    $message
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
    | VIEW MEMBER UPLOADED DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function viewDocument($id, $document)
    {
        $application =
            MemberProfile::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | ALLOWED DOCUMENTS
        |--------------------------------------------------------------------------
        */

        $allowedDocuments = [
            'cac_certificate',
            'cac_particulars_of_directors',
            'nepc_export_license',
        ];


        if (!in_array(
            $document,
            $allowedDocuments,
            true
        )) {

            abort(
                404,
                'Invalid document type.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET FILE PATH FROM DATABASE
        |--------------------------------------------------------------------------
        */

        $file =
            $application->{$document};


        if (!$file) {

            abort(
                404,
                'No document was uploaded.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE DATABASE PATH
        |--------------------------------------------------------------------------
        */

        $file =
            ltrim(
                $file,
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | ACTUAL FILE PATH
        |--------------------------------------------------------------------------
        */

        $fullPath =
            public_path(
                'document/' . $file
            );


        /*
        |--------------------------------------------------------------------------
        | CHECK FILE EXISTS
        |--------------------------------------------------------------------------
        */

        if (!is_file($fullPath)) {

            Log::error(
                'MEMBER DOCUMENT FILE NOT FOUND',
                [
                    'member_profile_id' =>
                        $application->id,

                    'document' =>
                        $document,

                    'database_path' =>
                        $file,

                    'expected_path' =>
                        $fullPath,
                ]
            );


            abort(
                404,
                'The uploaded document file could not be found on the server.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN DOCUMENT
        |--------------------------------------------------------------------------
        */

        return response()->file(
            $fullPath
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE CARD NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateCardNumber(): string
    {
        $lastCard =
            MembershipCard::latest('id')
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


        return $stateCodes[
            $normalizedState
        ] ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE RENEWAL DEBIT
    |--------------------------------------------------------------------------
    |
    | Temporary manual admin action for client presentation.
    |
    | This does NOT renew the membership.
    |
    | It only creates the financial debit that the member must pay
    | to renew the expired membership.
    |
    */

    public function generateRenewalDebit(
        $id,
        TransactionService $transactionService
    ) {
        /*
        |--------------------------------------------------------------------------
        | GET MEMBER APPLICATION
        |--------------------------------------------------------------------------
        */

        $application = MemberProfile::with([
            'user',
            'membership',
            'membershipCategory',
        ])
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP MUST EXIST
        |--------------------------------------------------------------------------
        */

        if (!$application->membership) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'No membership record was found for this member.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP MUST BE EXPIRED
        |--------------------------------------------------------------------------
        |
        | We check the actual expiration date rather than relying only
        | on the membership status.
        |
        */

        if (
            !$application->membership->expires_at ||
            !today()->gt(
                $application->membership->expires_at
            )
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Renewal debit can only be generated for an expired membership.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE RENEWAL DEBIT
        |--------------------------------------------------------------------------
        */

        $transaction =
            $transactionService
                ->createRenewalDebitIfExpired(
                    $application->user_id
                );


        /*
        |--------------------------------------------------------------------------
        | FAILED
        |--------------------------------------------------------------------------
        */

        if (!$transaction) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'A renewal debit could not be generated.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->back()
            ->with(
                'success',
                'Renewal debit generated successfully.'
            );
    }
}
