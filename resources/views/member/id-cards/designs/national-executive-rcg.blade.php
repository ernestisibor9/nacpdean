{{-- =========================================================
    NACPDEAN NATIONAL EXECUTIVE RCG MEMBERSHIP CARD
========================================================= --}}

@php
    $memberTypeLabel = match (strtolower(trim($memberType ?? ''))) {
        'regular'   => 'MEMBER',
        'member'    => 'MEMBER',
        'affiliate' => 'AFFILIATE',
        'executive' => 'EXECUTIVE',
        default     => strtoupper(trim($memberType ?? 'MEMBER')),
    };
@endphp


<div class="client-card national-executive-card">

    {{-- CLIENT ARTWORK --}}
    <img
        class="national-executive-card-background"
        src="{{ asset('images/id-cards/national-executive-rcg.jpg') }}"
        alt=""
    >

    {{-- MEMBERSHIP NUMBER (vertical LEFT green strip) --}}
    <div class="ne-membership-number">
        {{ $membershipNumber }}
    </div>

    {{-- MEMBER PHOTO --}}
    <div class="ne-photo">
        <img src="{{ $photoUrl }}" alt="{{ $memberName }}">
    </div>

    {{-- MEMBER NAME --}}
    <div class="ne-member-name">
        {{ $memberName }}
    </div>

    {{-- POSITION --}}
    @if (!empty($position))
        <div class="ne-position">
            {{ strtoupper($position) }}
        </div>
    @endif

    {{-- ISSUE DATE --}}
    @if (!empty($issueDate))
        <div class="ne-issue-date">
            <span class="ne-date-label">Issue Date:</span>
            <span class="ne-date-value">{{ $issueDate }}</span>
        </div>
    @endif

    {{-- EXPIRATION DATE --}}
    @if (!empty($expirationDate))
        <div class="ne-expiration-date">
            <span class="ne-date-label">Expires:</span>
            <span class="ne-date-value">{{ $expirationDate }}</span>
        </div>
    @endif

    {{-- MEMBERSHIP CATEGORY (text only — sits on the RED BANNER already baked into the artwork, right above "MEMBER: FACAN") --}}
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
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   MEMBERSHIP NUMBER (vertical LEFT green strip)
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
   ------------------------------------------------------------
   Force text to WRAP onto multiple lines instead of being
   truncated with "…". This overrides any parent nowrap rule.
============================================================ */
.ne-position {
    position: absolute;
    top: 452px;
    left: 100px;
    width: 230px;
    height: auto;
    margin: 0;
    padding: 0 4px;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.15;
    color: #ed1c24;
    text-transform: uppercase;

    /* Force wrapping */
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    overflow: visible !important;
    text-overflow: clip !important;

    z-index: 5;
}


/* ============================================================
   ISSUE DATE + EXPIRATION DATE
   ------------------------------------------------------------
   Moved up so they no longer sit on top of the baked-in red
   category banner (which turned out to be lower on the
   artwork than originally assumed — see CATEGORY BANNER
   below). Placed as a compact block right under Position.
============================================================ */
.ne-issue-date,
.ne-expiration-date {
    position: absolute;
    left: 100px;
    width: 230px;
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

.ne-issue-date {
    top: 500px;
    text-align: center;
}

.ne-expiration-date {
    top: 520px;
    text-align: center;
}

.ne-date-label {
    color: #555555;
    font-weight: 700;
    margin-right: 4px;
}

.ne-date-value {
    color: #111111;
    font-weight: 900;
}



/* ============================================================
   CATEGORY BANNER
   ------------------------------------------------------------
   IMPORTANT — CORRECTED POSITION:
   Turns out the artwork's actual baked-in red banner sits
   much lower than originally assumed (was placed at 500px,
   which had nothing baked behind it — that's why "EXPORTER"
   rendered as invisible white-on-white). The real red
   rectangle is the one that was colliding with the
   Expiration Date, right above the "MEMBER: FACAN" footer
   text (~600px+). Moved here, still transparent background
   (artwork supplies the red) — do NOT re-add a CSS
   background-color.
   The old .ne-white-overlay div/CSS has been removed
   entirely: it existed to hide this same red rectangle
   under a mistaken assumption that it was unwanted. It's
   not unwanted — it's the category banner's real home — so
   there's nothing left to hide.
============================================================ */
.ne-category-banner {
    position: absolute;
    top: 565px;
    left: 130px;
    width: 230px;
    height: 38px;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
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

    .ne-member-name {
        position: absolute !important;
        top: 428px !important;
        left: 100px !important;
        width: 230px !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

.ne-position {
    position: absolute !important;
    top: 452px !important;
    left: 100px !important;
    width: 230px !important;
    font-size: 12px !important;
    line-height: 1.15 !important;

    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    overflow: visible !important;
    text-overflow: clip !important;

    visibility: visible !important;
    z-index: 5 !important;
}

    .ne-issue-date,
    .ne-expiration-date {
        position: absolute !important;
        left: 100px !important;
        width: 230px !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        visibility: visible !important;
        z-index: 5 !important;
    }

    .ne-issue-date {
        top: 485px !important;
    }

    .ne-expiration-date {
        top: 503px !important;
    }

    .ne-category-banner {
        position: absolute !important;
        top: 565px !important;
        left: 100px !important;
        width: 230px !important;
        height: 38px !important;
        display: flex !important;
        visibility: visible !important;
        background: transparent !important;
        color: #ffffff !important;
        z-index: 5 !important;
    }

}

</style>
