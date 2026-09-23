@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Edit Member Profile
@endsection

@section('admin')

<style>
    .edit-profile-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1f2937;
    }

    /* HEADER */
    .edit-profile-page .page-header {
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

    .edit-profile-page .page-header h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.4px;
    }

    .edit-profile-page .page-header p {
        font-size: 13px;
        margin: 4px 0 0 0;
        opacity: 0.88;
    }

    .edit-profile-page .page-header .member-pill {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    /* Breadcrumb */
    .edit-profile-page .breadcrumb {
        font-size: 13px;
        margin-bottom: 22px;
    }

    .edit-profile-page .breadcrumb a {
        color: #047857;
        text-decoration: none;
        font-weight: 600;
    }

    .edit-profile-page .breadcrumb-item.active {
        color: #6b7280;
    }

    /* Alert */
    .edit-profile-page .alert {
        border-radius: 12px;
        border: none;
        font-size: 13px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Cards */
    .edit-profile-page .card {
        background: #ffffff;
        border: 1px solid #e5ede8;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(11, 59, 44, 0.04);
        margin-bottom: 22px;
        overflow: hidden;
    }

    .edit-profile-page .card-header {
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

    .edit-profile-page .card-header i {
        color: #047857;
    }

    .edit-profile-page .card-body {
        padding: 22px;
    }

    /* Fields */
    .edit-profile-page .field {
        margin-bottom: 18px;
    }

    .edit-profile-page .field-label {
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

    .edit-profile-page .form-control,
    .edit-profile-page .form-select {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.15s ease;
    }

    .edit-profile-page .form-control:focus,
    .edit-profile-page .form-select:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.10);
        outline: none;
    }

    .edit-profile-page .form-control:disabled {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Photo preview */
    .edit-profile-page .photo-frame {
        width: 140px;
        height: 165px;
        border: 3px solid #ffffff;
        outline: 2px solid #d1e6da;
        border-radius: 12px;
        overflow: hidden;
        background: #f3f7f5;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 18px rgba(11, 59, 44, 0.08);
        margin: 0 auto;
    }

    .edit-profile-page .photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .edit-profile-page .photo-frame .placeholder {
        color: #9ca3af;
        font-size: 32px;
    }

    /* Document card */
    .edit-profile-page .doc-card {
        border: 1px solid #e5ede8;
        border-radius: 12px;
        padding: 18px;
        background: #f9fbfa;
        height: 100%;
    }

    .edit-profile-page .doc-card label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
        display: block;
    }

    .edit-profile-page .doc-current {
        font-size: 11px;
        color: #6b7280;
        margin-top: 6px;
    }

    .edit-profile-page .doc-current a {
        color: #047857;
        font-weight: 600;
        text-decoration: none;
    }

    .edit-profile-page .doc-current a:hover {
        text-decoration: underline;
    }

    /* Buttons */
    .edit-profile-page .btn {
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        padding: 11px 20px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .edit-profile-page .btn:hover {
        transform: translateY(-1px);
    }

    .edit-profile-page .btn-primary {
        background: linear-gradient(135deg, #047857, #059669);
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(4, 120, 87, 0.20);
    }

    .edit-profile-page .btn-primary:hover {
        color: #ffffff;
        box-shadow: 0 9px 22px rgba(4, 120, 87, 0.28);
    }

    .edit-profile-page .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .edit-profile-page .btn-secondary:hover {
        background: #e5e7eb;
        color: #111827;
    }

    /* Info banner */
    .edit-profile-page .info-banner {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        color: #92400e;
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 22px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="container-fluid px-4 edit-profile-page">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-edit me-2"></i> Edit Member Profile</h1>
            <p>Admin-only editing — you can modify any field on this approved profile.</p>
        </div>
        <div class="member-pill">
            <i class="fas fa-user-circle"></i>
            {{ trim(($profile->first_name ?? '') . ' ' . ($profile->surname ?? '')) ?: 'Member' }}
        </div>
    </div>

    {{-- BREADCRUMB --}}
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.admin_dashboard') }}"><i class="fas fa-home me-1"></i> Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.member.approved') }}">Approved Members</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.members.show', $profile->id) }}">
                {{ trim(($profile->first_name ?? '') . ' ' . ($profile->surname ?? '')) ?: 'Member' }}
            </a>
        </li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>

    {{-- INFO BANNER --}}
    <div class="info-banner">
        <i class="fas fa-shield-alt" style="font-size:16px;"></i>
        <span>
            <strong>Admin mode:</strong>
            You can edit any field on this approved profile. Changes are saved immediately.
        </span>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div>
                <strong><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.member.update-profile', $member->id) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ================================================================
                 LEFT COLUMN
            ================================================================= --}}
            <div class="col-lg-8">

                {{-- PERSONAL INFORMATION --}}
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-signature"></i> Surname
                                    </label>
                                    <input type="text" name="surname" class="form-control"
                                           value="{{ old('surname', $profile->surname) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-signature"></i> First Name
                                    </label>
                                    <input type="text" name="first_name" class="form-control"
                                           value="{{ old('first_name', $profile->first_name) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-signature"></i> Middle Name
                                    </label>
                                    <input type="text" name="middle_name" class="form-control"
                                           value="{{ old('middle_name', $profile->middle_name) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $profile->phone) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-calendar"></i> Date of Birth
                                    </label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                           value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-venus-mars"></i> Gender
                                    </label>
                                    <select name="gender" class="form-select">
                                        <option value="">— Select —</option>
                                        <option value="Male"   {{ old('gender', $profile->gender) === 'Male'   ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $profile->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-globe"></i> Nationality
                                    </label>
                                    <input type="text" name="nationality" class="form-control"
                                           value="{{ old('nationality', $profile->nationality) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-envelope"></i> Email (read-only)
                                    </label>
                                    <input type="text" class="form-control" disabled
                                           value="{{ $member->email ?? '—' }}">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>


                {{-- RESIDENTIAL ADDRESS --}}
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-map-marker-alt"></i> Residential Address
                    </div>
                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-12">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-home"></i> Address
                                    </label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-city"></i> City
                                    </label>
                                    <input type="text" name="city" class="form-control"
                                           value="{{ old('city', $profile->city) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-map"></i> State
                                    </label>
                                    <input type="text" name="state" class="form-control"
                                           value="{{ old('state', $profile->state) }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-location-arrow"></i> LGA
                                    </label>
                                    <input type="text" name="lga" class="form-control"
                                           value="{{ old('lga', $profile->lga) }}">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>


                {{-- BUSINESS INFORMATION --}}
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-building"></i> Business Information
                    </div>
                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-briefcase"></i> Business Name
                                    </label>
                                    <input type="text" name="business_name" class="form-control"
                                           value="{{ old('business_name', $profile->business_name) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-id-card"></i> Business Registration Number
                                    </label>
                                    <input type="text" name="business_registration_number" class="form-control"
                                           value="{{ old('business_registration_number', $profile->business_registration_number) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-tags"></i> Business Type
                                    </label>
                                    <input type="text" name="business_type" class="form-control"
                                           value="{{ old('business_type', $profile->business_type) }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="field">
                                    <label class="field-label">
                                        <i class="fas fa-map-marked-alt"></i> Business Address
                                    </label>
                                    <textarea name="business_address" class="form-control" rows="2">{{ old('business_address', $profile->business_address) }}</textarea>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>


                {{-- DOCUMENTS --}}
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-upload"></i> Uploaded Documents
                    </div>
                    <div class="card-body">

                        <div class="row g-4">

                            {{-- CAC CERTIFICATE --}}
                            <div class="col-md-6">
                                <div class="doc-card">
                                    <label><i class="fas fa-file-contract me-1"></i> CAC Certificate</label>
                                    <input type="file" name="cac_certificate" class="form-control"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($profile->cac_certificate)
                                        <div class="doc-current">
                                            Current:
                                            <a href="{{ asset('document/' . $profile->cac_certificate) }}"
                                               target="_blank">View file</a>
                                        </div>
                                    @else
                                        <div class="doc-current">No file uploaded.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- CAC PARTICULARS --}}
                            <div class="col-md-6">
                                <div class="doc-card">
                                    <label><i class="fas fa-users me-1"></i> CAC Particulars of Directors</label>
                                    <input type="file" name="cac_particulars_of_directors" class="form-control"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($profile->cac_particulars_of_directors)
                                        <div class="doc-current">
                                            Current:
                                            <a href="{{ asset('document/' . $profile->cac_particulars_of_directors) }}"
                                               target="_blank">View file</a>
                                        </div>
                                    @else
                                        <div class="doc-current">No file uploaded.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- NEPC EXPORT LICENSE --}}
                            <div class="col-md-6">
                                <div class="doc-card">
                                    <label><i class="fas fa-ship me-1"></i> NEPC Export License</label>
                                    <input type="file" name="nepc_export_license" class="form-control"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    @if ($profile->nepc_export_license)
                                        <div class="doc-current">
                                            Current:
                                            <a href="{{ asset('document/' . $profile->nepc_export_license) }}"
                                               target="_blank">View file</a>
                                        </div>
                                    @else
                                        <div class="doc-current">No file uploaded.</div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>


            {{-- ================================================================
                 RIGHT COLUMN
            ================================================================= --}}
            <div class="col-lg-4">

                {{-- PASSPORT PHOTOGRAPH --}}
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-camera"></i> Passport Photograph
                    </div>
                    <div class="card-body text-center">

                        <div class="photo-frame mb-3">
                            @if ($profile->photo)
                                <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                     id="photoPreview" alt="Member Photo">
                            @else
                                <div id="photoPlaceholder" class="placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                <img id="photoPreview" src="" alt=""
                                     style="display:none;width:100%;height:100%;object-fit:cover;">
                            @endif
                        </div>

                        <input type="file" name="photo" id="photoInput" class="form-control"
                               accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted d-block mt-2">
                            JPG, JPEG, PNG or WEBP. Max 2MB.
                        </small>

                    </div>
                </div>


                {{-- ACTIONS --}}
                <div class="card">
                    <div class="card-body d-flex flex-column gap-2">

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>

                        <a href="{{ route('admin.members.show', $profile->id) }}"
                           class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

{{-- Photo preview script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('photoInput');
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');

        if (!input || !preview) return;

        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';

                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    });
</script>

@endsection
