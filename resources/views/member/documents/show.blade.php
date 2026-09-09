@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Document')

@section('member')

<style>
    /* =========================================================
       NACPDEAN GENERATED DOCUMENT VIEW
    ========================================================= */

    .nacp-generated-document-page {
        min-height: calc(100vh - 60px);
        background: #f5f7f6;
        padding: 24px 0 50px;
    }

    .nacp-generated-document-page * {
        box-sizing: border-box;
    }

    /* =========================================================
       TOP BAR
    ========================================================= */

    .nacp-document-toolbar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 15px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .035);
    }

    .nacp-document-toolbar-left {
        min-width: 0;
    }

    .nacp-document-toolbar-title {
        margin: 0;
        color: #111827;
        font-size: 17px;
        font-weight: 800;
    }

    .nacp-document-toolbar-number {
        margin-top: 3px;
        color: #6b7280;
        font-size: 12px;
        word-break: break-word;
    }

    .nacp-document-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
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
       DOCUMENT CONTAINER
    ========================================================= */

    .nacp-document-container {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 25px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .055);
        overflow-x: auto;
    }

    .nacp-document-content {
        width: 100%;
        margin: 0 auto;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 768px) {

        .nacp-generated-document-page {
            padding: 15px 0 35px;
        }

        .nacp-generated-document-page .container-fluid {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .nacp-document-toolbar {
            padding: 14px;
            flex-direction: column;
            align-items: stretch;
        }

        .nacp-document-toolbar-actions {
            width: 100%;
        }

        .nacp-document-toolbar-actions .nacp-doc-btn,
        .nacp-document-toolbar-actions .nacp-doc-btn-primary {
            flex: 1;
        }

        .nacp-document-container {
            padding: 12px;
            border-radius: 14px;
        }
    }

    /* =========================================================
       PRINT
    ========================================================= */

    @media print {

        body {
            background: #fff !important;
        }

        .nacp-document-toolbar {
            display: none !important;
        }

        .nacp-generated-document-page {
            padding: 0 !important;
            background: #fff !important;
        }

        .nacp-document-container {
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }
    }
</style>


<div class="nacp-generated-document-page">

    <div class="container-fluid px-4">

        {{-- =====================================================
             DOCUMENT TOOLBAR
        ====================================================== --}}

        <div class="nacp-document-toolbar">

            <div class="nacp-document-toolbar-left">

                <h5 class="nacp-document-toolbar-title">

                    {{ $generatedDocument->document->name ?? 'NACPDEAN Document' }}

                </h5>

                @if ($generatedDocument->document_number)

                    <div class="nacp-document-toolbar-number">

                        Document No:
                        <strong>
                            {{ $generatedDocument->document_number }}
                        </strong>

                    </div>

                @endif

            </div>


            <div class="nacp-document-toolbar-actions">

                <a href="{{ route('member.member_dashboard') }}"
                    class="nacp-doc-btn">

                    <i class="fas fa-arrow-left"></i>

                    Back

                </a>


                <a href="{{ route('member.documents.print', $generatedDocument->id) }}"
                    class="nacp-doc-btn"
                    target="_blank">

                    <i class="fas fa-print"></i>

                    Print

                </a>


                <a href="{{ route('member.documents.download', $generatedDocument->id) }}"
                    class="nacp-doc-btn-primary">

                    <i class="fas fa-download"></i>

                    Download PDF

                </a>

            </div>

        </div>


        {{-- =====================================================
             GENERATED DOCUMENT
        ====================================================== --}}

        <div class="nacp-document-container">

            <div class="nacp-document-content">

                @include($template)

            </div>

        </div>

    </div>

</div>

@endsection
