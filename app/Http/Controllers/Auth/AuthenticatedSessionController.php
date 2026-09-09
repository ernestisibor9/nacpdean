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

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | SAFETY CHECK
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            Auth::logout();

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Unable to authenticate your account.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK ACCOUNT STATUS
        |--------------------------------------------------------------------------
        */

        if ((int) $user->status !== 1) {

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your account is inactive. Please contact the administrator.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN LOGIN
        |--------------------------------------------------------------------------
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
        | Members no longer require:
        |
        | - Email verification
        | - OTP verification
        | - email_verified_at
        | - otp_user_id session
        |
        | A valid username/email/phone + password is enough.
        |
        */

        if ($user->role === 'member') {

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

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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

        return redirect()
            ->route('login');
    }
}
