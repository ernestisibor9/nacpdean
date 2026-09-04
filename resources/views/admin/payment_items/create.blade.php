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


@if ($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please correct the following:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach ($errors->all() as $error)

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
            action="{{ route('admin.payment-items.store') }}">

            @csrf


            <div class="row g-3">


                {{-- =========================================================
                | PAYMENT ITEM NAME
                ========================================================== --}}

                <div class="col-md-6">

                    <label class="form-label">

                        Payment Item Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="e.g. Afforestation Fee – Export Container"
                        required
                    >

                    @error('name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================================================
                | CODE
                ========================================================== --}}

                <div class="col-md-6">

                    <label class="form-label">

                        Code

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="code"
                        class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code') }}"
                        placeholder="e.g. AFF-MEMBER-EXPORT-CONTAINER"
                        required
                    >

                    @error('code')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================================================
                | PAYMENT TYPE
                ========================================================== --}}

                <div class="col-md-4">

                    <label class="form-label">

                        Payment Type

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="type"
                        id="payment_type"
                        class="form-select @error('type') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Type
                        </option>

                        @foreach ([
                            'membership',
                            'membership_renewal',
                            'member_fee',
                            'member_afforestation',
                            'member_afforestation_renewal',
                            'additional',
                            'penalty',
                            'other'
                        ] as $type)

                            <option
                                value="{{ $type }}"
                                {{ old('type') === $type ? 'selected' : '' }}
                            >

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


                {{-- =========================================================
                | AMOUNT
                ========================================================== --}}

                <div class="col-md-4">

                    <label class="form-label">

                        Amount

                        <span class="text-danger">*</span>

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            ₦
                        </span>

                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="0"
                            class="form-control @error('amount') is-invalid @enderror"
                            value="{{ old('amount') }}"
                            required
                        >

                    </div>

                    @error('amount')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================================================
                | MEMBERSHIP CATEGORY
                ========================================================== --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Membership Category
                    </label>

                    <select
                        name="membership_category_id"
                        class="form-select @error('membership_category_id') is-invalid @enderror"
                    >

                        <option value="">
                            None / All Categories
                        </option>

                        @foreach ($membershipCategories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ old('membership_category_id') == $category->id ? 'selected' : '' }}
                            >

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


                {{-- =========================================================
                | DOCUMENT
                ========================================================== --}}

                <div class="col-md-12">

                    <label class="form-label">

                        Document to Generate

                    </label>

                    <select
                        name="document_id"
                        id="document_id"
                        class="form-select @error('document_id') is-invalid @enderror"
                    >

                        <option value="">
                            No Document
                        </option>

                        @foreach ($documents as $document)

                            <option
                                value="{{ $document->id }}"
                                {{ old('document_id') == $document->id ? 'selected' : '' }}
                            >

                                {{ $document->name }}
                                — {{ $document->code }}

                            </option>

                        @endforeach

                    </select>

                    <div class="form-text">

                        After successful payment, this document will be
                        generated automatically for the member.

                        <br>

                        <strong>
                            Membership renewal does not require a document here.
                        </strong>

                        The membership certificate is generated automatically
                        from the member's membership category configuration.

                    </div>

                    @error('document_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- =========================================================
                | RENEWABLE
                ========================================================== --}}

                <div class="col-md-6">

                    <div class="form-check form-switch mt-3">

                        <input
                            type="hidden"
                            name="is_renewable"
                            value="0"
                        >

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_renewable"
                            value="1"
                            id="is_renewable"
                            {{ old('is_renewable') ? 'checked' : '' }}
                        >

                        <label
                            class="form-check-label"
                            for="is_renewable"
                        >

                            Renewable Payment

                        </label>

                    </div>

                    <small class="text-muted">

                        Allows this payment item to be renewed
                        using a separate renewal payment item.

                    </small>

                </div>


                {{-- =========================================================
                | ACTIVE
                ========================================================== --}}

                <div class="col-md-6">

                    <div class="form-check form-switch mt-3">

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >

                        <label
                            class="form-check-label"
                            for="is_active"
                        >

                            Active

                        </label>

                    </div>

                    <small class="text-muted">

                        Inactive payment items cannot be selected
                        by members.

                    </small>

                </div>


                {{-- =========================================================
                | RENEWAL PAYMENT ITEM
                ========================================================== --}}

                <div class="col-md-6">

                    <label class="form-label">

                        Renewal Payment Item

                    </label>

                    <select
                        name="renewal_payment_item_id"
                        id="renewal_payment_item_id"
                        class="form-select @error('renewal_payment_item_id') is-invalid @enderror"
                    >

                        <option value="">
                            Select Renewal Payment Item
                        </option>

                        @foreach ($renewalPaymentItems as $renewalItem)

                            <option
                                value="{{ $renewalItem->id }}"
                                {{ old('renewal_payment_item_id') == $renewalItem->id ? 'selected' : '' }}
                            >

                                {{ $renewalItem->name }}
                                — {{ $renewalItem->code }}
                                — ₦{{ number_format($renewalItem->amount, 2) }}

                            </option>

                        @endforeach

                    </select>

                    <div class="form-text">

                        Select the payment item members will use
                        when this payment item needs to be renewed.

                        <br>

                        For membership renewal, select a payment item
                        whose type is
                        <strong>Membership Renewal</strong>.

                    </div>

                    @error('renewal_payment_item_id')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


            </div>


            <hr class="my-4">


            {{-- =========================================================
            | MEMBERSHIP RENEWAL INFORMATION
            ========================================================== --}}

            <div
                id="membershipRenewalInfo"
                class="alert alert-warning d-none"
            >

                <strong>
                    Membership Renewal
                </strong>

                <p class="mb-0 mt-1">

                    This payment type is used to renew an expired
                    membership. The member's existing membership number
                    will be retained, the membership will become active
                    again, and a new membership certificate will be
                    generated after successful payment.

                </p>

            </div>


            {{-- =========================================================
            | DOCUMENT PAYMENT INFORMATION
            ========================================================== --}}

            <div
                id="documentPaymentInfo"
                class="alert alert-info"
            >

                <strong>
                    Payment Item → Document
                </strong>

                <p class="mb-0 mt-1">

                    If a document is selected, the successful payment
                    will automatically trigger document generation
                    using that document's configured fields.

                </p>

            </div>


            {{-- =========================================================
            | ACTIONS
            ========================================================== --}}

            <div class="d-flex justify-content-end gap-2">

                <a
                    href="{{ route('admin.payment-items.index') }}"
                    class="btn btn-outline-secondary"
                >

                    Cancel

                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-check-lg"></i>

                    Create Payment Item

                </button>

            </div>


        </form>

    </div>

</div>
```

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const renewableCheckbox =
        document.getElementById('is_renewable');

    const renewalPaymentItem =
        document.getElementById('renewal_payment_item_id');

    const paymentType =
        document.getElementById('payment_type');

    const membershipRenewalInfo =
        document.getElementById('membershipRenewalInfo');

    const documentPaymentInfo =
        document.getElementById('documentPaymentInfo');


    /*
    |--------------------------------------------------------------------------
    | RENEWAL PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

    function updateRenewalField() {

        if (!renewableCheckbox || !renewalPaymentItem) {
            return;
        }

        if (renewableCheckbox.checked) {

            renewalPaymentItem.disabled = false;

        } else {

            renewalPaymentItem.value = '';

            renewalPaymentItem.disabled = true;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT TYPE INFORMATION
    |--------------------------------------------------------------------------
    */

    function updatePaymentTypeInformation() {

        if (!paymentType) {
            return;
        }

        const selectedType =
            paymentType.value;


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP RENEWAL
        |--------------------------------------------------------------------------
        */

        if (
            selectedType === 'membership_renewal'
        ) {

            if (membershipRenewalInfo) {

                membershipRenewalInfo.classList.remove(
                    'd-none'
                );

            }

            if (documentPaymentInfo) {

                documentPaymentInfo.classList.add(
                    'd-none'
                );

            }

        } else {

            if (membershipRenewalInfo) {

                membershipRenewalInfo.classList.add(
                    'd-none'
                );

            }

            if (documentPaymentInfo) {

                documentPaymentInfo.classList.remove(
                    'd-none'
                );

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    if (renewableCheckbox) {

        renewableCheckbox.addEventListener(
            'change',
            updateRenewalField
        );

    }


    if (paymentType) {

        paymentType.addEventListener(
            'change',
            updatePaymentTypeInformation
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateRenewalField();

    updatePaymentTypeInformation();

});

</script>

@endsection
