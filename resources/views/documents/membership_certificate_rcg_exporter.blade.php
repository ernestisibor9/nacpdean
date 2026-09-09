@php
    /*
    |--------------------------------------------------------------------------
    | GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );


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
    */

    $membership = null;

    if ($documentUser) {

        $membership = \App\Models\Membership::with([
            'membershipCategory',
            'profile',
        ])
            ->where('user_id', $documentUser->id)
            ->latest('id')
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

    $membershipCategory = $fields->get(
        'membership_category',
        $fields->get(
            'membership_category_name',
            ''
        )
    );

    if (
        !$membershipCategory &&
        $membership?->membershipCategory
    ) {

        $membershipCategory =
            $membership->membershipCategory->name;
    }

    $membershipCategory = trim(
        (string) $membershipCategory
    );


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY CODE
    |--------------------------------------------------------------------------
    */

    $membershipCategoryCode = strtoupper(
        trim(
            (string) (
                $fields->get(
                    'membership_category_code',
                    ''
                )
                ?: $membership?->membershipCategory?->code
                ?: ''
            )
        )
    );


    /*
    |--------------------------------------------------------------------------
    | RCG DETECTION
    |--------------------------------------------------------------------------
    */

    $isRcg =
        $membershipCategoryCode === 'RCG'
        ||
        str_contains(
            strtoupper($membershipCategory),
            'RCG'
        );


    /*
    |--------------------------------------------------------------------------
    | MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    $memberProfile =
        $membership?->profile;


    /*
    |--------------------------------------------------------------------------
    | MEMBER / COMPANY NAME
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | We check several possible field names because different
    | document field definitions may use different keys.
    |
    */

    $companyName = '';


    /*
    |--------------------------------------------------------------------------
    | 1. GENERATED DOCUMENT FIELD VALUES
    |--------------------------------------------------------------------------
    */

    $memberNameFields = [
        'member_name',
        'business_name',
        'company_name',
        'companyName',
        'businessName',
        'organisation_name',
        'organization_name',
        'organisation',
        'organization',
        'company',
        'member_business_name',
        'business',
        'full_name',
        'name',
    ];


    foreach ($memberNameFields as $fieldKey) {

        $value = $fields->get($fieldKey);

        if (
            $value !== null &&
            trim((string) $value) !== ''
        ) {

            $companyName =
                trim((string) $value);

            break;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 2. MEMBER PROFILE FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        !$companyName &&
        $memberProfile
    ) {

        $profileNameFields = [
            'business_name',
            'company_name',
            'organisation_name',
            'organization_name',
            'business',
            'company',
            'name',
            'full_name',
        ];


        foreach ($profileNameFields as $fieldKey) {

            $value =
                $memberProfile->{$fieldKey}
                ?? null;

            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {

                $companyName =
                    trim((string) $value);

                break;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 3. USER FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        !$companyName &&
        $documentUser
    ) {

        $userNameFields = [
            'business_name',
            'company_name',
            'name',
        ];


        foreach ($userNameFields as $fieldKey) {

            $value =
                $documentUser->{$fieldKey}
                ?? null;

            if (
                $value !== null &&
                trim((string) $value) !== ''
            ) {

                $companyName =
                    trim((string) $value);

                break;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FINAL NAME FALLBACK
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
        $fields->get(
            'membership_no',
            ''
        )
    );


    if (
        !$membershipNumber &&
        $membership
    ) {

        $membershipNumber =
            $membership->membership_number;
    }


    /*
    |--------------------------------------------------------------------------
    | CERTIFICATE NUMBER
    |--------------------------------------------------------------------------
    */

    $certificateNumber = $fields->get(
        'certificate_number',
        $fields->get(
            'certificate_no',
            $fields->get(
                'document_number',
                $generatedDocument->document_number
            )
        )
    );


    /*
    |--------------------------------------------------------------------------
    | ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDate = $fields->get(
        'issued_at',
        $fields->get(
            'issue_date',
            ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP ISSUE DATE FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        !$issuedDate &&
        $membership?->issued_at
    ) {

        $issuedDate =
            $membership->issued_at;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATED DOCUMENT ISSUE DATE FALLBACK
    |--------------------------------------------------------------------------
    */

    if (
        !$issuedDate &&
        $generatedDocument->issued_at
    ) {

        $issuedDate =
            $generatedDocument->issued_at;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDateFormatted = '';

    if ($issuedDate) {

        try {

            $issuedDateFormatted =
                \Carbon\Carbon::parse(
                    $issuedDate
                )->format('F jS, Y');

        } catch (\Throwable $e) {

            $issuedDateFormatted =
                (string) $issuedDate;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICATION URL
    |--------------------------------------------------------------------------
    */

    $verificationUrl = $fields->get(
        'verification_url',
        ''
    );


    if (!$verificationUrl) {

        $verificationUrl = route(
            'documents.verify',
            $generatedDocument->tracking_code
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SIGNATORIES
    |--------------------------------------------------------------------------
    */

    $presidentName = $fields->get(
        'president_name',
        'Edu Babatunde'
    );

    $presidentTitle = $fields->get(
        'president_title',
        'National President'
    );


    $secretaryGeneralName = $fields->get(
        'secretary_general_name',
        $fields->get(
            'secretary_name',
            'Ojei Uche Joeseph'
        )
    );


    $secretaryGeneralTitle = $fields->get(
        'secretary_general_title',
        'National Secretary-General'
    );


    /*
    |--------------------------------------------------------------------------
    | ASSOCIATION NAME
    |--------------------------------------------------------------------------
    */

    $associationName = $fields->get(
        'association_name',
        'NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS, DEALERS, EXPORTERS AND AFFORESTATION OF NIGERIA'
    );


    /*
    |--------------------------------------------------------------------------
    | REGISTERED MEMBER TEXT
    |--------------------------------------------------------------------------
    */

    if ($isRcg) {

        $registeredText =
            'is a Registered RCG Export Member with';

    } elseif (
        str_contains(
            strtoupper($membershipCategory),
            'EXPORTER'
        )
    ) {

        $registeredText =
            'is a Registered Export Member with';

    } elseif (
        str_contains(
            strtoupper($membershipCategory),
            'SUPPLIER'
        )
    ) {

        $registeredText =
            'is a Registered Supplier Member with';

    } elseif (
        str_contains(
            strtoupper($membershipCategory),
            'DEALER'
        )
    ) {

        $registeredText =
            'is a Registered Dealer Member with';

    } elseif (
        str_contains(
            strtoupper($membershipCategory),
            'PRODUCER'
        )
    ) {

        $registeredText =
            'is a Registered Producer Member with';

    } else {

        $registeredText =
            'is a Registered Member with';
    }

@endphp


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        {{ $certificateNumber }}
    </title>

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
            margin: 20px auto 5px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 14px;
        }


        .nacpdean-logo {
            width: 210px;

            max-width: 100%;

            height: auto;

            display: block;

            object-fit: contain;
        }


        .rcg-logo {
            width: 65px;

            height: 65px;

            display: block;

            object-fit: contain;
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


            .certificate-wrapper {
                width: 297mm;

                height: 210mm;

                padding: 0;
            }


            .certificate {
                width: 297mm;

                height: 210mm;

                max-width: none;

                min-height: auto;

                margin: 0;

                border-width: 8mm;

                padding: 3mm;
            }


            .certificate-inner {
                width: 100%;

                height: 190mm;

                min-height: 190mm;

                padding: 8mm 12mm;
            }


            /*
            |--------------------------------------------------------------------------
            | LOGOS
            |--------------------------------------------------------------------------
            */

            .logos {
                margin-top: 4mm;

                gap: 5mm;

                display: flex;

                align-items: center;

                justify-content: center;
            }


            .nacpdean-logo {
                width: 58mm;

                height: auto;
            }


            .rcg-logo {
                width: 18mm;

                height: 18mm;
            }


            /*
            |--------------------------------------------------------------------------
            | BOTTOM SECTION
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


            .bottom-item {
                display: table-cell;

                vertical-align: bottom;

                text-align: center;
            }


            .signature {
                width: 220px;

                margin: 0 auto;
            }


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
                 NACPDEAN + RCG LOGOS
            =========================================================== --}}

            <div class="logos">

                {{-- NACPDEAN LOGO --}}

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="NACPDEAN"
                    class="nacpdean-logo"
                >


                {{-- RCG LOGO ONLY FOR RCG MEMBERS --}}

                @if($isRcg)

                    <img
                        src="{{ asset('images/rcc.jpeg') }}"
                        alt="RCG"
                        class="rcg-logo"
                    >

                @endif

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

                {{ strtoupper($companyName) }}

            </div>


            {{-- ==========================================================
                 REGISTERED TEXT
            =========================================================== --}}

            <div class="registered-text">

                {{ $registeredText }}

            </div>


            {{-- ==========================================================
                 ASSOCIATION
            =========================================================== --}}

            <div class="association">

                {{ strtoupper($associationName) }}

            </div>


            {{-- ==========================================================
                 MEMBERSHIP NUMBER
            =========================================================== --}}

            <div class="membership-number">

                MEMBERSHIP NO.:
                {{ $membershipNumber ?: '—' }}

            </div>


            {{-- ==========================================================
                 ISSUE DATE
            =========================================================== --}}

            <div class="dated">

                Dated this:
                {{ $issuedDateFormatted ?: '—' }}

            </div>


            {{-- ==========================================================
                 BOTTOM SECTION
            =========================================================== --}}

            <div class="bottom-section">


                {{-- ======================================================
                     PRESIDENT
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="signature">

                        <div class="signature-name">

                            {{ $presidentName }}

                        </div>

                        <div class="signature-line">

                            <strong>
                                {{ $presidentTitle }}
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

                        @if(!empty($qrCode))

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
                     SECRETARY-GENERAL
                ======================================================= --}}

                <div class="bottom-item">

                    <div class="signature">

                        <div class="signature-name">

                            {{ $secretaryGeneralName }}

                        </div>

                        <div class="signature-line">

                            <strong>
                                {{ $secretaryGeneralTitle }}
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
            href="{{ route(
                'member.documents.download',
                $generatedDocument
            ) }}"
        >
            ⬇ Download Certificate
        </a>

    </div>

@endif


</body>

</html>
