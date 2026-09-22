{{-- =========================================================
    NACPDEAN EXPORTER MEMBERSHIP CARD
========================================================= --}}

@php
    $memberTypeLabel = match (strtolower(trim($memberType ?? ''))) {
        'regular'   => 'MEMBER',
        'member'    => 'MEMBER',
        'affiliate' => 'AFFILIATE',
        default     => strtoupper(trim($memberType ?? 'MEMBER')),
    };
@endphp


<div class="client-card exporter-card">

    <img class="exporter-card-background"
         src="{{ asset('images/id-cards/exporter.jpg') }}"
         alt="">

    <div class="exporter-photo">
        <img src="{{ $photoUrl }}" alt="{{ $memberName }}">
    </div>

    <div class="exporter-member-name">{{ $memberName }}</div>
    <div class="exporter-member-type">{{ $memberTypeLabel }}</div>
    <div class="exporter-category">{{ $category }}</div>
    <div class="exporter-membership-number">{{ $membershipNumber }}</div>

    @if (!empty($issueDate))
        <div class="exporter-issue-date">
            <span class="exporter-date-label">Issue Date:</span>
            <span class="exporter-date-value">{{ $issueDate }}</span>
        </div>
    @endif

    @if (!empty($expirationDate))
        <div class="exporter-expiration-date">
            <span class="exporter-date-label">Expires:</span>
            <span class="exporter-date-value">{{ $expirationDate }}</span>
        </div>
    @endif

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
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

.exporter-card-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 390px;
    height: 644px;
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

.exporter-photo {
    position: absolute;
    top: 175px;
    left: 45px;
    width: 255px;
    height: 230px;
    overflow: hidden;
    margin: 0;
    padding: 0;
    background: transparent;
    z-index: 5;
}

.exporter-photo img {
    display: block;
    width: 255px;
    height: 230px;
    margin: 0;
    padding: 0;
    border: 0;
    object-fit: cover;
    object-position: center top;
}

.exporter-member-name {
    position: absolute;
    top: 425px;
    left: 5px;
    width: 340px;
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

.exporter-member-type {
    position: absolute;
    top: 462px;
    left: 15px;
    width: 340px;
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

.exporter-category {
    position: absolute;
    top: 512px;
    left: 25px;
    width: 340px;
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

.exporter-issue-date,
.exporter-expiration-date {
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

.exporter-issue-date      { top: 558px; }
.exporter-expiration-date { top: 576px; }

.exporter-date-label { color: #555555; font-weight: 700; margin-right: 4px; }
.exporter-date-value { color: #111111; font-weight: 900; }

@media (max-width: 576px) {
    .exporter-card { width: 390px; height: 644px; flex: 0 0 390px; }
    .exporter-card-background { width: 390px; height: 644px; }
}

/* ============================================================
   PRINT
   ------------------------------------------------------------
   NOTE:
   The parent Blade (member.membership-card) applies a single
   transform: scale(0.7692) to the whole card and controls
   pagination. We DO NOT override positions here, otherwise
   absolutely positioned children (dates, category, etc.) get
   out of sync with the scaled parent and overlap.
============================================================ */
@media print {

    .exporter-card {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .exporter-card-background,
    .exporter-photo img {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

}

</style>
