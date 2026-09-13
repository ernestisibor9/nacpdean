{{-- =========================================================
    NACPDEAN EXPORTER MEMBERSHIP CARD
========================================================= --}}

@php

    /*
    |--------------------------------------------------------------------------
    | MEMBER TYPE LABEL
    |--------------------------------------------------------------------------
    */

    $memberTypeLabel = match (strtolower(trim($memberType ?? ''))) {

        'regular' => 'MEMBER',

        'member' => 'MEMBER',

        'affiliate' => 'AFFILIATE',

        default => strtoupper(
            trim($memberType ?? 'MEMBER')
        ),

    };

@endphp


{{-- =========================================================
    EXPORTER CARD
========================================================= --}}

<div class="client-card exporter-card">

    {{-- =====================================================
        CLIENT ARTWORK
        -----------------------------------------------------
        IMPORTANT:
        The client artwork is a real IMG element instead
        of a CSS background-image.

        This is important for printing and PDF output.
    ====================================================== --}}

    <img
        class="exporter-card-background"
        src="{{ asset('images/id-cards/exporter.jpg') }}"
        alt=""
    >


    {{-- =====================================================
        MEMBER PHOTO
    ====================================================== --}}

    <div class="exporter-photo">

        <img
            src="{{ $photoUrl }}"
            alt="{{ $memberName }}"
        >

    </div>


    {{-- =====================================================
        MEMBER NAME
    ====================================================== --}}

    <div class="exporter-member-name">

        {{ $memberName }}

    </div>


    {{-- =====================================================
        MEMBER TYPE
    ====================================================== --}}

    <div class="exporter-member-type">

        {{ $memberTypeLabel }}

    </div>


    {{-- =====================================================
        MEMBERSHIP CATEGORY
    ====================================================== --}}

    <div class="exporter-category">

        {{ $category }}

    </div>


    {{-- =====================================================
        MEMBERSHIP NUMBER
    ====================================================== --}}

    <div class="exporter-membership-number">

        {{ $membershipNumber }}

    </div>

</div>


<style>

/* ============================================================
   EXPORTER CARD
============================================================ */

.exporter-card {

    position: relative;

    width: 390px;

    height: 644px;

    min-width: 390px;

    max-width: 390px;

    min-height: 644px;

    max-height: 644px;

    flex: 0 0 390px;

    overflow: hidden;

    border: none;

    border-radius: 0;

    margin: 0;

    padding: 0;

    background: transparent;

    box-sizing: border-box;

    /*
    |--------------------------------------------------------------------------
    | PRINT COLORS
    |--------------------------------------------------------------------------
    */

    -webkit-print-color-adjust: exact !important;

    print-color-adjust: exact !important;

}


/* ============================================================
   CLIENT ARTWORK
============================================================ */

.exporter-card-background {

    position: absolute;

    top: 0;

    left: 0;

    width: 390px;

    height: 644px;

    min-width: 390px;

    max-width: 390px;

    min-height: 644px;

    max-height: 644px;

    display: block;

    margin: 0;

    padding: 0;

    border: 0;

    object-fit: fill;

    z-index: 1;

    pointer-events: none;

    /*
    |--------------------------------------------------------------------------
    | PRINT COLORS
    |--------------------------------------------------------------------------
    */

    -webkit-print-color-adjust: exact !important;

    print-color-adjust: exact !important;

}


/* ============================================================
   MEMBER PHOTO
============================================================ */

.exporter-photo {

    position: absolute;

    top: 155px;

    left: 45px;

    width: 270px;

    height: 230px;

    overflow: hidden;

    margin: 0;

    padding: 0;

    background: transparent;

    z-index: 5;

}


.exporter-photo img {

    display: block;

    width: 270px;

    height: 230px;

    min-width: 270px;

    max-width: 270px;

    min-height: 230px;

    max-height: 230px;

    margin: 0;

    padding: 0;

    border: 0;

    object-fit: cover;

    object-position: center top;

}


/* ============================================================
   MEMBER NAME
============================================================ */

.exporter-member-name {

    position: absolute;

    top: 395px;

    left: 25px;

    width: 340px;

    height: auto;

    margin: 0;

    padding: 0;

    text-align: center;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 22px;

    font-weight: 900;

    line-height: 1.1;

    color: #111111;

    text-transform: uppercase;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    z-index: 5;

}


/* ============================================================
   MEMBER TYPE
============================================================ */

.exporter-member-type {

    position: absolute;

    top: 432px;

    left: 25px;

    width: 340px;

    height: auto;

    margin: 0;

    padding: 0;

    text-align: center;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 17px;

    font-weight: 900;

    line-height: 1.1;

    color: #ed1c24;

    text-transform: uppercase;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    z-index: 5;

}


/* ============================================================
   MEMBERSHIP CATEGORY
============================================================ */

.exporter-category {

    position: absolute;

    top: 462px;

    left: 25px;

    width: 340px;

    height: auto;

    margin: 0;

    padding: 0;

    text-align: center;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 30px;

    font-weight: 900;

    line-height: 1;

    color: #ed1c24;

    text-transform: uppercase;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    z-index: 5;

}


/* ============================================================
   MEMBERSHIP NUMBER
============================================================ */

.exporter-membership-number {

    position: absolute;

    top: 180px;

    right: 18px;

    width: 38px;

    height: 260px;

    margin: 0;

    padding: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    writing-mode: vertical-rl;

    transform: rotate(180deg);

    font-family: Arial, Helvetica, sans-serif;

    font-size: 20px;

    font-weight: 900;

    line-height: 1;

    letter-spacing: 1px;

    color: #ffffff;

    text-transform: uppercase;

    white-space: nowrap;

    z-index: 5;

}


/* ============================================================
   MOBILE SCREEN
============================================================ */

@media (max-width: 576px) {

    .exporter-card {

        width: 390px;

        height: 644px;

        min-width: 390px;

        max-width: 390px;

        min-height: 644px;

        max-height: 644px;

        flex: 0 0 390px;

    }


    .exporter-card-background {

        width: 390px;

        height: 644px;

    }

}


/* ============================================================
   PRINT
============================================================ */

@media print {

    .exporter-card {

        width: 390px !important;

        height: 644px !important;

        min-width: 390px !important;

        max-width: 390px !important;

        min-height: 644px !important;

        max-height: 644px !important;

        flex: 0 0 390px !important;

        overflow: hidden !important;

        margin: 0 !important;

        padding: 0 !important;

        border: 0 !important;

        border-radius: 0 !important;

        box-shadow: none !important;

        background: transparent !important;

        -webkit-print-color-adjust: exact !important;

        print-color-adjust: exact !important;

    }


    .exporter-card-background {

        position: absolute !important;

        top: 0 !important;

        left: 0 !important;

        width: 390px !important;

        height: 644px !important;

        min-width: 390px !important;

        max-width: 390px !important;

        min-height: 644px !important;

        max-height: 644px !important;

        display: block !important;

        visibility: visible !important;

        object-fit: fill !important;

        z-index: 1 !important;

        -webkit-print-color-adjust: exact !important;

        print-color-adjust: exact !important;

    }


    .exporter-photo {

        position: absolute !important;

        top: 155px !important;

        left: 45px !important;

        width: 270px !important;

        height: 230px !important;

        z-index: 5 !important;

    }


    .exporter-photo img {

        width: 270px !important;

        height: 230px !important;

        object-fit: cover !important;

    }


    .exporter-member-name {

        position: absolute !important;

        top: 395px !important;

        left: 25px !important;

        width: 340px !important;

        font-size: 22px !important;

        line-height: 1.1 !important;

        z-index: 5 !important;

    }


    .exporter-member-type {

        position: absolute !important;

        top: 432px !important;

        left: 25px !important;

        width: 340px !important;

        font-size: 17px !important;

        line-height: 1.1 !important;

        z-index: 5 !important;

    }


    .exporter-category {

        position: absolute !important;

        top: 462px !important;

        left: 25px !important;

        width: 340px !important;

        font-size: 30px !important;

        line-height: 1 !important;

        z-index: 5 !important;

    }


    .exporter-membership-number {

        position: absolute !important;

        top: 180px !important;

        right: 18px !important;

        width: 38px !important;

        height: 260px !important;

        font-size: 20px !important;

        z-index: 5 !important;

    }

}

</style>
