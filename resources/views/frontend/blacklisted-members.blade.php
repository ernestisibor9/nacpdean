@extends('frontend.master')

@section('home')

<style>
    .hero-blacklist {
        background: #dc3545 !important;
    }

    .blacklist-card {
        position: relative;
        border: 2px solid #dc3545;
        border-radius: 15px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        transition: .3s;
    }

    .blacklist-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 40px rgba(220, 53, 69, .25);
    }

    .blacklist-image img {
        width: 100%;
        height: 330px;
        object-fit: cover;
    }

    .blacklist-body {
        padding: 20px;
    }

    .blacklist-ribbon {
        position: absolute;
        top: 18px;
        left: -45px;
        width: 180px;
        text-align: center;
        background: #dc3545;
        color: #fff;
        font-weight: 700;
        transform: rotate(-45deg);
        padding: 8px 0;
        z-index: 5;
        letter-spacing: 1px;
    }

    .stakeholder-name {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .company-name {
        color: #666;
        margin-bottom: 10px;
    }

    .page-link {
        color: #dc3545;
    }

    .page-item.active .page-link {
        background: #dc3545;
        border-color: #dc3545;
    }

    .page-link:hover {
        color: #dc3545;
    }

    .page-item.disabled .page-link {
        color: #999;
    }

    .no-blacklist {
        padding: 60px 20px;
    }

    .no-blacklist i {
        font-size: 50px;
        color: #198754;
        margin-bottom: 20px;
    }
</style>

<main class="main">


<!-- Hero Section -->
<section class="hero hero-blacklist text-center bg-danger">
    <div class="container">

        <h1 class="display-5 fw-bold text-white">
            Blacklisted Stakeholders Directory
        </h1>

        <p class="text-white">
            Suspended, blacklisted, and caution-listed stakeholders.
        </p>

    </div>
</section>


<div class="container mt-4">

    @if($blacklistedMembers->count() > 0)

        <!-- Important Notice -->
        <div class="alert alert-danger shadow-sm border-0">

            <h4 class="mb-3">
                <i class="fas fa-triangle-exclamation"></i>
                Important Notice
            </h4>

            <p class="mb-2">
                This directory contains details of individuals, companies, or stakeholders
                who have been suspended, blacklisted, or whose activities require members
                to exercise caution.
            </p>

            <p class="mb-0">
                Members are advised to verify information carefully and avoid engaging in
                transactions or partnerships with listed stakeholders until further notice
                from the Association.
            </p>

        </div>

    @endif

</div>


<!-- =========================================================
     BLACKLISTED MEMBERS
========================================================== -->

@if($blacklistedMembers->count() > 0)

    <div class="container">

        <!-- Search -->
        <input
            type="text"
            id="search"
            class="form-control form-control-lg"
            placeholder="Search by name, company or membership number"
        >


        <!-- ================= BLACKLISTED STAKEHOLDERS ================= -->
        <div
            class="row mt-3"
            id="blacklistGrid"
        >

            @foreach($blacklistedMembers as $member)

                <div
                    class="col-lg-4 col-md-6 stakeholder mb-3"
                    data-search="{{ strtolower(
                        ($member->member_name ?? '') . ' ' .
                        ($member->company_name ?? '') . ' ' .
                        ($member->membership_number ?? '') . ' ' .
                        ($member->state ?? '')
                    ) }}"
                >

                    <div class="blacklist-card">

                        <!-- Ribbon -->
                        <div class="blacklist-ribbon">
                            BLACKLISTED
                        </div>


                        <!-- Photo -->
                        <div class="blacklist-image">

                            @if($member->photo)

                                <img
                                    src="{{ asset('frontend/' . $member->photo) }}"
                                    class="img-fluid"
                                    alt="{{ $member->member_name }}"
                                >

                            @else

                                <img
                                    src="{{ asset('frontend/assets/img/blacklisted/default-avatar.jpeg') }}"
                                    class="img-fluid"
                                    alt="{{ $member->member_name }}"
                                >

                            @endif

                        </div>


                        <!-- Card Body -->
                        <div class="blacklist-body">

                            <h4 class="stakeholder-name">
                                {{ $member->member_name }}
                            </h4>


                            @if($member->company_name)

                                <p class="company-name">
                                    {{ $member->company_name }}
                                </p>

                            @endif


                            <hr>


                            <!-- Membership Number -->
                            <p>

                                <i class="fas fa-id-card text-danger"></i>

                                <strong>
                                    Membership No:
                                </strong>

                                {{ $member->membership_number ?? 'N/A' }}

                            </p>


                            <!-- State -->
                            <p>

                                <i class="fas fa-map-marker-alt text-danger"></i>

                                <strong>
                                    State:
                                </strong>

                                {{ $member->state ?? 'N/A' }}

                            </p>


                            <!-- Effective Date -->
                            <p>

                                <i class="fas fa-calendar text-danger"></i>

                                <strong>
                                    Effective Date:
                                </strong>

                                {{ $member->effective_date?->format('d F Y') ?? 'N/A' }}

                            </p>


                            <!-- Status -->
                            <p class="text-danger fw-bold">

                                <i class="fas fa-ban"></i>

                                Status:
                                {{ strtoupper($member->status ?? 'BLACKLISTED') }}

                            </p>


                            <!-- View Profile Button -->
                            <button
                                type="button"
                                class="btn btn-danger w-100"

                                data-bs-toggle="modal"
                                data-bs-target="#blacklistModal"

                                data-name="{{ $member->member_name ?? '' }}"

                                data-company="{{ $member->company_name ?? '' }}"

                                data-membership="{{ $member->membership_number ?? '' }}"

                                data-state="{{ $member->state ?? '' }}"

                                data-date="{{ $member->effective_date?->format('d F Y') ?? '' }}"

                                data-status="{{ strtoupper($member->status ?? 'BLACKLISTED') }}"

                                data-image="{{ $member->photo
                                    ? asset('frontend/' . $member->photo)
                                    : asset('frontend/assets/img/blacklisted/default-avatar.jpeg') }}"

                                data-reason="{{ $member->reason ?? '' }}"

                                data-until="{{ $member->blacklisted_until?->format('d F Y') ?? 'Until further notice' }}"
                            >

                                View Profile

                            </button>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        <!-- ================= PAGINATION ================= -->

        <nav
            aria-label="Page navigation"
            class="mt-4"
        >

            <ul
                class="pagination justify-content-center"
                id="pagination"
            ></ul>

        </nav>


        <!-- ================= BLACKLIST MODAL ================= -->

        <div
            class="modal fade"
            id="blacklistModal"
            tabindex="-1"
            aria-labelledby="blacklistModalLabel"
            aria-hidden="true"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">


                    <!-- Modal Header -->
                    <div class="modal-header bg-danger text-white">

                        <h5
                            class="modal-title text-white"
                            id="blacklistModalLabel"
                        >
                            Blacklisted Stakeholder Details
                        </h5>

                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>

                    </div>


                    <!-- Modal Body -->
                    <div class="modal-body">

                        <div class="row">


                            <!-- Image -->
                            <div class="col-md-4 text-center mb-3 mb-md-0">

                                <img
                                    id="modalImage"
                                    src=""
                                    class="img-fluid rounded shadow"
                                    alt="Blacklisted Stakeholder"
                                >

                            </div>


                            <!-- Details -->
                            <div class="col-md-8">

                                <h3 id="modalName"></h3>


                                <p>

                                    <strong>
                                        Company:
                                    </strong>

                                    <span id="modalCompany"></span>

                                </p>


                                <p>

                                    <strong>
                                        Membership No:
                                    </strong>

                                    <span id="modalMembership"></span>

                                </p>


                                <p>

                                    <strong>
                                        State:
                                    </strong>

                                    <span id="modalState"></span>

                                </p>


                                <p>

                                    <strong>
                                        Effective Date:
                                    </strong>

                                    <span id="modalDate"></span>

                                </p>


                                <p>

                                    <strong>
                                        Status:
                                    </strong>

                                    <span
                                        class="badge bg-danger"
                                        id="modalStatus"
                                    ></span>

                                </p>


                                <p>

                                    <strong>
                                        Blacklisted Until:
                                    </strong>

                                    <span id="modalUntil"></span>

                                </p>


                                <hr>


                                <h5 class="text-danger">
                                    Reason for Blacklisting
                                </h5>


                                <p id="modalReason"></p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@else

    <!-- =====================================================
         NO BLACKLISTED MEMBERS
    ====================================================== -->

    <div class="container">

        <div class="no-blacklist text-center">

            <i class="fas fa-circle-check"></i>

            <h3 class="fw-bold">
                No Blacklisted Stakeholders
            </h3>

            <p class="text-muted mb-0">
                There are currently no blacklisted stakeholders listed by the Association.
            </p>

        </div>

    </div>

@endif
```

</main>

@if($blacklistedMembers->count() > 0)

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | Modal Event Handler
        |--------------------------------------------------------------------------
        */

        const blacklistModal =
            document.getElementById('blacklistModal');

        if (blacklistModal) {

            blacklistModal.addEventListener(
                'show.bs.modal',
                function (event) {

                    const button = event.relatedTarget;


                    document.getElementById('modalName').innerText =
                        button.dataset.name || '';


                    document.getElementById('modalCompany').innerText =
                        button.dataset.company || 'N/A';


                    document.getElementById('modalMembership').innerText =
                        button.dataset.membership || 'N/A';


                    document.getElementById('modalState').innerText =
                        button.dataset.state || 'N/A';


                    document.getElementById('modalDate').innerText =
                        button.dataset.date || 'N/A';


                    document.getElementById('modalStatus').innerText =
                        button.dataset.status || 'BLACKLISTED';


                    document.getElementById('modalUntil').innerText =
                        button.dataset.until || 'Until further notice';


                    document.getElementById('modalReason').innerText =
                        button.dataset.reason || 'No reason provided.';


                    document.getElementById('modalImage').src =
                        button.dataset.image || '';

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        const searchInput =
            document.getElementById('search');


        /*
        |--------------------------------------------------------------------------
        | Cards
        |--------------------------------------------------------------------------
        */

        const cards = Array.from(
            document.querySelectorAll(
                "#blacklistGrid .stakeholder"
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        const cardsPerPage = 6;

        const pagination =
            document.getElementById("pagination");

        let currentPage = 1;


        /*
        |--------------------------------------------------------------------------
        | Get Filtered Cards
        |--------------------------------------------------------------------------
        */

        function getFilteredCards() {

            const searchValue =
                searchInput
                    ? searchInput.value.toLowerCase().trim()
                    : '';


            if (!searchValue) {

                return cards;

            }


            return cards.filter(function (card) {

                const searchableText =
                    card.dataset.search || '';

                return searchableText.includes(searchValue);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Show Page
        |--------------------------------------------------------------------------
        */

        function showPage(page, scroll = false) {

            const filteredCards =
                getFilteredCards();


            const totalPages =
                Math.ceil(
                    filteredCards.length / cardsPerPage
                );


            if (
                totalPages > 0 &&
                page > totalPages
            ) {

                page = totalPages;

            }


            if (page < 1) {

                page = 1;

            }


            currentPage = page;


            const start =
                (page - 1) * cardsPerPage;


            const end =
                start + cardsPerPage;


            /*
            |--------------------------------------------------------------------------
            | Hide All Cards
            |--------------------------------------------------------------------------
            */

            cards.forEach(function (card) {

                card.style.display = 'none';

            });


            /*
            |--------------------------------------------------------------------------
            | Show Current Page Cards
            |--------------------------------------------------------------------------
            */

            filteredCards.forEach(function (card, index) {

                if (
                    index >= start &&
                    index < end
                ) {

                    card.style.display = 'block';

                }

            });


            createPagination();


            /*
            |--------------------------------------------------------------------------
            | Scroll To Grid
            |--------------------------------------------------------------------------
            */

            if (scroll) {

                const grid =
                    document.getElementById(
                        "blacklistGrid"
                    );


                if (grid) {

                    grid.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Create Pagination
        |--------------------------------------------------------------------------
        */

        function createPagination() {

            if (!pagination) {

                return;

            }


            pagination.innerHTML = "";


            const filteredCards =
                getFilteredCards();


            const totalPages =
                Math.ceil(
                    filteredCards.length / cardsPerPage
                );


            /*
            |--------------------------------------------------------------------------
            | No Pagination Needed
            |--------------------------------------------------------------------------
            */

            if (totalPages <= 1) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Previous
            |--------------------------------------------------------------------------
            */

            const prev =
                document.createElement("li");


            prev.className =
                `page-item ${
                    currentPage === 1
                        ? 'disabled'
                        : ''
                }`;


            prev.innerHTML =
                '<a class="page-link" href="#">Previous</a>';


            prev.onclick = function (e) {

                e.preventDefault();


                if (currentPage > 1) {

                    showPage(
                        currentPage - 1,
                        true
                    );

                }

            };


            pagination.appendChild(prev);


            /*
            |--------------------------------------------------------------------------
            | Page Numbers
            |--------------------------------------------------------------------------
            */

            for (
                let i = 1;
                i <= totalPages;
                i++
            ) {

                const li =
                    document.createElement("li");


                li.className =
                    `page-item ${
                        i === currentPage
                            ? 'active'
                            : ''
                    }`;


                li.innerHTML =
                    `<a class="page-link" href="#">${i}</a>`;


                li.onclick = function (e) {

                    e.preventDefault();


                    showPage(
                        i,
                        true
                    );

                };


                pagination.appendChild(li);

            }


            /*
            |--------------------------------------------------------------------------
            | Next
            |--------------------------------------------------------------------------
            */

            const next =
                document.createElement("li");


            next.className =
                `page-item ${
                    currentPage === totalPages
                        ? 'disabled'
                        : ''
                }`;


            next.innerHTML =
                '<a class="page-link" href="#">Next</a>';


            next.onclick = function (e) {

                e.preventDefault();


                if (
                    currentPage < totalPages
                ) {

                    showPage(
                        currentPage + 1,
                        true
                    );

                }

            };


            pagination.appendChild(next);

        }


        /*
        |--------------------------------------------------------------------------
        | Search Event
        |--------------------------------------------------------------------------
        */

        if (searchInput) {

            searchInput.addEventListener(
                'input',
                function () {

                    currentPage = 1;

                    showPage(
                        1,
                        false
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        if (cards.length > 0) {

            showPage(
                1,
                false
            );

        }

    });
</script>

@endif

@endsection
