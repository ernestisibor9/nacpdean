@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Index')

@section('member')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="mb-1">
                    Operational Rights Documents
                </h2>

                <p class="text-muted mb-0">
                    View, print and download your operational rights documents.
                </p>
            </div>

        </div>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if ($documents->count())
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>

                            <th>
                                Document Number
                            </th>

                            <th>
                                Document
                            </th>

                            <th>
                                Issued
                            </th>

                            <th>
                                Expires
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @foreach ($documents as $document)
                            <tr>

                                <td>
                                    <strong>
                                        {{ $document->document_number }}
                                    </strong>
                                </td>


                                <td>
                                    {{ $document->document_title }}
                                </td>


                                <td>
                                    {{ $document->issued_at?->format('d M Y') }}
                                </td>


                                <td>
                                    {{ $document->expires_at?->format('d M Y') }}
                                </td>


                                <td>

                                    @if ($document->isActive())
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @elseif($document->status === 'expired')
                                        <span class="badge bg-warning">
                                            Expired
                                        </span>
                                    @elseif($document->status === 'revoked')
                                        <span class="badge bg-danger">
                                            Revoked
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    @endif

                                </td>


                                <td>

                                    <div class="d-flex gap-2 flex-wrap">

                                        <a href="{{ route('member.documents.operational-rights.show', $document) }}"
                                            class="btn btn-sm btn-primary">
                                            View
                                        </a>


                                        <a href="{{ route('member.documents.operational-rights.pdf', $document) }}"
                                            target="_blank" class="btn btn-sm btn-secondary">
                                            View PDF
                                        </a>


                                        <a href="{{ route('member.documents.operational-rights.download', $document) }}"
                                            class="btn btn-sm btn-success">
                                            Download
                                        </a>


                                        <a href="{{ route('member.documents.operational-rights.print', $document) }}"
                                            target="_blank" class="btn btn-sm btn-dark">
                                            Print
                                        </a>

                                    </div>

                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>
        @else
            <div class="alert alert-info">

                You do not have any Operational Rights Documents yet.

            </div>
        @endif

    </div>
@endsection

