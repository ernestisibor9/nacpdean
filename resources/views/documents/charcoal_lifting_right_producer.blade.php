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
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

    $membershipCategory = '';

    /*
    |--------------------------------------------------------------------------
    | FIRST: CHECK GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $categoryFields = [
        'membership_category',

        'membership_category_name',

        'category_name',

        'category',

        'member_type',

        'membership_type',

        'role',

        'position',
    ];

    foreach ($categoryFields as $fieldKey) {
        $value = $fields->get($fieldKey);

        if ($value !== null && trim((string) $value) !== '') {
            $membershipCategory = trim((string) $value);

            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SECOND: GET CATEGORY FROM MEMBERSHIP RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    if (!$membershipCategory && $membership && $membership->membershipCategory) {
        $category = $membership->membershipCategory;

        $membershipCategoryFields = ['name', 'category_name', 'title', 'code', 'short_name', 'member_type'];

        foreach ($membershipCategoryFields as $fieldKey) {
            $value = $category->{$fieldKey} ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                $membershipCategory = trim((string) $value);

                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | THIRD: CHECK MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    if (!$membershipCategory && $membership && $membership->profile) {
        $memberProfile = $membership->profile;

        $profileCategoryFields = [
            'membership_category',

            'membership_category_name',

            'category_name',

            'category',

            'member_type',

            'membership_type',

            'role',

            'position',
        ];

        foreach ($profileCategoryFields as $fieldKey) {
            $value = $memberProfile->{$fieldKey} ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                $membershipCategory = trim((string) $value);

                break;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL CATEGORY FALLBACK
    |--------------------------------------------------------------------------
    */

    if (!$membershipCategory) {
        $membershipCategory = 'Member';
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY DISPLAY FORMAT
    |--------------------------------------------------------------------------
    */

    $categoryDisplayMap = [
        'RCG' => 'RCG MEMBER',

        'EXP' => 'EXPORTER',

        'SLR' => 'SAWMILL LICENSE HOLDER',

        'DEA' => 'DEALER',

        'PRD' => 'PRODUCER',

        'NEC' => 'NEC MEMBER',
    ];

    $categoryCode = strtoupper(trim((string) $membershipCategory));

    $membershipCategoryDisplay = $categoryDisplayMap[$categoryCode] ?? $membershipCategory;

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
        $fields->get('membership_no', $fields->get('membership_id', '')),
    );

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER FROM MEMBERSHIP TABLE
    |--------------------------------------------------------------------------
    */

    if ($membership && !empty($membership->membership_number)) {
        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP TABLE IS THE AUTHORITATIVE SOURCE
        |--------------------------------------------------------------------------
        */

        $membershipNumber = $membership->membership_number;
    }

    /*
    |--------------------------------------------------------------------------
    | CERTIFICATE NUMBER
    |--------------------------------------------------------------------------
    */

    $certificateNumber = $fields->get('certificate_number', $generatedDocument->document_number);

    /*
    |--------------------------------------------------------------------------
    | ISSUE DATE
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | The membership table is the PRIMARY source.
    |
    | memberships.issued_at
    |
    | If that is unavailable, we fall back to:
    |
    | 1. generated document issued_at
    | 2. field_values issued_at
    |
    */

    $issuedDate = null;

    /*
    |--------------------------------------------------------------------------
    | FIRST: MEMBERSHIP ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if ($membership && !empty($membership->issued_at)) {
        $issuedDate = $membership->issued_at;
    }

    /*
    |--------------------------------------------------------------------------
    | SECOND: GENERATED DOCUMENT ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if (!$issuedDate && !empty($generatedDocument->issued_at)) {
        $issuedDate = $generatedDocument->issued_at;
    }

    /*
    |--------------------------------------------------------------------------
    | THIRD: GENERATED DOCUMENT FIELD VALUE
    |--------------------------------------------------------------------------
    */

    if (!$issuedDate && $fields->get('issued_at')) {
        $issuedDate = $fields->get('issued_at');
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDateFormatted = '';

    if ($issuedDate) {
        try {
            $issuedDateFormatted = \Carbon\Carbon::parse($issuedDate)->format('F jS, Y');
        } catch (\Throwable $e) {
            $issuedDateFormatted = (string) $issuedDate;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRY DATE
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | The membership table is the PRIMARY source.
    |
    | memberships.expires_at
    |
    | If that is unavailable, we fall back to:
    |
    | 1. generated document expires_at
    | 2. field_values expires_at
    |
    */

    $validTill = null;

    /*
    |--------------------------------------------------------------------------
    | FIRST: MEMBERSHIP EXPIRY DATE
    |--------------------------------------------------------------------------
    */

    if ($membership && !empty($membership->expires_at)) {
        $validTill = $membership->expires_at;
    }

    /*
    |--------------------------------------------------------------------------
    | SECOND: GENERATED DOCUMENT EXPIRY DATE
    |--------------------------------------------------------------------------
    */

    if (!$validTill && !empty($generatedDocument->expires_at)) {
        $validTill = $generatedDocument->expires_at;
    }

    /*
    |--------------------------------------------------------------------------
    | THIRD: GENERATED DOCUMENT FIELD VALUE
    |--------------------------------------------------------------------------
    */

    if (!$validTill && $fields->get('expires_at')) {
        $validTill = $fields->get('expires_at');
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT VALID UNTIL DATE
    |--------------------------------------------------------------------------
    */

    $validTillFormatted = '';

    if ($validTill) {
        try {
            $validTillFormatted = \Carbon\Carbon::parse($validTill)->format('F jS, Y');
        } catch (\Throwable $e) {
            $validTillFormatted = (string) $validTill;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REFERENCE NUMBER
    |--------------------------------------------------------------------------
    */

    $refNo = $fields->get('ref_no', $fields->get('reference_number', $generatedDocument->document_number));

    /*
    |--------------------------------------------------------------------------
    | VERIFICATION URL
    |--------------------------------------------------------------------------
    */

    $verificationUrl = $fields->get('verification_url', route('documents.verify', $generatedDocument->tracking_code));

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
    */

    $isPdfMode = !empty($downloadMode);

    /*
    |--------------------------------------------------------------------------
    | STATIC CHARCOAL SUPPLIER ARTWORK
    |--------------------------------------------------------------------------
    */

    $certificateBackground = null;

    $certificatePath = public_path('images/certificates/charcoal-lifting-right-supplier.jpg');

    if (file_exists($certificatePath)) {
        $certificateBackground = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($certificatePath));
    }
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
        font-family: Arial, Helvetica, sans-serif;
        color: #000000;
    }

    /* WRAPPER */
    .nacp-certificate-wrapper {
        width: 100%;
        padding: 30px;
        margin: 0;
    }

    /* CERTIFICATE — fixed A4 aspect via mm */
    .nacp-certificate {
        position: relative;
        width: 210mm;
        height: 297mm;
        margin: 0 auto;
        padding: 0;
        overflow: hidden;
        background: #ffffff;
    }

    /* BACKGROUND ARTWORK */
    .nacp-certificate-background {
        display: block;
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        width: 210mm;
        height: 297mm;
        margin: 0;
        padding: 0;
        border: 0;
    }

    /* ALL ABSOLUTE FIELDS */
    .nacp-certificate-field {
        position: absolute;
        z-index: 10;
        color: #000000;
        font-family: Arial, Helvetica, sans-serif;
        line-height: 1.2;
    }

    /* CERT NUMBER — top right */
    .nacp-certificate-number {
        top: 32mm;
        right: 25mm;
        width: 70mm;
        font-size: 11px;
        font-weight: bold;
        text-align: right;
    }

    /* MEMBER NAME — centered */
    .nacp-certificate-name {
        top: 118mm;
        left: 20mm;
        width: 170mm;
        font-size: 26px;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* MEMBERSHIP CATEGORY */
    .nacp-membership-category {
        top: 115mm;
        left: 30mm;
        width: 150mm;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        text-transform: uppercase;
        color: #000000;
    }

    /* MEMBERSHIP NUMBER */
    .nacp-membership-number {
        top: 178mm;
        left: 35mm;
        width: 140mm;
        font-size: 16px;
        font-weight: bold;
        color: red;
        text-align: center;
        font-family: Tahoma, Arial, sans-serif;
    }

    /* ISSUE DATE */
    .nacp-issued-date {
        top: 191mm;
        left: 55mm;
        width: 130mm;
        font-size: 14px;
        font-style: italic;
        text-align: center;
    }

    /* VALID UNTIL */
    .nacp-valid-till {
        top: 197mm;
        left: 45mm;
        width: 130mm;
        font-size: 14px;
        text-align: center;
    }

    /* REFERENCE NUMBER */
    .nacp-reference-number {
        top: 213mm;
        left: 40mm;
        width: 130mm;
        font-size: 13px;
        text-align: center;
    }

    /* QR CODE — absolute mm, no %, no object-fit */
    .nacp-certificate-qr {
        position: absolute;
        z-index: 20;
        left: 155mm;
        top: 172mm;
        width: 28mm;
        height: 28mm;
        display: block;
        text-align: center;
    }

    .nacp-certificate-qr img {
        display: block;
        width: 28mm;
        height: 28mm;
        margin: 0 auto;
        border: 0;
    }

    .nacp-certificate-qr > div:last-child {
        position: absolute;
        top: 29mm;
        left: 0;
        width: 100%;
    }

    .nacp-certificate-verify {
        font-size: 8px;
        line-height: 1.2;
        color: #000000;
        text-align: center;
    }

    /* ACTION BUTTONS */
    .document-actions {
        max-width: 210mm;
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
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
    }

    .document-actions a:hover,
    .document-actions button:hover {
        opacity: .9;
    }

    /* MOBILE — scale whole cert using transform so % isn't needed */
    @media (max-width: 768px) {

        .nacp-certificate-wrapper {
            padding: 10px;
        }

        .nacp-certificate {
            transform: scale(0.4);
            transform-origin: top left;
        }
    }

    /* PRINT */
    @media print {

        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .nacp-certificate-wrapper {
            width: 210mm !important;
            height: 297mm !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .nacp-certificate {
            width: 210mm !important;
            height: 297mm !important;
            transform: none !important;
        }

        .document-actions {
            display: none !important;
        }
    }

    /* PDF MODE (DOMPDF) — same values, !important where DOMPDF needs it */
    html.pdf-document,
    body.pdf-document {
        width: 210mm !important;
        height: 297mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: hidden !important;
    }

    body.pdf-document .nacp-certificate-wrapper,
    body.pdf-document .nacp-certificate {
        width: 210mm !important;
        height: 297mm !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
    }

    body.pdf-document .nacp-certificate-background {
        width: 210mm !important;
        height: 297mm !important;
        border: 0 !important;
    }

    body.pdf-document .nacp-certificate-qr img {
        width: 28mm !important;
        height: 28mm !important;
    }

    body.pdf-document .document-actions {
        display: none !important;
    }

</style>

</head>


<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">


    {{-- ================================================================
         CERTIFICATE WRAPPER
    ================================================================= --}}

    <div class="nacp-certificate-wrapper">


        {{-- ==========================================================
             SINGLE CHARCOAL SUPPLIER CERTIFICATE
        =========================================================== --}}

        <div class="nacp-certificate">


            {{-- ======================================================
                 STATIC CLIENT ARTWORK
            ======================================================= --}}

            @if ($isPdfMode && !empty($certificateBackground))
                <img src="{{ $certificateBackground }}" class="nacp-certificate-background"
                    alt="Charcoal Lifting Right Supplier Certificate">
            @else
                <img src="{{ asset('images/certificates/charcoal-lifting-right-producer.jpg') }}"
                    class="nacp-certificate-background" alt="Charcoal Lifting Right Supplier Certificate">
            @endif


            {{-- ======================================================
                 DYNAMIC CERTIFICATE NUMBER
            ======================================================= --}}

            @if ($certificateNumber)
                <div
                    class="
                        nacp-certificate-field
                        nacp-certificate-number
                    ">

                    Cert No:
                    {{ $certificateNumber }}

                </div>
            @endif


            {{-- ======================================================
                 DYNAMIC MEMBER / COMPANY NAME
            ======================================================= --}}

            @if ($companyName)
                <div
                    class="
                        nacp-certificate-field
                        nacp-certificate-name
                    ">

                    {{ $companyName }}

                </div>
            @endif


            {{-- ======================================================
                 MEMBERSHIP CATEGORY
            ======================================================= --}}

            {{--
            @if ($membershipCategoryDisplay)

                <div
                    class="
                        nacp-certificate-field
                        nacp-membership-category
                    "
                >

                    {{ $membershipCategoryDisplay }}

                </div>

            @endif
            --}}


            {{-- ======================================================
                 MEMBERSHIP NUMBER
            ======================================================= --}}

            <div
                class="
                    nacp-certificate-field
                    nacp-membership-number
                ">

                {{ $membershipNumber ?: '—' }}

            </div>


            {{-- ======================================================
                 ISSUE DATE
            ======================================================= --}}

            @if ($issuedDateFormatted)
                <div
                    class="
                        nacp-certificate-field
                        nacp-issued-date
                    ">

                    {{ $issuedDateFormatted }}

                </div>
            @endif


            {{-- ======================================================
                 VALID UNTIL DATE
            ======================================================= --}}

            @if ($validTillFormatted)
                <div
                    class="
                        nacp-certificate-field
                        nacp-valid-till
                    ">

                    {{ $validTillFormatted }}

                </div>
            @endif


            {{-- ======================================================
                 REFERENCE NUMBER
            ======================================================= --}}

            {{--
            @if ($refNo)

                <div
                    class="
                        nacp-certificate-field
                        nacp-reference-number
                    "
                >

                    {{ $refNo }}

                </div>

            @endif
            --}}


            {{-- ======================================================
                 QR CODE
            ======================================================= --}}

            @if (!empty($documentQrCode))
                <div class="nacp-certificate-qr">

                    <img src="{{ $documentQrCode }}" alt="Certificate verification QR code">


                    <div>

                        <div class="nacp-certificate-verify">

                            Scan to Verify

                        </div>

                    </div>

                </div>
            @endif


        </div>


    </div>


    {{-- ================================================================
         ACTION BUTTONS
    ================================================================= --}}

    @if (empty($printMode))
        <div class="document-actions">


            <button type="button" onclick="window.print()">

                🖨 Print Certificate

            </button>


            <a
                href="{{ route('member.documents.download', $generatedDocument) }}">

                ⬇ Download Certificate

            </a>


        </div>
    @endif


</body>

</html>
