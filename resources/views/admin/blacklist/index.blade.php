@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Blacklisted Members
@endsection

@section('admin')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<style>
    .blacklist-page {
        font-family: 'Inter', sans-serif;
        color: #1f2937;
        width: 100%;
    }

    .blacklist-page .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .blacklist-page .header h1 {
        font-size: 24px;
        font-weight: 800;
        color: #0b3b2c;
        margin: 0;
    }

    .blacklist-page .header p {
        color: #6b7280;
        font-size: 13px;
        margin: 6px 0 0;
    }

    .blacklist-page .btn-create {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
        text-decoration: none;
        padding: 11px 20px;
        border-radius: 11px;
        font-weight: 700;
        font-size: 13px;
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.18);
        white-space: nowrap;
    }

    .blacklist-page .btn-create:hover {
        transform: translateY(-1px);
    }

    .blacklist-page .alert {
        padding: 13px 16px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .blacklist-page .alert-success {
        color: #065f46;
        background: #d1fae5;
        border: 1px solid #6ee7b7;
    }

    .blacklist-page .alert-danger {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
    }

    .blacklist-page .card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #d9e5de;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        padding: 22px 22px 8px;
        width: 100%;
    }

    .blacklist-page .card-title {
        font-size: 18px;
        font-weight: 800;
        color: #0b3b2c;
        margin-bottom: 6px;
    }

    .blacklist-page .card-subtitle {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 18px;
    }

    .blacklist-page .count-badge {
        display: inline-block;
        font-size: 12px;
        padding: 2px 10px;
        border-radius: 20px;
        margin-left: 8px;
        font-weight: 700;
        vertical-align: middle;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .blacklist-page table.dataTable {
        width: 100% !important;
        border-collapse: collapse;
    }

    .blacklist-page table.dataTable thead th {
        background: #f9fbfa;
        color: #346b53;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.6px;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #eef4f0;
        white-space: nowrap;
    }

    .blacklist-page table.dataTable tbody td {
        padding: 14px 16px;
        font-size: 13px;
        border-bottom: 1px solid #eef4f0;
        vertical-align: middle;
        color: #1f2937;
    }

    .blacklist-page table.dataTable tbody tr:hover {
        background: #f9fbfa;
    }

    .blacklist-page .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        line-height: 1.4;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .blacklist-page .badge-blacklisted {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .blacklist-page .badge-lifted {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .blacklist-page .actions {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .blacklist-page .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .blacklist-page .btn-view {
        background: #ffffff;
        color: #047857;
        border-color: #a7f3d0;
    }

    .blacklist-page .btn-view:hover {
        background: #ecfdf5;
        color: #047857;
    }

    .blacklist-page .btn-lift {
        background: linear-gradient(135deg, #047857, #059669);
        color: #ffffff;
        border: 1px solid #065f46;
        box-shadow: 0 4px 12px rgba(4, 120, 87, 0.20);
    }

    .blacklist-page .btn-lift:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(4, 120, 87, 0.28);
    }

    .blacklist-page .btn-delete {
        background: #ffffff;
        color: #dc2626;
        border-color: #fecaca;
    }

    .blacklist-page .btn-delete:hover {
        background: #fef2f2;
        color: #b91c1c;
    }

    .blacklist-page .btn-icon {
        font-size: 13px;
        line-height: 1;
    }

    .blacklist-page .photo-thumb {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .blacklist-page .photo-thumb-placeholder {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #f3f4f6;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: 1px solid #e5e7eb;
    }

    /* DataTables */
    .blacklist-page .dataTables_wrapper .dataTables_length,
    .blacklist-page .dataTables_wrapper .dataTables_filter {
        margin-bottom: 16px;
        font-size: 13px;
        color: #374151;
    }

    .blacklist-page .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 7px 12px;
        outline: none;
        font-size: 13px;
        margin-left: 8px;
    }

    .blacklist-page .dataTables_wrapper .dataTables_paginate {
        margin-top: 16px;
        padding-bottom: 14px;
    }

    .blacklist-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        padding: 6px 12px !important;
        margin: 0 3px !important;
        color: #374151 !important;
        background: #f9fafb !important;
        border: 1px solid transparent !important;
    }

    .blacklist-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #047857 !important;
        color: #ffffff !important;
    }
</style>

<div class="blacklist-page">
    <div class="wrap" style="padding: 0 20px;">

        {{-- HEADER --}}
        <div class="header">
            <div>
                <h1>Blacklisted Members</h1>
                <p>Members who have been blacklisted from NACPDEAN activities.</p>
            </div>
            <a href="{{ route('admin.blacklist.create') }}" class="btn-create">
                + Blacklist a Member
            </a>
        </div>

        {{-- FLASH --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- CARD --}}
        <div class="card">
            <div class="card-title">
                Blacklist Records
                <span class="count-badge">
                    {{ $blacklistedMembers->count() }}
                </span>
            </div>
            <div class="card-subtitle">
                Active blacklist entries are shown first.
                Lifted entries remain for record-keeping.
            </div>

            @if ($blacklistedMembers->count())
                <table id="blacklistTable" class="table table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Member</th>
                            <th>Membership No.</th>
                            <th>Company</th>
                            <th>State</th>
                            <th>Effective</th>
                            <th>Until</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blacklistedMembers as $entry)
                            <tr>
                                <td>
                                    @php
                                        /*
                                        |--------------------------------------------------------------------------
                                        | RESOLVE PHOTO URL
                                        |--------------------------------------------------------------------------
                                        |
                                        | Photo can live in one of two places:
                                        |
                                        |   1. public/uploads/blacklist/          (freshly uploaded here)
                                        |   2. public/uploads/member_profiles/    (pre-filled from member)
                                        |
                                        | We check both and use whichever exists.
                                        |
                                        */
                                        $photoUrl = null;

                                        if ($entry->photo) {

                                            $blacklistPath = public_path('uploads/blacklist/' . $entry->photo);
                                            $memberPath    = public_path('uploads/member_profiles/' . $entry->photo);

                                            if (file_exists($blacklistPath)) {
                                                $photoUrl = asset('uploads/blacklist/' . $entry->photo);
                                            } elseif (file_exists($memberPath)) {
                                                $photoUrl = asset('uploads/member_profiles/' . $entry->photo);
                                            }
                                        }
                                    @endphp

                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}"
                                             alt="Photo" class="photo-thumb">
                                    @else
                                        <div class="photo-thumb-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <strong>{{ $entry->member_name }}</strong>
                                </td>

                                <td>
                                    @if ($entry->membership_number)
                                        <span style="font-family:'Courier New', monospace; font-weight:700; color:#0b3b2c; font-size:12px;">
                                            {{ $entry->membership_number }}
                                        </span>
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </td>

                                <td>{{ $entry->company_name ?? '—' }}</td>
                                <td>{{ $entry->state ?? '—' }}</td>

                                <td data-order="{{ $entry->effective_date?->timestamp }}">
                                    {{ $entry->effective_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td data-order="{{ $entry->blacklisted_until?->timestamp ?? 0 }}">
                                    {{ $entry->blacklisted_until?->format('d M Y') ?? 'Indefinite' }}
                                </td>

                                <td>
                                    @if ($entry->status === 'blacklisted')
                                        <span class="badge badge-blacklisted">Blacklisted</span>
                                    @else
                                        <span class="badge badge-lifted">Lifted</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="actions">

                                        <form method="POST"
                                              action="{{ route('admin.blacklist.destroy', $entry->id) }}"
                                              style="display:inline;"
                                              onsubmit="return confirm('Permanently delete this blacklist record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete">
                                                <span class="btn-icon">🗑</span>
                                                Unblacklist
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:60px 20px; color:#6b7280; font-size:14px;">
                    No blacklisted members.
                </div>
            @endif
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        if ($('#blacklistTable').length) {
            $('#blacklistTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                searching: true,
                ordering: true,
                info: true,
                columnDefs: [
                    { targets: [8], orderable: false, searchable: false },
                    { targets: [7], searchable: false },
                ],
                order: [[5, 'desc']],
                language: {
                    search: "Search:",
                    searchPlaceholder: "Search blacklisted members...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No entries",
                    infoFiltered: "(filtered from _MAX_ total)",
                    zeroRecords: "No matching entries",
                    emptyTable: "No blacklisted members.",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Prev"
                    }
                }
            });
        }
    });
</script>

@endsection