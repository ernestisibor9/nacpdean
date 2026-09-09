@extends('member.member_dashboard')

@section('title', 'NACPDEAN - My Documents')

@section('member')


<style>
    /* =========================================================
       NACPDEAN MEMBER DOCUMENTS
    ========================================================= */

    .nacp-documents-page {
        min-height: calc(100vh - 60px);
        background: #f5f7f6;
        padding: 24px 0 50px;
    }

    .nacp-documents-page * {
        box-sizing: border-box;
    }

    /* =========================================================
       HERO
    ========================================================= */

    .nacp-documents-hero {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(135deg, #064e3b 0%, #047857 55%, #059669 100%);
        border-radius: 20px;
        padding: 30px 32px;
        margin-bottom: 24px;
        color: #fff;
        box-shadow: 0 12px 35px rgba(6, 78, 59, .16);
    }

    .nacp-documents-hero::before {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .055);
        right: -100px;
        top: -170px;
    }

    .nacp-documents-hero::after {
        content: "";
        position: absolute;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .045);
        right: 180px;
        bottom: -150px;
    }

    .nacp-documents-hero-content {
        position: relative;
        z-index: 2;
    }

    .nacp-documents-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 30px;
        background: rgba(255, 255, 255, .11);
        border: 1px solid rgba(255, 255, 255, .13);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .4px;
        margin-bottom: 14px;
    }

    .nacp-documents-hero h1 {
        margin: 0 0 7px;
        font-size: 29px;
        line-height: 1.25;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .nacp-documents-hero p {
        margin: 0;
        color: rgba(255, 255, 255, .82);
        font-size: 15px;
        line-height: 1.6;
    }

    /* =========================================================
       MAIN CARD
    ========================================================= */

    .nacp-documents-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .035);
        overflow: hidden;
    }

    .nacp-documents-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #f0f1f2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .nacp-documents-card-title {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 800;
    }

    .nacp-documents-card-subtitle {
        color: #6b7280;
        font-size: 13px;
        margin: 4px 0 0;
        line-height: 1.5;
    }

    .nacp-documents-card-body {
        padding: 22px;
    }

    /* =========================================================
       DOCUMENT ITEM
    ========================================================= */

    .nacp-document-item {
        height: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px;
        background: #fff;
        transition: all .2s ease;
    }

    .nacp-document-item:hover {
        border-color: #a7f3d0;
        box-shadow: 0 8px 22px rgba(0, 0, 0, .055);
        transform: translateY(-2px);
    }

    .nacp-document-icon {
        width: 46px;
        height: 46px;
        border-radius: 11px;
        background: #ecfdf5;
        color: #047857;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 15px;
    }

    .nacp-document-name {
        margin: 0 0 6px;
        color: #111827;
        font-size: 15px;
        font-weight: 800;
        line-height: 1.4;
    }

    .nacp-document-code {
        color: #6b7280;
        font-size: 11px;
        line-height: 1.5;
        word-break: break-word;
        margin-bottom: 12px;
    }

    .nacp-document-detail {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 9px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .nacp-document-detail-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .nacp-document-detail-value {
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        text-align: right;
        word-break: break-word;
    }

    /* =========================================================
       STATUS
    ========================================================= */

    .nacp-document-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 20px;
        background: #ecfdf5;
        color: #047857;
        font-size: 12px;
        font-weight: 700;
    }

    /* =========================================================
       BUTTONS
    ========================================================= */

    .nacp-doc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 10px 15px;
        background: #fff;
        color: #374151 !important;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .nacp-doc-btn:hover {
        border-color: #047857;
        color: #047857 !important;
        background: #f0fdf4;
    }

    .nacp-doc-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 0;
        border-radius: 9px;
        padding: 10px 15px;
        background: #047857;
        color: #fff !important;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .nacp-doc-btn-primary:hover {
        background: #065f46;
        color: #fff !important;
    }

    /* =========================================================
       EMPTY STATE
    ========================================================= */

    .nacp-document-empty {
        text-align: center;
        padding: 60px 25px;
    }

    .nacp-document-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 15px;
        border-radius: 16px;
        background: #f3f4f6;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
    }

    .nacp-document-empty h6 {
        color: #374151;
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .nacp-document-empty p {
        max-width: 550px;
        margin: 0 auto;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.7;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 768px) {

        .nacp-documents-page {
            padding: 15px 0 35px;
        }

        .nacp-documents-page .container-fluid {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .nacp-documents-hero {
            padding: 24px 21px;
            border-radius: 16px;
        }

        .nacp-documents-hero h1 {
            font-size: 24px;
        }

        .nacp-documents-hero p {
            font-size: 13px;
        }

        .nacp-documents-hero-badge {
            font-size: 11px;
        }

        .nacp-documents-card-header {
            padding: 17px;
        }

        .nacp-documents-card-title {
            font-size: 16px;
        }

        .nacp-documents-card-subtitle {
            font-size: 12px;
        }

        .nacp-documents-card-body {
            padding: 17px;
        }

        .nacp-document-item {
            padding: 17px;
        }
    }
</style>


<div class="nacp-documents-page">

    <div class="container-fluid px-4">

        {{-- =====================================================
             HERO
        ====================================================== --}}

        <div class="nacp-documents-hero">

            <div class="nacp-documents-hero-content">

                <div class="nacp-documents-hero-badge">
                    <i class="fas fa-file-alt"></i>
                    NACPDEAN MEMBER PORTAL
                </div>

                <h1>
                    My Documents
                </h1>

                <p>
                    View and manage your official NACPDEAN membership documents.
                </p>

            </div>

        </div>


        {{-- =====================================================
             SUCCESS MESSAGE
        ====================================================== --}}

        @if (session('success'))

            <div class="alert alert-success"
                style="border:0;border-radius:13px;padding:16px 19px;margin-bottom:22px;box-shadow:0 4px 15px rgba(0,0,0,.035);font-size:14px;line-height:1.6;">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button"
                    class="btn-close float-end"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        {{-- =====================================================
             DOCUMENTS
        ====================================================== --}}

        <div class="nacp-documents-card">

            <div class="nacp-documents-card-header">

                <div>

                    <h5 class="nacp-documents-card-title">
                        My Membership Documents
                    </h5>

                    <p class="nacp-documents-card-subtitle">
                        Official documents issued to you by NACPDEAN.
                    </p>

                </div>

                <a href="{{ route('member.member_dashboard') }}"
                    class="nacp-doc-btn">

                    <i class="fas fa-arrow-left"></i>

                    Dashboard

                </a>

            </div>


            <div class="nacp-documents-card-body">

                @if ($generatedDocuments->isNotEmpty())

                    <div class="row g-3">

                        @foreach ($generatedDocuments as $generatedDocument)

                            <div class="col-xl-4 col-lg-6 col-md-6">

                                <div class="nacp-document-item">

                                    {{-- DOCUMENT ICON --}}

                                    <div class="nacp-document-icon">

                                        <i class="fas fa-file-alt"></i>

                                    </div>


                                    {{-- DOCUMENT NAME --}}

                                    <h6 class="nacp-document-name">

                                        {{ $generatedDocument->document->name ?? 'Membership Document' }}

                                    </h6>


                                    {{-- DOCUMENT CODE --}}

                                    @if ($generatedDocument->document)

                                        <div class="nacp-document-code">

                                            {{ $generatedDocument->document->code }}

                                        </div>

                                    @endif


                                    {{-- DOCUMENT NUMBER --}}

                                    @if ($generatedDocument->document_number)

                                        <div class="nacp-document-detail">

                                            <span class="nacp-document-detail-label">
                                                Document No.
                                            </span>

                                            <span class="nacp-document-detail-value">
                                                {{ $generatedDocument->document_number }}
                                            </span>

                                        </div>

                                    @endif


                                    {{-- ISSUE DATE --}}

                                    @if ($generatedDocument->issued_at)

                                        <div class="nacp-document-detail">

                                            <span class="nacp-document-detail-label">
                                                Issued
                                            </span>

                                            <span class="nacp-document-detail-value">

                                                {{ \Carbon\Carbon::parse($generatedDocument->issued_at)->format('d M Y') }}

                                            </span>

                                        </div>

                                    @endif


                                    {{-- EXPIRATION --}}

                                    @if ($generatedDocument->expires_at)

                                        <div class="nacp-document-detail">

                                            <span class="nacp-document-detail-label">
                                                Expires
                                            </span>

                                            <span class="nacp-document-detail-value">

                                                {{ \Carbon\Carbon::parse($generatedDocument->expires_at)->format('d M Y') }}

                                            </span>

                                        </div>

                                    @endif


                                    {{-- STATUS --}}

                                    <div class="mt-3 mb-3">

                                        @if ($generatedDocument->status === 'active')

                                            <span class="nacp-document-status">

                                                <i class="fas fa-check-circle"></i>

                                                Active

                                            </span>

                                        @elseif ($generatedDocument->status === 'expired')

                                            <span class="nacp-document-status"
                                                style="background:#fef2f2;color:#dc2626;">

                                                <i class="fas fa-clock"></i>

                                                Expired

                                            </span>

                                        @elseif ($generatedDocument->status === 'revoked')

                                            <span class="nacp-document-status"
                                                style="background:#fef2f2;color:#dc2626;">

                                                <i class="fas fa-ban"></i>

                                                Revoked

                                            </span>

                                        @else

                                            <span class="nacp-document-status"
                                                style="background:#fffbeb;color:#b45309;">

                                                <i class="fas fa-info-circle"></i>

                                                {{ ucfirst($generatedDocument->status ?? 'Unknown') }}

                                            </span>

                                        @endif

                                    </div>


                                    {{-- ACTIONS --}}

                                    <div class="d-flex gap-2">

                                        <a href="{{ route('member.documents.show', $generatedDocument->id) }}"
                                            class="nacp-doc-btn-primary flex-grow-1">

                                            <i class="fas fa-eye"></i>

                                            View

                                        </a>

                                        <a href="{{ route('member.documents.print', $generatedDocument->id) }}"
                                            class="nacp-doc-btn"
                                            title="Print document">

                                            <i class="fas fa-print"></i>

                                        </a>

                                        <a href="{{ route('member.documents.download', $generatedDocument->id) }}"
                                            class="nacp-doc-btn"
                                            title="Download PDF">

                                            <i class="fas fa-download"></i>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    {{-- =================================================
                         EMPTY STATE
                    ================================================== --}}

                    <div class="nacp-document-empty">

                        <div class="nacp-document-empty-icon">

                            <i class="fas fa-file-alt"></i>

                        </div>

                        <h6>
                            No Membership Documents Available
                        </h6>

                        <p>
                            Your membership has been approved, but no membership
                            documents are currently available in your account.
                            Please check again after your documents have been generated.
                        </p>

                        <div class="mt-4">

                            <a href="{{ route('member.member_dashboard') }}"
                                class="nacp-doc-btn-primary">

                                <i class="fas fa-arrow-left"></i>

                                Return to Dashboard

                            </a>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


@endsection
