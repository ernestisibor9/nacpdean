@extends('admin.admin_dashboard')

@section('admin')

@section('title')
    NACPDEAN - Admin Dashboard
@endsection


<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Create Payment Item
            </h4>

            <p class="text-muted mb-0">
                Create a fee and optionally attach a document.
            </p>
        </div>

        <a href="{{ route('admin.payment-items.index') }}"
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
                Payment Item Details
            </strong>
        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                      'admin.payment-items.store'
                  ) }}">

                @csrf


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
                               value="{{ old('name') }}"
                               placeholder="e.g. Afforestation Fee – Export Container"
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
                               value="{{ old('code') }}"
                               placeholder="e.g. AFF-MEMBER-EXPORT-CONTAINER"
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

                            <option value="">
                                Select Type
                            </option>

                            @foreach([
                                'membership',
                                'member_fee',
                                'member_afforestation',
                                'additional',
                                'penalty',
                                'other'
                            ] as $type)

                                <option value="{{ $type }}"
                                    {{ old('type') === $type ? 'selected' : '' }}>

                                    {{ ucwords(str_replace('_', ' ', $type)) }}

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
                                   value="{{ old('amount') }}"
                                   required>

                        </div>

                        @error('amount')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Membership Category --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Membership Category
                        </label>

                        <select name="membership_category_id"
                                class="form-select @error('membership_category_id') is-invalid @enderror">

                            <option value="">
                                None / All Categories
                            </option>

                            @foreach($membershipCategories as $category)

                                <option value="{{ $category->id }}"
                                    {{ old('membership_category_id') == $category->id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('membership_category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Document --}}
                    <div class="col-md-12">

                        <label class="form-label">

                            Document to Generate

                        </label>

                        <select name="document_id"
                                id="document_id"
                                class="form-select @error('document_id') is-invalid @enderror">

                            <option value="">
                                No Document
                            </option>

                            @foreach($documents as $document)

                                <option value="{{ $document->id }}"
                                    {{ old('document_id') == $document->id ? 'selected' : '' }}>

                                    {{ $document->name }}
                                    — {{ $document->code }}

                                </option>

                            @endforeach

                        </select>

                        <div class="form-text">

                            After successful payment, this document will be
                            generated automatically for the member.

                        </div>

                        @error('document_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

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
                                   {{ old('is_renewable') ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_renewable">

                                Renewable Payment

                            </label>

                        </div>

                        <small class="text-muted">
                            Allows this payment item to be used again
                            when renewal is required.
                        </small>

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
                                   {{ old('is_active', true) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_active">

                                Active

                            </label>

                        </div>

                    </div>

                </div>


                <hr class="my-4">


                <div class="alert alert-info">

                    <strong>
                        Payment Item → Document
                    </strong>

                    <p class="mb-0 mt-1">

                        If a document is selected, the successful payment
                        will automatically trigger document generation
                        using that document's configured fields.

                    </p>

                </div>


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
                        Create Payment Item

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

