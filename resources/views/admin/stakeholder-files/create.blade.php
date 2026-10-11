@extends('admin.admin_dashboard')
@section('title', 'Upload Stakeholder File')
@section('admin')

<div class="container-fluid px-4">

    <h1 class="mt-4">Upload Stakeholder File</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.stakeholder-files.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" required
                           accept=".pdf,.jpg,.jpeg,.png,.webp">
                    <small class="text-muted">PDF, JPG, JPEG, PNG or WEBP. Max 20 MB.</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload
                </button>

                <a href="{{ route('admin.stakeholder-files.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>

@endsection
