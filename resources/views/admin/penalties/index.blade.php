@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - Penalties')

@section('admin')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0">Penalties Catalog</h1>
        <div>
            <a href="{{ route('admin.penalties.assignments') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> View Assignments
            </a>
            <a href="{{ route('admin.penalties.assign.form') }}" class="btn btn-outline-primary">
                <i class="fas fa-gavel"></i> Assign Penalty
            </a>
            <a href="{{ route('admin.penalties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Penalty
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Name</th>
                        <th>Action Required</th>
                        <th style="width:140px;">Amount</th>
                        <th style="width:100px;">Custom?</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:150px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penalties as $penalty)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $penalty->name }}
                                @if($penalty->description)
                                    <div class="text-muted small">{{ $penalty->description }}</div>
                                @endif
                            </td>
                            <td>{{ $penalty->action }}</td>
                            <td>₦{{ number_format($penalty->amount, 2) }}</td>
                            <td>
                                @if($penalty->is_custom_amount)
                                    <span class="badge bg-info">Yes</span>
                                @else
                                    <span class="badge bg-light text-dark">No</span>
                                @endif
                            </td>
                            <td>
                                @if($penalty->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.penalties.edit', $penalty) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.penalties.destroy', $penalty) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this penalty?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No penalties in the catalog yet.
                                <a href="{{ route('admin.penalties.create') }}">Create the first one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
