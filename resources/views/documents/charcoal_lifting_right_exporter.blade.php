@php
    /*
    |--------------------------------------------------------------------------
    | RESOLVE FIELD VALUES
    |--------------------------------------------------------------------------
    |
    | Document #30 — NACPDEAN Charcoal Lifting Right - Exporter.
    |
    | These were already resolved (system + manual) by
    | DocumentGenerationService::generate() and stored on
    | $generatedDocument->field_values. We just read them back
    | out here — nothing is recalculated in the view.
    |--------------------------------------------------------------------------
    */

    $fields = collect($generatedDocument->field_values ?? []);

    $memberName = $fields->get('member_name')
        ?: optional($generatedDocument->user)->name;

    $membershipNumber = $fields->get(
        'membership_number',
        $fields->get('membership_no')
    );

    // Sequential reference number (distinct from the internal
    // document_number). Falls back to document_number if no
    // dedicated ref_no field has been configured.
    $refNo = $fields->get('ref_no', $generatedDocument->document_number);

    $issuedAt = $generatedDocument->issued_at
        ? \Carbon\Carbon::parse($generatedDocument->issued_at)->format('d F Y')
        : '';

    $validTill = $generatedDocument->expires_at
        ? \Carbon\Carbon::parse($generatedDocument->expires_at)->format('jS F, Y')
        : '';

    $verificationUrl = route(
        'documents.verify',
        $generatedDocument->tracking_code
    );
@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>{{ $refNo }}</title>

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
            font-family: "Poppins", Arial, Helvetica, sans-serif;
            color: #1a1a1a;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE WRAPPER
        |--------------------------------------------------------------------------
        */

        .certificate-wrapper {
            width: 100%;
            padding: 30px;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE
        |
        | A rounded, thick green border approximates the reference's
        | pill-shaped frame. NOTE: the reference has an intricate
        | Celtic-knot scroll graphic at each corner — that kind of
        | illustration can't be reproduced reliably with CSS in
        | Dompdf. If pixel-perfect corners matter, export that
        | artwork as a transparent-center PNG and set it as the
        | background-image of .certificate instead of this border.
        |--------------------------------------------------------------------------
        */

        .certificate {
            position: relative;
            width: 100%;
            max-width: 760px;
            min-height: 1030px;
            margin: auto;

            background: #fff;

            border: 10px solid #1c9a4b;
            border-radius: 45px;

            padding: 8px;
        }

        .certificate-inner-border {
            position: absolute;

            top: 16px;
            left: 16px;
            right: 16px;
            bottom: 16px;

            border: 1px solid #1c9a4b;
            border-radius: 36px;

            pointer-events: none;
        }

        /*
        |--------------------------------------------------------------------------
        | DECORATIVE BEADS (top/bottom centre accents on the border)
        |--------------------------------------------------------------------------
        */

        .bead {
            position: absolute;

            width: 26px;
            height: 26px;

            background: #1c9a4b;
            border: 3px solid #fff;
            border-radius: 50%;

            left: 50%;
            margin-left: -13px;

            z-index: 4;
        }

        .bead-top {
            top: -13px;
        }

        .bead-bottom {
            bottom: -13px;
        }


        /*
        |--------------------------------------------------------------------------
        | INNER CERTIFICATE
        |--------------------------------------------------------------------------
        */

        .certificate-inner {
            position: relative;

            min-height: 990px;

            padding: 40px 55px 30px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        .wordmark {
            width: 230px;
            max-width: 60%;

            margin: 0 auto;
        }

        .wordmark img {
            width: 100%;
            height: auto;

            object-fit: contain;

            display: block;
        }

        .cac-no {
            margin-top: 2px;

            font-size: 10px;

            letter-spacing: .5px;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .rule-line {
            width: 300px;
            max-width: 70%;

            height: 3px;

            margin: 22px auto 12px;

            background: #1c9a4b;
            border-radius: 2px;
        }

        .title {
            font-size: 30px;

            font-weight: 800;

            letter-spacing: .5px;

            text-transform: uppercase;
        }

        .certify-text {
            margin-top: 18px;

            font-family: "Brush Script MT", "Segoe Script", cursive;

            font-style: italic;

            font-size: 18px;

            font-weight: bold;

            color: #e30613;
        }

        .member-name {
            margin-top: 14px;

            font-size: 34px;

            font-weight: 800;

            text-transform: uppercase;
        }

        .registered-with {
            margin-top: 14px;

            font-family: "Brush Script MT", "Segoe Script", cursive;

            font-style: italic;

            font-size: 18px;

            font-weight: bold;

            color: #1c9a4b;
        }

        .association {
            margin: 10px auto 0;

            max-width: 600px;

            font-size: 17px;

            font-weight: 800;

            line-height: 1.4;

            text-transform: uppercase;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP / QR ROW
        |--------------------------------------------------------------------------
        */

        .details-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 30px;
        }

        .qr-cell {
            display: table-cell;

            width: 130px;

            vertical-align: top;

            text-align: left;
        }

        .qr-cell img {
            width: 105px;
            height: 105px;

            object-fit: contain;

            display: block;
        }

        .details-cell {
            display: table-cell;

            vertical-align: top;

            text-align: center;

            font-size: 15px;
        }

        .membership-label {
            color: #1c9a4b;

            font-size: 16px;

            font-weight: 600;
        }

        .membership-value {
            margin-top: 2px;

            font-size: 20px;

            font-weight: 800;
        }

        .given-date,
        .valid-till {
            margin-top: 16px;

            font-size: 15px;
        }

        .valid-till span {
            color: #e30613;

            font-weight: 600;
        }

        .ref-no {
            margin-top: 18px;

            font-size: 15px;
        }

        .ref-no strong {
            font-weight: 800;
        }


        /*
        |--------------------------------------------------------------------------
        | SIGNATURES / SEAL
        |--------------------------------------------------------------------------
        */

        .signature-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 40px;
        }

        .signature-cell {
            display: table-cell;

            vertical-align: bottom;

            width: 33.33%;

            text-align: center;
        }

        .signature-img {
            height: 45px;

            max-width: 150px;

            object-fit: contain;

            display: block;

            margin: 0 auto 4px;
        }

        .signature-name {
            font-size: 15px;

            font-weight: 800;
        }

        .signature-title {
            font-size: 12px;

            font-weight: 600;
        }

        .seal-img {
            width: 100px;
            height: 100px;

            object-fit: contain;

            display: block;

            margin: 0 auto;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER LOGOS
        |--------------------------------------------------------------------------
        */

        .footer-logos {
            display: table;

            width: 230px;
            height: 40px;

            margin: 30px auto 0;

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

            background: #1c9a4b;

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

            .certificate-wrapper {
                width: 210mm;

                height: 297mm;

                padding: 8mm;
            }

            .certificate {
                width: 100%;

                height: 100%;

                max-width: none;

                min-height: auto;

                border-width: 5mm;
            }

            .certificate-inner {
                min-height: auto;
            }

            .document-actions {
                display: none !important;
            }
        }

    </style>

</head>


<body>


<div class="certificate-wrapper">


    <div class="certificate">


        <div class="certificate-inner-border"></div>

        <div class="bead bead-top"></div>
        <div class="bead bead-bottom"></div>


        <div class="certificate-inner">


            {{-- ==========================================================
                 LOGO
            =========================================================== --}}

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

            <div class="rule-line"></div>

            <div class="title">

                Charcoal Lifting Right

            </div>

            <div class="rule-line"></div>


            <div class="certify-text">

                This is to certify that

            </div>

            <div class="member-name">

                {{ $memberName }}

            </div>

            <div class="registered-with">

                is a registered exporter with the

            </div>

            <div class="association">

                NATIONAL ASSOCIATION OF<br>

                CHARCOAL PRODUCERS, DEALERS,<br>

                EXPORTERS AND AFFORESTATION OF NIGERIA

            </div>


            {{-- ==========================================================
                 MEMBERSHIP NUMBER / QR / DATES
            =========================================================== --}}

            <div class="details-row">

                <div class="qr-cell">

                    @if($qrCode)

                        <img
                            src="{{ $qrCode }}"
                            alt="Certificate verification QR code"
                        >

                    @endif

                </div>

                <div class="details-cell">

                    <div class="membership-label">

                        Membership Number

                    </div>

                    <div class="membership-value">

                        {{ $membershipNumber }}

                    </div>

                    <div class="given-date">

                        Given on this date: {{ $issuedAt }}

                    </div>

                    <div class="valid-till">

                        Valid till: <span>{{ $validTill }}</span>

                    </div>

                    <div class="ref-no">

                        REF NO.: <strong>{{ $refNo }}</strong>

                    </div>

                </div>

            </div>


            {{-- ==========================================================
                 SIGNATURES / SEAL
            =========================================================== --}}

            <div class="signature-row">

                <div class="signature-cell">

                    <img
                        src="{{ asset('images/signature-president.png') }}"
                        alt="Signature"
                        class="signature-img"
                    >

                    <div class="signature-name">

                        Edu Babatunde

                    </div>

                    <div class="signature-title">

                        National President

                    </div>

                </div>

                <div class="signature-cell">

                    <img
                        src="{{ asset('images/seal.jpg') }}"
                        alt="Official seal"
                        class="seal-img"
                    >

                </div>

                <div class="signature-cell">

                    <img
                        src="{{ asset('images/signature-secretary.png') }}"
                        alt="Signature"
                        class="signature-img"
                    >

                    <div class="signature-name">

                        Ojei Uche Joseph

                    </div>

                    <div class="signature-title">

                        National Secretary-General

                    </div>

                </div>

            </div>


            {{-- ==========================================================
                 FOOTER LOGOS
            =========================================================== --}}

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


</div>


{{-- ================================================================
     ACTION BUTTONS
================================================================= --}}

@if(!$printMode)

    <div class="document-actions">


        <button
            onclick="window.print()"
        >

            🖨 Print Certificate

        </button>


        <a
            href="{{ route('member.documents.download', $generatedDocument) }}"
        >

            ⬇ Download Certificate

        </a>


    </div>

@endif


</body>

</html>
