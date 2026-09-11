@extends('frontend.master')

@section('home')

<style>

    .member-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .08);
        border: 1px solid #ececec;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .member-image {
        width: 100%;
        height: 350px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .member-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center;
        display: block;
    }

    .member-body {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .member-badge {
        background: #608C11;
        color: #fff;
        border-radius: 30px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        align-self: center;
        margin-bottom: 18px;
    }

    .member-name {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        color: #222;
        margin-bottom: 8px;
    }

    .member-position {
        text-align: center;
        color: #608C11;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .member-state {
        text-align: center;
        color: #b8860b;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .member-info {
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        padding: 18px 0;
        margin-bottom: 22px;
    }

    .member-info p {
        margin-bottom: 12px;
        color: #555;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .member-info p:last-child {
        margin-bottom: 0;
    }

    .member-info i {
        width: 20px;
        text-align: center;
        color: #608C11;
        font-size: 15px;
    }

    .btn-main {
        background: #608C11;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        margin-top: auto;
    }

    .btn-main:hover {
        background: #496b0c;
        color: #fff;
    }

    .text-success {
        color: #2e7d32 !important;
        font-weight: 600;
    }

    .no-executives {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 18px;
        padding: 60px 30px;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .06);
    }

    .no-executives i {
        font-size: 55px;
        color: #608C11;
        margin-bottom: 20px;
    }

    .no-executives h4 {
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    .no-executives p {
        color: #777;
        margin-bottom: 0;
    }

    @media (max-width: 991px) {

        .member-image {
            height: 300px;
        }

    }

    @media (max-width: 767px) {

        .member-image {
            height: 280px;
        }

        .member-name {
            font-size: 20px;
        }

    }

    @media (max-width: 575px) {

        .member-image {
            height: 260px;
        }

        .member-body {
            padding: 20px;
        }

        .member-name {
            font-size: 18px;
        }

        .member-position {
            font-size: 15px;
        }

    }

</style>

<main class="main">

```
<!-- HERO -->

<section class="hero text-center">

    <div class="container">

        <h1 class="display-5 fw-bold text-white">
            State Executive Members
        </h1>

        <p>
            Meet the State Executive Members of NACPDEAN
        </p>

    </div>

</section>


<div class="container mt-4">

    <!-- SEARCH -->

    @if($executives->isNotEmpty())

        <div class="search mb-4">

            <input
                type="text"
                id="search"
                class="form-control form-control-lg"
                placeholder="Search by name, position or state"
            >

        </div>

    @endif


    <!-- STATE EXECUTIVES -->

    <div
        class="row g-4"
        id="grid"
    >

        @forelse($executives as $executive)

            @php

                $membership = $executive->membership;

                $profile = $membership?->profile;

                $user = $membership?->user;


                /*
                |--------------------------------------------------------------------------
                | MEMBER NAME
                |--------------------------------------------------------------------------
                */

                $memberName = strtoupper(
                    trim(
                        ($profile?->surname ?? '') . ' ' .
                        ($profile?->first_name ?? '') . ' ' .
                        ($profile?->middle_name ?? '')
                    )
                );

                if (!$memberName) {

                    $memberName = strtoupper(
                        $user?->name ?? 'STATE EXECUTIVE'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | POSITION
                |--------------------------------------------------------------------------
                */

                $position = strtoupper(
                    trim(
                        $executive->position
                        ?? $membership?->taskforce_position
                        ?? 'STATE EXECUTIVE'
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | STATE
                |--------------------------------------------------------------------------
                */

                $state = strtoupper(
                    trim(
                        $executive->state
                        ?? $membership?->taskforce_state
                        ?? 'STATE'
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | MEMBERSHIP NUMBER
                |--------------------------------------------------------------------------
                */

                $membershipNumber =
                    $membership?->membership_number ?? '';


                /*
                |--------------------------------------------------------------------------
                | CATEGORY
                |--------------------------------------------------------------------------
                */

                $category = strtoupper(
                    trim(
                        $membership?->membershipCategory?->name
                        ?? 'MEMBER'
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | PHONE
                |--------------------------------------------------------------------------
                */

                $phone =
                    $profile?->phone
                    ?? $user?->phone
                    ?? 'Not available';


                /*
                |--------------------------------------------------------------------------
                | EMAIL
                |--------------------------------------------------------------------------
                */

                $email =
                    $user?->email
                    ?? 'Not available';


                /*
                |--------------------------------------------------------------------------
                | PHOTO
                |--------------------------------------------------------------------------
                */

                $image = $profile?->photo
                    ? asset(
                        'uploads/member_profiles/' .
                        $profile->photo
                    )
                    : asset(
                        'images/photo-placeholder.png'
                    );


                /*
                |--------------------------------------------------------------------------
                | BIO
                |--------------------------------------------------------------------------
                */

                $bio =
                    'This member currently serves as ' .
                    $position .
                    ' of NACPDEAN in ' .
                    $state .
                    '.';

            @endphp


            <div
                class="col-lg-4 col-md-6 member"

                data-name="{{ strtolower($memberName) }}"

                data-position="{{ strtolower($position) }}"

                data-state="{{ strtolower($state) }}"

                data-num="{{ strtolower($membershipNumber) }}"
            >

                <div class="member-card">

                    <!-- PHOTO -->

                    <div class="member-image">

                        <img
                            src="{{ $image }}"
                            alt="{{ $memberName }}"
                            loading="lazy"
                            decoding="async"
                        >

                    </div>


                    <!-- BODY -->

                    <div class="member-body">

                        <span class="badge member-badge">
                            State Executive
                        </span>


                        <h4 class="member-name">
                            {{ $memberName }}
                        </h4>


                        <p class="member-position">
                            {{ $position }}
                        </p>


                        <p class="member-state">
                            {{ $state }}
                        </p>


                        <div class="member-info">

                            @if($membershipNumber)

                                <p>

                                    <i class="fas fa-id-card"></i>

                                    {{ $membershipNumber }}

                                </p>

                            @endif


                            <p>

                                <i class="fas fa-map-marker-alt"></i>

                                {{ $state }}

                            </p>


                            <p class="text-success">

                                <i class="fas fa-check-circle"></i>

                                Active Member

                            </p>

                        </div>


                        <!-- VIEW PROFILE -->

                        <button
                            type="button"
                            class="btn btn-main w-100 profile-btn"

                            data-name="{{ $memberName }}"

                            data-position="{{ $position }}"

                            data-state="{{ $state }}"

                            data-image="{{ $image }}"

                            data-phone="{{ $phone }}"

                            data-email="{{ $email }}"

                            data-bio="{{ $bio }}"

                            data-bs-toggle="modal"

                            data-bs-target="#profileModal"
                        >

                            View Profile

                        </button>

                    </div>

                </div>

            </div>


        @empty

            <div class="col-12">

                <div class="no-executives">

                    <i class="fas fa-users"></i>

                    <h4>
                        No State Executives Yet
                    </h4>

                    <p>
                        There are currently no approved State Executive Members.
                    </p>

                </div>

            </div>

        @endforelse

    </div>


    @if($executives->isNotEmpty())

        <div
            id="noResults"
            class="no-executives mt-4"
            style="display:none;"
        >

            <i class="fas fa-search"></i>

            <h4>
                No State Executive Found
            </h4>

            <p>
                No State Executive matches your search.
            </p>

        </div>

    @endif

</div>
```

</main>

<!-- PROFILE MODAL -->

<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
    aria-hidden="true"
>

```
<div class="modal-dialog modal-lg modal-dialog-scrollable">

    <div class="modal-content">

        <div class="modal-header">

            <h4
                id="modalName"
                class="modal-title"
            ></h4>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
            ></button>

        </div>


        <div class="modal-body">

            <div class="row">

                <div class="col-md-4">

                    <img
                        id="modalImage"
                        class="img-fluid rounded shadow"
                        alt="State Executive Profile"
                    >

                </div>


                <div class="col-md-8">

                    <h5
                        id="modalPosition"
                        class="text-success fw-bold"
                    ></h5>

                    <p
                        id="modalState"
                        class="fw-bold"
                    ></p>

                    <hr>

                    <p id="modalBio"></p>

                    <hr>

                    <p>
                        <strong>Phone:</strong>
                        <span id="modalPhone"></span>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <span id="modalEmail"></span>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

<!-- SIMPLE JAVASCRIPT -->

@if($executives->isNotEmpty())

<script>

document.addEventListener('DOMContentLoaded', function () {

    const grid = document.getElementById('grid');

    const searchInput = document.getElementById('search');

    const noResults = document.getElementById('noResults');


    if (!grid || !searchInput) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | PROFILE MODAL
    |--------------------------------------------------------------------------
    */

    grid.addEventListener('click', function (event) {

        const button =
            event.target.closest('.profile-btn');

        if (!button) {
            return;
        }


        document.getElementById('modalName').textContent =
            button.dataset.name || '';

        document.getElementById('modalPosition').textContent =
            button.dataset.position || '';

        document.getElementById('modalState').textContent =
            button.dataset.state || '';

        document.getElementById('modalBio').textContent =
            button.dataset.bio || '';

        document.getElementById('modalPhone').textContent =
            button.dataset.phone || 'Not available';

        document.getElementById('modalEmail').textContent =
            button.dataset.email || 'Not available';

        document.getElementById('modalImage').src =
            button.dataset.image || '';

    });


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    const cards =
        grid.querySelectorAll('.member');


    searchInput.addEventListener('input', function () {

        const search =
            this.value.toLowerCase().trim();

        let visible = 0;


        cards.forEach(function (card) {

            const name =
                card.dataset.name || '';

            const position =
                card.dataset.position || '';

            const state =
                card.dataset.state || '';

            const number =
                card.dataset.num || '';


            const matches =
                name.includes(search) ||
                position.includes(search) ||
                state.includes(search) ||
                number.includes(search);


            if (matches) {

                card.style.display = '';

                visible++;

            } else {

                card.style.display = 'none';

            }

        });


        if (noResults) {

            noResults.style.display =
                visible === 0 ? 'block' : 'none';

        }

    });

});

</script>

@endif

@endsection
