@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Member Application Details
@endsection

    <div class="container-fluid px-4">

        <h1 class="mt-4">
            Member Application
        </h1>


        <ol class="breadcrumb mb-4">

            <li class="breadcrumb-item">
                <a href="{{ route('admin.admin_dashboard') }}">
                    Dashboard
                </a>
            </li>

            <li class="breadcrumb-item">
                <a href="{{ route('admin.members.index') }}">
                    Member Applications
                </a>
            </li>

            <li class="breadcrumb-item active">
                Application Details
            </li>

        </ol>


        {{-- SUCCESS --}}

        @if (session('success'))
            <div class="alert alert-success">

                <i class="fas fa-check-circle"></i>

                {{ session('success') }}

            </div>
        @endif


        {{-- ERROR --}}

        @if (session('error'))
            <div class="alert alert-danger">

                <i class="fas fa-exclamation-circle"></i>

                {{ session('error') }}

            </div>
        @endif


        <div class="row">


            {{-- =========================================================
             LEFT SIDE
        ========================================================== --}}

            <div class="col-lg-8">


                {{-- =====================================================
                 PERSONAL INFORMATION
            ====================================================== --}}

                <div class="card mb-4">

                    <div class="card-header">

                        <i class="fas fa-user me-1"></i>

                        Personal Information

                    </div>


                    <div class="card-body">

                        <div class="row">


                            {{-- SURNAME --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    Surname
                                </label>

                                <div>
                                    {{ $application->surname ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- FIRST NAME --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    First Name
                                </label>

                                <div>
                                    {{ $application->first_name ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- MIDDLE NAME --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    Middle Name
                                </label>

                                <div>
                                    {{ $application->middle_name ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- EMAIL --}}

                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Email
                                </label>

                                <div>
                                    {{ $application->user->email ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- PHONE --}}

                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Phone Number
                                </label>

                                <div>
                                    {{ $application->phone ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- DOB --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    Date of Birth
                                </label>

                                <div>

                                    {{ $application->date_of_birth ? $application->date_of_birth->format('d M Y') : 'N/A' }}

                                </div>

                            </div>


                            {{-- GENDER --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    Gender
                                </label>

                                <div>
                                    {{ $application->gender ?? 'N/A' }}
                                </div>

                            </div>


                            {{-- NATIONALITY --}}

                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    Nationality
                                </label>

                                <div>
                                    {{ $application->nationality ?? 'N/A' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =====================================================
                 ADDRESS INFORMATION
            ====================================================== --}}

                <div class="card mb-4">

                    <div class="card-header">

                        <i class="fas fa-map-marker-alt me-1"></i>

                        Address Information

                    </div>


                    <div class="card-body">

                        <div class="row">


                            <div class="col-md-12 mb-3">

                                <label class="fw-bold">
                                    Address
                                </label>

                                <div>
                                    {{ $application->address ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    City
                                </label>

                                <div>
                                    {{ $application->city ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    State
                                </label>

                                <div>
                                    {{ $application->state ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="fw-bold">
                                    LGA
                                </label>

                                <div>
                                    {{ $application->lga ?? 'N/A' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =====================================================
                 BUSINESS INFORMATION
            ====================================================== --}}

                <div class="card mb-4">

                    <div class="card-header">

                        <i class="fas fa-building me-1"></i>

                        Business Information

                    </div>


                    <div class="card-body">

                        <div class="row">


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Business Name
                                </label>

                                <div>
                                    {{ $application->business_name ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Business Registration Number
                                </label>

                                <div>
                                    {{ $application->business_registration_number ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Business Type
                                </label>

                                <div>
                                    {{ $application->business_type ?? 'N/A' }}
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="fw-bold">
                                    Business Address
                                </label>

                                <div>
                                    {{ $application->business_address ?? 'N/A' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =====================================================
                 PAYMENT
            ====================================================== --}}

                <div class="card mb-4">

                    <div class="card-header">

                        <i class="fas fa-credit-card me-1"></i>

                        Payment Information

                    </div>


                    <div class="card-body">

                        @php

                            $payment = $application->user->payments->first();

                        @endphp


                        @if ($payment)
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Status
                                    </label>

                                    <div>

                                        @if ($payment->status === 'paid')
                                            <span class="badge bg-success">
                                                PAID
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                PENDING
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                {{ strtoupper($payment->status) }}
                                            </span>
                                        @endif

                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Amount
                                    </label>

                                    <div>
                                        ₦{{ number_format($payment->amount, 2) }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Payment Reference
                                    </label>

                                    <div>
                                        {{ $payment->reference }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Payment Type
                                    </label>

                                    <div>
                                        {{ ucfirst($payment->payment_type ?? 'N/A') }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Fee Type
                                    </label>

                                    <div>
                                        {{ ucfirst($payment->fee_type ?? 'N/A') }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Gateway
                                    </label>

                                    <div>
                                        {{ ucfirst($payment->gateway ?? 'N/A') }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Transaction ID
                                    </label>

                                    <div>
                                        {{ $payment->gateway_transaction_id ?? 'N/A' }}
                                    </div>

                                </div>


                                <div class="col-md-6 mb-3">

                                    <label class="fw-bold">
                                        Paid At
                                    </label>

                                    <div>

                                        {{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : 'N/A' }}

                                    </div>

                                </div>

                            </div>
                        @else
                            <div class="alert alert-danger mb-0">

                                No payment record found.

                            </div>
                        @endif

                    </div>

                </div>

            </div>



            {{-- =========================================================
             RIGHT SIDE
        ========================================================== --}}

            <div class="col-lg-4">


                {{-- APPLICATION STATUS --}}

                <div class="card mb-4">

                    <div class="card-header">

                        <i class="fas fa-tasks me-1"></i>

                        Application Status

                    </div>


                    <div class="card-body text-center">


                        @if ($application->status === 'approved')
                            <span class="badge bg-success fs-6 p-2">
                                APPROVED
                            </span>
                        @elseif($application->status === 'rejected')
                            <span class="badge bg-danger fs-6 p-2">
                                REJECTED
                            </span>
                        @elseif($application->status === 'submitted')
                            <span class="badge bg-warning text-dark fs-6 p-2">
                                PENDING REVIEW
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6 p-2">
                                {{ strtoupper($application->status) }}
                            </span>
                        @endif


                    </div>

                </div>



                {{-- =====================================================
                 MEMBERSHIP NUMBER
            ====================================================== --}}

                @if ($application->membership_number)
                    <div class="card mb-4">

                        <div class="card-header">

                            <i class="fas fa-id-badge me-1"></i>

                            Membership Number

                        </div>

                        <div class="card-body text-center">

                            <h4>
                                {{ $application->membership_number }}
                            </h4>

                        </div>

                    </div>
                @endif



                {{-- =====================================================
                 REJECTION REASON
            ====================================================== --}}

                @if ($application->status === 'rejected')
                    <div class="card mb-4">

                        <div class="card-header text-danger">

                            Rejection Reason

                        </div>

                        <div class="card-body">

                            {{ $application->rejection_reason ?? 'No reason provided.' }}

                        </div>

                    </div>
                @endif



                {{-- =====================================================
                 APPROVAL ACTIONS
            ====================================================== --}}

                @if ($application->status !== 'approved')
                    <div class="card mb-4">

                        <div class="card-header">

                            <i class="fas fa-gavel me-1"></i>

                            Admin Decision

                        </div>


                        <div class="card-body">


                            {{-- APPROVE --}}

                            <form method="POST" action="{{ route('admin.members.approve', $application->id) }}"
                                class="mb-3">

                                @csrf

                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Are you sure you want to approve this application?')">

                                    <i class="fas fa-check"></i>

                                    Approve Application

                                </button>

                            </form>


                            {{-- REJECT --}}

                            <form method="POST" action="{{ route('admin.members.reject', $application->id) }}">

                                @csrf


                                <div class="mb-3">

                                    <label class="form-label fw-bold">
                                        Rejection Reason
                                    </label>

                                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Enter reason for rejection..."
                                        required></textarea>

                                </div>


                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Are you sure you want to reject this application?')">

                                    <i class="fas fa-times"></i>

                                    Reject Application

                                </button>

                            </form>

                        </div>

                    </div>
                @endif



                {{-- =====================================================
                 ADMIN COMMENT
            ====================================================== --}}

                @if ($application->admin_comment)
                    <div class="card mb-4">

                        <div class="card-header">

                            <i class="fas fa-comment me-1"></i>

                            Admin Comment

                        </div>

                        <div class="card-body">

                            {{ $application->admin_comment }}

                        </div>

                    </div>
                @endif



                {{-- BACK --}}

                <a href="{{ route('admin.members.index') }}" class="btn btn-secondary w-100">

                    <i class="fas fa-arrow-left"></i>

                    Back to Applications

                </a>

            </div>

        </div>

    </div>

@endsection
