@extends('frontend.master')

@section('home')


<section class="py-5" style="background:#f6faf7;">
    <div class="container">

        <h1 style="font-weight:800;margin-bottom:10px;">Stakeholders Directory</h1>
        <p class="text-muted mb-5">Official NACPDEAN stakeholder directory.</p>

        @if ($files->isEmpty())

            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No stakeholders have been added yet.</p>
            </div>

        @else

            <div class="row g-4">
                @foreach ($files as $file)

                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">

                            @if ($file->isImage())
                                <img src="{{ $file->fileUrl() }}"
                                     alt="{{ $file->title }}"
                                     style="height:220px;object-fit:cover;border-radius:14px 14px 0 0;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light"
                                     style="height:220px;border-radius:14px 14px 0 0;">
                                    <i class="fas fa-file-pdf fa-4x text-danger"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold mb-3">{{ $file->title }}</h6>

                                <a href="{{ route('stakeholders-data.view', $file) }}"
                                   target="_blank"
                                   class="btn btn-success mt-auto">
                                    <i class="fas fa-eye me-1"></i>
                                    {{ $file->isImage() ? 'View Image' : 'View PDF' }}
                                </a>
                            </div>

                        </div>
                    </div>

                @endforeach
            </div>

        @endif

    </div>
</section>


@endsection
