@extends('admin.admin_dashboard')
@section('title', 'State Executive Positions')
@section('admin')

<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">State Executive Positions</h2>
        <a href="{{ route('admin.taskforce.index') }}" class="btn btn-outline-secondary">
            Back to Appointments
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ADD NEW --}}
    <div class="card mb-4">
        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
            <span>Add a State Position</span>

            <form method="POST" action="{{ route('admin.taskforce.state-positions.seed') }}"
                  onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Generating...'; return confirm('This will auto-generate all 27 standard positions for all 37 States/FCT. Proceed?');">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-magic me-1"></i> Auto-Generate All 27 Positions
                </button>
            </form>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.taskforce.state-positions.store') }}"
                  class="row g-3">
                @csrf

                <div class="col-md-3">
                    <label class="form-label">State</label>
                    <select name="state" class="form-select" required>
                        <option value="">— Select State —</option>
                        @foreach ($states as $s)
                            <option value="{{ $s }}" @selected(old('state') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Position Title</label>
                    <input type="text" name="position" class="form-control"
                           value="{{ old('position') }}"
                           placeholder="e.g. STATE CHAIRMAN" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code') }}"
                           placeholder="e.g. NMN-SEM-LAGOS-N01"
                           required>
                    <small class="text-muted">Unique. Uppercase.</small>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIST --}}
    <div class="card">
        <div class="card-header fw-bold">All State Positions</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>State</th>
                        <th>Position</th>
                        <th style="width:200px;">Code</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->state }}</td>
                            <td>{{ $p->position }}</td>
                            <td><code>{{ $p->code }}</code></td>
                            <td>
                                @if ($p->status === 'vacant')
                                    <span class="badge bg-success">Vacant</span>
                                @else
                                    <span class="badge bg-secondary">Occupied</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.taskforce.state-positions.destroy', $p) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Remove this position?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No state positions yet. Click the auto-generate button above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $positions->links() }}</div>
</div>

@endsection
