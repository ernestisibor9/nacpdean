@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                Officer Appointment
            </h3>

            <p class="text-muted mb-0">
                View officer appointment details and approval status.
            </p>

        </div>

        <a href="{{ route('admin.membership-officers.create') }}" class="btn btn-primary">
            New Officer Appointment
        </a>

    </div>


    {{-- SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- MEMBER INFORMATION --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Member Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <strong>Member Name</strong>

                    <div>
                        @php
                            $fullName = trim(
                                implode(
                                    ' ',
                                    array_filter([$profile?->first_name, $profile?->middle_name, $profile?->surname]),
                                ),
                            );

                            if (!$fullName) {
                                $fullName = $membership->user?->name ?? ($membership->user?->username ?? 'N/A');
                            }
                        @endphp

                        {{ $fullName }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Email</strong>

                    <div>
                        {{ $membership->user?->email ?? 'N/A' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Membership Number</strong>

                    <div>
                        <span class="badge bg-dark">
                            {{ $membership->membership_number }}
                        </span>
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Membership Category</strong>

                    <div>
                        {{ $membership->membershipCategory?->name ?? 'N/A' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Membership Status</strong>

                    <div>

                        @if ($membership->status === 'active')
                            <span class="badge bg-success">
                                Active
                            </span>
                        @else
                            <span class="badge bg-danger">
                                {{ ucfirst($membership->status) }}
                            </span>
                        @endif

                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Current Membership Position</strong>

                    <div>
                        {{ $membership->membership_position ?? 'Not assigned' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- APPOINTMENT INFORMATION --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Appointment Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- TYPE --}}
                <div class="col-md-6 mb-3">

                    <strong>Appointment Type</strong>

                    <div>

                        @if ($appointment->appointment_type === 'national_executive')
                            <span class="badge bg-primary">
                                National Executive
                            </span>
                        @else
                            <span class="badge bg-info text-dark">
                                Task Force
                            </span>
                        @endif

                    </div>

                </div>


                {{-- STATUS --}}
                <div class="col-md-6 mb-3">

                    <strong>Status</strong>

                    <div>

                        @if ($appointment->status === 'pending')
                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>
                        @elseif($appointment->status === 'approved')
                            <span class="badge bg-success">
                                Approved
                            </span>
                        @elseif($appointment->status === 'rejected')
                            <span class="badge bg-danger">
                                Rejected
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        @endif

                    </div>

                </div>


                {{-- NEM --}}
                @if ($appointment->appointment_type === 'national_executive')
                    <div class="col-md-6 mb-3">

                        <strong>NEM Code</strong>

                        <div>

                            <span class="badge bg-dark">
                                {{ $appointment->executive_id }}
                            </span>

                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>National Executive Position</strong>

                        <div>
                            {{ $appointment->position ?? ($executivePosition?->position ?? 'N/A') }}
                        </div>

                    </div>
                @endif


                {{-- TASK FORCE --}}
                @if ($appointment->appointment_type === 'task_force')
                    <div class="col-md-6 mb-3">

                        <strong>Task Force ID</strong>

                        <div>
                            {{ $appointment->taskforce_id ?? 'N/A' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Task Force Position</strong>

                        <div>
                            {{ $appointment->position ?? 'N/A' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Level</strong>

                        <div>
                            {{ $appointment->level ? ucfirst($appointment->level) : 'N/A' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>State</strong>

                        <div>
                            {{ $appointment->state ?? 'Not applicable' }}
                        </div>

                    </div>
                @endif


                {{-- CREATED --}}
                <div class="col-md-6 mb-3">

                    <strong>Application Date</strong>

                    <div>
                        {{ $appointment->created_at?->format('d M Y H:i') ?? 'N/A' }}
                    </div>

                </div>


                {{-- APPOINTED --}}
                @if ($appointment->appointed_at)
                    <div class="col-md-6 mb-3">

                        <strong>Appointed At</strong>

                        <div>
                            {{ $appointment->appointed_at->format('d M Y H:i') }}
                        </div>

                    </div>
                @endif


                {{-- APPROVED --}}
                @if ($appointment->approved_at)
                    <div class="col-md-6 mb-3">

                        <strong>Approved At</strong>

                        <div>
                            {{ $appointment->approved_at->format('d M Y H:i') }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <strong>Approved By</strong>

                        <div>
                            {{ $appointment->approved_by ?? 'N/A' }}
                        </div>

                    </div>
                @endif


                {{-- REJECTION --}}
                @if ($appointment->status === 'rejected')
                    <div class="col-12 mb-3">

                        <strong>
                            Rejection Reason
                        </strong>

                        <div class="alert alert-danger mt-2 mb-0">
                            {{ $appointment->rejection_reason ?? 'No reason provided.' }}
                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>


    {{-- CURRENT OFFICER INFORMATION --}}
    @if ($membership->executive_id || $membership->taskforce_id)
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Current Officer Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    @if ($membership->executive_id)
                        <div class="col-md-6 mb-3">

                            <strong>National Executive ID</strong>

                            <div>
                                <span class="badge bg-primary">
                                    {{ $membership->executive_id }}
                                </span>
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <strong>National Executive Position</strong>

                            <div>
                                {{ $membership->membership_position ?? 'N/A' }}
                            </div>

                        </div>
                    @endif


                    @if ($membership->taskforce_id)
                        <div class="col-md-6 mb-3">

                            <strong>Task Force ID</strong>

                            <div>
                                {{ $membership->taskforce_id }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <strong>Task Force Position</strong>

                            <div>
                                {{ $membership->taskforce_position ?? 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <strong>Task Force Level</strong>

                            <div>
                                {{ ucfirst($membership->taskforce_level ?? 'N/A') }}
                            </div>

                        </div>


                        <div class="col-md-6 mb-3">

                            <strong>Task Force State</strong>

                            <div>
                                {{ $membership->taskforce_state ?? 'Not applicable' }}
                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>
    @endif


    {{-- MEMBERSHIP CARDS --}}
    @if ($cards->isNotEmpty())
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Membership Cards
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    @foreach ($cards as $card)
                        <div class="col-md-6 mb-4">

                            <div class="border rounded p-4 h-100">

                                {{-- CARD TYPE --}}
                                <div class="mb-3">

                                    <strong>Card Type</strong>

                                    <div class="mt-1">

                                        @if ($card->card_type === 'membership')
                                            <span class="badge bg-secondary">
                                                Membership Card
                                            </span>
                                        @elseif ($card->card_type === 'national_executive')
                                            <span class="badge bg-primary">
                                                National Executive Card
                                            </span>
                                        @elseif ($card->card_type === 'task_force')
                                            <span class="badge bg-info text-dark">
                                                Task Force Card
                                            </span>
                                        @else
                                            <span class="badge bg-dark">
                                                {{ ucfirst(str_replace('_', ' ', $card->card_type)) }}
                                            </span>
                                        @endif

                                    </div>

                                </div>


                                {{-- CARD NUMBER --}}
                                <div class="mb-3">

                                    <strong>Card Number</strong>

                                    <div>
                                        <span class="badge bg-dark">
                                            {{ $card->card_number }}
                                        </span>
                                    </div>

                                </div>


                                {{-- MEMBERSHIP NUMBER --}}
                                <div class="mb-3">

                                    <strong>Card Membership Number</strong>

                                    <div>
                                        {{ $card->membership_number ?? 'N/A' }}
                                    </div>

                                </div>


                                {{-- POSITION --}}
                                @if ($card->card_type === 'national_executive')
                                    <div class="mb-3">

                                        <strong>National Executive Position</strong>

                                        <div>
                                            {{ $membership->membership_position ?? 'N/A' }}
                                        </div>

                                    </div>

                                    <div class="mb-3">

                                        <strong>NEM Code</strong>

                                        <div>
                                            <span class="badge bg-primary">
                                                {{ $membership->executive_id ?? 'N/A' }}
                                            </span>
                                        </div>

                                    </div>
                                @endif


                                {{-- TASK FORCE --}}
                                @if ($card->card_type === 'task_force')
                                    <div class="mb-3">

                                        <strong>Task Force ID</strong>

                                        <div>
                                            {{ $membership->taskforce_id ?? 'N/A' }}
                                        </div>

                                    </div>

                                    <div class="mb-3">

                                        <strong>Task Force Position</strong>

                                        <div>
                                            {{ $membership->taskforce_position ?? 'N/A' }}
                                        </div>

                                    </div>

                                    <div class="mb-3">

                                        <strong>Level</strong>

                                        <div>
                                            {{ ucfirst($membership->taskforce_level ?? 'N/A') }}
                                        </div>

                                    </div>

                                    @if ($membership->taskforce_level === 'state')
                                        <div class="mb-3">

                                            <strong>State</strong>

                                            <div>
                                                {{ $membership->taskforce_state ?? 'N/A' }}
                                            </div>

                                        </div>
                                    @endif
                                @endif


                                {{-- STATUS --}}
                                <div class="mb-3">

                                    <strong>Card Status</strong>

                                    <div>

                                        @if ($card->status === 'active')
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @elseif ($card->status === 'expired')
                                            <span class="badge bg-warning text-dark">
                                                Expired
                                            </span>
                                        @elseif ($card->status === 'revoked')
                                            <span class="badge bg-danger">
                                                Revoked
                                            </span>
                                        @elseif ($card->status === 'replaced')
                                            <span class="badge bg-secondary">
                                                Replaced
                                            </span>
                                        @else
                                            <span class="badge bg-dark">
                                                {{ ucfirst($card->status) }}
                                            </span>
                                        @endif

                                    </div>

                                </div>


                                {{-- ISSUED --}}
                                <div class="mb-3">

                                    <strong>Issued</strong>

                                    <div>
                                        {{ $card->issued_at?->format('d M Y') ?? 'N/A' }}
                                    </div>

                                </div>


                                {{-- EXPIRES --}}
                                <div>

                                    <strong>Expires</strong>

                                    <div>
                                        {{ $card->expires_at?->format('d M Y') ?? 'N/A' }}
                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>
    @else
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Membership Cards
                </h5>
            </div>

            <div class="card-body">

                <div class="alert alert-info mb-0">
                    No active membership cards found for this member.
                </div>

            </div>

        </div>
    @endif


    {{-- APPROVAL / REJECTION --}}
    @if ($appointment->status === 'pending')
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Appointment Decision
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- APPROVE --}}
                    <div class="col-md-6 mb-3">

                        <div class="border rounded p-4 h-100">

                            <h5>
                                Approve Appointment
                            </h5>

                            <p class="text-muted">
                                Approving this appointment will update the membership officer
                                information and regenerate the membership documents with the
                                current officer details.
                            </p>

                            <form method="POST"
                                action="{{ route('admin.membership-officers.approve', $appointment->id) }}"
                                onsubmit="return confirm(
                                    'Are you sure you want to approve this officer appointment?'
                                );">

                                @csrf

                                <button type="submit" class="btn btn-success">
                                    Approve Appointment
                                </button>

                            </form>

                        </div>

                    </div>


                    {{-- REJECT --}}
                    <div class="col-md-6 mb-3">

                        <div class="border rounded p-4 h-100">

                            <h5>
                                Reject Appointment
                            </h5>

                            <form method="POST"
                                action="{{ route('admin.membership-officers.reject', $appointment->id) }}">

                                @csrf

                                <div class="mb-3">

                                    <label for="rejection_reason" class="form-label fw-bold">
                                        Rejection Reason
                                    </label>

                                    <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" required
                                        placeholder="Enter reason for rejecting this appointment">{{ old('rejection_reason') }}</textarea>

                                </div>


                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm(
                                        'Are you sure you want to reject this officer appointment?'
                                    );">
                                    Reject Appointment
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    @endif


    {{-- GENERATED DOCUMENTS --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <h5 class="mb-0">
                Generated Membership Documents
            </h5>

        </div>

        <div class="card-body">

            @if ($documents->isEmpty())
                <div class="alert alert-info mb-0">
                    No generated documents found for this member.
                </div>
            @else
                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Document
                                </th>

                                <th>
                                    Document Number
                                </th>

                                <th>
                                    Tracking Code
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Issued
                                </th>

                                <th>
                                    Expires
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($documents as $document)
                                <tr>

                                    <td>
                                        {{ $document->document?->name ?? 'Document #' . $document->document_id }}
                                    </td>

                                    <td>
                                        {{ $document->document_number }}
                                    </td>

                                    <td>
                                        {{ $document->tracking_code }}
                                    </td>

                                    <td>

                                        @if ($document->status === 'active')
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @elseif($document->status === 'replaced')
                                            <span class="badge bg-secondary">
                                                Replaced
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                {{ ucfirst($document->status) }}
                                            </span>
                                        @endif

                                    </td>

                                    <td>
                                        {{ $document->issued_at?->format('d M Y') ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $document->expires_at?->format('d M Y') ?? 'N/A' }}
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>
            @endif

        </div>

    </div>


    {{-- BACK --}}
    <div class="mb-4">

        <a href="{{ route('admin.membership-officers.create') }}" class="btn btn-secondary">
            Create Another Appointment
        </a>

    </div>

</div>


@endsection
