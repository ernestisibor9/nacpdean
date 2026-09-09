@php
    $fields = collect($generatedDocument->field_values ?? []);

    /*
    |--------------------------------------------------------------------------
    | DYNAMIC DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $memberName = $fields->get('member_name', '');

    $membershipNumber = $fields->get('membership_number', '');

    $refNo = $fields->get(
        'ref_no',
        $generatedDocument->document_number
    );

    $issuedAt = $fields->get('issued_at')
        ? \Carbon\Carbon::parse(
            $fields->get('issued_at')
        )->format('d F Y')
        : '';

    $validTill = $fields->get('expires_at')
        ? \Carbon\Carbon::parse(
            $fields->get('expires_at')
        )->format('jS F, Y')
        : '';

    $verificationUrl = $fields->get(
        'verification_url',
        route(
            'documents.verify',
            $generatedDocument->tracking_code
        )
    );
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Charcoal Lifting Right</title>

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
            padding: 0;
            background: #eee;
            font-family: "Poppins", Arial, sans-serif;
        }

        .certificate-wrapper {
            padding: 30px;
        }

        .certificate {
            position: relative;
            max-width: 760px;
            min-height: 1030px;
            margin: 0 auto;
            background: #fff;
            border: 10px solid #1c9a4b;
            border-radius: 45px;
            padding: 8px;
            overflow: hidden;
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

        .certificate-inner {
            position: relative;
            z-index: 2;
            min-height: 990px;
            padding: 40px 55px 30px;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | DECORATIVE BEADS
        |--------------------------------------------------------------------------
        */

        .beads {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 0 auto 15px;
        }

        .bead {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #1c9a4b;
        }

        .bead:nth-child(2),
        .bead:nth-child(4) {
            width: 7px;
            height: 7px;
        }

        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        .wordmark {
            width: 230px;
            margin: 0 auto 5px;
        }

        .wordmark img {
            width: 100%;
            height: auto;
            display: block;
        }

        .cac-number {
            font-size: 11px;
            font-weight: bold;
            color: #444;
            margin-top: 5px;
        }

        /*
        |--------------------------------------------------------------------------
        | GREEN RULE
        |--------------------------------------------------------------------------
        */

        .green-rule {
            width: 100%;
            height: 2px;
            background: #1c9a4b;
            margin: 18px auto 28px;
        }

        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .certificate-title {
            font-size: 31px;
            font-weight: 700;
            color: #1c9a4b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }

        .certify-text {
            font-size: 17px;
            color: #d71920;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .member-name {
            font-size: 29px;
            font-weight: 700;
            color: #222;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .registered-text {
            font-size: 16px;
            color: #1c9a4b;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .association {
            font-size: 15px;
            line-height: 1.55;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | DETAILS
        |--------------------------------------------------------------------------
        */

        .details-row {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }

        .details-row td {
            vertical-align: middle;
        }

        .qr-cell {
            width: 30%;
            text-align: center;
        }

        .qr-code {
            width: 115px;
            height: 115px;
            object-fit: contain;
            display: block;
            margin: 0 auto 8px;
        }

        .verify-text {
            font-size: 9px;
            color: #555;
            line-height: 1.4;
        }

        .verify-url {
            font-size: 8px;
            color: #1c9a4b;
            word-break: break-all;
            margin-top: 3px;
        }

        .details-cell {
            width: 70%;
            text-align: left;
            padding-left: 25px;
        }

        .detail-item {
            margin-bottom: 13px;
        }

        .detail-label {
            display: block;
            font-size: 11px;
            color: #777;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .detail-value {
            display: block;
            font-size: 16px;
            color: #222;
            font-weight: 700;
        }

        /*
        |--------------------------------------------------------------------------
        | SIGNATURES
        |--------------------------------------------------------------------------
        */

        .signature-row {
            display: table;
            width: 100%;
            margin-top: 45px;
            table-layout: fixed;
        }

        .signature-box {
            display: table-cell;
            width: 33.333%;
            vertical-align: bottom;
            text-align: center;
        }

        .signature-image {
            height: 58px;
            max-width: 150px;
            object-fit: contain;
            display: block;
            margin: 0 auto 5px;
        }

        .signature-line {
            width: 145px;
            border-top: 1px solid #333;
            margin: 0 auto 7px;
        }

        .signatory-name {
            font-size: 13px;
            font-weight: 700;
            color: #222;
        }

        .signatory-title {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .seal-image {
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .footer-logo {
            width: 100px;
            max-height: 55px;
            object-fit: contain;
        }

        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 25px auto;
        }

        .document-actions button,
        .document-actions a {
            border: none;
            padding: 11px 20px;
            border-radius: 6px;
            background: #1c9a4b;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        @media print {

            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .certificate-wrapper {
                padding: 0;
            }

            .certificate {
                width: 210mm;
                min-height: 297mm;
                max-width: none;
                border-radius: 0;
                margin: 0;
                border-width: 10px;
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

        <div class="certificate-inner">

            {{-- Decorative beads --}}
            <div class="beads">
                <span class="bead"></span>
                <span class="bead"></span>
                <span class="bead"></span>
                <span class="bead"></span>
                <span class="bead"></span>
            </div>

            {{-- Logo --}}
            <div class="wordmark">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="NACPDEAN Logo"
                >
            </div>

            <div class="cac-number">
                CAC/IT/NO.182068
            </div>

            <div class="green-rule"></div>

            {{-- Title --}}
            <div class="certificate-title">
                Charcoal Lifting Right
            </div>

            <div class="certify-text">
                This is to certify that
            </div>

            {{-- Dynamic Member --}}
            <div class="member-name">
                {{ $memberName }}
            </div>

            <div class="registered-text">
                is a registered exporter with the
            </div>

            <div class="association">
                NATIONAL ASSOCIATION OF<br>
                CHARCOAL PRODUCERS, DEALERS,<br>
                EXPORTERS AND AFFORESTATION OF NIGERIA
            </div>

            {{-- Details --}}
            <table class="details-row">
                <tr>

                    <td class="qr-cell">

                        @if(!empty($qrCode))

                            <img
                                src="{{ $qrCode }}"
                                alt="Verification QR Code"
                                class="qr-code"
                            >

                        @endif

                        <div class="verify-text">
                            Scan to verify online
                        </div>

                        <div class="verify-url">
                            {{ $verificationUrl }}
                        </div>

                    </td>

                    <td class="details-cell">

                        <div class="detail-item">

                            <span class="detail-label">
                                Membership Number
                            </span>

                            <span class="detail-value">
                                {{ $membershipNumber }}
                            </span>

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Issued Date
                            </span>

                            <span class="detail-value">
                                {{ $issuedAt }}
                            </span>

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Valid Till
                            </span>

                            <span class="detail-value">
                                {{ $validTill }}
                            </span>

                        </div>

                        <div class="detail-item">

                            <span class="detail-label">
                                Reference Number
                            </span>

                            <span class="detail-value">
                                {{ $refNo }}
                            </span>

                        </div>

                    </td>

                </tr>
            </table>

            {{-- Signatures --}}
            <div class="signature-row">

                <div class="signature-box">

                    <img
                        src="{{ asset('images/signature-president.png') }}"
                        alt="President Signature"
                        class="signature-image"
                    >

                    <div class="signature-line"></div>

                    <div class="signatory-name">
                        Edu Babatunde
                    </div>

                    <div class="signatory-title">
                        National President
                    </div>

                </div>

                <div class="signature-box">

                    <img
                        src="{{ asset('images/seal.jpg') }}"
                        alt="NACPDEAN Seal"
                        class="seal-image"
                    >

                </div>

                <div class="signature-box">

                    <img
                        src="{{ asset('images/signature-secretary.png') }}"
                        alt="Secretary Signature"
                        class="signature-image"
                    >

                    <div class="signature-line"></div>

                    <div class="signatory-name">
                        Ojei Uche Joseph
                    </div>

                    <div class="signatory-title">
                        National Secretary-General
                    </div>

                </div>

            </div>

            {{-- Footer Logos --}}
            <div class="footer-logos">

                <img
                    src="{{ asset('images/coat.png') }}"
                    alt="Federal Ministry"
                    class="footer-logo"
                >

                <img
                    src="{{ asset('images/facan.png') }}"
                    alt="FACAN"
                    class="footer-logo"
                >

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="NACPDEAN"
                    class="footer-logo"
                >

            </div>

        </div>

    </div>

</div>

@if(!$printMode)
    <div class="document-actions">

        <button onclick="window.print()">
            🖨 Print Lifting Right
        </button>

        <a href="{{ route('member.documents.download', $generatedDocument) }}">
            ⬇ Download Lifting Right
        </a>

    </div>
@endif

</body>
</html>
