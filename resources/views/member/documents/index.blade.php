@extends('member.member_dashboard')

@section('title', 'NACPDEAN - My Documents')

@section('member')

<style>
    .doc-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .doc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .badge-soft-success { background-color: #e8f5e9; color: #2e7d32; }
    .badge-soft-warning { background-color: #fff8e1; color: #f57f17; }
    .badge-soft-danger  { background-color: #ffebee; color: #c62828; }
    .badge-soft-secondary { background-color: #f5f5f5; color: #616161; }

    .doc-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>

<div class="container-fluid py-4 px-lg-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">My Documents</h3>
            <p class="text-muted mb-0 small">Access, print, and manage all documents issued to your account.</p>
        </div>
    </div>

    {{-- Dynamic Alerts --}}
    @foreach (['success' => 'check-circle-fill', 'error' => 'x-circle-fill', 'info' => 'info-circle-fill'] as $type => $icon)
        @if(session($type))
            @php $alertClass = $type === 'error' ? 'danger' : $type; @endphp
            <div class="alert alert-{{ $alertClass }} alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4 rounded-3" role="alert">
                <i class="bi bi-{{ $icon }} fs-5 me-2"></i>
                <div>{{ session($type) }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    {{-- Empty State --}}
    @if($documents->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body py-5">
                <div class="bg-light d-inline-flex p-4 rounded-circle mb-3">
                    <i class="bi bi-folder-x display-5 text-secondary opacity-75"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">No Documents Available</h5>
                <p class="text-muted mx-auto mb-0" style="max-width: 420px;">
                    You do not have any issued documents yet. Generated documents will automatically appear here once relevant payments are completed.
                </p>
            </div>
        </div>

    {{-- Document Cards Grid --}}
    @else
        <div class="row g-4">
            @foreach($documents as $generatedDocument)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm doc-card bg-white">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- Card Header & Badge --}}
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="doc-icon-wrapper bg-light text-primary">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $generatedDocument->document?->name ?? 'Document' }}
                                        </h6>
                                        <small class="text-muted fw-semibold" style="font-size: 0.75rem;">
                                            CODE: {{ $generatedDocument->document?->code ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>

                                {{-- Status Badges --}}
                                @switch($generatedDocument->status)
                                    @case('active')
                                        <span class="badge badge-soft-success px-2.5 py-1.5 rounded-pill fw-semibold">
                                            <i class="bi bi-dot me-1"></i>Active
                                        </span>
                                        @break
                                    @case('expired')
                                        <span class="badge badge-soft-warning px-2.5 py-1.5 rounded-pill fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i>Expired
                                        </span>
                                        @break
                                    @case('revoked')
                                        <span class="badge badge-soft-danger px-2.5 py-1.5 rounded-pill fw-semibold">
                                            <i class="bi bi-x-circle me-1"></i>Revoked
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-soft-secondary px-2.5 py-1.5 rounded-pill fw-semibold">
                                            {{ ucfirst($generatedDocument->status) }}
                                        </span>
                                @endswitch
                            </div>

                            <hr class="my-3 text-muted opacity-25">

                            {{-- Metadata Grid --}}
                            <div class="bg-light p-3 rounded-3 mb-4">
                                <div class="row g-2">
                                    <div class="col-12 mb-2">
                                        <span class="text-muted d-block small" style="font-size: 0.75rem;">DOCUMENT NUMBER</span>
                                        <span class="fw-mono fw-bold text-dark font-monospace">
                                            {{ $generatedDocument->document_number ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block small" style="font-size: 0.75rem;">ISSUED</span>
                                        <span class="fw-semibold text-dark small">
                                            {{ $generatedDocument->issued_at?->format('d M, Y') ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block small" style="font-size: 0.75rem;">EXPIRES</span>
                                        <span class="fw-semibold text-dark small">
                                            {{ $generatedDocument->expires_at?->format('d M, Y') ?? 'Lifetime' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Action Footer --}}
                            <div class="mt-auto">
                                @if($generatedDocument->status === 'active')
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('member.documents.show', $generatedDocument) }}" class="btn btn-primary fw-semibold rounded-2 py-2">
                                            <i class="bi bi-eye me-1.5"></i> View Document
                                        </a>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <a href="{{ route('member.documents.print', $generatedDocument) }}" target="_blank" class="btn btn-outline-secondary w-100 fw-semibold rounded-2 py-1.5 small">
                                                    <i class="bi bi-printer me-1"></i> Print
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="{{ route('member.documents.download', $generatedDocument) }}" class="btn btn-success w-100 fw-semibold rounded-2 py-1.5 small">
                                                    <i class="bi bi-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                @elseif($generatedDocument->status === 'expired')
                                    <div class="d-grid gap-2">
                                        <div class="p-2 text-center rounded bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 small mb-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> This document has expired.
                                        </div>

                                        @if($generatedDocument->transaction?->paymentItem?->is_renewable)
                                            @if($generatedDocument->transaction->paymentItem->renewalPaymentItem)
                                                <a href="{{ route('member.documents.renew', $generatedDocument) }}" class="btn btn-warning fw-semibold rounded-2 py-2 text-dark">
                                                    <i class="bi bi-arrow-repeat me-1"></i> Renew Document
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-light text-muted w-100 fw-semibold rounded-2 py-2" disabled>
                                                    <i class="bi bi-exclamation-circle me-1"></i> Renewal Unconfigured
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-light text-muted w-100 fw-semibold rounded-2 py-2" disabled>
                                                <i class="bi bi-lock me-1"></i> Renewal Unavailable
                                            </button>
                                        @endif
                                    </div>

                                @elseif($generatedDocument->status === 'revoked')
                                    <button type="button" class="btn btn-light text-danger w-100 fw-semibold rounded-2 py-2" disabled>
                                        <i class="bi bi-x-circle me-1"></i> Document Revoked
                                    </button>

                                @else
                                    <button type="button" class="btn btn-light text-muted w-100 fw-semibold rounded-2 py-2" disabled>
                                        <i class="bi bi-lock me-1"></i> Document Unavailable
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
