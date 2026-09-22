@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Member Application Details
@endsection

@section('admin')

<style>
    /* =========================================================
       APPLICATION DETAILS PAGE — SCOPED STYLES
    ========================================================= */
    .app-details {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1f2937;
    }

    /* Page header */
    .app-details .page-header {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        border-radius: 18px;
        padding: 26px 30px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 12px 30px rgba(4, 120, 87, 0.18);
        position: relative;
        overflow: hidden;
    }

    .app-details .page-header::before {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        top: -100px;
        right: -60px;
    }

    .app-details .page-header::after {
        content: "";
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        bottom: -80px;
        right: 120px;
    }

    .app-details .page-header h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.4px;
        position: relative;
        z-index: 1;
    }

    .app-details .page-header p {
        font-size: 13px;
        margin: 4px 0 0 0;
        opacity: 0.88;
        position: relative;
        z-index: 1;
    }

    .app-details .page-header .badge-status {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(4px);
    }

    /* Breadcrumb */
    .app-details .breadcrumb {
        font-size: 13px;
        margin-bottom: 22px;
    }
    .app-details .breadcrumb a {
        color: #047857;
        text-decoration: none;
        font-weight: 600;
    }
    .app-details .breadcrumb a:hover {
        text-decoration: underline;
    }
    .app-details .breadcrumb-item.active {
        color: #6b7280;
    }

    /* Alerts */
    .app-details .alert {
        border-radius: 12px;
        border: none;
        font-size: 13px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Cards */
    .app-details .card {
        background: #ffffff;
        border: 1px solid #e5ede8;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(11, 59, 44, 0.04);
        margin-bottom: 22px;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    .app-details .card:hover {
        box-shadow: 0 6px 22px rgba(11, 59, 44, 0.07);
    }

    .app-details .card-header {
        background: #f9fbfa;
        border-bottom: 1px solid #e5ede8;
        padding: 15px 22px;
        font-weight: 700;
        color: #0b3b2c;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .app-details .card-header i {
        color: #047857;
        font-size: 15px;
    }

    .app-details .card-body {
        padding: 22px;
    }

    /* Field blocks */
    .app-details .field {
        margin-bottom: 18px;
    }

    .app-details .field-label {
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .app-details .field-value {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
        word-break: break-word;
        line-height: 1.5;
    }

    .app-details .field-value.muted {
        color: #9ca3af;
        font-style: italic;
        font-weight: 400;
    }

    /* Photo */
    .app-details .photo-frame {
        width: 130px;
        height: 155px;
        border: 3px solid #ffffff;
        outline: 2px solid #d1e6da;
        border-radius: 12px;
        overflow: hidden;
        background: #f3f7f5;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(11, 59, 44, 0.08);
    }

    .app-details .photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .app-details .photo-frame .placeholder {
        color: #9ca3af;
        font-size: 32px;
    }

    /* Badges */
    .app-details .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        line-height: 1;
        border: 1px solid transparent;
    }

    .app-details .badge-success {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .app-details .badge-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .app-details .badge-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    .app-details .badge-info {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }

    .app-details .badge-secondary {
        background: #e5e7eb;
        color: #374151;
        border-color: #d1d5db;
    }

    /* Documents list */
    .app-details .doc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px dashed #e5ede8;
        flex-wrap: wrap;
    }

    .app-details .doc-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .app-details .doc-row:first-child {
        padding-top: 0;
    }

    .app-details .doc-icon {
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: #ecfdf5;
        color: #047857;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }

    .app-details .doc-info {
        flex: 1;
        min-width: 160px;
    }

    .app-details .doc-title {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .app-details .doc-sub {
        font-size: 12px;
        color: #6b7280;
    }

    /* Payment summary grid */
    .app-details .payment-summary {
        background: #f9fbfa;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 20px;
        border: 1px solid #e5ede8;
    }

    .app-details .payment-amount {
        font-size: 26px;
        font-weight: 800;
        color: #0b3b2c;
        letter-spacing: -0.5px;
    }

    .app-details .payment-label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    /* Sidebar sticky */
    .app-details .sidebar-sticky {
        position: sticky;
        top: 90px;
    }

    /* Status card */
    .app-details .status-card {
        border-radius: 16px;
        padding: 24px 22px;
        text-align: center;
        border: 1px solid;
        margin-bottom: 22px;
    }

    .app-details .status-card.approved {
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        border-color: #6ee7b7;
    }

    .app-details .status-card.rejected {
        background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
        border-color: #fca5a5;
    }

    .app-details .status-card.submitted {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border-color: #fcd34d;
    }

    .app-details .status-card.draft {
        background: linear-gradient(135deg, #ede9fe 0%, #f5f3ff 100%);
        border-color: #c4b5fd;
    }

    .app-details .status-card .status-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 14px;
    }

    .app-details .status-card .status-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .app-details .status-card .status-value {
        font-size: 18px;
        font-weight: 800;
        color: #0b3b2c;
        letter-spacing: -0.3px;
    }

    /* Membership number box */
    .app-details .membership-number-box {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        color: #ffffff;
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        margin-bottom: 22px;
        box-shadow: 0 8px 22px rgba(4, 120, 87, 0.20);
    }

    .app-details .membership-number-box .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.85;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .app-details .membership-number-box .value {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1px;
        font-family: 'Courier New', monospace;
    }

    /* Buttons */
    .app-details .btn {
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 18px;
        border: none;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .app-details .btn:hover {
        transform: translateY(-1px);
    }

    .app-details .btn-primary {
        background: linear-gradient(135deg, #047857, #059669);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(4, 120, 87, 0.20);
    }

    .app-details .btn-primary:hover {
        color: #ffffff;
        box-shadow: 0 9px 22px rgba(4, 120, 87, 0.28);
    }

    .app-details .btn-success {
        background: linear-gradient(135deg, #059669, #10b981);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(5, 150, 105, 0.20);
    }

    .app-details .btn-success:hover {
        color: #ffffff;
        box-shadow: 0 9px 22px rgba(5, 150, 105, 0.28);
    }

    .app-details .btn-danger {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.20);
    }

    .app-details .btn-danger:hover {
        color: #ffffff;
        box-shadow: 0 9px 22px rgba(220, 38, 38, 0.28);
    }

    .app-details .btn-warning {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(217, 119, 6, 0.20);
    }

    .app-details .btn-warning:hover {
        color: #ffffff;
        box-shadow: 0 9px 22px rgba(217, 119, 6, 0.28);
    }

    .app-details .btn-outline {
        background: #ffffff;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .app-details .btn-outline:hover {
        background: #ecfdf5;
        color: #047857;
    }

    .app-details .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .app-details .btn-secondary:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .app-details .btn-sm {
        padding: 7px 14px;
        font-size: 12px;
        border-radius: 8px;
    }

    .app-details .btn:disabled,
    .app-details .btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
    }

    @media (max-width: 991px) {
        .app-details .sidebar-sticky {
            position: static;
            top: auto;
        }
    }
</style>

<div class="container-fluid px-4 app-details">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="page-header">

        <div>
            <h1>
                <i class="fas fa-file-signature me-2"></i>
                Member Application
            </h1>
            <p>
                Review submitted member details, documents, and payment before making a decision.
            </p>
        </div>

        <div class="badge-status">

            @if ($application->status === 'approved')
                <i class="fas fa-check-circle me-1"></i> Approved
            @elseif ($application->status === 'rejected')
                <i class="fas fa-times-circle me-1"></i> Rejected
            @elseif ($application->status === 'submitted')
                <i class="fas fa-clock me-1"></i> Pending Review
            @else
                <i class="fas fa-file me-1"></i> {{ ucfirst($application->status) }}
            @endif

        </div>

    </div>


    {{-- =========================================================
         BREADCRUMB
    ========================================================== --}}
    <ol class="breadcrumb">

        <li class="breadcrumb-item">
            <a href="{{ route('admin.admin_dashboard') }}">
                <i class="fas fa-home me-1"></i> Dashboard
            </a>
        </li>

        <li class="breadcrumb-item">
            <a href="{{ route('admin.members.index') }}">
                Member Applications
            </a>
        </li>

        <li class="breadcrumb-item active">
            {{ $application->surname ?? '' }} {{ $application->first_name ?? '' }}
        </li>

    </ol>


    {{-- =========================================================
         FLASH MESSAGES
    ========================================================== --}}
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif


    <div class="row g-4">

        {{-- =====================================================
             LEFT COLUMN — DETAILS
        ====================================================== --}}
        <div class="col-lg-8">

            {{-- IDENTITY CARD --}}
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center g-4">

                        <div class="col-auto">
                            <div class="photo-frame">
                                @if ($application->photo)
                                    <img src="{{ asset('uploads/member_profiles/' . $application->photo) }}"
                                        alt="Member Photograph">
                                @else
                                    <div class="placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col">

                            <div class="field">
                                <div class="field-label">
                                    <i class="fas fa-user-circle"></i>
                                    Full Name
                                </div>
                                <div class="field-value" style="font-size:18px; font-weight:700;">
                                    {{ trim(
                                        ($application->surname ?? '') . ' ' .
                                        ($application->first_name ?? '') . ' ' .
                                        ($application->middle_name ?? '')
                                    ) ?: 'N/A' }}
                                </div>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="field" style="margin-bottom:0;">
                                        <div class="field-label">
                                            <i class="fas fa-tag"></i>
                                            Membership Category
                                        </div>
                                        <div class="field-value">
                                            @if ($application->membershipCategory)
                                                <span class="badge badge-info">
                                                    {{ $application->membershipCategory->name }}
                                                    @if ($application->membershipCategory->code)
                                                        • {{ $application->membershipCategory->code }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="field-value muted">Not assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="field" style="margin-bottom:0;">
                                        <div class="field-label">
                                            <i class="fas fa-user-tag"></i>
                                            Member Type
                                        </div>
                                        <div class="field-value">
                                            {{ $application->user->member_type ? ucfirst($application->user->member_type) : 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- PERSONAL INFORMATION --}}
            <div class="card">

                <div class="card-header">
                    <i class="fas fa-user"></i>
                    Personal Information
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-signature"></i> Surname</div>
                                <div class="field-value">{{ $application->surname ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-signature"></i> First Name</div>
                                <div class="field-value">{{ $application->first_name ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-signature"></i> Middle Name</div>
                                <div class="field-value">{{ $application->middle_name ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="field-value">{{ $application->user->email ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-phone"></i> Phone Number</div>
                                <div class="field-value">{{ $application->phone ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-calendar"></i> Date of Birth</div>
                                <div class="field-value">
                                    {{ $application->date_of_birth ? $application->date_of_birth->format('d M Y') : '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                <div class="field-value">{{ $application->gender ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-globe"></i> Nationality</div>
                                <div class="field-value">{{ $application->nationality ?? '—' }}</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            {{-- ADDRESS INFORMATION --}}
            <div class="card">

                <div class="card-header">
                    <i class="fas fa-map-marker-alt"></i>
                    Address Information
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-12">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-home"></i> Residential Address</div>
                                <div class="field-value">{{ $application->address ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-city"></i> City</div>
                                <div class="field-value">{{ $application->city ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-map"></i> State</div>
                                <div class="field-value">{{ $application->state ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-location-arrow"></i> LGA</div>
                                <div class="field-value">{{ $application->lga ?? '—' }}</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            {{-- BUSINESS INFORMATION --}}
            <div class="card">

                <div class="card-header">
                    <i class="fas fa-building"></i>
                    Business Information
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-briefcase"></i> Business Name</div>
                                <div class="field-value">{{ $application->business_name ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-id-card"></i> Business Registration Number</div>
                                <div class="field-value">{{ $application->business_registration_number ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-tags"></i> Business Type</div>
                                <div class="field-value">{{ $application->business_type ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="field">
                                <div class="field-label"><i class="fas fa-map-marked-alt"></i> Business Address</div>
                                <div class="field-value">{{ $application->business_address ?? '—' }}</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            {{-- UPLOADED DOCUMENTS --}}
            <div class="card">

                <div class="card-header">
                    <i class="fas fa-file-alt"></i>
                    Uploaded Documents
                </div>

                <div class="card-body">

                    {{-- CAC CERTIFICATE --}}
                    <div class="doc-row">
                        <div class="doc-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div class="doc-info">
                            <div class="doc-title">CAC Certificate</div>
                            <div class="doc-sub">Company registration certificate</div>
                        </div>
                        <div>
                            @if ($application->cac_certificate)
                                <span class="badge badge-success me-2">
                                    <i class="fas fa-check"></i> Uploaded
                                </span>
                                <a href="{{ route('admin.members.document', [
                                    'id' => $application->id,
                                    'document' => 'cac_certificate',
                                ]) }}"
                                    target="_blank"
                                    class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> Not Uploaded
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- CAC PARTICULARS OF DIRECTORS --}}
                    <div class="doc-row">
                        <div class="doc-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="doc-info">
                            <div class="doc-title">CAC Particulars of Directors</div>
                            <div class="doc-sub">Directors listing</div>
                        </div>
                        <div>
                            @if ($application->cac_particulars_of_directors)
                                <span class="badge badge-success me-2">
                                    <i class="fas fa-check"></i> Uploaded
                                </span>
                                <a href="{{ route('admin.members.document', [
                                    'id' => $application->id,
                                    'document' => 'cac_particulars_of_directors',
                                ]) }}"
                                    target="_blank"
                                    class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> Not Uploaded
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- NEPC EXPORT LICENSE --}}
                    <div class="doc-row">
                        <div class="doc-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="doc-info">
                            <div class="doc-title">NEPC Export License</div>
                            <div class="doc-sub">Nigerian Export Promotion Council license</div>
                        </div>
                        <div>
                            @if ($application->nepc_export_license)
                                <span class="badge badge-success me-2">
                                    <i class="fas fa-check"></i> Uploaded
                                </span>
                                <a href="{{ route('admin.members.document', [
                                    'id' => $application->id,
                                    'document' => 'nepc_export_license',
                                ]) }}"
                                    target="_blank"
                                    class="btn btn-outline btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> Not Uploaded
                                </span>
                            @endif
                        </div>
                    </div>

                </div>

            </div>


            {{-- PAYMENT INFORMATION --}}
            <div class="card">

                <div class="card-header">
                    <i class="fas fa-credit-card"></i>
                    Payment Information
                </div>

                <div class="card-body">

                    @php
                        $payment = $application->user->payments->first();
                    @endphp

                    @if ($payment)

                        <div class="payment-summary">
                            <div class="row align-items-center g-3">
                                <div class="col-md-6">
                                    <div class="payment-label">Amount Paid</div>
                                    <div class="payment-amount">
                                        ₦{{ number_format($payment->amount, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    @if ($payment->status === 'paid')
                                        <span class="badge badge-success" style="font-size:12px; padding:8px 16px;">
                                            <i class="fas fa-check-circle"></i> PAID
                                        </span>
                                    @elseif ($payment->status === 'pending')
                                        <span class="badge badge-warning" style="font-size:12px; padding:8px 16px;">
                                            <i class="fas fa-clock"></i> PENDING
                                        </span>
                                    @else
                                        <span class="badge badge-danger" style="font-size:12px; padding:8px 16px;">
                                            <i class="fas fa-times-circle"></i> {{ strtoupper($payment->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-hashtag"></i> Payment Reference</div>
                                    <div class="field-value" style="font-family:'Courier New', monospace; font-size:13px;">
                                        {{ $payment->reference ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-file-invoice"></i> Payment Type</div>
                                    <div class="field-value">{{ ucfirst($payment->payment_type ?? '—') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-tag"></i> Fee Type</div>
                                    <div class="field-value">{{ ucfirst($payment->fee_type ?? '—') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-network-wired"></i> Gateway</div>
                                    <div class="field-value">{{ ucfirst($payment->gateway ?? '—') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-fingerprint"></i> Transaction ID</div>
                                    <div class="field-value" style="font-family:'Courier New', monospace; font-size:13px;">
                                        {{ $payment->gateway_transaction_id ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <div class="field-label"><i class="fas fa-calendar-check"></i> Paid At</div>
                                    <div class="field-value">
                                        {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '—' }}
                                    </div>
                                </div>
                            </div>

                        </div>

                    @else

                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            No payment record found for this member.
                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- =====================================================
             RIGHT COLUMN — STATUS & ACTIONS
        ====================================================== --}}
        <div class="col-lg-4">
            <div class="sidebar-sticky">

                {{-- STATUS CARD --}}
                @php
                    $statusClass = 'draft';
                    $statusIcon = 'fa-file';
                    $statusColor = '#8b5cf6';
                    $statusText = ucfirst($application->status);

                    if ($application->status === 'approved') {
                        $statusClass = 'approved';
                        $statusIcon = 'fa-check-circle';
                        $statusColor = '#047857';
                        $statusText = 'Approved';
                    } elseif ($application->status === 'rejected') {
                        $statusClass = 'rejected';
                        $statusIcon = 'fa-times-circle';
                        $statusColor = '#dc2626';
                        $statusText = 'Rejected';
                    } elseif ($application->status === 'submitted') {
                        $statusClass = 'submitted';
                        $statusIcon = 'fa-clock';
                        $statusColor = '#d97706';
                        $statusText = 'Pending Review';
                    }
                @endphp

                <div class="status-card {{ $statusClass }}">

                    <div class="status-icon" style="background:{{ $statusColor }}20; color:{{ $statusColor }};">
                        <i class="fas {{ $statusIcon }}"></i>
                    </div>

                    <div class="status-label">Application Status</div>
                    <div class="status-value">{{ $statusText }}</div>

                </div>


                {{-- MEMBERSHIP NUMBER --}}
                @if ($application->membership_number)
                    <div class="membership-number-box">
                        <div class="label">
                            <i class="fas fa-id-badge me-1"></i> Membership Number
                        </div>
                        <div class="value">{{ $application->membership_number }}</div>
                    </div>
                @endif


                {{-- RENEWAL DEBIT (EXPIRED MEMBERSHIP) --}}
                @if (
                    $application->membership &&
                    $application->membership->expires_at &&
                    today()->gt($application->membership->expires_at))
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-sync-alt"></i>
                            Membership Renewal
                        </div>
                        <div class="card-body">

                            <div class="field">
                                <div class="field-label">Membership Status</div>
                                <div class="field-value">
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-circle"></i> EXPIRED
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <div class="field-label">Expired On</div>
                                <div class="field-value">
                                    {{ $application->membership->expires_at->format('d M Y') }}
                                </div>
                            </div>

                            <form method="POST"
                                action="{{ route('admin.members.generate-renewal-debit', $application->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100"
                                    onclick="return confirm('Generate the renewal debit for this expired membership?')">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    Generate Renewal Debit
                                </button>
                            </form>

                        </div>
                    </div>
                @endif


                {{-- REJECTION REASON --}}
                @if ($application->status === 'rejected' && $application->rejection_reason)
                    <div class="card">
                        <div class="card-header" style="color:#dc2626;">
                            <i class="fas fa-times-circle" style="color:#dc2626;"></i>
                            Rejection Reason
                        </div>
                        <div class="card-body">
                            <div class="field-value">{{ $application->rejection_reason }}</div>
                        </div>
                    </div>
                @endif


                {{-- ADMIN COMMENT --}}
                @if ($application->admin_comment)
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-comment"></i>
                            Admin Comment
                        </div>
                        <div class="card-body">
                            <div class="field-value">{{ $application->admin_comment }}</div>
                        </div>
                    </div>
                @endif


                {{-- ADMIN DECISION --}}
                @if ($application->status !== 'approved')
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-gavel"></i>
                            Admin Decision
                        </div>
                        <div class="card-body">

                            {{-- APPROVE --}}
                            <form method="POST"
                                action="{{ route('admin.members.approve', $application->id) }}"
                                class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Approve this application? This will generate the membership number and all membership documents.')">
                                    <i class="fas fa-check"></i>
                                    Approve Application
                                </button>
                            </form>

                            {{-- REJECT --}}
                            <form method="POST"
                                action="{{ route('admin.members.reject', $application->id) }}">
                                @csrf

                                <div class="field">
                                    <div class="field-label">
                                        <i class="fas fa-comment-dots"></i>
                                        Rejection Reason
                                    </div>
                                    <textarea name="rejection_reason"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Explain why this application is being rejected..."
                                        required></textarea>
                                </div>

                                <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Reject this application?')">
                                    <i class="fas fa-times"></i>
                                    Reject Application
                                </button>
                            </form>

                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt" style="font-size:26px; color:#047857;"></i>
                            <p class="mb-0 mt-2" style="color:#065f46; font-weight:600; font-size:13px;">
                                This member has been approved.
                            </p>
                        </div>
                    </div>
                @endif


                {{-- BACK --}}
                <a href="{{ route('admin.members.index') }}"
                    class="btn btn-secondary w-100">
                    <i class="fas fa-arrow-left"></i>
                    Back to Applications
                </a>

            </div>
        </div>

    </div>

</div>

@endsection
