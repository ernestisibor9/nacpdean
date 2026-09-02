<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login | NACPDEAN</title>

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
        | PASSWORD
        |--------------------------------------------------------------------------
        */

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-input {
            padding-right: 50px;
        }

        .toggle-password {
            position: absolute;

            right: 15px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            cursor: pointer;

            color: #6b7280;

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | OPTIONS
        |--------------------------------------------------------------------------
        */

        .form-options {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;
        }

        .remember {
            display: flex;

            align-items: center;

            gap: 8px;

            font-size: 13px;

            color: #6b7280;
        }

        .remember input {
            width: 16px;
            height: 16px;

            accent-color: #059669;
        }

        .forgot-password {
            font-size: 13px;

            color: #047857;

            font-weight: 600;

            text-decoration: none;
        }

        .forgot-password:hover {
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
        | REGISTER
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

            .form-options {
                align-items: flex-start;

                gap: 10px;
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
                Welcome Back
            </h1>


            <p>
                Sign in to your NACPDEAN membership portal
                to manage your membership, applications,
                payments and member services.
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

                    Manage your membership online

                </div>


                <div class="benefit">

                    <div class="benefit-icon">
                        ✓
                    </div>

                    Access your membership services

                </div>

            </div>

        </div>

    </div>



    <!-- =========================================================
         RIGHT LOGIN FORM
    ========================================================== -->

    <div class="login-right">

        <div class="login-container">


            <!-- HEADER -->

            <div class="login-header">

                <h2>
                    Sign in
                </h2>

                <p>
                    Enter your account details to continue
                    to your dashboard.
                </p>

            </div>



            <!-- SESSION STATUS -->

            @if (session('status'))

                <div class="alert alert-success">

                    {{ session('status') }}

                </div>

            @endif



            <!-- GENERAL ERROR -->

            @if (session('error'))

                <div class="alert alert-error">

                    {{ session('error') }}

                </div>

            @endif



            <!-- LOGIN FORM -->

            <form method="POST"
                  action="{{ route('login') }}">

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



                <!-- PASSWORD -->

                <div class="form-group">

                    <label
                        for="password"
                        class="form-label">

                        Password

                    </label>


                    <div class="password-wrapper">

                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password">


                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()">

                            Show

                        </button>

                    </div>


                    @error('password')

                        <div class="error-message">

                            {{ $message }}

                        </div>

                    @enderror

                </div>



                <!-- OPTIONS -->

                <div class="form-options">



                    @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="forgot-password">

                            Forgot password?

                        </a>

                    @endif

                </div>



                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="login-button">

                    Sign In

                </button>

            </form>



            <!-- REGISTRATION -->

            @if (Route::has('register'))

                <div class="register-text">

                    Don't have an account?

                    <a href="{{ route('register') }}">

                        Create an account

                    </a>

                </div>

            @endif



            <!-- FOOTER -->

            <div class="login-footer">

                © {{ date('Y') }} NACPDEAN.
                All rights reserved.

            </div>


        </div>

    </div>

</div>



<script>

function togglePassword()
{
    const password =
        document.getElementById('password');

    const button =
        document.querySelector('.toggle-password');


    if (password.type === 'password') {

        password.type = 'text';

        button.textContent = 'Hide';

    } else {

        password.type = 'password';

        button.textContent = 'Show';

    }
}

</script>


</body>

</html>
