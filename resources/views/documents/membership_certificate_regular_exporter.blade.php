
@php
    $profile = $generatedDocument->user->profile;

    $companyName =
        $profile->business_name
        ?? trim(
            ($profile->first_name ?? '') . ' ' .
            ($profile->middle_name ?? '') . ' ' .
            ($profile->surname ?? '')
        );

    $membershipNumber =
        $generatedDocument->field_values['membership_number']
        ?? $generatedDocument->user->membership?->membership_number
        ?? $profile->membership_number
        ?? '';

    $certificateNumber = $generatedDocument->document_number;

    $issuedDate = $generatedDocument->issued_at
        ? $generatedDocument->issued_at->format('F jS, Y')
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

    <title>{{ $certificateNumber }}</title>

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
        |--------------------------------------------------------------------------
        */

        .certificate {
            position: relative;
            width: 100%;
            max-width: 1100px;
            min-height: 770px;
            margin: auto;

            background: #fff;

            border: 28px solid #32b66d;

            padding: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | INNER CERTIFICATE
        |--------------------------------------------------------------------------
        */

        .certificate-inner {
            position: relative;

            min-height: 710px;

            border: 4px solid #10a653;

            padding: 35px 45px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE NUMBER
        |--------------------------------------------------------------------------
        */

        .certificate-number {
            position: absolute;

            top: 15px;
            right: 25px;

            font-size: 14px;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGOS
        |--------------------------------------------------------------------------
        */

        .logos {
            width: 230px;
            max-width: 40%;

            margin: 20px auto 5px;
        }

        .logos img {
            width: 100%;

            display: block;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .title {
            margin-top: 5px;

            font-size: 48px;

            font-weight: bold;

            color: #ed1c24;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | SUBTITLE
        |--------------------------------------------------------------------------
        */

        .subtitle {
            margin-top: 10px;

            font-size: 22px;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER NAME
        |--------------------------------------------------------------------------
        */

        .member-name {
            margin: 15px 0;

            font-size: 36px;

            font-weight: bold;

            text-transform: uppercase;
        }


        /*
        |--------------------------------------------------------------------------
        | REGISTERED TEXT
        |--------------------------------------------------------------------------
        */

        .registered-text {
            font-size: 20px;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | ASSOCIATION
        |--------------------------------------------------------------------------
        */

        .association {
            margin: 15px auto;

            max-width: 850px;

            font-size: 20px;

            font-weight: bold;

            line-height: 1.35;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP NUMBER
        |--------------------------------------------------------------------------
        */

        .membership-number {
            margin-top: 25px;

            font-size: 21px;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        .dated {
            margin-top: 12px;

            font-size: 18px;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | BOTTOM SECTION
        |
        | IMPORTANT:
        | Do NOT use CSS FLEXBOX here.
        |
        | Dompdf handles table layouts much more reliably than flexbox.
        |--------------------------------------------------------------------------
        */

        .bottom-section {
            position: absolute;

            left: 45px;
            right: 45px;

            bottom: 25px;

            width: calc(100% - 90px);

            display: table;

            table-layout: fixed;
        }


        /*
        |--------------------------------------------------------------------------
        | BOTTOM COLUMNS
        |--------------------------------------------------------------------------
        */

        .bottom-item {
            display: table-cell;

            vertical-align: bottom;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | SIGNATURE
        |--------------------------------------------------------------------------
        */

        .signature {
            width: 220px;

            margin: 0 auto;

            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;

            margin-top: 8px;

            padding-top: 5px;
        }

        .signature-name {
            font-size: 18px;

            font-weight: bold;
        }

        .signature-title {
            font-size: 15px;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | SEAL
        |--------------------------------------------------------------------------
        */

        .seal {
            width: 120px;

            margin: 0 auto;

            text-align: center;
        }

        .seal img {
            width: 100px;
            height: 100px;

            display: block;

            margin: 0 auto;
        }


        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        .qr-section {
            width: 150px;

            margin: 0 auto;

            text-align: center;
        }

        .qr-section img {
            width: 120px;
            height: 120px;

            display: block;

            margin: 0 auto;
        }

        .verify-text {
            margin-top: 4px;

            font-size: 11px;
        }

        .verify-url {
            font-size: 8px;

            word-break: break-all;

            overflow-wrap: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {
            max-width: 1100px;

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

            background: #10a653;

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
                size: A4 landscape;

                margin: 0;
            }

            html,
            body {
                width: 297mm;

                height: 210mm;

                margin: 0;

                padding: 0;

                background: #fff;
            }


            /*
            |--------------------------------------------------------------------------
            | WRAPPER
            |--------------------------------------------------------------------------
            */

            .certificate-wrapper {
                width: 297mm;

                height: 210mm;

                padding: 0;
            }


            /*
            |--------------------------------------------------------------------------
            | CERTIFICATE
            |--------------------------------------------------------------------------
            */

            .certificate {
                width: 297mm;

                height: 210mm;

                max-width: none;

                min-height: auto;

                margin: 0;

                border-width: 8mm;

                padding: 3mm;
            }


            /*
            |--------------------------------------------------------------------------
            | INNER CERTIFICATE
            |--------------------------------------------------------------------------
            */

            .certificate-inner {
                width: 100%;

                height: 190mm;

                min-height: 190mm;

                padding: 8mm 12mm;
            }


            /*
            |--------------------------------------------------------------------------
            | BOTTOM SECTION
            |
            | Keep this as TABLE for Dompdf compatibility.
            |--------------------------------------------------------------------------
            */

            .bottom-section {
                left: 12mm;

                right: 12mm;

                bottom: 8mm;

                width: calc(100% - 24mm);

                display: table;

                table-layout: fixed;
            }


            /*
            |--------------------------------------------------------------------------
            | BOTTOM COLUMNS
            |--------------------------------------------------------------------------
            */

            .bottom-item {
                display: table-cell;

                vertical-align: bottom;

                text-align: center;
            }


            /*
            |--------------------------------------------------------------------------
            | SIGNATURES
            |--------------------------------------------------------------------------
            */

            .signature {
                width: 220px;

                margin: 0 auto;
            }


            /*
            |--------------------------------------------------------------------------
            | SEAL
            |--------------------------------------------------------------------------
            */

            .seal {
                width: 120px;

                margin: 0 auto;
            }

            .seal img {
                width: 100px;

                height: 100px;

                display: block;

                margin: 0 auto;
            }


            /*
            |--------------------------------------------------------------------------
            | QR
            |--------------------------------------------------------------------------
            */

            .qr-section {
                width: 150px;

                margin: 0 auto;
            }

            .qr-section img {
                width: 120px;

                height: 120px;

                display: block;

                margin: 0 auto;
            }


            /*
            |--------------------------------------------------------------------------
            | HIDE ACTION BUTTONS
            |--------------------------------------------------------------------------
            */

            .document-actions {
                display: none !important;
            }
        }

    </style>

</head>


<body>


<div class="certificate-wrapper">


    <div class="certificate">


        <div class="certificate-inner">


            {{-- ==========================================================
                 CERTIFICATE NUMBER
            =========================================================== --}}

            <div class="certificate-number">

                Cert No:
                {{ $certificateNumber }}

            </div>


            {{-- ==========================================================
                 LOGOS
            =========================================================== --}}

            <div class="logos">

                <img
                    src="{{ asset('images/logos.jpg') }}"
                    alt="Association logos"
                >

            </div>


            {{-- ==========================================================
                 TITLE
            =========================================================== --}}

            <div class="title">

                Certificate of Membership

            </div>


            {{-- ==========================================================
                 SUBTITLE
            =========================================================== --}}

            <div class="subtitle">

                This is to certify that

            </div>


            {{-- ==========================================================
                 MEMBER / COMPANY NAME
            =========================================================== --}}

            <div class="member-name">

                {{ $companyName }}

            </div>


            {{-- ==========================================================
                 REGISTERED TEXT
            =========================================================== --}}

            <div class="registered-text">

                is a Registered Export Member with

            </div>


            {{-- ==========================================================
                 ASSOCIATION NAME
            =========================================================== --}}

            <div class="association">

                NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS, DEALERS,<br>

                EXPORTERS AND AFFORESTATION OF NIGERIA

            </div>


            {{-- ==========================================================
                 MEMBERSHIP NUMBER
            =========================================================== --}}

            <div class="membership-number">

                MEMBERSHIP NO.:
                {{ $membershipNumber }}

            </div>


            {{-- ==========================================================
                 ISSUE DATE
            =========================================================== --}}

            <div class="dated">

                Dated this:
                {{ $issuedDate }}

            </div>


            {{-- ==========================================================
                 BOTTOM SECTION
                 PRESIDENT | SEAL | QR | SECRETARY-GENERAL
            =========================================================== --}}

            <div class="bottom-section">


                {{-- ======================================================
                     NATIONAL PRESIDENT
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="signature">

                        <div class="signature-name">

                            Edu Babatunde

                        </div>

                        <div class="signature-line">

                            <strong>
                                National President
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- ======================================================
                     OFFICIAL SEAL
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="seal">

                        <img
                            src="{{ asset('images/seal.jpg') }}"
                            alt="Official seal"
                        >

                    </div>

                </div>


                {{-- ======================================================
                     QR CODE
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="qr-section">


                        @if($qrCode)

                            <img
                                src="{{ $qrCode }}"
                                alt="Certificate verification QR code"
                            >

                        @endif


                        <div class="verify-text">

                            Scan to Verify

                        </div>


                        <div class="verify-url">

                            {{ $verificationUrl }}

                        </div>


                    </div>

                </div>


                {{-- ======================================================
                     NATIONAL SECRETARY-GENERAL
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="signature">

                        <div class="signature-name">

                            Ojei Uche Joeseph

                        </div>

                        <div class="signature-line">

                            <strong>
                                National Secretary-General
                            </strong>

                        </div>

                    </div>

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
