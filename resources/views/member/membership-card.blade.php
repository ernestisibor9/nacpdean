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
                | CARD TYPE LABEL
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
                */

                $categoryCode = strtoupper(
                    trim(
                        $card->category?->code ?? ''
                    )
                );


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

                $isRcgMember =
                    $memberType === 'affiliate';


                /*
                |--------------------------------------------------------------------------
                | AFFILIATE BODY
                |--------------------------------------------------------------------------
                */

                $affiliate =
                    $card->affiliate_body ?? null;


                /*
                |--------------------------------------------------------------------------
                | MEMBERSHIP NUMBER
                |--------------------------------------------------------------------------
                */

                $membershipNumber =
                    $card->membership_number ?? '';


                /*
                |--------------------------------------------------------------------------
                | CARD NUMBER
                |--------------------------------------------------------------------------
                */

                $cardNumber =
                    $card->card_number ?? '';


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

                    : asset(
                        'images/photo-placeholder.png'
                    );


                /*
                |--------------------------------------------------------------------------
                | QR IMAGE
                |--------------------------------------------------------------------------
                */

                $qrImage =
                    $card->qr_image ?? null;


                /*
                |--------------------------------------------------------------------------
                | POSITION
                |--------------------------------------------------------------------------
                |
                | Membership:
                |     memberships.membership_position
                |
                | National Executive:
                |     memberships.membership_position
                |
                | Task Force:
                |     approved Task Force appointment
                |
                */

                $taskForceAppointment =
                    $card->taskForceAppointment ?? null;


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
                | TASK FORCE LEVEL
                |--------------------------------------------------------------------------
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
                */

                $isStateTaskForce =
                    $card->card_type === 'task_force' &&
                    $taskForceLevel === 'state';


                /*
                |--------------------------------------------------------------------------
                | CARD DESIGN
                |--------------------------------------------------------------------------
                |
                | This is the important part of the new architecture.
                |
                | We determine the design here.
                |
                | The member does NOT select the design.
                |
                */

                $cardDesign = null;


                if ($card->card_type === 'national_executive') {

                    $cardDesign =
                        'member.id-cards.designs.national-executive';

                } elseif ($card->card_type === 'task_force') {

                    if ($isNationalTaskForce) {

                        $cardDesign =
                            'member.id-cards.designs.task-force-national';

                    } elseif ($isStateTaskForce) {

                        $cardDesign =
                            'member.id-cards.designs.task-force-state';

                    }

                } elseif ($card->card_type === 'membership') {

                    $cardDesign = match ($categoryCode) {

                        'EXP' =>
                            'member.id-cards.designs.exporter',

                        'PRD' =>
                            'member.id-cards.designs.producer',

                        'DEA' =>
                            'member.id-cards.designs.dealer',

                        'SLR' =>
                            'member.id-cards.designs.supplier',

                        'RCG' =>
                            'member.id-cards.designs.rcg',

                        default =>
                            null,

                    };

                }

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
                                FRONT DESIGN
                            ================================================== --}}

                            @if ($cardDesign)

                                @include(
                                    $cardDesign,
                                    [
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
                                    ]
                                )

                            @else

                                {{-- =================================================
                                    UNKNOWN / UNSUPPORTED CARD
                                ================================================== --}}
                                <div
                                    class="client-card unsupported-card"
                                >

                                    <div class="unsupported-card-content">

                                        <i
                                            class="bi bi-card-heading"
                                            style="font-size: 50px;"
                                        ></i>

                                        <h4>
                                            Card Design Not Available
                                        </h4>

                                        <p>
                                            The design for this card
                                            has not yet been configured.
                                        </p>

                                        <small>
                                            Card Type:
                                            {{ $card->card_type }}
                                        </small>

                                        <br>

                                        <small>
                                            Category:
                                            {{ $categoryCode ?: 'N/A' }}
                                        </small>

                                    </div>

                                </div>

                            @endif


                            {{-- =================================================
                                COMMON BACK OF CARD
                            ================================================== --}}
                            @include(
                                'member.id-cards.card-back',
                                [
                                    'card' => $card,
                                    'membershipNumber' => $membershipNumber,
                                    'cardNumber' => $cardNumber,
                                    'qrImage' => $qrImage,
                                ]
                            )

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


                                    {{-- CARD TYPE --}}
                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Card Type
                                        </small>

                                        <strong>
                                            {{ $cardTypeLabel }}
                                        </strong>

                                    </div>


                                    {{-- MEMBERSHIP NUMBER --}}
                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Membership Number
                                        </small>

                                        <strong>
                                            {{ $membershipNumber }}
                                        </strong>

                                    </div>


                                    {{-- CATEGORY --}}
                                    <div class="col-md-3 mb-3 mb-md-0">

                                        <small class="text-muted d-block">
                                            Category
                                        </small>

                                        <strong>
                                            {{ $category }}
                                        </strong>

                                    </div>


                                    {{-- STATUS --}}
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
```

</div>

{{-- =============================================================
MAIN PAGE CSS
============================================================== --}}

<style>

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


/*
|--------------------------------------------------------------------------
| COMMON CARD SIZE
|--------------------------------------------------------------------------
|
| The individual designs will control their own artwork/positioning.
|
*/

.client-card {

    position: relative;

    width: 390px;

    height: 600px;

    flex: 0 0 390px;

    overflow: hidden;

    border-radius: 6px;

}


/*
|--------------------------------------------------------------------------
| TEMPORARY UNSUPPORTED CARD
|--------------------------------------------------------------------------
|
| This is only a safety fallback while we build the individual designs.
|
*/

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


/*
|--------------------------------------------------------------------------
| RESPONSIVE
|--------------------------------------------------------------------------
*/

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


    .client-card {

        width: 350px;

        height: 538px;

        flex-basis: 350px;

    }

}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

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


    .client-card {

        box-shadow: none !important;

        flex: 0 0 390px;

    }


    /*
    |--------------------------------------------------------------------------
    | HIDE INFORMATION BOXES DURING PRINT
    |--------------------------------------------------------------------------
    |
    | Only the actual front/back cards should be printed.
    |
    */

    .card-group-wrapper > .row.mt-4 {

        display: none !important;

    }


    /*
    |--------------------------------------------------------------------------
    | HIDE PRINT BUTTON
    |--------------------------------------------------------------------------
    */

    button {

        display: none !important;

    }

}

</style>

@endsection
