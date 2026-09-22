{{-- =========================================================
    NACPDEAN NATIONAL TASK FORCE CARD
    PRINT-SAFE VERSION
========================================================= --}}

<div class="client-card task-force-national-card">

    {{-- CLIENT ARTWORK BACKGROUND --}}
    <img
        class="task-force-national-card-background"
        src="{{ asset('images/id-cards/task-force-national.jpg') }}"
        alt=""
    >

    {{-- MEMBER PHOTO --}}
    <div class="tfn-photo">
        <img
            src="{{ $photoUrl }}"
            alt="{{ $memberName }}"
        >
    </div>

    {{-- TASK FORCE ID --}}
    @if ($taskForceAppointment && !empty($taskForceAppointment->taskforce_id))
        <div class="tfn-id-number">
            {{ $taskForceAppointment->taskforce_id }}
        </div>
    @endif

    {{-- MEMBER NAME --}}
    <div class="tfn-member-name">
        {{ $memberName }}
    </div>

    {{-- TASK FORCE POSITION --}}
    @if (!empty($position))
        <div class="tfn-position">
            {{ strtoupper($position) }}
        </div>
    @endif

</div>


<style>

/* ============================================================
   PRINT PAGE SIZE — one card per page, exact card dimensions
============================================================ */
@page {
    size: 390px 644px;
    margin: 0;
}


/* ============================================================
   NATIONAL TASK FORCE CARD
============================================================ */
.task-force-national-card {
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
    page-break-after: always;
    break-after: page;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}


/* ============================================================
   CLIENT ARTWORK BACKGROUND
============================================================ */
.task-force-national-card-background {
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
.tfn-photo {
    position: absolute;
    top: 222px;
    left: 52px;
    width: 205px;
    height: 240px;
    overflow: hidden;
    margin: 0;
    padding: 0;
    background: transparent;
    z-index: 5;
}

.tfn-photo img {
    display: block;
    width: 205px;
    height: 240px;
    min-width: 205px;
    max-width: 205px;
    min-height: 240px;
    max-height: 240px;
    margin: 0;
    padding: 0;
    border: 0;
    object-fit: cover;
    object-position: center top;
}


/* ============================================================
   TASK FORCE ID NUMBER
============================================================ */
.tfn-id-number {
    position: absolute;
    top: 428px;
    left: 248px;
    width: 130px;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 19px;
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
   MEMBER NAME
============================================================ */
.tfn-member-name {
    position: absolute;
    top: 478px;
    left: 5px;
    width: 340px;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 20px;
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
   TASK FORCE POSITION
============================================================ */
.tfn-position {
    position: absolute;
    top: 508px;
    left: 5px;
    width: 340px;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 15px;
    font-weight: 900;
    line-height: 1.1;
    color: #1b7f3a;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    z-index: 5;
}


/* ============================================================
   MOBILE
============================================================ */
@media (max-width: 576px) {
    .task-force-national-card {
        width: 390px;
        height: 644px;
        min-width: 390px;
        max-width: 390px;
        min-height: 644px;
        max-height: 644px;
        flex: 0 0 390px;
    }
    .task-force-national-card-background {
        width: 390px;
        height: 644px;
    }
}


/* ============================================================
   PRINT
   ------------------------------------------------------------
   CRITICAL:
   Every value here MUST match the on-screen value above.
   All values were previously stale (548/578/25) and are now
   synced to the screen values (478/508/5).
============================================================ */
@media print {

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        width: 390px !important;
        min-width: 390px !important;
        max-width: 390px !important;
        background: transparent !important;
    }

    /* --------------------------------------------------------
       CARD
    -------------------------------------------------------- */
    .task-force-national-card {
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

    /* --------------------------------------------------------
       CLIENT ARTWORK
    -------------------------------------------------------- */
    .task-force-national-card-background {
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

    /* --------------------------------------------------------
       MEMBER PHOTO
    -------------------------------------------------------- */
    .tfn-photo {
        position: absolute !important;
        top: 222px !important;
        left: 52px !important;
        width: 205px !important;
        height: 240px !important;
        display: block !important;
        visibility: visible !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        z-index: 5 !important;
    }

    .tfn-photo img {
        display: block !important;
        width: 205px !important;
        height: 240px !important;
        min-width: 205px !important;
        max-width: 205px !important;
        min-height: 240px !important;
        max-height: 240px !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        object-fit: cover !important;
        object-position: center top !important;
    }

    /* --------------------------------------------------------
       TASK FORCE ID
    -------------------------------------------------------- */
    .tfn-id-number {
        position: absolute !important;
        top: 428px !important;
        left: 248px !important;
        width: 130px !important;
        display: block !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        text-align: center !important;
        z-index: 5 !important;
    }

    /* --------------------------------------------------------
       MEMBER NAME
       ✅ synced: top 478px, left 5px
    -------------------------------------------------------- */
    .tfn-member-name {
        position: absolute !important;
        top: 478px !important;
        left: 5px !important;
        width: 340px !important;
        display: block !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        text-align: center !important;
        z-index: 5 !important;
    }

    /* --------------------------------------------------------
       TASK FORCE POSITION
       ✅ synced: top 508px, left 5px
    -------------------------------------------------------- */
    .tfn-position {
        position: absolute !important;
        top: 508px !important;
        left: 5px !important;
        width: 340px !important;
        display: block !important;
        visibility: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        text-align: center !important;
        z-index: 5 !important;
    }

}

</style>
