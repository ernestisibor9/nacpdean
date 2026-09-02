@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection



<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Add Document Field
            </h4>

            <div class="text-muted">
                {{ $document->name }}
            </div>

        </div>


        <a href="{{ route(
            'admin.documents.fields.index',
            $document
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back to Fields

        </a>

    </div>


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


    <div class="card shadow-sm">

        <div class="card-header">

            <strong>
                Field Configuration
            </strong>

        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                      'admin.documents.fields.store',
                      $document
                  ) }}">

                @csrf


                @include(
                    'admin.documents.fields._form'
                )


                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route(
                        'admin.documents.fields.index',
                        $document
                    ) }}"
                       class="btn btn-outline-secondary">

                        Cancel

                    </a>


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-lg"></i>
                        Create Field

                    </button>

                </div>

            </form>

        </div>

    </div>


    <div class="alert alert-info mt-4">

        <strong>Examples of system fields:</strong>

        <div class="mt-2">

            <code>receipt_no</code>,
            <code>member_name</code>,
            <code>membership_no</code>,
            <code>phone</code>,
            <code>payment_amount</code>,
            <code>payment_reference</code>,
            <code>payment_date</code>,
            <code>lifting_right_no</code>

        </div>

    </div>

</div>


@endsection





