{{-- =========================================================
    NACPDEAN COMMON MEMBERSHIP CARD BACK
========================================================= --}}

@php
    $qrCode = $qrCode ?? null;
@endphp

<div class="membership-card-back">

    {{-- CLIENT BACKGROUND ARTWORK --}}
    <img
        src="{{ asset('images/id-cards/card-back.jpg') }}"
        alt="NACPDEAN Membership Card Back"
        class="card-back-background"
    >

    {{-- DYNAMIC QR CODE --}}
    @if (!empty($qrCode))
        <div class="card-back-qr-code">
            <img
                src="{{ $qrCode }}"
                alt="Membership Verification QR Code"
            >
        </div>
    @endif

</div>


<style>

/* ============================================================
   CARD CONTAINER  (same 390×644 as the front card)
============================================================ */
.membership-card-back {
    position: relative;
    width: 390px;
    height: 644px;
    min-width: 390px;
    max-width: 390px;
    min-height: 644px;
    max-height: 644px;
    flex: 0 0 390px;
    overflow: hidden;         /* ← clips any extra artwork below 644px */
    margin: 0;
    padding: 0;
    border: none;
    border-radius: 0;
    background: transparent;
    box-sizing: border-box;
    page-break-inside: avoid;
    break-inside: avoid;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   BACKGROUND ARTWORK
   ------------------------------------------------------------
   object-fit: cover + object-position: top center forces the
   artwork to fill the 390×644 box while KEEPING its aspect
   ratio, so any extra height gets cropped at the bottom.
   This is what makes the back card end at the same y as the
   front card.
============================================================ */
.membership-card-back .card-back-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 390px;
    height: 644px;
    display: block;
    margin: 0;
    padding: 0;
    border: 0;
    object-fit: cover;            /* ← was fill; cover keeps aspect ratio */
    object-position: top center;  /* ← top-anchored so the footer text stays */
    z-index: 1;
    pointer-events: none;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   QR CODE CONTAINER
============================================================ */
.membership-card-back .card-back-qr-code {
    position: absolute;
    top: 190px;
    left: 97px;
    width: 205px;
    height: 205px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 0;
    z-index: 5;
    box-sizing: border-box;
    background: transparent;
}


/* ============================================================
   QR CODE IMAGE
============================================================ */
.membership-card-back .card-back-qr-code img {
    display: block;
    width: 205px;
    height: 205px;
    max-width: 205px;
    max-height: 205px;
    margin: 0;
    padding: 0;
    border: 0;
    object-fit: contain;
    box-sizing: border-box;
}


/* ============================================================
   MOBILE
============================================================ */
@media (max-width: 576px) {
    .membership-card-back {
        width: 390px;
        height: 644px;
        min-width: 390px;
        max-width: 390px;
        min-height: 644px;
        max-height: 644px;
        flex: 0 0 390px;
    }
}


/* ============================================================
   PRINT
   ------------------------------------------------------------
   No @page / html-body rules here — the PARENT Blade
   (member.membership-card) owns page setup and pagination.
   This partial only styles its own card so it looks identical
   to the screen version.
============================================================ */
@media print {

    .membership-card-back {
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
        box-sizing: border-box !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .membership-card-back .card-back-background {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 390px !important;
        height: 644px !important;
        display: block !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        object-fit: cover !important;            /* same as screen */
        object-position: top center !important;
        z-index: 1 !important;
        pointer-events: none !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .membership-card-back .card-back-qr-code {
        position: absolute !important;
        top: 190px !important;
        left: 97px !important;
        width: 205px !important;
        height: 205px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        z-index: 5 !important;
        box-sizing: border-box !important;
        background: transparent !important;
    }

    .membership-card-back .card-back-qr-code img {
        display: block !important;
        visibility: visible !important;
        width: 205px !important;
        height: 205px !important;
        max-width: 205px !important;
        max-height: 205px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        object-fit: contain !important;
        box-sizing: border-box !important;
    }

}

</style>
