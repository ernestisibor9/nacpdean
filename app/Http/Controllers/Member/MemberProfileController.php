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
    |
    | Get status directly from member_profiles.
    |
    */

    $membershipStatus = $profile->status;

    /*
    |--------------------------------------------------------------------------
    | CHECK APPROVAL
    |--------------------------------------------------------------------------
    */

    $isApproved =
        $profile->status === 'approved';

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
    | Each section is saved independently.
    |
    | personal     = Personal Information + Photo
    | residential  = Residential Address
    | business     = Business Information
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

        if (in_array($profile->status, ['submitted', 'approved'])) {

            return back()->with(
                'error',
                'Your profile cannot be edited at this stage.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GET MEMBERSHIP PAYMENT
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


                /*
                |--------------------------------------------------------------------------
                | CREATE DIRECTORY
                |--------------------------------------------------------------------------
                */

                $uploadPath =
                    public_path('uploads/member_profiles');


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
                        $uploadPath . '/' . $profile->photo;


                    if (File::exists($oldPhoto)) {

                        File::delete($oldPhoto);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CREATE UNIQUE FILE NAME
                |--------------------------------------------------------------------------
                */

                $extension =
                    $photo->getClientOriginalExtension();

                $filename =
                    'member_' .
                    $user->id .
                    '_' .
                    Str::random(20) .
                    '.' .
                    strtolower($extension);


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
            |
            | membership_category_id is still taken from the verified
            | successful payment.
            |
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
        |
        | IMPORTANT:
        |
        | We are NOT validating the browser form here.
        |
        | We are checking what has actually been saved to the database.
        |
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
