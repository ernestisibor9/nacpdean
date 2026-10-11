@extends('admin.admin_dashboard')
@section('title', 'Stakeholders Data')
@section('admin')

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="mb-0">Stakeholders Data</h1>
        <a href="{{ route('admin.stakeholder-files.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Upload File
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
                        <th style="width:80px;">Preview</th>
                        <th>Title</th>
                        <th style="width:100px;">Type</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($files as $file)
                        <tr>
                            <td>
                                @if ($file->isImage())
                                    <img src="{{ $file->fileUrl() }}"
                                         style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                @else
                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                @endif
                            </td>
                            <td><strong>{{ $file->title }}</strong></td>
                            <td>{{ strtoupper($file->file_type) }}</td>
                            <td>
                                @if ($file->is_active)
                                    <span class="badge bg-success">Visible</span>
                                @else
                                    <span class="badge bg-danger">Hidden</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('stakeholders-data.view', $file) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <form action="{{ route('admin.stakeholder-files.toggle', $file) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">
                                        {{ $file->is_active ? 'Hide' : 'Show' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.stakeholder-files.destroy', $file) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this file?');">
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
                                No files uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $files->links() }}
        </div>
    </div>

</div>

@endsection
