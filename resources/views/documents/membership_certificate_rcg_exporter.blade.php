@php
    /*
    |--------------------------------------------------------------------------
    | GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect($generatedDocument->field_values ?? []);

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    $documentUser = $generatedDocument->user ?? null;

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    |
    | Get the actual membership belonging to this generated document user.
    |
    */

    $membership = null;

    if ($documentUser) {
        $membership = \App\Models\Membership::query()
            ->with(['profile', 'membershipCategory'])
            ->where('user_id', $documentUser->id)
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBER / COMPANY NAME
    |--------------------------------------------------------------------------
    */

    $companyName = '';

    /*
    |--------------------------------------------------------------------------
    | CHECK GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $nameFields = [
        'member_name',
        'business_name',
        'company_name',
        'organisation_name',
        'organization_name',
        'organisation',
        'organization',
        'business',
        'company',
        'member_business_name',
        'full_name',
        'name',
    ];

    foreach ($nameFields as $fieldKey) {
        $value = $fields->get($fieldKey);

        if ($value !== null && trim((string) $value) !== '') {
            $companyName = trim((string) $value);

            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    if (!$companyName && $membership && $membership->profile) {
        $memberProfile = $membership->profile;

        $profileNameFields = [
            'business_name',
            'company_name',
            'organisation_name',
            'organization_name',
            'organisation',
            'organization',
            'business',
            'company',
            'business_registration_name',
            'registered_business_name',
            'registered_company_name',
            'full_name',
            'name',
        ];

        foreach ($profileNameFields as $fieldKey) {
            $value = $memberProfile->{$fieldKey} ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                $companyName = trim((string) $value);

                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK USER
    |--------------------------------------------------------------------------
    */

    if (!$companyName && $documentUser) {
        $userNameFields = [
            'business_name',
            'company_name',
            'organisation_name',
            'organization_name',
            'business',
            'company',
            'name',
        ];

        foreach ($userNameFields as $fieldKey) {
            $value = $documentUser->{$fieldKey} ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                $companyName = trim((string) $value);

                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL FALLBACK
    |--------------------------------------------------------------------------
    */

    if (!$companyName) {
        $companyName = 'Member';
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER
    |--------------------------------------------------------------------------
    */

    $membershipNumber = $fields->get(
        'membership_number',
        $fields->get('membership_no', '')
    );

    if (!$membershipNumber && $membership) {
        $membershipNumber = $membership->membership_number;
    }

    /*
    |--------------------------------------------------------------------------
    | CERTIFICATE NUMBER
    |--------------------------------------------------------------------------
    */

    $certificateNumber = $fields->get(
        'certificate_number',
        $generatedDocument->document_number
    );

    /*
    |--------------------------------------------------------------------------
    | ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDate = $fields->get('issued_at', '');

    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO MEMBERSHIP ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if (!$issuedDate && $membership && $membership->issued_at) {
        $issuedDate = $membership->issued_at;
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO GENERATED DOCUMENT ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if (!$issuedDate && $generatedDocument->issued_at) {
        $issuedDate = $generatedDocument->issued_at;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDateFormatted = '';

    if ($issuedDate) {
        try {
            $issuedDateFormatted = \Carbon\Carbon::parse($issuedDate)
                ->format('F jS, Y');
        } catch (\Throwable $e) {
            $issuedDateFormatted = (string) $issuedDate;
        }
    }

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
    | QR CODE
    |--------------------------------------------------------------------------
    */

    $documentQrCode = $qrCode ?? null;

    /*
    |--------------------------------------------------------------------------
    | PDF MODE
    |--------------------------------------------------------------------------
    |
    | downloadMode=true when the controller is generating the PDF.
    |
    */

    $isPdfMode = !empty($downloadMode);

@endphp


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $certificateNumber }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>

        /*
        |--------------------------------------------------------------------------
        | BASIC RESET
        |--------------------------------------------------------------------------
        */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            padding: 0;
        }


        body {
            background: #eeeeee;
            font-family: "Times New Roman", serif;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE VIEWER
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-wrapper {
            width: 100%;
            padding: 30px;
            margin: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE
        |--------------------------------------------------------------------------
        */

        .nacp-certificate {
            position: relative;

            width: 100%;

            max-width: 1200px;

            margin: 0 auto;

            padding: 0;

            overflow: hidden;

            background: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | CLIENT CERTIFICATE BACKGROUND
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-background {
            display: block;

            width: 100%;

            height: auto;

            margin: 0;

            padding: 0;

            border: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-field {
            position: absolute;

            z-index: 10;

            color: #000;

            text-align: center;

            line-height: 1.2;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER / COMPANY NAME
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-name {
            top: 32.8%;

            left: 12%;

            width: 76%;

            font-size: 32px;

            font-weight: bold;

            text-transform: uppercase;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE NUMBER
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-number {
            top: 11.2%;

            right: 11%;

            width: 30%;

            font-size: 14px;

            font-weight: bold;

            text-align: right;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP NUMBER
        |--------------------------------------------------------------------------
        */

        .nacp-membership-number {
            top: 48.1%;

            left: 20%;

            width: 60%;

            font-size: 18px;

            font-weight: bolder;

            color: red;

            font-family: Tahoma, sans-serif;
        }


        /*
        |--------------------------------------------------------------------------
        | ISSUE DATE
        |--------------------------------------------------------------------------
        */

        .nacp-issued-date {
            top: 51.7%;

            left: 20%;

            width: 60%;

            font-size: 17px;

            font-style: italic;
        }


        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-qr {
            position: absolute;

            z-index: 20;

            left: 54.7%;

            top: 76.3%;

            width: 34%;

            display: flex;

            align-items: center;

            gap: 12px;

            text-align: left;
        }


        .nacp-certificate-qr img {
            display: block;

            width: 90px;

            height: 90px;

            min-width: 90px;

            min-height: 90px;

            flex-shrink: 0;

            object-fit: contain;

            border: 0;
        }


        .nacp-certificate-verify {
            font-size: 9px;

            line-height: 1.2;

            color: #000;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICATION URL
        |--------------------------------------------------------------------------
        */

        .nacp-certificate-verification-url {
            margin-top: 2px;

            font-size: 7px;

            line-height: 1.1;

            word-break: break-all;

            overflow-wrap: break-word;

            color: #000;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {
            max-width: 1200px;

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
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 768px) {

            .nacp-certificate-wrapper {
                padding: 10px;
            }

            .nacp-certificate-name {
                font-size: 18px;
            }

            .nacp-certificate-number {
                font-size: 9px;
            }

            .nacp-membership-number {
                font-size: 11px;
            }

            .nacp-issued-date {
                font-size: 10px;
            }

            .nacp-certificate-qr {
                gap: 6px;
            }

            .nacp-certificate-qr img {
                width: 30px;

                height: 30px;

                min-width: 30px;

                min-height: 30px;
            }

            .nacp-certificate-verify {
                font-size: 6px;
            }

            .nacp-certificate-verification-url {
                font-size: 5px;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        @media print {

            @page {
                size: A4 landscape;

                margin: 0;
            }


            html,
            body {
                width: 297mm !important;

                height: 210mm !important;

                margin: 0 !important;

                padding: 0 !important;

                background: #fff !important;
            }


            .nacp-certificate-wrapper {
                width: 297mm !important;

                height: 210mm !important;

                padding: 0 !important;

                margin: 0 !important;
            }


            .nacp-certificate {
                position: relative !important;

                width: 297mm !important;

                height: 210mm !important;

                max-width: none !important;

                margin: 0 !important;

                padding: 0 !important;

                overflow: hidden !important;
            }


            .nacp-certificate-background {
                display: block !important;

                position: absolute !important;

                z-index: 1 !important;

                top: 0 !important;

                left: 0 !important;

                width: 297mm !important;

                height: 210mm !important;

                margin: 0 !important;

                padding: 0 !important;

                border: 0 !important;
            }


            .nacp-certificate-field {
                z-index: 10 !important;
            }


            .nacp-certificate-qr {
                z-index: 20 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | PRINT FIELD SIZES
            |--------------------------------------------------------------------------
            */

            .nacp-certificate-name {
                font-size: 27px !important;
            }


            .nacp-certificate-number {
                font-size: 12px !important;
            }


            .nacp-membership-number {
                font-size: 16px !important;
            }


            .nacp-issued-date {
                font-size: 15px !important;
            }


            /*
            |--------------------------------------------------------------------------
            | QR
            |--------------------------------------------------------------------------
            */

            .nacp-certificate-qr img {
                width: 15mm !important;

                height: 15mm !important;

                min-width: 15mm !important;

                min-height: 15mm !important;
            }


            .nacp-certificate-verify {
                font-size: 7px !important;
            }


            .nacp-certificate-verification-url {
                font-size: 6px !important;
            }


            /*
            |--------------------------------------------------------------------------
            | HIDE BUTTONS
            |--------------------------------------------------------------------------
            */

            .document-actions {
                display: none !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DOMPDF / DOWNLOAD MODE
        |--------------------------------------------------------------------------
        |
        | THIS IS THE IMPORTANT PART.
        |
        | When Laravel generates the PDF, we do NOT want normal HTML
        | flow to determine the page position.
        |
        | Everything is locked to one A4 landscape page.
        |
        */

        @page {
            size: A4 landscape;

            margin: 0;
        }


        html.pdf-document,
        body.pdf-document {
            width: 297mm !important;

            height: 210mm !important;

            min-width: 297mm !important;

            min-height: 210mm !important;

            max-width: 297mm !important;

            max-height: 210mm !important;

            margin: 0 !important;

            padding: 0 !important;

            background: #ffffff !important;

            overflow: hidden !important;
        }


        body.pdf-document {
            position: relative !important;
        }


        body.pdf-document .nacp-certificate-wrapper {
            position: relative !important;

            width: 297mm !important;

            height: 210mm !important;

            min-width: 297mm !important;

            min-height: 210mm !important;

            max-width: 297mm !important;

            max-height: 210mm !important;

            margin: 0 !important;

            padding: 0 !important;

            overflow: hidden !important;

            page-break-before: avoid !important;

            page-break-after: avoid !important;

            page-break-inside: avoid !important;
        }


        body.pdf-document .nacp-certificate {
            position: relative !important;

            width: 297mm !important;

            height: 210mm !important;

            min-width: 297mm !important;

            min-height: 210mm !important;

            max-width: 297mm !important;

            max-height: 210mm !important;

            margin: 0 !important;

            padding: 0 !important;

            overflow: hidden !important;

            page-break-before: avoid !important;

            page-break-after: avoid !important;

            page-break-inside: avoid !important;

            background: #ffffff !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF BACKGROUND
        |--------------------------------------------------------------------------
        |
        | The JPG is absolutely positioned so it cannot create a
        | second page by participating in normal document flow.
        |
        */

        body.pdf-document .nacp-certificate-background {
            position: absolute !important;

            z-index: 1 !important;

            top: 0 !important;

            left: 0 !important;

            display: block !important;

            width: 297mm !important;

            height: 210mm !important;

            min-width: 297mm !important;

            min-height: 210mm !important;

            max-width: 297mm !important;

            max-height: 210mm !important;

            margin: 0 !important;

            padding: 0 !important;

            border: 0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        */

        body.pdf-document .nacp-certificate-field {
            position: absolute !important;
        }


        body.pdf-document .nacp-certificate-name {
            z-index: 10 !important;
        }


        body.pdf-document .nacp-certificate-number {
            z-index: 10 !important;
        }


        body.pdf-document .nacp-membership-number {
            z-index: 10 !important;
        }


        body.pdf-document .nacp-issued-date {
            z-index: 10 !important;
        }


        body.pdf-document .nacp-certificate-qr {
            position: absolute !important;

            z-index: 20 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        body.pdf-document .document-actions {
            display: none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF PAGE BREAK PROTECTION
        |--------------------------------------------------------------------------
        */

        body.pdf-document .nacp-certificate-wrapper,
        body.pdf-document .nacp-certificate,
        body.pdf-document .nacp-certificate-background {
            page-break-before: avoid !important;

            page-break-after: avoid !important;

            page-break-inside: avoid !important;
        }

    </style>

</head>


<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">


    <div class="nacp-certificate-wrapper">


        {{-- ==========================================================
             CERTIFICATE
        =========================================================== --}}

        <div class="nacp-certificate">


            {{-- ======================================================
                 CLIENT'S STATIC CERTIFICATE ARTWORK
            ======================================================= --}}

            @if ($isPdfMode && !empty($certificateBackground))

                <img src="{{ $certificateBackground }}"
                    class="nacp-certificate-background"
                    alt="NACPDEAN Certificate">

            @else

                <img src="{{ asset('images/certificates/regular-exporter-template.jpg') }}"
                    class="nacp-certificate-background"
                    alt="NACPDEAN Certificate">

            @endif


            {{-- ======================================================
                 DYNAMIC CERTIFICATE NUMBER
            ======================================================= --}}

            <div class="nacp-certificate-field nacp-certificate-number">

                Cert No:
                {{ $certificateNumber }}

            </div>


            {{-- ======================================================
                 DYNAMIC MEMBER / COMPANY NAME
            ======================================================= --}}

            <div class="nacp-certificate-field nacp-certificate-name">

                {{ $companyName }}

            </div>


            {{-- ======================================================
                 DYNAMIC MEMBERSHIP NUMBER
            ======================================================= --}}

            <div class="nacp-certificate-field nacp-membership-number">

                MEMBERSHIP NO.:
                {{ $membershipNumber ?: '—' }}

            </div>


            {{-- ======================================================
                 DYNAMIC ISSUE DATE
            ======================================================= --}}

            <div class="nacp-certificate-field nacp-issued-date">

                Dated this:
                {{ $issuedDateFormatted ?: '—' }}

            </div>


            {{-- ======================================================
                 QR CODE
            ======================================================= --}}

            @if (!empty($documentQrCode))

                <div class="nacp-certificate-qr">

                    <img src="{{ $documentQrCode }}"
                        alt="Certificate verification QR code">

                    <div>

                        <div class="nacp-certificate-verify">

                            Scan to Verify

                        </div>

                        {{--

                        <div class="nacp-certificate-verification-url">

                            {{ $verificationUrl }}

                        </div>

                        --}}

                    </div>

                </div>

            @endif


        </div>


    </div>


    {{-- ================================================================
         ACTION BUTTONS
    ================================================================= --}}

    @if (!$printMode)

        <div class="document-actions">

            <button type="button" onclick="window.print()">

                🖨 Print Certificate

            </button>


            <a href="{{ route('member.documents.download', $generatedDocument) }}">

                ⬇ Download Certificate

            </a>

        </div>

    @endif


</body>

</html>
