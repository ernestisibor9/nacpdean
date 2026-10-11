@extends('admin.admin_dashboard')
@section('title', 'Home Videos')
@section('admin')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0">Home Page Videos</h1>
        <a href="{{ route('admin.home-videos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Video
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width:120px;">Preview</th>
                        <th>Title</th>
                        <th style="width:100px;">Type</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($videos as $video)
                        <tr>
                            <td>
                                @if ($video->thumbnailUrl())
                                    <img src="{{ $video->thumbnailUrl() }}"
                                         style="width:100px;height:60px;object-fit:cover;border-radius:6px;">
                                @else
                                    <i class="fas fa-video fa-2x text-muted"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $video->title }}</strong>
                                @if ($video->description)
                                    <div class="text-muted small">{{ Str::limit($video->description, 80) }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($video->isYoutube())
                                    <span class="badge bg-danger">YouTube</span>
                                @else
                                    <span class="badge bg-primary">File</span>
                                @endif
                            </td>
                            <td>
                                @if ($video->is_active)
                                    <span class="badge bg-success">Visible</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.home-videos.toggle', $video) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">
                                        {{ $video->is_active ? 'Hide' : 'Show' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.home-videos.destroy', $video) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this video?');">
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
                            <td colspan="5" class="text-center py-4">
                                No videos yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $videos->links() }}
        </div>
    </div>

</div>

@endsection
