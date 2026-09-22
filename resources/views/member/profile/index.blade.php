@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Member Profile')

@section('member')

    @php
        /*
        |--------------------------------------------------------------------------
        | PROFILE EDITING STATE
        |--------------------------------------------------------------------------
        |
        | Members can only edit their profile when:
        |
        | 1. Profile is still a draft
        | 2. Profile was rejected and needs correction
        |
        | Once approved, the profile becomes permanently read-only
        | from the member side.
        |
        */

        $isApproved = $profile->status === 'approved';

        $canEditProfile = in_array($profile->status, ['draft', 'rejected']);
    @endphp


    <div class="container-fluid py-4">

        {{-- ============================================================
             ADMIN MODE BANNER
             Shows only when an admin is filling the profile on behalf of a member
        ============================================================ --}}
        @if (!empty($adminFillingFor))
            <div style="
                background: #fef3c7;
                border: 1px solid #f59e0b;
                color: #92400e;
                padding: 14px 18px;
                border-radius: 11px;
                margin-bottom: 20px;
                font-size: 13px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            ">
                <span style="font-size: 18px;">🛡️</span>
                <span style="flex: 1;">
                    <strong>Admin mode:</strong>
                    You are completing the profile on behalf of
                    <strong>{{ $adminFillingFor->username }}</strong>
                    ({{ $adminFillingFor->phone ?? $adminFillingFor->email ?? 'no contact' }}).
                    All changes will be saved to the member's account.
                </span>
                <a href="{{ route('admin.member.index') }}"
                   style="color:#92400e; text-decoration:underline; font-size:12px; white-space:nowrap;">
                    Cancel &amp; Return
                </a>
            </div>
        @endif


        {{-- ============================================================
             PAGE HEADER
        ============================================================ --}}
        <div class="mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        Member Profile
                    </h3>
                    <p class="text-muted mb-0">
                        Review and manage your membership profile information.
                    </p>
                </div>
            </div>
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
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ============================================================
             PERSONAL INFORMATION
        ============================================================ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Personal Information</h5>
                    @if ($isApproved)
                        <span class="text-success small fw-semibold">
                            <i class="fas fa-lock me-1"></i> Read Only
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body">

                @if ($canEditProfile)
                    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="personal">
                @endif

                <div class="row g-3">

                    {{-- PASSPORT PHOTOGRAPH --}}
                    <div class="col-12">
                        <div class="border rounded p-3 bg-light">
                            <div class="row align-items-center g-4">

                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">
                                        Passport Photograph
                                        @if (!$profile->photo)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($canEditProfile)
                                        <input type="file" name="photo" id="photo" class="form-control"
                                            accept="image/jpeg,image/png,image/webp"
                                            {{ !$profile->photo ? 'required' : '' }}>
                                        <small class="text-muted d-block mt-2">
                                            JPG, JPEG, PNG or WEBP. Maximum size: 2MB.
                                        </small>
                                    @else
                                        <div class="form-control bg-light">
                                            <i class="fas fa-lock text-success me-2"></i>
                                            Photograph upload is locked.
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            Your profile has been approved.
                                            You cannot change your photograph.
                                        </small>
                                    @endif

                                    @if ($profile->photo)
                                        <small class="text-success d-block mt-2">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Passport photograph uploaded.
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-5 text-center">
                                    <div class="mb-2">
                                        <span class="fw-semibold">Photograph</span>
                                    </div>

                                    <div style="
                                        width:150px;
                                        height:180px;
                                        margin:0 auto;
                                        border:1px solid #dee2e6;
                                        border-radius:8px;
                                        background:#ffffff;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        overflow:hidden;
                                    ">
                                        @if ($profile->photo)
                                            <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                                id="photoPreview" alt="Member Photograph"
                                                style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div id="photoPlaceholder" class="text-muted text-center px-2">
                                                <i class="fas fa-user fa-3x mb-2"></i>
                                                <div>No photograph</div>
                                            </div>

                                            @if ($canEditProfile)
                                                <img id="photoPreview" src="" alt="Photograph Preview"
                                                    style="display:none; width:100%; height:100%; object-fit:cover;">
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- SURNAME --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Surname <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="surname" class="form-control"
                            value="{{ old('surname', $profile->surname) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    {{-- FIRST NAME --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            First Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="first_name" class="form-control"
                            value="{{ old('first_name', $profile->first_name) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    {{-- MIDDLE NAME --}}
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control"
                            value="{{ old('middle_name', $profile->middle_name) }}"
                            {{ $canEditProfile ? '' : 'disabled' }}>
                    </div>

                    {{-- PHONE --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $profile->phone) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    {{-- DATE OF BIRTH --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Date of Birth <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date_of_birth" class="form-control"
                            value="{{ old('date_of_birth', $profile->date_of_birth) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    {{-- GENDER --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Gender <span class="text-danger">*</span>
                        </label>
                        <select name="gender" class="form-select"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $profile->gender) === 'Male' ? 'selected' : '' }}>
                                Male
                            </option>
                            <option value="Female" {{ old('gender', $profile->gender) === 'Female' ? 'selected' : '' }}>
                                Female
                            </option>
                        </select>
                    </div>

                    {{-- NATIONALITY --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Nationality <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nationality" class="form-control"
                            value="{{ old('nationality', $profile->nationality) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                </div>

                @if ($canEditProfile)
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Personal Information
                        </button>
                    </div>
                @else
                    <div class="alert alert-light border mt-4 mb-0">
                        <i class="fas fa-lock text-success me-2"></i>
                        <strong>Personal information is locked.</strong>
                        This information cannot be changed after your application has been approved.
                    </div>
                @endif

                @if ($canEditProfile)
                    </form>
                @endif

            </div>
        </div>


        {{-- ============================================================
             RESIDENTIAL ADDRESS
        ============================================================ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Residential Address</h5>
                    @if ($isApproved)
                        <span class="text-success small fw-semibold">
                            <i class="fas fa-lock me-1"></i> Read Only
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body">

                @if ($canEditProfile)
                    <form method="POST" action="{{ route('member.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="residential">
                @endif

                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">
                            Address <span class="text-danger">*</span>
                        </label>
                        <textarea name="address" class="form-control" rows="3"
                            {{ $canEditProfile ? '' : 'disabled' }} required>{{ old('address', $profile->address) }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            City <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="city" class="form-control"
                            value="{{ old('city', $profile->city) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            State <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="state" class="form-control"
                            value="{{ old('state', $profile->state) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            LGA <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="lga" class="form-control"
                            value="{{ old('lga', $profile->lga) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                </div>

                @if ($canEditProfile)
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Residential Address
                        </button>
                    </div>
                @else
                    <div class="alert alert-light border mt-4 mb-0">
                        <i class="fas fa-lock text-success me-2"></i>
                        <strong>Residential address is locked.</strong>
                        This information cannot be changed after your application has been approved.
                    </div>
                @endif

                @if ($canEditProfile)
                    </form>
                @endif

            </div>
        </div>


        {{-- ============================================================
             BUSINESS INFORMATION
        ============================================================ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Business Information</h5>
                    @if ($isApproved)
                        <span class="text-success small fw-semibold">
                            <i class="fas fa-lock me-1"></i> Read Only
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body">

                @if ($canEditProfile)
                    <form method="POST" action="{{ route('member.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="business">
                @endif

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Business Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="business_name" class="form-control"
                            value="{{ old('business_name', $profile->business_name) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business Registration Number</label>
                        <input type="text" name="business_registration_number" class="form-control"
                            value="{{ old('business_registration_number', $profile->business_registration_number) }}"
                            {{ $canEditProfile ? '' : 'disabled' }}>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Business Type <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="business_type" class="form-control"
                            value="{{ old('business_type', $profile->business_type) }}"
                            {{ $canEditProfile ? '' : 'disabled' }} required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Business Address <span class="text-danger">*</span>
                        </label>
                        <textarea name="business_address" class="form-control" rows="3"
                            {{ $canEditProfile ? '' : 'disabled' }} required>{{ old('business_address', $profile->business_address) }}</textarea>
                    </div>

                </div>

                @if ($canEditProfile)
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Business Information
                        </button>
                    </div>
                @else
                    <div class="alert alert-light border mt-4 mb-0">
                        <i class="fas fa-lock text-success me-2"></i>
                        <strong>Business information is locked.</strong>
                        This information cannot be changed after your application has been approved.
                    </div>
                @endif

                @if ($canEditProfile)
                    </form>
                @endif

            </div>
        </div>


        {{-- ============================================================
             APPLICANT DOCUMENTS
        ============================================================ --}}
        @php
            $categoryName = strtolower(trim($profile->membershipCategory->name ?? ''));
            $categoryCode = strtoupper(trim($profile->membershipCategory->code ?? ''));

            $isExporter = str_contains($categoryName, 'exporter') || $categoryCode === 'EXPORTER';
            $isSupplier = str_contains($categoryName, 'supplier') || $categoryCode === 'SUPPLIER';
            $isDealer   = str_contains($categoryName, 'dealer')   || $categoryCode === 'DEALER';
        @endphp

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-file-upload me-2 text-primary"></i>
                        Applicant Documents
                    </h5>
                    @if ($isApproved)
                        <span class="text-success small fw-semibold">
                            <i class="fas fa-lock me-1"></i> Read Only
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body">

                <div class="alert alert-info">
                    <div class="fw-semibold mb-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Required Documents
                    </div>

                    @if ($isExporter)
                        Exporters are required to upload all three documents:
                        <strong>CAC Certificate, CAC Particulars of Directors, and NEPC (Export) License.</strong>
                    @elseif ($isSupplier)
                        CAC Certificate and CAC Particulars of Directors are optional for Suppliers.
                    @elseif ($isDealer)
                        CAC Certificate and CAC Particulars of Directors are optional for Dealers.
                    @else
                        Please upload the documents applicable to your membership category.
                    @endif
                </div>

                @if ($canEditProfile)
                    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="section" value="documents">
                @endif

                <div class="row g-4">

                    {{-- CAC CERTIFICATE --}}
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <label class="form-label fw-semibold">
                                CAC Certificate
                                @if ($isExporter && !$profile->cac_certificate)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if ($canEditProfile)
                                <input type="file" name="cac_certificate" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    {{ $isExporter && !$profile->cac_certificate ? 'required' : '' }}>
                                <small class="text-muted d-block mt-2">
                                    PDF, JPG, JPEG or PNG. Maximum size: 5MB.
                                    @if ($isExporter)
                                        <span class="text-danger">Required for Exporters.</span>
                                    @else
                                        <span class="text-muted">Optional.</span>
                                    @endif
                                </small>
                            @else
                                <div class="form-control bg-light">
                                    <i class="fas fa-lock text-success me-2"></i>
                                    Document upload is locked.
                                </div>
                            @endif

                            @if ($profile->cac_certificate)
                                <div class="mt-3">
                                    <div class="text-success small mb-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        CAC Certificate uploaded.
                                    </div>
                                    <a href="{{ asset('document/' . $profile->cac_certificate) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Document
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- CAC PARTICULARS OF DIRECTORS --}}
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <label class="form-label fw-semibold">
                                CAC Particulars of Directors
                                @if ($isExporter && !$profile->cac_particulars_of_directors)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>

                            @if ($canEditProfile)
                                <input type="file" name="cac_particulars_of_directors" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    {{ $isExporter && !$profile->cac_particulars_of_directors ? 'required' : '' }}>
                                <small class="text-muted d-block mt-2">
                                    PDF, JPG, JPEG or PNG. Maximum size: 5MB.
                                    @if ($isExporter)
                                        <span class="text-danger">Required for Exporters.</span>
                                    @else
                                        <span class="text-muted">Optional.</span>
                                    @endif
                                </small>
                            @else
                                <div class="form-control bg-light">
                                    <i class="fas fa-lock text-success me-2"></i>
                                    Document upload is locked.
                                </div>
                            @endif

                            @if ($profile->cac_particulars_of_directors)
                                <div class="mt-3">
                                    <div class="text-success small mb-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        CAC Particulars of Directors uploaded.
                                    </div>
                                    <a href="{{ asset('document/' . $profile->cac_particulars_of_directors) }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Document
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- NEPC EXPORT LICENSE --}}
                    @if ($isExporter)
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <label class="form-label fw-semibold">
                                    NEPC (Export) License
                                    @if (!$profile->nepc_export_license)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if ($canEditProfile)
                                    <input type="file" name="nepc_export_license" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        {{ !$profile->nepc_export_license ? 'required' : '' }}>
                                    <small class="text-muted d-block mt-2">
                                        PDF, JPG, JPEG or PNG. Maximum size: 5MB.
                                        <span class="text-danger">Required for Exporters.</span>
                                    </small>
                                @else
                                    <div class="form-control bg-light">
                                        <i class="fas fa-lock text-success me-2"></i>
                                        Document upload is locked.
                                    </div>
                                @endif

                                @if ($profile->nepc_export_license)
                                    <div class="mt-3">
                                        <div class="text-success small mb-2">
                                            <i class="fas fa-check-circle me-1"></i>
                                            NEPC Export License uploaded.
                                        </div>
                                        <a href="{{ asset('document/' . $profile->nepc_export_license) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> View Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                @if ($canEditProfile)
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Save Applicant Documents
                        </button>
                    </div>
                @else
                    <div class="alert alert-light border mt-4 mb-0">
                        <i class="fas fa-lock text-success me-2"></i>
                        <strong>Applicant documents are locked.</strong>
                        Your profile has been approved and documents cannot be changed.
                    </div>
                @endif

                @if ($canEditProfile)
                    </form>
                @endif

            </div>
        </div>


        {{-- ============================================================
             FINAL SUBMIT / RESUBMIT APPLICATION
        ============================================================ --}}
        @if ($profile->status === 'draft' || $profile->status === 'rejected')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">

                    @if ($profile->status === 'rejected')
                        <div class="mb-4">
                            <div class="mb-3">
                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-circle
                                        bg-danger bg-opacity-10 text-danger"
                                    style="width:70px;height:70px;">
                                    <i class="fas fa-redo fa-2x"></i>
                                </span>
                            </div>

                            <h5 class="fw-bold mb-2">Resubmit Your Application</h5>
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
                                    <div>{{ $profile->rejection_reason }}</div>
                                </div>
                            @endif

                            @if ($profile->admin_comment)
                                <div class="alert alert-warning text-start mb-4">
                                    <div class="fw-bold mb-2">
                                        <i class="fas fa-comment-dots me-1"></i>
                                        Administrator's Comment
                                    </div>
                                    <div>{{ $profile->admin_comment }}</div>
                                </div>
                            @endif
                        </div>
                    @else
                        <h5 class="fw-bold mb-2">Submit Your Application</h5>
                        <p class="text-muted mb-4">
                            Make sure you have saved all sections and provided
                            all required information before submitting your
                            application for review.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('member.profile.submit') }}">
                        @csrf
                        @if ($profile->status === 'rejected')
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-1"></i>
                                Resubmit Application
                            </button>
                        @else
                            <button type="submit" class="btn btn-success btn-lg">
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
                            class="d-inline-flex align-items-center justify-content-center rounded-circle
                                bg-warning bg-opacity-10 text-warning"
                            style="width:70px;height:70px;">
                            <i class="fas fa-clock fa-2x"></i>
                        </span>
                    </div>

                    <h5 class="fw-bold mb-2">Application Awaiting Review</h5>
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
                            class="d-inline-flex align-items-center justify-content-center rounded-circle
                                bg-success bg-opacity-10 text-success"
                            style="width:70px;height:70px;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </span>
                    </div>

                    @if ($profile->membership_number)
                        <div class="alert alert-success mb-0">
                            <strong>Membership Number:</strong>
                            <span class="fw-bold ms-1">{{ $profile->membership_number }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif


        {{-- ============================================================
             PHOTO PREVIEW SCRIPT
        ============================================================ --}}
        @if ($canEditProfile)
            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    const photoInput = document.getElementById('photo');
                    const photoPreview = document.getElementById('photoPreview');
                    const photoPlaceholder = document.getElementById('photoPlaceholder');

                    if (!photoInput || !photoPreview) {
                        return;
                    }

                    photoInput.addEventListener('change', function (event) {

                        const file = event.target.files[0];

                        if (!file) {
                            return;
                        }

                        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                        if (!allowedTypes.includes(file.type)) {
                            alert('Please select a JPG, JPEG, PNG or WEBP image.');
                            photoInput.value = '';
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            alert('The photograph must not be larger than 2MB.');
                            photoInput.value = '';
                            return;
                        }

                        const reader = new FileReader();

                        reader.onload = function (e) {
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
        @endif

    </div>

@endsection
