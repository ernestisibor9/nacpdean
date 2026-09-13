{{-- =========================================================
    NACPDEAN NATIONAL EXECUTIVE MEMBERSHIP CARD
========================================================= --}}

@php

    /*
    |--------------------------------------------------------------------------
    | MEMBER TYPE LABEL
    |--------------------------------------------------------------------------
    */

    $memberTypeLabel = match (strtolower(trim($memberType ?? ''))) {

        'regular'   => 'MEMBER',

        'member'    => 'MEMBER',

        'affiliate' => 'AFFILIATE',

        'executive' => 'EXECUTIVE',

        default => strtoupper(
            trim($memberType ?? 'MEMBER')
        ),

    };

@endphp


{{-- =========================================================
    NATIONAL EXECUTIVE CARD
========================================================= --}}

<div class="client-card national-executive-card">


    {{-- =====================================================
        CLIENT ARTWORK
        -----------------------------------------------------
        IMPORTANT:
        The artwork is an actual IMG element rather than
        a CSS background-image.

        This makes the card much more reliable when:
        - Printing
        - Saving as PDF
        - Using browser print preview
    ====================================================== --}}

    <img
        class="national-executive-card-background"
        src="{{ asset('images/id-cards/national-executive.jpg') }}"
        alt=""
    >


    {{-- =====================================================
        WHITE OVERLAY
        -----------------------------------------------------
        Hides the empty red rectangle baked into the
        artwork just above the MEMBER: FACAN footer line.
    ====================================================== --}}

    <div class="ne-white-overlay"></div>


    {{-- =====================================================
        MEMBERSHIP NUMBER
        -----------------------------------------------------
        Vertical LEFT green strip
    ====================================================== --}}

    <div class="ne-membership-number">

        {{ $membershipNumber }}

    </div>


    {{-- =====================================================
        MEMBER PHOTO
    ====================================================== --}}

    <div class="ne-photo">

        <img
            src="{{ $photoUrl }}"
            alt="{{ $memberName }}"
        >

    </div>


    {{-- =====================================================
        MEMBER NAME
    ====================================================== --}}

    <div class="ne-member-name">

        {{ $memberName }}

    </div>


    {{-- =====================================================
        POSITION
    ====================================================== --}}

    @if (!empty($position))

        <div class="ne-position">

            {{ strtoupper($position) }}

        </div>

    @endif


    {{-- =====================================================
        MEMBERSHIP CATEGORY
        -----------------------------------------------------
        Red banner with white text
    ====================================================== --}}

    <div class="ne-category-banner">

        {{ $category }}

    </div>


</div>


<style>

/* ============================================================
   NATIONAL EXECUTIVE CARD
============================================================ */

.national-executive-card {

    position: relative;

    width: 390px;

    height: 644px;

    min-width: 390px;

    max-width: 390px;

    min-height: 644px;

    max-height: 644px;

    flex: 0 0 390px;

    overflow: hidden;

    margin: 0;

    padding: 0;

    border: none;

    border-radius: 0;

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

.national-executive-card-background {

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
   WHITE OVERLAY
   ------------------------------------------------------------
   Hides the empty red rectangle baked into the artwork.
============================================================ */

.ne-white-overlay {

    position: absolute;

    top: 590px;

    left: 100px;

    width: 230px;

    height: 42px;

    margin: 0;

    padding: 0;

    background: #ffffff;

    z-index: 2;

}


/* ============================================================
   MEMBERSHIP NUMBER
   ------------------------------------------------------------
   Vertical LEFT green strip
============================================================ */

.ne-membership-number {

    position: absolute;

    top: 205px;

    left: 18px;

    width: 38px;

    height: 270px;

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
   MEMBER PHOTO
============================================================ */

.ne-photo {

    position: absolute;

    top: 195px;

    left: 100px;

    width: 230px;

    height: 220px;

    overflow: hidden;

    margin: 0;

    padding: 0;

    background: transparent;

    z-index: 5;

}


.ne-photo img {

    display: block;

    width: 230px;

    height: 220px;

    min-width: 230px;

    max-width: 230px;

    min-height: 220px;

    max-height: 220px;

    margin: 0;

    padding: 0;

    border: 0;

    object-fit: cover;

    object-position: center top;

}


/* ============================================================
   MEMBER NAME
============================================================ */

.ne-member-name {

    position: absolute;

    top: 428px;

    left: 100px;

    width: 230px;

    height: auto;

    margin: 0;

    padding: 0;

    text-align: center;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 17px;

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
   POSITION
============================================================ */

.ne-position {

    position: absolute;

    top: 452px;

    left: 100px;

    width: 230px;

    height: auto;

    margin: 0;

    padding: 0;

    text-align: center;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 13px;

    font-weight: 800;

    line-height: 1.1;

    color: #ed1c24;

    text-transform: uppercase;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    z-index: 5;

}


/* ============================================================
   CATEGORY BANNER
   ------------------------------------------------------------
   Red banner with white text
============================================================ */

.ne-category-banner {

    position: absolute;

    top: 500px;

    left: 100px;

    width: 230px;

    height: 38px;

    margin: 0;

    padding: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #ed1c24;

    color: #ffffff;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 22px;

    font-weight: 900;

    line-height: 1;

    letter-spacing: 1px;

    text-transform: uppercase;

    text-align: center;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    z-index: 5;

    -webkit-print-color-adjust: exact !important;

    print-color-adjust: exact !important;

}


/* ============================================================
   MOBILE SCREEN
============================================================ */

@media (max-width: 576px) {

    .national-executive-card {

        width: 390px;

        height: 644px;

        min-width: 390px;

        max-width: 390px;

        min-height: 644px;

        max-height: 644px;

        flex: 0 0 390px;

    }


    .national-executive-card-background {

        width: 390px;

        height: 644px;

    }

}


/* ============================================================
   PRINT
============================================================ */

@media print {

    /*
    |--------------------------------------------------------------------------
    | CARD
    |--------------------------------------------------------------------------
    */

    .national-executive-card {

        position: relative !important;

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


    /*
    |--------------------------------------------------------------------------
    | CLIENT ARTWORK
    |--------------------------------------------------------------------------
    */

    .national-executive-card-background {

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


    /*
    |--------------------------------------------------------------------------
    | WHITE OVERLAY
    |--------------------------------------------------------------------------
    */

    .ne-white-overlay {

        position: absolute !important;

        top: 590px !important;

        left: 100px !important;

        width: 230px !important;

        height: 42px !important;

        display: block !important;

        visibility: visible !important;

        background: #ffffff !important;

        z-index: 2 !important;

        -webkit-print-color-adjust: exact !important;

        print-color-adjust: exact !important;

    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER
    |--------------------------------------------------------------------------
    */

    .ne-membership-number {

        position: absolute !important;

        top: 205px !important;

        left: 18px !important;

        width: 38px !important;

        height: 270px !important;

        display: flex !important;

        visibility: visible !important;

        z-index: 5 !important;

    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER PHOTO
    |--------------------------------------------------------------------------
    */

    .ne-photo {

        position: absolute !important;

        top: 195px !important;

        left: 100px !important;

        width: 230px !important;

        height: 220px !important;

        visibility: visible !important;

        z-index: 5 !important;

    }


    .ne-photo img {

        display: block !important;

        width: 230px !important;

        height: 220px !important;

        min-width: 230px !important;

        max-width: 230px !important;

        min-height: 220px !important;

        max-height: 220px !important;

        visibility: visible !important;

        object-fit: cover !important;

        object-position: center top !important;

    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER NAME
    |--------------------------------------------------------------------------
    */

    .ne-member-name {

        position: absolute !important;

        top: 428px !important;

        left: 100px !important;

        width: 230px !important;

        visibility: visible !important;

        z-index: 5 !important;

    }


    /*
    |--------------------------------------------------------------------------
    | POSITION
    |--------------------------------------------------------------------------
    */

    .ne-position {

        position: absolute !important;

        top: 452px !important;

        left: 100px !important;

        width: 230px !important;

        visibility: visible !important;

        z-index: 5 !important;

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY BANNER
    |--------------------------------------------------------------------------
    */

    .ne-category-banner {

        position: absolute !important;

        top: 500px !important;

        left: 100px !important;

        width: 230px !important;

        height: 38px !important;

        display: flex !important;

        visibility: visible !important;

        background: #ed1c24 !important;

        color: #ffffff !important;

        z-index: 5 !important;

        -webkit-print-color-adjust: exact !important;

        print-color-adjust: exact !important;

    }

}

</style>
