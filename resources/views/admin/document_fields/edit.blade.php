@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection

<div class="container-fluid">

    {{-- HEADER --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Edit Document Field
            </h4>

            <div class="text-muted">
                {{ $document->name }}
            </div>

        </div>

        <a
            href="{{ route('admin.documents.fields.index', $document) }}"
            class="btn btn-outline-secondary"
        >
            ← Back to Fields
        </a>

    </div>


    {{-- ERRORS --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <div class="fw-bold mb-2">
                Please correct the following:
            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- SYSTEM WARNING --}}

    @if($field->is_system)

        <div class="alert alert-primary">

            <strong>
                System-generated field
            </strong>

            <br>

            This field is populated automatically by the application.
            Members should not manually enter its value.

        </div>

    @endif


    {{-- UPDATE FORM --}}

    <form
        method="POST"
        action="{{ route(
            'admin.documents.fields.update',
            [$document, $field]
        ) }}"
    >

        @csrf
        @method('PUT')

        <div class="row">

            {{-- MAIN --}}

            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">

                    <div class="card-header">
                        <strong>Field Details</strong>
                    </div>

                    <div class="card-body">

                        {{-- FIELD KEY --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Key
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="field_key"
                                value="{{ old(
                                    'field_key',
                                    $field->field_key
                                ) }}"
                                class="form-control @error('field_key') is-invalid @enderror"
                                required
                            >

                            @error('field_key')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- LABEL --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Label
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="label"
                                value="{{ old(
                                    'label',
                                    $field->label
                                ) }}"
                                class="form-control @error('label') is-invalid @enderror"
                                required
                            >

                            @error('label')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- FIELD TYPE --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Type
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="field_type"
                                id="field_type"
                                class="form-select @error('field_type') is-invalid @enderror"
                                required
                            >

                                @foreach([
                                    'text' => 'Text',
                                    'textarea' => 'Textarea',
                                    'number' => 'Number',
                                    'date' => 'Date',
                                    'email' => 'Email',
                                    'phone' => 'Phone',
                                    'select' => 'Select',
                                    'checkbox' => 'Checkbox',
                                    'file' => 'File',
                                ] as $value => $label)

                                    <option
                                        value="{{ $value }}"
                                        @selected(
                                            old(
                                                'field_type',
                                                $field->field_type
                                            ) === $value
                                        )
                                    >
                                        {{ $label }}
                                    </option>

                                @endforeach

                            </select>

                            @error('field_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- SELECT OPTIONS --}}

                        <div
                            id="selectOptionsContainer"
                            class="card border-primary mb-4"
                            style="display: none;"
                        >

                            <div class="card-header bg-primary text-white">

                                <strong>
                                    Select Options
                                </strong>

                            </div>

                            <div class="card-body">

                                <div class="small text-muted mb-3">

                                    Configure the choices available to the
                                    member.

                                </div>

                                <div id="optionsList">

                                    @php
                                        $options = old(
                                            'options',
                                            $field->options ?? []
                                        );
                                    @endphp

                                    @forelse($options as $index => $option)

                                        <div class="row g-2 mb-2 option-row">

                                            <div class="col-md-5">

                                                <input
                                                    type="text"
                                                    name="options[{{ $index }}][label]"
                                                    value="{{ $option['label'] ?? '' }}"
                                                    class="form-control"
                                                    placeholder="Label"
                                                >

                                            </div>

                                            <div class="col-md-5">

                                                <input
                                                    type="text"
                                                    name="options[{{ $index }}][value]"
                                                    value="{{ $option['value'] ?? '' }}"
                                                    class="form-control"
                                                    placeholder="Value"
                                                >

                                            </div>

                                            <div class="col-md-2">

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger w-100 remove-option"
                                                >
                                                    Remove
                                                </button>

                                            </div>

                                        </div>

                                    @empty

                                        <div class="row g-2 mb-2 option-row">

                                            <div class="col-md-5">

                                                <input
                                                    type="text"
                                                    name="options[0][label]"
                                                    class="form-control"
                                                    placeholder="Label"
                                                >

                                            </div>

                                            <div class="col-md-5">

                                                <input
                                                    type="text"
                                                    name="options[0][value]"
                                                    class="form-control"
                                                    placeholder="Value"
                                                >

                                            </div>

                                            <div class="col-md-2">

                                                <button
                                                    type="button"
                                                    class="btn btn-outline-danger w-100 remove-option"
                                                >
                                                    Remove
                                                </button>

                                            </div>

                                        </div>

                                    @endforelse

                                </div>

                                <button
                                    type="button"
                                    id="addOption"
                                    class="btn btn-outline-primary btn-sm mt-2"
                                >
                                    + Add Option
                                </button>

                                @error('options')
                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>


                        {{-- SECTION --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Section
                            </label>

                            <input
                                type="text"
                                name="section"
                                value="{{ old(
                                    'section',
                                    $field->section
                                ) }}"
                                class="form-control"
                                placeholder="e.g. Seller Details"
                            >

                        </div>


                        {{-- PLACEHOLDER --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Placeholder
                            </label>

                            <input
                                type="text"
                                name="placeholder"
                                value="{{ old(
                                    'placeholder',
                                    $field->placeholder
                                ) }}"
                                class="form-control"
                            >

                        </div>


                        {{-- DEFAULT --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Default Value
                            </label>

                            <textarea
                                name="default_value"
                                rows="3"
                                class="form-control"
                            >{{ old(
                                'default_value',
                                $field->default_value
                            ) }}</textarea>

                        </div>


                        {{-- SORT ORDER --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Sort Order
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                value="{{ old(
                                    'sort_order',
                                    $field->sort_order
                                ) }}"
                                min="0"
                                class="form-control"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- BEHAVIOUR --}}

            <div class="col-lg-4">

                <div class="card shadow-sm mb-4">

                    <div class="card-header">
                        <strong>Field Behaviour</strong>
                    </div>

                    <div class="card-body">

                        {{-- SYSTEM --}}

                        <div class="form-check form-switch mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="is_system"
                                value="1"
                                id="is_system"
                                @checked(
                                    old(
                                        'is_system',
                                        $field->is_system
                                    )
                                )
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="is_system"
                            >
                                System-generated field
                            </label>

                            <div class="form-text">
                                Automatically populated by the system.
                            </div>

                        </div>


                        {{-- REQUIRED --}}

                        <div class="form-check form-switch mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="is_required"
                                value="1"
                                id="is_required"
                                @checked(
                                    old(
                                        'is_required',
                                        $field->is_required
                                    )
                                )
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="is_required"
                            >
                                Required field
                            </label>

                            <div class="form-text">
                                Manual fields must have a value before
                                generation.
                            </div>

                        </div>


                        {{-- FIELD INFORMATION --}}

                        <div class="border rounded p-3 bg-light">

                            <div class="small text-muted mb-1">
                                Field Key
                            </div>

                            <code>
                                {{ $field->field_key }}
                            </code>

                            <hr>

                            <div class="small text-muted mb-1">
                                Source
                            </div>

                            @if($field->is_system)

                                <span class="badge bg-primary">
                                    System-generated
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Manual
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- SAVE --}}

                <div class="d-grid">

                    <button
                        type="submit"
                        class="btn btn-primary btn-lg"
                    >
                        Update Field
                    </button>

                </div>

            </div>

        </div>

    </form>


    {{-- DELETE FORM --}}
    {{-- IMPORTANT: This is outside the update form. --}}

    @if(!$field->is_system)

        <div class="row">

            <div class="col-lg-4 ms-auto">

                <div class="card border-danger mt-4">

                    <div class="card-body">

                        <h6 class="text-danger">
                            Delete Field
                        </h6>

                        <p class="small text-muted">
                            This permanently removes the field from
                            this document configuration.
                        </p>

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.documents.fields.destroy',
                                [$document, $field]
                            ) }}"
                            onsubmit="return confirm(
                                'Are you sure you want to delete this field?'
                            );"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                            >
                                Delete Field
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>


{{-- SELECT OPTIONS JAVASCRIPT --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const fieldType = document.getElementById('field_type');

    const optionsContainer =
        document.getElementById(
            'selectOptionsContainer'
        );

    const optionsList =
        document.getElementById('optionsList');

    const addOptionButton =
        document.getElementById('addOption');


    let optionIndex =
        optionsList.querySelectorAll(
            '.option-row'
        ).length;


    function toggleOptions() {

        if (fieldType.value === 'select') {

            optionsContainer.style.display =
                'block';

        } else {

            optionsContainer.style.display =
                'none';

        }

    }


    fieldType.addEventListener(
        'change',
        toggleOptions
    );


    addOptionButton.addEventListener(
        'click',
        function () {

            const row =
                document.createElement('div');

            row.className =
                'row g-2 mb-2 option-row';

            row.innerHTML = `
                <div class="col-md-5">

                    <input
                        type="text"
                        name="options[${optionIndex}][label]"
                        class="form-control"
                        placeholder="Label"
                    >

                </div>

                <div class="col-md-5">

                    <input
                        type="text"
                        name="options[${optionIndex}][value]"
                        class="form-control"
                        placeholder="Value"
                    >

                </div>

                <div class="col-md-2">

                    <button
                        type="button"
                        class="btn btn-outline-danger w-100 remove-option"
                    >
                        Remove
                    </button>

                </div>
            `;

            optionsList.appendChild(row);

            optionIndex++;

        }
    );


    optionsList.addEventListener(
        'click',
        function (event) {

            if (
                event.target.classList.contains(
                    'remove-option'
                )
            ) {

                const rows =
                    optionsList.querySelectorAll(
                        '.option-row'
                    );

                if (rows.length > 1) {

                    event.target
                        .closest('.option-row')
                        .remove();

                }

            }

        }
    );


    toggleOptions();

});

</script>


@endsection
