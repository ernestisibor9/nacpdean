@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


    <div class="container-fluid">

        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Documents
                </h4>

                <p class="text-muted mb-0">
                    Manage documents and their configuration.
                </p>
            </div>

            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Create Document
            </a>


        </div>


        {{-- ========================================================= --}}
        {{-- SUCCESS MESSAGE --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- ERROR MESSAGE --}}
        {{-- ========================================================= --}}

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">

                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- DOCUMENT TABLE --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <strong>
                        Document List
                    </strong>

                    <span class="text-muted small">
                        {{ $documents->total() }} document(s)
                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                @if ($documents->count())

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th style="width: 60px;">
                                        #
                                    </th>

                                    <th>
                                        Document
                                    </th>

                                    <th>
                                        Code
                                    </th>

                                    <th>
                                        Validity
                                    </th>

                                    <th class="text-center">
                                        Fields
                                    </th>

                                    <th class="text-center">
                                        Status
                                    </th>

                                    <th class="text-end" style="width: 280px;">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($documents as $document)
                                    <tr>

                                        {{-- ================================= --}}
                                        {{-- NUMBER --}}
                                        {{-- ================================= --}}

                                        <td>

                                            {{ $documents->firstItem() + $loop->index }}

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- DOCUMENT --}}
                                        {{-- ================================= --}}

                                        <td>

                                            <div class="fw-semibold">

                                                {{ $document->name }}

                                            </div>


                                            @if ($document->description)
                                                <small class="text-muted">

                                                    {{ \Illuminate\Support\Str::limit($document->description, 90) }}

                                                </small>
                                            @endif

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- CODE --}}
                                        {{-- ================================= --}}

                                        <td>

                                            <code>
                                                {{ $document->code }}
                                            </code>

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- VALIDITY --}}
                                        {{-- ================================= --}}

                                        <td>

                                            @switch($document->validity_type)
                                                @case('none')
                                                    <span class="text-muted">
                                                        No Expiry
                                                    </span>
                                                @break

                                                @case('year_end')
                                                    <span>
                                                        End of Year
                                                    </span>
                                                @break

                                                @case('days')
                                                    {{ $document->validity_value }}
                                                    day(s)
                                                @break

                                                @case('months')
                                                    {{ $document->validity_value }}
                                                    month(s)
                                                @break

                                                @case('years')
                                                    {{ $document->validity_value }}
                                                    year(s)
                                                @break

                                                @case('fixed_date')
                                                    @if ($document->validity_date)
                                                        {{ $document->validity_date->format('d M Y') }}
                                                    @else
                                                        <span class="text-danger">
                                                            Not configured
                                                        </span>
                                                    @endif
                                                @break

                                                @default
                                                    <span class="text-muted">
                                                        —
                                                    </span>
                                            @endswitch

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- FIELD COUNT --}}
                                        {{-- ================================= --}}

                                        <td class="text-center">

                                            <span class="badge bg-info text-dark">
                                                {{ $document->fields_count }}
                                            </span>

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- STATUS --}}
                                        {{-- ================================= --}}

                                        <td class="text-center">

                                            @if ($document->is_active)
                                                <span class="badge bg-success">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    Inactive
                                                </span>
                                            @endif

                                        </td>


                                        {{-- ================================= --}}
                                        {{-- ACTIONS --}}
                                        {{-- ================================= --}}

                                        <td class="text-end">

                                            <div class="btn-group">

                                                <a href="{{ route('admin.documents.fields.index', $document) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Fields
                                                </a>

                                                <!-- Preview Button belongs HERE where $document exists -->
                    <a href="{{ route('admin.documents.preview', $document) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i> Preview
                    </a>
                                                <a href="{{ route('admin.documents.edit', $document) }}"
                                                    class="btn btn-sm btn-outline-secondary">
                                                    Edit
                                                </a>


                                                @if (!$document->paymentItems()->exists() && !$document->generatedDocuments()->exists())
                                                    <form method="POST"
                                                        action="{{ route('admin.documents.destroy', $document) }}"
                                                        onsubmit="return confirm(
                                                        'Are you sure you want to delete this document?'
                                                    )">

                                                        @csrf

                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </button>

                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-danger" disabled
                                                        title="This document is already in use.">
                                                        Delete
                                                    </button>
                                                @endif

                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- ================================================ --}}
                    {{-- PAGINATION --}}
                    {{-- ================================================ --}}

                    @if ($documents->hasPages())
                        <div class="p-3 border-top">

                            {{ $documents->links() }}

                        </div>
                    @endif
                @else
                    <div class="text-center py-5">

                        <div class="mb-3">

                            <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>

                        </div>

                        <h5>
                            No documents found
                        </h5>

                        <p class="text-muted">
                            Create your first document configuration.
                        </p>

                        <a href="{{ route('admin.documents.create') }}"
                            class="btn btn-primary">
                            Create Document
                        </a>

                    </div>

                @endif

            </div>

        </div>

    </div>


@endsection
