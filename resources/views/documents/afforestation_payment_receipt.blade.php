@php
    /*
    |--------------------------------------------------------------------------
    | RESOLVE FIELD VALUES
    |--------------------------------------------------------------------------
    |
    | All of these were already resolved (system + manual) by
    | DocumentGenerationService::generate() for document #35
    | and stored on $generatedDocument->field_values.
    |
    | We just read them back out here — nothing is recalculated
    | in the view.
    |--------------------------------------------------------------------------
    */

    $fields = collect($generatedDocument->field_values ?? []);

    $receiptNo = $fields->get('receipt_no', $generatedDocument->document_number);

    // SELLER (system-resolved)
    $sellerName          = $fields->get('seller_member_name');
    $sellerMembershipNo  = $fields->get('seller_membership_no');
    $sellerDealingRightNo = $fields->get('seller_dealing_right_no');
    $sellerPhone         = $fields->get('seller_phone');

    // Manual field on document #35
    $loadingPoint = $fields->get('loading_point');

    // AMOUNT
    $amountPaid     = $fields->get('amount_paid', $fields->get('amount'));
    $amountInFigure = $fields->get('amount_in_figure');
    $paymentDate    = $fields->get('payment_date');
    $paymentTime    = $fields->get('payment_time');

    // BUYER (manual fields on document #35)
    $buyerName           = $fields->get('buyer_member_name');
    $buyerMembershipNo   = $fields->get('buyer_membership_no');
    $buyerDealingRightNo = $fields->get('buyer_dealing_right_no');
    $buyerState          = $fields->get('buyer_state');
    $vehicleNumber       = $fields->get('vehicle_number');

    $trackingCode = $fields->get('tracking_code', $generatedDocument->tracking_code);

    $verificationUrl = route(
        'documents.verify',
        $generatedDocument->tracking_code
    );

    $formattedAmount = $amountPaid !== null && $amountPaid !== ''
        ? 'N' . number_format((float) $amountPaid, 2)
        : '';

    $formattedDate = $paymentDate
        ? \Carbon\Carbon::parse($paymentDate)->format('d-m-Y')
        : '';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>{{ $receiptNo }}</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eee;
            font-family: "Times New Roman", serif;
            color: #1a1a1a;
        }


        /*
        |--------------------------------------------------------------------------
        | RECEIPT WRAPPER
        |--------------------------------------------------------------------------
        */

        .receipt-wrapper {
            width: 100%;
            padding: 30px;
        }


        /*
        |--------------------------------------------------------------------------
        | RECEIPT
        |--------------------------------------------------------------------------
        */

        .receipt {
            position: relative;
            width: 100%;
            max-width: 760px;
            min-height: 1000px;
            margin: auto;

            background: #fff;

            border: 14px solid #189B49;

            padding: 6px;
        }


        /*
        |--------------------------------------------------------------------------
        | INNER RECEIPT
        |--------------------------------------------------------------------------
        */

        .receipt-inner {
            position: relative;

            min-height: 970px;

            padding: 30px 35px 20px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | CORNER ORNAMENTS (dot squares, dompdf-safe: table layout, no flexbox)
        |--------------------------------------------------------------------------
        */

        .corner {
            position: absolute;

            width: 34px;
            height: 34px;

            background: #189B49;

            border: 3px solid #fff;

            display: table;
        }

        .corner-row {
            display: table-row;
        }

        .corner-dot {
            display: table-cell;

            width: 50%;
            height: 17px;

            padding: 2px;
        }

        .corner-dot span {
            display: block;

            width: 100%;
            height: 100%;

            background: #fff;

            border-radius: 50%;
        }

        .corner-top-left {
            top: -4px;
            left: -4px;
        }

        .corner-top-right {
            top: -4px;
            right: -4px;
        }

        .corner-bottom-left {
            bottom: -4px;
            left: -4px;
        }

        .corner-bottom-right {
            bottom: -4px;
            right: -4px;
        }


        /*
        |--------------------------------------------------------------------------
        | WATERMARK
        |
        | Sits centered behind all content. Low opacity + pointer-events:
        | none so it never interferes with the text or form lines above it.
        |--------------------------------------------------------------------------
        */

        .watermark {
            position: absolute;

            top: 50%;
            left: 50%;

            transform: translate(-50%, -50%);

            width: 380px;
            height: 380px;

            object-fit: contain;

            opacity: 0.08;

            z-index: 1;

            pointer-events: none;
        }


        /*
        |--------------------------------------------------------------------------
        | CONTENT STACKING WRAPPER
        |
        | Everything except the watermark lives in here, one stacking
        | level above it, so the watermark stays a faint background
        | element instead of covering the receipt text.
        |--------------------------------------------------------------------------
        */

        .content {
            position: relative;

            z-index: 2;
        }
        |
        | Sits right-aligned just above the Seller Details section,
        | matching the reference receipt (not at the very top).
        |--------------------------------------------------------------------------
        */

        .receipt-number {
            margin-top: 18px;

            text-align: right;

            font-size: 13px;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |
        | Sits absolutely at the top-left of the card, matching the
        | reference receipt. object-fit: contain (never stretch/skew)
        | since the badge image is already circular.
        |--------------------------------------------------------------------------
        */

        .logo-mark {
            position: absolute;

            top: 18px;
            left: 18px;

            width: 92px;
            height: 92px;
        }

        .logo-mark img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;
        }

        .wordmark {
            width: 210px;
            max-width: 55%;

            margin: 10px auto 0;
        }

        .wordmark img {
            width: 100%;
            height: auto;

            object-fit: contain;

            display: block;
        }

        .cac-no {
            margin-top: 2px;

            font-size: 9px;

            letter-spacing: .5px;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .title {
            margin-top: 10px;

            font-size: 26px;

            font-weight: bold;

            color: #ed1c24;

            letter-spacing: .5px;
        }

        .subtitle {
            margin-top: 2px;

            font-size: 16px;

            font-weight: bold;
        }

        .lead-text {
            margin: 10px auto 0;

            max-width: 560px;

            font-size: 13px;

            font-style: italic;

            line-height: 1.4;
        }

        .association {
            margin: 12px auto 0;

            max-width: 600px;

            font-size: 14px;

            font-weight: bold;

            color: #189B49;

            line-height: 1.35;
        }


        /*
        |--------------------------------------------------------------------------
        | SECTION LABEL
        |--------------------------------------------------------------------------
        */

        .section-label {
            margin-top: 22px;

            text-align: left;

            font-size: 14px;

            font-weight: bold;

            text-transform: uppercase;
        }


        /*
        |--------------------------------------------------------------------------
        | FORM LINES
        |
        | Dompdf-safe: table layout instead of flexbox.
        |--------------------------------------------------------------------------
        */

        .form-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 14px;
        }

        .form-cell {
            display: table-cell;

            vertical-align: bottom;

            text-align: left;

            font-size: 13px;

            padding-right: 20px;
        }

        .form-cell:last-child {
            padding-right: 0;
        }

        .form-cell .value {
            display: inline-block;

            min-width: 60%;

            margin-left: 4px;

            border-bottom: 1px solid #000;

            padding-bottom: 2px;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | AMOUNT BLOCK
        |--------------------------------------------------------------------------
        */

        .amount-block {
            margin-top: 22px;

            font-size: 13px;

            line-height: 1.8;
        }

        .amount-block strong {
            font-weight: bold;
        }

        .amount-paid {
            font-size: 15px;
        }


        /*
        |--------------------------------------------------------------------------
        | OFFICIAL USE
        |--------------------------------------------------------------------------
        */

        .official-use-label {
            margin-top: 28px;

            font-size: 14px;

            font-weight: bold;

            text-transform: uppercase;
        }

        .official-use-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 14px;
        }

        .official-use-item {
            display: table-cell;

            vertical-align: middle;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | STAMP
        |--------------------------------------------------------------------------
        */

        .stamp {
            width: 110px;
            height: 110px;

            margin: 0 auto;
        }

        .stamp img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;

            margin: 0 auto;
        }


        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |
        | Bordered box: QR image (left cell) + verify text (right cell),
        | tracking code sits in its own bordered strip underneath —
        | matching the reference receipt's layout.
        |--------------------------------------------------------------------------
        */

        .qr-section {
            width: 220px;

            margin: 0 auto;
        }

        .qr-box {
            display: table;

            width: 100%;

            table-layout: fixed;

            border: 1px solid #aaa;

            padding: 6px 8px;
        }

        .qr-box-cell {
            display: table-cell;

            vertical-align: middle;
        }

        .qr-box-cell.qr-image-cell {
            width: 70px;

            text-align: center;
        }

        .qr-section img {
            width: 64px;
            height: 64px;

            object-fit: contain;

            image-rendering: pixelated;

            display: block;
            margin: 0 auto;
        }

        .verify-text {
            padding-left: 8px;

            text-align: left;

            font-size: 9px;

            line-height: 1.3;
        }

        .tracking-code-box {
            display: block;

            margin-top: 6px;

            padding: 3px 8px;

            border: 1px solid #aaa;

            font-size: 9px;

            font-weight: bold;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | BOTTOM LOGO STRIP
        |
        | Fixed height cells + object-fit: contain so none of the three
        | partner logos get stretched/skewed relative to each other.
        |--------------------------------------------------------------------------
        */

        .footer-logos {
            display: table;

            width: 230px;

            height: 40px;

            margin: 0 auto;

            table-layout: fixed;
        }

        .footer-logo-column {
            display: table-cell;

            width: 33.33%;

            height: 40px;

            text-align: center;

            vertical-align: middle;
        }

        .footer-logo {
            max-width: 68px;
            max-height: 38px;

            width: auto;
            height: auto;

            object-fit: contain;

            display: inline-block;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .footer-text {
            margin-top: 22px;

            font-size: 10px;

            line-height: 1.5;
        }

        .footer-text a {
            color: #1a1a1a;

            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {
            max-width: 760px;

            margin: 20px auto;

            text-align: center;
        }

        .document-actions a,
        .document-actions button {
            display: inline-block;

            padding: 10px 18px;

            margin: 0 5px;

            border: none;

            border-radius: 5px;

            background: #189B49;

            color: #fff;

            text-decoration: none;

            cursor: pointer;

            font-size: 14px;
        }

        .document-actions a:hover,
        .document-actions button:hover {
            opacity: .9;
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT / PDF
        |--------------------------------------------------------------------------
        */

        @media print {

            @page {
                size: A4 portrait;

                margin: 0;
            }

            html,
            body {
                width: 210mm;

                height: 297mm;

                margin: 0;

                padding: 0;

                background: #fff;
            }

            .receipt-wrapper {
                width: 210mm;

                height: 297mm;

                padding: 8mm;
            }

            .receipt {
                width: 100%;

                height: 100%;

                max-width: none;

                min-height: auto;

                border-width: 6mm;
            }

            .receipt-inner {
                min-height: auto;
            }

            .document-actions {
                display: none !important;
            }
        }

    </style>

</head>


<body>


<div class="receipt-wrapper">


    <div class="receipt">


        {{-- ==============================================================
             CORNER ORNAMENTS
        =============================================================== --}}

        @foreach (['top-left', 'top-right', 'bottom-left', 'bottom-right'] as $corner)
            <div class="corner corner-{{ $corner }}">
                <div class="corner-row">
                    <div class="corner-dot"><span></span></div>
                    <div class="corner-dot"><span></span></div>
                </div>
                <div class="corner-row">
                    <div class="corner-dot"><span></span></div>
                    <div class="corner-dot"><span></span></div>
                </div>
            </div>
        @endforeach


        {{-- ==============================================================
             WATERMARK
        =============================================================== --}}

        <img
            src="{{ asset('images/lo1.png') }}"
            alt="NACPDEAN Watermark"
            class="watermark"
        >


        <div class="content">


        <div class="receipt-inner">


            {{-- ==========================================================
                 LOGO / WORDMARK
            =========================================================== --}}

            <div class="logo-mark">

                <img
                    src="{{ asset('images/lo1.png') }}"
                    alt="NACPDEAN logo"
                >

            </div>

            <div class="wordmark">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="nacpdean"
                >

            </div>

            <div class="cac-no">

                CAC/IT/NO.182068

            </div>


            {{-- ==========================================================
                 TITLE
            =========================================================== --}}

            <div class="title">

                AFFORESTATION PAYMENT RECEIPT

            </div>

            <div class="subtitle">

                (Dealer/Supplier)

            </div>

            <div class="lead-text">

                This is to acknowledge payment made toward afforestation
                contribution for tree planting and environmental
                sustainability

            </div>

            <div class="association">

                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS DEALERS EXPORTERS<br>

                AND AFFORESTATION OF NIGERIA (NACPDEAN)

            </div>


            {{-- ==========================================================
                 RECEIPT NUMBER
            =========================================================== --}}

            <div class="receipt-number">

                RECEIPT N0: {{ $receiptNo }}

            </div>


            {{-- ==========================================================
                 SELLER DETAILS
            =========================================================== --}}

            <div class="section-label">

                Seller Details

            </div>

            <div class="form-row">
                <div class="form-cell">
                    Member Name:
                    <span class="value">{{ $sellerName }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Membership No:
                    <span class="value">{{ $sellerMembershipNo }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Dealing Right No:
                    <span class="value">{{ $sellerDealingRightNo }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Phone:
                    <span class="value">{{ $sellerPhone }}</span>
                </div>
                <div class="form-cell">
                    Loading Point:
                    <span class="value">{{ $loadingPoint }}</span>
                </div>
            </div>


            {{-- ==========================================================
                 AMOUNT
            =========================================================== --}}

            <div class="amount-block">

                <div class="amount-paid">
                    <strong>Amount paid:</strong> {{ $formattedAmount }}
                </div>

                <div>
                    <strong>Amount in figure:</strong> {{ $amountInFigure }}
                    &nbsp;&nbsp;<strong>Date:</strong> {{ $formattedDate }}
                    &nbsp;&nbsp;<strong>Time:</strong> {{ $paymentTime }}
                </div>

            </div>


            {{-- ==========================================================
                 BUYER DETAILS
            =========================================================== --}}

            <div class="section-label">

                Buyer Details

            </div>

            <div class="form-row">
                <div class="form-cell">
                    Member Name:
                    <span class="value">{{ $buyerName }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Membership N0:
                    <span class="value">{{ $buyerMembershipNo }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Dealing Right N0:
                    <span class="value">{{ $buyerDealingRightNo }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    State:
                    <span class="value">{{ $buyerState }}</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-cell">
                    Vehicle/Truck/Trailer/Container N0:
                    <span class="value">{{ $vehicleNumber }}</span>
                </div>
            </div>


            {{-- ==========================================================
                 OFFICIAL USE
            =========================================================== --}}

            <div class="official-use-label">

                Official Use

            </div>

            <div class="official-use-row">

                <div class="official-use-item">

                    <div class="stamp">

                        <img
                            src="{{ asset('images/stamp.png') }}"
                            alt="NACPDEAN official stamp"
                        >

                    </div>

                </div>

                <div class="official-use-item">

                    <div class="qr-section">

                        <div class="qr-box">

                            <div class="qr-box-cell qr-image-cell">

                                @if($qrCode)

                                    <img
                                        src="{{ $qrCode }}"
                                        alt="Receipt verification QR code"
                                    >

                                @endif

                            </div>

                            <div class="qr-box-cell verify-text">

                                Scan to Verify Authenticity of the Document

                            </div>

                        </div>

                        <div class="tracking-code-box">

                            Tracking Code: {{ $trackingCode }}

                        </div>

                    </div>

                </div>

                <div class="official-use-item">

                    <div class="footer-logos">

                        <div class="footer-logo-column">

                            <img
                                src="{{ asset('images/coat.png') }}"
                                alt="Coat of Arms"
                                class="footer-logo"
                            >

                        </div>

                        <div class="footer-logo-column">

                            <img
                                src="{{ asset('images/facan.png') }}"
                                alt="FACAN"
                                class="footer-logo"
                            >

                        </div>

                        <div class="footer-logo-column">

                            <img
                                src="{{ asset('images/logo.png') }}"
                                alt="NACPDEAN"
                                class="footer-logo"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- ==========================================================
                 FOOTER
            =========================================================== --}}

            <div class="footer-text">

                Thank you for supporting afforestation and contributing to a
                greener, more sustainable environment.<br>

                Email: <a href="mailto:info@nacpdean.org">info@nacpdean.org</a>
                &nbsp;&nbsp;
                Website: <a href="https://www.nacpdean.org">www.nacpdean.org</a>

            </div>


        </div>


        </div>


    </div>


</div>


{{-- ================================================================
     ACTION BUTTONS
================================================================= --}}

@if(!$printMode)

    <div class="document-actions">


        <button
            onclick="window.print()"
        >

            🖨 Print Receipt

        </button>


        <a
            href="{{ route('member.documents.download', $generatedDocument) }}"
        >

            ⬇ Download Receipt

        </a>


    </div>

@endif


</body>

</html>
