<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        return view('auth.register');
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER USER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
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
        | GENERATE OTP
        |--------------------------------------------------------------------------
        */

        $otp = (string) random_int(100000, 999999);


        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        |
        | At registration we ONLY save:
        |
        | - Membership type
        | - Email
        | - Password
        |
        | Category will be selected later during payment.
        |
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

            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP TYPE
            |--------------------------------------------------------------------------
            |
            | regular
            | affiliate
            |
            |--------------------------------------------------------------------------
            */

            'member_type' => $validated['member_type'],

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
            | REMOVE USER IF EMAIL FAILS
            |--------------------------------------------------------------------------
            */

            $user->delete();

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
