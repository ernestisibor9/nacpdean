<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailOtpController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW OTP PAGE
    |--------------------------------------------------------------------------
    */

    public function show()
    {
        $userId = session('otp_user_id');


        /*
        |--------------------------------------------------------------------------
        | NO OTP SESSION
        |--------------------------------------------------------------------------
        */

        if (!$userId) {

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'Please register first.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GET USER
        |--------------------------------------------------------------------------
        */

        $user = User::find($userId);


        if (!$user) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'Registration session has expired. Please register again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY VERIFIED
        |--------------------------------------------------------------------------
        */

        if ($user->email_verified_at) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your email is already verified. Please login.'
                );
        }


        return view(
            'auth.verify-otp',
            compact('user')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY OTP
    |--------------------------------------------------------------------------
    */

    public function verify(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'otp' => [
                'required',
                'digits:6',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | GET USER ID FROM SESSION
        |--------------------------------------------------------------------------
        */

        $userId = session('otp_user_id');


        if (!$userId) {

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'Your verification session has expired. Please register again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GET USER
        |--------------------------------------------------------------------------
        */

        $user = User::find($userId);


        if (!$user) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'User account could not be found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY VERIFIED
        |--------------------------------------------------------------------------
        */

        if ($user->email_verified_at) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your email is already verified. Please login.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK OTP EXISTS
        |--------------------------------------------------------------------------
        */

        if (!$user->otp) {

            return back()
                ->with(
                    'error',
                    'No verification code is available. Please request a new OTP.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK OTP
        |--------------------------------------------------------------------------
        */

        if ((string) $user->otp !== (string) $request->otp) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid verification code.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK OTP EXPIRY
        |--------------------------------------------------------------------------
        */

        if (
            !$user->otp_expires_at ||
            now()->greaterThan($user->otp_expires_at)
        ) {

            return back()
                ->with(
                    'error',
                    'This verification code has expired. Please request a new OTP.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY EMAIL
        |--------------------------------------------------------------------------
        */

        $user->email_verified_at = now();

        $user->otp = null;

        $user->otp_expires_at = null;

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | CLEAR OTP SESSION
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'otp_user_id'
        );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO LOGIN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Email verified successfully. You can now login.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RESEND OTP
    |--------------------------------------------------------------------------
    */

    public function resend(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | GET USER ID
        |--------------------------------------------------------------------------
        */

        $userId = session('otp_user_id');


        if (!$userId) {

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'Your verification session has expired. Please register again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GET USER
        |--------------------------------------------------------------------------
        */

        $user = User::find($userId);


        if (!$user) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('register')
                ->with(
                    'error',
                    'User account could not be found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALREADY VERIFIED
        |--------------------------------------------------------------------------
        */

        if ($user->email_verified_at) {

            session()->forget(
                'otp_user_id'
            );

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your email is already verified. Please login.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GENERATE NEW OTP
        |--------------------------------------------------------------------------
        */

        $otp = (string) random_int(
            100000,
            999999
        );


        /*
        |--------------------------------------------------------------------------
        | SAVE NEW OTP
        |--------------------------------------------------------------------------
        */

        $user->otp = $otp;

        $user->otp_expires_at =
            now()->addMinutes(10);

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | SEND OTP
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

            return back()
                ->with(
                    'error',
                    'We could not send the verification email. Please try again.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return back()
            ->with(
                'success',
                'A new verification code has been sent to your email.'
            );
    }
}
