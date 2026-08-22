@extends('member.member_dashboard')

@section('member')

@section('title')
    NACPDEAN - Application Status
@endsection

<div class="container py-4">

    <div class="row justify-content-center">

        <div class="col-lg-9">

            {{-- =====================================================
                SUCCESS MESSAGE
            ====================================================== --}}

            @if (session('success'))

                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}
                </div>

            @endif


            {{-- =====================================================
                ERROR MESSAGE
            ====================================================== --}}

            @if (session('error'))

                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ session('error') }}
                </div>

            @endif


            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h3 class="mb-1">
                        Application Status
                    </h3>

                    <p class="text-muted mb-0">
                        Track the progress of your membership application.
                    </p>

                </div>

            </div>


            {{-- =====================================================
                APPLICATION STATUS CARD
            ====================================================== --}}

            <div class="card shadow-sm mb-4">

                <div class="card-body text-center py-5">

                    {{-- DRAFT --}}

                    @if ($profile->status === 'draft')

                        <div class="mb-3">

                            <i class="fas fa-file-alt fa-3x text-warning"></i>

                        </div>

                        <h4>
                            Application Not Yet Submitted
                        </h4>

                        <span class="badge bg-warning text-dark fs-6">
                            Draft
                        </span>

                        <p class="text-muted mt-3">
                            Your profile has been saved, but your membership
                            application has not yet been submitted.
                        </p>

                        <a
                            href="{{ route('member.profile') }}"
                            class="btn btn-primary mt-2"
                        >

                            <i class="fas fa-user-edit me-1"></i>

                            Complete Profile

                        </a>


                    {{-- SUBMITTED --}}

                    @elseif ($profile->status === 'submitted')

                        <div class="mb-3">

                            <i class="fas fa-clock fa-3x text-info"></i>

                        </div>

                        <h4>
                            Application Under Review
                        </h4>

                        <span class="badge bg-info fs-6">
                            Submitted
                        </span>

                        <p class="text-muted mt-3 mb-1">
                            Your membership application has been successfully
                            submitted and is currently awaiting administrator
                            approval.
                        </p>

                        @if ($profile->submitted_at)

                            <small class="text-muted">

                                Submitted on
                                <strong>
                                    {{ $profile->submitted_at->format('d M Y, h:i A') }}
                                </strong>

                            </small>

                        @endif


                    {{-- APPROVED --}}

                    @elseif ($profile->status === 'approved')

                        <div class="mb-3">

                            <i class="fas fa-check-circle fa-3x text-success"></i>

                        </div>

                        <h4>
                            Application Approved
                        </h4>

                        <span class="badge bg-success fs-6">
                            Approved
                        </span>

                        <p class="text-success mt-3 mb-1">
                            Congratulations! Your membership application
                            has been approved.
                        </p>

                        @if ($profile->approved_at)

                            <small class="text-muted">

                                Approved on
                                <strong>
                                    {{ $profile->approved_at->format('d M Y, h:i A') }}
                                </strong>

                            </small>

                        @endif


                    {{-- REJECTED --}}

                    @elseif ($profile->status === 'rejected')

                        <div class="mb-3">

                            <i class="fas fa-times-circle fa-3x text-danger"></i>

                        </div>

                        <h4>
                            Application Rejected
                        </h4>

                        <span class="badge bg-danger fs-6">
                            Rejected
                        </span>

                        <p class="text-danger mt-3 mb-0">
                            Your application requires correction before
                            it can be approved.
                        </p>

                    @endif

                </div>

            </div>


            {{-- =====================================================
                APPLICATION INFORMATION
            ====================================================== --}}

            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <strong>
                        <i class="fas fa-info-circle me-1"></i>
                        Application Information
                    </strong>

                </div>


                <div class="card-body">

                    <div class="row">


                        {{-- STATUS --}}

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Application Status
                            </small>

                            @if ($profile->status === 'draft')

                                <span class="badge bg-warning text-dark">
                                    Draft
                                </span>

                            @elseif ($profile->status === 'submitted')

                                <span class="badge bg-info">
                                    Submitted
                                </span>

                            @elseif ($profile->status === 'approved')

                                <span class="badge bg-success">
                                    Approved
                                </span>

                            @elseif ($profile->status === 'rejected')

                                <span class="badge bg-danger">
                                    Rejected
                                </span>

                            @endif

                        </div>


                        {{-- SUBMITTED DATE --}}

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Submitted Date
                            </small>

                            <strong>

                                @if ($profile->submitted_at)

                                    {{ $profile->submitted_at->format('d M Y, h:i A') }}

                                @else

                                    Not submitted

                                @endif

                            </strong>

                        </div>


                        {{-- APPROVED DATE --}}

                        @if ($profile->approved_at)

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Approval Date
                                </small>

                                <strong>

                                    {{ $profile->approved_at->format('d M Y, h:i A') }}

                                </strong>

                            </div>

                        @endif


                        {{-- MEMBERSHIP NUMBER --}}

                        @if ($profile->membership_number)

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Membership Number
                                </small>

                                <strong class="text-success">

                                    {{ $profile->membership_number }}

                                </strong>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            {{-- =====================================================
                ADMIN COMMENT
            ====================================================== --}}

            @if ($profile->status === 'rejected' && $profile->admin_comment)

                <div class="card shadow-sm border-danger mb-4">

                    <div class="card-header text-danger">

                        <strong>

                            <i class="fas fa-comment-alt me-1"></i>

                            Administrator Comment

                        </strong>

                    </div>

                    <div class="card-body">

                        <p class="mb-0">
                            {{ $profile->admin_comment }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- =====================================================
                REJECTION REASON
            ====================================================== --}}

            @if ($profile->status === 'rejected' && $profile->rejection_reason)

                <div class="card shadow-sm border-danger mb-4">

                    <div class="card-header text-danger">

                        <strong>

                            <i class="fas fa-exclamation-triangle me-1"></i>

                            Rejection Reason

                        </strong>

                    </div>

                    <div class="card-body">

                        <p class="mb-0">
                            {{ $profile->rejection_reason }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- =====================================================
                WHAT TO DO NEXT
            ====================================================== --}}

            <div class="card shadow-sm">

                <div class="card-header">

                    <strong>
                        What happens next?
                    </strong>

                </div>

                <div class="card-body">


                    {{-- DRAFT --}}

                    @if ($profile->status === 'draft')

                        <p class="mb-0">

                            <i class="fas fa-arrow-right text-primary me-2"></i>

                            Complete all required profile information,
                            save your profile and submit your application
                            for administrator review.

                        </p>


                    {{-- SUBMITTED --}}

                    @elseif ($profile->status === 'submitted')

                        <p class="mb-0">

                            <i class="fas fa-clock text-info me-2"></i>

                            Your application is currently being reviewed.
                            Please wait for the administrator to approve
                            or reject your application.

                        </p>


                    {{-- APPROVED --}}

                    @elseif ($profile->status === 'approved')

                        <p class="mb-0">

                            <i class="fas fa-check text-success me-2"></i>

                            Your application has been approved. You can now
                            access the full membership services available
                            on your dashboard.

                        </p>


                    {{-- REJECTED --}}

                    @elseif ($profile->status === 'rejected')

                        <p class="mb-3">

                            <i class="fas fa-edit text-danger me-2"></i>

                            Please review the administrator's comments,
                            correct your profile information and submit
                            your application again.

                        </p>


                        <a
                            href="{{ route('member.profile') }}"
                            class="btn btn-danger"
                        >

                            <i class="fas fa-edit me-1"></i>

                            Edit Profile

                        </a>

                    @endif

                </div>

            </div>


        </div>

    </div>

</div>

@endsection
