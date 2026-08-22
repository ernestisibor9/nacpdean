@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')

{{-- ============================================================
    PAYMENT PAGE STYLES
============================================================ --}}

<style>
    .nacp-payment-page {
        font-size: 16px;
    }

    .nacp-payment-page h3 {
        font-size: 1.75rem;
    }

    .nacp-payment-page h5 {
        font-size: 1.25rem;
    }

    .nacp-payment-page h6 {
        font-size: 1.05rem;
    }

    .nacp-payment-page p {
        font-size: 1rem;
    }

    .nacp-payment-page .form-label {
        font-size: 1rem;
    }

    .nacp-payment-page .form-control,
    .nacp-payment-page .form-select {
        font-size: 1rem;
        min-height: 46px;
    }

    .nacp-payment-page .form-control {
        padding: 0.65rem 0.8rem;
    }

    .nacp-payment-page .form-select {
        padding: 0.65rem 2.25rem 0.65rem 0.8rem;
    }

    .nacp-payment-page .text-muted {
        font-size: 0.95rem;
    }

    .nacp-payment-page .small {
        font-size: 0.9rem !important;
    }

    .nacp-payment-page .alert {
        font-size: 1rem;
    }

    .nacp-payment-page .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.75em;
    }

    .nacp-payment-page .btn {
        font-size: 1rem;
        padding: 0.65rem 1.1rem;
    }

    .nacp-payment-page .membership-value {
        font-size: 1.05rem;
    }

    .nacp-payment-page .payment-amount {
        font-size: 1.3rem !important;
    }

    .nacp-payment-page .fee-description {
        font-size: 1.05rem;
    }

    .nacp-payment-page .info-label {
        font-size: 0.95rem;
    }

    .nacp-payment-page .info-value {
        font-size: 1.05rem;
    }

    .nacp-payment-page .empty-state-title {
        font-size: 1.1rem;
    }

    @media (max-width: 767.98px) {

        .nacp-payment-page {
            font-size: 15px;
        }

        .nacp-payment-page h3 {
            font-size: 1.5rem;
        }

        .nacp-payment-page h5 {
            font-size: 1.15rem;
        }

        .nacp-payment-page .btn {
            font-size: 0.95rem;
        }
    }
</style>


<div class="container-fluid py-4 nacp-payment-page">

    {{-- ============================================================
        DETERMINE MEMBERSHIP APPROVAL FROM DATABASE
        member_profiles.status MUST BE "approved"
    ============================================================ --}}

    @php

        $membershipStatus = strtolower(trim($profile->status ?? ''));

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | A member is considered APPROVED ONLY when the admin has changed
        | member_profiles.status to "approved".
        |
        | submitted = waiting for admin approval
        | draft     = application not submitted
        | approved  = admin approved the application
        |--------------------------------------------------------------------------
        */

        $isApproved = $membershipStatus === 'approved';

    @endphp


    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}

    <div class="mb-4">

        <h3 class="fw-bold mb-1">
            Payments
        </h3>

        <p class="text-muted mb-0">
            Manage your NACPDEAN membership payments and other payment
            obligations.
        </p>

    </div>


    {{-- ============================================================
        ERRORS
    ============================================================ --}}

    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle me-1"></i>

            <strong>
                Payment Error
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- ============================================================
        SUCCESS
    ============================================================ --}}

    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- ============================================================
        FIRST-TIME MEMBER
    ============================================================ --}}

    @if (!$currentCategory)

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Membership Registration
                </h5>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('payment.initialize') }}"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="payment_option"
                        value="membership"
                    >


                    {{-- ====================================================
                        MEMBER TYPE
                    ==================================================== --}}

                    <div class="mb-4">

                        <label class="form-label fw-bold">
                            Member Type
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ ucfirst($memberType) }}"
                            readonly
                        >

                        <small class="text-muted">
                            Your member type was selected during registration.
                        </small>

                    </div>


                    {{-- ====================================================
                        MEMBERSHIP CATEGORY
                    ==================================================== --}}

                    <div class="mb-4">

                        <label
                            for="membership_category_id"
                            class="form-label fw-bold"
                        >
                            Membership Category
                        </label>

                        <select
                            name="membership_category_id"
                            id="membership_category_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Select Membership Category --
                            </option>

                            @foreach ($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ old('membership_category_id') == $category->id ? 'selected' : '' }}
                                >

                                    {{ $category->name }}
                                    ({{ $category->code }})

                                </option>

                            @endforeach

                        </select>


                        @error('membership_category_id')

                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ====================================================
                        INFORMATION
                    ==================================================== --}}

                    <div class="alert alert-info">

                        <i class="fas fa-info-circle me-2"></i>

                        Your membership category is based on the
                        member type you selected during registration.

                    </div>


                    {{-- ====================================================
                        PAYMENT BUTTON
                    ==================================================== --}}

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >

                        <i class="fas fa-lock me-2"></i>

                        Continue to Payment

                    </button>

                </form>

            </div>

        </div>


    {{-- ============================================================
        EXISTING MEMBER
    ============================================================ --}}

    @else


        {{-- ========================================================
            ANNUAL MEMBERSHIP
        ======================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="fw-bold mb-0">
                        Annual Membership
                    </h5>


                    {{-- ====================================================
                        MEMBERSHIP STATUS
                    ==================================================== --}}

                    @if ($hasActiveAnnualMembership)

                        {{-- ACTIVE PAYMENT --}}

                        <span class="badge bg-success">
                            Active
                        </span>


                    @elseif ($isApproved)

                        {{-- ADMIN HAS APPROVED THE MEMBER --}}

                        <span class="badge bg-success">
                            Approved
                        </span>


                    @else

                        {{-- ADMIN HAS NOT APPROVED THE MEMBER --}}

                        <span class="badge bg-warning text-dark">

                            {{ ucfirst($membershipStatus ?: 'Pending') }}

                        </span>

                    @endif

                </div>

            </div>


            <div class="card-body">


                {{-- ====================================================
                    CATEGORY + MEMBER TYPE
                ==================================================== --}}

                <div class="row mb-4">

                    <div class="col-md-6 mb-3 mb-md-0">

                        <span class="text-muted d-block info-label">
                            Membership Category
                        </span>

                        <strong class="info-value">

                            {{ $currentCategory->name }}
                            ({{ $currentCategory->code }})

                        </strong>

                    </div>


                    <div class="col-md-6">

                        <span class="text-muted d-block info-label">
                            Membership Type
                        </span>

                        <strong class="info-value">
                            {{ ucfirst($memberType) }}
                        </strong>

                    </div>

                </div>


                {{-- ====================================================
                    ADMIN APPROVAL STATUS
                ==================================================== --}}

                @if (!$isApproved)

                    <div class="alert alert-warning mb-4">

                        <div class="d-flex align-items-start">

                            <i class="fas fa-clock me-3 mt-1"></i>

                            <div>

                                <strong>
                                    Membership Approval Pending
                                </strong>

                                <div class="mt-1">

                                    Your membership application has not
                                    yet been approved by the administrator.

                                    Your membership will only become
                                    <strong>Approved</strong> after the
                                    administrator reviews and approves
                                    your application.

                                </div>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ====================================================
                    ANNUAL MEMBERSHIP IS ACTIVE
                ==================================================== --}}

                @if ($hasActiveAnnualMembership)

                    <div class="alert alert-success mb-0">

                        <i class="fas fa-check-circle me-2"></i>

                        Your annual membership is currently active.

                        @if ($profile->membership_expires_at ?? false)

                            Your membership expires on

                            <strong>

                                {{ \Carbon\Carbon::parse($profile->membership_expires_at)->format('d M Y') }}

                            </strong>.

                        @endif

                    </div>


                {{-- ====================================================
                    APPROVED BUT ANNUAL MEMBERSHIP NOT PAID
                ==================================================== --}}

                @elseif ($isApproved)

                    <div class="alert alert-success mb-3">

                        <i class="fas fa-check-circle me-2"></i>

                        Your membership application has been
                        <strong>approved by the administrator</strong>.

                    </div>


                    {{-- ====================================================
                        MEMBERSHIP PAYMENT
                    ==================================================== --}}

                    @if ($membershipFee)

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                            <div>

                                <span class="text-muted d-block">
                                    Annual Membership Fee
                                </span>

                                <strong class="payment-amount">

                                    ₦{{ number_format($membershipFee->amount, 2) }}

                                </strong>

                            </div>


                            <form
                                method="POST"
                                action="{{ route('payment.initialize') }}"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="payment_option"
                                    value="membership"
                                >


                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >

                                    <i class="fas fa-lock me-1"></i>

                                    Pay Annual Membership

                                </button>

                            </form>

                        </div>

                    @endif


                {{-- ====================================================
                    NOT APPROVED
                ==================================================== --}}

                @else

                    <div class="alert alert-info mb-0">

                        <i class="fas fa-info-circle me-2"></i>

                        Your membership application is currently awaiting
                        admin approval.

                    </div>

                @endif

            </div>

        </div>


        {{-- ========================================================
            ADDITIONAL PAYMENTS
            ONLY AVAILABLE AFTER ADMIN APPROVAL
        ======================================================== --}}

        @if ($isApproved)

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <h5 class="fw-bold mb-1">
                        Other Payments
                    </h5>

                    <p class="text-muted mb-0">
                        Pay for certificates, penalties and other
                        charges assigned to your membership account.
                    </p>

                </div>


                <div class="card-body">

                    @if ($memberFees->count())

                        @foreach ($memberFees as $memberFee)

                            <div class="card border mb-3">

                                <div class="card-body">

                                    <div class="row align-items-center">

                                        {{-- ====================================================
                                            FEE INFORMATION
                                        ==================================================== --}}

                                        <div class="col-md-7">

                                            <div class="d-flex align-items-center">

                                                <div
                                                    class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                                    style="
                                                        width:50px;
                                                        height:50px;
                                                    "
                                                >

                                                    @if (
                                                        strtolower($memberFee->fee_type ?? '') === 'certificate'
                                                    )

                                                        <i class="fas fa-certificate fa-lg text-primary"></i>

                                                    @elseif (
                                                        strtolower($memberFee->fee_type ?? '') === 'penalty'
                                                    )

                                                        <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>

                                                    @elseif (
                                                        strtolower($memberFee->fee_type ?? '') === 'id_card'
                                                        ||
                                                        strtolower($memberFee->fee_type ?? '') === 'id card'
                                                    )

                                                        <i class="fas fa-id-card fa-lg text-primary"></i>

                                                    @else

                                                        <i class="fas fa-file-invoice-dollar fa-lg text-primary"></i>

                                                    @endif

                                                </div>


                                                <div>

                                                    <h6 class="fw-bold mb-1 fee-description">

                                                        {{ $memberFee->description ?? 'Additional Fee' }}

                                                    </h6>


                                                    @if ($memberFee->fee_type)

                                                        <span class="badge bg-light text-dark">

                                                            {{ ucfirst(str_replace('_', ' ', $memberFee->fee_type)) }}

                                                        </span>

                                                    @endif


                                                    @if ($memberFee->due_date)

                                                        <div class="text-muted mt-1">

                                                            <i class="fas fa-calendar-alt me-1"></i>

                                                            Due:

                                                            {{ \Carbon\Carbon::parse($memberFee->due_date)->format('d M Y') }}

                                                        </div>

                                                    @endif

                                                </div>

                                            </div>

                                        </div>


                                        {{-- ====================================================
                                            AMOUNT + PAYMENT
                                        ==================================================== --}}

                                        <div class="col-md-5 text-md-end mt-3 mt-md-0">

                                            <div class="fw-bold payment-amount mb-2">

                                                ₦{{ number_format($memberFee->amount, 2) }}

                                            </div>


                                            <form
                                                method="POST"
                                                action="{{ route('payment.initialize') }}"
                                            >

                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="payment_option"
                                                    value="member_fee_{{ $memberFee->id }}"
                                                >


                                                <button
                                                    type="submit"
                                                    class="btn btn-primary"
                                                >

                                                    <i class="fas fa-lock me-1"></i>

                                                    Pay Now

                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @endforeach


                    @else

                        {{-- ====================================================
                            NO ADDITIONAL FEES
                        ==================================================== --}}

                        <div class="text-center py-4">

                            <div
                                class="mb-3 mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center"
                                style="
                                    width:70px;
                                    height:70px;
                                "
                            >

                                <i class="fas fa-check-circle fa-2x text-success"></i>

                            </div>


                            <h6 class="fw-bold empty-state-title">
                                No Outstanding Payments
                            </h6>


                            <p class="text-muted mb-0">

                                You currently have no additional fees
                                available for payment.

                            </p>

                        </div>

                    @endif

                </div>

            </div>


        @else

            {{-- ====================================================
                ADDITIONAL PAYMENTS NOT YET AVAILABLE
                BECAUSE ADMIN HAS NOT APPROVED MEMBER
            ==================================================== --}}

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="alert alert-info mb-0">

                        <i class="fas fa-info-circle me-2"></i>

                        Additional payments such as certificates,
                        penalties and other membership charges will
                        become available after your membership
                        application has been approved by the
                        administrator.

                    </div>

                </div>

            </div>

        @endif

    @endif

</div>

@endsection
