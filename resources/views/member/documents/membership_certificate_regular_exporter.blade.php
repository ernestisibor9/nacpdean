<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $document->name ?? 'Certificate of Membership' }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: #eeeeee;
            font-family: "Times New Roman", Times, serif;
        }

        .certificate-page {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .certificate {
            position: relative;
            width: min(100%, 937px);
            aspect-ratio: 937 / 655;
            overflow: hidden;
            background: #ffffff;
            border: 28px solid #32b66d;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .certificate::before {
            content: "";
            position: absolute;
            inset: -28px;
            pointer-events: none;
            background:
                repeating-radial-gradient(
                    ellipse at center,
                    rgba(255, 255, 255, 0.25) 0 2px,
                    transparent 2px 5px
                );
            opacity: 0.75;
        }

        .certificate-inner {
            position: absolute;
            top: 4.8%;
            left: 4.8%;
            right: 4.8%;
            bottom: 4.8%;
            border: 4px solid #10a653;
            background: #ffffff;
        }

        .certificate-content {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .certificate-number {
            position: absolute;
            top: 3.2%;
            right: 4.2%;

            font-family: Arial, Helvetica, sans-serif;
            font-size: clamp(8px, 1.35vw, 14px);
            font-weight: 700;
            white-space: nowrap;
        }

        .certificate-title {
            position: absolute;
            top: 2%;
            left: 4%;
            width: 92%;
            height: 27%;
            overflow: visible;
        }

        .certificate-title text {
            fill: #ed1c24;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 58px;
            font-weight: 400;
        }

        .certificate-subtitle {
            position: absolute;
            top: 21%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: clamp(12px, 2.05vw, 22px);
            font-style: italic;
        }

        .company-name {
            position: absolute;
            top: 30%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: clamp(17px, 3.05vw, 32px);
            line-height: 1;
            white-space: nowrap;
        }

        .registered-text {
            position: absolute;
            top: 37.5%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: clamp(10px, 1.7vw, 18px);
            font-style: italic;
        }

        .association-name {
            position: absolute;
            top: 41%;
            left: 0;
            width: 100%;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: clamp(7px, 1.45vw, 15px);
            font-weight: 800;
            line-height: 1.1;
        }

        .membership-number {
            position: absolute;
            top: 49.5%;
            left: 0;
            width: 100%;
            text-align: center;
            color: #ed1717;
            font-family: Arial, Helvetica, sans-serif;
            font-size: clamp(9px, 1.7vw, 17px);
            font-weight: 800;
        }

        .dated {
            position: absolute;
            top: 55.3%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: clamp(8px, 1.4vw, 15px);
            font-style: italic;
        }

        .certificate-logos {
            position: absolute;
            top: 63%;
            left: 27%;
            width: 46%;
            height: 10%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .president-block {
            position: absolute;
            left: 7%;
            bottom: 0;
            font-weight: bold;
            line-height: 1;
        }

        .president-block h5,
        .president-block p,
        .secretary-block h5,
        .secretary-block p {
            margin: 4px 0 0;
        }

        .sign-line {
            background-color: black;
            height: 1.3px;
            width: 110px;
        }

        .official-seal {
            position: absolute;
            left: 39%;
            bottom: -1%;
            width: 17%;
            height: 22%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .verification {
            position: absolute;
            left: 57%;
            bottom: 3%;
            height: 14%;
            object-fit: contain;
            border: 5px solid black;
            padding: 2.5px;
            border-radius: 10px;
        }

        .secretary-block {
            position: absolute;
            right: 6%;
            bottom: 0;
            font-weight: bold;
            line-height: 1;
        }

        @media (max-width: 768px) {

            .certificate-page {
                padding: 10px;
            }

            .certificate {
                border-width: 22px;
            }

            .certificate-inner {
                border-width: 3px;
            }

            .certificate-title text {
                font-size: 58px;
            }
        }

        @media (max-width: 576px) {

            .certificate-page {
                padding: 0;
            }

            .certificate {
                width: 100vw;
                border-width: 18px;
                box-shadow: none;
            }

            .certificate-inner {
                border-width: 2px;
            }

            .certificate-title {
                top: 2.5%;
            }

            .certificate-title text {
                font-size: 58px;
            }

            .certificate-number {
                top: 3%;
                right: 3%;
            }

            .president-block h5,
            .president-block p,
            .secretary-block h5,
            .secretary-block p {
                font-size: 12px;
                font-weight: bold;
            }

            .sign-line {
                width: 75px;
            }

            .secretary-block {
                right: 7%;
            }
        }

        @media (max-width: 380px) {

            .certificate {
                border-width: 14px;
            }

            .certificate-title text {
                font-size: 55px;
            }

            .president-block h5,
            .president-block p,
            .secretary-block h5,
            .secretary-block p {
                font-size: 12px;
                font-weight: bold;
            }

            .sign-line {
                width: 75px;
            }

            .secretary-block {
                right: 3%;
            }
        }

        @media print {

            @page {
                size: A4 landscape;
                margin: 0;
            }

            html,
            body {
                width: 100%;
                height: 100%;
            }

            body {
                background: #ffffff;
            }

            .certificate-page {
                min-height: 100vh;
                padding: 0;
            }

            .certificate {
                width: 100vw;
                max-width: none;
                box-shadow: none;
            }
        }
    </style>
</head>

@php
    $values = $generatedDocument->field_values ?? [];

    $companyName =
        $values['company_name']
        ?? $values['business_name']
        ?? $values['full_name']
        ?? $generatedDocument->user->profile->business_name
        ?? $generatedDocument->user->profile->first_name . ' '
        . $generatedDocument->user->profile->surname;

    $membershipNumber =
        $values['membership_number']
        ?? $generatedDocument->user->profile->membership_number
        ?? '';

    $certificateNumber =
        $values['certificate_number']
        ?? $generatedDocument->document_number;

    $issuedDate =
        $generatedDocument->issued_at
            ? $generatedDocument->issued_at->format('F jS, Y')
            : '';

    $qrCode = $qrCode ?? null;
@endphp

<body>

<div class="certificate-page">

    <div class="certificate">

        <div class="certificate-inner">

            <div class="certificate-content">

                {{-- Certificate number --}}
                <div class="certificate-number">
                    Cert No: {{ $certificateNumber }}
                </div>

                {{-- Curved heading --}}
                <svg
                    class="certificate-title"
                    viewBox="0 0 900 220"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-label="Certificate of Membership"
                >
                    <defs>
                        <path
                            id="heading-curve"
                            d="M 65 185 Q 450 -25 835 185"
                        />
                    </defs>

                    <text>
                        <textPath
                            href="#heading-curve"
                            startOffset="50%"
                            text-anchor="middle"
                        >
                            Certificate of Membership
                        </textPath>
                    </text>
                </svg>

                {{-- Main certificate text --}}
                <div class="certificate-subtitle">
                    This is to certify that
                </div>

                <div class="company-name">
                    {{ strtoupper($companyName) }}
                </div>

                <div class="registered-text">
                    is a Registered Export Member with
                </div>

                <div class="association-name">
                    NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS, DEALERS,<br>
                    EXPORTERS AND AFFORESTATION OF NIGERIA
                </div>

                <div class="membership-number">
                    MEMBERSHIP NO.: {{ $membershipNumber }}
                </div>

                <div class="dated">
                    Dated this: {{ $issuedDate }}
                </div>

                {{-- Association logos --}}
                <img
                    src="{{ asset('images/logos.jpg') }}"
                    class="certificate-logos"
                    alt="Association logos"
                >

                {{-- President --}}
                <div class="president-block">

                    <div class="sign-line"></div>

                    <h5>Edu Babatunde</h5>

                    <p>National President</p>

                </div>

                {{-- Official seal --}}
                <img
                    src="{{ asset('images/seal.jpg') }}"
                    class="official-seal"
                    alt="Official seal"
                >

                {{-- QR verification --}}
                @if($qrCode)
                    <img
                        src="{{ $qrCode }}"
                        class="verification"
                        alt="Document verification QR Code"
                    >
                @endif

                {{-- Secretary General --}}
                <div class="secretary-block">

                    <div class="sign-line"></div>

                    <h5>Ojei Uche Joeseph</h5>

                    <p>National Secretary-General</p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
