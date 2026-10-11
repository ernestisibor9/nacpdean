@extends('admin.admin_dashboard')
@section('title', 'Gallery')
@section('admin')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0">Gallery</h1>
        <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Upload Photos
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($photos->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No photos uploaded yet.</p>
            </div>
        </div>
    @else

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($photos as $photo)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="border rounded overflow-hidden h-100">

                                <img src="{{ $photo->imageUrl() }}"
                                     alt="{{ $photo->title }}"
                                     style="width:100%;height:180px;object-fit:cover;">

                                <div class="p-2">
                                    <div class="small fw-bold text-truncate">
                                        {{ $photo->title ?: 'Untitled' }}
                                    </div>

                                    @if ($photo->caption)
                                        <div class="text-muted small text-truncate">
                                            {{ $photo->caption }}
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        @if ($photo->is_active)
                                            <span class="badge bg-success">Visible</span>
                                        @else
                                            <span class="badge bg-secondary">Hidden</span>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-1 mt-2">
                                        <form action="{{ route('admin.gallery.toggle', $photo) }}"
                                              method="POST" class="flex-fill">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-warning w-100">
                                                {{ $photo->is_active ? 'Hide' : 'Show' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.gallery.destroy', $photo) }}"
                                              method="POST" class="flex-fill"
                                              onsubmit="return confirm('Delete this photo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $photos->links() }}
        </div>
    @endif

</div>

@endsection
