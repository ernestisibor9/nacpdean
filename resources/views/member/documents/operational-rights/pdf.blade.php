<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        {{ $document->document_title }}
    </title>

    <style>

        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header h2 {
            margin: 8px 0 0;
            font-size: 15px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #999;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
        }

        td:first-child {
            width: 35%;
            font-weight: bold;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | QR VERIFICATION
        |--------------------------------------------------------------------------
        */

        .verification {
            margin-top: 25px;
            padding: 20px;
            border: 2px solid #222;
            text-align: center;
        }

        .verification-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .qr-code {
            margin: 15px auto;
        }

        .qr-code img {
            width: 180px;
            height: 180px;
        }

        .verification-text {
            font-size: 10px;
            margin-top: 10px;
        }

        .verification-url {
            font-size: 8px;
            margin-top: 10px;
            word-wrap: break-word;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

    </style>

</head>

<body>

    <div class="header">

        <h1>
            NACPDEAN
        </h1>

        <h2>
            {{ $document->document_title }}
        </h2>

    </div>


    <!-- DOCUMENT INFORMATION -->

    <div class="section">

        <div class="section-title">
            Document Information
        </div>

        <table>

            <tr>
                <td>Document Number</td>

                <td>
                    {{ $document->document_number }}
                </td>
            </tr>

            <tr>
                <td>Reference Number</td>

                <td>
                    {{ $document->reference_number }}
                </td>
            </tr>

            <tr>
                <td>Authentication Code</td>

                <td>
                    {{ $document->authentication_code }}
                </td>
            </tr>

            <tr>
                <td>Document Type</td>

                <td>
                    {{ $document->document_type }}
                </td>
            </tr>

            <tr>
                <td>Status</td>

                <td class="status">
                    {{ strtoupper($document->status) }}
                </td>
            </tr>

            <tr>
                <td>Issued Date</td>

                <td>
                    {{ optional($document->issued_at)->format('d M Y') }}
                </td>
            </tr>

            <tr>
                <td>Expiry Date</td>

                <td>
                    {{ optional($document->expires_at)->format('d M Y') }}
                </td>
            </tr>

        </table>

    </div>


    <!-- MEMBER INFORMATION -->

    <div class="section">

        <div class="section-title">
            Member Information
        </div>

        <table>

            <tr>
                <td>Member Name</td>

                <td>
                    {{ trim(
                        ($document->profile->surname ?? '') . ' ' .
                        ($document->profile->first_name ?? '') . ' ' .
                        ($document->profile->middle_name ?? '')
                    ) }}
                </td>
            </tr>

            <tr>
                <td>Membership Number</td>

                <td>
                    {{ $document->membership->membership_number ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td>Membership Category</td>

                <td>
                    {{ $document->category->name ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td>State</td>

                <td>
                    {{ $document->profile->state ?? 'N/A' }}
                </td>
            </tr>

        </table>

    </div>


    <!-- VERIFICATION -->

    <div class="verification">

        <div class="verification-title">
            DOCUMENT AUTHENTICATION
        </div>

        <div class="qr-code">

            <img
                src="{{ $qrCode['path'] }}"
                alt="QR Code"
            >

        </div>

        <div class="verification-text">

            Scan this QR code to verify this document.

        </div>

        <div class="verification-url">

            {{ $verificationUrl }}

        </div>

    </div>


    <div class="footer">

        This document is electronically generated by NACPDEAN.

        <br>

        Verify the authenticity and current status of this document
        using the official NACPDEAN verification system.

    </div>

</body>

</html>
