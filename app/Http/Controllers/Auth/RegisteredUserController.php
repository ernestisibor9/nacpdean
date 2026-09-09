<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MembershipCategory;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = MembershipCategory::query()
            ->where('status', true)
            ->with([
                'fees' => function ($query) {
                    $query->where('status', true);
                },
            ])
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('categories'));
    }


    /**
     * Handle an incoming registration request.
     */
    public function store(
        Request $request,
        TransactionService $transactionService
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE REGISTRATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | MEMBER TYPE
            |--------------------------------------------------------------------------
            */

            'member_type' => [
                'required',
                'string',
                'in:regular,affiliate',
            ],


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP CATEGORY
            |--------------------------------------------------------------------------
            */

            'membership_category_id' => [
                'required',
                'integer',
                'exists:membership_categories,id',
            ],


            /*
            |--------------------------------------------------------------------------
            | USERNAME
            |--------------------------------------------------------------------------
            |
            | Username is optional.
            | If supplied, it must be unique.
            |
            */

            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'unique:users,username',
            ],


            /*
            |--------------------------------------------------------------------------
            | PHONE
            |--------------------------------------------------------------------------
            |
            | Phone number is optional.
            | If supplied, it must be unique.
            |
            */

            'phone' => [
                'nullable',
                'string',
                'max:30',
                'unique:users,phone',
            ],


            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            |
            | Email is optional.
            | If supplied, it must be valid and unique.
            |
            */

            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],


            /*
            |--------------------------------------------------------------------------
            | PASSWORD
            |--------------------------------------------------------------------------
            */

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE INPUT
        |--------------------------------------------------------------------------
        */

        $username = !empty($validated['username'])
            ? strtolower(trim($validated['username']))
            : null;

        $email = !empty($validated['email'])
            ? strtolower(trim($validated['email']))
            : null;

        $phone = !empty($validated['phone'])
            ? trim($validated['phone'])
            : null;


        /*
        |--------------------------------------------------------------------------
        | ENSURE AT LEAST ONE LOGIN IDENTIFIER EXISTS
        |--------------------------------------------------------------------------
        |
        | Email, username and phone are individually optional.
        |
        | However, at least ONE must be provided because the user needs
        | at least one identifier to log into the system.
        |
        */

        if (
            empty($username) &&
            empty($email) &&
            empty($phone)
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' =>
                        'Please provide at least an email address, username, or phone number.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK NORMALIZED USERNAME
        |--------------------------------------------------------------------------
        */

        if (
            !empty($username) &&
            User::where('username', $username)->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'username' =>
                        'This username is already taken.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK NORMALIZED EMAIL
        |--------------------------------------------------------------------------
        */

        if (
            !empty($email) &&
            User::where('email', $email)->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' =>
                        'This email address is already registered.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK NORMALIZED PHONE
        |--------------------------------------------------------------------------
        */

        if (
            !empty($phone) &&
            User::where('phone', $phone)->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone' =>
                        'This phone number is already registered.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | FIND MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        */

        $category = MembershipCategory::query()
            ->where('id', $validated['membership_category_id'])
            ->where('status', true)
            ->with([
                'fees' => function ($query) {
                    $query->where('status', true);
                },
            ])
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CATEGORY NOT AVAILABLE
        |--------------------------------------------------------------------------
        */

        if (!$category) {
            return back()
                ->withInput()
                ->withErrors([
                    'membership_category_id' =>
                        'The selected membership category is not available.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE CATEGORY MEMBER TYPE
        |--------------------------------------------------------------------------
        */

        if (
            !empty($category->member_type) &&
            $category->member_type !== $validated['member_type']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'membership_category_id' =>
                        'The selected membership category is not available for the selected member type.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | AFFILIATE RESTRICTION
        |--------------------------------------------------------------------------
        |
        | Affiliate members can only register under RCG.
        |
        */

        if (
            $validated['member_type'] === 'affiliate' &&
            strtoupper($category->code ?? '') !== 'RCG'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'membership_category_id' =>
                        'Affiliate members can only register under the RCG membership category.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE USER + MEMBERSHIP DEBIT
        |--------------------------------------------------------------------------
        |
        | Both operations happen inside the same database transaction.
        |
        | This preserves your existing ledger process.
        |
        */

        try {

            DB::transaction(function () use (
                $validated,
                $username,
                $email,
                $phone,
                $category,
                $transactionService,
                &$user
            ) {

                /*
                |--------------------------------------------------------------------------
                | CREATE USER
                |--------------------------------------------------------------------------
                */

                $user = User::create([
                    'name' => null,

                    'username' => $username,

                    'email' => $email,

                    'phone' => $phone,

                    'password' => Hash::make(
                        $validated['password']
                    ),

                    'member_type' =>
                        $validated['member_type'],

                    'membership_category_id' =>
                        $category->id,

                    'role' => 'member',

                    'user_type' => 'member',

                    'status' => 1,
                ]);


                /*
                |--------------------------------------------------------------------------
                | CREATE MEMBERSHIP DEBIT
                |--------------------------------------------------------------------------
                |
                | false = NOT PAID
                |
                */

                $transactionService->createMembershipDebit(
                    $user->id,
                    $category->id,
                    false
                );
            });

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG ERROR
            |--------------------------------------------------------------------------
            */

            report($e);


            /*
            |--------------------------------------------------------------------------
            | RETURN TO REGISTRATION
            |--------------------------------------------------------------------------
            */

            return back()
                ->withInput()
                ->withErrors([
                    'email' =>
                        'Registration could not be completed. Please try again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | REGISTRATION SUCCESSFUL
        |--------------------------------------------------------------------------
        |
        | NO OTP
        | NO EMAIL VERIFICATION
        | NO OTP EMAIL
        |
        */

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Registration successful. You can now log in with your email, username, or phone number and password.'
            );
    }
}
