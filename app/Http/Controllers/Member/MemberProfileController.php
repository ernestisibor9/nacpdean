<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MemberProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW PROFILE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        $profile = MemberProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP STATUS
        |--------------------------------------------------------------------------
        */

        $membershipStatus = $profile->status;

        /*
        |--------------------------------------------------------------------------
        | CHECK APPROVAL
        |--------------------------------------------------------------------------
        */

        $isApproved = $profile->status === 'approved';

        /*
        |--------------------------------------------------------------------------
        | LOAD MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

        $profile->load('membershipCategory');

        /*
        |--------------------------------------------------------------------------
        | IF PROFILE DOES NOT HAVE CATEGORY
        |--------------------------------------------------------------------------
        |
        | Get category from the member's successful membership payment.
        |
        */

        if (!$profile->membership_category_id) {

            $payment = Payment::where(
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
                ->latest()
                ->first();

            if (
                $payment &&
                $payment->membership_category_id
            ) {

                $profile->membership_category_id =
                    $payment->membership_category_id;

                $profile->save();

                $profile->load('membershipCategory');
            }
        }

        return view(
            'member.profile.index',
            compact(
                'profile',
                'membershipStatus',
                'isApproved'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE PROFILE SECTION
    |--------------------------------------------------------------------------
    |
    | Sections:
    |
    | personal
    | residential
    | business
    | documents
    |
    */

    public function update(Request $request)
    {
        $user = Auth::user();

        $profile = MemberProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | DO NOT ALLOW EDITING AFTER SUBMISSION
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $profile->status,
                [
                    'submitted',
                    'approved',
                ]
            )
        ) {

            return back()->with(
                'error',
                'Your profile cannot be edited at this stage.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET SUCCESSFUL MEMBERSHIP PAYMENT
        |--------------------------------------------------------------------------
        */

        $payment = Payment::where(
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
            ->latest()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP PAYMENT MUST EXIST
        |--------------------------------------------------------------------------
        */

        if (!$payment) {

            return back()->with(
                'error',
                'Please complete your membership payment before updating your profile.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP CATEGORY MUST EXIST
        |--------------------------------------------------------------------------
        */

        if (!$payment->membership_category_id) {

            return back()->with(
                'error',
                'Your membership category could not be determined from your payment.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAVE MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

        if (
            !$profile->membership_category_id ||
            (int) $profile->membership_category_id !==
            (int) $payment->membership_category_id
        ) {

            $profile->membership_category_id =
                $payment->membership_category_id;

            $profile->save();
        }


        /*
        |--------------------------------------------------------------------------
        | GET SECTION
        |--------------------------------------------------------------------------
        */

        $section = $request->input('section');


        /*
        |--------------------------------------------------------------------------
        | PERSONAL INFORMATION
        |--------------------------------------------------------------------------
        */

        if ($section === 'personal') {

            $validated = $request->validate([

                'surname' =>
                    'nullable|string|max:255',

                'first_name' =>
                    'nullable|string|max:255',

                'middle_name' =>
                    'nullable|string|max:255',

                'phone' =>
                    'nullable|string|max:255',

                'date_of_birth' =>
                    'nullable|date',

                'gender' =>
                    'nullable|string|in:Male,Female',

                'nationality' =>
                    'nullable|string|max:255',

                'photo' =>
                    'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            ]);


            /*
            |--------------------------------------------------------------------------
            | PHOTO UPLOAD
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                $uploadPath =
                    public_path('uploads/member_profiles');


                /*
                |--------------------------------------------------------------------------
                | CREATE DIRECTORY
                |--------------------------------------------------------------------------
                */

                if (!File::exists($uploadPath)) {

                    File::makeDirectory(
                        $uploadPath,
                        0755,
                        true
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DELETE OLD PHOTO
                |--------------------------------------------------------------------------
                */

                if ($profile->photo) {

                    $oldPhoto =
                        $uploadPath .
                        DIRECTORY_SEPARATOR .
                        $profile->photo;

                    if (File::exists($oldPhoto)) {

                        File::delete($oldPhoto);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | GENERATE UNIQUE FILE NAME
                |--------------------------------------------------------------------------
                */

                $extension =
                    strtolower(
                        $photo->getClientOriginalExtension()
                    );

                $filename =
                    'member_' .
                    $user->id .
                    '_' .
                    Str::random(20) .
                    '.' .
                    $extension;


                /*
                |--------------------------------------------------------------------------
                | MOVE PHOTO
                |--------------------------------------------------------------------------
                */

                $photo->move(
                    $uploadPath,
                    $filename
                );


                $validated['photo'] =
                    $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE PERSONAL INFORMATION
            |--------------------------------------------------------------------------
            */

            $profile->update(
                array_merge(
                    $validated,
                    [
                        'membership_category_id' =>
                            $payment->membership_category_id,
                    ]
                )
            );


            return back()->with(
                'success',
                'Personal information saved successfully.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RESIDENTIAL ADDRESS
        |--------------------------------------------------------------------------
        */

        if ($section === 'residential') {

            $validated = $request->validate([

                'address' =>
                    'nullable|string',

                'city' =>
                    'nullable|string|max:255',

                'state' =>
                    'nullable|string|max:255',

                'lga' =>
                    'nullable|string|max:255',

            ]);


            /*
            |--------------------------------------------------------------------------
            | SAVE RESIDENTIAL ADDRESS
            |--------------------------------------------------------------------------
            */

            $profile->update(
                array_merge(
                    $validated,
                    [
                        'membership_category_id' =>
                            $payment->membership_category_id,
                    ]
                )
            );


            return back()->with(
                'success',
                'Residential address saved successfully.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        if ($section === 'business') {

            $validated = $request->validate([

                'business_name' =>
                    'nullable|string|max:255',

                'business_registration_number' =>
                    'nullable|string|max:255',

                'business_type' =>
                    'nullable|string|max:255',

                'business_address' =>
                    'nullable|string',

            ]);


            /*
            |--------------------------------------------------------------------------
            | SAVE BUSINESS INFORMATION
            |--------------------------------------------------------------------------
            */

            $profile->update(
                array_merge(
                    $validated,
                    [
                        'membership_category_id' =>
                            $payment->membership_category_id,
                    ]
                )
            );


            return back()->with(
                'success',
                'Business information saved successfully.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | APPLICANT DOCUMENTS
        |--------------------------------------------------------------------------
        */

        if ($section === 'documents') {

            /*
            |--------------------------------------------------------------------------
            | LOAD MEMBERSHIP CATEGORY
            |--------------------------------------------------------------------------
            */

            $category =
                $payment->membershipCategory;


            if (!$category) {

                return back()->with(
                    'error',
                    'Your membership category could not be determined.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CATEGORY CODE
            |--------------------------------------------------------------------------
            |
            | Current database:
            |
            | EXP = Exporter
            | SLR = Supplier
            | DEA = Dealer
            | PRD = Producer
            | RCG = Affiliate
            |
            */

            $categoryCode =
                strtoupper(
                    trim(
                        $category->code ?? ''
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | CATEGORY NAME
            |--------------------------------------------------------------------------
            */

            $categoryName =
                strtolower(
                    trim(
                        $category->name ?? ''
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | DETERMINE CATEGORY
            |--------------------------------------------------------------------------
            */

            $isExporter =
                $categoryCode === 'EXP' ||
                str_contains(
                    $categoryName,
                    'exporter'
                );


            $isSupplier =
                $categoryCode === 'SLR' ||
                str_contains(
                    $categoryName,
                    'supplier'
                );


            $isDealer =
                $categoryCode === 'DEA' ||
                str_contains(
                    $categoryName,
                    'dealer'
                );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE UPLOADS
            |--------------------------------------------------------------------------
            |
            | All documents are nullable here because documents are saved
            | individually.
            |
            | Exporter requirements are enforced during final submission.
            |
            */

            $validated = $request->validate([

                'cac_certificate' =>
                    'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

                'cac_particulars_of_directors' =>
                    'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

                'nepc_export_license' =>
                    'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            ]);


            /*
            |--------------------------------------------------------------------------
            | BASE DOCUMENT DIRECTORY
            |--------------------------------------------------------------------------
            */

            $basePath =
                public_path(
                    'document/member_profiles'
                );


            /*
            |--------------------------------------------------------------------------
            | CREATE BASE DIRECTORY
            |--------------------------------------------------------------------------
            */

            if (!File::exists($basePath)) {

                File::makeDirectory(
                    $basePath,
                    0755,
                    true
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CAC CERTIFICATE
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cac_certificate')) {

                $file =
                    $request->file(
                        'cac_certificate'
                    );


                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'cac_certificate';


                if (!File::exists($directory)) {

                    File::makeDirectory(
                        $directory,
                        0755,
                        true
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DELETE OLD CAC CERTIFICATE
                |--------------------------------------------------------------------------
                */

                if ($profile->cac_certificate) {

                    $oldFile =
                        public_path(
                            'document/' .
                            $profile->cac_certificate
                        );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | GENERATE UNIQUE FILE NAME
                |--------------------------------------------------------------------------
                */

                $extension =
                    strtolower(
                        $file->getClientOriginalExtension()
                    );

                $filename =
                    'member_' .
                    $user->id .
                    '_cac_' .
                    Str::random(20) .
                    '.' .
                    $extension;


                /*
                |--------------------------------------------------------------------------
                | MOVE FILE
                |--------------------------------------------------------------------------
                */

                $file->move(
                    $directory,
                    $filename
                );


                /*
                |--------------------------------------------------------------------------
                | STORE RELATIVE PATH
                |--------------------------------------------------------------------------
                */

                $validated['cac_certificate'] =
                    'member_profiles/cac_certificate/' .
                    $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | CAC PARTICULARS OF DIRECTORS
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile(
                    'cac_particulars_of_directors'
                )
            ) {

                $file =
                    $request->file(
                        'cac_particulars_of_directors'
                    );


                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'cac_particulars_of_directors';


                if (!File::exists($directory)) {

                    File::makeDirectory(
                        $directory,
                        0755,
                        true
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DELETE OLD DOCUMENT
                |--------------------------------------------------------------------------
                */

                if (
                    $profile->cac_particulars_of_directors
                ) {

                    $oldFile =
                        public_path(
                            'document/' .
                            $profile->cac_particulars_of_directors
                        );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | GENERATE UNIQUE FILE NAME
                |--------------------------------------------------------------------------
                */

                $extension =
                    strtolower(
                        $file->getClientOriginalExtension()
                    );

                $filename =
                    'member_' .
                    $user->id .
                    '_directors_' .
                    Str::random(20) .
                    '.' .
                    $extension;


                /*
                |--------------------------------------------------------------------------
                | MOVE FILE
                |--------------------------------------------------------------------------
                */

                $file->move(
                    $directory,
                    $filename
                );


                /*
                |--------------------------------------------------------------------------
                | STORE RELATIVE PATH
                |--------------------------------------------------------------------------
                */

                $validated[
                    'cac_particulars_of_directors'
                ] =
                    'member_profiles/cac_particulars_of_directors/' .
                    $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | NEPC EXPORT LICENSE
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile(
                    'nepc_export_license'
                )
            ) {

                $file =
                    $request->file(
                        'nepc_export_license'
                    );


                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'nepc_export_license';


                if (!File::exists($directory)) {

                    File::makeDirectory(
                        $directory,
                        0755,
                        true
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DELETE OLD NEPC LICENSE
                |--------------------------------------------------------------------------
                */

                if (
                    $profile->nepc_export_license
                ) {

                    $oldFile =
                        public_path(
                            'document/' .
                            $profile->nepc_export_license
                        );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | GENERATE UNIQUE FILE NAME
                |--------------------------------------------------------------------------
                */

                $extension =
                    strtolower(
                        $file->getClientOriginalExtension()
                    );

                $filename =
                    'member_' .
                    $user->id .
                    '_nepc_' .
                    Str::random(20) .
                    '.' .
                    $extension;


                /*
                |--------------------------------------------------------------------------
                | MOVE FILE
                |--------------------------------------------------------------------------
                */

                $file->move(
                    $directory,
                    $filename
                );


                /*
                |--------------------------------------------------------------------------
                | STORE RELATIVE PATH
                |--------------------------------------------------------------------------
                */

                $validated[
                    'nepc_export_license'
                ] =
                    'member_profiles/nepc_export_license/' .
                    $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | SAVE DOCUMENT INFORMATION
            |--------------------------------------------------------------------------
            */

            $profile->update(
                array_merge(
                    $validated,
                    [
                        'membership_category_id' =>
                            $payment->membership_category_id,
                    ]
                )
            );


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            return back()->with(
                'success',
                'Applicant documents saved successfully.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INVALID SECTION
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'error',
            'Invalid profile section.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT APPLICATION
    |--------------------------------------------------------------------------
    */

    public function submit(Request $request)
    {
        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | GET PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | PROFILE MUST EXIST
        |--------------------------------------------------------------------------
        */

        if (!$profile) {

            return redirect()
                ->route('member.profile')
                ->with(
                    'error',
                    'Please complete your profile first.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY APPROVED
        |--------------------------------------------------------------------------
        */

        if ($profile->status === 'approved') {

            return redirect()
                ->route('member.application.status')
                ->with(
                    'error',
                    'Your application has already been approved.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY SUBMITTED
        |--------------------------------------------------------------------------
        */

        if ($profile->status === 'submitted') {

            return redirect()
                ->route('member.application.status')
                ->with(
                    'error',
                    'Your application is already awaiting approval.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE DATABASE PROFILE
        |--------------------------------------------------------------------------
        */

        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | PERSONAL INFORMATION
        |--------------------------------------------------------------------------
        */

        if (empty($profile->surname)) {

            $errors[] =
                'Surname is required.';
        }


        if (empty($profile->first_name)) {

            $errors[] =
                'First name is required.';
        }


        if (empty($profile->phone)) {

            $errors[] =
                'Phone number is required.';
        }


        if (empty($profile->date_of_birth)) {

            $errors[] =
                'Date of birth is required.';
        }


        if (empty($profile->gender)) {

            $errors[] =
                'Gender is required.';
        }


        if (empty($profile->nationality)) {

            $errors[] =
                'Nationality is required.';
        }


        if (empty($profile->photo)) {

            $errors[] =
                'Passport photograph is required.';
        }


        /*
        |--------------------------------------------------------------------------
        | RESIDENTIAL ADDRESS
        |--------------------------------------------------------------------------
        */

        if (empty($profile->address)) {

            $errors[] =
                'Residential address is required.';
        }


        if (empty($profile->city)) {

            $errors[] =
                'City is required.';
        }


        if (empty($profile->state)) {

            $errors[] =
                'State is required.';
        }


        if (empty($profile->lga)) {

            $errors[] =
                'LGA is required.';
        }


        /*
        |--------------------------------------------------------------------------
        | BUSINESS INFORMATION
        |--------------------------------------------------------------------------
        */

        if (empty($profile->business_name)) {

            $errors[] =
                'Business name is required.';
        }


        if (empty($profile->business_type)) {

            $errors[] =
                'Business type is required.';
        }


        if (empty($profile->business_address)) {

            $errors[] =
                'Business address is required.';
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

        $profile->load('membershipCategory');

        $category =
            $profile->membershipCategory;


        if (!$category) {

            $errors[] =
                'Membership category could not be determined.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | CATEGORY CODE
            |--------------------------------------------------------------------------
            */

            $categoryCode =
                strtoupper(
                    trim(
                        $category->code ?? ''
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | CATEGORY NAME
            |--------------------------------------------------------------------------
            */

            $categoryName =
                strtolower(
                    trim(
                        $category->name ?? ''
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | IDENTIFY EXPORTER
            |--------------------------------------------------------------------------
            */

            $isExporter =
                $categoryCode === 'EXP' ||
                str_contains(
                    $categoryName,
                    'exporter'
                );


            /*
            |--------------------------------------------------------------------------
            | IDENTIFY SUPPLIER
            |--------------------------------------------------------------------------
            */

            $isSupplier =
                $categoryCode === 'SLR' ||
                str_contains(
                    $categoryName,
                    'supplier'
                );


            /*
            |--------------------------------------------------------------------------
            | IDENTIFY DEALER
            |--------------------------------------------------------------------------
            */

            $isDealer =
                $categoryCode === 'DEA' ||
                str_contains(
                    $categoryName,
                    'dealer'
                );


            /*
            |--------------------------------------------------------------------------
            | EXPORTER DOCUMENT REQUIREMENTS
            |--------------------------------------------------------------------------
            |
            | Exporters MUST provide:
            |
            | 1. CAC Certificate
            | 2. CAC Particulars of Directors
            | 3. NEPC Export License
            |
            */

            if ($isExporter) {

                if (
                    empty(
                        $profile->cac_certificate
                    )
                ) {

                    $errors[] =
                        'CAC Certificate is required for Exporters.';
                }


                if (
                    empty(
                        $profile->cac_particulars_of_directors
                    )
                ) {

                    $errors[] =
                        'CAC Particulars of Directors is required for Exporters.';
                }


                if (
                    empty(
                        $profile->nepc_export_license
                    )
                ) {

                    $errors[] =
                        'NEPC (Export) License is required for Exporters.';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SUPPLIER DOCUMENTS
            |--------------------------------------------------------------------------
            |
            | Supplier documents are optional.
            |
            */

            if ($isSupplier) {

                // No compulsory document validation.
            }


            /*
            |--------------------------------------------------------------------------
            | DEALER DOCUMENTS
            |--------------------------------------------------------------------------
            |
            | Dealer documents are optional.
            |
            */

            if ($isDealer) {

                // No compulsory document validation.
            }
        }


        /*
        |--------------------------------------------------------------------------
        | STOP IF PROFILE IS INCOMPLETE
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {

            return redirect()
                ->route('member.profile')
                ->withErrors($errors);
        }


        /*
        |--------------------------------------------------------------------------
        | SUBMIT APPLICATION
        |--------------------------------------------------------------------------
        */

        $profile->update([

            'status' =>
                'submitted',

            'submitted_at' =>
                now(),

            'approved_at' =>
                null,

            'rejection_reason' =>
                null,

            'admin_comment' =>
                null,

        ]);


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('member.application.status')
            ->with(
                'success',
                'Your application has been successfully submitted for review.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | APPLICATION STATUS
    |--------------------------------------------------------------------------
    */

    public function applicationStatus()
    {
        $user = Auth::user();

        $profile = MemberProfile::where(
            'user_id',
            $user->id
        )->first();


        /*
        |--------------------------------------------------------------------------
        | PROFILE MUST EXIST
        |--------------------------------------------------------------------------
        */

        if (!$profile) {

            return redirect()
                ->route('member.profile')
                ->with(
                    'error',
                    'Please complete your profile first.'
                );
        }


        return view(
            'member.application-status',
            compact('profile')
        );
    }
}
