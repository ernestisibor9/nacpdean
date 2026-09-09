@php
    $fields = collect($generatedDocument->field_values ?? []);

    $memberName = $fields->get('member_name', '');

    $membershipNumber = $fields->get('membership_number', '');

    $refNo = $fields->get(
        'ref_no',
        $generatedDocument->document_number
    );

    $issuedAt = $fields->get('issued_at')
        ? \Carbon\Carbon::parse($fields->get('issued_at'))->format('d F Y')
        : '';

    $validTill = $fields->get('expires_at')
        ? \Carbon\Carbon::parse($fields->get('expires_at'))->format('jS F, Y')
        : '';

    $verificationUrl = $fields->get(
        'verification_url',
        route('documents.verify', $generatedDocument->tracking_code)
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
        | CERTIFICATE FRAME
        |
        | The ornamental Celtic-knot corners, the segmented bead border, and
        | the faint radiating guilloche watermark are all drawn as one inline
        | SVG layered behind/around the content, rather than an image, so the
        | frame scales with the certificate at any size. If your PDF renderer
        | is Dompdf (which has weak/partial SVG support), rasterize this SVG
        | to a transparent PNG once and set it as a background-image on
        | .certificate instead — the markup and IDs below stay the same either
        | way. wkhtmltopdf / Chromium-based renderers handle the inline SVG
        | natively.
        |--------------------------------------------------------------------------
        */

        .certificate {
            position: relative;
            width: 100%;
            max-width: 760px;
            min-height: 1060px;
            margin: auto;
            background: #fffefb;
        }

        .certificate-frame-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .certificate-inner {
            position: relative;
            z-index: 2;

            min-height: 1060px;

            padding: 78px 70px 40px;

            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        .wordmark {
            display: inline-flex;
            align-items: flex-end;
            justify-content: center;
            gap: 2px;
        }

        .wordmark-text {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #1a1a1a;
            line-height: 1;
        }

        .wordmark-leaf {
            width: 34px;
            height: 46px;
            margin-bottom: 2px;
        }

        .cac-no {
            margin-top: 2px;

            font-size: 10px;
            font-weight: 600;

            letter-spacing: .5px;
            color: #333;
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

            margin: 20px auto 10px;

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
            margin-top: 16px;

            font-family: "Brush Script MT", "Segoe Script", cursive;

            font-style: italic;

            font-size: 19px;

            font-weight: bold;

            color: #e30613;
        }

        .member-name {
            margin-top: 12px;

            font-size: 33px;
            font-family: Georgia, "Times New Roman", serif;

            font-weight: 700;

            text-transform: uppercase;
        }

        .registered-with {
            margin-top: 12px;

            font-family: "Brush Script MT", "Segoe Script", cursive;

            font-style: italic;

            font-size: 18px;

            font-weight: bold;

            color: #1c9a4b;
        }

        .association {
            margin: 8px auto 0;

            max-width: 600px;

            font-size: 16px;

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

            margin-top: 26px;
        }

        .qr-cell {
            display: table-cell;

            width: 130px;

            vertical-align: top;

            text-align: left;
        }

        .qr-cell img,
        .qr-placeholder {
            width: 100px;
            height: 100px;

            object-fit: contain;

            display: block;
        }

        .qr-placeholder {
            border: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #555;
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
            margin-top: 14px;

            font-size: 15px;
        }

        .valid-till span {
            color: #e30613;

            font-weight: 600;
        }

        .ref-no {
            margin-top: 16px;

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

            margin-top: 34px;
        }

        .signature-cell {
            display: table-cell;

            vertical-align: bottom;

            width: 33.33%;

            text-align: center;
        }

        .signature-img {
            height: 40px;

            max-width: 150px;

            object-fit: contain;

            display: block;

            margin: 0 auto 4px;
        }

        .signature-scribble {
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: 24px;
            height: 40px;
            color: #2a2a70;
        }

        .signature-scribble.dark {
            color: #1a1a1a;
        }

        .signature-name {
            font-size: 14px;

            font-weight: 800;
        }

        .signature-title {
            font-size: 11px;

            font-weight: 600;
        }

        .seal-svg {
            width: 96px;
            height: 96px;

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

            width: 320px;
            max-width: 90%;
            height: 40px;

            margin: 26px auto 0;

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
            max-width: 70px;
            max-height: 38px;

            width: auto;
            height: auto;

            object-fit: contain;

            display: inline-block;
        }

        .footer-logo-text {
            font-size: 8px;
            font-weight: 700;
            color: #333;
            line-height: 1.2;
        }

        .footer-logo-text.green {
            color: #1c9a4b;
        }

        .footer-logo-text.wordmark-mini {
            font-size: 12px;
            font-weight: 800;
        }

        .footer-logo-text.wordmark-mini span {
            color: #1c9a4b;
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


        {{-- ==========================================================
             DECORATIVE FRAME: guilloche watermark + bead border +
             Celtic-knot corner flourishes, all in one inline SVG.
        =========================================================== --}}

        <svg
            class="certificate-frame-svg"
            viewBox="0 0 800 1060"
            preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
        >

            <defs>
                <g id="guilloche-fan">
                    @php $lineCount = 90; @endphp
                    @for ($i = 0; $i < $lineCount; $i++)
                        @php $angle = ($i / $lineCount) * 360; @endphp
                        <line
                            x1="400" y1="530"
                            x2="{{ 400 + 900 * cos(deg2rad($angle)) }}"
                            y2="{{ 530 + 900 * sin(deg2rad($angle)) }}"
                            stroke="#c9c17a"
                            stroke-width="0.6"
                        />
                    @endfor
                </g>

                <g id="corner-scroll">
                    <path
                        d="M 2,0 L 2,0.5 L 1.9,1 L 1.7,1.5 L 1.4,1.9 L 0.9,2.2 L 0.4,2.5 L -0.2,2.6 L -0.8,2.6 L -1.4,2.4 L -2,2.1 L -2.5,1.6 L -2.9,1 L -3.1,0.3 L -3.2,-0.4 L -3.1,-1.2 L -2.8,-1.9 L -2.3,-2.6 L -1.7,-3.2 L -0.9,-3.6 L -0.1,-3.8 L 0.8,-3.8 L 1.7,-3.6 L 2.5,-3.1 L 3.3,-2.5 L 3.8,-1.7 L 4.2,-0.8 L 4.4,0.2 L 4.3,1.2 L 4,2.2 L 3.4,3.2 L 2.6,3.9 L 1.7,4.5 L 0.6,4.9 L -0.6,5 L -1.7,4.8 L -2.8,4.3 L -3.8,3.6 L -4.6,2.7 L -5.2,1.5 L -5.5,0.3 L -5.5,-1 L -5.2,-2.3 L -4.6,-3.5 L -3.7,-4.5 L -2.6,-5.4 L -1.3,-5.9 L 0.1,-6.1 L 1.5,-6 L 2.9,-5.6 L 4.2,-4.8 L 5.3,-3.8 L 6.1,-2.5 L 6.6,-1 L 6.7,0.6 L 6.5,2.2 L 5.9,3.6 L 5,5 L 3.7,6.1 L 2.2,6.8"
                        fill="none"
                        stroke="#1c9a4b"
                        stroke-width="3.4"
                        stroke-linecap="round"
                        transform="scale(4.6)"
                    />
                    <path
                        d="M 2,0 L 2,0.5 L 1.9,1 L 1.7,1.5 L 1.4,1.9 L 0.9,2.2 L 0.4,2.5 L -0.2,2.6 L -0.8,2.6 L -1.4,2.4 L -2,2.1 L -2.5,1.6 L -2.9,1 L -3.1,0.3 L -3.2,-0.4 L -3.1,-1.2 L -2.8,-1.9 L -2.3,-2.6 L -1.7,-3.2 L -0.9,-3.6 L -0.1,-3.8 L 0.8,-3.8 L 1.7,-3.6 L 2.5,-3.1 L 3.3,-2.5 L 3.8,-1.7 L 4.2,-0.8 L 4.4,0.2 L 4.3,1.2 L 4,2.2 L 3.4,3.2 L 2.6,3.9 L 1.7,4.5 L 0.6,4.9 L -0.6,5 L -1.7,4.8 L -2.8,4.3 L -3.8,3.6 L -4.6,2.7 L -5.2,1.5 L -5.5,0.3 L -5.5,-1 L -5.2,-2.3 L -4.6,-3.5 L -3.7,-4.5 L -2.6,-5.4 L -1.3,-5.9 L 0.1,-6.1 L 1.5,-6 L 2.9,-5.6 L 4.2,-4.8 L 5.3,-3.8 L 6.1,-2.5 L 6.6,-1 L 6.7,0.6 L 6.5,2.2 L 5.9,3.6 L 5,5 L 3.7,6.1 L 2.2,6.8"
                        fill="none"
                        stroke="#1c9a4b"
                        stroke-width="3.4"
                        stroke-linecap="round"
                        transform="scale(-4.6,4.6) translate(-40,-15)"
                    />
                </g>
            </defs>

            {{-- faint radiating watermark, clipped to the card --}}
            <clipPath id="cardClip">
                <rect x="16" y="16" width="768" height="1028" rx="40" />
            </clipPath>
            <g clip-path="url(#cardClip)" opacity="0.35">
                <use xlink:href="#guilloche-fan" href="#guilloche-fan" />
            </g>

            {{-- outer bead-segmented pill border --}}
            <rect
                x="16" y="16" width="768" height="1028" rx="40"
                fill="none" stroke="#1c9a4b" stroke-width="9"
            />
            <rect
                x="30" y="30" width="740" height="1000" rx="32"
                fill="none" stroke="#1c9a4b" stroke-width="1.4"
            />

            {{-- beads: top / bottom centre, and mid-way down each side --}}
            <circle cx="400" cy="16"   r="15" fill="#1c9a4b" stroke="#fff" stroke-width="3" />
            <circle cx="400" cy="1044" r="15" fill="#1c9a4b" stroke="#fff" stroke-width="3" />
            <circle cx="16"  cy="300"  r="11" fill="#1c9a4b" stroke="#fff" stroke-width="2.5" />
            <circle cx="16"  cy="760"  r="11" fill="#1c9a4b" stroke="#fff" stroke-width="2.5" />
            <circle cx="784" cy="300"  r="11" fill="#1c9a4b" stroke="#fff" stroke-width="2.5" />
            <circle cx="784" cy="760"  r="11" fill="#1c9a4b" stroke="#fff" stroke-width="2.5" />

            {{-- corner flourishes --}}
            <use xlink:href="#corner-scroll" href="#corner-scroll" transform="translate(16,16)" />
            <use xlink:href="#corner-scroll" href="#corner-scroll" transform="translate(784,16) scale(-1,1)" />
            <use xlink:href="#corner-scroll" href="#corner-scroll" transform="translate(16,1044) scale(1,-1)" />
            <use xlink:href="#corner-scroll" href="#corner-scroll" transform="translate(784,1044) scale(-1,-1)" />

        </svg>


        <div class="certificate-inner">


            {{-- ==========================================================
                 LOGO
            =========================================================== --}}

            <div class="wordmark">

                <span class="wordmark-text">nacpde</span>

                <svg class="wordmark-leaf" viewBox="0 0 40 55" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 50 C6 30 14 10 34 4" fill="none" stroke="#1c9a4b" stroke-width="3" stroke-linecap="round"/>
                    <path d="M18 22 C12 18 8 12 10 4 C18 6 24 12 24 20 Z" fill="#3fae5c"/>
                    <path d="M28 12 C24 8 22 4 24 0 C30 2 33 8 31 14 Z" fill="#6fc482"/>
                </svg>

                <span class="wordmark-text" style="color:#1c9a4b;">n</span>

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

                    @else

                        <div class="qr-placeholder">[ QR Code ]</div>

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

                    @if(isset($signaturePresidentUrl) && $signaturePresidentUrl)
                        <img
                            src="{{ $signaturePresidentUrl }}"
                            alt="Signature"
                            class="signature-img"
                        >
                    @else
                        <div class="signature-scribble">&#10022;</div>
                    @endif

                    <div class="signature-name">

                        Edu Babatunde

                    </div>

                    <div class="signature-title">

                        National President

                    </div>

                </div>

                <div class="signature-cell">

                    {{-- wax seal, drawn as SVG since no seal image is on hand --}}
                    <svg class="seal-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <radialGradient id="sealGrad" cx="40%" cy="35%" r="70%">
                                <stop offset="0%" stop-color="#e6483f" />
                                <stop offset="55%" stop-color="#c11f24" />
                                <stop offset="100%" stop-color="#8f1418" />
                            </radialGradient>
                        </defs>
                        <path
                            d="M50 4
                               C58 4 60 12 66 10 C72 8 78 14 76 20
                               C82 22 84 30 78 34 C84 38 84 46 78 48
                               C84 52 82 60 76 62 C78 68 72 74 66 72
                               C60 78 52 76 50 82 C48 76 40 78 34 72
                               C28 74 22 68 24 62 C18 60 16 52 22 48
                               C16 46 16 38 22 34 C16 30 18 22 24 20
                               C22 14 28 8 34 10 C40 12 42 4 50 4 Z"
                            fill="url(#sealGrad)"
                        />
                        <circle cx="50" cy="46" r="30" fill="none" stroke="#f3d9b1" stroke-width="1" opacity="0.5" />
                        <text x="50" y="43" text-anchor="middle" font-size="10" font-weight="800" fill="#f3d9b1" font-family="Georgia, serif">nacpdean</text>
                        <text x="50" y="56" text-anchor="middle" font-size="6" fill="#f3d9b1" font-family="Arial, sans-serif">OFFICIAL SEAL</text>
                    </svg>

                </div>

                <div class="signature-cell">

                    @if(isset($signatureSecretaryUrl) && $signatureSecretaryUrl)
                        <img
                            src="{{ $signatureSecretaryUrl }}"
                            alt="Signature"
                            class="signature-img"
                        >
                    @else
                        <div class="signature-scribble dark">&#10022;</div>
                    @endif

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

                    @if(isset($coatOfArmsUrl) && $coatOfArmsUrl)
                        <img src="{{ $coatOfArmsUrl }}" alt="Coat of Arms" class="footer-logo">
                    @else
                        <div class="footer-logo-text">Federal Ministry<br>of Industry, Trade<br>&amp; Investment</div>
                    @endif

                </div>

                <div class="footer-logo-column">

                    @if(isset($facanLogoUrl) && $facanLogoUrl)
                        <img src="{{ $facanLogoUrl }}" alt="FACAN" class="footer-logo">
                    @else
                        <div class="footer-logo-text green">FACAN</div>
                    @endif

                </div>

                <div class="footer-logo-column">

                    <div class="footer-logo-text wordmark-mini">nacpde<span>an</span></div>

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
