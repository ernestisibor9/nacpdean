<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MemberProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | RESOLVE EFFECTIVE USER
    |--------------------------------------------------------------------------
    |
    | Returns the member when an admin is filling a profile on their behalf,
    | otherwise returns the authenticated user.
    |
    | The session flag "admin_filling_profile_for_member_id" is set by
    | AdminMemberController@completeProfile().
    |
    */

    protected function resolveEffectiveUser()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN ON-BEHALF MODE
        |--------------------------------------------------------------------------
        */

        if (
            $user &&
            $user->role === 'admin' &&
            session()->has('admin_filling_profile_for_member_id')
        ) {
            $memberId = (int) session('admin_filling_profile_for_member_id');

            $member = User::where('id', $memberId)
                ->where('role', 'member')
                ->first();

            if ($member) {
                return $member;
            }

            /*
            |--------------------------------------------------------------------------
            | MEMBER GONE — CLEAR STALE FLAG
            |--------------------------------------------------------------------------
            */

            session()->forget([
                'admin_filling_profile_for_member_id',
                'admin_filling_profile_return_url',
            ]);
        }

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | IS ADMIN FILLING ON BEHALF?
    |--------------------------------------------------------------------------
    */

    protected function isAdminOnBehalf(): bool
    {
        return Auth::check()
            && Auth::user()->role === 'admin'
            && session()->has('admin_filling_profile_for_member_id');
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW PROFILE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | EFFECTIVE USER
        |--------------------------------------------------------------------------
        */

        $user = $this->resolveEffectiveUser();

        /*
        |--------------------------------------------------------------------------
        | ADMIN FILLING ON BEHALF?
        |--------------------------------------------------------------------------
        |
        | Passed to the view so the Blade can render the admin banner.
        |
        */

        $adminFillingFor = $this->isAdminOnBehalf() ? $user : null;

        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = MemberProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ]
        );

        $membershipStatus = $profile->status;

        $isApproved = $profile->status === 'approved';

        $profile->load('membershipCategory');

        if (!$profile->membership_category_id) {

            $payment = Payment::where('user_id', $user->id)
                ->where('payment_type', 'membership')
                ->where('status', 'paid')
                ->latest()
                ->first();

            if ($payment && $payment->membership_category_id) {

                $profile->membership_category_id =
                    $payment->membership_category_id;

                $profile->save();

                $profile->load('membershipCategory');
            }
        }

        /*
|--------------------------------------------------------------------------
| RENDER THE RIGHT LAYOUT
|--------------------------------------------------------------------------
|
| When an admin is filling the profile on behalf of a member,
| render the admin-layout Blade. Otherwise render the member Blade.
|
| Both blades share the same fields, forms, and validation.
| The only difference is the surrounding layout (sidebar, chrome).
|
*/

        $view = $adminFillingFor
            ? 'admin.member.profile'
            : 'member.profile.index';

        return view(
            $view,
            compact('profile', 'membershipStatus', 'isApproved', 'adminFillingFor')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE PROFILE SECTION
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | EFFECTIVE USER
        |--------------------------------------------------------------------------
        */

        $user = $this->resolveEffectiveUser();

        $profile = MemberProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'status' => 'draft',
            ]
        );

        if (in_array($profile->status, ['submitted', 'approved'])) {

            return back()->with(
                'error',
                'Your profile cannot be edited at this stage.'
            );
        }

        $payment = Payment::where('user_id', $user->id)
            ->where('payment_type', 'membership')
            ->where('status', 'paid')
            ->latest()
            ->first();

        if (!$payment) {

            return back()->with(
                'error',
                'Please complete your membership payment before updating your profile.'
            );
        }

        if (!$payment->membership_category_id) {

            return back()->with(
                'error',
                'Your membership category could not be determined from your payment.'
            );
        }

        if (
            !$profile->membership_category_id ||
            (int) $profile->membership_category_id !==
            (int) $payment->membership_category_id
        ) {

            $profile->membership_category_id =
                $payment->membership_category_id;

            $profile->save();
        }

        $section = $request->input('section');


        /*
        |--------------------------------------------------------------------------
        | PERSONAL INFORMATION
        |--------------------------------------------------------------------------
        */

        if ($section === 'personal') {

            $validated = $request->validate([

                'surname'       => 'nullable|string|max:255',
                'first_name'    => 'nullable|string|max:255',
                'middle_name'   => 'nullable|string|max:255',
                'phone'         => 'nullable|string|max:255',
                'date_of_birth' => 'nullable|date',
                'gender'        => 'nullable|string|in:Male,Female',
                'nationality'   => 'nullable|string|max:255',

                'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            ]);


            /*
            |--------------------------------------------------------------------------
            | PHOTO UPLOAD
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('photo')) {

                $photo = $request->file('photo');

                $uploadPath = config('filesystems.member_uploads.photo_path');

                Log::info('Photo upload debug', [
                    'valid'       => $photo->isValid(),
                    'error_code'  => $photo->getError(),
                    'error_msg'   => $photo->getErrorMessage(),
                    'size'        => $photo->getSize(),
                    'upload_path' => $uploadPath,
                    'writable'    => File::exists($uploadPath)
                        ? is_writable($uploadPath)
                        : false,
                    'exists'      => File::exists($uploadPath),
                ]);

                if (!File::exists($uploadPath)) {

                    File::makeDirectory($uploadPath, 0755, true);
                }

                if ($profile->photo) {

                    $oldPhoto =
                        $uploadPath .
                        DIRECTORY_SEPARATOR .
                        $profile->photo;

                    if (File::exists($oldPhoto)) {

                        File::delete($oldPhoto);
                    }
                }

                $extension = strtolower(
                    $photo->getClientOriginalExtension()
                );

                $filename =
                    'member_' .
                    $user->id .
                    '_' .
                    Str::random(20) .
                    '.' .
                    $extension;

                $photo->move($uploadPath, $filename);

                $validated['photo'] = $filename;
            }


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

                'address' => 'nullable|string',
                'city'    => 'nullable|string|max:255',
                'state'   => 'nullable|string|max:255',
                'lga'     => 'nullable|string|max:255',

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
        | APPLICANT DOCUMENTS
        |--------------------------------------------------------------------------
        */

        if ($section === 'documents') {

            $category = $payment->membershipCategory;

            if (!$category) {

                return back()->with(
                    'error',
                    'Your membership category could not be determined.'
                );
            }

            $categoryCode = strtoupper(trim($category->code ?? ''));
            $categoryName = strtolower(trim($category->name ?? ''));

            $isExporter =
                $categoryCode === 'EXP' ||
                str_contains($categoryName, 'exporter');

            $isSupplier =
                $categoryCode === 'SLR' ||
                str_contains($categoryName, 'supplier');

            $isDealer =
                $categoryCode === 'DEA' ||
                str_contains($categoryName, 'dealer');

            $validated = $request->validate([

                'cac_certificate' =>
                'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

                'cac_particulars_of_directors' =>
                'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

                'nepc_export_license' =>
                'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            ]);

            $basePath = config('filesystems.member_uploads.document_path');

            if (!File::exists($basePath)) {

                File::makeDirectory($basePath, 0755, true);
            }


            /*
            |--------------------------------------------------------------------------
            | CAC CERTIFICATE
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cac_certificate')) {

                $file = $request->file('cac_certificate');

                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'cac_certificate';

                if (!File::exists($directory)) {

                    File::makeDirectory($directory, 0755, true);
                }

                if ($profile->cac_certificate) {

                    $oldFile = public_path(
                        'document/' . $profile->cac_certificate
                    );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $filename =
                    'member_' .
                    $user->id .
                    '_cac_' .
                    Str::random(20) .
                    '.' .
                    $extension;

                $file->move($directory, $filename);

                $validated['cac_certificate'] =
                    'member_profiles/cac_certificate/' . $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | CAC PARTICULARS OF DIRECTORS
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cac_particulars_of_directors')) {

                $file = $request->file('cac_particulars_of_directors');

                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'cac_particulars_of_directors';

                if (!File::exists($directory)) {

                    File::makeDirectory($directory, 0755, true);
                }

                if ($profile->cac_particulars_of_directors) {

                    $oldFile = public_path(
                        'document/' . $profile->cac_particulars_of_directors
                    );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $filename =
                    'member_' .
                    $user->id .
                    '_directors_' .
                    Str::random(20) .
                    '.' .
                    $extension;

                $file->move($directory, $filename);

                $validated['cac_particulars_of_directors'] =
                    'member_profiles/cac_particulars_of_directors/' .
                    $filename;
            }


            /*
            |--------------------------------------------------------------------------
            | NEPC EXPORT LICENSE
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('nepc_export_license')) {

                $file = $request->file('nepc_export_license');

                $directory =
                    $basePath .
                    DIRECTORY_SEPARATOR .
                    'nepc_export_license';

                if (!File::exists($directory)) {

                    File::makeDirectory($directory, 0755, true);
                }

                if ($profile->nepc_export_license) {

                    $oldFile = public_path(
                        'document/' . $profile->nepc_export_license
                    );

                    if (File::exists($oldFile)) {

                        File::delete($oldFile);
                    }
                }

                $extension = strtolower(
                    $file->getClientOriginalExtension()
                );

                $filename =
                    'member_' .
                    $user->id .
                    '_nepc_' .
                    Str::random(20) .
                    '.' .
                    $extension;

                $file->move($directory, $filename);

                $validated['nepc_export_license'] =
                    'member_profiles/nepc_export_license/' . $filename;
            }


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
        /*
        |--------------------------------------------------------------------------
        | EFFECTIVE USER
        |--------------------------------------------------------------------------
        */

        $user = $this->resolveEffectiveUser();

        $isAdminOnBehalf = $this->isAdminOnBehalf();

        $profile = MemberProfile::where('user_id', $user->id)->first();

        if (!$profile) {

            return redirect()
                ->route('member.profile')
                ->with(
                    'error',
                    'Please complete your profile first.'
                );
        }

        if ($profile->status === 'approved') {

            return redirect()
                ->route('member.application.status')
                ->with(
                    'error',
                    'Your application has already been approved.'
                );
        }

        if ($profile->status === 'submitted') {

            return redirect()
                ->route('member.application.status')
                ->with(
                    'error',
                    'Your application is already awaiting approval.'
                );
        }

        $errors = [];

        if (empty($profile->surname)) {
            $errors[] = 'Surname is required.';
        }

        if (empty($profile->first_name)) {
            $errors[] = 'First name is required.';
        }

        if (empty($profile->phone)) {
            $errors[] = 'Phone number is required.';
        }

        if (empty($profile->date_of_birth)) {
            $errors[] = 'Date of birth is required.';
        }

        if (empty($profile->gender)) {
            $errors[] = 'Gender is required.';
        }

        if (empty($profile->nationality)) {
            $errors[] = 'Nationality is required.';
        }

        if (empty($profile->photo)) {
            $errors[] = 'Passport photograph is required.';
        }

        if (empty($profile->address)) {
            $errors[] = 'Residential address is required.';
        }

        if (empty($profile->city)) {
            $errors[] = 'City is required.';
        }

        if (empty($profile->state)) {
            $errors[] = 'State is required.';
        }

        if (empty($profile->lga)) {
            $errors[] = 'LGA is required.';
        }

        if (empty($profile->business_name)) {
            $errors[] = 'Business name is required.';
        }

        if (empty($profile->business_type)) {
            $errors[] = 'Business type is required.';
        }

        if (empty($profile->business_address)) {
            $errors[] = 'Business address is required.';
        }

        $profile->load('membershipCategory');

        $category = $profile->membershipCategory;

        if (!$category) {

            $errors[] =
                'Membership category could not be determined.';
        } else {

            $categoryCode = strtoupper(trim($category->code ?? ''));
            $categoryName = strtolower(trim($category->name ?? ''));

            $isExporter =
                $categoryCode === 'EXP' ||
                str_contains($categoryName, 'exporter');

            $isSupplier =
                $categoryCode === 'SLR' ||
                str_contains($categoryName, 'supplier');

            $isDealer =
                $categoryCode === 'DEA' ||
                str_contains($categoryName, 'dealer');

            if ($isExporter) {

                if (empty($profile->cac_certificate)) {

                    $errors[] =
                        'CAC Certificate is required for Exporters.';
                }

                if (empty($profile->cac_particulars_of_directors)) {

                    $errors[] =
                        'CAC Particulars of Directors is required for Exporters.';
                }

                if (empty($profile->nepc_export_license)) {

                    $errors[] =
                        'NEPC (Export) License is required for Exporters.';
                }
            }

            if ($isSupplier) {
                // No compulsory document validation.
            }

            if ($isDealer) {
                // No compulsory document validation.
            }
        }

        if (!empty($errors)) {

            return redirect()
                ->route('member.profile')
                ->withErrors($errors);
        }

        $profile->update([

            'status'           => 'submitted',
            'submitted_at'     => now(),
            'approved_at'      => null,
            'rejection_reason' => null,
            'admin_comment'    => null,

        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN ON-BEHALF RETURN
        |--------------------------------------------------------------------------
        |
        | If the profile was submitted by an admin on behalf of a member,
        | clear the session flag and return the admin to their member list.
        |
        */

        if ($isAdminOnBehalf) {

            $adminReturnUrl = session(
                'admin_filling_profile_return_url',
                route('admin.member.index')
            );

            session()->forget([
                'admin_filling_profile_for_member_id',
                'admin_filling_profile_return_url',
            ]);

            return redirect($adminReturnUrl)
                ->with(
                    'success',
                    'Profile submitted successfully on behalf of the member.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MEMBER RETURN
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

        $profile = MemberProfile::where('user_id', $user->id)->first();

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
