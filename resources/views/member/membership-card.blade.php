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

                $cardTypeLabel = match ($card->card_type) {
                    'national_executive' => 'National Executive Card',
                    'task_force'         => 'Task Force Card',
                    default              => 'Membership Card',
                };

                $memberName = strtoupper(
                    trim(
                        ($card->profile?->surname ?? '') . ' ' .
                        ($card->profile?->first_name ?? '') . ' ' .
                        ($card->profile?->middle_name ?? '')
                    )
                );

                $category     = strtoupper($card->category?->name ?? 'MEMBER');
                $categoryCode = strtoupper(trim($card->category?->code ?? ''));

                $memberType  = strtolower($card->category?->member_type ?? '');
                $isRcgMember = $memberType === 'affiliate';
                $affiliate   = $card->affiliate_body ?? null;

                $membershipNumber = $card->membership_number ?? '';
                $cardNumber       = $card->card_number ?? '';

                $photoUrl = $card->profile?->photo
                    ? asset('uploads/member_profiles/' . $card->profile->photo)
                    : asset('images/photo-placeholder.png');

                $qrImage = $card->qr_image ?? null;
                $qrCode  = $qrImage ?? null;

                $issueDate = $card->issued_at
                    ? \Carbon\Carbon::parse($card->issued_at)->format('d/m/Y')
                    : null;

                $expirationDate = $card->expires_at
                    ? \Carbon\Carbon::parse($card->expires_at)->format('d/m/Y')
                    : null;

                $taskForceAppointment = $card->taskForceAppointment ?? null;

                $position = match ($card->card_type) {
                    'national_executive' => $card->membership?->membership_position,
                    'task_force'         => $taskForceAppointment?->position,
                    'membership'         => $card->membership?->membership_position,
                    default              => null,
                };

                $taskForceLevel = strtolower(trim($taskForceAppointment?->level ?? ''));
                $taskForceState = strtoupper(trim($taskForceAppointment?->state ?? ''));

                $isNationalTaskForce =
                    $card->card_type === 'task_force' &&
                    in_array(
                        $taskForceLevel,
                        ['national', 'national level', 'national-level'],
                        true
                    );

                $isStateTaskForce =
                    $card->card_type === 'task_force' &&
                    $taskForceLevel === 'state';

                $cardDesign = null;

                if ($card->card_type === 'national_executive') {
                    $cardDesign = 'member.id-cards.designs.national-executive';
                } elseif ($card->card_type === 'task_force') {
                    if ($isNationalTaskForce) {
                        $cardDesign = 'member.id-cards.designs.task-force-national';
                    } elseif ($isStateTaskForce) {
                        $cardDesign = 'member.id-cards.designs.task-force-state';
                    }
                } elseif ($card->card_type === 'membership') {
                    $cardDesign = match ($categoryCode) {
                        'EXP' => 'member.id-cards.designs.exporter',
                        'PRD' => 'member.id-cards.designs.producer',
                        'DEA' => 'member.id-cards.designs.dealer',
                        'SLR' => 'member.id-cards.designs.supplier',
                        'RCG' => 'member.id-cards.designs.rcg',
                        default => null,
                    };
                }

            @endphp

            <div class="card-group-wrapper">

                <div class="text-center mb-3 card-type-label">

                    <h5 class="mb-1">
                        {{ $cardTypeLabel }}
                    </h5>

                    @if ($card->card_type === 'national_executive')
                        <small class="text-muted">National Executive Appointment</small>
                    @elseif ($card->card_type === 'task_force')
                        <small class="text-muted">Task Force Appointment</small>
                    @else
                        <small class="text-muted">General Membership</small>
                    @endif

                </div>

                <div class="row justify-content-center">

                    <div class="col-12">

                        <div class="id-card-preview">

                            @if ($cardDesign)

                                @include($cardDesign, [
                                    'card' => $card,
                                    'memberName' => $memberName,
                                    'category' => $category,
                                    'categoryCode' => $categoryCode,
                                    'memberType' => $memberType,
                                    'isRcgMember' => $isRcgMember,
                                    'affiliate' => $affiliate,
                                    'membershipNumber' => $membershipNumber,
                                    'cardNumber' => $cardNumber,
                                    'photoUrl' => $photoUrl,
                                    'qrImage' => $qrImage,
                                    'position' => $position,
                                    'taskForceAppointment' => $taskForceAppointment,
                                    'taskForceLevel' => $taskForceLevel,
                                    'taskForceState' => $taskForceState,
                                    'isNationalTaskForce' => $isNationalTaskForce,
                                    'isStateTaskForce' => $isStateTaskForce,
                                ])

                            @else

                                <div class="client-card unsupported-card">

                                    <div class="unsupported-card-content">

                                        <i class="bi bi-card-heading" style="font-size: 50px;"></i>

                                        <h4>Card Design Not Available</h4>

                                        <p>The design for this card has not yet been configured.</p>

                                        <small>Card Type: {{ $card->card_type }}</small>
                                        <br>
                                        <small>Category: {{ $categoryCode ?: 'N/A' }}</small>

                                    </div>

                                </div>

                            @endif

                            @include('member.id-cards.card-back', [
                                'card' => $card,
                                'membershipNumber' => $membershipNumber,
                                'cardNumber' => $cardNumber,
                                'qrImage' => $qrImage,
                                'qrCode' => $qrCode,
                                'issueDate' => $issueDate,
                                'expirationDate' => $expirationDate,
                            ])

                        </div>

                    </div>

                </div>

                <div class="row justify-content-center mt-4">

                    <div class="col-lg-8">

                        <div class="card border-0 shadow-sm">

                            <div class="card-body">

                                <div class="row text-center">

                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <small class="text-muted d-block">Card Type</small>
                                        <strong>{{ $cardTypeLabel }}</strong>
                                    </div>

                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <small class="text-muted d-block">Membership Number</small>
                                        <strong>{{ $membershipNumber }}</strong>
                                    </div>

                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <small class="text-muted d-block">Category</small>
                                        <strong>{{ $category }}</strong>
                                    </div>

                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Status</small>

                                        @if ($card->status === 'active')
                                            <span class="badge bg-success">ACTIVE</span>
                                        @else
                                            <span class="badge bg-danger">{{ strtoupper($card->status) }}</span>
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

/* -------------------- SCREEN -------------------- */

.id-card-preview {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 30px;
    width: 100%;
    overflow-x: auto;
    padding: 20px;
}

.card-group-wrapper {
    margin-bottom: 50px;
}

.card-type-label h5 {
    font-weight: 700;
}

.client-card {
    position: relative;
    width: 390px;
    height: 644px;
    min-width: 390px;
    max-width: 390px;
    min-height: 644px;
    max-height: 644px;
    flex: 0 0 390px;
    overflow: hidden;
    box-sizing: border-box;
}

.unsupported-card {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #f8f9fa;
    border: 2px dashed #adb5bd;
}

.unsupported-card-content {
    padding: 30px;
    color: #495057;
}

.unsupported-card-content h4 {
    margin-top: 15px;
    font-weight: 700;
}

.unsupported-card-content p {
    margin-bottom: 10px;
}

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
}


/* ============================================================
   PRINT
   ------------------------------------------------------------
   KEY POINTS:

   1. Page size is 300 × 495 — smaller than the card’s 390 ×
      644 so Chrome scales the card down cleanly rather than
      truncating it.

   2. The .client-card / .membership-card-back are scaled with
      transform: scale(0.7692) to convert 390 × 644 → 300 × 495
      visually.

   3. Negative margins (-90px right, -149px bottom) collapse
      the layout box so the printed page only reserves 300 × 495
      for each card.

   4. All dashboard chrome is removed via display: none so no
      space is reserved above the first card → no blank page 1.

   5. One card per page via page-break-before on every card
      except the very first.

   Chrome print settings to use:
      - Margins: None
      - Scale: 100%
      - Background graphics: ON
      - Headers and footers: OFF
   ============================================================ */

@media print {

    @page {
        size: 300px 495px;
        margin: 0;
    }

    html, body {
        width: 300px !important;
        min-width: 300px !important;
        max-width: 300px !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ---- Hide all non-print chrome ---- */
    nav,
    header,
    footer,
    aside,
    .navbar,
    .sidebar,
    .sidebar-wrapper,
    .layout-wrapper,
    .layout-sidenav-content,
    .page-header,
    .container-fluid > .row:first-child,
    .card-type-label,
    .card-group-wrapper > .row.mt-4,
    button {
        display: none !important;
    }

    /* ---- Print area ---- */
    #id-card-print-area {
        display: block !important;
        position: static !important;
        width: 300px !important;
        max-width: 300px !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: #ffffff !important;
    }

    #id-card-print-area .card-group-wrapper {
        display: block !important;
        width: 300px !important;
        max-width: 300px !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* ---- Collapse Bootstrap wrappers ---- */
    #id-card-print-area .row,
    #id-card-print-area .col-12,
    #id-card-print-area .col-lg-8,
    #id-card-print-area .col-md-3,
    .container-fluid {
        display: block !important;
        width: 300px !important;
        max-width: 300px !important;
        flex: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ---- Stack cards vertically ---- */
    #id-card-print-area .id-card-preview {
        display: block !important;
        width: 300px !important;
        max-width: 300px !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* ---- Cards: intrinsic 390×644 + scale(0.769) + negative margins ---- */
    #id-card-print-area .client-card,
    #id-card-print-area .membership-card-back {
        position: relative !important;
        display: block !important;

        /* Intrinsic size so absolutely positioned children stay correct */
        width: 390px !important;
        height: 644px !important;
        min-width: 390px !important;
        max-width: 390px !important;
        min-height: 644px !important;
        max-height: 644px !important;

        /* Scale visually down to 300 × 495 */
        transform: scale(0.7692307692) !important;
        transform-origin: top left !important;

        /* Collapse layout box back to 300 × 495 */
        margin-top: 0 !important;
        margin-left: 0 !important;
        margin-right: -90px !important;    /* 390 - 300 */
        margin-bottom: -149px !important;  /* 644 - 495 */

        flex: none !important;
        padding: 0 !important;
        overflow: hidden !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* ---- Page breaks ---- */

    /* First front card of the first group: no break */
    #id-card-print-area
    .card-group-wrapper:first-child
    .id-card-preview
    > .client-card:first-child {
        page-break-before: auto !important;
        break-before: auto !important;
    }

    /* Every back card: new page */
    #id-card-print-area
    .id-card-preview
    .membership-card-back {
        page-break-before: always !important;
        break-before: page !important;
    }

    /* Every subsequent front card: new page */
    #id-card-print-area
    .card-group-wrapper
    + .card-group-wrapper
    .id-card-preview
    > .client-card {
        page-break-before: always !important;
        break-before: page !important;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

}

</style>

@endsection
