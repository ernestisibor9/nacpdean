<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        NACPDEAN - Document Verification
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 20px;
            background: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin-top: 8px;
            color: #666;
        }

        .verification-status {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .verification-status.success {
            background: #e8f7ee;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .verification-status.danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .verification-status h2 {
            margin: 0 0 8px;
            font-size: 22px;
        }

        .verification-status p {
            margin: 0;
        }

        .section-title {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        td {
            padding: 11px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        td:first-child {
            width: 40%;
            font-weight: bold;
            color: #555;
        }

        .active {
            font-weight: bold;
            text-transform: uppercase;
            color: #15803d;
        }

        .inactive {
            font-weight: bold;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 13px;
            line-height: 1.6;
        }

        .official {
            margin-top: 25px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 10px;
            }

            .card {
                padding: 22px;
            }

            .header h1 {
                font-size: 23px;
            }

            td:first-child {
                width: 45%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="header">

            <h1>NACPDEAN</h1>

            <p>
                Operational Rights Document Verification
            </p>

        </div>


        @if($verified)

            <div class="verification-status success">

                <h2>
                    ✓ DOCUMENT VERIFIED
                </h2>

                <p>
                    {{ $message }}
                </p>

            </div>

        @else

            <div class="verification-status danger">

                <h2>
                    ✕ DOCUMENT NOT VERIFIED
                </h2>

                <p>
                    {{ $message }}
                </p>

            </div>

        @endif


        @if($document)

            <div class="section-title">
                Document Information
            </div>

            <table>

                <tr>
                    <td>
                        Document Number
                    </td>

                    <td>
                        {{ $document->document_number }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Document
                    </td>

                    <td>
                        {{ $document->document_title }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Reference Number
                    </td>

                    <td>
                        {{ $document->reference_number }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Authentication Code
                    </td>

                    <td>
                        {{ $document->authentication_code }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Document Type
                    </td>

                    <td>
                        {{ $document->document_type }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Issued Date
                    </td>

                    <td>
                        {{ optional($document->issued_at)->format('d M Y') }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Expiry Date
                    </td>

                    <td>
                        {{ optional($document->expires_at)->format('d M Y') }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Current Status
                    </td>

                    <td>

                        @if(strtolower($document->status) === 'active')

                            <span class="active">
                                ACTIVE
                            </span>

                        @else

                            <span class="inactive">
                                {{ strtoupper($document->status) }}
                            </span>

                        @endif

                    </td>
                </tr>

            </table>


            <div class="section-title">
                Member Information
            </div>

            <table>

                <tr>
                    <td>
                        Member Name
                    </td>

                    <td>

                        {{ trim(
                            ($document->profile->surname ?? '') . ' ' .
                            ($document->profile->first_name ?? '') . ' ' .
                            ($document->profile->middle_name ?? '')
                        ) }}

                    </td>
                </tr>

                <tr>
                    <td>
                        Membership Number
                    </td>

                    <td>
                        {{ $document->membership->membership_number ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Membership Category
                    </td>

                    <td>
                        {{ $document->category->name ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <td>
                        State
                    </td>

                    <td>
                        {{ $document->profile->state ?? 'N/A' }}
                    </td>
                </tr>

            </table>


            <div class="footer">

                This document was verified through the official
                NACPDEAN document verification system.

                <br><br>

                The information displayed above reflects the
                current status of the document in the NACPDEAN system.

            </div>

        @endif


        <div class="official">

            © {{ date('Y') }} NACPDEAN.
            All rights reserved.

        </div>

    </div>

</div>

</body>
</html>
