@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - Assigned Penalties')

@section('admin')


<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0">Penalty Assignments</h1>
        <div>
            <a href="{{ route('admin.penalties.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> View Catalog
            </a>
            <a href="{{ route('admin.penalties.assign.form') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Assign New Penalty
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
                <div class="col-auto">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                        <option value="paid"   @selected(request('status') === 'paid')>Paid</option>
                    </select>
                </div>
            </form>

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Penalty</th>
                        <th>Assigned To</th>
                        <th style="width:130px;">Amount</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:160px;">Paid At</th>
                        <th style="width:180px;">Receipt</th>
                        <th style="width:120px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->name }}</td>
                            <td>
                                @if($a->user)
                                    <strong>{{ $a->user->username }}</strong>
                                    <div class="text-muted small">Member</div>
                                @else
                                    <strong>{{ $a->non_member_name }}</strong>
                                    <div class="text-muted small">Non-Member</div>
                                @endif
                            </td>
                            <td>₦{{ number_format($a->amount, 2) }}</td>
                            <td>
                                @if($a->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                @endif
                            </td>
                            <td>{{ $a->paid_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td>
                                @if($a->generated_document_id)
                                    <a href="{{ route('admin.documents.preview', $a->generated_document_id) }}"
                                       class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fas fa-receipt"></i> View Receipt
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($a->payment_status === 'unpaid')
                                    <form action="{{ route('admin.penalties.mark-paid', $a) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">
                                            Mark Paid
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No assignments yet.
                                <a href="{{ route('admin.penalties.assign.form') }}">Assign the first penalty</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
