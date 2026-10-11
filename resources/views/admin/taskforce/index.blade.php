@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - Appointments')

@section('admin')
<div class="container-fluid py-4">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color:#0b3b2c;">Appointments</h2>
            <p class="text-muted mb-0">
                Task Force (National + State), National Executive, and State Executive positions.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.taskforce.create', ['type' => 'task_force']) }}"
               class="btn btn-success">
                <i class="fas fa-plus"></i> Task Force
            </a>
            <a href="{{ route('admin.taskforce.create', ['type' => 'national_executive']) }}"
               class="btn btn-outline-success">
                <i class="fas fa-plus"></i> National Executive
            </a>
            <a href="{{ route('admin.taskforce.create', ['type' => 'state_executive']) }}"
               class="btn btn-outline-success">
                <i class="fas fa-plus"></i> State Executive
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- =========================================================
         FILTERS
    ========================================================== --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="appointment_type" class="form-select">
                <option value="">All Types</option>

                <option value="task_force"
                    {{ request('appointment_type') === 'task_force' ? 'selected' : '' }}>
                    Task Force
                </option>

                <option value="national_executive"
                    {{ request('appointment_type') === 'national_executive' ? 'selected' : '' }}>
                    National Executive
                </option>

                <option value="state_executive"
                    {{ request('appointment_type') === 'state_executive' ? 'selected' : '' }}>
                    State Executive
                </option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="level" class="form-select">
                <option value="">Any Level</option>
                <option value="national" {{ request('level') === 'national' ? 'selected' : '' }}>National</option>
                <option value="state"    {{ request('level') === 'state'    ? 'selected' : '' }}>State</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Any Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('admin.taskforce.index') }}"
               class="btn btn-light w-100">
                Reset
            </a>
        </div>
    </form>

    {{-- =========================================================
         APPOINTMENTS TABLE
    ========================================================== --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background:#f9fbfa;">
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Member</th>
                            <th style="width:140px;">Type</th>
                            <th>Position</th>
                            <th style="width:160px;">Level / State</th>
                            <th style="width:110px;">Status</th>
                            <th style="width:120px;">Appointed</th>
                            <th class="text-end" style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $a)
                            <tr>
                                <td>{{ $a->id }}</td>

                                {{-- MEMBER --}}
                                <td>
                                    <strong>{{ $a->membership->user->username ?? '—' }}</strong>

                                    @if ($a->membership->user->phone ?? null)
                                        <br><small class="text-muted">{{ $a->membership->user->phone }}</small>
                                    @endif
                                </td>

                                {{-- TYPE --}}
                                <td>
                                    @if ($a->appointment_type === 'task_force')
                                        <span class="badge bg-info text-dark">Task Force</span>

                                    @elseif ($a->appointment_type === 'national_executive')
                                        <span class="badge bg-primary">National Exec</span>

                                    @elseif ($a->appointment_type === 'state_executive')
                                        <span class="badge bg-success">State Exec</span>

                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $a->appointment_type)) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- POSITION --}}
                                <td>
                                    <div class="fw-semibold">{{ $a->position }}</div>

                                    @if ($a->executive_id)
                                        <small class="text-muted">Code: {{ $a->executive_id }}</small>
                                    @endif

                                    @if ($a->taskforce_id)
                                        <small class="text-muted">TF ID: {{ $a->taskforce_id }}</small>
                                    @endif
                                </td>

                                {{-- LEVEL / STATE --}}
                                <td>
                                    @if ($a->appointment_type === 'state_executive')

                                        {{-- State Executive: always show the state --}}
                                        @if ($a->state)
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-map-marker-alt text-success"></i>
                                                {{ $a->state }}
                                            </span>
                                        @else
                                            —
                                        @endif

                                    @elseif ($a->appointment_type === 'task_force')

                                        {{-- Task Force: show level, and state when applicable --}}
                                        {{ ucfirst($a->level ?? '—') }}

                                        @if ($a->level === 'state' && $a->state)
                                            <br>
                                            <small class="text-muted">{{ $a->state }}</small>
                                        @endif

                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if ($a->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($a->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif ($a->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($a->status) }}</span>
                                    @endif
                                </td>

                                {{-- APPOINTED DATE --}}
                                <td>{{ $a->appointed_at?->format('d M Y') ?? '—' }}</td>

                                {{-- ACTIONS --}}
                                <td class="text-end">

                                    @if ($a->status !== 'approved')
                                        <form method="POST"
                                              action="{{ route('admin.taskforce.approve', $a->id) }}"
                                              class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if ($a->status !== 'rejected')
                                        <form method="POST"
                                              action="{{ route('admin.taskforce.reject', $a->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Reject this appointment?');">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('admin.taskforce.destroy', $a->id) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this appointment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-secondary">
                                            Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No appointments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================================================
         PAGINATION
    ========================================================== --}}
    <div class="mt-3">
        {{ $appointments->links() }}
    </div>

</div>
@endsection
