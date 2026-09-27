@extends('admin.admin_dashboard')
@section('title', 'NACPDEAN - New Penalty')

@section('admin')


<div class="container-fluid">

    <h1 class="mt-4">New Penalty</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.penalties.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Penalty Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name') }}" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Amount (₦) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount"
                               class="form-control" value="{{ old('amount', 0) }}" required>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_custom_amount"
                                   id="is_custom_amount" value="1"
                                   @checked(old('is_custom_amount'))>
                            <label class="form-check-label" for="is_custom_amount">
                                Custom amount (admin enters amount when assigning)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Action Required <span class="text-danger">*</span></label>
                    <input type="text" name="action" class="form-control"
                           value="{{ old('action') }}" required maxlength="255"
                           placeholder="e.g. Pay penalty + renew operational membership">
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="status"
                           id="status" value="1" @checked(old('status', true))>
                    <label class="form-check-label" for="status">Active</label>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Penalty
                </button>
                <a href="{{ route('admin.penalties.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>


@endsection
