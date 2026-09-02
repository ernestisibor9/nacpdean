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
                Payment Items
            </h4>

            <p class="text-muted mb-0">
                Manage fees, payments and their associated documents.
            </p>
        </div>

        <a href="{{ route('admin.payment-items.create') }}" class="btn btn-primary">

            <i class="bi bi-plus-lg"></i>
            Add Payment Item

        </a>

    </div>


    {{-- Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- Error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-body p-0">

            @if ($paymentItems->count())
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Payment Item
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Membership Category
                                </th>

                                <th>
                                    Document
                                </th>

                                <th>
                                    Renewable
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($paymentItems as $item)
                                <tr>

                                    {{-- ID --}}
                                    <td>
                                        {{ $item->id }}
                                    </td>


                                    {{-- Name --}}
                                    <td>

                                        <strong>
                                            {{ $item->name }}
                                        </strong>

                                    </td>


                                    {{-- Code --}}
                                    <td>

                                        <code>
                                            {{ $item->code }}
                                        </code>

                                    </td>


                                    {{-- Type --}}
                                    <td>

                                        <span class="badge bg-light text-dark border">
                                            {{ $item->type }}
                                        </span>

                                    </td>


                                    {{-- Amount --}}
                                    <td>

                                        <strong>
                                            ₦{{ number_format($item->amount, 2) }}
                                        </strong>

                                    </td>


                                    {{-- Membership Category --}}
                                    <td>

                                        @if ($item->membershipCategory)
                                            <span>
                                                {{ $item->membershipCategory->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                All / None
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Document --}}
                                    <td>

                                        @if ($item->document)
                                            <div>

                                                <span class="badge bg-success">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                    Attached
                                                </span>

                                            </div>

                                            <small class="d-block mt-1">

                                                {{ $item->document->name }}

                                            </small>

                                            <code class="small">
                                                {{ $item->document->code }}
                                            </code>

                                            <div class="mt-1">

                                                <a href="{{ route('admin.documents.preview', $item->document) }}"
                                                    class="btn btn-sm btn-outline-info">

                                                    <i class="bi bi-eye"></i>
                                                    Preview Document

                                                </a>

                                            </div>
                                        @else
                                            <span class="badge bg-danger">

                                                <i class="bi bi-exclamation-triangle"></i>
                                                No Document

                                            </span>

                                            <small class="d-block text-danger mt-1">
                                                Payment cannot generate a document.
                                            </small>
                                        @endif

                                    </td>


                                    {{-- Renewable --}}
                                    <td>

                                        @if ($item->is_renewable)
                                            <span class="badge bg-info text-dark">
                                                Yes
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                No
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if ($item->is_active)
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end">

                                        <a href="{{ route('admin.payment-items.edit', $item) }}"
                                            class="btn btn-sm btn-outline-primary">

                                            <i class="bi bi-pencil"></i>
                                            Edit

                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if ($paymentItems->hasPages())
                    <div class="p-3">

                        {{ $paymentItems->links() }}

                    </div>
                @endif
            @else
                <div class="text-center py-5">

                    <i class="bi bi-wallet2 fs-1 text-muted"></i>

                    <h5 class="mt-3">
                        No Payment Items
                    </h5>

                    <p class="text-muted">
                        Create your first payment item.
                    </p>

                    <a href="{{ route('admin.payment-items.create') }}"
                        class="btn btn-primary">

                        Add Payment Item

                    </a>

                </div>
            @endif

        </div>

    </div>


    {{-- Important explanation --}}
    <div class="alert alert-info mt-4">

        <strong>
            Document Generation
        </strong>

        <p class="mb-0 mt-1">

            When a member successfully pays for a payment item,
            the system uses the <strong>Document</strong> attached
            to that payment item to generate the member's document.

            Payment items that require a document should therefore
            always have a document attached.

        </p>

    </div>

</div>

@endsection
