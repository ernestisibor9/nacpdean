@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Edit Payment Item
            </h4>

            <p class="text-muted mb-0">
                {{ $paymentItem->name }}
            </p>

        </div>


        <a href="{{ route(
            'admin.payment-items.index'
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

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
                Payment Item Configuration
            </strong>

        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                      'admin.payment-items.update',
                      $paymentItem
                  ) }}">

                @csrf
                @method('PUT')


                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Payment Item Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old(
                                   'name',
                                   $paymentItem->name
                               ) }}"
                               required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Code --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Code
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old(
                                   'code',
                                   $paymentItem->code
                               ) }}"
                               required>

                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Type --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Payment Type
                            <span class="text-danger">*</span>
                        </label>

                        <select name="type"
                                class="form-select @error('type') is-invalid @enderror"
                                required>

                            @foreach([
                                'membership',
                                'member_fee',
                                'member_afforestation',
                                'additional',
                                'penalty',
                                'other'
                            ] as $type)

                                <option value="{{ $type }}"
                                    {{ old(
                                        'type',
                                        $paymentItem->type
                                    ) === $type ? 'selected' : '' }}>

                                    {{ ucwords(
                                        str_replace('_', ' ', $type)
                                    ) }}

                                </option>

                            @endforeach

                        </select>

                        @error('type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Amount --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Amount
                            <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₦
                            </span>

                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   min="0"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old(
                                       'amount',
                                       $paymentItem->amount
                                   ) }}"
                                   required>

                        </div>

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Category --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Membership Category
                        </label>

                        <select name="membership_category_id"
                                class="form-select">

                            <option value="">
                                None / All Categories
                            </option>

                            @foreach($membershipCategories as $category)

                                <option value="{{ $category->id }}"
                                    {{ old(
                                        'membership_category_id',
                                        $paymentItem->membership_category_id
                                    ) == $category->id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Document --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Document to Generate
                        </label>

                        <select name="document_id"
                                id="document_id"
                                class="form-select">

                            <option value="">
                                No Document
                            </option>

                            @foreach($documents as $document)

                                <option value="{{ $document->id }}"
                                    {{ old(
                                        'document_id',
                                        $paymentItem->document_id
                                    ) == $document->id ? 'selected' : '' }}>

                                    {{ $document->name }}
                                    — {{ $document->code }}

                                </option>

                            @endforeach

                        </select>

                        <div class="form-text">

                            This is the document that will be generated
                            automatically after successful payment.

                        </div>

                    </div>


                    {{-- Renewable --}}
                    <div class="col-md-6">

                        <div class="form-check form-switch mt-3">

                            <input type="hidden"
                                   name="is_renewable"
                                   value="0">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_renewable"
                                   value="1"
                                   id="is_renewable"
                                   {{ old(
                                       'is_renewable',
                                       $paymentItem->is_renewable
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_renewable">

                                Renewable Payment

                            </label>

                        </div>

                    </div>


                    {{-- Active --}}
                    <div class="col-md-6">

                        <div class="form-check form-switch mt-3">

                            <input type="hidden"
                                   name="is_active"
                                   value="0">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   value="1"
                                   id="is_active"
                                   {{ old(
                                       'is_active',
                                       $paymentItem->is_active
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_active">

                                Active

                            </label>

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                {{-- Current Document --}}
                @if($paymentItem->document)

                    <div class="alert alert-success">

                        <div class="d-flex align-items-start">

                            <i class="bi bi-file-earmark-check fs-4 me-3"></i>

                            <div>

                                <strong>
                                    Document Attached
                                </strong>

                                <div class="mt-1">

                                    {{ $paymentItem->document->name }}

                                </div>

                                <code>
                                    {{ $paymentItem->document->code }}
                                </code>

                            </div>

                        </div>

                    </div>

                @else

                    <div class="alert alert-warning">

                        <strong>
                            No Document Attached
                        </strong>

                        <p class="mb-0 mt-1">

                            If this payment item is expected to generate
                            a document, select one above.

                        </p>

                    </div>

                @endif


                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route(
                        'admin.payment-items.index'
                    ) }}"
                       class="btn btn-outline-secondary">

                        Cancel

                    </a>


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-lg"></i>
                        Update Payment Item

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

