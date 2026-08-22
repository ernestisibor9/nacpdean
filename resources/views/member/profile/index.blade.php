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
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- ============================================================
        ERROR MESSAGE
    ============================================================ --}}

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        {{-- ============================================================
        VALIDATION ERRORS
    ============================================================ --}}

        @if ($errors->any())

            <div class="alert alert-danger">

                <strong>
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

        {{--
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Application Status</h5>

                        @if ($profile->status === 'draft')
                            <p class="text-muted mb-0">
                                Complete your profile and submit your application for review.
                            </p>
                        @elseif ($profile->status === 'submitted')
                            <p class="text-warning mb-0">
                                Your application is awaiting review.
                            </p>
                        @elseif ($profile->status === 'approved')
                            <p class="text-success mb-0">
                                Your membership application has been approved.
                            </p>
                        @endif
                    </div>

                    <div>
                        @if ($profile->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif ($profile->status === 'submitted')
                            <span class="badge bg-warning text-dark">Submitted</span>
                        @elseif ($profile->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    --}}


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

                <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">

                    @csrf

                    @method('PUT')

                    <input type="hidden" name="section" value="personal">


                    <div class="row g-3">

                        {{-- ============================================================
    PASSPORT PHOTOGRAPH
============================================================ --}}

                        <div class="col-12">

                            <div class="border rounded p-3 bg-light">

                                <div class="row align-items-center g-4">

                                    {{-- PHOTO UPLOAD --}}
                                    <div class="col-md-7">

                                        <label class="form-label fw-semibold">
                                            Passport Photograph

                                            @if (!$profile->photo)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        <input type="file" name="photo" id="photo" class="form-control"
                                            accept="image/jpeg,image/png,image/webp"
                                            {{ !$profile->photo ? 'required' : '' }}>

                                        <small class="text-muted d-block mt-2">
                                            JPG, JPEG, PNG or WEBP. Maximum size: 2MB.
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

                                        <div
                                            style="
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
                                                    id="photoPreview" alt="Member Photograph"
                                                    style="
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                            ">
                                            @else
                                                <div id="photoPlaceholder" class="text-muted text-center px-2">

                                                    <i class="fas fa-user fa-3x mb-2"></i>

                                                    <div>
                                                        No photograph
                                                    </div>

                                                </div>

                                                <img id="photoPreview" src="" alt="Photograph Preview"
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


                        {{-- Surname --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Surname
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="surname" class="form-control"
                                value="{{ old('surname', $profile->surname) }}" required>

                        </div>


                        {{-- First Name --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                First Name
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="first_name" class="form-control"
                                value="{{ old('first_name', $profile->first_name) }}" required>

                        </div>


                        {{-- Middle Name --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Middle Name
                            </label>

                            <input type="text" name="middle_name" class="form-control"
                                value="{{ old('middle_name', $profile->middle_name) }}">

                        </div>


                        {{-- Phone --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Phone Number
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $profile->phone) }}" required>

                        </div>


                        {{-- Date of Birth --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Date of Birth
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="date_of_birth" class="form-control"
                                value="{{ old('date_of_birth', $profile->date_of_birth) }}" required>

                        </div>


                        {{-- Gender --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Gender
                                <span class="text-danger">*</span>
                            </label>

                            <select name="gender" class="form-select" required>

                                <option value="">
                                    Select Gender
                                </option>

                                <option value="Male" {{ old('gender', $profile->gender) === 'Male' ? 'selected' : '' }}>
                                    Male
                                </option>

                                <option value="Female"
                                    {{ old('gender', $profile->gender) === 'Female' ? 'selected' : '' }}>
                                    Female
                                </option>

                            </select>

                        </div>


                        {{-- Nationality --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Nationality
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="nationality" class="form-control"
                                value="{{ old('nationality', $profile->nationality) }}" required>

                        </div>


                    </div>


                    {{-- Personal Save --}}

                    <div class="mt-4">

                        <button type="submit" class="btn btn-primary">
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

                <form method="POST" action="{{ route('member.profile.update') }}">

                    @csrf

                    @method('PUT')

                    <input type="hidden" name="section" value="residential">


                    <div class="row g-3">


                        {{-- Address --}}

                        <div class="col-12">

                            <label class="form-label">
                                Address
                                <span class="text-danger">*</span>
                            </label>

                            <textarea name="address" class="form-control" rows="3" required>{{ old('address', $profile->address) }}</textarea>

                        </div>


                        {{-- City --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                City
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $profile->city) }}" required>

                        </div>


                        {{-- State --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                State
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="state" class="form-control"
                                value="{{ old('state', $profile->state) }}" required>

                        </div>


                        {{-- LGA --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                LGA
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="lga" class="form-control"
                                value="{{ old('lga', $profile->lga) }}" required>

                        </div>


                    </div>


                    {{-- Residential Save --}}

                    <div class="mt-4">

                        <button type="submit" class="btn btn-primary">
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

                <form method="POST" action="{{ route('member.profile.update') }}">

                    @csrf

                    @method('PUT')

                    <input type="hidden" name="section" value="business">


                    <div class="row g-3">


                        {{-- Business Name --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Business Name
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="business_name" class="form-control"
                                value="{{ old('business_name', $profile->business_name) }}" required>

                        </div>


                        {{-- Registration Number --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Business Registration Number
                            </label>

                            <input type="text" name="business_registration_number" class="form-control"
                                value="{{ old('business_registration_number', $profile->business_registration_number) }}">

                        </div>


                        {{-- Business Type --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Business Type
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="business_type" class="form-control"
                                value="{{ old('business_type', $profile->business_type) }}" required>

                        </div>


                        {{-- Business Address --}}

                        <div class="col-12">

                            <label class="form-label">
                                Business Address
                                <span class="text-danger">*</span>
                            </label>

                            <textarea name="business_address" class="form-control" rows="3" required>{{ old('business_address', $profile->business_address) }}</textarea>

                        </div>


                    </div>


                    {{-- Business Save --}}

                    <div class="mt-4">

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Business Information
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ============================================================
        FINAL SUBMIT APPLICATION
    ============================================================ --}}

        @if ($profile->status === 'draft')
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body text-center">

                    <h5 class="fw-bold mb-2">
                        Submit Your Application
                    </h5>

                    <p class="text-muted mb-4">
                        Make sure you have saved all sections and provided
                        all required information before submitting your
                        application for review.
                    </p>


                    <form method="POST" action="{{ route('member.profile.submit') }}">

                        @csrf

                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane me-1"></i>
                            Submit Application
                        </button>

                    </form>

                </div>

            </div>
        @endif


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

                        alert('Please select a JPG, JPEG, PNG or WEBP image.');

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

                        alert('The photograph must not be larger than 2MB.');

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
