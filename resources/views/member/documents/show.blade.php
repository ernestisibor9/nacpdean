@extends('member.member_dashboard')

@section('title', 'NACPDEAN - View Document')

@section('member')

<div class="container-fluid py-4">

    {{-- =========================================================
         TOP BAR
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                {{-- Document Information --}}
                <div>

                    <h4 class="mb-1">
                        {{ $document->name ?? 'Document' }}
                    </h4>

                    <div class="text-muted small">

                        {{ $document->code ?? 'N/A' }}

                        @if($generatedDocument->document_number)

                            <span class="mx-2">•</span>

                            {{ $generatedDocument->document_number }}

                        @endif

                    </div>

                </div>


                {{-- Actions --}}
                <div class="d-flex flex-wrap gap-2">

                    {{-- Back --}}
                    <a
                        href="{{ route('member.documents.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-left me-1"></i>
                        Back
                    </a>


                    {{-- Print --}}
                    <a
                        href="{{ route('member.documents.print', $generatedDocument) }}"
                        target="_blank"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-printer me-1"></i>
                        Print
                    </a>


                    {{-- Download --}}
                    <a
                        href="{{ route('member.documents.download', $generatedDocument) }}"
                        class="btn btn-success"
                    >
                        <i class="bi bi-download me-1"></i>
                        Download PDF
                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         DOCUMENT
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="document-preview">

                @include($template, [
                    'generatedDocument' => $generatedDocument,
                    'document'          => $document,
                    'qrCode'             => $qrCode,
                    'printMode'          => false,
                    'downloadMode'      => false,
                ])

            </div>

        </div>

    </div>

</div>


<style>

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT PREVIEW
    |--------------------------------------------------------------------------
    */

    .document-preview {
        width: 100%;
        overflow-x: auto;
        background: #f5f5f5;
        padding: 25px;
    }


    /*
    |--------------------------------------------------------------------------
    | Prevent the document from shrinking strangely
    |--------------------------------------------------------------------------
    */

    .document-preview > * {
        margin-left: auto;
        margin-right: auto;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (max-width: 768px) {

        .document-preview {
            padding: 10px;
        }

    }

</style>

@endsection
