@extends('admin.admin_dashboard')

@section('title')
    NACPDEAN - Member Applications
@endsection

@section('admin')

    {{-- =========================================================
         DataTables CSS
    ========================================================== --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        /* =========================================================
               SCOPED STYLES — only apply inside .members-page
            ========================================================= */
        .members-page {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            width: 100%;
        }

        .members-page .wrap {
            width: 100%;
            max-width: 100%;
            margin: 0;
        }

        /* HEADER */
        .members-page .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .members-page .header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0b3b2c;
            line-height: 1.2;
        }

        .members-page .header p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 6px;
        }

        .members-page .btn-create {
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

        .members-page .btn-create:hover {
            transform: translateY(-1px);
        }

        /* ALERTS */
        .members-page .alert {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .members-page .alert-success {
            color: #065f46;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
        }

        .members-page .alert-danger {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fca5a5;
        }

        /* CARD */
        .members-page .card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #d9e5de;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            padding: 22px 22px 8px;
            width: 100%;
            margin-bottom: 30px;
        }

        .members-page .card-title {
            font-size: 18px;
            font-weight: 800;
            color: #0b3b2c;
            margin-bottom: 6px;
        }

        .members-page .card-subtitle {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .members-page .count-badge {
            display: inline-block;
            font-size: 12px;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 8px;
            font-weight: 700;
            vertical-align: middle;
        }

        .members-page .count-unpaid {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .members-page .count-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .members-page .count-submitted {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #60a5fa;
        }

        .members-page .count-draft {
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #a78bfa;
        }

        /* TABLE */
        .members-page table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        .members-page table.dataTable thead th {
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

        .members-page table.dataTable tbody td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid #eef4f0;
            vertical-align: middle;
            color: #1f2937;
        }

        .members-page table.dataTable tbody tr:hover {
            background: #f9fbfa;
        }

        /* =========================================================
               BADGES
            ========================================================= */
        .members-page .badge {
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

        .members-page .badge-cat {
            background: #047857;
            color: #ffffff;
            border-color: #065f46;
        }

        .members-page .badge-unpaid {
            background: #f59e0b;
            color: #3f2d00;
            border-color: #d97706;
        }

        .members-page .badge-paid {
            background: #047857;
            color: #ffffff;
            border-color: #065f46;
        }

        .members-page .badge-submitted {
            background: #2563eb;
            color: #ffffff;
            border-color: #1d4ed8;
        }

        .members-page .badge-draft {
            background: #8b5cf6;
            color: #ffffff;
            border-color: #6d28d9;
        }

        .members-page .badge-neutral {
            background: #e5e7eb;
            color: #374151;
            border-color: #d1d5db;
        }

        /* =========================================================
               ACTION BUTTONS
            ========================================================= */
        .members-page .actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .members-page .btn {
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

        .members-page .btn-approve {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #ffffff;
            border: 1px solid #047857;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.20);
        }

        .members-page .btn-approve:hover {
            background: linear-gradient(135deg, #047857, #059669);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.28);
            transform: translateY(-1px);
        }

        .members-page .btn-pay {
            background: linear-gradient(135deg, #047857, #059669);
            color: #fff;
            box-shadow: 0 6px 14px rgba(4, 120, 87, 0.18);
        }

        .members-page .btn-pay:hover {
            transform: translateY(-1px);
            box-shadow: 0 9px 18px rgba(4, 120, 87, 0.25);
            color: #fff;
        }

        .members-page .btn-view {
            background: #ffffff;
            color: #047857;
            border-color: #a7f3d0;
        }

        .members-page .btn-view:hover {
            background: #ecfdf5;
            color: #047857;
        }

        .members-page .btn-icon {
            font-size: 13px;
            line-height: 1;
        }

        /* =========================================================
               DATATABLE OVERRIDES — match brand
            ========================================================= */
        .members-page .dataTables_wrapper .dataTables_length,
        .members-page .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
            font-size: 13px;
            color: #374151;
        }

        .members-page .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 7px 12px;
            outline: none;
            font-size: 13px;
            margin-left: 8px;
            transition: all 0.15s ease;
        }

        .members-page .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.10);
        }

        .members-page .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 6px 10px;
            font-size: 13px;
            margin: 0 6px;
            outline: none;
        }

        /* Pagination */
        .members-page .dataTables_wrapper .dataTables_paginate {
            margin-top: 16px;
            padding-bottom: 14px;
        }

        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button {
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

        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #ecfdf5 !important;
            color: #047857 !important;
            border-color: #a7f3d0 !important;
        }

        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #047857 !important;
            color: #ffffff !important;
            border-color: #065f46 !important;
        }

        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .members-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f3f4f6 !important;
            color: #9ca3af !important;
        }

        /* Info text */
        .members-page .dataTables_wrapper .dataTables_info {
            font-size: 12px;
            color: #6b7280;
            padding-bottom: 14px;
        }

        /* Sort arrows color */
        .members-page table.dataTable thead .sorting:before,
        .members-page table.dataTable thead .sorting:after,
        .members-page table.dataTable thead .sorting_asc:before,
        .members-page table.dataTable thead .sorting_asc:after,
        .members-page table.dataTable thead .sorting_desc:before,
        .members-page table.dataTable thead .sorting_desc:after {
            color: #047857;
            opacity: 0.7;
        }
    </style>

    <div class="members-page">
        <div class="wrap">

            {{-- =========================================================
                 HEADER
            ========================================================== --}}
            <div class="header">
                <div>
                    <h1 style="margin-left: 20px; margin-top:10px;">
                        Member Onboarding Pipeline
                    </h1>
                    <p style="margin-left: 20px;">
                        Track new members from account creation through payment, profile completion and submission.
                    </p>
                </div>
                <a href="{{ route('admin.member.create') }}" class="btn-create">
                    + Create Member Account
                </a>
            </div>

            {{-- =========================================================
                 ALERTS
            ========================================================== --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- =========================================================
                 LIST 1 — UNPAID MEMBERS
            ========================================================== --}}
            <div class="card">
                <div class="card-title">
                    1. Unpaid Members — Membership Fee Pending
                    <span class="count-badge count-unpaid">
                        {{ $newMembers->count() }}
                    </span>
                </div>
                <div class="card-subtitle">
                    These accounts have been created but the annual membership fee has not been paid yet.
                    Click <strong>Pay</strong> to complete the payment on the member's behalf.
                </div>

                @if ($newMembers->count())
                    <table id="membersTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($newMembers as $member)
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

                                    <td>{{ ucfirst($member->member_type ?? '—') }}</td>

                                    <td data-order="{{ $member->created_at?->timestamp }}">
                                        {{ $member->created_at?->format('d M, Y H:i') }}
                                    </td>

                                    <td>
                                        <span class="badge badge-unpaid">Unpaid</span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.member.pay', $member->id) }}" class="btn btn-pay">
                                                <span class="btn-icon">💳</span>
                                                Pay
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#6b7280; font-size:14px;">
                        No unpaid members. 🎉
                    </div>
                @endif
            </div>

            {{-- =========================================================
                 LIST 2 — PAID MEMBERS WITHOUT PROFILE
            ========================================================== --}}
            <div class="card">
                <div class="card-title">
                    2. Paid Members — Profile Pending
                    <span class="count-badge count-paid">
                        {{ $paidMembers->count() }}
                    </span>
                </div>
                <div class="card-subtitle">
                    Membership fee has been paid, but the member's profile has not been filled yet.
                    Click <strong>Complete Profile</strong> to fill it on the member's behalf.
                </div>

                @if ($paidMembers->count())
                    <table id="paidMembersTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paidMembers as $member)
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

                                    <td>{{ ucfirst($member->member_type ?? '—') }}</td>

                                    <td data-order="{{ $member->created_at?->timestamp }}">
                                        {{ $member->created_at?->format('d M, Y H:i') }}
                                    </td>

                                    <td>
                                        <span class="badge badge-paid">Paid</span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.member.complete-profile', $member->id) }}"
                                                class="btn btn-pay">
                                                <span class="btn-icon">📝</span>
                                                Complete Profile
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#6b7280; font-size:14px;">
                        No paid members pending profile. 🎉
                    </div>
                @endif
            </div>

            {{-- =========================================================
                 LIST 3 — PAID MEMBERS WITH DRAFT PROFILE (NOT SUBMITTED)
            ========================================================== --}}
            <div class="card">
                <div class="card-title">
                    3. Paid Members — Profile Draft (Not Yet Submitted)
                    <span class="count-badge count-draft">
                        {{ $draftMembers->count() }}
                    </span>
                </div>
                <div class="card-subtitle">
                    The profile has been started but not yet submitted for review.
                    Click <strong>Continue Profile</strong> to resume filling it on the member's behalf.
                </div>

                @if ($draftMembers->count())
                    <table id="draftMembersTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Last Updated</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($draftMembers as $member)
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

                                    <td>{{ ucfirst($member->member_type ?? '—') }}</td>

                                    <td data-order="{{ $member->profile?->updated_at?->timestamp }}">
                                        {{ $member->profile?->updated_at?->format('d M, Y H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-draft">Draft</span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.member.complete-profile', $member->id) }}"
                                                class="btn btn-pay">
                                                <span class="btn-icon">📝</span>
                                                Continue Profile
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#6b7280; font-size:14px;">
                        No draft profiles. 🎉
                    </div>
                @endif
            </div>

            {{-- =========================================================
                 LIST 4 — PAID MEMBERS WITH SUBMITTED PROFILE
            ========================================================== --}}
            <div class="card">
                <div class="card-title">
                    4. Paid Members — Profile Submitted
                    <span class="count-badge count-submitted">
                        {{ $submittedMembers->count() }}
                    </span>
                </div>
                <div class="card-subtitle">
                    These members have paid and submitted their profile. Their applications are ready for review.
                </div>

                @if ($submittedMembers->count())
                    <table id="submittedMembersTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submittedMembers as $member)
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

                                    <td>{{ ucfirst($member->member_type ?? '—') }}</td>

                                    <td data-order="{{ $member->profile?->submitted_at?->timestamp }}">
                                        {{ $member->profile?->submitted_at?->format('d M, Y H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge badge-submitted">Submitted</span>
                                    </td>

                                    <td>
                                        <div class="actions">
                                            {{-- VIEW --}}
                                            <a href="{{ route('admin.members.show', $member->profile->id) }}"
                                                class="btn btn-view">
                                                <span class="btn-icon">👁</span>
                                                View
                                            </a>

                                            {{-- APPROVE --}}
                                            <form method="POST"
                                                action="{{ route('admin.members.approve', $member->profile->id) }}"
                                                style="display:inline;"
                                                onsubmit="return confirm('Approve this application?\n\nThis will:\n• Assign a membership number\n• Create the membership record\n• Generate the membership card\n• Generate all membership documents\n\nContinue?');">
                                                @csrf
                                                <button type="submit" class="btn btn-approve">
                                                    <span class="btn-icon">✓</span>
                                                    Approve
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
                        No submitted profiles yet.
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- =========================================================
         DataTables JS (jQuery + DataTables core + Bootstrap 5 + Responsive)
    ========================================================== --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            // =========================================================
            // Common DataTables options
            // =========================================================
            const commonOptions = {
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
                        searchable: false,
                    },
                    {
                        targets: [5],
                        searchable: false,
                    },
                ],
                order: [
                    [4, 'desc']
                ],
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Prev"
                }
            };

            // =========================================================
            // LIST 1 — UNPAID MEMBERS
            // =========================================================
            if ($('#membersTable').length) {
                $('#membersTable').DataTable($.extend({}, commonOptions, {
                    language: {
                        search: "Search:",
                        searchPlaceholder: "Search unpaid members...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ unpaid members",
                        infoEmpty: "No unpaid members",
                        infoFiltered: "(filtered from _MAX_ total)",
                        zeroRecords: "No matching members found",
                        emptyTable: "No unpaid members. 🎉",
                        paginate: commonOptions.paginate
                    }
                }));
            }

            // =========================================================
            // LIST 2 — PAID MEMBERS WITHOUT PROFILE
            // =========================================================
            if ($('#paidMembersTable').length) {
                $('#paidMembersTable').DataTable($.extend({}, commonOptions, {
                    language: {
                        search: "Search:",
                        searchPlaceholder: "Search paid members...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ paid members",
                        infoEmpty: "No paid members",
                        infoFiltered: "(filtered from _MAX_ total)",
                        zeroRecords: "No matching members found",
                        emptyTable: "No paid members pending profile. 🎉",
                        paginate: commonOptions.paginate
                    }
                }));
            }

            // =========================================================
            // LIST 3 — PAID MEMBERS WITH DRAFT PROFILE
            // =========================================================
            if ($('#draftMembersTable').length) {
                $('#draftMembersTable').DataTable($.extend({}, commonOptions, {
                    language: {
                        search: "Search:",
                        searchPlaceholder: "Search draft profiles...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ draft profiles",
                        infoEmpty: "No draft profiles",
                        infoFiltered: "(filtered from _MAX_ total)",
                        zeroRecords: "No matching drafts found",
                        emptyTable: "No draft profiles. 🎉",
                        paginate: commonOptions.paginate
                    }
                }));
            }

            // =========================================================
            // LIST 4 — PAID MEMBERS WITH SUBMITTED PROFILE
            // =========================================================
            if ($('#submittedMembersTable').length) {
                $('#submittedMembersTable').DataTable($.extend({}, commonOptions, {
                    language: {
                        search: "Search:",
                        searchPlaceholder: "Search submitted members...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ submitted members",
                        infoEmpty: "No submitted members",
                        infoFiltered: "(filtered from _MAX_ total)",
                        zeroRecords: "No matching members found",
                        emptyTable: "No submitted profiles yet.",
                        paginate: commonOptions.paginate
                    }
                }));
            }

        });
    </script>

@endsection
