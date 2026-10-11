@extends('admin.admin_dashboard')
@section('title', 'Upload Gallery Photos')
@section('admin')

<div class="container-fluid px-4">

    <h1 class="mt-4">Upload Gallery Photos</h1>

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
            <form action="{{ route('admin.gallery.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title (optional)</label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}"
                           placeholder="e.g. National Conference 2026">
                </div>

                <div class="mb-3">
                    <label class="form-label">Caption (optional)</label>
                    <textarea name="caption" rows="2" class="form-control"
                              placeholder="Short description">{{ old('caption') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Photos <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="photos[]" class="form-control"
                           accept="image/jpeg,image/png,image/webp" multiple required>
                    <small class="text-muted">
                        JPG, JPEG, PNG or WEBP. You can select multiple at once. Max 8 MB each.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload
                </button>

                <a href="{{ route('admin.gallery.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

</div>

@endsection
