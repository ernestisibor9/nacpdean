@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - Appointments')

@section('admin')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color:#0b3b2c;">Appointments</h2>
            <p class="text-muted mb-0">Task Force (National + State) and National Executive positions.</p>
        </div>
        <div>
            <a href="{{ route('admin.taskforce.create', ['type' => 'task_force']) }}"
               class="btn btn-success">
                + Task Force
            </a>
            <a href="{{ route('admin.taskforce.create', ['type' => 'national_executive']) }}"
               class="btn btn-outline-success">
                + National Executive
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="appointment_type" class="form-select">
                <option value="">All Types</option>
                <option value="task_force"          {{ request('appointment_type') === 'task_force' ? 'selected' : '' }}>Task Force</option>
                <option value="national_executive"  {{ request('appointment_type') === 'national_executive' ? 'selected' : '' }}>National Executive</option>
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
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:#f9fbfa;">
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Position</th>
                        <th>Level / State</th>
                        <th>Status</th>
                        <th>Appointed</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($appointments as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>
                                <strong>{{ $a->membership->user->username ?? '—' }}</strong>
                                @if ($a->membership->user->phone ?? null)
                                    <br><small class="text-muted">{{ $a->membership->user->phone }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($a->appointment_type === 'task_force')
                                    <span class="badge bg-info text-dark">Task Force</span>
                                @else
                                    <span class="badge bg-primary">National Exec</span>
                                @endif
                            </td>
                            <td>{{ $a->position }}</td>
                            <td>
                                @if ($a->appointment_type === 'task_force')
                                    {{ ucfirst($a->level ?? '—') }}
                                    @if ($a->state) — {{ $a->state }} @endif
                                @else
                                    —
                                @endif
                            </td>
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
                            <td>{{ $a->appointed_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">

                                @if ($a->status !== 'approved')
                                    <form method="POST"
                                          action="{{ route('admin.taskforce.approve', $a->id) }}"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">Approve</button>
                                    </form>
                                @endif

                                @if ($a->status !== 'rejected')
                                    <form method="POST"
                                          action="{{ route('admin.taskforce.reject', $a->id) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Reject this appointment?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                @endif

                                <form method="POST"
                                      action="{{ route('admin.taskforce.destroy', $a->id) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this appointment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-secondary">Delete</button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $appointments->links() }}</div>

</div>
@endsection
