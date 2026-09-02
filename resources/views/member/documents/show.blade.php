@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Index')

@section('member')


    <div class="container py-4">

        {{-- ================================================================
         PAGE HEADER
    ================================================================= --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="mb-1">
                    {{ $generatedDocument->document->name }}
                </h2>

                <p class="text-muted mb-0">
                    {{ $generatedDocument->document->code }}
                </p>
            </div>

            <div class="d-flex gap-2">

                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    Print
                </button>

                {{-- Future PDF download --}}
                <button type="button" class="btn btn-primary" disabled>
                    <i class="bi bi-download"></i>
                    Download
                </button>

            </div>

        </div>


        {{-- ================================================================
         DOCUMENT STATUS
    ================================================================= --}}

        <div class="mb-4">

            @if ($generatedDocument->status === 'active')
                <span class="badge bg-success">
                    Active
                </span>
            @elseif($generatedDocument->status === 'expired')
                <span class="badge bg-warning text-dark">
                    Expired
                </span>
            @elseif($generatedDocument->status === 'revoked')
                <span class="badge bg-danger">
                    Revoked
                </span>
            @endif

        </div>


        <div class="row g-4">

            {{-- ============================================================
             DOCUMENT DEFINITION
        ============================================================= --}}

            <div class="col-lg-6">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-white">

                        <h5 class="mb-0">
                            Document Information
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="text-muted small">
                                Document Name
                            </label>

                            <div class="fw-semibold">
                                {{ $generatedDocument->document->name }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="text-muted small">
                                Document Code
                            </label>

                            <div class="fw-semibold">
                                {{ $generatedDocument->document->code }}
                            </div>

                        </div>


                        @if ($generatedDocument->document->description)
                            <div class="mb-3">

                                <label class="text-muted small">
                                    Description
                                </label>

                                <div>
                                    {{ $generatedDocument->document->description }}
                                </div>

                            </div>
                        @endif

                    </div>

                </div>

            </div>


            {{-- ============================================================
             GENERATED DOCUMENT INFORMATION
        ============================================================= --}}

            <div class="col-lg-6">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-white">

                        <h5 class="mb-0">
                            Issued Document
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="mb-3">

                            <label class="text-muted small">
                                Document Number
                            </label>

                            <div class="fw-bold fs-5">
                                {{ $generatedDocument->document_number }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <label class="text-muted small">
                                Tracking Code
                            </label>

                            <div class="fw-semibold">
                                {{ $generatedDocument->tracking_code }}
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="text-muted small">
                                    Issued Date
                                </label>

                                <div>
                                    {{ $generatedDocument->issued_at?->format('d M Y') ?? '—' }}
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="text-muted small">
                                    Expiry Date
                                </label>

                                <div>
                                    {{ $generatedDocument->expires_at?->format('d M Y') ?? '—' }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ============================================================
             PAYMENT INFORMATION
        ============================================================= --}}

            <div class="col-12">

                <div class="card shadow-sm">

                    <div class="card-header bg-white">

                        <h5 class="mb-0">
                            Payment Information
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="text-muted small">
                                    Payment Item
                                </label>

                                <div class="fw-semibold">
                                    {{ $generatedDocument->transaction->paymentItem->name }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="text-muted small">
                                    Amount
                                </label>

                                <div class="fw-semibold">
                                    ₦{{ number_format((float) $generatedDocument->transaction->amount, 2) }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="text-muted small">
                                    Transaction Status
                                </label>

                                <div>

                                    <span class="badge bg-success">
                                        {{ ucfirst($generatedDocument->transaction->status) }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="text-muted small">
                                    Transaction ID
                                </label>

                                <div>
                                    #{{ $generatedDocument->transaction->id }}
                                </div>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="text-muted small">
                                    Payment Type
                                </label>

                                <div>
                                    {{ ucfirst($generatedDocument->transaction->type) }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ============================================================
             FUTURE DYNAMIC FIELDS
        ============================================================= --}}

            <div class="col-12">

                <div class="card shadow-sm">

                    <div class="card-header bg-white">

                        <h5 class="mb-0">
                            Document Details
                        </h5>

                    </div>

                    <div class="card-body">

                        @if ($generatedDocument->field_values && count($generatedDocument->field_values))

                            <div class="row">

                                @foreach ($generatedDocument->field_values as $key => $value)
                                    <div class="col-md-6 mb-3">

                                        <label class="text-muted small">
                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                        </label>

                                        <div>
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="text-muted">

                                Document fields will appear here.

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
     PRINT STYLES
================================================================= --}}

    <style>
        @media print {

            .navbar,
            .sidebar,
            footer,
            .btn,
            button {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

        }
    </style>

@endsection
