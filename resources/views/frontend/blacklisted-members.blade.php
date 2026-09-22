@extends('frontend.master')

@section('home')


<h1 class="display-5 fw-bold text-white">
    Blacklisted Stakeholders Directory
</h1>

<p class="text-white">
    Suspended, blacklisted, and caution-listed stakeholders.
</p>


</div>

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
                                src="{{ asset('uploads/blacklisted_photos/' . basename($member->photo)) }}"
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
                                ? asset('uploads/blacklisted_photos/' . basename($member->photo))
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

@if($blacklistedMembers->count() > 0)

@endif

@endsection
