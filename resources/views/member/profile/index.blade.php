@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Member Profile')

@section('member')

    <div class="container-fluid py-4">

        {{-- ============================================================
        PAGE HEADER
        ============================================================ --}}

        <div class="mb-4">

            <h3 class="fw-bold mb-1">
                Member Profile
            </h3>

            <p class="text-muted mb-0">
                Complete your profile information to proceed with your
                membership application.
            </p>

        </div>


        {{-- ============================================================
        SUCCESS MESSAGE
        ============================================================ --}}

        @if (session('success'))

            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>

        @endif


        {{-- ============================================================
        ERROR MESSAGE
        ============================================================ --}}

        @if (session('error'))

            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <i class="fas fa-exclamation-circle me-2"></i>

                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>

        @endif


        {{-- ============================================================
        VALIDATION ERRORS
        ============================================================ --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <strong>
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Please correct the following:
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ============================================================
        APPLICATION STATUS
        ============================================================ --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-center">

                    {{-- STATUS INFORMATION --}}

                    <div class="col-md-8">

                        <h5 class="fw-bold mb-2">
                            Application Status
                        </h5>


                        {{-- DRAFT --}}

                        @if ($profile->status === 'draft')

                            <p class="text-muted mb-0">

                                <i class="fas fa-edit me-1"></i>

                                Your application is currently a draft.

                                Please complete all required sections and
                                submit your application for review.

                            </p>


                        {{-- SUBMITTED --}}

                        @elseif ($profile->status === 'submitted')

                            <p class="text-warning mb-0">

                                <i class="fas fa-clock me-1"></i>

                                Your application has been submitted and is
                                currently awaiting administrative review.

                            </p>


                        {{-- APPROVED --}}

                        @elseif ($profile->status === 'approved')

                            <p class="text-success mb-0">

                                <i class="fas fa-check-circle me-1"></i>

                                Congratulations! Your membership application
                                has been approved.

                            </p>


                        {{-- REJECTED --}}

                        @elseif ($profile->status === 'rejected')

                            <p class="text-danger mb-2">

                                <i class="fas fa-times-circle me-1"></i>

                                Your membership application was not approved.

                            </p>

                            <p class="text-muted mb-0">

                                Please review the administrator's comments
                                below, make the necessary corrections and
                                resubmit your application.

                            </p>

                        @endif

                    </div>


                    {{-- STATUS BADGE --}}

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        @if ($profile->status === 'draft')

                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                <i class="fas fa-edit me-1"></i>
                                Draft
                            </span>

                        @elseif ($profile->status === 'submitted')

                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="fas fa-clock me-1"></i>
                                Awaiting Review
                            </span>

                        @elseif ($profile->status === 'approved')

                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>
                                Approved
                            </span>

                        @elseif ($profile->status === 'rejected')

                            <span class="badge bg-danger fs-6 px-3 py-2">
                                <i class="fas fa-times-circle me-1"></i>
                                Rejected
                            </span>

                        @endif

                    </div>

                </div>


                {{-- ====================================================
                REJECTION REASON
                ==================================================== --}}

                @if ($profile->status === 'rejected' && $profile->rejection_reason)

                    <div class="alert alert-danger mt-4 mb-0">

                        <div class="d-flex align-items-start">

                            <div class="me-3">

                                <i class="fas fa-comment-alt fa-lg"></i>

                            </div>

                            <div>

                                <h6 class="fw-bold mb-2">
                                    Administrator's Rejection Reason
                                </h6>

                                <p class="mb-0">
                                    {{ $profile->rejection_reason }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- ====================================================
                ADMIN COMMENT
                ==================================================== --}}

                @if ($profile->status === 'rejected' && $profile->admin_comment)

                    <div class="alert alert-warning mt-3 mb-0">

                        <div class="d-flex align-items-start">

                            <div class="me-3">

                                <i class="fas fa-comment-dots fa-lg"></i>

                            </div>

                            <div>

                                <h6 class="fw-bold mb-2">
                                    Administrator's Comment
                                </h6>

                                <p class="mb-0">
                                    {{ $profile->admin_comment }}
                                </p>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- ============================================================
        PERSONAL INFORMATION
        ============================================================ --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="fw-bold mb-0">
                    Personal Information
                </h5>

            </div>


            <div class="card-body">

                <form method="POST"
                    action="{{ route('member.profile.update') }}"
                    enctype="multipart/form-data">

                    @csrf

                    @method('PUT')

                    <input type="hidden"
                        name="section"
                        value="personal">


                    <div class="row g-3">


                        {{-- ====================================================
                        PASSPORT PHOTOGRAPH
                        ==================================================== --}}

                        <div class="col-12">

                            <div class="border rounded p-3 bg-light">

                                <div class="row align-items-center g-4">


                                    {{-- PHOTO UPLOAD --}}

                                    <div class="col-md-7">

                                        <label class="form-label fw-semibold">

                                            Passport Photograph

                                            @if (!$profile->photo)

                                                <span class="text-danger">
                                                    *
                                                </span>

                                            @endif

                                        </label>


                                        <input type="file"
                                            name="photo"
                                            id="photo"
                                            class="form-control"
                                            accept="image/jpeg,image/png,image/webp"
                                            {{ !$profile->photo ? 'required' : '' }}>


                                        <small class="text-muted d-block mt-2">

                                            JPG, JPEG, PNG or WEBP.
                                            Maximum size: 2MB.

                                        </small>


                                        @if ($profile->photo)

                                            <small class="text-success d-block mt-2">

                                                <i class="fas fa-check-circle me-1"></i>

                                                Passport photograph already uploaded.

                                            </small>

                                        @endif

                                    </div>


                                    {{-- PHOTO PREVIEW --}}

                                    <div class="col-md-5 text-center">

                                        <div class="mb-2">

                                            <span class="fw-semibold">
                                                Photograph Preview
                                            </span>

                                        </div>


                                        <div style="
                                            width: 150px;
                                            height: 180px;
                                            margin: 0 auto;
                                            border: 1px solid #dee2e6;
                                            border-radius: 8px;
                                            background: #ffffff;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            overflow: hidden;
                                        ">


                                            @if ($profile->photo)

                                                <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                                    id="photoPreview"
                                                    alt="Member Photograph"
                                                    style="
                                                        width: 100%;
                                                        height: 100%;
                                                        object-fit: cover;
                                                    ">

                                            @else

                                                <div id="photoPlaceholder"
                                                    class="text-muted text-center px-2">

                                                    <i class="fas fa-user fa-3x mb-2"></i>

                                                    <div>
                                                        No photograph
                                                    </div>

                                                </div>


                                                <img id="photoPreview"
                                                    src=""
                                                    alt="Photograph Preview"
                                                    style="
                                                        display: none;
                                                        width: 100%;
                                                        height: 100%;
                                                        object-fit: cover;
                                                    ">

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ====================================================
                        SURNAME
                        ==================================================== --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                Surname

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="surname"
                                class="form-control"
                                value="{{ old('surname', $profile->surname) }}"
                                required>

                        </div>


                        {{-- FIRST NAME --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                First Name

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="first_name"
                                class="form-control"
                                value="{{ old('first_name', $profile->first_name) }}"
                                required>

                        </div>


                        {{-- MIDDLE NAME --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Middle Name
                            </label>


                            <input type="text"
                                name="middle_name"
                                class="form-control"
                                value="{{ old('middle_name', $profile->middle_name) }}">

                        </div>


                        {{-- PHONE --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                Phone Number

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', $profile->phone) }}"
                                required>

                        </div>


                        {{-- DATE OF BIRTH --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                Date of Birth

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="date"
                                name="date_of_birth"
                                class="form-control"
                                value="{{ old('date_of_birth', $profile->date_of_birth) }}"
                                required>

                        </div>


                        {{-- GENDER --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                Gender

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <select name="gender"
                                class="form-select"
                                required>

                                <option value="">
                                    Select Gender
                                </option>


                                <option value="Male"
                                    {{ old('gender', $profile->gender) === 'Male' ? 'selected' : '' }}>

                                    Male

                                </option>


                                <option value="Female"
                                    {{ old('gender', $profile->gender) === 'Female' ? 'selected' : '' }}>

                                    Female

                                </option>

                            </select>

                        </div>


                        {{-- NATIONALITY --}}

                        <div class="col-md-6">

                            <label class="form-label">

                                Nationality

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="nationality"
                                class="form-control"
                                value="{{ old('nationality', $profile->nationality) }}"
                                required>

                        </div>


                    </div>


                    {{-- PERSONAL SAVE --}}

                    <div class="mt-4">

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-save me-1"></i>

                            Save Personal Information

                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ============================================================
        RESIDENTIAL ADDRESS
        ============================================================ --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="fw-bold mb-0">
                    Residential Address
                </h5>

            </div>


            <div class="card-body">

                <form method="POST"
                    action="{{ route('member.profile.update') }}">

                    @csrf

                    @method('PUT')

                    <input type="hidden"
                        name="section"
                        value="residential">


                    <div class="row g-3">


                        {{-- ADDRESS --}}

                        <div class="col-12">

                            <label class="form-label">

                                Address

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <textarea name="address"
                                class="form-control"
                                rows="3"
                                required>{{ old('address', $profile->address) }}</textarea>

                        </div>


                        {{-- CITY --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                City

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="city"
                                class="form-control"
                                value="{{ old('city', $profile->city) }}"
                                required>

                        </div>


                        {{-- STATE --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                State

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="state"
                                class="form-control"
                                value="{{ old('state', $profile->state) }}"
                                required>

                        </div>


                        {{-- LGA --}}

                        <div class="col-md-4">

                            <label class="form-label">

                                LGA

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="lga"
                                class="form-control"
                                value="{{ old('lga', $profile->lga) }}"
                                required>

                        </div>


                    </div>


                    {{-- RESIDENTIAL SAVE --}}

                    <div class="mt-4">

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-save me-1"></i>

                            Save Residential Address

                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ============================================================
        BUSINESS INFORMATION
        ============================================================ --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="fw-bold mb-0">
                    Business Information
                </h5>

            </div>


            <div class="card-body">

                <form method="POST"
                    action="{{ route('member.profile.update') }}">

                    @csrf

                    @method('PUT')

                    <input type="hidden"
                        name="section"
                        value="business">


                    <div class="row g-3">


                        {{-- BUSINESS NAME --}}

                        <div class="col-md-6">

                            <label class="form-label">

                                Business Name

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="business_name"
                                class="form-control"
                                value="{{ old('business_name', $profile->business_name) }}"
                                required>

                        </div>


                        {{-- REGISTRATION NUMBER --}}

                        <div class="col-md-6">

                            <label class="form-label">

                                Business Registration Number

                            </label>


                            <input type="text"
                                name="business_registration_number"
                                class="form-control"
                                value="{{ old('business_registration_number', $profile->business_registration_number) }}">

                        </div>


                        {{-- BUSINESS TYPE --}}

                        <div class="col-md-6">

                            <label class="form-label">

                                Business Type

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input type="text"
                                name="business_type"
                                class="form-control"
                                value="{{ old('business_type', $profile->business_type) }}"
                                required>

                        </div>


                        {{-- BUSINESS ADDRESS --}}

                        <div class="col-12">

                            <label class="form-label">

                                Business Address

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <textarea name="business_address"
                                class="form-control"
                                rows="3"
                                required>{{ old('business_address', $profile->business_address) }}</textarea>

                        </div>


                    </div>


                    {{-- BUSINESS SAVE --}}

                    <div class="mt-4">

                        <button type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-save me-1"></i>

                            Save Business Information

                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ============================================================
        FINAL SUBMIT / RESUBMIT APPLICATION
        ============================================================ --}}

        @if ($profile->status === 'draft' || $profile->status === 'rejected')

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body text-center">


                    {{-- ====================================================
                    REJECTED APPLICATION
                    ==================================================== --}}

                    @if ($profile->status === 'rejected')

                        <div class="mb-4">

                            <div class="mb-3">

                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger"
                                    style="width: 70px; height: 70px;">

                                    <i class="fas fa-redo fa-2x"></i>

                                </span>

                            </div>


                            <h5 class="fw-bold mb-2">
                                Resubmit Your Application
                            </h5>


                            <p class="text-muted mb-3">

                                Your previous application was rejected.

                                Please review the administrator's feedback,
                                make the necessary corrections and submit
                                your application again.

                            </p>


                            @if ($profile->rejection_reason)

                                <div class="alert alert-danger text-start mb-4">

                                    <div class="fw-bold mb-2">

                                        <i class="fas fa-exclamation-circle me-1"></i>

                                        Reason for Rejection

                                    </div>


                                    <div>
                                        {{ $profile->rejection_reason }}
                                    </div>

                                </div>

                            @endif


                            @if ($profile->admin_comment)

                                <div class="alert alert-warning text-start mb-4">

                                    <div class="fw-bold mb-2">

                                        <i class="fas fa-comment-dots me-1"></i>

                                        Administrator's Comment

                                    </div>


                                    <div>
                                        {{ $profile->admin_comment }}
                                    </div>

                                </div>

                            @endif

                        </div>


                    {{-- ====================================================
                    DRAFT APPLICATION
                    ==================================================== --}}

                    @else

                        <h5 class="fw-bold mb-2">

                            Submit Your Application

                        </h5>


                        <p class="text-muted mb-4">

                            Make sure you have saved all sections and provided
                            all required information before submitting your
                            application for review.

                        </p>

                    @endif


                    {{-- ====================================================
                    SUBMIT / RESUBMIT BUTTON
                    ==================================================== --}}

                    <form method="POST"
                        action="{{ route('member.profile.submit') }}">

                        @csrf


                        @if ($profile->status === 'rejected')

                            <button type="submit"
                                class="btn btn-success btn-lg">

                                <i class="fas fa-paper-plane me-1"></i>

                                Resubmit Application

                            </button>

                        @else

                            <button type="submit"
                                class="btn btn-success btn-lg">

                                <i class="fas fa-paper-plane me-1"></i>

                                Submit Application

                            </button>

                        @endif

                    </form>


                </div>

            </div>

        @endif


        {{-- ============================================================
        SUBMITTED APPLICATION
        ============================================================ --}}

        @if ($profile->status === 'submitted')

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <span
                            class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning"
                            style="width: 70px; height: 70px;">

                            <i class="fas fa-clock fa-2x"></i>

                        </span>

                    </div>


                    <h5 class="fw-bold mb-2">
                        Application Awaiting Review
                    </h5>


                    <p class="text-muted mb-0">

                        Your application has been submitted successfully
                        and is currently being reviewed by the administrator.

                        You will be notified once a decision has been made.

                    </p>

                </div>

            </div>

        @endif


        {{-- ============================================================
        APPROVED APPLICATION
        ============================================================ --}}

        @if ($profile->status === 'approved')

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <span
                            class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success"
                            style="width: 70px; height: 70px;">

                            <i class="fas fa-check-circle fa-2x"></i>

                        </span>

                    </div>


                    <h5 class="fw-bold mb-2">
                        Application Approved
                    </h5>


                    <p class="text-muted mb-3">

                        Your membership application has been approved.

                    </p>


                    @if ($profile->membership_number)

                        <div class="alert alert-success mb-0">

                            <strong>
                                Membership Number:
                            </strong>

                            <span class="fw-bold ms-1">

                                {{ $profile->membership_number }}

                            </span>

                        </div>

                    @endif

                </div>

            </div>

        @endif


        {{-- ============================================================
        PHOTO PREVIEW SCRIPT
        ============================================================ --}}

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const photoInput = document.getElementById('photo');
                const photoPreview = document.getElementById('photoPreview');
                const photoPlaceholder = document.getElementById('photoPlaceholder');

                if (!photoInput || !photoPreview) {
                    return;
                }


                photoInput.addEventListener('change', function(event) {

                    const file = event.target.files[0];

                    if (!file) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK FILE TYPE
                    |--------------------------------------------------------------------------
                    */

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];


                    if (!allowedTypes.includes(file.type)) {

                        alert(
                            'Please select a JPG, JPEG, PNG or WEBP image.'
                        );

                        photoInput.value = '';

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK FILE SIZE
                    |--------------------------------------------------------------------------
                    |
                    | Maximum: 2MB
                    |
                    */

                    if (file.size > 2 * 1024 * 1024) {

                        alert(
                            'The photograph must not be larger than 2MB.'
                        );

                        photoInput.value = '';

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DISPLAY PREVIEW
                    |--------------------------------------------------------------------------
                    */

                    const reader = new FileReader();


                    reader.onload = function(e) {

                        photoPreview.src = e.target.result;

                        photoPreview.style.display = 'block';


                        if (photoPlaceholder) {

                            photoPlaceholder.style.display = 'none';

                        }

                    };


                    reader.readAsDataURL(file);

                });

            });
        </script>


    </div>

@endsection
