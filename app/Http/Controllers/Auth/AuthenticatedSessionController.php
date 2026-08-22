<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | AUTHENTICATE USER
        |--------------------------------------------------------------------------
        */

        $request->authenticate();


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | GET AUTHENTICATED USER
        |--------------------------------------------------------------------------
        */

        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | CHECK ACCOUNT STATUS
        |--------------------------------------------------------------------------
        */

        if ($user->status != 1) {

            Auth::logout();

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account is inactive.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN LOGIN
        |--------------------------------------------------------------------------
        |
        | Admins do not need member email OTP verification.
        |
        */

        if ($user->role === 'admin') {

            return redirect()
                ->route('admin.admin_dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER LOGIN
        |--------------------------------------------------------------------------
        |
        | Members MUST verify their email before accessing
        | the member dashboard.
        |
        */

        if ($user->role === 'member') {

            /*
            |--------------------------------------------------------------------------
            | CHECK EMAIL VERIFICATION
            |--------------------------------------------------------------------------
            */

            if (!$user->email_verified_at) {

                /*
                |--------------------------------------------------------------------------
                | SAVE USER ID FOR OTP VERIFICATION
                |--------------------------------------------------------------------------
                */

                $request->session()->put(
                    'otp_user_id',
                    $user->id
                );


                /*
                |--------------------------------------------------------------------------
                | LOG USER OUT
                |--------------------------------------------------------------------------
                |
                | The user should NOT remain authenticated while
                | waiting for email verification.
                |
                */

                Auth::logout();


                /*
                |--------------------------------------------------------------------------
                | REDIRECT TO OTP PAGE
                |--------------------------------------------------------------------------
                */

                return redirect()
                    ->route('verification.otp')
                    ->with(
                        'error',
                        'Please verify your email address before accessing your dashboard.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | VERIFIED MEMBER
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('member.member_dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | USER LOGIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'user') {

            return redirect()
                ->route('user.user_dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | INVALID USER TYPE
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Invalid user type.'
            );
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
