@extends('member.member_dashboard')

@section('title', 'Membership Card | NACPDEAN')

@section('member')

<style>
    /* =========================================================
       NACPDEAN MEMBERSHIP CARD
       Based on the original Bussy ID Card design
    ========================================================= */

    .nacp-card-page {
        background: #e8e8e8;
        min-height: calc(100vh - 60px);
        padding: 35px 15px 60px;
    }

    .nacp-card-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        gap: 35px;
    }

    /* =========================================================
       CARD
    ========================================================= */

    .nacp-id-card {
        position: relative;

        width: 370px;
        height: 600px;

        background:
            radial-gradient(
                circle at 30% 20%,
                rgba(255,255,255,.98),
                rgba(232,232,232,.94)
            );

        border: 1px solid #d5d5d5;

        border-radius: 5px;

        overflow: hidden;

        box-shadow:
            0 8px 25px rgba(0,0,0,.25);

        font-family:
            Arial,
            Helvetica,
            sans-serif;
    }


    /* =========================================================
       LEFT GREEN STRIP
    ========================================================= */

    .nacp-green-strip {

        position: absolute;

        left: 0;
        top: 0;

        width: 72px;
        height: 100%;

        background: #00a651;

        z-index: 1;
    }


    /* =========================================================
       GREEN TRIANGLE
    ========================================================= */

    .nacp-green-triangle {

        position: absolute;

        bottom: 0;
        left: 0;

        width: 0;
        height: 0;

        border-top: 75px solid #d4e99a;
        border-right: 75px solid transparent;

        transform: rotate(180deg);
    }


    /* =========================================================
       VERTICAL MEMBERSHIP NUMBER
    ========================================================= */

    .nacp-vertical-id {

        position: absolute;

        left: 0;
        top: 24%;

        width: 100%;

        padding-right: 10px;

        color: #fff;

        font-size: 17px;

        font-weight: 800;

        letter-spacing: 1px;

        text-align: center;

        writing-mode: vertical-rl;

        transform: rotate(180deg);

        white-space: nowrap;
    }


    /* =========================================================
       CARD CONTENT
    ========================================================= */

    .nacp-card-content {

        position: absolute;

        left: 72px;
        top: 0;

        width: calc(100% - 72px);

        height: 100%;

        padding: 12px 12px 8px;

        z-index: 2;
    }


    /* =========================================================
       HEADER
    ========================================================= */

    .nacp-header {

        position: relative;

        height: 78px;

        display: flex;

        align-items: flex-start;

        justify-content: center;
    }


    .nacp-coat {

        position: absolute;

        left: -92px;
        top: 0;

        width: 65px;
        height: 70px;

        object-fit: contain;
    }


    .nacp-logo {

        width: 105px;
        height: 72px;

        object-fit: contain;
    }


    .nacp-rcg-logo {

        position: absolute;

        right: -2px;
        top: 0;

        width: 55px;
        height: 70px;

        object-fit: contain;
    }


    /* =========================================================
       ORGANIZATION
    ========================================================= */

    .nacp-organization {

        margin-top: 2px;

        text-align: center;

        color: #111;
    }


    .nacp-organization h4 {

        margin: 0;

        font-size: 10px;

        font-weight: 800;

        line-height: 1.35;
    }


    .nacp-organization p {

        margin: 4px 0 0;

        font-size: 9px;

        font-weight: 700;

        color: #333;
    }


    /* =========================================================
       PHOTO
    ========================================================= */

    .nacp-photo-box {

        width: 76%;

        height: 225px;

        margin: 18px auto 0;

        background: #fff;

        border: 3px solid #fff;

        box-shadow:
            0 2px 8px rgba(0,0,0,.18);

        overflow: hidden;
    }


    .nacp-photo-box img {

        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;
    }


    .nacp-photo-placeholder {

        width: 100%;
        height: 100%;

        display: flex;

        justify-content: center;
        align-items: center;

        background: #f2f2f2;

        color: #999;

        font-size: 12px;
        font-weight: 700;
    }


    /* =========================================================
       MEMBER NAME
    ========================================================= */

    .nacp-name {

        margin-top: 12px;

        text-align: center;

        color: #282828;
    }


    .nacp-name h3 {

        margin: 0;

        font-size: 21px;

        font-weight: 900;

        line-height: 1.1;
    }


    .nacp-name h4 {

        margin: 5px 0 0;

        font-size: 12px;

        font-weight: 900;

        color: #ed1c24;

        text-transform: uppercase;
    }


    /* =========================================================
       CATEGORY BAR
    ========================================================= */

    .nacp-role {

        width: 82%;

        margin-top: 18px;

        margin-left: 18%;

        padding: 9px 4px;

        background: #ed1c24;

        color: #fff;

        text-align: center;

        box-shadow:
            0 0 0 1px #ed1c24;
    }


    .nacp-role h3 {

        margin: 0;

        font-size: 18px;

        font-weight: 900;

        line-height: 1;
    }


    /* =========================================================
       MEMBERSHIP LABEL
    ========================================================= */

    .nacp-membership-label {

        margin-top: 10px;

        padding-right: 12px;

        text-align: right;

        color: #253e9a;

        font-size: 10px;

        font-weight: 800;

        line-height: 1;
    }


    /* =========================================================
       BACK CARD
    ========================================================= */

    .nacp-back-content {

        position: absolute;

        left: 0;
        top: 0;

        width: 100%;
        height: 100%;

        padding: 20px;
    }


    .nacp-back-header {

        text-align: center;

        border-bottom: 3px solid #00a651;

        padding-bottom: 10px;
    }


    .nacp-back-header img {

        width: 85px;

        height: 55px;

        object-fit: contain;
    }


    .nacp-back-header h3 {

        margin: 5px 0 0;

        font-size: 14px;

        font-weight: 900;

        color: #222;
    }


    .nacp-back-header p {

        margin: 3px 0 0;

        font-size: 9px;

        font-weight: 700;
    }


    /* =========================================================
       BACK MEMBERSHIP NUMBER
    ========================================================= */

    .nacp-back-number {

        margin-top: 15px;

        text-align: center;
    }


    .nacp-back-number small {

        display: block;

        color: #777;

        font-size: 8px;

        font-weight: 700;

        text-transform: uppercase;
    }


    .nacp-back-number strong {

        display: block;

        margin-top: 3px;

        color: #111;

        font-size: 16px;

        letter-spacing: .5px;
    }


    /* =========================================================
       DETAILS
    ========================================================= */

    .nacp-details {

        margin-top: 15px;

        border: 1px solid #ddd;

        background: rgba(255,255,255,.8);
    }


    .nacp-detail-row {

        display: flex;

        border-bottom: 1px solid #ddd;
    }


    .nacp-detail-row:last-child {

        border-bottom: none;
    }


    .nacp-detail-label {

        width: 42%;

        padding: 7px 8px;

        background: #f1f1f1;

        color: #555;

        font-size: 9px;

        font-weight: 800;

        text-transform: uppercase;
    }


    .nacp-detail-value {

        width: 58%;

        padding: 7px 8px;

        color: #111;

        font-size: 9px;

        font-weight: 700;
    }


    /* =========================================================
       QR SECTION
    ========================================================= */

    .nacp-qr-section {

        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-top: 16px;

        padding: 10px;

        border: 1px solid #ddd;

        background: #fff;
    }


    .nacp-qr {

        width: 105px;
        height: 105px;

        display: flex;

        justify-content: center;
        align-items: center;
    }


    .nacp-qr svg {

        width: 100%;
        height: 100%;
    }


    .nacp-qr-text {

        width: calc(100% - 120px);

        padding-left: 10px;
    }


    .nacp-qr-text strong {

        display: block;

        font-size: 10px;

        color: #00a651;

        margin-bottom: 5px;
    }


    .nacp-qr-text p {

        margin: 0;

        font-size: 8px;

        line-height: 1.5;

        color: #555;
    }


    /* =========================================================
       FOOTER
    ========================================================= */

    .nacp-back-footer {

        position: absolute;

        left: 20px;
        right: 20px;
        bottom: 15px;

        text-align: center;

        border-top: 1px solid #ddd;

        padding-top: 7px;

        font-size: 7px;

        color: #666;

        line-height: 1.5;
    }


    .nacp-back-footer strong {

        color: #00a651;
    }


    /* =========================================================
       STATUS
    ========================================================= */

    .nacp-status {

        display: inline-block;

        margin-top: 8px;

        padding: 4px 12px;

        background: #00a651;

        color: #fff;

        border-radius: 20px;

        font-size: 8px;

        font-weight: 900;
    }


    /* =========================================================
       PAGE CONTROLS
    ========================================================= */

    .nacp-card-controls {

        text-align: center;

        margin-bottom: 30px;
    }


    .nacp-card-controls h2 {

        margin-bottom: 5px;

        font-weight: 800;

        color: #1d1d1d;
    }


    .nacp-card-controls p {

        color: #777;

        margin-bottom: 15px;
    }


    /* =========================================================
       PRINT
    ========================================================= */

    @media print {

        @page {

            size: A4 portrait;

            margin: 10mm;
        }


        body {

            background: #fff !important;
        }


        .nacp-card-page {

            padding: 0;

            background: #fff;
        }


        .nacp-card-controls,
        .dashboard-sidebar,
        .navbar,
        header,
        footer {

            display: none !important;
        }


        .nacp-card-wrapper {

            display: flex;

            flex-direction: row;

            justify-content: center;

            align-items: flex-start;

            gap: 15mm;
        }


        .nacp-id-card {

            box-shadow: none;

            page-break-inside: avoid;
        }
    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 767px) {

        .nacp-card-page {

            padding: 20px 10px 40px;
        }


        .nacp-card-wrapper {

            gap: 25px;
        }


        .nacp-id-card {

            transform-origin: top center;

            max-width: 100%;
        }
    }


    @media (max-width: 390px) {

        .nacp-id-card {

            width: 350px;

            height: 570px;
        }


        .nacp-green-strip {

            width: 68px;
        }


        .nacp-card-content {

            left: 68px;

            width: calc(100% - 68px);
        }


        .nacp-photo-box {

            height: 210px;
        }
    }

</style>


<div class="nacp-card-page">


    {{-- =====================================================
         PAGE HEADER
    ====================================================== --}}

    <div class="nacp-card-controls">

        <h2>
            Membership ID Card
        </h2>

        <p>
            Official NACPDEAN Membership Identification Card
        </p>

        @if ($card)

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-success px-4"
            >
                <i class="bi bi-printer"></i>
                Print / Save Card
            </button>

        @endif

    </div>


    @if (!$card)

        {{-- =================================================
             NO CARD
        ================================================== --}}

        <div class="container">

            <div class="alert alert-warning text-center">

                <h5 class="mb-2">
                    Membership Card Not Available
                </h5>

                <p class="mb-0">
                    Your membership card has not been generated yet.
                    Please contact the NACPDEAN administration.
                </p>

            </div>

        </div>

    @else


        @php

            $profile = $card->profile;

            $membership = $card->membership;

            $category = $card->category;

            $fullName = strtoupper(
                trim(
                    ($profile?->surname ?? '') . ' ' .
                    ($profile?->first_name ?? '') . ' ' .
                    ($profile?->middle_name ?? '')
                )
            );

            /*
             * Membership category
             */
            $categoryName = strtoupper(
                $category?->name ?? 'MEMBER'
            );

            /*
             * RCG detection
             */
            $isRcg =
                strtoupper($category?->code ?? '') === 'RCG'
                ||
                str_contains(
                    strtoupper($categoryName),
                    'RCG'
                );

            /*
             * Verification URL
             */
            $verificationUrl = route(
                'membership.verify',
                $card->qr_token
            );

        @endphp


        <div class="nacp-card-wrapper">


            {{-- =================================================
                 FRONT OF CARD
            ================================================== --}}

            <div class="nacp-id-card">


                {{-- GREEN STRIP --}}

                <div class="nacp-green-strip">

                    <div class="nacp-green-triangle"></div>

                    <div class="nacp-vertical-id">

                        {{ $card->membership_number }}

                    </div>

                </div>


                {{-- CARD CONTENT --}}

                <div class="nacp-card-content">


                    {{-- HEADER --}}

                    <div class="nacp-header">


                        {{-- COAT OF ARMS --}}

                        <img
                            src="{{ asset('images/coat.png') }}"
                            class="nacp-coat"
                            alt="Nigeria Coat of Arms"
                        >


                        {{-- NACPDEAN LOGO --}}

                        <img
                            src="{{ asset('images/logo.png') }}"
                            class="nacp-logo"
                            alt="NACPDEAN Logo"
                        >


                        {{-- RCG LOGO --}}

                        @if ($isRcg)

                            <img
                                src="{{ asset('images/rcc.jpeg') }}"
                                class="nacp-rcg-logo"
                                alt="RCG Logo"
                            >

                        @endif

                    </div>


                    {{-- ORGANIZATION --}}

                    <div class="nacp-organization">

                        <h4>

                            NATIONAL ASSOCIATION OF CHARCOAL
                            PRODUCERS, DEALERS, EXPORTERS AND
                            AFFORESTATION OF NIGERIA

                        </h4>

                        <p>
                            CAC/IT/NO.182068
                        </p>

                    </div>


                    {{-- PHOTO --}}

                    <div class="nacp-photo-box">

                        @if ($profile?->photo)

                            <img
                                src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                alt="{{ $fullName }}"
                            >

                        @else

                            <div class="nacp-photo-placeholder">
                                MEMBER PHOTO
                            </div>

                        @endif

                    </div>


                    {{-- NAME --}}

                    <div class="nacp-name">

                        <h3>
                            {{ $fullName }}
                        </h3>

                        <h4>

                            {{ strtoupper(
                                $category?->name ?? 'NACPDEAN MEMBER'
                            ) }}

                        </h4>

                    </div>


                    {{-- CATEGORY --}}

                    <div class="nacp-role">

                        <h3>
                            {{ $categoryName }}
                        </h3>

                    </div>


                    {{-- MEMBER LABEL --}}

                    <div class="nacp-membership-label">

                        MEMBER: NACPDEAN

                    </div>

                </div>

            </div>


            {{-- =================================================
                 BACK OF CARD
            ================================================== --}}

            <div class="nacp-id-card">


                <div class="nacp-back-content">


                    {{-- BACK HEADER --}}

                    <div class="nacp-back-header">

                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="NACPDEAN Logo"
                        >

                        <h3>
                            MEMBERSHIP IDENTIFICATION CARD
                        </h3>

                        <p>
                            NATIONAL ASSOCIATION OF CHARCOAL
                            PRODUCERS, DEALERS, EXPORTERS AND
                            AFFORESTATION OF NIGERIA
                        </p>

                    </div>


                    {{-- MEMBERSHIP NUMBER --}}

                    <div class="nacp-back-number">

                        <small>
                            Membership Number
                        </small>

                        <strong>
                            {{ $card->membership_number }}
                        </strong>

                        <span class="nacp-status">
                            ACTIVE MEMBER
                        </span>

                    </div>


                    {{-- DETAILS --}}

                    <div class="nacp-details">


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                Member
                            </div>

                            <div class="nacp-detail-value">
                                {{ $fullName }}
                            </div>

                        </div>


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                Category
                            </div>

                            <div class="nacp-detail-value">
                                {{ $category?->name ?? 'N/A' }}
                            </div>

                        </div>


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                Card Number
                            </div>

                            <div class="nacp-detail-value">
                                {{ $card->card_number }}
                            </div>

                        </div>


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                State
                            </div>

                            <div class="nacp-detail-value">
                                {{ $profile?->state ?? 'N/A' }}
                            </div>

                        </div>


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                Issue Date
                            </div>

                            <div class="nacp-detail-value">

                                {{ optional($card->issued_at)->format('d M Y') }}

                            </div>

                        </div>


                        <div class="nacp-detail-row">

                            <div class="nacp-detail-label">
                                Expiry Date
                            </div>

                            <div class="nacp-detail-value">

                                {{ optional($card->expires_at)->format('d M Y') }}

                            </div>

                        </div>


                    </div>


                    {{-- =================================================
                         QR CODE
                    ================================================== --}}

                    <div class="nacp-qr-section">


                        <div class="nacp-qr">

                            {!! QrCode::size(105)
                                ->margin(0)
                                ->generate($verificationUrl) !!}

                        </div>


                        <div class="nacp-qr-text">

                            <strong>
                                SCAN TO VERIFY MEMBERSHIP
                            </strong>

                            <p>

                                Scan this QR code using a
                                smartphone camera to verify
                                the authenticity and current
                                status of this membership card.

                            </p>

                            <p style="margin-top:5px; word-break:break-all;">

                                {{ $verificationUrl }}

                            </p>

                        </div>

                    </div>


                    {{-- FOOTER --}}

                    <div class="nacp-back-footer">

                        <strong>
                            IMPORTANT
                        </strong>

                        <br>

                        This card remains the property of NACPDEAN
                        and must be surrendered upon request.

                        <br>

                        Verification is available through the
                        official NACPDEAN membership database.

                    </div>


                </div>

            </div>


        </div>

    @endif

</div>

@endsection
