@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                Document Fields
            </h4>

            <div class="text-muted">
                {{ $document->name }}
            </div>

            <small class="text-muted">
                Code: {{ $document->code }}
            </small>
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

            <a href="{{ route('admin.documents.preview', $document) }}" class="btn btn-outline-info">

                <i class="bi bi-eye"></i>
                Preview

            </a>

        </div>
    </div>


    {{-- Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Document Information --}}
    <div class="card mb-4 shadow-sm">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">
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

                    <code>
                        {{ $document->code }}
                    </code>
                </div>

                <div class="col-md-2">
                    <small class="text-muted d-block">
                        Fields
                    </small>

                    <strong>
                        {{ $fields->count() }}
                    </strong>
                </div>

                <div class="col-md-2">
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

            </div>

        </div>

    </div>


    {{-- Fields --}}
    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>
                Field Configuration
            </strong>

            @if ($fields->count() > 1)
                <button type="button" id="saveOrderBtn" class="btn btn-sm btn-outline-primary">

                    <i class="bi bi-arrows-move"></i>
                    Save Order

                </button>
            @endif

        </div>


        <div class="card-body p-0">

            @if ($fields->count())
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th style="width:60px;">
                                    #
                                </th>

                                <th>
                                    Field
                                </th>

                                <th>
                                    Label
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Section
                                </th>

                                <th>
                                    Required
                                </th>

                                <th>
                                    System
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody id="fieldsTableBody">

                            @foreach ($fields as $field)
                                <tr data-id="{{ $field->id }}">

                                    <td>

                                        <span class="badge bg-light text-dark border">
                                            {{ $field->sort_order }}
                                        </span>

                                    </td>


                                    <td>

                                        <code>
                                            {{ $field->field_key }}
                                        </code>

                                    </td>


                                    <td>

                                        <strong>
                                            {{ $field->label }}
                                        </strong>

                                        @if ($field->placeholder)
                                            <small class="text-muted d-block">
                                                Placeholder:
                                                {{ $field->placeholder }}
                                            </small>
                                        @endif

                                    </td>


                                    <td>

                                        <span class="badge bg-info text-dark">
                                            {{ ucfirst($field->field_type) }}
                                        </span>

                                    </td>


                                    <td>

                                        @if ($field->section)
                                            <span class="text-muted">
                                                {{ $field->section }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                —
                                            </span>
                                        @endif

                                    </td>


                                    <td>

                                        @if ($field->is_required)
                                            <span class="badge bg-danger">
                                                Required
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Optional
                                            </span>
                                        @endif

                                    </td>


                                    <td>

                                        @if ($field->is_system)
                                            <span class="badge bg-primary">
                                                System
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border">
                                                Manual
                                            </span>
                                        @endif

                                    </td>


                                    <td class="text-end">

                                        <a href="{{ route('admin.documents.fields.edit', [$document, $field]) }}"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-pencil"></i>
                                            Edit

                                        </a>


                                        <form method="POST"
                                            action="{{ route('admin.documents.fields.destroy', [$document, $field]) }}"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                  'Delete this document field?'
                                              );">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>
            @else
                <div class="text-center py-5">

                    <div class="mb-3">
                        <i class="bi bi-ui-checks-grid fs-1 text-muted"></i>
                    </div>

                    <h5>
                        No fields configured
                    </h5>

                    <p class="text-muted">
                        Add the fields that should be available when
                        this document is generated.
                    </p>

                    <a href="{{ route('admin.documents.fields.create', $document) }}"
                        class="btn btn-primary">

                        <i class="bi bi-plus-lg"></i>
                        Add First Field

                    </a>

                </div>
            @endif

        </div>

    </div>


    {{-- Help --}}
    <div class="alert alert-info mt-4">

        <strong>System vs Manual fields</strong>

        <ul class="mb-0 mt-2">

            <li>
                <strong>System:</strong>
                Automatically populated from the member,
                membership, payment or transaction.
            </li>

            <li>
                <strong>Manual:</strong>
                Supplied when the document requires additional
                information such as container or truck number.
            </li>

        </ul>

    </div>

</div>


{{-- Reordering --}}
@if ($fields->count() > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tbody = document.getElementById('fieldsTableBody');
            const saveButton = document.getElementById('saveOrderBtn');

            if (!tbody || !saveButton) {
                return;
            }


            let draggedRow = null;


            tbody.querySelectorAll('tr').forEach(function(row) {

                row.setAttribute('draggable', 'true');


                row.addEventListener('dragstart', function() {

                    draggedRow = row;

                    row.classList.add('table-active');

                });


                row.addEventListener('dragend', function() {

                    row.classList.remove('table-active');

                    draggedRow = null;

                });


                row.addEventListener('dragover', function(event) {

                    event.preventDefault();

                    if (!draggedRow || draggedRow === row) {
                        return;
                    }


                    const rect = row.getBoundingClientRect();

                    const middle =
                        rect.top + rect.height / 2;


                    if (event.clientY < middle) {

                        row.parentNode.insertBefore(
                            draggedRow,
                            row
                        );

                    } else {

                        row.parentNode.insertBefore(
                            draggedRow,
                            row.nextSibling
                        );

                    }

                });

            });


            saveButton.addEventListener('click', function() {

                const rows =
                    tbody.querySelectorAll('tr');


                const fields =
                    Array.from(rows).map(function(row) {

                        return row.dataset.id;

                    });


                saveButton.disabled = true;

                saveButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Saving...';


                fetch(
                        "{{ route('admin.documents.fields.reorder', $document) }}", {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).getAttribute('content')
                            },

                            body: JSON.stringify({
                                fields: fields
                            })
                        }
                    )
                    .then(function(response) {

                        if (!response.ok) {
                            throw new Error('Unable to save order.');
                        }

                        return response.json();

                    })
                    .then(function() {

                        window.location.reload();

                    })
                    .catch(function(error) {

                        alert(error.message);

                        saveButton.disabled = false;

                        saveButton.innerHTML =
                            '<i class="bi bi-arrows-move"></i> Save Order';

                    });

            });

        });
    </script>
@endif

@endsection
