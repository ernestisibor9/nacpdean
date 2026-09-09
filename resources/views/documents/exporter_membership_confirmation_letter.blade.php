@php
    /*
    |--------------------------------------------------------------------------
    | LOAD GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );

    /*
    |--------------------------------------------------------------------------
    | LOAD USER
    |--------------------------------------------------------------------------
    */

    $documentUser = $generatedDocument->user ?? null;

    /*
    |--------------------------------------------------------------------------
    | LOAD MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    $memberProfile = null;

    if ($documentUser) {
        $memberProfile = $documentUser->profile ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | FULL MEMBER NAME
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. Generated document member_name
    | 2. MemberProfile full_name
    | 3. MemberProfile first/middle/last name
    | 4. User name
    |
    | This replaces the old hard-coded:
    |
    | UWI TREES ENTERPRISES
    |
    */

    $memberName = trim(
        (string) $fields->get(
            'member_name',
            ''
        )
    );

    if (!$memberName && $memberProfile) {

        $memberName = trim(
            (string) (
                $memberProfile->full_name
                ?? ''
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD FULL NAME FROM PROFILE NAMES
    |--------------------------------------------------------------------------
    */

    if (!$memberName && $memberProfile) {

        $memberName = trim(
            implode(
                ' ',
                array_filter([
                    $memberProfile->first_name ?? null,
                    $memberProfile->middle_name ?? null,
                    $memberProfile->last_name ?? null,
                ])
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINAL FALLBACK TO USER NAME
    |--------------------------------------------------------------------------
    */

    if (!$memberName && $documentUser) {

        $memberName = trim(
            (string) (
                $documentUser->name
                ?? ''
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REPRESENTATIVE NAME
    |--------------------------------------------------------------------------
    */

    $representativeName = $fields->get(
        'representative_name',
        $fields->get(
            'contact_name',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | FALLBACK REPRESENTATIVE
    |--------------------------------------------------------------------------
    */

    if (!$representativeName && $memberProfile) {

        $representativeName = trim(
            (string) (
                $memberProfile->representative_name
                ?? $memberProfile->contact_name
                ?? ''
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER
    |--------------------------------------------------------------------------
    |
    | Membership ID = Membership Number
    |
    */

    $membershipNumber = $fields->get(
        'membership_number',
        $fields->get(
            'membership_no',
            $fields->get(
                'membership_id',
                ''
            )
        )
    );

    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO ACTUAL MEMBERSHIP RECORD
    |--------------------------------------------------------------------------
    */

    $membership = null;

    if ($documentUser) {

        $membership = \App\Models\Membership::query()
            ->where(
                'user_id',
                $documentUser->id
            )
            ->latest('id')
            ->first();

        if (
            !$membershipNumber &&
            $membership
        ) {

            $membershipNumber =
                $membership->membership_number;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ISSUED DATE
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. field_values.issued_at
    | 2. Membership issued_at
    | 3. GeneratedDocument issued_at
    |
    */

    $issuedDate = $fields->get(
        'issued_at',
        ''
    );

    if (
        !$issuedDate &&
        $membership &&
        $membership->issued_at
    ) {

        $issuedDate =
            $membership->issued_at;
    }

    if (
        !$issuedDate &&
        $generatedDocument->issued_at
    ) {

        $issuedDate =
            $generatedDocument->issued_at;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT ISSUED DATE
    |--------------------------------------------------------------------------
    */

    $issuedDateFormatted = '';

    if ($issuedDate) {

        try {

            $issuedDateFormatted =
                \Carbon\Carbon::parse(
                    $issuedDate
                )->format('jS F Y');

        } catch (\Throwable $e) {

            $issuedDateFormatted =
                (string) $issuedDate;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REFERENCE NUMBER
    |--------------------------------------------------------------------------
    */

    $refNo = $fields->get(
        'ref_no',
        $fields->get(
            'reference_number',
            $generatedDocument->document_number
                ?? ''
        )
    );

@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Membership Confirmation Letter - NACPDEAN
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background-color: #f5f5f5;
            padding: 20px;
            font-size: 13.5px;
            line-height: 1.5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto 20px auto;
            padding: 12mm 15mm 15mm 15mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header Layout */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-left {
            width: 45%;
        }

        .logo-text {
            font-size: 34px;
            font-weight: 900;
            color: #3a2318;
            letter-spacing: -1.5px;
            line-height: 1;
            font-family: Arial, sans-serif;
        }

        .logo-text span {
            color: #8cc63f;
        }

        .header-right {
            width: 53%;
            text-align: right;
        }

        .it-number {
            font-size: 11px;
            font-weight: bold;
            color: #000000;
        }

        .org-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #000000;
            line-height: 1.2;
            text-transform: uppercase;
        }

        /* Two-Column Layout */

        .main-container {
            display: flex;
            justify-content: space-between;
            flex-grow: 1;
        }

        /* Left Sidebar */

        .sidebar-officers {
            width: 24%;
            padding-top: 130px;
        }

        .officer-block {
            margin-bottom: 18px;
            font-size: 10px;
            line-height: 1.3;
        }

        .officer-name {
            font-weight: bold;
            color: #000000;
            font-size: 10.5px;
        }

        .officer-title {
            color: #333333;
            font-size: 9.5px;
        }

        .officer-phone {
            color: #000000;
            font-size: 9.5px;
        }

        /* Main Letter Column */

        .letter-content {
            width: 74%;
            padding-left: 10px;
            position: relative;
        }

        .doc-meta {
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.4;
            font-weight: normal;
        }

        .recipient-block {
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.4;
        }

        .company-name {
            font-weight: bold;
            color: #000000;
        }

        .subject {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
            color: #000000;
            text-transform: uppercase;
        }

        .body-text p {
            margin-bottom: 12px;
            text-align: justify;
            font-size: 12.5px;
            line-height: 1.45;
        }

        /* Bullet lists */

        .details-list,
        .benefits-list {
            list-style: none;
            margin: 8px 0 12px 10px;
        }

        .details-list li,
        .benefits-list li {
            position: relative;
            padding-left: 16px;
            margin-bottom: 5px;
            font-size: 12.5px;
            line-height: 1.4;
        }

        .details-list li::before,
        .benefits-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            font-size: 14px;
            top: -1px;
        }

        .highlight-red {
            color: #c00000;
            font-weight: bold;
        }

        /* Red Stamp Box */

        .red-stamp-box {
            position: absolute;
            right: 0px;
            top: 260px;
            border: 2px solid #c00000;
            padding: 6px 12px;
            text-align: center;
            background: #ffffff;
            box-shadow: 0 0 0 1px #ffffff;
        }

        .red-stamp-title {
            color: #c00000;
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .red-stamp-sub {
            color: #c00000;
            font-size: 9px;
            font-weight: bold;
        }

        /* Page 2 Signatures & Stamps */

        .signatures-area {
            position: relative;
            margin-top: 30px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 10px;
            padding: 0 10px;
        }

        .sig-block {
            text-align: center;
            width: 40%;
            position: relative;
            z-index: 2;
        }

        .sig-image {
            height: 45px;
            margin-bottom: -10px;
        }

        .sig-name {
            font-weight: bold;
            font-size: 13px;
        }

        .sig-title {
            font-weight: bold;
            font-size: 12px;
            color: #000000;
        }

        /* Center Red Wax Seal */

        .wax-seal {
            position: absolute;
            left: 50%;
            top: -15px;
            transform: translateX(-50%);
            width: 70px;
            height: 70px;
            background: radial-gradient(
                circle,
                #aa0000 0%,
                #770000 100%
            );
            border-radius: 50%;
            box-shadow: 2px 3px 6px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .wax-seal-inner {
            width: 54px;
            height: 54px;
            border: 1px dashed #ff9999;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffcccc;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            text-shadow: 1px 1px 1px #000;
        }

        .center-title-sub {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }

        .for-organization {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
        }

        /* Footer */

        .footer {
            margin-top: auto;
            padding-top: 6px;
            border-top: 1.5px solid #000000;
            font-size: 8px;
            text-align: center;
            line-height: 1.3;
            color: #000000;
        }

        .motto {
            font-style: italic;
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 8.5px;
        }

        .address,
        .contact-info {
            margin-bottom: 2px;
        }

        .inaugurators {
            font-weight: bold;
        }

        @media print {

            body {
                background-color: #ffffff;
                padding: 0;
            }

            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                height: 100vh;
                page-break-after: always;
            }

        }

    </style>

</head>

<body>

    <!-- PAGE 1 -->

    <div class="page">

        <div>

            <!-- Header Banner -->

            <div class="header">

                <div class="header-left">

                    <div class="logo-text">
                        nacpde<span>an</span>
                    </div>

                </div>

                <div class="header-right">

                    <div class="it-number">
                        IT:182068
                    </div>

                    <div class="org-title">

                        NATIONAL ASSOCIATION OF CHARCOAL<br>

                        PRODUCERS, DEALERS, EXPORTERS AND<br>

                        AFFORESTATION OF NIGERIA

                    </div>

                </div>

            </div>

            <!-- Two Column Content Layout -->

            <div class="main-container">

                <!-- Left Sidebar: Executive Officers -->

                <div class="sidebar-officers">

                    <div class="officer-block">

                        <div class="officer-name">
                            Edu Babatunde
                        </div>

                        <div class="officer-title">
                            National President
                        </div>

                        <div class="officer-phone">
                            +2348023972044
                        </div>

                    </div>

                    <div class="officer-block">

                        <div class="officer-name">
                            Ali Bukar Jallaba
                        </div>

                        <div class="officer-title">
                            National Dep. President
                        </div>

                        <div class="officer-phone">
                            +2347069728541
                        </div>

                    </div>

                    <div class="officer-block">

                        <div class="officer-name">
                            Ojei Joseph Uche
                        </div>

                        <div class="officer-title">
                            National Asst. Sec. General
                        </div>

                        <div class="officer-phone">
                            +2348036670360
                        </div>

                    </div>

                    <div class="officer-block">

                        <div class="officer-name">
                            Abubakar A. Bako
                        </div>

                        <div class="officer-title">
                            National Treasurer
                        </div>

                        <div class="officer-phone">
                            +2348062832928
                        </div>

                    </div>

                    <div class="officer-block">

                        <div class="officer-name">
                            Dada Daniel
                        </div>

                        <div class="officer-title">
                            National Fin. Secretary
                        </div>

                        <div class="officer-phone">
                            +2349071723949
                        </div>

                    </div>

                </div>

                <!-- Right Column: Page 1 Content -->

                <div class="letter-content">

                    <!-- Date & Ref -->

                    <div class="doc-meta">

                        <div>
                            Date:
                            {{ $issuedDateFormatted ?: '—' }}
                        </div>

                        <div>
                            Ref:
                            {{ $refNo ?: '—' }}
                        </div>

                    </div>

                    <!-- Recipient -->

                    <div class="recipient-block">

                        <div>
                            The Managing Director
                        </div>

                        <div class="company-name">

                            {{ $memberName ?: '—' }}

                        </div>

                        <div>

                            Attn:
                            {{ $representativeName ?: '—' }}

                        </div>

                    </div>

                    <!-- Subject -->

                    <div class="subject">

                        MEMBERSHIP CONFIRMATION LETTER

                    </div>

                    <!-- Body Content -->

                    <div class="body-text">

                        <p>

                            On behalf of the National Charcoal Producers,
                            Dealers &amp; Exporters Association of Nigeria
                            (NACPDEAN), we are pleased to formally confirm
                            your organization's membership in our Association
                            for the year 2026.

                        </p>

                        <p>

                            Following the payment of your membership fee,
                            we are delighted to welcome

                            <strong>
                                {{ $memberName ?: '—' }}
                            </strong>

                            as a valued member of the National Charcoal
                            Producers, Dealers, Exporters, and Afforestation
                            Association of Nigeria (NACPDEAN).

                        </p>

                        <p>

                            Your membership affirms your commitment to
                            fostering sustainable practices, ensuring
                            compliance with government regulations,
                            supporting NACPDEAN's vision, and contributing
                            to the collective growth and development of
                            Nigeria's charcoal industry.

                        </p>

                        <p>

                            Please find below your official membership
                            details:

                        </p>

                        <ul class="details-list">

                            <li>

                                Membership Name:

                                <strong>
                                    {{ $memberName ?: '—' }}
                                </strong>

                            </li>

                            <li>

                                Membership Representative:

                                <strong>
                                    {{ $representativeName ?: '—' }}
                                </strong>

                            </li>

                            <li>

                                Membership Number:

                                <strong class="highlight-red">
                                    {{ $membershipNumber ?: '—' }}
                                </strong>

                            </li>

                        </ul>

                        <!-- Red Stamp Box -->

                        <div class="red-stamp-box">

                            <div class="red-stamp-title">
                                NACPDEAN
                            </div>

                            <div class="red-stamp-sub">
                                IT:182068
                            </div>

                        </div>

                        <p>

                            As a member, you are entitled to:

                        </p>

                        <ul class="benefits-list">

                            <li>

                                Access to NACPDEAN support and
                                representation in dealings with government
                                agencies and international partners.

                            </li>

                            <li>

                                Participation in stakeholder meetings,
                                trainings, and trade forums organized
                                by NACPDEAN.

                            </li>

                            <li>

                                Ensuring compliance with NACPDEAN's policies
                                and government regulations governing charcoal
                                production and the entire export value chain.

                            </li>

                            <li>

                                Collaboration and networking with other
                                industry players for sustainable growth.

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

        <!-- Footer -->

        <div class="footer">

            <div class="motto">

                ...Strive for Biomass Energy, Ecological Afforestation,
                Nation Builder, Transparency &amp; Traceability

            </div>

            <div class="address">

                Block D Complex, Federal Ministry of Industry,
                Trade &amp; Investment, Old Secretariat, Area 1,
                Garki, Abuja, FCT.

            </div>

            <div class="contact-info">

                Tel: +234 814 567 2358,
                +234 803 667 0360,
                +234 905 301 8515

                &nbsp;|&nbsp;

                Email: nacpdean55@gmail.com

                &nbsp;|&nbsp;

                W: www.nacpdean.com

            </div>

            <div class="inaugurators">

                Our inaugurators:
                Federation of Agricultural Commodity Associations
                of Nigeria FACAN
                |
                Federal Ministry of Industry Trade and Investment - FMITI

            </div>

        </div>

    </div>


    <!-- PAGE 2 -->

    <div class="page">

        <div>

            <!-- Page 2 Content Header -->

            <div
                class="body-text"
                style="padding-top: 20px;"
            >

                <p>

                    We encourage you to actively participate in our
                    programs and contribute to our mission of building
                    a transparent, well-regulated, and globally
                    competitive charcoal sector.

                </p>

                <p>

                    Once again, we congratulate you on your successful
                    registration and look forward to working closely
                    with you.

                </p>

                <p>

                    We remain grateful for your attention to this matter
                    and respectfully extend our highest regards and best
                    wishes from the National Association of Charcoal
                    Producers, Dealers, Exporters, and Afforestation
                    of Nigeria (NACPDEAN)

                </p>

                <!-- Signatures Section -->

                <div class="signatures-area">

                    <!-- Wax Seal -->

                    <div class="wax-seal">

                        <div class="wax-seal-inner">

                            NACPDEAN<br>
                            SEAL

                        </div>

                    </div>

                    <div class="signatures">

                        <div class="sig-block">

                            <div
                                style="
                                    font-family: 'Brush Script MT', cursive;
                                    font-size: 24px;
                                    color: #1a237e;
                                    transform: rotate(-5deg);
                                "
                            >

                                Edu Babatunde

                            </div>

                            <div class="sig-name">

                                Edu Babatunde

                            </div>

                            <div class="sig-title">

                                National President

                            </div>

                        </div>

                        <div class="sig-block">

                            <div
                                style="
                                    font-family: 'Brush Script MT', cursive;
                                    font-size: 24px;
                                    color: #1a237e;
                                    transform: rotate(-3deg);
                                "
                            >

                                Ojei Uche

                            </div>

                            <div class="sig-name">

                                Ojei Uche Joseph

                            </div>

                            <div class="sig-title">

                                National Secretary-General

                            </div>

                        </div>

                    </div>

                    <div class="center-title-sub">

                        National President

                    </div>

                    <div class="for-organization">

                        For:
                        National Association of Charcoal Producers,
                        Dealers, Exporters, and Afforestation of Nigeria
                        (NACPDEAN)

                    </div>

                </div>

            </div>

        </div>

        <!-- Footer -->

        <div class="footer">

            <div class="motto">

                ...Strive for Biomass Energy, Ecological Afforestation,
                Nation Builder, Transparency &amp; Traceability

            </div>

            <div class="address">

                Block D Complex, Federal Ministry of Industry,
                Trade &amp; Investment, Old Secretariat, Area 1,
                Garki, Abuja, FCT.

            </div>

            <div class="contact-info">

                Tel: +234 814 567 2358,
                +234 803 667 0360,
                +234 905 301 8515

                &nbsp;|&nbsp;

                Email: nacpdean55@gmail.com

                &nbsp;|&nbsp;

                W: www.nacpdean.com

            </div>

            <div class="inaugurators">

                Our inaugurators:
                Federation of Agricultural Commodity Associations
                of Nigeria FACAN
                |
                Federal Ministry of Industry Trade and Investment - FMITI

            </div>

        </div>

    </div>

</body>

</html>
