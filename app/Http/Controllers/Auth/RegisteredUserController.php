<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MembershipCategory;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTRATION FORM
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD ACTIVE MEMBERSHIP CATEGORIES
        |--------------------------------------------------------------------------
        |
        | Only active categories are displayed on the registration form.
        |
        */

        $categories = MembershipCategory::where(
            'status',
            true
        )
            ->with([
                'fees' => function ($query) {
                    $query->where('status', true);
                }
            ])
            ->orderBy('name')
            ->get();

        return view(
            'auth.register',
            compact('categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER USER
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        TransactionService $transactionService
    ) {

        /*
        |--------------------------------------------------------------------------
        | VALIDATE REGISTRATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'member_type' => [
                'required',
                'in:regular,affiliate',
            ],

            'membership_category_id' => [
                'required',
                'integer',
                'exists:membership_categories,id',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

        ], [

            'member_type.required' =>
                'Please select your membership type.',

            'member_type.in' =>
                'Invalid membership type selected.',

            'membership_category_id.required' =>
                'Please select a membership category.',

            'membership_category_id.exists' =>
                'The selected membership category is invalid.',

            'email.required' =>
                'Email address is required.',

            'email.email' =>
                'Please enter a valid email address.',

            'email.unique' =>
                'This email address is already registered.',

            'password.min' =>
                'Password must be at least 8 characters.',

            'password.confirmed' =>
                'Password confirmation does not match.',

        ]);


        /*
        |--------------------------------------------------------------------------
        | FIND SELECTED MEMBERSHIP CATEGORY
        |--------------------------------------------------------------------------
        |
        | We NEVER trust the category name or amount coming from
        | the frontend.
        |
        | The category and fee are retrieved from the database.
        |
        */

        $category = MembershipCategory::where(
            'id',
            $validated['membership_category_id']
        )
            ->where(
                'status',
                true
            )
            ->first();


        if (!$category) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected membership category is no longer available.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY MEMBER TYPE AGAINST CATEGORY
        |--------------------------------------------------------------------------
        |
        | A regular member should not be able to register against
        | an affiliate category and vice versa.
        |
        | If your membership_categories table uses member_type,
        | this check protects the registration process.
        |
        */

        if (
            isset($category->member_type) &&
            $category->member_type !== $validated['member_type']
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected membership category does not match your membership type.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | AFFILIATE VALIDATION
        |--------------------------------------------------------------------------
        |
        | Affiliate registration is specifically for RCG.
        |
        | If the affiliate membership category is identified by
        | code = RCG, make sure that is the category selected.
        |
        */

        if (
            $validated['member_type'] === 'affiliate' &&
            strtoupper($category->code) !== 'RCG'
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Affiliate members must select the RCG membership category.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE OTP
        |--------------------------------------------------------------------------
        */

        $otp = (string) random_int(
            100000,
            999999
        );


        /*
        |--------------------------------------------------------------------------
        | CREATE USER + MEMBERSHIP DEBIT
        |--------------------------------------------------------------------------
        |
        | Everything inside this transaction succeeds or fails together.
        |
        | User
        | +
        | OTP
        | +
        | Membership category
        | +
        | Membership debit
        |
        */

        try {

            $user = DB::transaction(
                function () use (
                    $validated,
                    $otp,
                    $category,
                    $transactionService
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE USER
                    |--------------------------------------------------------------------------
                    */

                    $user = User::create([

                        'name' => null,

                        'email' => strtolower(
                            trim($validated['email'])
                        ),

                        'password' => Hash::make(
                            $validated['password']
                        ),

                        'member_type' =>
                            $validated['member_type'],

                        /*
                        |--------------------------------------------------------------------------
                        | SAVE MEMBERSHIP CATEGORY
                        |--------------------------------------------------------------------------
                        |
                        | This is the important addition.
                        |
                        | The category selected during registration is now
                        | permanently stored against the user.
                        |
                        */

                        'membership_category_id' =>
                            $validated['membership_category_id'],

                        'role' => 'member',

                        'user_type' => 'member',

                        'status' => 1,

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | SAVE OTP
                    |--------------------------------------------------------------------------
                    */

                    $user->otp = $otp;

                    $user->otp_expires_at =
                        now()->addMinutes(10);

                    $user->save();


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE MEMBERSHIP DEBIT
                    |--------------------------------------------------------------------------
                    |
                    | There is NO registration payment item.
                    |
                    | The debit is created from the selected
                    | membership category and its applicable fee.
                    |
                    */

                    $transactionService->createMembershipDebit(
                        $user->id,
                        $category->id,
                        false
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | RETURN USER
                    |--------------------------------------------------------------------------
                    */

                    return $user;
                }
            );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG ERROR
            |--------------------------------------------------------------------------
            */

            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'We could not complete your registration. Please try again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | STORE USER ID IN SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->put(
            'otp_user_id',
            $user->id
        );


        /*
        |--------------------------------------------------------------------------
        | SEND OTP EMAIL
        |--------------------------------------------------------------------------
        */

        try {

            Mail::send(
                'emails.otp',
                [
                    'user' => $user,
                    'otp' => $otp,
                ],
                function ($message) use ($user) {

                    $message
                        ->to($user->email)
                        ->subject(
                            'NACPDEAN Email Verification OTP'
                        );
                }
            );

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | EMAIL FAILED
            |--------------------------------------------------------------------------
            |
            | The user and debit have already been created.
            |
            | Since the registration cannot continue without OTP,
            | remove the newly created user and its transactions.
            |
            */

            report($e);


            try {

                DB::transaction(function () use ($user) {

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE TRANSACTIONS
                    |--------------------------------------------------------------------------
                    */

                    TransactionService::deleteUserTransactions(
                        $user->id
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | DELETE USER
                    |--------------------------------------------------------------------------
                    */

                    $user->delete();
                });

            } catch (\Throwable $cleanupException) {

                report($cleanupException);
            }


            /*
            |--------------------------------------------------------------------------
            | CLEAR OTP SESSION
            |--------------------------------------------------------------------------
            */

            $request->session()->forget(
                'otp_user_id'
            );


            return back()
                ->withInput()
                ->with(
                    'error',
                    'We could not send the verification email. Please try again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO OTP VERIFICATION
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('verification.otp')
            ->with(
                'success',
                'Registration successful. A 6-digit verification code has been sent to your email.'
            );
    }
}
