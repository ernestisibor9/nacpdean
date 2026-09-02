@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')

<style>
    .payment-page {
        font-size: 15px;
    }

    .payment-header {
        margin-bottom: 25px;
    }

    .payment-header h3 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .payment-header p {
        color: #6c757d;
        margin-bottom: 0;
    }

    .payment-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .payment-card-header {
        background: #fff;
        border-bottom: 1px solid #eee;
        padding: 18px 22px;
    }

    .payment-card-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .payment-card-body {
        padding: 25px 22px;
    }

    .category-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px 18px;
        height: 100%;
    }

    .category-label {
        display: block;
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .category-name {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .payment-details {
        margin-bottom: 22px;
    }

    .payment-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .fee-label {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .fee-amount {
        font-size: 1.6rem;
        font-weight: 800;
    }

    .pay-btn {
        min-width: 180px;
        padding: 11px 20px;
        font-weight: 600;
        border-radius: 8px;
    }

    .status-badge {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 20px;
    }

    .payment-note {
        color: #6c757d;
        font-size: 13px;
        margin-top: 14px;
    }

    .additional-card {
        margin-top: 22px;
    }

    .additional-item {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .additional-item:last-child {
        margin-bottom: 0;
    }

    .additional-title {
        font-weight: 700;
        margin-bottom: 4px;
    }

    .additional-type {
        font-size: 12px;
        color: #6c757d;
    }

    .additional-amount {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .empty-payment {
        text-align: center;
        padding: 35px 20px;
    }

    .empty-payment i {
        font-size: 35px;
        margin-bottom: 12px;
    }

    .empty-payment h6 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .empty-payment p {
        color: #6c757d;
        margin-bottom: 0;
    }

    @media (max-width: 767px) {

        .payment-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .pay-btn {
            width: 100%;
        }

        .fee-amount {
            font-size: 1.4rem;
        }
    }
</style>


<div class="container-fluid py-4 payment-page">

    {{-- ============================================================
        USER INFORMATION
    ============================================================ --}}

    @php
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | MEMBER TYPE
        |--------------------------------------------------------------------------
        | Comes directly from users.member_type
        | Values: regular / affiliate
        |--------------------------------------------------------------------------
        */

        $memberType = $user->member_type ?? null;

        /*
        |--------------------------------------------------------------------------
        | DISPLAY MEMBER TYPE
        |--------------------------------------------------------------------------
        */

        $displayMemberType = $memberType
            ? ucfirst($memberType)
            : 'N/A';
    @endphp


    {{-- ============================================================
        HEADER
    ============================================================ --}}

    <div class="payment-header">

        <h3>
            Payments
        </h3>

        <p>
            Make your membership payment securely.
        </p>

    </div>


    {{-- ============================================================
        ERRORS
    ============================================================ --}}

    @if ($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle me-2"></i>

            {{ $errors->first() }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ============================================================
        SUCCESS
    ============================================================ --}}

    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ============================================================
        MEMBERSHIP PAYMENT
    ============================================================ --}}

    <div class="card payment-card">

        {{-- ========================================================
            CARD HEADER
        ======================================================== --}}

        <div class="payment-card-header">

            <div class="d-flex justify-content-between align-items-center">

                <h5>
                    Membership Payment
                </h5>


                @if ($hasActiveAnnualMembership)

                    <span class="badge bg-success status-badge">
                        Active
                    </span>

                @elseif ($isApproved)

                    <span class="badge bg-success status-badge">
                        Approved
                    </span>

                @else

                    <span class="badge bg-warning text-dark status-badge">
                        Payment Required
                    </span>

                @endif

            </div>

        </div>


        {{-- ========================================================
            CARD BODY
        ======================================================== --}}

        <div class="payment-card-body">


            {{-- ====================================================
                MEMBERSHIP CATEGORY
            ==================================================== --}}

            @if ($currentCategory)

                <div class="row g-3 payment-details">

                    {{-- =================================================
                        MEMBERSHIP CATEGORY
                    ================================================= --}}

                    <div class="col-md-6">

                        <div class="category-box">

                            <span class="category-label">
                                Registered Membership Category
                            </span>

                            <div class="category-name">

                                {{ $currentCategory->name }}

                                @if ($currentCategory->code)

                                    ({{ $currentCategory->code }})

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        MEMBER TYPE
                    ================================================= --}}

                    <div class="col-md-6">

                        <div class="category-box">

                            <span class="category-label">
                                Member Type
                            </span>

                            <div class="category-name">

                                {{ $displayMemberType }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ====================================================
                    ACTIVE MEMBERSHIP
                ==================================================== --}}

                @if ($hasActiveAnnualMembership)

                    <div class="alert alert-success mb-0">

                        <i class="fas fa-check-circle me-2"></i>

                        Your membership is active until

                        <strong>
                            {{ \Carbon\Carbon::parse($membership->expires_at)->format('d M Y') }}
                        </strong>.

                    </div>


                {{-- ====================================================
                    MEMBERSHIP PAYMENT
                    PAYMENT MUST BE AVAILABLE BEFORE APPROVAL
                ==================================================== --}}

                @elseif ($membershipFee)

                    <div class="payment-row">

                        <div>

                            <div class="fee-label">
                                Membership Fee
                            </div>

                            <div class="fee-amount">

                                ₦{{ number_format($membershipFee->amount, 2) }}

                            </div>

                        </div>


                        {{-- =================================================
                            PAY NOW
                        ================================================= --}}

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
                                class="btn btn-primary pay-btn"
                            >

                                <i class="fas fa-lock me-2"></i>

                                Pay Now

                            </button>

                        </form>

                    </div>


                    {{-- =================================================
                        PAYMENT NOTE
                    ================================================= --}}

                    @if (!$isApproved)

                        <div class="payment-note">

                            <i class="fas fa-info-circle me-1"></i>

                            Your application will be reviewed after payment.

                        </div>

                    @endif


                {{-- ====================================================
                    NO FEE
                ==================================================== --}}

                @else

                    <div class="alert alert-danger mb-0">

                        <i class="fas fa-exclamation-circle me-2"></i>

                        Membership fee is not available for your
                        registered category.

                    </div>

                @endif


            {{-- ========================================================
                NO CATEGORY
            ======================================================== --}}

            @else

                <div class="empty-payment">

                    <i class="fas fa-exclamation-circle text-danger"></i>

                    <h6>
                        Membership Category Not Found
                    </h6>

                    <p>
                        Your membership category could not be determined.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
        ADDITIONAL PAYMENTS
        ONLY AFTER ADMIN APPROVAL
    ============================================================ --}}

    @if ($isApproved)

        <div class="card payment-card additional-card">

            <div class="payment-card-header">

                <h5>
                    Other Payments
                </h5>

            </div>


            <div class="payment-card-body">

                @if ($memberFees->count())

                    @foreach ($memberFees as $memberFee)

                        <div class="additional-item">

                            <div class="row align-items-center">

                                {{-- ====================================
                                    FEE INFORMATION
                                ==================================== --}}

                                <div class="col-md-8">

                                    <div class="additional-title">

                                        {{ $memberFee->description ?? 'Additional Payment' }}

                                    </div>


                                    @if ($memberFee->fee_type)

                                        <div class="additional-type">

                                            {{ ucfirst(str_replace('_', ' ', $memberFee->fee_type)) }}

                                        </div>

                                    @endif


                                    @if ($memberFee->due_date)

                                        <div class="additional-type mt-1">

                                            Due:

                                            {{ \Carbon\Carbon::parse($memberFee->due_date)->format('d M Y') }}

                                        </div>

                                    @endif

                                </div>


                                {{-- ====================================
                                    AMOUNT + PAYMENT
                                ==================================== --}}

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                                    <div class="additional-amount">

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
                                            class="btn btn-primary btn-sm px-4"
                                        >

                                            <i class="fas fa-lock me-1"></i>

                                            Pay Now

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @endforeach


                @else

                    <div class="empty-payment">

                        <i class="fas fa-check-circle text-success"></i>

                        <h6>
                            No Outstanding Payments
                        </h6>

                        <p>
                            You have no additional payments at this time.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    @endif

</div>

@endsection
