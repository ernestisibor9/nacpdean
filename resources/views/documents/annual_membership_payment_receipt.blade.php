@php
    /*
    |--------------------------------------------------------------------------
    | LOAD GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );

    /*
    |--------------------------------------------------------------------------
    | LOAD USER
    |--------------------------------------------------------------------------
    */

    $documentUser = $generatedDocument->user ?? null;

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    |
    | Membership ID = Membership Number
    |
    | We first check field_values and then directly resolve the user's
    | membership record as a fallback.
    |
    */

    $membershipNumber = $fields->get(
        'membership_number',
        $fields->get(
            'membership_no',
            $fields->get(
                'membership_id',
                ''
            )
        )
    );

    $membership = null;

    /*
    |--------------------------------------------------------------------------
    | FALLBACK: ACTUAL MEMBERSHIP RECORD
    |--------------------------------------------------------------------------
    */

    if ($documentUser) {

        $membership = \App\Models\Membership::query()
            ->with('membershipCategory')
            ->where('user_id', $documentUser->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (
            !$membershipNumber &&
            $membership
        ) {
            $membershipNumber =
                $membership->membership_number;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. Generated document field
    | 2. Actual membership category
    |
    */

    $membershipCategory = strtoupper(
        trim(
            (string) $fields->get(
                'membership_category',
                $fields->get(
                    'category',
                    ''
                )
            )
        )
    );

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY CODE
    |--------------------------------------------------------------------------
    |
    | For RCG we use the actual membership category code when available.
    |
    */

    $membershipCategoryCode = '';

    if ($membership && $membership->membershipCategory) {

        $membershipCategoryCode = strtoupper(
            trim(
                (string) $membership
                    ->membershipCategory
                    ->code
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RCG CHECK
    |--------------------------------------------------------------------------
    |
    | RCG is category code "RCG".
    |
    | We check both the generated field and the actual database category.
    |
    */

    $isRCG =
        $membershipCategoryCode === 'RCG'
        ||
        $membershipCategory === 'RCG'
        ||
        str_contains(
            $membershipCategory,
            'RCG'
        );

    /*
    |--------------------------------------------------------------------------
    | PAYMENT DATE
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. payment_date in generated fields
    | 2. paid_at in generated fields
    | 3. Payment record matched by transaction reference
    | 4. Latest successful membership payment for the user
    |
    */

    $paymentDate = $fields->get(
        'payment_date',
        $fields->get(
            'paid_at',
            null
        )
    );

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION REFERENCE
    |--------------------------------------------------------------------------
    */

    $transactionReference = $fields->get(
        'transaction_reference',
        $fields->get(
            'payment_reference',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | FALLBACK: FIND PAYMENT RECORD
    |--------------------------------------------------------------------------
    */

    $receiptPayment = null;

    if ($documentUser) {

        /*
        |--------------------------------------------------------------------------
        | FIRST TRY: TRANSACTION REFERENCE
        |--------------------------------------------------------------------------
        */

        if ($transactionReference) {

            $receiptPayment = \App\Models\Payment::query()
                ->where(
                    'user_id',
                    $documentUser->id
                )
                ->where(
                    function ($query) use (
                        $transactionReference
                    ) {

                        $query
                            ->where(
                                'reference',
                                $transactionReference
                            )
                            ->orWhere(
                                'payment_reference',
                                $transactionReference
                            )
                            ->orWhere(
                                'paystack_reference',
                                $transactionReference
                            );
                    }
                )
                ->where(
                    'status',
                    'paid'
                )
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | SECOND FALLBACK
        |--------------------------------------------------------------------------
        |
        | Get the latest successful membership payment.
        |
        */

        if (!$receiptPayment) {

            $receiptPayment = \App\Models\Payment::query()
                ->where(
                    'user_id',
                    $documentUser->id
                )
                ->where(
                    'status',
                    'paid'
                )
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'payment_type',
                                'membership'
                            )
                            ->orWhere(
                                'payment_type',
                                'membership_renewal'
                            );
                    }
                )
                ->latest('paid_at')
                ->latest('id')
                ->first();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | USE ACTUAL PAYMENT DATE IF FIELD VALUE IS MISSING
    |--------------------------------------------------------------------------
    */

    if (
        !$paymentDate &&
        $receiptPayment &&
        $receiptPayment->paid_at
    ) {
        $paymentDate =
            $receiptPayment->paid_at;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT PAYMENT DATE
    |--------------------------------------------------------------------------
    */

    $paymentDateFormatted = '';

    if ($paymentDate) {

        try {

            $paymentDateFormatted =
                \Carbon\Carbon::parse(
                    $paymentDate
                )->format('d F Y');

        } catch (\Throwable $e) {

            $paymentDateFormatted =
                (string) $paymentDate;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIPT NUMBER
    |--------------------------------------------------------------------------
    */

    $receiptNumber = $fields->get(
        'receipt_number',
        $fields->get(
            'receipt_no',
            $fields->get(
                'ref_no',
                $generatedDocument->document_number
            )
        )
    );

    /*
    |--------------------------------------------------------------------------
    | MEMBER INFORMATION
    |--------------------------------------------------------------------------
    */

    $memberName = $fields->get(
        'member_name',
        ''
    );

    $businessName = $fields->get(
        'business_name',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | PHONE
    |--------------------------------------------------------------------------
    */

    $phone = $fields->get(
        'phone',
        $fields->get(
            'phone_number',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | EMAIL
    |--------------------------------------------------------------------------
    */

    $email = $fields->get(
        'email',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    $state = $fields->get(
        'state',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | DATE JOINED
    |--------------------------------------------------------------------------
    */

    $dateJoined = $fields->get(
        'date_joined',
        ''
    );

    $dateJoinedFormatted = '';

    if ($dateJoined) {

        try {

            $dateJoinedFormatted =
                \Carbon\Carbon::parse(
                    $dateJoined
                )->format('d F Y');

        } catch (\Throwable $e) {

            $dateJoinedFormatted =
                (string) $dateJoined;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP YEAR
    |--------------------------------------------------------------------------
    */

    $membershipYear = $fields->get(
        'membership_year',
        $fields->get(
            'membership_year_range',
            ''
        )
    );

    $membershipYearNumber = $fields->get(
        'membership_year_number',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | EXTRACT YEAR FROM MEMBERSHIP YEAR RANGE
    |--------------------------------------------------------------------------
    */

    if (
        !$membershipYearNumber &&
        $membershipYear
    ) {

        if (
            preg_match(
                '/\b(20\d{2})\b/',
                (string) $membershipYear,
                $matches
            )
        ) {
            $membershipYearNumber =
                $matches[1];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO PAYMENT YEAR
    |--------------------------------------------------------------------------
    */

    if (
        !$membershipYearNumber &&
        $paymentDate
    ) {

        try {

            $membershipYearNumber =
                \Carbon\Carbon::parse(
                    $paymentDate
                )->format('Y');

        } catch (\Throwable $e) {
            //
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD MEMBERSHIP YEAR RANGE
    |--------------------------------------------------------------------------
    */

    if (
        !$membershipYear &&
        $membershipYearNumber
    ) {

        $membershipYear =
            "01 January {$membershipYearNumber} - 31 December {$membershipYearNumber}";
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT FOR
    |--------------------------------------------------------------------------
    */

    $paymentFor = $fields->get(
        'payment_for',
        ''
    );

    if (
        !$paymentFor &&
        $membershipYearNumber
    ) {

        $paymentFor =
            "Annual membership subscription {$membershipYearNumber}";
    }

    if (!$paymentFor) {

        $paymentFor =
            'Annual membership subscription';
    }

    /*
    |--------------------------------------------------------------------------
    | AMOUNT PAID
    |--------------------------------------------------------------------------
    */

    $amountPaid = $fields->get(
        'amount_paid',
        $fields->get(
            'amount',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO ACTUAL PAYMENT AMOUNT
    |--------------------------------------------------------------------------
    */

    if (
        $amountPaid === '' &&
        $receiptPayment
    ) {

        $amountPaid =
            $receiptPayment->amount;
    }

    if (is_numeric($amountPaid)) {

        $amountPaid =
            '₦' .
            number_format(
                (float) $amountPaid,
                2
            );
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION REFERENCE FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        !$transactionReference &&
        $receiptPayment
    ) {

        $transactionReference =
            $receiptPayment->reference
            ?:
            $receiptPayment->payment_reference
            ?:
            $receiptPayment->paystack_reference
            ?:
            '';
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION CODE
    |--------------------------------------------------------------------------
    */

    $authenticationCode = $fields->get(
        'authentication_code',
        $generatedDocument->tracking_code
    );

    /*
    |--------------------------------------------------------------------------
    | VERIFICATION URL
    |--------------------------------------------------------------------------
    */

    $verificationUrl = $fields->get(
        'verification_url',
        route(
            'documents.verify',
            $generatedDocument->tracking_code
        )
    );

    /*
    |--------------------------------------------------------------------------
    | SERIAL / RUNNING RECEIPT TAG
    |--------------------------------------------------------------------------
    */

    $serialTag = $fields->get(
        'serial_number',
        str_pad(
            preg_replace(
                '/\D/',
                '',
                (string) $receiptNumber
            ) ?: '1',
            5,
            '0',
            STR_PAD_LEFT
        )
    );

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY FLAGS
    |--------------------------------------------------------------------------
    */

    $isProducer = str_contains(
        $membershipCategory,
        'PRODUCER'
    );

    $isDealer = str_contains(
        $membershipCategory,
        'DEALER'
    );

    $isSupplier = str_contains(
        $membershipCategory,
        'SUPPLIER'
    );

    $isExporter = str_contains(
        $membershipCategory,
        'EXPORTER'
    );

@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Annual Membership Payment Receipt
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #eef1ee;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #1c1c1c;
        }

        .receipt-card {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        .inner {
            padding: 14mm 16mm 0 16mm;
            flex: 1;
        }

        /* --------------------------------------------------------------
           HEADER
        -------------------------------------------------------------- */

        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .brand-section {
            width: 58%;
        }

        /*
        |--------------------------------------------------------------------------
        | LOGO ROW
        |--------------------------------------------------------------------------
        |
        | NACPDEAN logo is always displayed.
        |
        | RCC logo is displayed ONLY when membership category is RCG.
        |
        */

        .logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nacpdean-logo {
            max-width: 150px;
            max-height: 55px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .rcc-logo {
            max-width: 90px;
            max-height: 55px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .logo-divider {
            width: 1px;
            height: 45px;
            background: #d0d0d0;
        }

        .logo-text {
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
            color: #1c1c1c;
            letter-spacing: -1px;
        }

        .logo-text span {
            color: #1f7a3d;
        }

        .cac-number {
            font-size: 8px;
            font-weight: 700;
            color: #444;
            margin-left: 2px;
        }

        .organization-name {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.3;
            text-transform: uppercase;
            color: #1f7a3d;
        }

        .organization-motto {
            margin-top: 4px;
            font-size: 9.5px;
            font-weight: 700;
            color: #c81f2f;
            line-height: 1.4;
        }

        .secretariat-section {
            width: 40%;
            text-align: right;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.55;
            color: #1c1c1c;
        }

        /* --------------------------------------------------------------
           TITLE BANNER
        -------------------------------------------------------------- */

        .title-banner-row {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
        }

        .title-banner {
            background: #d5202c;
            color: #fff;
            padding: 10px 20px;
            text-align: right;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.2;
            letter-spacing: .3px;
            width: 62%;
        }

        .ack-strip {
            margin-top: 0;
            background: #1f7a3d;
            color: #fff;
            text-align: center;
            font-size: 10.5px;
            font-weight: 600;
            padding: 6px 10px;
        }

        /* --------------------------------------------------------------
           BODY GRID
        -------------------------------------------------------------- */

        .body-grid {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-top: 16px;
        }

        .col-left {
            width: 58%;
        }

        .col-right {
            width: 38%;
        }

        .block-title {
            font-size: 11px;
            font-weight: 800;
            color: #1f7a3d;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 8px;
        }

        .info-row {
            display: flex;
            padding: 4px 0;
            font-size: 11.5px;
        }

        .info-label {
            width: 42%;
            color: #333;
        }

        .info-value {
            width: 58%;
            font-weight: 700;
            color: #111;
            word-break: break-word;
        }

        .payment-section {
            margin-top: 18px;
        }

        /* --------------------------------------------------------------
           RIGHT COLUMN
        -------------------------------------------------------------- */

        .receipt-no-box,
        .payment-date-box {
            margin-bottom: 12px;
        }

        .receipt-no-label,
        .payment-date-label {
            font-size: 10px;
            font-weight: 800;
            color: #d5202c;
            text-transform: uppercase;
        }

        .receipt-no-value,
        .payment-date-value {
            font-size: 11.5px;
            font-weight: 700;
            color: #1c1c1c;
            margin-top: 2px;
        }

        .category-box {
            background: #e7edd9;
            padding: 10px 12px;
            margin-top: 4px;
        }

        .category-box-title {
            font-size: 10px;
            font-weight: 800;
            color: #1f7a3d;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .category-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #222;
            padding: 3px 0;
        }

        .checkbox {
            width: 16px;
            height: 16px;
            border: 1.5px solid #888;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            color: #1f7a3d;
        }

        .checkbox.checked {
            border-color: #1f7a3d;
        }

        .auth-box {
            margin-top: 14px;
            border: 1px solid #ccc;
            background: #fafaf7;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-code,
        .qr-placeholder {
            width: 72px;
            height: 72px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .qr-placeholder {
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #777;
            text-align: center;
        }

        .auth-text {
            font-size: 9px;
            color: #444;
        }

        .auth-verify-label {
            font-size: 8.5px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .auth-code {
            font-family: monospace;
            font-size: 11px;
            font-weight: 800;
            color: #1f7a3d;
            margin-top: 2px;
            word-break: break-all;
        }

        /* --------------------------------------------------------------
           VALIDITY NOTE
        -------------------------------------------------------------- */

        .validity-note {
            margin-top: 16px;
            font-size: 10px;
            font-style: italic;
            color: #333;
            line-height: 1.5;
        }

        /* --------------------------------------------------------------
           FOOTER SIGNATURES
        -------------------------------------------------------------- */

        .signature-strip {
            margin-top: 22px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 0 6px;
        }

        .sig-col {
            width: 26%;
            text-align: center;
        }

        .sig-scribble {
            font-family: "Brush Script MT", cursive;
            font-size: 26px;
            color: #1c1c1c;
            height: 34px;
        }

        .sig-line {
            border-top: 1.5px solid #333;
            margin-top: 2px;
            padding-top: 4px;
        }

        .sig-name {
            font-size: 12px;
            font-weight: 800;
            color: #1c1c1c;
        }

        .sig-title {
            font-size: 9px;
            color: #555;
            margin-top: 1px;
        }

        .seal-col {
            width: 22%;
            text-align: center;
        }

        .seal-badge {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            border: 2.5px solid #1f7a3d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 7px;
            font-weight: 800;
            color: #1f7a3d;
            text-align: center;
            line-height: 1.3;
            padding: 4px;
        }

        .year-col {
            width: 22%;
            text-align: center;
        }

        .year-badge {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: radial-gradient(
                circle,
                #f6d270 0%,
                #d5202c 70%
            );
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #fff;
        }

        .year-badge .yr {
            font-size: 19px;
            font-weight: 900;
        }

        .year-badge .yr-label {
            font-size: 6px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
            text-align: center;
        }

        /* --------------------------------------------------------------
           PILLARS
        -------------------------------------------------------------- */

        .pillars-row {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #e3e3e3;
            border-bottom: 1px solid #e3e3e3;
            padding: 10px 4px;
        }

        .pillar-item {
            width: 24%;
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            color: #333;
            line-height: 1.35;
        }

        .pillar-icon {
            font-size: 16px;
            margin-bottom: 4px;
        }

        /* --------------------------------------------------------------
           THANK YOU BAR
        -------------------------------------------------------------- */

        .thankyou-bar {
            margin-top: 0;
            background: #1f7a3d;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16mm;
            font-style: italic;
            font-size: 12px;
            font-weight: 600;
        }

        .serial-tag {
            background: #d5202c;
            color: #fff;
            font-style: normal;
            font-weight: 900;
            font-size: 15px;
            padding: 6px 16px;
            letter-spacing: .5px;
        }

        /* --------------------------------------------------------------
           PRINT
        -------------------------------------------------------------- */

        @media print {

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .receipt-card {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                border: none;
                box-shadow: none;
            }
        }

    </style>

</head>

<body>

<div class="receipt-card">

    {{-- ==============================================================
         HEADER
    ============================================================== --}}

    <div class="inner">

        <div class="receipt-header">

            <div class="brand-section">

                <div class="logo-row">

                    {{-- ==================================================
                         NACPDEAN LOGO
                         Always displayed
                    =================================================== --}}

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="NACPDEAN Logo"
                        class="nacpdean-logo"
                    >

                    {{-- ==================================================
                         RCC LOGO
                         ONLY DISPLAY FOR RCG MEMBERS
                    =================================================== --}}

                    @if ($isRCG)

                        <div class="logo-divider"></div>

                        <img
                            src="{{ asset('images/rcc.jpeg') }}"
                            alt="RCG Logo"
                            class="rcc-logo"
                        >

                    @endif

                </div>

                <div class="cac-number">
                    CAC/IT/NO.182068
                </div>

                <div class="organization-name">
                    National Association of Charcoal<br>
                    Producers Dealers Exporters<br>
                    And Afforestation of Nigeria
                </div>

                <div class="organization-motto">
                    United for Sustainable Production,
                    Responsible Trade &amp; Afforestation
                </div>

            </div>

            <div class="secretariat-section">

                National Secretariat:<br>

                Block D Complex, Federal Ministry of Industry<br>

                Trade and Investment, Old Secretariat, Area 1,<br>

                Garki, Abuja &ndash; FCT, Nigeria.<br>

                Tel: +234 814 567 2358,
                +234 803 667 0360,<br>

                +234 905 301 8515<br>

                Email: nacpdean55@gmail.com<br>

                Website: www.nacpdean.com

            </div>

        </div>

        {{-- TITLE --}}

        <div class="title-banner-row">

            <div class="title-banner">

                Annual Membership<br>
                Receipt Payment

            </div>

        </div>

    </div>

    {{-- ==============================================================
         ACKNOWLEDGEMENT
    ============================================================== --}}

    <div class="ack-strip">

        This is to acknowledge the receipt of payment for
        Annual Membership Subscription as detailed below.

    </div>

    <div class="inner">

        {{-- ==========================================================
             BODY
        =========================================================== --}}

        <div class="body-grid">

            {{-- LEFT COLUMN --}}

            <div class="col-left">

                <div class="block-title">
                    Name Information
                </div>

                <div class="info-row">

                    <div class="info-label">
                        Membership ID:
                    </div>

                    <div class="info-value">
                        {{ $membershipNumber ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Member Name:
                    </div>

                    <div class="info-value">
                        {{ $memberName ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Business Name:
                    </div>

                    <div class="info-value">
                        {{ $businessName ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Membership Category:
                    </div>

                    <div class="info-value">
                        {{ $membershipCategory ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Phone Number:
                    </div>

                    <div class="info-value">
                        {{ $phone ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Email:
                    </div>

                    <div class="info-value">
                        {{ $email ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        State:
                    </div>

                    <div class="info-value">
                        {{ $state ?: '—' }}
                    </div>

                </div>

                <div class="info-row">

                    <div class="info-label">
                        Date Joined:
                    </div>

                    <div class="info-value">
                        {{ $dateJoinedFormatted ?: '—' }}
                    </div>

                </div>

                {{-- PAYMENT DETAILS --}}

                <div class="payment-section">

                    <div class="block-title">
                        Payment Details
                    </div>

                    <div class="info-row">

                        <div class="info-label">
                            Payment for:
                        </div>

                        <div class="info-value">
                            {{ $paymentFor }}
                        </div>

                    </div>

                    <div class="info-row">

                        <div class="info-label">
                            Membership Year:
                        </div>

                        <div class="info-value">
                            {{ $membershipYear ?: '—' }}
                        </div>

                    </div>

                    <div class="info-row">

                        <div class="info-label">
                            Amount Paid:
                        </div>

                        <div class="info-value">
                            {{ $amountPaid ?: '—' }}
                        </div>

                    </div>

                    <div class="info-row">

                        <div class="info-label">
                            Date of Payment:
                        </div>

                        <div class="info-value">
                            {{ $paymentDateFormatted ?: '—' }}
                        </div>

                    </div>

                    <div class="info-row">

                        <div class="info-label">
                            Transaction Reference:
                        </div>

                        <div class="info-value">
                            {{ $transactionReference ?: '—' }}
                        </div>

                    </div>

                </div>

                {{-- VALIDITY --}}

                <div class="validity-note">

                    This receipt is valid for the membership year
                    stated above.<br>

                    It is not transferable and remains the property
                    of NACPDEAN.

                </div>

            </div>

            {{-- RIGHT COLUMN --}}

            <div class="col-right">

                {{-- RECEIPT NUMBER --}}

                <div class="receipt-no-box">

                    <div class="receipt-no-label">
                        Receipt No:
                    </div>

                    <div class="receipt-no-value">
                        {{ $receiptNumber }}
                    </div>

                </div>

                {{-- PAYMENT DATE --}}

                <div class="payment-date-box">

                    <div class="payment-date-label">
                        Date of Payment:
                    </div>

                    <div class="payment-date-value">
                        {{ $paymentDateFormatted ?: '—' }}
                    </div>

                </div>

                {{-- CATEGORY --}}

                <div class="category-box">

                    <div class="category-box-title">
                        Membership Category
                    </div>

                    <div class="category-item">

                        <span>
                            Producer
                        </span>

                        <span
                            class="checkbox {{ $isProducer ? 'checked' : '' }}"
                        >
                            {{ $isProducer ? '✓' : '' }}
                        </span>

                    </div>

                    <div class="category-item">

                        <span>
                            Dealer
                        </span>

                        <span
                            class="checkbox {{ $isDealer ? 'checked' : '' }}"
                        >
                            {{ $isDealer ? '✓' : '' }}
                        </span>

                    </div>

                    <div class="category-item">

                        <span>
                            Supplier
                        </span>

                        <span
                            class="checkbox {{ $isSupplier ? 'checked' : '' }}"
                        >
                            {{ $isSupplier ? '✓' : '' }}
                        </span>

                    </div>

                    <div class="category-item">

                        <span>
                            Exporter
                        </span>

                        <span
                            class="checkbox {{ $isExporter ? 'checked' : '' }}"
                        >
                            {{ $isExporter ? '✓' : '' }}
                        </span>

                    </div>

                </div>

                {{-- QR / VERIFICATION --}}

                <div class="auth-box">

                    @if (!empty($qrCode))

                        <img
                            src="{{ $qrCode }}"
                            alt="Verification QR Code"
                            class="qr-code"
                        >

                    @else

                        <div class="qr-placeholder">
                            [ QR Code ]
                        </div>

                    @endif

                    <div>

                        <div class="auth-text">
                            Scan to verify this receipt online.
                        </div>

                        <div class="auth-verify-label">
                            Verification Code
                        </div>

                        <div class="auth-code">
                            {{ $authenticationCode }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ==========================================================
             SIGNATURES
        =========================================================== --}}

        <div class="signature-strip">

            <div class="sig-col">

                <div class="sig-scribble">
                    &#9994;
                </div>

                <div class="sig-line">

                    <div class="sig-name">
                        Dada Daniel
                    </div>

                    <div class="sig-title">
                        National Financial Secretary
                    </div>

                </div>

            </div>

            <div class="seal-col">

                <div class="seal-badge">

                    NACPDEAN<br>
                    OFFICIAL<br>
                    RECEIPT

                </div>

            </div>

            <div class="sig-col">

                <div class="sig-scribble">
                    &#9994;
                </div>

                <div class="sig-line">

                    <div class="sig-name">
                        Edu Babatunde
                    </div>

                    <div class="sig-title">
                        National President
                    </div>

                </div>

            </div>

            <div class="year-col">

                <div class="year-badge">

                    <div class="yr">
                        {{ $membershipYearNumber ?: '—' }}
                    </div>

                    <div class="yr-label">
                        Membership<br>
                        Year
                    </div>

                </div>

            </div>

        </div>

        {{-- ==========================================================
             PILLARS
        =========================================================== --}}

        <div class="pillars-row">

            <div class="pillar-item">

                <div class="pillar-icon">
                    &#9679;
                </div>

                Promoting Sustainable Charcoal Production

            </div>

            <div class="pillar-item">

                <div class="pillar-icon">
                    &#9670;
                </div>

                Ethical Trade &amp; Best Practices

            </div>

            <div class="pillar-item">

                <div class="pillar-icon">
                    &#9733;
                </div>

                Afforestation &amp; Environmental Stewardship

            </div>

            <div class="pillar-item">

                <div class="pillar-icon">
                    &#9679;
                </div>

                Stronger Together for a Greener Future

            </div>

        </div>

    </div>

    {{-- ==============================================================
         THANK YOU BAR
    ============================================================== --}}

    <div class="thankyou-bar">

        <span>
            Thank you for being a valued member of NACPDEAN
        </span>

        <span class="serial-tag">
            {{ $serialTag }}
        </span>

    </div>

</div>

</body>

</html>
