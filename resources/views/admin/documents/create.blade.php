@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Create Document
            </h4>

            <p class="text-muted mb-0">
                Create a document that can later be linked to a payment item.
            </p>

        </div>


        <a
            href="{{ route('admin.documents.index') }}"
            class="btn btn-secondary"
        >
            Back
        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- VALIDATION ERRORS --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please correct the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        method="POST"
        action="{{ route('admin.documents.store') }}"
    >

        @csrf


        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <strong>
                    Document Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- ============================================= --}}
                    {{-- NAME --}}
                    {{-- ============================================= --}}

                    <div class="col-md-8">

                        <label class="form-label">
                            Document Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="e.g. NACPDEAN Afforestation Payment Receipt"
                            required
                        >

                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- CODE --}}
                    {{-- ============================================= --}}

                    <div class="col-md-4">

                        <label class="form-label">
                            Document Code
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="document_code"
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code') }}"
                            placeholder="AFFORESTATION_RECEIPT"
                            required
                        >

                        <small class="text-muted">
                            Use letters, numbers, hyphens or underscores.
                        </small>

                        @error('code')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- DESCRIPTION --}}
                    {{-- ============================================= --}}

                    <div class="col-12">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="4"
                            placeholder="Describe the purpose of this document..."
                        >{{ old('description') }}</textarea>

                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- VALIDITY --}}
                    {{-- ============================================= --}}

                    <div class="col-md-4">

                        <label class="form-label">
                            Validity Type
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="validity_type"
                            id="validity_type"
                            class="form-select @error('validity_type') is-invalid @enderror"
                            required
                        >

                            <option value="none">
                                No Expiry
                            </option>

                            <option
                                value="year_end"
                                @selected(
                                    old('validity_type') === 'year_end'
                                )
                            >
                                End of Year
                            </option>

                            <option
                                value="days"
                                @selected(
                                    old('validity_type') === 'days'
                                )
                            >
                                Number of Days
                            </option>

                            <option
                                value="months"
                                @selected(
                                    old('validity_type') === 'months'
                                )
                            >
                                Number of Months
                            </option>

                            <option
                                value="years"
                                @selected(
                                    old('validity_type') === 'years'
                                )
                            >
                                Number of Years
                            </option>

                            <option
                                value="fixed_date"
                                @selected(
                                    old('validity_type') === 'fixed_date'
                                )
                            >
                                Fixed Date
                            </option>

                        </select>

                        @error('validity_type')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- VALIDITY VALUE --}}
                    {{-- ============================================= --}}

                    <div
                        class="col-md-4"
                        id="validity_value_container"
                    >

                        <label class="form-label">
                            Validity Value
                        </label>

                        <input
                            type="number"
                            name="validity_value"
                            min="1"
                            class="form-control @error('validity_value') is-invalid @enderror"
                            value="{{ old('validity_value') }}"
                            placeholder="e.g. 12"
                        >

                        <small
                            class="text-muted"
                            id="validity_value_help"
                        >
                            Enter the number of days, months or years.
                        </small>

                        @error('validity_value')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- VALIDITY DATE --}}
                    {{-- ============================================= --}}

                    <div
                        class="col-md-4"
                        id="validity_date_container"
                    >

                        <label class="form-label">
                            Expiry Date
                        </label>

                        <input
                            type="date"
                            name="validity_date"
                            class="form-control @error('validity_date') is-invalid @enderror"
                            value="{{ old('validity_date') }}"
                        >

                        @error('validity_date')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- ACTIVE --}}
                    {{-- ============================================= --}}

                    <div class="col-12">

                        <hr>

                        <div class="form-check form-switch">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="form-check-input"
                                id="is_active"
                                @checked(
                                    old(
                                        'is_active',
                                        true
                                    )
                                )
                            >

                            <label
                                class="form-check-label"
                                for="is_active"
                            >

                                <strong>
                                    Active Document
                                </strong>

                                <br>

                                <small class="text-muted">

                                    Active documents can be selected when configuring payment items.

                                </small>

                            </label>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- FOOTER --}}
            {{-- ===================================================== --}}

            <div class="card-footer bg-white d-flex justify-content-end gap-2">

                <a
                    href="{{ route(
                        'admin.documents.index'
                    ) }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Create Document
                </button>

            </div>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- VALIDITY JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const validityType = document.getElementById('validity_type');

    const valueContainer =
        document.getElementById('validity_value_container');

    const dateContainer =
        document.getElementById('validity_date_container');

    const validityValue =
        document.querySelector('[name="validity_value"]');

    const validityDate =
        document.querySelector('[name="validity_date"]');


    function updateValidityFields() {

        const type = validityType.value;


        /*
        |--------------------------------------------------------------------------
        | Numeric validity
        |--------------------------------------------------------------------------
        */

        const isNumericValidity = [
            'days',
            'months',
            'years'
        ].includes(type);


        if (isNumericValidity) {

            valueContainer.style.display = 'block';

        } else {

            valueContainer.style.display = 'none';

            /*
            | Prevent stale values from being submitted
            */

            validityValue.value = '';

        }


        /*
        |--------------------------------------------------------------------------
        | Fixed date
        |--------------------------------------------------------------------------
        */

        if (type === 'fixed_date') {

            dateContainer.style.display = 'block';

        } else {

            dateContainer.style.display = 'none';

            /*
            | Prevent stale values from being submitted
            */

            validityDate.value = '';

        }

    }


    validityType.addEventListener(
        'change',
        updateValidityFields
    );


    updateValidityFields();

});

</script>


@endsection





