@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Approved Members
@endsection

@section('admin')

    {{-- =========================================================
         DataTables CSS
    ========================================================== --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        /* =========================================================
               SCOPED STYLES — only apply inside .approved-page
            ========================================================= */
        .approved-page {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            width: 100%;
        }

        .approved-page .wrap {
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        /* HEADER */
        .approved-page .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .approved-page .header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0b3b2c;
            line-height: 1.2;
        }

        .approved-page .header p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 6px;
        }

        .approved-page .btn-create {
            background: linear-gradient(135deg, #047857, #059669);
            color: #fff;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 11px;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 10px 25px rgba(4, 120, 87, 0.18);
            white-space: nowrap;
        }

        .approved-page .btn-create:hover {
            transform: translateY(-1px);
        }

        /* ALERTS */
        .approved-page .alert {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .approved-page .alert-success {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
        }

        .approved-page .alert-danger {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fca5a5;
        }

        /* CARD */
        .approved-page .card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #d9e5de;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            padding: 22px 22px 8px;
            width: 100%;
        }

        .approved-page .card-title {
            font-size: 18px;
            font-weight: 800;
            color: #0b3b2c;
            margin-bottom: 6px;
        }

        .approved-page .card-subtitle {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .approved-page .count-badge {
            display: inline-block;
            font-size: 12px;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 8px;
            font-weight: 700;
            vertical-align: middle;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        /* TABLE */
        .approved-page table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        .approved-page table.dataTable thead th {
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

        .approved-page table.dataTable tbody td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid #eef4f0;
            vertical-align: middle;
            color: #1f2937;
        }

        .approved-page table.dataTable tbody tr:hover {
            background: #f9fbfa;
        }

        /* BADGES */
        .approved-page .badge {
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

        .approved-page .badge-cat {
            background: #047857;
            color: #ffffff;
            border-color: #065f46;
        }

        .approved-page .badge-approved {
            background: #047857;
            color: #ffffff;
            border-color: #065f46;
        }

        .approved-page .badge-neutral {
            background: #e5e7eb;
            color: #374151;
            border-color: #d1d5db;
        }

        /* ACTION BUTTONS */
        .approved-page .actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .approved-page .btn {
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

        .approved-page .btn-view {
            background: #ffffff;
            color: #047857;
            border-color: #a7f3d0;
        }

        .approved-page .btn-view:hover {
            background: #ecfdf5;
            color: #047857;
        }

        .approved-page .btn-icon {
            font-size: 13px;
            line-height: 1;
        }

        /* DATATABLE OVERRIDES */
        .approved-page .dataTables_wrapper .dataTables_length,
        .approved-page .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
            font-size: 13px;
            color: #374151;
        }

        .approved-page .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 7px 12px;
            outline: none;
            font-size: 13px;
            margin-left: 8px;
            transition: all 0.15s ease;
        }

        .approved-page .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.10);
        }

        .approved-page .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 6px 10px;
            font-size: 13px;
            margin: 0 6px;
            outline: none;
        }

        .approved-page .dataTables_wrapper .dataTables_paginate {
            margin-top: 16px;
            padding-bottom: 14px;
        }

        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            border: 1px solid transparent !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            margin: 0 3px !important;
            color: #374151 !important;
            background: #f9fafb !important;
            cursor: pointer;
        }

        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #ecfdf5 !important;
            color: #047857 !important;
            border-color: #a7f3d0 !important;
        }

        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #047857 !important;
            color: #ffffff !important;
            border-color: #065f46 !important;
        }

        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .approved-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f3f4f6 !important;
            color: #9ca3af !important;
        }

        .approved-page .dataTables_wrapper .dataTables_info {
            font-size: 12px;
            color: #6b7280;
            padding-bottom: 14px;
        }

        .approved-page table.dataTable thead .sorting:before,
        .approved-page table.dataTable thead .sorting:after,
        .approved-page table.dataTable thead .sorting_asc:before,
        .approved-page table.dataTable thead .sorting_asc:after,
        .approved-page table.dataTable thead .sorting_desc:before,
        .approved-page table.dataTable thead .sorting_desc:after {
            color: #047857;
            opacity: 0.7;
        }
    </style>

    <div class="approved-page">
        <div class="wrap">

            {{-- HEADER --}}
            <div class="header">
                <div>
                    <h1 style="margin-left: 20px; margin-top: 10px;">
                        Approved Members
                    </h1>
                    <p style="margin-left: 20px;">
                        All members whose applications have been approved and assigned a membership number.
                    </p>
                </div>
                <a href="{{ route('admin.member.index') }}" class="btn-create">
                    ← Back to Pipeline
                </a>
            </div>

            {{-- ALERTS --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- CARD --}}
            <div class="card">
                <div class="card-title">
                    Approved Members
                    <span class="count-badge">
                        {{ $approvedMembers->count() }}
                    </span>
                </div>
                <div class="card-subtitle">
                    These members have been approved and assigned a membership number.
                    Click <strong>View</strong> to review their full profile and generated documents.
                </div>

                @if ($approvedMembers->count())
                    <table id="approvedMembersTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Membership No.</th>
                                <th>Approved At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvedMembers as $member)
                                <tr>
                                    <td>
                                        @if ($member->username)
                                            <strong>{{ $member->username }}</strong>
                                        @else
                                            <span style="color:#9ca3af;">no username</span>
                                        @endif
                                    </td>

                                    <td>{{ $member->phone ?? '—' }}</td>

                                    <td>
                                        @if ($member->membershipCategory)
                                            <span class="badge badge-cat">
                                                {{ $member->membershipCategory->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-neutral">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($member->profile?->membership_number)
                                            <span
                                                style="font-family:'Courier New', monospace; font-weight:700; color:#0b3b2c; font-size:12px;">
                                                {{ $member->profile->membership_number }}
                                            </span>
                                        @else
                                            <span style="color:#9ca3af;">—</span>
                                        @endif
                                    </td>

                                    <td data-order="{{ $member->profile?->approved_at?->timestamp }}">
                                        {{ $member->profile?->approved_at?->format('d M, Y H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-approved">Approved</span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.members.show', $member->profile->id) }}"
                                                class="btn btn-view">
                                                <span class="btn-icon">👁</span>
                                                View
                                            </a>
                                            <a href="{{ route('admin.member.edit-profile', $member->id) }}"
                                                class="btn btn-view"
                                                style="background:#fef3c7;color:#92400e;border-color:#f59e0b;">
                                                <span class="btn-icon">✏️</span>
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#6b7280; font-size:14px;">
                        No approved members yet.
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            if ($('#approvedMembersTable').length) {
                $('#approvedMembersTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    paging: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    searching: true,
                    ordering: true,
                    info: true,
                    columnDefs: [{
                            targets: [6],
                            orderable: false,
                            searchable: false
                        },
                        {
                            targets: [5],
                            searchable: false
                        },
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    language: {
                        search: "Search:",
                        searchPlaceholder: "Search approved members...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ approved members",
                        infoEmpty: "No approved members",
                        infoFiltered: "(filtered from _MAX_ total)",
                        zeroRecords: "No matching members found",
                        emptyTable: "No approved members yet.",
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
