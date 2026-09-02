@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection



<div class="container-fluid">

    {{-- ================================================================
         HEADER
    ================================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Document Fields
            </h4>

            <div class="text-muted">
                {{ $document->name }}
            </div>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Back to Document
            </a>

            <a href="{{ route('admin.documents.fields.create', $document) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Add Field
            </a>

        </div>

    </div>


    {{-- ================================================================
         FLASH MESSAGES
    ================================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">

            {{ session('info') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- ================================================================
         DOCUMENT INFORMATION
    ================================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Document
                    </small>

                    <strong>
                        {{ $document->name }}
                    </strong>

                </div>


                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Code
                    </small>

                    <strong>
                        {{ $document->code ?: '—' }}
                    </strong>

                </div>


                <div class="col-md-3">

                    <small class="text-muted d-block">
                        Status
                    </small>

                    @if ($document->is_active)
                        <span class="badge bg-success">
                            Active
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Inactive
                        </span>
                    @endif

                </div>


                <div class="col-md-2">

                    <small class="text-muted d-block">
                        Fields
                    </small>

                    <strong>
                        {{ $fields->count() }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
         FIELD EXPLANATION
    ================================================================= --}}

    <div class="alert alert-light border mb-4">

        <div class="fw-bold mb-1">
            Field configuration
        </div>

        <div class="small text-muted">

            <strong>System</strong> fields are populated automatically
            by the application.

            <br>

            <strong>Manual</strong> fields are supplied by the member or
            administrator during the document process.

        </div>

    </div>


    {{-- ================================================================
         FIELDS TABLE
    ================================================================= --}}

    <div class="card shadow-sm">

        <div class="card-body p-0">

            @if ($fields->isEmpty())
                <div class="text-center py-5">

                    <div class="mb-3 text-muted">
                        <i class="bi bi-ui-checks fs-1"></i>
                    </div>

                    <h5>
                        No fields configured
                    </h5>

                    <p class="text-muted mb-3">
                        Add the fields that this document requires.
                    </p>

                    <a href="{{ route('admin.documents.fields.create', $document) }}" class="btn btn-primary">
                        Add First Field
                    </a>

                </div>
            @else
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="70">
                                    #
                                </th>

                                <th>
                                    Field
                                </th>

                                <th>
                                    Key
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Section
                                </th>

                                <th>
                                    Source
                                </th>

                                <th>
                                    Required
                                </th>

                                <th width="210">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($fields as $field)
                                <tr>

                                    <td>

                                        <span class="fw-semibold">
                                            {{ $field->sort_order }}
                                        </span>

                                    </td>


                                    <td>

                                        <div class="fw-semibold">
                                            {{ $field->label }}
                                        </div>

                                        @if ($field->placeholder)
                                            <small class="text-muted">
                                                {{ $field->placeholder }}
                                            </small>
                                        @endif

                                    </td>


                                    <td>

                                        <code>
                                            {{ $field->field_key }}
                                        </code>

                                    </td>


                                    <td>

                                        <span class="badge text-bg-light border">

                                            {{ ucfirst($field->field_type) }}

                                        </span>

                                    </td>


                                    <td>

                                        {{ $field->section ?: '—' }}

                                    </td>


                                    <td>

                                        @if ($field->is_system)
                                            <span class="badge bg-primary">
                                                System
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Manual
                                            </span>
                                        @endif

                                    </td>


                                    <td>

                                        @if ($field->is_required)
                                            <span class="badge bg-danger">
                                                Required
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                Optional
                                            </span>
                                        @endif

                                    </td>


                                    <td>

                                        <div class="d-flex gap-1">

                                            {{-- Move Up --}}

                                            <form method="POST"
                                                action="{{ route('admin.documents.fields.move-up', [$document, $field]) }}">

                                                @csrf

                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    title="Move Up">
                                                    ↑
                                                </button>

                                            </form>


                                            {{-- Move Down --}}

                                            <form method="POST"
                                                action="{{ route('admin.documents.fields.move-down', [$document, $field]) }}">

                                                @csrf

                                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    title="Move Down">
                                                    ↓
                                                </button>

                                            </form>


                                            {{-- Edit --}}

                                            <a href="{{ route('admin.documents.fields.edit', [$document, $field]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>


                                            {{-- Delete --}}

                                            @if (!$field->is_system)
                                                <form method="POST"
                                                    action="{{ route('admin.documents.fields.destroy', [$document, $field]) }}"
                                                    onsubmit="return confirm(
                                                        'Delete this document field?'
                                                    );">

                                                    @csrf

                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Delete
                                                    </button>

                                                </form>
                                            @endif

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>
            @endif

        </div>

    </div>

</div>


@endsection
