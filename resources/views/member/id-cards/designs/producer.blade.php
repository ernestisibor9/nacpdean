{{-- =========================================================
    NACPDEAN PRODUCER MEMBERSHIP CARD
========================================================= --}}

@php
    $memberTypeLabel = match (strtolower(trim($memberType ?? ''))) {
        'regular'   => 'MEMBER',
        'member'    => 'MEMBER',
        'affiliate' => 'AFFILIATE',
        default     => strtoupper(trim($memberType ?? 'MEMBER')),
    };
@endphp


<div class="client-card producer-card">

    {{-- CLIENT ARTWORK --}}
    <img
        class="producer-card-background"
        src="{{ asset('images/id-cards/producer.jpg') }}"
        alt=""
    >
    {{-- MEMBER PHOTO --}}
    <div class="producer-photo">
        <img src="{{ $photoUrl }}" alt="{{ $memberName }}">
    </div>

    {{-- MEMBER NAME --}}
    <div class="producer-member-name">
        {{ $memberName }}
    </div>

    {{-- MEMBER TYPE --}}
    <div class="producer-member-type">
        {{ $memberTypeLabel }}
    </div>

    {{-- MEMBERSHIP CATEGORY --}}
    <div class="producer-category">
        {{ $category }}
    </div>

    {{-- MEMBERSHIP NUMBER --}}
    <div class="producer-membership-number">
        {{ $membershipNumber }}
    </div>

    {{-- ISSUE DATE --}}
    @if (!empty($issueDate))
        <div class="producer-issue-date">
            <span class="producer-date-label">Issue Date:</span>
            <span class="producer-date-value">{{ $issueDate }}</span>
        </div>
    @endif

    {{-- EXPIRATION DATE --}}
    @if (!empty($expirationDate))
        <div class="producer-expiration-date">
            <span class="producer-date-label">Expires:</span>
            <span class="producer-date-value">{{ $expirationDate }}</span>
        </div>
    @endif

</div>


<style>

/* ============================================================
   PRINT PAGE SIZE (prevents the card being cut off)
============================================================ */
@page {
    size: 390px 644px;
    margin: 0;
}


/* ============================================================
   PRODUCER CARD
============================================================ */
.producer-card {
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
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   CLIENT ARTWORK
============================================================ */
.producer-card-background {
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
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   MEMBER PHOTO
============================================================ */
.producer-photo {
    position: absolute;
    top: 190px;
    left: 45px;
    width: 255px;
    height: 230px;
    overflow: hidden;
    margin: 0;
    padding: 0;
    background: transparent;
    z-index: 5;
}

.producer-photo img {
    display: block;
    width: 255px;
    height: 230px;
    min-width: 255px;
    max-width: 255px;
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
.producer-member-name {
    position: absolute;
    top: 425px;
    left: 5px;
    width: 340px;
    height: auto;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 22px;
    font-weight: bolder;
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
.producer-member-type {
    position: absolute;
    top: 462px;
    left: 15px;
    width: 340px;
    height: auto;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 21px;
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
.producer-category {
    position: absolute;
    top: 512px;
    left: 25px;
    width: 340px;
    height: auto;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 35px;
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
.producer-membership-number {
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
   ISSUE DATE + EXPIRATION DATE
============================================================ */
.producer-issue-date,
.producer-expiration-date {
    position: absolute;
    left: 25px;
    width: 240px;
    margin: 0;
    padding: 0;
    text-align: left;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
    color: #111111;
    text-transform: uppercase;
    z-index: 5;
}

.producer-issue-date {
    top: 558px;
}

.producer-expiration-date {
    top: 576px;
}

.producer-date-label {
    color: #555555;
    font-weight: 700;
    margin-right: 4px;
}

.producer-date-value {
    color: #111111;
    font-weight: 900;
}


/* ============================================================
   MOBILE SCREEN
============================================================ */
@media (max-width: 576px) {
    .producer-card {
        width: 390px;
        height: 644px;
        min-width: 390px;
        max-width: 390px;
        min-height: 644px;
        max-height: 644px;
        flex: 0 0 390px;
    }
    .producer-card-background {
        width: 390px;
        height: 644px;
    }
}


/* ============================================================
   PRINT
   ------------------------------------------------------------
   Every value here MUST match the on-screen values above.
============================================================ */
@media print {

    /* ----- CARD WRAPPER ----- */
    .producer-card {
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
        page-break-after: always !important;
        break-after: page !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ----- ARTWORK ----- */
    .producer-card-background {
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
        margin: 0 !important;
        padding: 0 !important;
        z-index: 1 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ----- PHOTO ----- */
    .producer-photo {
        position: absolute !important;
        top: 175px !important;
        left: 45px !important;
        width: 255px !important;
        height: 230px !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        z-index: 5 !important;
    }

    .producer-photo img {
        display: block !important;
        width: 255px !important;
        height: 230px !important;
        min-width: 255px !important;
        max-width: 255px !important;
        min-height: 230px !important;
        max-height: 230px !important;
        visibility: visible !important;
        object-fit: cover !important;
        object-position: center top !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ----- MEMBER NAME ----- */
    .producer-member-name {
        position: absolute !important;
        top: 425px !important;
        left: 5px !important;
        width: 340px !important;
        font-size: 22px !important;
        line-height: 1.1 !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    /* ----- MEMBER TYPE ----- */
    .producer-member-type {
        position: absolute !important;
        top: 462px !important;
        left: 15px !important;
        width: 340px !important;
        font-size: 27px !important;
        line-height: 1.1 !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    /* ----- CATEGORY ----- */
    .producer-category {
        position: absolute !important;
        top: 512px !important;
        left: 25px !important;
        width: 340px !important;
        font-size: 38px !important;
        line-height: 1 !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    /* ----- MEMBERSHIP NUMBER ----- */
    .producer-membership-number {
        position: absolute !important;
        top: 180px !important;
        right: 18px !important;
        width: 38px !important;
        height: 260px !important;
        font-size: 20px !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    /* ----- ISSUE / EXPIRATION DATES ----- */
    .producer-issue-date,
    .producer-expiration-date {
        position: absolute !important;
        left: 25px !important;
        width: 240px !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    .producer-issue-date {
        top: 558px !important;
    }

    .producer-expiration-date {
        top: 576px !important;
    }

}

</style>
