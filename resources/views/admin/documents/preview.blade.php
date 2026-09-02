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
                Document Preview / Field Mapping
            </h4>

            <div class="text-muted">
                {{ $document->name }}
            </div>

            <small class="text-muted">
                Code:
                <code>{{ $document->code }}</code>
            </small>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route(
                'admin.documents.fields.index',
                $document
            ) }}"
               class="btn btn-outline-primary">

                <i class="bi bi-ui-checks-grid"></i>
                Manage Fields

            </a>


            <a href="{{ route(
                'admin.documents.edit',
                $document
            ) }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-pencil"></i>
                Edit Document

            </a>

        </div>

    </div>


    {{-- Warnings --}}
    @if(count($warnings))

        <div class="alert alert-warning">

            <div class="d-flex">

                <i class="bi bi-exclamation-triangle fs-4 me-3"></i>

                <div>

                    <strong>
                        Configuration Warnings
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach($warnings as $warning)

                            <li>
                                {{ $warning }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @else

        <div class="alert alert-success">

            <i class="bi bi-check-circle me-2"></i>

            <strong>
                Document configuration looks good.
            </strong>

            No unsupported system fields or obvious field
            configuration problems were detected.

        </div>

    @endif


    <div class="row g-4">


        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- Document Preview --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header d-flex justify-content-between">

                    <strong>
                        Document Preview
                    </strong>

                    <span class="badge bg-light text-dark border">
                        {{ ucfirst(str_replace(
                            '_',
                            ' ',
                            $document->type ?? 'document'
                        )) }}
                    </span>

                </div>


                <div class="card-body">

                    {{-- Fake Document --}}
                    <div class="border rounded bg-white p-4">

                        {{-- Document Header --}}
                        <div class="text-center border-bottom pb-4 mb-4">

                            <h5 class="fw-bold mb-1">
                                NACPDEAN
                            </h5>

                            <div class="text-muted small mb-3">
                                {{ $document->name }}
                            </div>

                            <span class="badge bg-light text-dark border">
                                {{ $document->code }}
                            </span>

                        </div>


                        {{-- Fields --}}
                        @if($fields->count())

                            @php
                                $currentSection = null;
                            @endphp


                            @foreach($fields as $field)

                                @if(
                                    $field->section &&
                                    $field->section !== $currentSection
                                )

                                    @php
                                        $currentSection =
                                            $field->section;
                                    @endphp

                                    <div class="mt-4 mb-3">

                                        <h6 class="fw-bold border-bottom pb-2">

                                            {{ $field->section }}

                                        </h6>

                                    </div>

                                @endif


                                <div class="row mb-3">

                                    <div class="col-md-5">

                                        <strong>
                                            {{ $field->label }}
                                        </strong>

                                        @if($field->is_required)

                                            <span class="text-danger">
                                                *
                                            </span>

                                        @endif

                                    </div>


                                    <div class="col-md-7">

                                        @if($field->is_system)

                                            <span class="text-primary">

                                                <i class="bi bi-cpu"></i>

                                                Auto-generated

                                            </span>

                                            <div>
                                                <code>
                                                    {{ $field->field_key }}
                                                </code>
                                            </div>

                                        @else

                                            @if($field->field_type === 'checkbox')

                                                <div class="form-check">

                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        disabled
                                                    >

                                                    <label class="form-check-label">
                                                        Example value
                                                    </label>

                                                </div>

                                            @elseif($field->field_type === 'textarea')

                                                <textarea
                                                    class="form-control"
                                                    rows="2"
                                                    placeholder="{{ $field->placeholder }}"
                                                    disabled></textarea>

                                            @elseif($field->field_type === 'select')

                                                <select
                                                    class="form-select"
                                                    disabled>

                                                    <option>
                                                        Select...
                                                    </option>

                                                    @if($field->options)

                                                        @foreach(
                                                            preg_split(
                                                                '/\r\n|\r|\n/',
                                                                $field->options
                                                            ) as $option
                                                        )

                                                            @if(trim($option))

                                                                <option>
                                                                    {{ trim($option) }}
                                                                </option>

                                                            @endif

                                                        @endforeach

                                                    @endif

                                                </select>

                                            @else

                                                <input
                                                    type="{{ $field->field_type }}"
                                                    class="form-control"
                                                    placeholder="{{ $field->placeholder }}"
                                                    value="{{ $field->default_value }}"
                                                    disabled>

                                            @endif

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        @else

                            <div class="text-center py-5">

                                <i class="bi bi-ui-checks-grid fs-1 text-muted"></i>

                                <h6 class="mt-3">
                                    No fields configured
                                </h6>

                            </div>

                        @endif


                        {{-- Footer --}}
                        <div class="border-top pt-4 mt-4 text-center">

                            <small class="text-muted">
                                This is an administrative preview.
                                Actual values are populated during
                                document generation.
                            </small>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Field Mapping --}}
            <div class="card shadow-sm">

                <div class="card-header">

                    <strong>
                        Field Mapping
                    </strong>

                </div>


                <div class="card-body p-0">

                    @if($fields->count())

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            #
                                        </th>

                                        <th>
                                            Field Key
                                        </th>

                                        <th>
                                            Label
                                        </th>

                                        <th>
                                            Type
                                        </th>

                                        <th>
                                            Source
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($fields as $field)

                                        <tr>

                                            <td>
                                                {{ $field->sort_order }}
                                            </td>


                                            <td>

                                                <code>
                                                    {{ $field->field_key }}
                                                </code>

                                            </td>


                                            <td>
                                                {{ $field->label }}
                                            </td>


                                            <td>

                                                <span class="badge bg-light text-dark border">

                                                    {{ ucfirst(
                                                        $field->field_type
                                                    ) }}

                                                </span>

                                            </td>


                                            <td>

                                                @if($field->is_system)

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

                                                @if($field->is_system)

                                                    @if(
                                                        $field->is_supported_system_field
                                                    )

                                                        <span class="text-success">

                                                            <i class="bi bi-check-circle"></i>
                                                            Supported

                                                        </span>

                                                    @else

                                                        <span class="text-danger">

                                                            <i class="bi bi-x-circle"></i>
                                                            Unsupported

                                                        </span>

                                                    @endif

                                                @else

                                                    <span class="text-success">

                                                        <i class="bi bi-check-circle"></i>
                                                        Manual Input

                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="text-center py-5">

                            <p class="text-muted mb-0">
                                No fields configured.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- RIGHT --}}
        <div class="col-lg-4">


            {{-- Document Information --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <strong>
                        Document Information
                    </strong>

                </div>


                <div class="card-body">

                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Name
                        </small>

                        <strong>
                            {{ $document->name }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Code
                        </small>

                        <code>
                            {{ $document->code }}
                        </code>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Type
                        </small>

                        {{ ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $document->type ?? 'document'
                            )
                        ) }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Validity
                        </small>

                        @if($document->validity_type === 'none')

                            No expiry

                        @elseif($document->validity_type === 'year_end')

                            End of current year

                        @elseif($document->validity_type === 'fixed_date')

                            {{ optional(
                                $document->validity_date
                            )->format('d M Y') }}

                        @else

                            {{ $document->validity_value }}
                            {{ $document->validity_type }}

                        @endif

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Status
                        </small>

                        @if($document->is_active)

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


            {{-- Field Summary --}}
            <div class="card shadow-sm mb-4">

                <div class="card-header">

                    <strong>
                        Field Summary
                    </strong>

                </div>


                <div class="card-body">

                    @php

                        $systemCount =
                            $fields->where(
                                'is_system',
                                true
                            )->count();

                        $manualCount =
                            $fields->where(
                                'is_system',
                                false
                            )->count();

                        $requiredCount =
                            $fields->where(
                                'is_required',
                                true
                            )->count();

                    @endphp


                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Total Fields
                        </span>

                        <strong>
                            {{ $fields->count() }}
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            System Fields
                        </span>

                        <span class="badge bg-primary">
                            {{ $systemCount }}
                        </span>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Manual Fields
                        </span>

                        <span class="badge bg-secondary">
                            {{ $manualCount }}
                        </span>

                    </div>


                    <div class="d-flex justify-content-between">

                        <span>
                            Required Fields
                        </span>

                        <span class="badge bg-danger">
                            {{ $requiredCount }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- Payment Items --}}
            <div class="card shadow-sm">

                <div class="card-header">

                    <strong>
                        Attached Payment Items
                    </strong>

                </div>


                <div class="card-body">

                    @if($document->paymentItems->count())

                        @foreach($document->paymentItems as $item)

                            <div class="border-bottom pb-3 mb-3">

                                <strong class="d-block">
                                    {{ $item->name }}
                                </strong>

                                <code class="small">
                                    {{ $item->code }}
                                </code>

                                <div class="mt-2">

                                    <span class="badge bg-light text-dark border">

                                        ₦{{ number_format(
                                            $item->amount,
                                            2
                                        ) }}

                                    </span>

                                    @if($item->is_active)

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

                        @endforeach

                    @else

                        <p class="text-muted mb-0">

                            No payment items are currently
                            attached to this document.

                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


@endsection
