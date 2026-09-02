<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NACPDEAN - Create Account</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {

            font-family: 'Inter', sans-serif;

            min-height: 100vh;

            background: #f3f7f5;

            color: #1f2937;

        }

        /* =========================================================
           PAGE
        ========================================================= */

        .page {

            min-height: 100vh;

            display: flex;

        }

        /* =========================================================
           LEFT PANEL
        ========================================================= */

        .left-panel {

            width: 52%;

            min-height: 100vh;

            position: relative;

            overflow: hidden;

            display: flex;

            align-items: center;

            padding: 70px;

            background:
                linear-gradient(
                    135deg,
                    #022c22 0%,
                    #064e3b 40%,
                    #047857 75%,
                    #059669 100%
                );

            color: white;

        }

        .left-panel::before {

            content: "";

            position: absolute;

            width: 600px;

            height: 600px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.045);

            top: -280px;

            right: -220px;

        }

        .left-panel::after {

            content: "";

            position: absolute;

            width: 450px;

            height: 450px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.04);

            bottom: -230px;

            left: -180px;

        }

        .left-content {

            position: relative;

            z-index: 2;

            max-width: 560px;

        }

        /* =========================================================
           BRAND
        ========================================================= */

        .brand {

            display: flex;

            align-items: center;

            gap: 14px;

            margin-bottom: 65px;

        }

        .brand-logo {

            width: 160px;

            height: 74px;

            border-radius: 16px;

            background: white;

            display: flex;

            align-items: center;

            justify-content: center;

            box-shadow:
                0 12px 30px rgba(0, 0, 0, 0.18);

        }

        .brand-logo img {

            max-width: 140px;

            max-height: 62px;

            object-fit: contain;

        }

        .brand-name {

            font-size: 22px;

            font-weight: 800;

            letter-spacing: -0.5px;

        }

        .brand-subtitle {

            font-size: 11px;

            color: rgba(255, 255, 255, 0.65);

            margin-top: 3px;

            letter-spacing: 1px;

            text-transform: uppercase;

        }

        /* =========================================================
           HERO
        ========================================================= */

        .hero h1 {

            font-size: 48px;

            line-height: 1.12;

            font-weight: 800;

            letter-spacing: -1.5px;

            margin-bottom: 22px;

        }

        .hero h1 span {

            color: #a7f3d0;

        }

        .hero p {

            font-size: 17px;

            line-height: 1.8;

            color: rgba(255, 255, 255, 0.82);

            max-width: 500px;

            margin-bottom: 40px;

        }

        /* =========================================================
           FEATURES
        ========================================================= */

        .features {

            display: flex;

            flex-direction: column;

            gap: 17px;

        }

        .feature {

            display: flex;

            align-items: center;

            font-size: 14px;

            color: rgba(255, 255, 255, 0.92);

        }

        .feature-icon {

            width: 32px;

            height: 32px;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.12);

            border: 1px solid rgba(255, 255, 255, 0.12);

            display: flex;

            align-items: center;

            justify-content: center;

            margin-right: 13px;

            font-size: 14px;

            font-weight: 700;

        }

        /* =========================================================
           RIGHT PANEL
        ========================================================= */

        .right-panel {

            width: 48%;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 50px;

            background: #ffffff;

        }

        .register-container {

            width: 100%;

            max-width: 440px;

        }

        /* =========================================================
           FORM HEADER
        ========================================================= */

        .form-header {

            margin-bottom: 32px;

        }

        .form-header h2 {

            font-size: 32px;

            line-height: 1.2;

            font-weight: 800;

            color: #111827;

            letter-spacing: -0.8px;

            margin-bottom: 10px;

        }

        .form-header p {

            font-size: 14px;

            line-height: 1.7;

            color: #6b7280;

        }

        /* =========================================================
           ALERT
        ========================================================= */

        .alert {

            padding: 14px 16px;

            border-radius: 10px;

            margin-bottom: 22px;

            font-size: 13px;

            line-height: 1.5;

        }

        .alert-success {

            color: #047857;

            background: #ecfdf5;

            border: 1px solid #a7f3d0;

        }

        .alert-danger {

            color: #b91c1c;

            background: #fef2f2;

            border: 1px solid #fecaca;

        }

        .alert-danger ul {

            margin: 0;

            padding-left: 18px;

        }

        /* =========================================================
           FORM GROUP
        ========================================================= */

        .form-group {

            margin-bottom: 21px;

        }

        .form-label {

            display: block;

            font-size: 13px;

            font-weight: 700;

            color: #374151;

            margin-bottom: 8px;

        }

        .input-wrapper {

            position: relative;

        }

        .form-input {

            width: 100%;

            height: 54px;

            border: 1px solid #d1d5db;

            border-radius: 11px;

            padding: 0 17px;

            background: #fff;

            color: #111827;

            font-family: inherit;

            font-size: 14px;

            outline: none;

            transition: all 0.2s ease;

        }

        .form-input:hover {

            border-color: #9ca3af;

        }

        .form-input:focus {

            border-color: #059669;

            box-shadow:
                0 0 0 4px rgba(5, 150, 105, 0.10);

        }

        .form-input::placeholder {

            color: #9ca3af;

        }

        .password-input {

            padding-right: 75px;

        }

        /* =========================================================
           SHOW PASSWORD
        ========================================================= */

        .toggle-password {

            position: absolute;

            right: 14px;

            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #047857;

            font-family: inherit;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            padding: 5px;

        }

        .toggle-password:hover {

            color: #065f46;

        }

        /* =========================================================
           ERROR
        ========================================================= */

        .error-message {

            margin-top: 7px;

            color: #dc2626;

            font-size: 12px;

        }

        /* =========================================================
           PASSWORD HINT
        ========================================================= */

        .password-hint {

            margin-top: 7px;

            font-size: 11px;

            color: #9ca3af;

        }

        /* =========================================================
           MEMBERSHIP OPTIONS
        ========================================================= */

        .membership-options {

            display: flex;

            flex-direction: column;

            gap: 10px;

        }

        .membership-option {

            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding: 14px;

            border: 1px solid #d1d5db;

            border-radius: 11px;

            background: #ffffff;

            cursor: pointer;

            transition: all 0.2s ease;

        }

        .membership-option:hover {

            border-color: #059669;

            background: #f0fdf4;

        }

        .membership-option input[type="radio"] {

            width: 18px;

            height: 18px;

            margin-top: 2px;

            accent-color: #059669;

            cursor: pointer;

            flex-shrink: 0;

        }

        .membership-option-content {

            flex: 1;

            cursor: pointer;

        }

        .membership-card-title {

            font-size: 13px;

            font-weight: 800;

            color: #111827;

            margin-bottom: 3px;

        }

        .membership-card-description {

            font-size: 11px;

            line-height: 1.5;

            color: #6b7280;

        }

        .membership-price {

            font-size: 12px;

            font-weight: 800;

            color: #047857;

            margin-top: 5px;

        }

        .membership-option:has(input[type="radio"]:checked) {

            border-color: #059669;

            background: #ecfdf5;

            box-shadow:
                0 0 0 3px rgba(5, 150, 105, 0.08);

        }

        /* =========================================================
           CATEGORY CONTAINER
        ========================================================= */

        #category-section {

            display: none;

        }

        #category-section.show {

            display: block;

            animation: categoryFadeIn 0.25s ease;

        }

        @keyframes categoryFadeIn {

            from {

                opacity: 0;

                transform: translateY(-5px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }

        .category-empty {

            padding: 16px;

            border-radius: 11px;

            background: #f9fafb;

            border: 1px dashed #d1d5db;

            color: #6b7280;

            font-size: 12px;

            text-align: center;

        }

        /* =========================================================
           INFORMATION BOX
        ========================================================= */

        .registration-info {

            margin-top: 8px;

            padding: 12px 14px;

            border-radius: 10px;

            background: #f0fdf4;

            border: 1px solid #bbf7d0;

            color: #166534;

            font-size: 11px;

            line-height: 1.6;

        }

        /* =========================================================
           BUTTON
        ========================================================= */

        .register-button {

            width: 100%;

            height: 54px;

            margin-top: 7px;

            border: none;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #047857,
                    #059669
                );

            color: white;

            font-family: inherit;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 10px 25px rgba(4, 120, 87, 0.18);

            transition: all 0.2s ease;

        }

        .register-button:hover {

            transform: translateY(-1px);

            box-shadow:
                0 13px 30px rgba(4, 120, 87, 0.25);

        }

        .register-button:active {

            transform: translateY(0);

        }

        /* =========================================================
           LOGIN
        ========================================================= */

        .login-text {

            text-align: center;

            margin-top: 25px;

            font-size: 13px;

            color: #6b7280;

        }

        .login-text a {

            color: #047857;

            font-weight: 700;

            text-decoration: none;

            margin-left: 4px;

        }

        .login-text a:hover {

            text-decoration: underline;

        }

        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {

            text-align: center;

            margin-top: 45px;

            color: #9ca3af;

            font-size: 11px;

        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 900px) {

            .left-panel {

                display: none;

            }

            .right-panel {

                width: 100%;

                min-height: 100vh;

                padding: 35px 22px;

            }

            .register-container {

                max-width: 460px;

            }

        }

        @media (max-width: 480px) {

            .right-panel {

                padding: 28px 18px;

            }

            .form-header h2 {

                font-size: 27px;

            }

            .form-header {

                margin-bottom: 27px;

            }

        }

    </style>

</head>


<body>

    <div class="page">


        <!-- =========================================================
             LEFT BRANDING
        ========================================================== -->

        <section class="left-panel">

            <div class="left-content">


                <!-- BRAND -->

                <div class="brand">

                    <div class="brand-logo">

                        <img
                            src="{{ asset('frontend/assets/img/logo.png') }}"
                            alt="NACPDEAN Logo"
                        >

                    </div>


                    <div>

                        <div class="brand-name">
                            NACPDEAN
                        </div>

                        <div class="brand-subtitle">
                            Membership Portal
                        </div>

                    </div>

                </div>


                <!-- HERO -->

                <div class="hero">

                    <h1>

                        Become a member of
                        <span>NACPDEAN.</span>

                    </h1>


                    <p>

                        Create your account and select your
                        membership category to begin your
                        NACPDEAN membership registration.

                    </p>


                    <!-- FEATURES -->

                    <div class="features">

                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            Secure account registration

                        </div>


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            Email verification with OTP

                        </div>


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            Membership category selection

                        </div>


                        <div class="feature">

                            <div class="feature-icon">
                                ✓
                            </div>

                            Access your membership services online

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             RIGHT FORM
        ========================================================== -->

        <section class="right-panel">

            <div class="register-container">


                <!-- HEADER -->

                <div class="form-header">

                    <h2>
                        Create your account
                    </h2>


                    <p>

                        Select your membership type and category,
                        then enter your account details to begin
                        your NACPDEAN registration.

                    </p>

                </div>


                <!-- SUCCESS -->

                @if (session('success'))

                    <div class="alert alert-success">

                        {{ session('success') }}

                    </div>

                @endif


                <!-- ERROR -->

                @if (session('error'))

                    <div class="alert alert-danger">

                        {{ session('error') }}

                    </div>

                @endif


                <!-- VALIDATION ERRORS -->

                @if ($errors->any())

                    <div class="alert alert-danger">

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- =================================================
                     REGISTRATION FORM
                ================================================== -->

                <form
                    method="POST"
                    action="{{ route('register') }}"
                >

                    @csrf


                    <!-- =================================================
                         MEMBER TYPE
                    ================================================== -->

                    <div class="form-group">

                        <label class="form-label">

                            Membership Type

                        </label>


                        <div class="membership-options">


                            <!-- REGULAR MEMBER -->

                            <label class="membership-option">

                                <input
                                    type="radio"
                                    name="member_type"
                                    value="regular"
                                    {{ old('member_type') === 'regular' ? 'checked' : '' }}
                                    required
                                >


                                <div class="membership-option-content">

                                    <div class="membership-card-title">

                                        Regular Member

                                    </div>

                                    <div class="membership-card-description">

                                        For Exporter, Dealer, Supplier
                                        or Producer membership categories.

                                    </div>

                                </div>

                            </label>


                            <!-- AFFILIATE MEMBER -->

                            <label class="membership-option">

                                <input
                                    type="radio"
                                    name="member_type"
                                    value="affiliate"
                                    {{ old('member_type') === 'affiliate' ? 'checked' : '' }}
                                >


                                <div class="membership-option-content">

                                    <div class="membership-card-title">

                                        Affiliate Member

                                    </div>

                                    <div class="membership-card-description">

                                        RCG affiliate membership.

                                    </div>

                                </div>

                            </label>


                        </div>


                        @error('member_type')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    <!-- =================================================
                         MEMBERSHIP CATEGORY
                    ================================================== -->

                    <div
                        class="form-group"
                        id="category-section"
                    >

                        <label class="form-label">

                            Select Membership Category

                        </label>


                        <div
                            class="membership-options"
                            id="category-options"
                        >


                            @forelse ($categories as $category)

                                <label
                                    class="membership-option category-option"
                                    data-member-type="{{ $category->member_type }}"
                                >

                                    <input
                                        type="radio"
                                        name="membership_category_id"
                                        value="{{ $category->id }}"
                                        data-member-type="{{ $category->member_type }}"
                                        {{ old('membership_category_id') == $category->id ? 'checked' : '' }}
                                    >


                                    <div class="membership-option-content">

                                        <div class="membership-card-title">

                                            {{ $category->name }}

                                            @if ($category->code)

                                                ({{ $category->code }})

                                            @endif

                                        </div>


                                        @if (!empty($category->description))

                                            <div class="membership-card-description">

                                                {{ $category->description }}

                                            </div>

                                        @endif


                                        @php

                                            $categoryFee = null;

                                            if (method_exists($category, 'fees')) {

                                                $categoryFee = $category->fees
                                                    ->where('is_active', true)
                                                    ->first();

                                            }

                                        @endphp


                                        @if ($categoryFee)

                                            <div class="membership-price">

                                                ₦{{ number_format($categoryFee->amount, 2) }}

                                            </div>

                                        @endif

                                    </div>

                                </label>

                            @empty

                                <div class="category-empty">

                                    No membership categories are currently
                                    available.

                                </div>

                            @endforelse


                        </div>


                        <div
                            class="category-empty"
                            id="no-category-message"
                            style="display: none;"
                        >

                            No membership categories are currently available
                            for the selected membership type.

                        </div>


                        <div class="registration-info">

                            Your selected membership category will determine
                            the applicable registration fee. The amount will
                            be recorded as a debit against your account when
                            your registration is created.

                        </div>


                        @error('membership_category_id')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    <!-- =================================================
                         EMAIL
                    ================================================== -->

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >

                            Email Address

                        </label>


                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-input"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required
                            autofocus
                            autocomplete="email"
                        >


                        @error('email')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    <!-- =================================================
                         PASSWORD
                    ================================================== -->

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >

                            Password

                        </label>


                        <div class="input-wrapper">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="form-input password-input"
                                placeholder="Create a password"
                                required
                                autocomplete="new-password"
                            >


                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword('password', this)"
                            >

                                Show

                            </button>

                        </div>


                        <div class="password-hint">

                            Minimum 8 characters.

                        </div>


                        @error('password')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    <!-- =================================================
                         CONFIRM PASSWORD
                    ================================================== -->

                    <div class="form-group">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >

                            Confirm Password

                        </label>


                        <div class="input-wrapper">

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                class="form-input password-input"
                                placeholder="Re-enter your password"
                                required
                                autocomplete="new-password"
                            >


                            <button
                                type="button"
                                class="toggle-password"
                                onclick="togglePassword(
                                    'password_confirmation',
                                    this
                                )"
                            >

                                Show

                            </button>

                        </div>


                        @error('password_confirmation')

                            <div class="error-message">

                                {{ $message }}

                            </div>

                        @enderror

                    </div>


                    <!-- =================================================
                         SUBMIT
                    ================================================== -->

                    <button
                        type="submit"
                        class="register-button"
                    >

                        Create Membership Account

                    </button>

                </form>


                <!-- LOGIN -->

                <div class="login-text">

                    Already have an account?

                    <a href="{{ route('login') }}">
                        Login
                    </a>

                </div>


                <!-- FOOTER -->

                <div class="footer">

                    © {{ date('Y') }} NACPDEAN.
                    All rights reserved.

                </div>


            </div>

        </section>

    </div>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <script>

        /*
        |--------------------------------------------------------------------------
        | TOGGLE PASSWORD
        |--------------------------------------------------------------------------
        */

        function togglePassword(fieldId, button) {

            const field =
                document.getElementById(fieldId);


            if (field.type === 'password') {

                field.type = 'text';

                button.textContent = 'Hide';

            } else {

                field.type = 'password';

                button.textContent = 'Show';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP TYPE → CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        document.addEventListener('DOMContentLoaded', function () {

            const memberTypeInputs =
                document.querySelectorAll(
                    'input[name="member_type"]'
                );


            const categorySection =
                document.getElementById(
                    'category-section'
                );


            const categoryOptions =
                document.querySelectorAll(
                    '.category-option'
                );


            const categoryRadios =
                document.querySelectorAll(
                    'input[name="membership_category_id"]'
                );


            const noCategoryMessage =
                document.getElementById(
                    'no-category-message'
                );


            /*
            |--------------------------------------------------------------------------
            | FILTER CATEGORIES
            |--------------------------------------------------------------------------
            */

            function filterCategories(memberType) {

                if (!memberType) {

                    categorySection.classList.remove('show');

                    return;

                }


                categorySection.classList.add('show');


                let visibleCategories = 0;


                categoryOptions.forEach(function (option) {

                    const optionType =
                        option.dataset.memberType;


                    if (optionType === memberType) {

                        option.style.display = 'flex';

                        visibleCategories++;

                    } else {

                        option.style.display = 'none';


                        /*
                        |--------------------------------------------------------------------------
                        | Uncheck categories that no longer belong
                        |--------------------------------------------------------------------------
                        */

                        const radio =
                            option.querySelector(
                                'input[type="radio"]'
                            );


                        if (radio) {

                            radio.checked = false;

                        }

                    }

                });


                /*
                |--------------------------------------------------------------------------
                | Show / hide empty message
                |--------------------------------------------------------------------------
                */

                if (visibleCategories === 0) {

                    noCategoryMessage.style.display = 'block';

                } else {

                    noCategoryMessage.style.display = 'none';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | MEMBER TYPE CHANGE
            |--------------------------------------------------------------------------
            */

            memberTypeInputs.forEach(function (input) {

                input.addEventListener(
                    'change',
                    function () {

                        filterCategories(
                            this.value
                        );

                    }
                );

            });


            /*
            |--------------------------------------------------------------------------
            | INITIAL PAGE LOAD
            |--------------------------------------------------------------------------
            |
            | This handles old() after a validation error.
            |
            */

            const selectedMemberType =
                document.querySelector(
                    'input[name="member_type"]:checked'
                );


            if (selectedMemberType) {

                filterCategories(
                    selectedMemberType.value
                );

            }

        });

    </script>


</body>

</html>
