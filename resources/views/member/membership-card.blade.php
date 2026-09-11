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
                    Membership Cards
                </h3>

                <p class="text-muted mb-0">
                    Your official NACPDEAN membership identification cards
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
@if ($cards->isEmpty())

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
        ALL ACTIVE CARDS
    ========================================================== --}}
    <div id="id-card-print-area">

        @foreach ($cards as $card)

            @php

                /*
                |--------------------------------------------------------------------------
                | CARD TYPE
                |--------------------------------------------------------------------------
                */

                $cardTypeLabel = match ($card->card_type) {

                    'national_executive' =>
                        'National Executive Card',

                    'task_force' =>
                        'Task Force Card',

                    default =>
                        'Membership Card',

                };


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
                | TASK FORCE APPOINTMENT
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | Task Force information must come from the approved
                | MembershipOfficerAppointment record.
                |
                | The controller attaches the approved appointment
                | to the card as:
                |
                | $card->taskForceAppointment
                |
                */

                $taskForceAppointment =
                    $card->taskForceAppointment ?? null;


                /*
                |--------------------------------------------------------------------------
                | POSITION
                |--------------------------------------------------------------------------
                |
                | Membership card:
                |     Uses memberships.membership_position.
                |
                | National Executive card:
                |     Uses memberships.membership_position.
                |
                | Task Force card:
                |     Uses the approved appointment position.
                |
                */

                $position = match ($card->card_type) {

                    'national_executive' =>
                        $card->membership?->membership_position,

                    'task_force' =>
                        $taskForceAppointment?->position,

                    'membership' =>
                        $card->membership?->membership_position,

                    default =>
                        null,

                };


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
                | CATEGORY CODE
                |--------------------------------------------------------------------------
                |
                | Drives which visual template + colour theme the
                | 'membership' card_type renders with:
                |
                | EXP / RCG
                |     -> classic strip layout
                |
                | DEA / PRD / SLR
                |     -> category-colour layout
                |
                */

                $categoryCode = strtoupper(
                    trim($card->category?->code ?? '')
                );

                $categoryUsesClassicLayout = in_array(
                    $categoryCode,
                    ['EXP', 'RCG'],
                    true
                );

                $categoryThemeClass = match ($categoryCode) {
                    'DEA' => 'category-theme-dea',
                    'PRD' => 'category-theme-prd',
                    'SLR' => 'category-theme-slr',
                    default => 'category-theme-dea',
                };


                /*
                |--------------------------------------------------------------------------
                | MEMBER TYPE
                |--------------------------------------------------------------------------
                */

                $memberType = strtolower(
                    $card->category?->member_type ?? ''
                );


                /*
                |--------------------------------------------------------------------------
                | RCG MEMBER
                |--------------------------------------------------------------------------
                */

                $isRcgMember = $memberType === 'affiliate';


                /*
                |--------------------------------------------------------------------------
                | AFFILIATE BODY
                |--------------------------------------------------------------------------
                */

                $affiliate = $card->affiliate_body ?? null;


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
                | MEMBER PHOTO
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
                | QR IMAGE
                |--------------------------------------------------------------------------
                |
                | Generated by MembershipCardController using
                | QrCodeService::generateMembershipCard().
                |
                */

                $qrImage = $card->qr_image ?? null;


                /*
                |--------------------------------------------------------------------------
                | TASK FORCE LEVEL
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | Read level from the approved appointment.
                |
                | DO NOT use:
                |
                | $card->membership?->taskforce_level
                |
                */

                $taskForceLevel = strtolower(
                    trim(
                        $taskForceAppointment?->level ?? ''
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | TASK FORCE STATE
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | Read state from the approved appointment.
                |
                | DO NOT use:
                |
                | $card->membership?->taskforce_state
                |
                */

                $taskForceState = strtoupper(
                    trim(
                        $taskForceAppointment?->state ?? ''
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | NATIONAL TASK FORCE
                |--------------------------------------------------------------------------
                */

                $isNationalTaskForce =
                    $card->card_type === 'task_force' &&
                    in_array(
                        $taskForceLevel,
                        [
                            'national',
                            'national level',
                            'national-level',
                        ],
                        true
                    );


                /*
                |--------------------------------------------------------------------------
                | STATE TASK FORCE
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | A card is considered State Task Force ONLY when
                | the appointment explicitly says "state".
                |
                | This prevents a blank/invalid level from automatically
                | becoming a State Task Force card.
                |
                */

                $isStateTaskForce =
                    $card->card_type === 'task_force' &&
                    $taskForceLevel === 'state';


                /*
                |--------------------------------------------------------------------------
                | TASK FORCE ID NUMBER
                |--------------------------------------------------------------------------
                */

                $taskForceIdNo = $cardNumber;

            @endphp


            {{-- =================================================
                CARD GROUP
            ================================================== --}}
            <div class="card-group-wrapper">

                {{-- =================================================
                    CARD TYPE LABEL
                ================================================== --}}
                <div class="text-center mb-3 card-type-label">

                    <h5 class="mb-1">
                        {{ $cardTypeLabel }}
                    </h5>

                    @if ($card->card_type === 'national_executive')

                        <small class="text-muted">
                            National Executive Appointment
                        </small>

                    @elseif ($card->card_type === 'task_force')

                        <small class="text-muted">
                            Task Force Appointment
                        </small>

                    @else

                        <small class="text-muted">
                            General Membership
                        </small>

                    @endif

                </div>


                {{-- =================================================
                    CARD PREVIEW
                ================================================== --}}
                <div class="row justify-content-center">

                    <div class="col-12">

                        <div class="id-card-preview">


                            {{-- =================================================
                                MEMBERSHIP CARD — CLASSIC LAYOUT
                                (Exporter / RCG)
                            ================================================== --}}
                            @if ($card->card_type === 'membership' && $categoryUsesClassicLayout)

                                <div class="client-card bussy-id-card2">

                                    {{-- RIGHT GREEN STRIP --}}
                                    <div class="bussy-green-strip2">

                                        <div class="bussy-green-triangle"></div>

                                        <div class="bussy-vertical-id2">
                                            {{ $membershipNumber }}
                                        </div>

                                    </div>


                                    {{-- MAIN CONTENT --}}
                                    <div class="bussy-card-content2">

                                        {{-- HEADER --}}
                                        <div class="bussy-header">

                                            <img
                                                src="{{ asset('frontend/assets/img/coat.png') }}"
                                                class="bussy-coat-of-arms2"
                                                alt="Nigeria Coat of Arms"
                                            >


                                            {{-- NACPDEAN + RCG LOGOS --}}
                                            <div class="bussy-nacpdean-logo">

                                                <img
                                                    src="{{ asset('frontend/assets/img/logo.png') }}"
                                                    class="bussy-logo"
                                                    alt="NACPDEAN Logo"
                                                >

                                                @if ($isRcgMember)

                                                    <img
                                                        src="{{ asset('backend/assets/img/rcg.png') }}"
                                                        class="bussy-rcg-logo-inline"
                                                        alt="RCG Logo"
                                                    >

                                                @endif

                                            </div>

                                        </div>


                                        {{-- ORGANIZATION --}}
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


                                        {{-- PHOTO --}}
                                        <div class="bussy-photo-box">

                                            <img
                                                src="{{ $photoUrl }}"
                                                class="w-100 h-100"
                                                alt="{{ $memberName }}"
                                            >

                                        </div>


                                        {{-- NAME + MEMBERSHIP POSITION --}}
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


                                        {{-- CATEGORY --}}
                                        <div class="bussy-role">

                                            <h3>
                                                {{ $category }}
                                            </h3>

                                        </div>


                                        {{-- AFFILIATION --}}
                                        <div class="bussy-membership">

                                            MEMBER:
                                            {{ $affiliate ? strtoupper($affiliate) : 'FACAN' }}

                                        </div>

                                    </div>

                                </div>


                            {{-- =================================================
                                MEMBERSHIP CARD — CATEGORY LAYOUT
                                (Dealer / Producer / Supplier)
                            ================================================== --}}
                            @elseif ($card->card_type === 'membership' && !$categoryUsesClassicLayout)

                                <div class="client-card category-card {{ $categoryThemeClass }}">

                                    {{-- RIGHT ACCENT STRIP --}}
                                    <div class="category-accent-strip">

                                        <div class="category-accent-corner"></div>

                                        <div class="category-vertical-id">
                                            {{ $membershipNumber }}
                                        </div>

                                    </div>


                                    {{-- MAIN CONTENT --}}
                                    <div class="category-card-content">

                                        {{-- HEADER --}}
                                        <div class="category-header">

                                            <img
                                                src="{{ asset('frontend/assets/img/coat.png') }}"
                                                class="category-coat-of-arms"
                                                alt="Nigeria Coat of Arms"
                                            >

                                            <img
                                                src="{{ asset('frontend/assets/img/logo.png') }}"
                                                class="category-logo"
                                                alt="NACPDEAN Logo"
                                            >

                                        </div>


                                        {{-- ORGANIZATION --}}
                                        <div class="category-organization">

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


                                        {{-- CATEGORY LABEL --}}
                                        <div class="category-label">
                                            {{ $category }}
                                        </div>


                                        {{-- PHOTO --}}
                                        <div class="category-photo-box">

                                            <img
                                                src="{{ $photoUrl }}"
                                                class="w-100 h-100"
                                                alt="{{ $memberName }}"
                                            >

                                        </div>


                                        {{-- NAME + POSITION --}}
                                        <div class="category-name">

                                            <h3>
                                                {{ $memberName }}
                                            </h3>

                                            <h4>
                                                {{ $position ? strtoupper($position) : 'MEMBER' }}
                                            </h4>

                                        </div>


                                        {{-- AFFILIATION --}}
                                        <div class="category-affiliate">

                                            MEMBER:
                                            {{ $affiliate ? strtoupper($affiliate) : 'FACAN' }}

                                        </div>

                                    </div>

                                </div>


                            {{-- =================================================
                                NATIONAL EXECUTIVE CARD
                            ================================================== --}}
                            @elseif ($card->card_type === 'national_executive')

                                <div class="client-card bussy-id-card">

                                    {{-- LEFT GREEN STRIP --}}
                                    <div class="bussy-green-strip">

                                        <div class="bussy-green-triangle"></div>

                                        <div class="bussy-vertical-id">
                                            {{ $membershipNumber }}
                                        </div>

                                    </div>


                                    {{-- MAIN CONTENT --}}
                                    <div class="bussy-card-content">

                                        {{-- HEADER --}}
                                        <div class="bussy-header">

                                            <img
                                                src="{{ asset('frontend/assets/img/coat.png') }}"
                                                class="bussy-coat-of-arms"
                                                alt="Nigeria Coat of Arms"
                                            >


                                            {{-- NACPDEAN + RCG LOGOS --}}
                                            <div class="bussy-nacpdean-logo">

                                                <img
                                                    src="{{ asset('frontend/assets/img/logo.png') }}"
                                                    class="bussy-logo"
                                                    alt="NACPDEAN Logo"
                                                >

                                                @if ($isRcgMember)

                                                    <img
                                                        src="{{ asset('backend/assets/img/rcg.png') }}"
                                                        class="bussy-rcg-logo-inline"
                                                        alt="RCG Logo"
                                                        width="90px"
                                                        height="60px"
                                                    >

                                                @endif

                                            </div>

                                        </div>


                                        {{-- ORGANIZATION --}}
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


                                        {{-- PHOTO --}}
                                        <div class="bussy-photo-box">

                                            <img
                                                src="{{ $photoUrl }}"
                                                class="w-100 h-100"
                                                alt="{{ $memberName }}"
                                            >

                                        </div>


                                        {{-- NAME + EXECUTIVE POSITION --}}
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


                                        {{-- EXECUTIVE MEMBERSHIP CATEGORY --}}
                                        <div class="bussy-role">

                                            <h3>
                                                {{ $category }}
                                            </h3>

                                        </div>


                                        {{-- AFFILIATE BODY --}}
                                        <div class="bussy-membership">

                                            MEMBER:
                                            {{ $affiliate ? strtoupper($affiliate) : 'FACAN' }}

                                        </div>

                                    </div>

                                </div>


                            {{-- =================================================
                                NATIONAL TASK FORCE
                            ================================================== --}}
                            @elseif ($isNationalTaskForce)

                                <div class="client-card ntf-card">

                                    <div class="ntf-top-wave"></div>
                                    <div class="ntf-bottom-wave"></div>

                                    {{-- HEADER --}}
                                    <div class="ntf-header">

                                        <img
                                            src="{{ asset('frontend/assets/img/logo.png') }}"
                                            class="ntf-logo"
                                            alt="NACPDEAN Logo"
                                        >

                                        <p class="ntf-org">
                                            NATIONAL ASSOCIATION OF CHARCOAL
                                            PRODUCERS, DEALERS,
                                            <br>
                                            EXPORTERS AND AFFORESTATION OF NIGERIA
                                        </p>

                                        <p class="ntf-cac">
                                            CAC/IT/NO.182068
                                        </p>

                                    </div>


                                    {{-- TITLE BAR --}}
                                    <div class="ntf-bar">
                                        NATIONAL TASK-FORCE
                                    </div>


                                    {{-- PHOTO + ID --}}
                                    <div class="ntf-body">

                                        <div class="ntf-photo-box">

                                            <img
                                                src="{{ $photoUrl }}"
                                                class="w-100 h-100"
                                                alt="{{ $memberName }}"
                                            >

                                        </div>

                                        <div class="ntf-side">

                                            <img
                                                src="{{ asset('frontend/assets/img/coat.png') }}"
                                                class="ntf-coat"
                                                alt="Nigeria Coat of Arms"
                                            >

                                            <div class="ntf-idno-label">
                                                ID NO.
                                            </div>

                                            <div class="ntf-idno-value">
                                                {{ $taskForceIdNo }}
                                            </div>

                                        </div>

                                    </div>


                                    {{-- NAME / POSITION --}}
                                    <div class="ntf-name">

                                        <h3>
                                            {{ $memberName }}
                                        </h3>

                                        @if ($position)

                                            <h4>
                                                {{ strtoupper($position) }}
                                            </h4>

                                        @endif

                                    </div>


                                    {{-- FOOTER --}}
                                    <div class="ntf-footer">
                                        SUSTAINABLE CHARCOAL GREENER NIGERIA
                                    </div>

                                </div>


                            {{-- =================================================
                                STATE TASK FORCE
                            ================================================== --}}
                            @elseif ($isStateTaskForce)

                                <div class="client-card stf-card">

                                    {{-- RIGHT STRIP --}}
                                    <div class="stf-strip">

                                        <div class="stf-strip-id">
                                            ID NO. {{ $taskForceIdNo }}
                                        </div>

                                    </div>


                                    <div class="stf-content">

                                        {{-- HEADER --}}
                                        <div class="stf-header">

                                            <img
                                                src="{{ asset('frontend/assets/img/coat.png') }}"
                                                class="stf-coat"
                                                alt="Nigeria Coat of Arms"
                                            >

                                            <img
                                                src="{{ asset('frontend/assets/img/logo.png') }}"
                                                class="stf-logo"
                                                alt="NACPDEAN Logo"
                                            >

                                            <p class="stf-orgname">
                                                NATIONAL ASSOCIATION OF CHARCOAL
                                                PRODUCERS, DEALERS,
                                                <br>
                                                EXPORTERS AND AFFORESTATION OF NIGERIA
                                            </p>

                                            <p class="stf-cac">
                                                CAC/IT/NO.182068
                                            </p>

                                        </div>


                                        {{-- TITLE PILL --}}
                                        <div class="stf-pill">
                                            STATE TASK-FORCE
                                        </div>


                                        {{-- STATE NAME --}}
                                        @if ($taskForceState)

                                            <div class="stf-state">
                                                {{ $taskForceState }}
                                            </div>

                                        @endif


                                        {{-- PHOTO --}}
                                        <div class="stf-photo-box">

                                            <img
                                                src="{{ $photoUrl }}"
                                                class="w-100 h-100"
                                                alt="{{ $memberName }}"
                                            >

                                        </div>


                                        {{-- NAME / POSITION --}}
                                        <div class="stf-name">

                                            <h3>
                                                {{ $memberName }}
                                            </h3>

                                            @if ($position)

                                                <h4>
                                                    {{ strtoupper($position) }}
                                                </h4>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- FOOTER --}}
                                    <div class="stf-footer">
                                        SUSTAINABLE CHARCOAL GREENER NIGERIA
                                    </div>

                                </div>

                            @endif


                            {{-- =================================================
                                BACK OF CARD
                            ================================================== --}}
                            <div class="bussy-id-card4">

                                <div class="bussy-back-content">

                                    {{-- HEADER --}}
                                    <div class="bussy-back-header">

                                        <h4>
                                            NACPDEAN ELECTRONIC
                                        </h4>

                                        <h4>
                                            MEMBERSHIP IDENTIFICATION
                                        </h4>

                                        <h4>
                                            CARD (QR ENABLED)
                                        </h4>

                                    </div>


                                    {{-- QR CODE --}}
                                    @if ($qrImage)

                                        <div class="bussy-qr-wrapper">

                                            <img
                                                src="{{ $qrImage }}"
                                                alt="Membership Card QR Code"
                                            >

                                        </div>

                                    @endif


                                    {{-- SCAN --}}
                                    <div class="bussy-scan-me">
                                        SCAN ME
                                    </div>


                                    {{-- CONTACT --}}
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


                                    {{-- CONTACT FOOTER --}}
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

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    CARD INFORMATION
                ================================================== --}}
                <div class="row justify-content-center mt-4">

                    <div class="col-lg-8">

                        <div class="card border-0 shadow-sm">

                            <div class="card-body">

                                <div class="row text-center">

                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Card Type
                                        </small>

                                        <strong>
                                            {{ $cardTypeLabel }}
                                        </strong>

                                    </div>


                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Membership Number
                                        </small>

                                        <strong>
                                            {{ $membershipNumber }}
                                        </strong>

                                    </div>


                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Category
                                        </small>

                                        <strong>
                                            {{ $category }}
                                        </strong>

                                    </div>


                                    <div class="col-md-3">

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

            </div>

        @endforeach

    </div>


    {{-- =========================================================
        ACTION BUTTON
    ========================================================== --}}
    <div class="text-center mt-4">

        <button
            type="button"
            onclick="window.print()"
            class="btn btn-primary px-4"
        >
            <i class="bi bi-printer me-1"></i>
            Print / Save All Cards
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
   CARD GROUP
================================================================ */

.card-group-wrapper {

    margin-bottom: 50px;

}


/* ================================================================
   CARD TYPE LABEL
================================================================ */

.card-type-label h5 {

    font-weight: 700;

}


/* ================================================================
   COMMON CLIENT CARD
================================================================ */

.client-card,
.bussy-id-card,
.bussy-id-card2,
.bussy-id-card3,
.bussy-id-card4,
.category-card,
.ntf-card,
.stf-card {

    position: relative;

    width: 390px;

    height: 600px;

    flex: 0 0 390px;

    overflow: hidden;

    border-radius: 6px;

}


/* ================================================================
   MEMBERSHIP CARD 2 (Exporter / RCG classic layout)
================================================================ */

.bussy-id-card2 {

    background:
        radial-gradient(
            circle at 30% 20%,
            rgba(255,255,255,.95),
            rgba(232,232,232,.90)
        );

    box-shadow:
        0 4px 15px rgba(0,0,0,.25);

    border: 1px solid #ddd;

}


/* ================================================================
   EXECUTIVE CARD
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
   BACK CARD
================================================================ */

.bussy-id-card4 {

    background: rgb(255,255,255);

    border: 1px solid #000;

    box-shadow:
        0 8px 25px rgba(0,0,0,.20);

}


/* ================================================================
   GREEN STRIP - LEFT
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
   GREEN STRIP - RIGHT
================================================================ */

.bussy-green-strip2 {

    position: absolute;

    right: 0;

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
   VERTICAL ID - LEFT
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
   VERTICAL ID - RIGHT
================================================================ */

.bussy-vertical-id2 {

    position: absolute;

    right: 0;

    top: 46%;

    padding-right: 12px;

    width: 100%;

    color: black;

    font-size: 6.1mm;

    font-weight: 800;

    letter-spacing: .25mm;

    text-align: center;

    writing-mode: vertical-rl;

    transform: rotate(180deg);

    white-space: nowrap;

}


/* ================================================================
   EXECUTIVE CONTENT
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
   MEMBERSHIP CONTENT
================================================================ */

.bussy-card-content2 {

    position: absolute;

    left: 4mm;

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
   COAT OF ARMS - EXECUTIVE
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
   COAT OF ARMS - MEMBERSHIP
================================================================ */

.bussy-coat-of-arms2 {

    position: absolute;

    left: -12.5mm;

    top: 0;

    height: 78px;

    width: auto;

    object-fit: contain;

}


/* ================================================================
   NACPDEAN + RCG LOGOS
================================================================ */

.bussy-nacpdean-logo {

    margin-top: -2%;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

}


/* ================================================================
   NACPDEAN LOGO
================================================================ */

.bussy-logo {

    max-width: 150px;

    max-height: 85px;

    width: auto;

    height: auto;

    object-fit: contain;

}


/* ================================================================
   RCG LOGO BESIDE NACPDEAN LOGO
================================================================ */

.bussy-rcg-logo-inline {

    width: 45px;

    height: 60px;

    object-fit: contain;

    flex: 0 0 auto;

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
   CATEGORY / EXECUTIVE ROLE (classic layout)
================================================================ */

.bussy-role {

    width: 80%;

    margin-top: 10%;

    margin-left: 20%;

    padding: 10px 0;

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
   AFFILIATION (classic layout)
================================================================ */

.bussy-membership {

    margin-top: 12px;

    padding-right: 20px;

    color: #253e9a;

    font-size: 12px;

    font-weight: 700;

    line-height: 1;

    text-align: right;

}


/* ================================================================================================
   CATEGORY CARD (Dealer / Producer / Supplier)
================================================================================================ */

.category-theme-dea {
    --accent-main: #6f7c1e;
    --accent-corner: #2eae2e;
    --bg-tint: #f6f8f2;
}

.category-theme-prd {
    --accent-main: #159c3a;
    --accent-corner: #8dc63f;
    --bg-tint: #f1f4d9;
}

.category-theme-slr {
    --accent-main: #1c1512;
    --accent-corner: #2b211d;
    --bg-tint: #eaf6fb;
}

.category-card {

    background: var(--bg-tint, #fff);

    box-shadow: 0 4px 15px rgba(0,0,0,.25);

    border: 1px solid #ddd;

}


.category-accent-strip {

    position: absolute;

    right: 0;

    top: 0;

    bottom: 0;

    width: 20mm;

    background: var(--accent-main);

    z-index: 1;

}


.category-accent-corner {

    position: absolute;

    right: 0;

    top: 0;

    width: 20mm;

    height: 20mm;

    background: var(--accent-corner);

    clip-path: polygon(100% 0, 0 0, 100% 100%);

}


.category-vertical-id {

    position: absolute;

    right: 0;

    top: 42%;

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


.category-card-content {

    position: absolute;

    left: 0;

    top: 0;

    width: calc(100% - 20mm);

    height: 100%;

    padding: 4mm 4mm 1.5mm 6mm;

    z-index: 2;

}


.category-header {

    position: relative;

    height: 13mm;

    display: flex;

    align-items: flex-start;

    justify-content: center;

    gap: 8px;

}


.category-coat-of-arms {

    position: absolute;

    left: 0;

    top: 0;

    height: 68px;

    width: auto;

    object-fit: contain;

}


.category-logo {

    max-width: 150px;

    max-height: 78px;

    width: auto;

    height: auto;

    object-fit: contain;

    margin-left: 60px;

}


.category-organization {

    margin-top: 10%;

    text-align: center;

    color: #111;

}


.category-organization h4 {

    margin: 0;

    font-size: 10.5px;

    font-weight: 700;

    line-height: 1.4;

}


.category-organization p {

    margin-top: 4px;

    margin-bottom: 0;

    font-size: 13px;

    font-weight: 600;

    line-height: 1.4;

}


.category-label {

    margin-top: 6mm;

    color: #ed1c24;

    font-size: 32px;

    font-weight: 900;

    text-transform: uppercase;

    line-height: 1;

}


.category-photo-box {

    width: 78%;

    height: 220px;

    margin: 6mm auto 0;

    background: #fff;

    overflow: hidden;

    border: 1px solid #ddd;

}


.category-photo-box img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

}


.category-name {

    margin-top: 3mm;

    text-align: center;

    color: #1a1a1a;

}


.category-name h3 {

    margin: 0;

    font-size: 21px;

    font-weight: 800;

    line-height: 1.15;

}


.category-name h4 {

    margin: 4px 0 0;

    font-size: 16px;

    font-weight: 900;

    color: #ed1c24;

    line-height: 1.1;

}


.category-affiliate {

    position: absolute;

    left: 6mm;

    bottom: 10mm;

    color: #253e9a;

    font-size: 12px;

    font-weight: 700;

}


/* ================================================================================================
   NATIONAL TASK FORCE
================================================================================================ */

.ntf-card {

    background: #ffffff;

    border: 1px solid #ddd;

    box-shadow: 0 4px 15px rgba(0,0,0,.25);

}


.ntf-top-wave {

    position: absolute;

    top: -70px;

    left: -70px;

    width: 220px;

    height: 220px;

    background: #0c3d12;

    border-radius: 50%;

    z-index: 0;

}


.ntf-bottom-wave {

    position: absolute;

    bottom: -60px;

    right: -80px;

    width: 260px;

    height: 200px;

    background: #0c3d12;

    border-radius: 50%;

    z-index: 0;

}


.ntf-header {

    position: relative;

    z-index: 1;

    text-align: center;

    padding-top: 16px;

}


.ntf-logo {

    max-width: 160px;

    max-height: 60px;

    object-fit: contain;

}


.ntf-org {

    margin: 6px 20px 0;

    font-size: 10px;

    font-weight: 700;

    line-height: 1.4;

    color: #111;

}


.ntf-cac {

    margin: 2px 0 0;

    font-size: 12px;

    font-weight: 600;

    color: #111;

}


.ntf-bar {

    position: relative;

    z-index: 1;

    background: #111;

    color: #fff;

    text-align: center;

    font-weight: 900;

    font-size: 19px;

    padding: 9px 0;

    margin-top: 10px;

    letter-spacing: .5px;

}


.ntf-body {

    position: relative;

    z-index: 1;

    display: flex;

    align-items: flex-start;

    justify-content: center;

    gap: 16px;

    padding: 16px 20px 0;

}


.ntf-photo-box {

    width: 150px;

    height: 180px;

    border: 3px solid #0c3d12;

    overflow: hidden;

    background: #fff;

    flex: 0 0 auto;

}


.ntf-photo-box img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

}


.ntf-side {

    text-align: center;

    padding-top: 10px;

}


.ntf-coat {

    height: 64px;

    width: auto;

    object-fit: contain;

}


.ntf-idno-label {

    margin-top: 10px;

    color: #111;

    font-weight: 800;

    font-size: 13px;

}


.ntf-idno-value {

    color: #ed1c24;

    font-weight: 900;

    font-size: 20px;

}


.ntf-name {

    position: relative;

    z-index: 1;

    text-align: center;

    margin-top: 16px;

}


.ntf-name h3 {

    margin: 0;

    font-size: 21px;

    font-weight: 800;

    color: #111;

}


.ntf-name h4 {

    margin: 4px 0 0;

    font-size: 15px;

    font-weight: 800;

    color: #00a651;

}


.ntf-footer {

    position: absolute;

    bottom: 0;

    left: 0;

    width: 100%;

    z-index: 2;

    background: #0c3d12;

    color: #ffe500;

    text-align: center;

    font-weight: 800;

    font-size: 12px;

    padding: 16px 0;

    letter-spacing: .5px;

}


/* ================================================================================================
   STATE TASK FORCE
================================================================================================ */

.stf-card {

    background: #06400f;

    border: 1px solid #052f0b;

    box-shadow: 0 4px 15px rgba(0,0,0,.25);

    color: #fff;

}


.stf-strip {

    position: absolute;

    right: 0;

    top: 0;

    bottom: 0;

    width: 20mm;

    background: #159c3a;

    border-top-left-radius: 10px;

    border-bottom-left-radius: 10px;

    z-index: 1;

}


.stf-strip-id {

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #fff;

    font-weight: 800;

    font-size: 5.4mm;

    letter-spacing: .25mm;

    text-align: center;

    writing-mode: vertical-rl;

    transform: rotate(180deg);

    white-space: nowrap;

}


.stf-content {

    position: relative;

    height: 100%;

    padding: 16px 20mm 40px 16px;

}


.stf-header {

    position: relative;

    text-align: center;

    padding-top: 4px;

}


.stf-coat {

    position: absolute;

    left: 0;

    top: 0;

    height: 60px;

    width: auto;

    object-fit: contain;

}


.stf-logo {

    max-width: 150px;

    max-height: 60px;

    object-fit: contain;

    margin-left: 55px;

}


.stf-orgname {

    margin: 6px 0 0;

    font-size: 9.5px;

    font-weight: 700;

    line-height: 1.4;

    color: #fff;

}


.stf-cac {

    margin: 2px 0 0;

    font-size: 11px;

    font-weight: 600;

    color: #fff;

}


.stf-pill {

    margin-top: 14px;

    background: #cf0a0a;

    color: #fff;

    text-align: center;

    font-weight: 900;

    font-size: 15px;

    border-radius: 50px;

    padding: 9px 0;

}


.stf-state {

    margin-top: 8px;

    color: #ffe500;

    text-align: center;

    font-weight: 900;

    font-size: 19px;

}


.stf-photo-box {

    width: 78%;

    height: 200px;

    margin: 10px auto 0;

    border: 2px solid #fff;

    overflow: hidden;

    background: #fff;

}


.stf-photo-box img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

}


.stf-name {

    margin-top: 8px;

    text-align: center;

}


.stf-name h3 {

    margin: 0;

    font-size: 18px;

    font-weight: 800;

    color: #fff;

}


.stf-name h4 {

    margin: 4px 0 0;

    font-size: 14px;

    font-weight: 800;

    color: #8dc63f;

}


.stf-footer {

    position: absolute;

    bottom: 0;

    left: 0;

    width: 100%;

    background: #ffe500;

    color: #111;

    text-align: center;

    font-weight: 900;

    font-size: 12px;

    padding: 10px 0;

    z-index: 2;

}


/* ================================================================
   BACK CARD
================================================================ */

.bussy-back-content {

    width: 100%;

    height: 100%;

    padding: 36px 26px;

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

    margin-bottom: 6px;

}


.bussy-back-header h4 {

    margin: 0;

    color: #111;

    font-size: 18px;

    font-weight: 900;

    line-height: 1.35;

}


/* ================================================================
   QR CODE
================================================================ */

.bussy-qr-wrapper {

    margin-top: 26px;

    padding: 14px;

    background: white;

    border: 10px solid #000;

    border-radius: 4px;

    display: flex;

    justify-content: center;

    align-items: center;

}


.bussy-qr-wrapper img {

    display: block;

    width: 190px;

    height: 190px;

    object-fit: contain;

}


/* ================================================================
   SCAN ME
================================================================ */

.bussy-scan-me {

    margin-top: 22px;

    font-size: 28px;

    font-weight: 900;

    color: #111;

    letter-spacing: .5px;

}


/* ================================================================
   CONTACT
================================================================ */

.bussy-contact {

    width: 100%;

    margin-top: 28px;

    font-size: 15px;

    line-height: 1.6;

    color: #222;

}


.bussy-contact p {

    margin-bottom: 6px;

}


.bussy-contact strong {

    font-size: 22px;

}


/* ================================================================
   FOOTER CONTACT
================================================================ */

.bussy-contact-footer {

    width: 100%;

    margin-top: 22px;

    color: #333;

    font-size: 13px;

    line-height: 1.8;

}


.bussy-contact-footer strong {

    font-weight: 600;

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


    .client-card,
    .bussy-id-card,
    .bussy-id-card2,
    .bussy-id-card3,
    .bussy-id-card4,
    .category-card,
    .ntf-card,
    .stf-card {

        width: 350px;

        height: 538px;

        flex-basis: 350px;

    }


    .bussy-photo-box,
    .category-photo-box {

        height: 195px;

    }


    .bussy-name h3,
    .category-name h3 {

        font-size: 20px;

    }


    .bussy-role h3 {

        font-size: 21px;

    }


    .bussy-organization h4,
    .category-organization h4 {

        font-size: 9.5px;

    }


    .bussy-rcg-logo-inline {

        width: 38px;

        height: 52px;

    }


    .category-label {

        font-size: 26px;

    }


    .ntf-photo-box {

        width: 120px;

        height: 150px;

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

        padding: 0;

        margin: 0;

        overflow: visible;

    }


    .card-group-wrapper {

        margin: 0 0 30px 0;

        page-break-inside: avoid;

        break-inside: avoid;

    }


    .card-type-label {

        display: none;

    }


    .id-card-preview {

        display: flex;

        justify-content: center;

        gap: 20px;

        padding: 0;

        margin: 0;

        overflow: visible;

    }


    .client-card,
    .bussy-id-card,
    .bussy-id-card2,
    .bussy-id-card3,
    .bussy-id-card4,
    .category-card,
    .ntf-card,
    .stf-card {

        box-shadow: none !important;

        flex: 0 0 390px;

    }

}

</style>

@endsection
