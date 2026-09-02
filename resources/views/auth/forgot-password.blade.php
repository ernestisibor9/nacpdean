<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Forgot Password | NACPDEAN</title>

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f8f6;
            min-height: 100vh;
            color: #1f2937;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /*
        |--------------------------------------------------------------------------
        | LEFT SIDE
        |--------------------------------------------------------------------------
        */

        .login-left {
            width: 50%;
            background: linear-gradient(
                145deg,
                #064e3b 0%,
                #047857 50%,
                #059669 100%
            );

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 60px;

            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: "";
            position: absolute;

            width: 450px;
            height: 450px;

            border-radius: 50%;

            background: rgba(255,255,255,0.05);

            top: -180px;
            right: -180px;
        }

        .login-left::after {
            content: "";
            position: absolute;

            width: 350px;
            height: 350px;

            border-radius: 50%;

            background: rgba(255,255,255,0.04);

            bottom: -150px;
            left: -150px;
        }

        .branding {
            position: relative;
            z-index: 2;

            max-width: 520px;

            color: white;
        }

        .brand-logo {
            width: 160px;
            height: 90px;

            background: white;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 30px;

            box-shadow:
                0 15px 40px rgba(0,0,0,0.15);
        }

        .brand-logo span {
            font-size: 30px;
            font-weight: 800;
            color: #047857;
        }

        .branding h1 {
            font-size: 42px;
            line-height: 1.15;

            font-weight: 800;

            margin-bottom: 20px;
        }

        .branding p {
            font-size: 17px;
            line-height: 1.7;

            color: rgba(255,255,255,0.85);

            margin-bottom: 35px;
        }

        .benefits {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .benefit {
            display: flex;
            align-items: center;

            font-size: 14px;

            color: rgba(255,255,255,0.95);
        }

        .benefit-icon {
            width: 28px;
            height: 28px;

            border-radius: 50%;

            background: rgba(255,255,255,0.15);

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 12px;

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | RIGHT SIDE
        |--------------------------------------------------------------------------
        */

        .login-right {
            width: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 40px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .login-header {
            margin-bottom: 35px;
        }

        .login-header h2 {
            font-size: 32px;

            font-weight: 800;

            color: #111827;

            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 15px;

            color: #6b7280;

            line-height: 1.6;
        }


        /*
        |--------------------------------------------------------------------------
        | ALERTS
        |--------------------------------------------------------------------------
        */

        .alert {
            padding: 13px 16px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .alert-error {
            background: #fef2f2;

            border: 1px solid #fecaca;

            color: #b91c1c;
        }

        .alert-success {
            background: #ecfdf5;

            border: 1px solid #a7f3d0;

            color: #047857;
        }


        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        .form-group {
            margin-bottom: 22px;
        }

        .form-label {
            display: block;

            font-size: 14px;

            font-weight: 600;

            color: #374151;

            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;

            height: 52px;

            border: 1px solid #d1d5db;

            border-radius: 10px;

            padding: 0 16px;

            font-size: 15px;

            font-family: inherit;

            background: white;

            color: #111827;

            outline: none;

            transition: 0.2s;
        }

        .form-input:focus {
            border-color: #059669;

            box-shadow:
                0 0 0 3px rgba(5,150,105,0.12);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .error-message {
            color: #dc2626;

            font-size: 13px;

            margin-top: 6px;
        }


        /*
        |--------------------------------------------------------------------------
        | SENT STATE
        |--------------------------------------------------------------------------
        */

        .sent-state {
            background: #ecfdf5;

            border: 1px solid #a7f3d0;

            border-radius: 14px;

            padding: 32px 28px;

            text-align: center;
        }

        .sent-icon {
            width: 56px;
            height: 56px;

            border-radius: 50%;

            background: #047857;

            color: white;

            font-size: 26px;
            font-weight: 700;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 18px;

            box-shadow:
                0 8px 20px rgba(4,120,87,0.25);
        }

        .sent-title {
            font-size: 20px;

            font-weight: 800;

            color: #065f46;

            margin-bottom: 10px;
        }

        .sent-text {
            font-size: 14px;

            line-height: 1.6;

            color: #047857;

            margin-bottom: 14px;
        }

        .sent-subtext {
            font-size: 13px;

            line-height: 1.6;

            color: #6b7280;
        }

        .resend-link {
            color: #047857;

            font-weight: 700;

            text-decoration: none;
        }

        .resend-link:hover {
            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | BUTTON
        |--------------------------------------------------------------------------
        */

        .login-button {
            width: 100%;

            height: 52px;

            border: none;

            border-radius: 10px;

            background: #047857;

            color: white;

            font-family: inherit;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;

            box-shadow:
                0 8px 20px rgba(4,120,87,0.18);
        }

        .login-button:hover {
            background: #065f46;

            transform: translateY(-1px);

            box-shadow:
                0 10px 25px rgba(4,120,87,0.25);
        }


        /*
        |--------------------------------------------------------------------------
        | REGISTER / BACK TO LOGIN
        |--------------------------------------------------------------------------
        */

        .register-text {
            text-align: center;

            margin-top: 28px;

            font-size: 14px;

            color: #6b7280;
        }

        .register-text a {
            color: #047857;

            font-weight: 700;

            text-decoration: none;
        }

        .register-text a:hover {
            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .login-footer {
            text-align: center;

            margin-top: 45px;

            font-size: 12px;

            color: #9ca3af;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            .login-left {
                display: none;
            }

            .login-right {
                width: 100%;

                padding: 30px 20px;
            }

            .login-container {
                max-width: 480px;
            }

            .login-header h2 {
                font-size: 28px;
            }

        }


        @media (max-width: 480px) {

            .login-right {
                padding: 25px 18px;
            }

            .login-header {
                margin-bottom: 28px;
            }

            .login-header h2 {
                font-size: 26px;
            }

        }

    </style>

</head>


<body>


<div class="login-wrapper">


    <!-- =========================================================
         LEFT BRANDING
    ========================================================== -->

    <div class="login-left">

        <div class="branding">


            <div class="brand-logo">

                {{-- Replace this with the actual NACPDEAN logo later --}}

                <span>
                    <img src="{{ asset('frontend/assets/img/logo.png') }}" alt="NACPDEAN Logo">
                </span>

            </div>


            <h1>
                Forgot Your Password?
            </h1>


            <p>
                No problem. Enter the email address linked
                to your NACPDEAN membership account and
                we'll send you a link to reset it.
            </p>


            <div class="benefits">

                <div class="benefit">

                    <div class="benefit-icon">
                        ✓
                    </div>

                    Secure member portal

                </div>


                <div class="benefit">

                    <div class="benefit-icon">
                        ✓
                    </div>

                    Reset your password in minutes

                </div>


                <div class="benefit">

                    <div class="benefit-icon">
                        ✓
                    </div>

                    Regain access to your account

                </div>

            </div>

        </div>

    </div>



    <!-- =========================================================
         RIGHT FORGOT PASSWORD FORM
    ========================================================== -->

    <div class="login-right">

        <div class="login-container">


            <!-- HEADER -->

            <div class="login-header">

                <h2>
                    Reset password
                </h2>

                <p>
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>

            </div>



            <!-- GENERAL ERROR -->

            @if (session('error'))

                <div class="alert alert-error">

                    {{ session('error') }}

                </div>

            @endif



            @if (session('status'))

                <!-- =========================================================
                     SUCCESS STATE — email sent, hide the form
                ========================================================== -->

                <div class="sent-state">

                    <div class="sent-icon">
                        ✓
                    </div>

                    <h3 class="sent-title">
                        Check your inbox
                    </h3>

                    <p class="sent-text">
                        {{ session('status') }}
                    </p>

                    <p class="sent-subtext">
                        Didn't get the email? Check your spam folder,
                        or
                        <a
                            href="{{ route('password.request') }}"
                            class="resend-link">

                            try another email address

                        </a>.
                    </p>

                </div>

            @else

                <!-- =========================================================
                     FORGOT PASSWORD FORM
                ========================================================== -->

                <form method="POST"
                      action="{{ route('password.email') }}">

                    @csrf


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label">

                            Email Address

                        </label>


                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required
                            autofocus
                            autocomplete="username">


                        @error('email')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>



                    <!-- SEND RESET LINK BUTTON -->

                    <button
                        type="submit"
                        class="login-button">

                        {{ __('Email Password Reset Link') }}

                    </button>

                </form>

            @endif



            <!-- BACK TO LOGIN -->

            <div class="register-text">

                Remembered your password?

                <a href="{{ route('login') }}">

                    Back to Sign In

                </a>

            </div>



            <!-- FOOTER -->

            <div class="login-footer">

                © {{ date('Y') }} NACPDEAN.
                All rights reserved.

            </div>


        </div>

    </div>

</div>


</body>

</html>
