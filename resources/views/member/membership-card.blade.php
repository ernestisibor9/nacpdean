@extends('member.member_dashboard')

@section('title', "NACPDEAN - Member's ID Card")

@section('member')

<div class="container-fluid py-4">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h3 class="mb-1">
                        Membership Card
                    </h3>

                    <p class="text-muted mb-0">
                        Your official NACPDEAN membership identification card
                    </p>
                </div>

                <a
                    href="{{ route('member.member_dashboard') }}"
                    class="btn btn-outline-secondary"
                >
                    Dashboard
                </a>

            </div>

        </div>
    </div>


    {{-- =========================================================
        NO CARD
    ========================================================== --}}
    @if (!$card)

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center py-5">

                        <div class="mb-3">
                            <i
                                class="bi bi-card-heading"
                                style="font-size: 50px;"
                            ></i>
                        </div>

                        <h5>
                            Membership Card Not Available
                        </h5>

                        <p class="text-muted mb-0">
                            Your membership card will become available
                            after your application has been approved.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    @else

        {{-- =========================================================
            DYNAMIC CARD DATA
        ========================================================== --}}
        @php

            /*
            |--------------------------------------------------------------------------
            | MEMBER NAME
            |--------------------------------------------------------------------------
            */

            $memberName = strtoupper(
                trim(
                    ($card->profile?->surname ?? '') . ' ' .
                    ($card->profile?->first_name ?? '') . ' ' .
                    ($card->profile?->middle_name ?? '')
                )
            );


            /*
            |--------------------------------------------------------------------------
            | POSITION
            |--------------------------------------------------------------------------
            |
            | If your MembershipCard table does not yet have position,
            | this safely returns null.
            |
            */

            $position = $card->position ?? null;


            /*
            |--------------------------------------------------------------------------
            | AFFILIATE
            |--------------------------------------------------------------------------
            */

            $affiliate = $card->affiliate_body ?? null;


            /*
            |--------------------------------------------------------------------------
            | CATEGORY
            |--------------------------------------------------------------------------
            */

            $category = strtoupper(
                $card->category?->name ?? 'MEMBER'
            );


            /*
            |--------------------------------------------------------------------------
            | RCG MEMBER
            |--------------------------------------------------------------------------
            |
            | If is_rcg_member exists on MembershipCard, use it.
            |
            */

            $isRcgMember = (bool) ($card->is_rcg_member ?? false);


            /*
            |--------------------------------------------------------------------------
            | MEMBERSHIP NUMBER
            |--------------------------------------------------------------------------
            */

            $membershipNumber = $card->membership_number ?? '';


            /*
            |--------------------------------------------------------------------------
            | CARD NUMBER
            |--------------------------------------------------------------------------
            */

            $cardNumber = $card->card_number ?? '';


            /*
            |--------------------------------------------------------------------------
            | PHOTO
            |--------------------------------------------------------------------------
            */

            $photoUrl = $card->profile?->photo
                ? asset(
                    'uploads/member_profiles/' .
                    $card->profile->photo
                )
                : asset('images/photo-placeholder.png');


            /*
            |--------------------------------------------------------------------------
            | QR VERIFICATION URL
            |--------------------------------------------------------------------------
            */

            $verificationUrl = route(
                'membership.verify',
                $card->qr_token
            );

        @endphp


        {{-- =========================================================
            CARD PREVIEW AREA
        ========================================================== --}}
        <div class="row justify-content-center">

            <div class="col-12">

                <div
                    id="id-card-print-area"
                    class="id-card-preview"
                >


                    {{-- =================================================
                        FRONT OF CARD
                    ================================================== --}}
                    <div class="bussy-id-card">

                        {{-- GREEN SIDE STRIP --}}
                        <div class="bussy-green-strip">

                            <div class="bussy-green-triangle"></div>

                            <div class="bussy-vertical-id">
                                {{ $membershipNumber }}
                            </div>

                        </div>


                        {{-- MAIN CARD CONTENT --}}
                        <div class="bussy-card-content">


                            {{-- =========================================
                                HEADER LOGOS
                            ========================================== --}}
                            <div class="bussy-header">

                                {{-- NIGERIA COAT OF ARMS --}}
                                <img
                                    src="{{ asset('frontend/assets/img/coat.png') }}"
                                    class="bussy-coat-of-arms"
                                    alt="Nigeria Coat of Arms"
                                >


                                {{-- NACPDEAN LOGO --}}
                                <div class="bussy-nacpdean-logo">

                                    <img
                                        src="{{ asset('frontend/assets/img/logo.png') }}"
                                        class="bussy-logo"
                                        alt="NACPDEAN Logo"
                                    >

                                </div>


                                {{-- RCG LOGO --}}
                                @if ($isRcgMember)

                                    <img
                                        src="{{ asset('images/rcc.jpeg') }}"
                                        class="bussy-rcg-logo"
                                        alt="RCG Logo"
                                    >

                                @endif

                            </div>


                            {{-- =========================================
                                ORGANIZATION
                            ========================================== --}}
                            <div class="bussy-organization">

                                <h4>
                                    NATIONAL ASSOCIATION OF CHARCOAL
                                    PRODUCERS, DEALERS,
                                    <br>
                                    EXPORTERS AND AFFORESTATION OF NIGERIA
                                </h4>

                                <p>
                                    CAC/IT/NO.182068
                                </p>

                            </div>


                            {{-- =========================================
                                MEMBER PHOTO
                            ========================================== --}}
                            <div class="bussy-photo-box">

                                <img
                                    src="{{ $photoUrl }}"
                                    class="w-100 img-fluid"
                                    alt="{{ $memberName }}"
                                >

                            </div>


                            {{-- =========================================
                                MEMBER NAME / POSITION
                            ========================================== --}}
                            <div class="bussy-name">

                                <h3>
                                    {{ $memberName }}
                                </h3>

                                @if ($position)

                                    <h4>
                                        {{ strtoupper($position) }}
                                    </h4>

                                @endif

                            </div>


                            {{-- =========================================
                                MEMBERSHIP CATEGORY
                            ========================================== --}}
                            <div class="bussy-role">

                                <h3>
                                    {{ $category }}
                                </h3>

                            </div>


                            {{-- =========================================
                                AFFILIATION
                            ========================================== --}}
                            @if ($affiliate)

                                <div class="bussy-membership">

                                    MEMBER:
                                    {{ strtoupper($affiliate) }}

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- =================================================
                        BACK OF CARD
                    ================================================== --}}
                    <div class="bussy-id-card4">


                        {{-- BACK CONTENT --}}
                        <div class="bussy-back-content">


                            {{-- =========================================
                                HEADER
                            ========================================== --}}
                            <div class="bussy-back-header">

                                <h4>
                                    NACPDEAN ELECTRONIC
                                </h4>

                                <h4>
                                    MEMBERSHIP IDENTIFICATION CARD
                                </h4>

                                <div class="bussy-qr-enabled">
                                    (QR ENABLED)
                                </div>

                            </div>


                            {{-- =========================================
                                QR CODE
                            ========================================== --}}
                            <div class="bussy-qr-wrapper">

                                {!! QrCode::size(175)->generate(
                                    $verificationUrl
                                ) !!}

                            </div>


                            <div class="bussy-scan-me">
                                SCAN ME TO VERIFY
                            </div>


                            {{-- =========================================
                                CARD NUMBER
                            ========================================== --}}
                            <div class="bussy-back-card-number">

                                <div>
                                    CARD NUMBER
                                </div>

                                <strong>
                                    {{ $cardNumber }}
                                </strong>

                            </div>


                            {{-- =========================================
                                MEMBERSHIP NUMBER
                            ========================================== --}}
                            <div class="bussy-back-card-number">

                                <div>
                                    MEMBERSHIP NUMBER
                                </div>

                                <strong>
                                    {{ $membershipNumber }}
                                </strong>

                            </div>


                            {{-- =========================================
                                CONTACT
                            ========================================== --}}
                            <div class="bussy-contact">

                                <p class="mb-1">
                                    IF FOUND PLEASE CALL:
                                </p>

                                <strong>
                                    +234 803 667 0360
                                </strong>

                                <br>

                                <strong>
                                    +234 814 567 2358
                                </strong>

                            </div>


                            {{-- =========================================
                                WEBSITE / EMAIL
                            ========================================== --}}
                            <div class="bussy-contact-footer">

                                <div>
                                    Email:
                                    <strong>
                                        info@nacpdean.org
                                    </strong>
                                </div>

                                <div>
                                    Website:
                                    <strong>
                                        www.nacpdean.org
                                    </strong>
                                </div>

                            </div>


                            {{-- =========================================
                                OFFICIAL NOTICE
                            ========================================== --}}
                            <div class="bussy-verification-note">

                                This card is electronically verifiable
                                through the official NACPDEAN membership
                                verification system.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            CARD INFORMATION
        ========================================================== --}}
        <div class="row justify-content-center mt-4">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-4 mb-3 mb-md-0">

                                <small class="text-muted d-block">
                                    Membership Number
                                </small>

                                <strong>
                                    {{ $membershipNumber }}
                                </strong>

                            </div>


                            <div class="col-md-4 mb-3 mb-md-0">

                                <small class="text-muted d-block">
                                    Category
                                </small>

                                <strong>
                                    {{ $category }}
                                </strong>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted d-block">
                                    Status
                                </small>

                                @if ($card->status === 'active')

                                    <span class="badge bg-success">
                                        ACTIVE
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        {{ strtoupper($card->status) }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            ACTION BUTTONS
        ========================================================== --}}
        <div class="text-center mt-4">

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-primary px-4"
            >
                <i class="bi bi-printer me-1"></i>
                Print / Save Card
            </button>

        </div>

    @endif

</div>


<style>

/* ================================================================
   PAGE
================================================================ */

.id-card-preview {

    display: flex;

    justify-content: center;

    align-items: flex-start;

    gap: 30px;

    width: 100%;

    overflow-x: auto;

    padding: 20px;

}


/* ================================================================
   COMMON CARD
================================================================ */

.bussy-id-card,
.bussy-id-card4 {

    position: relative;

    width: 390px;

    height: 600px;

    flex: 0 0 390px;

    overflow: hidden;

    border-radius: 6px;

}


/* ================================================================
   FRONT CARD
================================================================ */

.bussy-id-card {

    background:
        radial-gradient(
            circle at 30% 20%,
            rgba(255,255,255,.98),
            rgba(232,232,232,.92)
        );

    border: 1px solid #d7d7d7;

    box-shadow:
        0 8px 25px rgba(0,0,0,.20);

}


/* ================================================================
   GREEN SIDE STRIP
================================================================ */

.bussy-green-strip {

    position: absolute;

    left: 0;

    top: 0;

    width: 20mm;

    height: 100%;

    background: #00a651;

    z-index: 1;

}


/* ================================================================
   LIGHT GREEN TRIANGLE
================================================================ */

.bussy-green-triangle {

    position: absolute;

    bottom: 0;

    left: 0;

    width: 0;

    height: 0;

    border-top: 20mm solid #d4e99a;

    border-right: 20mm solid transparent;

    transform: rotate(180deg);

}


/* ================================================================
   VERTICAL MEMBERSHIP NUMBER
================================================================ */

.bussy-vertical-id {

    position: absolute;

    left: 0;

    top: 25%;

    width: 100%;

    padding-right: 12px;

    color: white;

    font-size: 6.1mm;

    font-weight: 800;

    letter-spacing: .25mm;

    text-align: center;

    writing-mode: vertical-rl;

    transform: rotate(180deg);

    white-space: nowrap;

}


/* ================================================================
   FRONT CONTENT
================================================================ */

.bussy-card-content {

    position: absolute;

    left: 20mm;

    top: 0;

    width: calc(100% - 20mm);

    height: 100%;

    padding: 2.2mm 2.2mm 1.5mm;

    z-index: 2;

}


/* ================================================================
   HEADER
================================================================ */

.bussy-header {

    position: relative;

    height: 13mm;

    display: flex;

    align-items: flex-start;

    justify-content: center;

}


/* ================================================================
   COAT OF ARMS
================================================================ */

.bussy-coat-of-arms {

    position: absolute;

    left: -23.5mm;

    top: 0;

    height: 78px;

    width: auto;

    object-fit: contain;

}


/* ================================================================
   NACPDEAN LOGO
================================================================ */

.bussy-nacpdean-logo {

    position: relative;

    margin-top: -2%;

    text-align: center;

}


.bussy-logo {

    max-width: 150px;

    max-height: 85px;

    object-fit: contain;

}


/* ================================================================
   RCG LOGO
================================================================ */

.bussy-rcg-logo {

    position: absolute;

    right: -11mm;

    top: 1mm;

    width: 12mm;

    height: 16mm;

    object-fit: contain;

}


/* ================================================================
   ORGANIZATION
================================================================ */

.bussy-organization {

    margin-top: 13%;

    text-align: center;

    color: #111;

    max-width: 100%;

}


.bussy-organization h4 {

    margin: 0;

    font-size: 11px;

    font-weight: 700;

    line-height: 1.5;

}


.bussy-organization p {

    margin-top: 4px;

    margin-bottom: 0;

    font-size: 14px;

    font-weight: 600;

    line-height: 1.5;

}


/* ================================================================
   MEMBER PHOTO
================================================================ */

.bussy-photo-box {

    width: 80%;

    height: 222px;

    margin: 7mm auto 0;

    background: #fff;

    overflow: hidden;

    border: 1px solid #ddd;

}


.bussy-photo-box img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

}


/* ================================================================
   MEMBER NAME
================================================================ */

.bussy-name {

    margin-top: 2.5mm;

    text-align: center;

    color: #282828;

}


.bussy-name h3 {

    margin: 0;

    font-size: 23px;

    font-weight: 700;

    line-height: 1.15;

}


.bussy-name h4 {

    margin: 4px 0 0;

    font-size: 18px;

    font-weight: 900;

    color: #ed1c24;

    line-height: 1.1;

}


/* ================================================================
   CATEGORY / ROLE
================================================================ */

.bussy-role {

    width: 80%;

    margin-top: 10%;

    margin-left: 20%;

    padding: 1.2mm 0;

    background: #ed1c24;

    color: white;

    text-align: center;

    box-shadow: 0 0 0 1px #ed1c24;

}


.bussy-role h3 {

    margin: 0;

    font-size: 25px;

    font-weight: 900;

    line-height: 1;

}


/* ================================================================
   AFFILIATION
================================================================ */

.bussy-membership {

    margin-top: 8px;

    padding-right: 20px;

    color: #253e9a;

    font-size: 12px;

    font-weight: 700;

    line-height: 1;

    text-align: right;

}


/* ================================================================
   BACK CARD
================================================================ */

.bussy-id-card4 {

    background: #ffffff;

    border: 1px solid #000;

    box-shadow:
        0 8px 25px rgba(0,0,0,.20);

}


/* ================================================================
   BACK CONTENT
================================================================ */

.bussy-back-content {

    width: 100%;

    height: 100%;

    padding: 30px 28px;

    display: flex;

    flex-direction: column;

    align-items: center;

    text-align: center;

}


/* ================================================================
   BACK HEADER
================================================================ */

.bussy-back-header {

    width: 100%;

    margin-bottom: 10px;

}


.bussy-back-header h4 {

    margin: 0;

    color: #111;

    font-size: 16px;

    font-weight: 900;

    line-height: 1.3;

}


.bussy-qr-enabled {

    margin-top: 5px;

    font-size: 11px;

    color: #777;

    font-weight: 600;

}


/* ================================================================
   QR CODE
================================================================ */

.bussy-qr-wrapper {

    margin-top: 15px;

    padding: 12px;

    background: white;

    border: 1px solid #ddd;

    display: flex;

    justify-content: center;

    align-items: center;

}


.bussy-qr-wrapper svg {

    display: block;

    width: 175px;

    height: 175px;

}


/* ================================================================
   SCAN ME
================================================================ */

.bussy-scan-me {

    margin-top: 10px;

    font-size: 15px;

    font-weight: 900;

    color: #00a651;

    letter-spacing: .5px;

}


/* ================================================================
   BACK CARD NUMBER
================================================================ */

.bussy-back-card-number {

    width: 100%;

    margin-top: 15px;

    padding: 8px 10px;

    background: #f6f7f7;

    border: 1px solid #e1e1e1;

}


.bussy-back-card-number div {

    font-size: 9px;

    font-weight: 700;

    color: #777;

    letter-spacing: .5px;

}


.bussy-back-card-number strong {

    display: block;

    margin-top: 2px;

    color: #111;

    font-size: 12px;

    font-weight: 900;

    word-break: break-word;

}


/* ================================================================
   CONTACT
================================================================ */

.bussy-contact {

    width: 100%;

    margin-top: 17px;

    font-size: 11px;

    line-height: 1.5;

    color: #222;

}


.bussy-contact p {

    margin-bottom: 3px;

}


.bussy-contact strong {

    font-size: 12px;

}


/* ================================================================
   FOOTER CONTACT
================================================================ */

.bussy-contact-footer {

    width: 100%;

    margin-top: 12px;

    padding-top: 10px;

    border-top: 1px solid #ddd;

    color: #333;

    font-size: 9px;

    line-height: 1.7;

}


.bussy-contact-footer strong {

    font-weight: 800;

}


/* ================================================================
   VERIFICATION NOTE
================================================================ */

.bussy-verification-note {

    margin-top: auto;

    padding: 8px 10px;

    width: 100%;

    background: #f1f8f3;

    border: 1px solid #cfe8d7;

    color: #22613a;

    font-size: 8px;

    line-height: 1.4;

}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (max-width: 850px) {

    .id-card-preview {

        justify-content: flex-start;

        padding-left: 15px;

        padding-right: 15px;

    }

}


@media (max-width: 576px) {

    .id-card-preview {

        gap: 20px;

    }

    .bussy-id-card,
    .bussy-id-card4 {

        width: 350px;

        height: 538px;

        flex-basis: 350px;

    }

    .bussy-photo-box {

        height: 195px;

    }

    .bussy-name h3 {

        font-size: 20px;

    }

    .bussy-role h3 {

        font-size: 21px;

    }

    .bussy-organization h4 {

        font-size: 9.5px;

    }

}


/* ================================================================
   PRINT
================================================================ */

@media print {

    @page {

        size: auto;

        margin: 0;

    }


    html,
    body {

        margin: 0 !important;

        padding: 0 !important;

        background: white !important;

    }


    body * {

        visibility: hidden;

    }


    #id-card-print-area,
    #id-card-print-area * {

        visibility: visible;

    }


    #id-card-print-area {

        position: absolute;

        left: 0;

        top: 0;

        width: 100%;

        display: flex;

        justify-content: center;

        gap: 20px;

        padding: 0;

        margin: 0;

        overflow: visible;

    }


    .bussy-id-card,
    .bussy-id-card4 {

        box-shadow: none !important;

        flex: 0 0 390px;

    }

}

</style>

@endsection
