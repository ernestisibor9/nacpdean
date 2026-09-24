@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Blacklist a Member
@endsection

@section('admin')

<style>
    .blacklist-create {
        font-family: 'Inter', sans-serif;
        color: #1f2937;
        max-width: 900px;
        margin: 0 auto;
    }

    .blacklist-create .page-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 18px;
        padding: 26px 30px;
        color: #ffffff;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: 0 12px 30px rgba(220, 38, 38, 0.20);
    }

    .blacklist-create .page-header h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0;
    }

    .blacklist-create .page-header p {
        font-size: 13px;
        margin: 4px 0 0;
        opacity: 0.9;
    }

    .blacklist-create .breadcrumb {
        font-size: 13px;
        margin-bottom: 22px;
    }

    .blacklist-create .breadcrumb a {
        color: #047857;
        text-decoration: none;
        font-weight: 600;
    }

    .blacklist-create .breadcrumb-item.active {
        color: #6b7280;
    }

    .blacklist-create .alert {
        border-radius: 12px;
        border: none;
        font-size: 13px;
        padding: 14px 18px;
        margin-bottom: 20px;
    }

    .blacklist-create .alert-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .blacklist-create .card {
        background: #fff;
        border: 1px solid #e5ede8;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(11, 59, 44, 0.04);
        margin-bottom: 22px;
    }

    .blacklist-create .card-header {
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

    .blacklist-create .card-header i {
        color: #dc2626;
    }

    .blacklist-create .card-body {
        padding: 22px;
    }

    .blacklist-create .field {
        margin-bottom: 18px;
    }

    .blacklist-create .field-label {
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

    .blacklist-create .form-control {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.15s ease;
    }

    .blacklist-create .form-control:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10);
        outline: none;
    }

    .blacklist-create .form-control:disabled {
        background: #f3f4f6;
        color: #6b7280;
    }

    .blacklist-create .btn {
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
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .blacklist-create .btn-danger {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.20);
    }

    .blacklist-create .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 9px 22px rgba(220, 38, 38, 0.28);
    }

    .blacklist-create .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .blacklist-create .btn-secondary:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .blacklist-create .warning-banner {
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

<div class="blacklist-create">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h1><i class="fas fa-ban me-2"></i> Blacklist a Member</h1>
            <p>Add a member to the official NACPDEAN blacklist.</p>
        </div>
    </div>

    {{-- BREADCRUMB --}}
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.admin_dashboard') }}"><i class="fas fa-home me-1"></i> Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.blacklist.index') }}">Blacklisted Members</a>
        </li>
        <li class="breadcrumb-item active">Add</li>
    </ol>

    {{-- WARNING --}}
    <div class="warning-banner">
        <i class="fas fa-exclamation-triangle" style="font-size:16px;"></i>
        <span>
            <strong>Important:</strong>
            Blacklisting a member will publicly display their details on the NACPDEAN website.
            Use this action only with proper verification.
        </span>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.blacklist.store') }}"
          enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="member_id" value="{{ old('member_id', $prefill['member_id']) }}">

        <div class="card">
            <div class="card-header">
                <i class="fas fa-user"></i> Member Details
            </div>
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-user"></i> Member Name *</label>
                            <input type="text" name="member_name" class="form-control"
                                   value="{{ old('member_name', $prefill['member_name']) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-id-badge"></i> Membership Number</label>
                            <input type="text" name="membership_number" class="form-control"
                                   value="{{ old('membership_number', $prefill['membership_number']) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-briefcase"></i> Company Name</label>
                            <input type="text" name="company_name" class="form-control"
                                   value="{{ old('company_name', $prefill['company_name']) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-map"></i> State</label>
                            <input type="text" name="state" class="form-control"
                                   value="{{ old('state', $prefill['state']) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-calendar-plus"></i> Effective Date *</label>
                            <input type="date" name="effective_date" class="form-control"
                                   value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-calendar-times"></i> Blacklisted Until</label>
                            <input type="date" name="blacklisted_until" class="form-control"
                                   value="{{ old('blacklisted_until') }}">
                            <small class="text-muted">Leave blank for indefinite.</small>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-comment-alt"></i> Reason *</label>
                            <textarea name="reason" class="form-control" rows="4" required
                                      placeholder="Explain why this member is being blacklisted...">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="field">
                            <label class="field-label"><i class="fas fa-camera"></i> Photo (optional)</label>
                            <input type="file" name="photo" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                            @if (!empty($prefill['photo']))
                                <small class="text-muted">
                                    Pre-filled from the member's profile: {{ $prefill['photo'] }}
                                </small>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex flex-column gap-2">

                <button type="submit" class="btn btn-danger w-100"
                        onclick="return confirm('Blacklist this member?');">
                    <i class="fas fa-ban"></i>
                    Add to Blacklist
                </button>

                <a href="{{ route('admin.blacklist.index') }}"
                   class="btn btn-secondary w-100">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>

            </div>
        </div>

    </form>

</div>

@endsection