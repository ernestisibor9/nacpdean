@extends('frontend.master')

@section('home')

<style>
    /* =========================================================
       NACPDEAN REGISTERED EXPORTERS
       Lightweight • Responsive • Professional
    ========================================================= */

    .exporters-page {
        background: #f7f9fc;
        padding: 70px 0 80px;
    }

    /* ---------------------------------------------------------
       HERO / HEADER
    --------------------------------------------------------- */

    .exporters-header {
        text-align: center;
        max-width: 760px;
        margin: 0 auto 42px;
    }

    .exporters-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        background: rgba(25, 135, 84, 0.09);
        color: #198754;

        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;

        padding: 8px 16px;
        border-radius: 50px;

        margin-bottom: 16px;
    }

    .exporters-eyebrow::before {
        content: "";

        width: 7px;
        height: 7px;

        background: #198754;
        border-radius: 50%;
    }

    .exporters-title {
        margin: 0 0 14px;

        color: #172b4d;

        font-size: clamp(32px, 4vw, 46px);
        font-weight: 800;

        line-height: 1.15;
        letter-spacing: -1px;
    }

    .exporters-title span {
        color: #198754;
    }

    .exporters-description {
        margin: 0 auto;

        color: #68778d;

        font-size: 16px;
        line-height: 1.7;

        max-width: 650px;
    }


    /* ---------------------------------------------------------
       SEARCH
    --------------------------------------------------------- */

    .exporters-search {
        max-width: 850px;

        margin: 0 auto 34px;
    }

    .search-wrapper {
        position: relative;

        display: flex;
        align-items: center;

        background: #ffffff;

        border: 1px solid #e2e8f0;
        border-radius: 14px;

        padding: 6px;

        box-shadow: 0 8px 28px rgba(31, 45, 61, 0.07);

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .search-wrapper:focus-within {
        border-color: #198754;

        box-shadow:
            0 8px 30px rgba(25, 135, 84, 0.12);
    }

    .search-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #198754;

        font-size: 18px;

        flex-shrink: 0;
    }

    .search-input {
        flex: 1;
        min-width: 0;

        height: 46px;

        border: 0;
        outline: 0;

        background: transparent;

        color: #344054;

        font-size: 14px;
        font-weight: 500;
    }

    .search-input::placeholder {
        color: #98a2b3;

        font-weight: 400;
    }

    .search-clear {
        width: 40px;
        height: 40px;

        display: none;

        align-items: center;
        justify-content: center;

        color: #98a2b3;

        text-decoration: none;

        flex-shrink: 0;

        cursor: pointer;

        transition: color 0.2s ease;
    }

    .search-clear:hover {
        color: #dc3545;
    }

    .search-clear.visible {
        display: flex;
    }


    /* ---------------------------------------------------------
       RESULTS COUNT
    --------------------------------------------------------- */

    .exporters-summary {
        display: flex;
        justify-content: center;

        margin-bottom: 20px;
    }

    .exporters-count {
        display: inline-flex;
        align-items: center;
        gap: 10px;

        background: #ffffff;

        border: 1px solid #e8edf3;

        border-radius: 50px;

        padding: 9px 17px;

        color: #526174;

        font-size: 14px;
        font-weight: 600;

        box-shadow: 0 5px 20px rgba(25, 42, 70, 0.05);
    }

    .exporters-count strong {
        color: #198754;
        font-size: 16px;
    }

    .exporters-results {
        margin-top: 0;
        margin-bottom: 28px;

        color: #7a8798;

        font-size: 13px;

        text-align: center;
    }

    .exporters-results strong {
        color: #344054;
    }


    /* ---------------------------------------------------------
       MEMBER GRID
    --------------------------------------------------------- */

    .exporter-card-column {
        transition: opacity 0.2s ease;
    }

    .exporter-card-column.hidden {
        display: none;
    }


    /* ---------------------------------------------------------
       MEMBER CARD
    --------------------------------------------------------- */

    .member-card {
        height: 100%;

        background: #ffffff;

        border: 1px solid #e9eef4;
        border-radius: 18px;

        overflow: hidden;

        position: relative;

        box-shadow: 0 8px 30px rgba(31, 45, 61, 0.07);

        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    .member-card:hover {
        transform: translateY(-6px);

        border-color: rgba(25, 135, 84, 0.25);

        box-shadow:
            0 16px 40px rgba(31, 45, 61, 0.12);
    }


    /* ---------------------------------------------------------
       MEMBER PHOTO
    --------------------------------------------------------- */

    .member-photo-wrapper {
        position: relative;

        height: 290px;

        overflow: hidden;

        background: #edf2f7;
    }

    .member-photo-wrapper::after {
        content: "";

        position: absolute;

        inset: 0;

        background: linear-gradient(
            to bottom,
            transparent 55%,
            rgba(0, 0, 0, 0.12)
        );

        pointer-events: none;
    }

    .member-photo {
        width: 100%;
        height: 100%;

        display: block;

        object-fit: cover;
        object-position: center;

        transition: transform 0.4s ease;
    }

    .member-card:hover .member-photo {
        transform: scale(1.035);
    }


    /* ---------------------------------------------------------
       VERIFIED BADGE
    --------------------------------------------------------- */

    .member-status {
        position: absolute;

        top: 16px;
        right: 16px;

        z-index: 2;

        display: inline-flex;
        align-items: center;
        gap: 6px;

        background: rgba(255, 255, 255, 0.96);

        color: #198754;

        padding: 7px 11px;

        border-radius: 50px;

        font-size: 11px;
        font-weight: 700;

        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.12);
    }

    .member-status i {
        font-size: 13px;
    }


    /* ---------------------------------------------------------
       CARD BODY
    --------------------------------------------------------- */

    .member-body {
        padding: 24px 23px 25px;
    }

    .member-badge {
        display: inline-block;

        color: #198754;

        background: rgba(25, 135, 84, 0.09);

        padding: 6px 11px;

        border-radius: 6px;

        font-size: 11px;
        font-weight: 800;

        letter-spacing: 0.7px;

        text-transform: uppercase;

        margin-bottom: 12px;
    }

    .member-name {
        color: #172b4d;

        font-size: 21px;
        font-weight: 750;

        line-height: 1.3;

        margin: 0 0 5px;
    }

    .member-position {
        color: #7a8798;

        font-size: 13px;
        font-weight: 600;

        margin: 0 0 20px;
    }


    /* ---------------------------------------------------------
       MEMBER INFORMATION
    --------------------------------------------------------- */

    .member-info {
        border-top: 1px solid #edf0f4;

        padding-top: 16px;
    }

    .member-info-row {
        display: flex;
        align-items: flex-start;

        gap: 11px;

        margin-bottom: 13px;
    }

    .member-info-row:last-child {
        margin-bottom: 0;
    }

    .member-info-icon {
        flex: 0 0 30px;

        width: 30px;
        height: 30px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #f2f8f5;

        color: #198754;

        border-radius: 8px;

        font-size: 13px;
    }

    .member-info-content {
        min-width: 0;
    }

    .member-info-label {
        display: block;

        color: #98a3b3;

        font-size: 10px;
        font-weight: 700;

        letter-spacing: 0.7px;

        text-transform: uppercase;

        margin-bottom: 2px;
    }

    .member-info-value {
        display: block;

        color: #344054;

        font-size: 13px;
        font-weight: 600;

        line-height: 1.45;

        word-break: break-word;
    }


    /* ---------------------------------------------------------
       EMPTY STATE
    --------------------------------------------------------- */

    .exporters-empty {
        background: #ffffff;

        border: 1px solid #e9eef4;

        border-radius: 18px;

        padding: 65px 25px;

        text-align: center;

        box-shadow: 0 8px 30px rgba(31, 45, 61, 0.05);

        max-width: 850px;

        margin: 0 auto;
    }

    .exporters-empty-icon {
        width: 65px;
        height: 65px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 18px;

        background: #f2f8f5;

        color: #198754;

        border-radius: 50%;

        font-size: 25px;
    }

    .exporters-empty h4 {
        color: #172b4d;

        font-weight: 700;

        margin-bottom: 8px;
    }

    .exporters-empty p {
        color: #7a8798;

        margin: 0;
    }


    /* ---------------------------------------------------------
       LIVE SEARCH EMPTY STATE
    --------------------------------------------------------- */

    .live-search-empty {
        display: none;

        background: #ffffff;

        border: 1px solid #e9eef4;

        border-radius: 18px;

        padding: 55px 25px;

        text-align: center;

        box-shadow: 0 8px 30px rgba(31, 45, 61, 0.05);

        margin-top: 10px;
    }

    .live-search-empty.visible {
        display: block;
    }

    .live-search-empty-icon {
        width: 65px;
        height: 65px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 18px;

        background: #f2f8f5;

        color: #198754;

        border-radius: 50%;

        font-size: 25px;
    }

    .live-search-empty h4 {
        color: #172b4d;

        font-weight: 700;

        margin-bottom: 8px;
    }

    .live-search-empty p {
        color: #7a8798;

        margin: 0;
    }


    /* ---------------------------------------------------------
       PAGINATION
       Hidden during live search.
    --------------------------------------------------------- */

    .exporters-pagination {
        display: flex;

        justify-content: center;

        margin-top: 55px;
    }

    .exporters-pagination nav {
        display: flex;

        justify-content: center;
    }

    .exporters-pagination .pagination {
        display: flex;

        align-items: center;
        justify-content: center;

        gap: 6px;

        margin: 0;
    }

    .exporters-pagination .page-item .page-link {
        min-width: 40px;
        height: 40px;

        display: flex;

        align-items: center;
        justify-content: center;

        border: 1px solid #e2e8f0;

        border-radius: 9px !important;

        background: #ffffff;

        color: #526174;

        font-size: 13px;
        font-weight: 700;

        box-shadow: 0 3px 12px rgba(31, 45, 61, 0.04);

        transition:
            background 0.2s ease,
            color 0.2s ease,
            border-color 0.2s ease,
            transform 0.2s ease;
    }

    .exporters-pagination .page-item .page-link:hover {
        background: #f2f8f5;

        border-color: #198754;

        color: #198754;

        transform: translateY(-1px);
    }

    .exporters-pagination .page-item.active .page-link {
        background: #198754;

        border-color: #198754;

        color: #ffffff;

        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
    }

    .exporters-pagination .page-item.disabled .page-link {
        background: #f8fafc;

        border-color: #edf0f4;

        color: #c2c9d2;

        box-shadow: none;
    }

    .exporters-pagination.hidden {
        display: none;
    }


    /* ---------------------------------------------------------
       RESPONSIVE
    --------------------------------------------------------- */

    @media (max-width: 767px) {

        .exporters-page {
            padding: 50px 0 60px;
        }

        .exporters-header {
            margin-bottom: 35px;
        }

        .exporters-title {
            font-size: 32px;
        }

        .exporters-description {
            font-size: 14px;
        }

        .member-photo-wrapper {
            height: 270px;
        }

        .member-body {
            padding: 21px;
        }

        .member-name {
            font-size: 20px;
        }
    }


    @media (max-width: 575px) {

        .exporters-search {
            margin-bottom: 20px;
        }

        .search-wrapper {
            padding: 5px;
        }

        .search-icon {
            width: 38px;
        }

        .search-input {
            font-size: 13px;
        }

        .exporters-pagination {
            margin-top: 40px;
        }

        .exporters-pagination .pagination {
            gap: 4px;
        }

        .exporters-pagination .page-item .page-link {
            min-width: 36px;
            height: 36px;

            font-size: 12px;
        }
    }
</style>


<section class="exporters-page">

    <div class="container">


        {{-- =====================================================
             PAGE HEADER
        ====================================================== --}}

        <div class="exporters-header">

            <div class="exporters-eyebrow">
                NACPDEAN Directory
            </div>

            <h1 class="exporters-title">

                Registered <span>Exporters</span>

            </h1>

            <p class="exporters-description">

                Explore approved exporters registered with NACPDEAN.
                All members displayed in this directory have been
                approved by the association.

            </p>

        </div>


        {{-- =====================================================
             ONLY SHOW SEARCH, COUNT, RESULTS AND MEMBERS
             WHEN EXPORTERS EXIST
        ====================================================== --}}

        @if($exporters->count() > 0)


            {{-- =================================================
                 LIVE SEARCH
            ================================================== --}}

            <div class="exporters-search">

                <form
                    id="exporterSearchForm"
                    onsubmit="return false;"
                >

                    <div class="search-wrapper">

                        <div class="search-icon">

                            <i class="bi bi-search"></i>

                        </div>


                        <input
                            type="search"
                            class="search-input"
                            id="exporterSearchInput"
                            placeholder="Search by name, membership number or business..."
                            autocomplete="off"
                            aria-label="Search registered exporters"
                        >


                        <button
                            type="button"
                            class="search-clear"
                            id="exporterSearchClear"
                            aria-label="Clear search"
                            title="Clear search"
                        >

                            <i class="bi bi-x-circle-fill"></i>

                        </button>

                    </div>

                </form>

            </div>


            {{-- =================================================
                 RESULT SUMMARY
            ================================================== --}}

            <div class="exporters-summary">

                <div class="exporters-count">

                    <strong id="exporterCount">

                        {{ $exporters->count() }}

                    </strong>

                    <span id="exporterCountLabel">

                        {{ $exporters->count() === 1
                            ? 'Registered Exporter'
                            : 'Registered Exporters'
                        }}

                    </span>

                </div>

            </div>


            {{-- =================================================
                 CURRENT RESULTS
            ================================================== --}}

            <div
                class="exporters-results"
                id="exportersResults"
            >

                Showing

                <strong id="visibleExporterCount">

                    {{ $exporters->count() }}

                </strong>

                of

                <strong>

                    {{ $exporters->count() }}

                </strong>

                exporters

            </div>


            {{-- =================================================
                 EXPORTER GRID
            ================================================== --}}

            <div
                class="row g-4"
                id="exportersGrid"
            >

                @foreach ($exporters as $exporter)

                    @php

                        $fullName = collect([
                            $exporter->first_name,
                            $exporter->middle_name,
                            $exporter->surname
                        ])->filter()->implode(' ');

                    @endphp


                    <div
                        class="col-xl-4 col-lg-4 col-md-6 exporter-card-column"
                        data-search="{{ strtolower(
                            $fullName . ' ' .
                            ($exporter->membership_number ?? '') . ' ' .
                            ($exporter->business_name ?? '')
                        ) }}"
                    >

                        <article class="member-card">


                            {{-- =================================================
                                 MEMBER PHOTO
                            ================================================== --}}

                            <div class="member-photo-wrapper">

                                @if ($exporter->photo)

                                    <img
                                        class="member-photo"
                                        src="{{ asset('uploads/member_profiles/' . $exporter->photo) }}"
                                        alt="{{ $fullName }}"
                                        loading="lazy"
                                        decoding="async"
                                    >

                                @else

                                    <img
                                        class="member-photo"
                                        src="{{ asset('assets/img/default-member.jpg') }}"
                                        alt="NACPDEAN member"
                                        loading="lazy"
                                        decoding="async"
                                    >

                                @endif


                                {{-- VERIFIED BADGE --}}

                                <div class="member-status">

                                    <i class="bi bi-patch-check-fill"></i>

                                    Verified

                                </div>

                            </div>


                            {{-- =================================================
                                 MEMBER INFORMATION
                            ================================================== --}}

                            <div class="member-body">


                                {{-- CATEGORY --}}

                                <span class="member-badge">

                                    {{ $exporter->membershipCategory?->name ?? 'Exporter' }}

                                </span>


                                {{-- NAME --}}

                                <h2 class="member-name">

                                    {{ $fullName }}

                                </h2>


                                {{-- POSITION --}}

                                <p class="member-position">

                                    Registered Exporter

                                </p>


                                <div class="member-info">


                                    {{-- MEMBERSHIP NUMBER --}}

                                    <div class="member-info-row">

                                        <div class="member-info-icon">

                                            <i class="bi bi-person-vcard"></i>

                                        </div>

                                        <div class="member-info-content">

                                            <span class="member-info-label">

                                                Membership Number

                                            </span>

                                            <span class="member-info-value">

                                                {{ $exporter->membership_number ?: 'Not assigned' }}

                                            </span>

                                        </div>

                                    </div>


                                    {{-- BUSINESS --}}

                                    <div class="member-info-row">

                                        <div class="member-info-icon">

                                            <i class="bi bi-building"></i>

                                        </div>

                                        <div class="member-info-content">

                                            <span class="member-info-label">

                                                Business

                                            </span>

                                            <span class="member-info-value">

                                                {{ $exporter->business_name ?: 'Not provided' }}

                                            </span>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </article>

                    </div>

                @endforeach

            </div>


            {{-- =================================================
                 LIVE SEARCH EMPTY STATE
            ================================================== --}}

            <div
                class="live-search-empty"
                id="liveSearchEmpty"
            >

                <div class="live-search-empty-icon">

                    <i class="bi bi-search"></i>

                </div>

                <h4>

                    No Exporter Found

                </h4>

                <p>

                    No registered exporter matches your search.
                    Try another name, membership number or business name.

                </p>

            </div>


            {{-- =================================================
                 PAGINATION
            ================================================== --}}

            @if($exporters->hasPages())

                <div
                    class="exporters-pagination"
                    id="exportersPagination"
                >

                    {{ $exporters
                        ->onEachSide(1)
                        ->links('pagination::bootstrap-5')
                    }}

                </div>

            @endif


        @else


            {{-- =================================================
                 NO EXPORTERS IN DATABASE
            ================================================== --}}

            <div class="exporters-empty">

                <div class="exporters-empty-icon">

                    <i class="bi bi-people"></i>

                </div>

                <h4>

                    No Registered Exporters

                </h4>

                <p>

                    There are currently no approved exporters
                    available in the NACPDEAN directory.

                </p>

            </div>


        @endif


    </div>

</section>


{{-- =============================================================
     LIVE SEARCH

     Only load the JavaScript when exporters actually exist.
============================================================= --}}

@if($exporters->count() > 0)

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const searchInput = document.getElementById(
            'exporterSearchInput'
        );

        const searchClear = document.getElementById(
            'exporterSearchClear'
        );

        const exporterCards = document.querySelectorAll(
            '.exporter-card-column'
        );

        const exporterCount = document.getElementById(
            'exporterCount'
        );

        const exporterCountLabel = document.getElementById(
            'exporterCountLabel'
        );

        const visibleExporterCount = document.getElementById(
            'visibleExporterCount'
        );

        const liveSearchEmpty = document.getElementById(
            'liveSearchEmpty'
        );

        const exportersResults = document.getElementById(
            'exportersResults'
        );

        const exportersPagination = document.getElementById(
            'exportersPagination'
        );


        /*
         * Total number of exporters currently
         * loaded on this page.
         */
        const totalExporters = exporterCards.length;


        /*
         * Perform live search.
         */
        function performSearch() {

            const searchTerm = searchInput.value
                .toLowerCase()
                .trim();

            let visibleCount = 0;


            exporterCards.forEach(function (card) {

                const searchableText =
                    card.dataset.search || '';


                const matches =
                    searchTerm === '' ||
                    searchableText.includes(searchTerm);


                if (matches) {

                    card.classList.remove('hidden');

                    visibleCount++;

                } else {

                    card.classList.add('hidden');

                }

            });


            /*
             * Update count.
             */
            exporterCount.textContent =
                visibleCount;


            visibleExporterCount.textContent =
                visibleCount;


            /*
             * Update singular/plural text.
             */
            exporterCountLabel.textContent =
                visibleCount === 1
                    ? 'Registered Exporter'
                    : 'Registered Exporters';


            /*
             * Show/hide clear button.
             */
            if (searchTerm !== '') {

                searchClear.classList.add(
                    'visible'
                );

            } else {

                searchClear.classList.remove(
                    'visible'
                );

            }


            /*
             * Show empty state when no exporter
             * matches the search.
             */
            if (
                searchTerm !== '' &&
                visibleCount === 0
            ) {

                liveSearchEmpty.classList.add(
                    'visible'
                );

                exportersResults.style.display =
                    'none';

            } else {

                liveSearchEmpty.classList.remove(
                    'visible'
                );

                exportersResults.style.display =
                    'block';

            }


            /*
             * Update result text.
             */
            if (searchTerm !== '') {

                exportersResults.innerHTML =
                    'Showing <strong>' +
                    visibleCount +
                    '</strong> matching ' +
                    (
                        visibleCount === 1
                            ? 'exporter'
                            : 'exporters'
                    );

            } else {

                exportersResults.innerHTML =
                    'Showing <strong>' +
                    totalExporters +
                    '</strong> of <strong>' +
                    totalExporters +
                    '</strong> exporters';

            }


            /*
             * Hide Laravel pagination while
             * searching.
             */
            if (exportersPagination) {

                if (searchTerm !== '') {

                    exportersPagination.classList.add(
                        'hidden'
                    );

                } else {

                    exportersPagination.classList.remove(
                        'hidden'
                    );

                }

            }

        }


        /*
         * Search whenever the user types.
         */
        searchInput.addEventListener(
            'input',
            performSearch
        );


        /*
         * Clear search.
         */
        searchClear.addEventListener(
            'click',
            function () {

                searchInput.value = '';

                searchInput.focus();

                performSearch();

            }
        );

    });

</script>

@endif

@endsection
