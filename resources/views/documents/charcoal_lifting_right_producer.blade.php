@php
    /*
    |--------------------------------------------------------------------------
    | RESOLVE FIELD VALUES
    |--------------------------------------------------------------------------
    |
    | NACPDEAN Charcoal Producing Right - Producer.
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
        | NOTE: the reference has a twisted-rope motif running down
        | each side plus 2x2 dot-square corner ornaments. The dot
        | squares are reproduced below (table-based, Dompdf-safe,
        | same technique as the afforestation receipt). The woven
        | rope pattern itself is genuine illustration work and can't
        | be reproduced reliably with plain CSS in Dompdf — a solid
        | border is used along the sides instead. For pixel-perfect
        | fidelity, export the rope motif as a repeatable PNG strip
        | and set it as a background-image on .certificate.
        |--------------------------------------------------------------------------
        */

        .certificate {
            position: relative;
            width: 100%;
            max-width: 760px;
            min-height: 1030px;
            margin: auto;

            background: #fff;

            border: 12px solid #1c9a4b;

            padding: 8px;
        }

        .certificate-inner-border {
            position: absolute;

            top: 16px;
            left: 16px;
            right: 16px;
            bottom: 16px;

            border: 1px solid #1c9a4b;

            pointer-events: none;
        }


        /*
        |--------------------------------------------------------------------------
        | CORNER ORNAMENTS (2x2 dot squares, dompdf-safe: table layout)
        |--------------------------------------------------------------------------
        */

        .corner {
            position: absolute;

            width: 36px;
            height: 36px;

            background: #1c9a4b;

            border: 3px solid #fff;

            display: table;

            z-index: 5;
        }

        .corner-row {
            display: table-row;
        }

        .corner-dot {
            display: table-cell;

            width: 50%;
            height: 18px;

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
            top: -6px;
            left: -6px;
        }

        .corner-top-right {
            top: -6px;
            right: -6px;
        }

        .corner-bottom-left {
            bottom: -6px;
            left: -6px;
        }

        .corner-bottom-right {
            bottom: -6px;
            right: -6px;
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
        | REF NO. (top right, above the title — unlike the exporter /
        | dealer certificates, which put it lower down)
        |--------------------------------------------------------------------------
        */

        .ref-no-top {
            margin-top: 18px;

            text-align: right;

            font-size: 13px;
        }

        .ref-no-top strong {
            font-weight: 800;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .title {
            margin-top: 16px;

            font-size: 30px;

            font-weight: 800;

            letter-spacing: .5px;

            text-transform: uppercase;
        }

        .role-badge {
            display: inline-block;

            margin-top: 12px;

            padding: 6px 26px;

            background: #6aa624;
            border-radius: 20px;

            color: #fff;

            font-size: 15px;

            font-weight: 800;

            letter-spacing: .5px;

            text-transform: uppercase;
        }

        .certify-text {
            margin-top: 20px;

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
        |
        | QR sits on the RIGHT (same as the dealer certificate).
        | No REF NO. row here — it already appeared at the top.
        |--------------------------------------------------------------------------
        */

        .details-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 30px;
        }

        .details-cell {
            display: table-cell;

            vertical-align: top;

            text-align: center;

            font-size: 15px;
        }

        .qr-cell {
            display: table-cell;

            width: 130px;

            vertical-align: top;

            text-align: right;
        }

        .qr-cell img {
            width: 105px;
            height: 105px;

            object-fit: contain;

            display: block;

            margin-left: auto;
        }

        .membership-label {
            color: #e30613;

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


        /*
        |--------------------------------------------------------------------------
        | SIGNATURES / SEAL
        |--------------------------------------------------------------------------
        */

        .signature-row {
            display: table;

            width: 100%;

            table-layout: fixed;

            margin-top: 45px;
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
                 REF NO. (top right)
            =========================================================== --}}

            <div class="ref-no-top">

                REF NO.: <strong>{{ $refNo }}</strong>

            </div>


            {{-- ==========================================================
                 TITLE + ROLE BADGE
            =========================================================== --}}

            <div class="title">

                Charcoal Producing Right

            </div>

            <div class="role-badge">

                Producer

            </div>


            <div class="certify-text">

                This is to certify that

            </div>

            <div class="member-name">

                {{ $memberName }}

            </div>

            <div class="registered-with">

                is a registered producer with the

            </div>

            <div class="association">

                NATIONAL ASSOCIATION OF<br>

                CHARCOAL PRODUCERS, DEALERS,<br>

                EXPORTERS AND AFFORESTATION OF NIGERIA

            </div>


            {{-- ==========================================================
                 MEMBERSHIP NUMBER / DATES / QR
            =========================================================== --}}

            <div class="details-row">

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

                </div>

                <div class="qr-cell">

                    @if($qrCode)

                        <img
                            src="{{ $qrCode }}"
                            alt="Certificate verification QR code"
                        >

                    @endif

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
