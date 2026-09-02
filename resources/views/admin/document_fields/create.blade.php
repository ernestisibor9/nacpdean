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
                Add Document Field
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


    {{-- ================================================================
         VALIDATION ERRORS
    ================================================================= --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <div class="fw-bold mb-2">
                Please correct the following:
            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ================================================================
         FORM
    ================================================================= --}}

    <form
        method="POST"
        action="{{ route('admin.documents.fields.store', $document) }}"
    >

        @csrf


        <div class="row">

            {{-- ========================================================
                 MAIN FORM
            ========================================================= --}}

            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">

                    <div class="card-header">

                        <strong>
                            Field Details
                        </strong>

                    </div>

                    <div class="card-body">


                        {{-- Field Key --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Key
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="field_key"
                                value="{{ old('field_key') }}"
                                class="form-control @error('field_key') is-invalid @enderror"
                                placeholder="e.g. container_number"
                                required
                            >

                            @error('field_key')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                            <div class="form-text">

                                Use letters, numbers, underscores and dots only.

                                Examples:

                                <code>container_number</code>,

                                <code>buyer_name</code>,

                                <code>profile.business_name</code>

                            </div>

                        </div>


                        {{-- Label --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Label
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="label"
                                value="{{ old('label') }}"
                                class="form-control @error('label') is-invalid @enderror"
                                placeholder="e.g. Container Number"
                                required
                            >

                            @error('label')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Field Type --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Field Type
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="field_type"
                                class="form-select @error('field_type') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Select field type
                                </option>

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
                                        @selected(old('field_type') === $value)
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


                        {{-- Section --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Section
                            </label>

                            <input
                                type="text"
                                name="section"
                                value="{{ old('section') }}"
                                class="form-control"
                                placeholder="e.g. Seller Details"
                            >

                            <div class="form-text">
                                Used to group fields on the generated/member form.
                            </div>

                        </div>


                        {{-- Placeholder --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Placeholder
                            </label>

                            <input
                                type="text"
                                name="placeholder"
                                value="{{ old('placeholder') }}"
                                class="form-control"
                                placeholder="e.g. Enter container number"
                            >

                        </div>


                        {{-- Default Value --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Default Value
                            </label>

                            <textarea
                                name="default_value"
                                rows="3"
                                class="form-control"
                                placeholder="Optional default value"
                            >{{ old('default_value') }}</textarea>

                        </div>


                        {{-- Sort Order --}}

                        <div class="mb-3">

                            <label class="form-label">
                                Sort Order
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                value="{{ old('sort_order') }}"
                                min="0"
                                class="form-control"
                            >

                            <div class="form-text">
                                Leave blank to place the field at the end.
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 FIELD BEHAVIOUR
            ========================================================= --}}

            <div class="col-lg-4">

                <div class="card shadow-sm mb-4">

                    <div class="card-header">

                        <strong>
                            Field Behaviour
                        </strong>

                    </div>

                    <div class="card-body">


                        {{-- System Field --}}

                        <div class="form-check form-switch mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="is_system"
                                value="1"
                                id="is_system"
                                @checked(old('is_system'))
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="is_system"
                            >
                                System-generated field
                            </label>

                            <div class="form-text">

                                The application will automatically populate
                                this field.

                            </div>

                        </div>


                        {{-- Required --}}

                        <div class="form-check form-switch mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                role="switch"
                                name="is_required"
                                value="1"
                                id="is_required"
                                @checked(old('is_required'))
                            >

                            <label
                                class="form-check-label fw-semibold"
                                for="is_required"
                            >
                                Required field
                            </label>

                            <div class="form-text">

                                Manual fields marked required must be supplied
                                before the document can be generated.

                            </div>

                        </div>


                        <div class="alert alert-info small">

                            <strong>System field example:</strong>

                            <br>

                            <code>member_name</code>

                            <br><br>

                            The application automatically retrieves the
                            member's name.

                            <br><br>

                            <strong>Manual field example:</strong>

                            <br>

                            <code>container_number</code>

                            <br><br>

                            The member/admin supplies the value.

                        </div>

                    </div>

                </div>


                <div class="d-grid">

                    <button
                        type="submit"
                        class="btn btn-primary btn-lg"
                    >
                        Save Field
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


@endsection
