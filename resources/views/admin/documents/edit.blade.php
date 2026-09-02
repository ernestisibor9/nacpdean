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
                Edit Document
            </h4>

            <p class="text-muted mb-0">

                {{ $document->name }}

                <span class="badge bg-secondary ms-2">
                    {{ $document->code }}
                </span>

            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="{{ route(
                    'admin.documents.fields.index',
                    $document
                ) }}"
                class="btn btn-primary"
            >
                Manage Fields
            </a>

            <a
                href="{{ route(
                    'admin.documents.index'
                ) }}"
                class="btn btn-secondary"
            >
                Back
            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


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
    {{-- DOCUMENT FORM --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <strong>
                Document Configuration
            </strong>

        </div>


        <form
            method="POST"
            action="{{ route(
                'admin.documents.update',
                $document
            ) }}"
        >

            @csrf

            @method('PUT')


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
                            value="{{ old(
                                'name',
                                $document->name
                            ) }}"
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
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old(
                                'code',
                                $document->code
                            ) }}"
                            required
                        >

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
                        >{{ old(
                            'description',
                            $document->description
                        ) }}</textarea>

                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- VALIDITY TYPE --}}
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

                            <option
                                value="none"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'none'
                                )
                            >
                                No Expiry
                            </option>


                            <option
                                value="year_end"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'year_end'
                                )
                            >
                                End of Year
                            </option>


                            <option
                                value="days"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'days'
                                )
                            >
                                Number of Days
                            </option>


                            <option
                                value="months"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'months'
                                )
                            >
                                Number of Months
                            </option>


                            <option
                                value="years"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'years'
                                )
                            >
                                Number of Years
                            </option>


                            <option
                                value="fixed_date"
                                @selected(
                                    old(
                                        'validity_type',
                                        $document->validity_type
                                    ) === 'fixed_date'
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
                            value="{{ old(
                                'validity_value',
                                $document->validity_value
                            ) }}"
                        >

                        <small class="text-muted">
                            Number of days, months or years.
                        </small>

                        @error('validity_value')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ============================================= --}}
                    {{-- FIXED DATE --}}
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
                            value="{{ old(
                                'validity_date',
                                $document->validity_date
                                    ? $document->validity_date->format('Y-m-d')
                                    : ''
                            ) }}"
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
                                id="document_active"
                                @checked(
                                    old(
                                        'is_active',
                                        $document->is_active
                                    )
                                )
                            >

                            <label
                                class="form-check-label"
                                for="document_active"
                            >

                                <strong>
                                    Active Document
                                </strong>

                                <br>

                                <small class="text-muted">

                                    Only active documents should normally be
                                    selected for new payment items.

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
                    Save Changes
                </button>

            </div>

        </form>

    </div>



    {{-- ========================================================= --}}
    {{-- DOCUMENT FIELD SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Document Fields
                </strong>


                <a
                    href="{{ route(
                        'admin.documents.fields.index',
                        $document
                    ) }}"
                    class="btn btn-sm btn-primary"
                >
                    Manage Fields
                </a>

            </div>

        </div>


        <div class="card-body">

            @if($document->fields_count > 0)

                <div class="alert alert-info mb-0">

                    This document currently has

                    <strong>
                        {{ $document->fields_count }}
                    </strong>

                    configured field(s).

                    Use

                    <strong>
                        Manage Fields
                    </strong>

                    to add, edit, delete or reorder them.

                </div>

            @else

                <div class="alert alert-warning mb-0">

                    <strong>
                        No fields configured yet.
                    </strong>

                    This document cannot be generated correctly if it
                    requires fields that have not been configured.

                    <div class="mt-2">

                        <a
                            href="{{ route(
                                'admin.documents.fields.index',
                                $document
                            ) }}"
                            class="btn btn-sm btn-warning"
                        >
                            Configure Fields
                        </a>

                    </div>

                </div>

            @endif

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- DOCUMENT USAGE --}}
    {{-- ========================================================= --}}

    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <strong>
                Document Usage
            </strong>

        </div>


        <div class="card-body">

            @php

                $paymentItemCount =
                    $document
                        ->paymentItems()
                        ->count();

                $generatedDocumentCount =
                    $document
                        ->generatedDocuments()
                        ->count();

            @endphp


            <div class="row g-3">

                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <div class="text-muted small">
                            Payment Items Using Document
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ $paymentItemCount }}
                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <div class="text-muted small">
                            Generated Documents
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ $generatedDocumentCount }}
                        </div>

                    </div>

                </div>

            </div>


            @if($paymentItemCount > 0)

                <div class="alert alert-info mt-3 mb-0">

                    This document is already linked to

                    <strong>
                        {{ $paymentItemCount }}
                    </strong>

                    payment item(s).

                    Avoid changing field keys that your
                    `DocumentGenerationService` depends on.

                </div>

            @endif

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- VALIDITY JAVASCRIPT --}}
{{-- ============================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const validityType =
            document.getElementById(
                'validity_type'
            );

        const valueContainer =
            document.getElementById(
                'validity_value_container'
            );

        const dateContainer =
            document.getElementById(
                'validity_date_container'
            );


        function updateValidityFields()
        {
            const type =
                validityType.value;


            /*
            |--------------------------------------------------------------------------
            | Numeric validity
            |--------------------------------------------------------------------------
            */

            if (
                [
                    'days',
                    'months',
                    'years'
                ].includes(type)
            ) {

                valueContainer.style.display =
                    'block';

            } else {

                valueContainer.style.display =
                    'none';

            }


            /*
            |--------------------------------------------------------------------------
            | Fixed date
            |--------------------------------------------------------------------------
            */

            if (
                type === 'fixed_date'
            ) {

                dateContainer.style.display =
                    'block';

            } else {

                dateContainer.style.display =
                    'none';

            }
        }


        validityType.addEventListener(
            'change',
            updateValidityFields
        );


        updateValidityFields();

    }
);

</script>


@endsection
