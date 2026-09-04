@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')


<style>
    /* =========================================================
       NACPDEAN ADDITIONAL PAYMENT PAGE
       STYLING ONLY — NO LOGIC CHANGES
    ========================================================== */

    .nacp-payment-page {
        background: #f6f8fb;
        min-height: calc(100vh - 100px);
        padding-top: 35px;
        padding-bottom: 50px;
    }

    /* =========================================================
       MAIN CARD
    ========================================================== */

    .nacp-payment-card {
        border: 0 !important;
        border-radius: 20px !important;
        overflow: hidden;
        background: #ffffff;
        box-shadow:
            0 12px 40px rgba(15, 23, 42, 0.07),
            0 2px 8px rgba(15, 23, 42, 0.03) !important;
    }

    .nacp-payment-card-body {
        padding: 38px !important;
    }

    /* =========================================================
       PAGE HEADER
    ========================================================== */

    .nacp-page-header {
        margin-bottom: 30px;
    }

    .nacp-page-header .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 13px;
        margin-bottom: 13px;
        border-radius: 50px;
        background: #eef5ff;
        color: #0d6efd;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .nacp-page-header h3 {
        color: #182230;
        font-size: 1.65rem;
        font-weight: 750;
        letter-spacing: -0.4px;
        margin-bottom: 7px;
    }

    .nacp-page-header p {
        color: #778294;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    /* =========================================================
       PAYMENT SELECTION
    ========================================================== */

    .nacp-selection-box {
        padding: 22px;
        border: 1px solid #e7ebf0;
        border-radius: 15px;
        background: #fafbfd;
        margin-bottom: 25px;
    }

    .nacp-selection-label {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #273142;
        font-size: 0.84rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .nacp-selection-icon {
        width: 31px;
        height: 31px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #eaf2ff;
        color: #0d6efd;
    }

    #payment_item_id {
        min-height: 53px;
        border: 1px solid #dce2e9;
        border-radius: 11px;
        background-color: #ffffff;
        color: #273142;
        font-size: 0.92rem;
        font-weight: 500;
        padding: 0.7rem 1rem;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    #payment_item_id:hover {
        border-color: #b9c3cf;
    }

    #payment_item_id:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.09);
    }

    .nacp-selection-help {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 9px;
        color: #8993a3;
        font-size: 0.75rem;
    }

    /* =========================================================
       PAYMENT DETAILS CONTAINER
    ========================================================== */

    #paymentDetails {
        animation: nacpFadeIn 0.25s ease;
    }

    @keyframes nacpFadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .nacp-details-box {
        border: 1px solid #e5eaf0 !important;
        border-radius: 17px !important;
        background: #ffffff !important;
        overflow: hidden;
        padding: 0 !important;
    }

    /* =========================================================
       DETAILS HEADER
    ========================================================== */

    .nacp-details-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 20px 24px;
        border-bottom: 1px solid #e8edf2;
        background: linear-gradient(
            135deg,
            #f5f9ff 0%,
            #ffffff 100%
        );
    }

    .nacp-details-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: #eaf2ff;
        color: #0d6efd;
        font-size: 1.1rem;
    }

    .nacp-details-header h5 {
        margin: 0 0 3px;
        color: #1b2533;
        font-size: 1rem;
        font-weight: 750;
    }

    .nacp-details-header small {
        color: #8a94a3;
        font-size: 0.75rem;
    }

    .nacp-details-content {
        padding: 25px;
    }

    /* =========================================================
       ITEM INFORMATION
    ========================================================== */

    .nacp-info-label {
        display: block;
        margin-bottom: 6px;
        color: #8a94a3;
        font-size: 0.68rem;
        font-weight: 750;
        letter-spacing: 0.65px;
        text-transform: uppercase;
    }

    #itemName {
        color: #172033;
        font-size: 1.08rem;
        font-weight: 750;
        line-height: 1.45;
    }

    #itemDescription {
        color: #687385;
        font-size: 0.88rem;
        line-height: 1.7;
    }

    /* =========================================================
       DOCUMENT INFORMATION
    ========================================================== */

    #documentInfo .nacp-document-box {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px 17px;
        border: 1px solid #d9e8ff;
        border-radius: 13px;
        background: #f6faff;
    }

    .nacp-document-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: #e8f1ff;
        color: #0d6efd;
    }

    .nacp-document-title {
        color: #8792a2;
        font-size: 0.66rem;
        font-weight: 750;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    #documentName {
        color: #263142;
        font-size: 0.87rem;
        font-weight: 650;
        margin-top: 2px;
    }

    /* =========================================================
       SECTION DIVIDERS
    ========================================================== */

    .nacp-section-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 27px 0 18px;
        color: #8b95a5;
        font-size: 0.68rem;
        font-weight: 750;
        letter-spacing: 0.7px;
        text-transform: uppercase;
    }

    .nacp-section-divider::before,
    .nacp-section-divider::after {
        content: "";
        height: 1px;
        flex: 1;
        background: #e8ecf1;
    }

    /* =========================================================
       SELLER DETAILS
    ========================================================== */

    #sellerDetails .card {
        border: 1px solid #e7ebf0 !important;
        border-radius: 15px !important;
        background: #fbfcfd !important;
        box-shadow: none !important;
    }

    #sellerDetails .card-body {
        padding: 18px !important;
    }

    #sellerDetails .row > div {
        margin-bottom: 0;
    }

    #sellerDetails .row > div > div {
        height: 100%;
        padding: 13px 14px;
        border: 1px solid #edf0f3;
        border-radius: 11px;
        background: #ffffff;
    }

    #sellerDetails label {
        color: #8b95a4 !important;
        font-size: 0.67rem;
        font-weight: 750;
        letter-spacing: 0.55px;
        text-transform: uppercase;
    }

    #sellerMemberName,
    #sellerMembershipNo,
    #sellerDealingRightNo,
    #sellerPhone {
        color: #263142;
        font-size: 0.87rem;
        line-height: 1.5;
    }

    /* =========================================================
       DYNAMIC DOCUMENT FIELDS
    ========================================================== */

    #documentFieldsSection > h5 {
        color: #273142;
        font-size: 0.95rem;
        font-weight: 750;
    }

    #documentFields {
        padding: 19px;
        border: 1px solid #e7ebf0;
        border-radius: 15px;
        background: #fbfcfd;
    }

    #documentFields .mb-3 {
        margin-bottom: 17px !important;
    }

    #documentFields .form-label {
        color: #374151;
        font-size: 0.82rem;
        font-weight: 650;
        margin-bottom: 7px;
    }

    #documentFields .form-control,
    #documentFields .form-select {
        min-height: 48px;
        border: 1px solid #dce2e8;
        border-radius: 10px;
        background: #ffffff;
        color: #273142;
        font-size: 0.88rem;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    #documentFields textarea.form-control {
        min-height: 105px;
    }

    #documentFields .form-control:focus,
    #documentFields .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.08);
    }

    #documentFields .form-control::placeholder {
        color: #a5adba;
    }

    #documentFields small {
        color: #8a94a3 !important;
        font-size: 0.71rem;
    }

    /* =========================================================
       NO DOCUMENT FIELDS
    ========================================================== */

    #noDocumentFields .alert {
        border: 1px dashed #d8dee6 !important;
        border-radius: 13px;
        background: #fafbfc;
        color: #7d8795;
        font-size: 0.82rem;
        padding: 14px 16px;
    }

    /* =========================================================
       PAYMENT FOOTER
    ========================================================== */

    .nacp-payment-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-top: 28px;
        padding: 20px 21px;
        border: 1px solid #e5eaf0;
        border-radius: 15px;
        background: linear-gradient(
            135deg,
            #f8faff 0%,
            #f5f8fc 100%
        );
    }

    .nacp-amount-label {
        display: block;
        margin-bottom: 3px;
        color: #8b95a4;
        font-size: 0.67rem;
        font-weight: 750;
        letter-spacing: 0.65px;
        text-transform: uppercase;
    }

    #itemAmount {
        color: #0d6efd;
        font-size: 1.55rem;
        font-weight: 800;
        letter-spacing: -0.4px;
    }

    .nacp-payment-security {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        color: #8993a1;
        font-size: 0.69rem;
    }

    /* =========================================================
       PAY BUTTON
    ========================================================== */

    #payButton {
        min-width: 145px;
        min-height: 51px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(
            135deg,
            #0d6efd,
            #0959c9
        );
        font-size: 0.88rem;
        font-weight: 700;
        box-shadow: 0 7px 18px rgba(13, 110, 253, 0.20);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            opacity 0.2s ease;
    }

    #payButton:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 11px 24px rgba(13, 110, 253, 0.27);
    }

    #payButton:active:not(:disabled) {
        transform: translateY(0);
    }

    #payButton:disabled {
        opacity: 0.55;
        box-shadow: none;
        cursor: not-allowed;
    }

    /* =========================================================
       MOBILE
    ========================================================== */

    @media (max-width: 767.98px) {

        .nacp-payment-page {
            padding-top: 20px;
            padding-bottom: 30px;
        }

        .nacp-payment-card-body {
            padding: 20px !important;
        }

        .nacp-page-header h3 {
            font-size: 1.4rem;
        }

        .nacp-selection-box {
            padding: 16px;
        }

        .nacp-details-content {
            padding: 18px;
        }

        .nacp-details-header {
            padding: 17px 18px;
        }

        .nacp-payment-footer {
            flex-direction: column;
            align-items: stretch;
        }

        #payButton {
            width: 100%;
        }

        #itemAmount {
            font-size: 1.4rem;
        }
    }
</style>



<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-md-5">

                    {{-- =========================================================
                    | PAGE HEADER
                    ========================================================== --}}

                    <div class="mb-4">

                        <h3 class="fw-bold mb-2">
                            Make Additional Payment
                        </h3>

                        <p class="text-muted mb-0">
                            Select the document or service you want to pay for.
                        </p>

                    </div>


                    {{-- =========================================================
                    | PAYMENT ITEM
                    ========================================================== --}}

                    <div class="mb-4">

                        <label
                            for="payment_item_id"
                            class="form-label fw-semibold"
                        >
                            Select Document / Payment
                        </label>

                        <select
                            id="payment_item_id"
                            class="form-select form-select-lg"
                        >

                            <option value="">
                                -- Select a document to pay for --
                            </option>

                            @foreach ($paymentItems as $item)

                                <option
                                    value="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-description="{{ $item->description ?? '' }}"
                                    data-amount="{{ $item->amount }}"
                                >
                                    {{ $item->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =========================================================
                    | PAYMENT DETAILS
                    ========================================================== --}}

                    <div
                        id="paymentDetails"
                        class="d-none"
                    >

                        <div class="border rounded-3 p-4 bg-light">


                            {{-- =================================================
                            | PAYMENT ITEM INFORMATION
                            ================================================== --}}

                            <div class="mb-3">

                                <small class="text-muted d-block mb-1">
                                    Payment Item
                                </small>

                                <h5
                                    id="itemName"
                                    class="fw-bold mb-0"
                                ></h5>

                            </div>


                            <div class="mb-4">

                                <small class="text-muted d-block mb-1">
                                    Description
                                </small>

                                <p
                                    id="itemDescription"
                                    class="mb-0"
                                ></p>

                            </div>


                            {{-- =================================================
                            | DOCUMENT INFORMATION
                            ================================================== --}}

                            <div
                                id="documentInfo"
                                class="d-none mb-4"
                            >

                                <div class="alert alert-info mb-0">

                                    <div class="fw-semibold">
                                        Document
                                    </div>

                                    <div id="documentName"></div>

                                </div>

                            </div>


                            {{-- =================================================
                            | SELLER / LOGGED-IN MEMBER DETAILS
                            |--------------------------------------------------------------------------
                            | These values are loaded automatically from the
                            | authenticated member's profile and membership.
                            |
                            | They are READ-ONLY.
                            |
                            | They are NOT submitted as manual document fields.
                            ================================================== --}}

                            <div
                                id="sellerDetails"
                                class="d-none mb-4"
                            >

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Seller Details
                                </h5>

                                <div class="card border-0 bg-white">

                                    <div class="card-body">

                                        <div class="row g-3">

                                            {{-- MEMBER NAME --}}

                                            <div class="col-md-6">

                                                <label class="form-label text-muted mb-1">
                                                    Member Name
                                                </label>

                                                <div
                                                    id="sellerMemberName"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>

                                            </div>


                                            {{-- MEMBERSHIP NUMBER --}}

                                            <div class="col-md-6">

                                                <label class="form-label text-muted mb-1">
                                                    Membership No.
                                                </label>

                                                <div
                                                    id="sellerMembershipNo"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>

                                            </div>


                                            {{-- DEALING RIGHT NUMBER --}}

                                            <div class="col-md-6">

                                                <label class="form-label text-muted mb-1">
                                                    Dealing Right No.
                                                </label>

                                                <div
                                                    id="sellerDealingRightNo"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>

                                            </div>


                                            {{-- PHONE --}}

                                            <div class="col-md-6">

                                                <label class="form-label text-muted mb-1">
                                                    Phone
                                                </label>

                                                <div
                                                    id="sellerPhone"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                            | DYNAMIC MANUAL DOCUMENT FIELDS
                            |--------------------------------------------------------------------------
                            | ONLY fields where is_system = false are returned
                            | by getAdditionalPaymentFields().
                            |
                            | For Document #35 this should be:
                            |
                            | loading_point
                            | buyer_member_name
                            | buyer_membership_no
                            | buyer_dealing_right_no
                            | state
                            | vehicle_number
                            ================================================== --}}

                            <div
                                id="documentFieldsSection"
                                class="d-none mb-4"
                            >

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Buyer Details
                                </h5>

                                <div id="documentFields"></div>

                            </div>


                            {{-- =================================================
                            | NO MANUAL FIELDS
                            ================================================== --}}

                            <div
                                id="noDocumentFields"
                                class="d-none mb-4"
                            >

                                <div class="alert alert-light border mb-0">

                                    No additional information is required for
                                    this document.

                                </div>

                            </div>


                            {{-- =================================================
                            | AMOUNT + PAY BUTTON
                            ================================================== --}}

                            <div
                                class="d-flex justify-content-between align-items-center"
                            >

                                <div>

                                    <small class="text-muted d-block">
                                        Amount to Pay
                                    </small>

                                    <h3
                                        id="itemAmount"
                                        class="fw-bold mb-0"
                                    ></h3>

                                </div>


                                <button
                                    type="button"
                                    id="payButton"
                                    class="btn btn-primary btn-lg px-4"
                                    disabled
                                >

                                    <span id="payButtonText">
                                        Pay Now
                                    </span>

                                    <span
                                        id="paySpinner"
                                        class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const paymentSelect =
            document.getElementById('payment_item_id');

        const paymentDetails =
            document.getElementById('paymentDetails');

        const itemName =
            document.getElementById('itemName');

        const itemDescription =
            document.getElementById('itemDescription');

        const itemAmount =
            document.getElementById('itemAmount');

        const documentInfo =
            document.getElementById('documentInfo');

        const documentName =
            document.getElementById('documentName');

        const sellerDetails =
            document.getElementById('sellerDetails');

        const sellerMemberName =
            document.getElementById('sellerMemberName');

        const sellerMembershipNo =
            document.getElementById('sellerMembershipNo');

        const sellerDealingRightNo =
            document.getElementById('sellerDealingRightNo');

        const sellerPhone =
            document.getElementById('sellerPhone');

        const documentFieldsSection =
            document.getElementById('documentFieldsSection');

        const documentFields =
            document.getElementById('documentFields');

        const noDocumentFields =
            document.getElementById('noDocumentFields');

        const payButton =
            document.getElementById('payButton');

        const payButtonText =
            document.getElementById('payButtonText');

        const paySpinner =
            document.getElementById('paySpinner');


        /*
        |--------------------------------------------------------------------------
        | STATE
        |--------------------------------------------------------------------------
        */

        let documentFieldsLoaded = false;

        let fieldsRequestController = null;


        /*
        |--------------------------------------------------------------------------
        | RESET DOCUMENT INFORMATION
        |--------------------------------------------------------------------------
        */

        function resetDocumentFields() {

            documentFieldsLoaded = false;

            documentInfo.classList.add('d-none');

            sellerDetails.classList.add('d-none');

            documentFieldsSection.classList.add('d-none');

            noDocumentFields.classList.add('d-none');

            documentName.textContent = '';

            documentFields.innerHTML = '';

            sellerMemberName.textContent = '-';

            sellerMembershipNo.textContent = '-';

            sellerDealingRightNo.textContent = '-';

            sellerPhone.textContent = '-';

            payButton.disabled = true;

        }


        /*
        |--------------------------------------------------------------------------
        | RESET PAYMENT BUTTON
        |--------------------------------------------------------------------------
        */

        function resetPaymentButton() {

            payButton.disabled = true;

            payButtonText.textContent =
                'Pay Now';

            paySpinner.classList.add('d-none');

        }


        /*
        |--------------------------------------------------------------------------
        | DISPLAY SELLER DETAILS
        |--------------------------------------------------------------------------
        |
        | These values come from the backend.
        |
        | The backend should return them automatically when loading the
        | payment item's document information.
        |
        */

        function displaySellerDetails(data) {

            const seller =
                data.seller || data.member || null;


            /*
            |--------------------------------------------------------------------------
            | NO SELLER DATA
            |--------------------------------------------------------------------------
            */

            if (!seller) {

                sellerDetails.classList.add('d-none');

                return;

            }


            sellerMemberName.textContent =
                seller.name ||
                seller.member_name ||
                'N/A';


            sellerMembershipNo.textContent =
                seller.membership_number ||
                seller.membership_no ||
                'N/A';


            sellerDealingRightNo.textContent =
                seller.dealing_right_number ||
                seller.dealing_right_no ||
                'N/A';


            sellerPhone.textContent =
                seller.phone ||
                'N/A';


            sellerDetails.classList.remove(
                'd-none'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CREATE DOCUMENT FIELD
        |--------------------------------------------------------------------------
        */

        function renderField(field) {

            const wrapper =
                document.createElement('div');

            wrapper.className =
                'mb-3';


            /*
            |--------------------------------------------------------------------------
            | LABEL
            |--------------------------------------------------------------------------
            */

            const label =
                document.createElement('label');

            label.className =
                'form-label fw-semibold';

            label.setAttribute(
                'for',
                'document_field_' + field.field_key
            );

            label.textContent =
                field.label ||
                field.field_key;


            /*
            |--------------------------------------------------------------------------
            | REQUIRED INDICATOR
            |--------------------------------------------------------------------------
            */

            if (field.is_required) {

                const required =
                    document.createElement('span');

                required.className =
                    'text-danger ms-1';

                required.textContent =
                    '*';

                label.appendChild(required);

            }


            wrapper.appendChild(label);


            /*
            |--------------------------------------------------------------------------
            | FIELD INPUT
            |--------------------------------------------------------------------------
            */

            let input;


            switch (field.field_type) {

                /*
                |--------------------------------------------------------------------------
                | TEXTAREA
                |--------------------------------------------------------------------------
                */

                case 'textarea':

                    input =
                        document.createElement('textarea');

                    input.rows = 4;

                    input.className =
                        'form-control';

                    break;


                /*
                |--------------------------------------------------------------------------
                | SELECT
                |--------------------------------------------------------------------------
                */

                case 'select':

                    input =
                        document.createElement('select');

                    input.className =
                        'form-select';


                    const emptyOption =
                        document.createElement('option');

                    emptyOption.value =
                        '';

                    emptyOption.textContent =
                        '-- Select --';

                    input.appendChild(
                        emptyOption
                    );


                    if (
                        Array.isArray(field.options)
                    ) {

                        field.options.forEach(
                            function (option) {

                                const optionElement =
                                    document.createElement(
                                        'option'
                                    );


                                /*
                                |--------------------------------------------------------------------------
                                | OBJECT OPTION
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    option !== null &&
                                    typeof option === 'object'
                                ) {

                                    optionElement.value =
                                        option.value ??
                                        option.key ??
                                        '';

                                    optionElement.textContent =
                                        option.label ??
                                        option.name ??
                                        option.value ??
                                        option.key ??
                                        '';

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | SIMPLE OPTION
                                |--------------------------------------------------------------------------
                                */

                                else {

                                    optionElement.value =
                                        option ?? '';

                                    optionElement.textContent =
                                        option ?? '';

                                }


                                input.appendChild(
                                    optionElement
                                );

                            }
                        );

                    }

                    break;


                /*
                |--------------------------------------------------------------------------
                | NUMBER
                |--------------------------------------------------------------------------
                */

                case 'number':

                    input =
                        document.createElement('input');

                    input.type =
                        'number';

                    input.className =
                        'form-control';

                    break;


                /*
                |--------------------------------------------------------------------------
                | DATE
                |--------------------------------------------------------------------------
                */

                case 'date':

                    input =
                        document.createElement('input');

                    input.type =
                        'date';

                    input.className =
                        'form-control';

                    break;


                /*
                |--------------------------------------------------------------------------
                | TIME
                |--------------------------------------------------------------------------
                */

                case 'time':

                    input =
                        document.createElement('input');

                    input.type =
                        'time';

                    input.className =
                        'form-control';

                    break;


                /*
                |--------------------------------------------------------------------------
                | TEXT
                |--------------------------------------------------------------------------
                */

                case 'text':

                default:

                    input =
                        document.createElement('input');

                    input.type =
                        'text';

                    input.className =
                        'form-control';

                    break;

            }


            /*
            |--------------------------------------------------------------------------
            | FIELD ID
            |--------------------------------------------------------------------------
            */

            input.id =
                'document_field_' +
                field.field_key;


            /*
            |--------------------------------------------------------------------------
            | FIELD NAME
            |--------------------------------------------------------------------------
            |
            | Laravel expects:
            |
            | document_field_values[field_key]
            |
            */

            input.name =
                'document_field_values[' +
                field.field_key +
                ']';


            /*
            |--------------------------------------------------------------------------
            | PLACEHOLDER
            |--------------------------------------------------------------------------
            */

            if (
                field.placeholder !== null &&
                field.placeholder !== undefined &&
                field.placeholder !== ''
            ) {

                input.placeholder =
                    field.placeholder;

            }


            /*
            |--------------------------------------------------------------------------
            | DEFAULT VALUE
            |--------------------------------------------------------------------------
            */

            if (
                field.default_value !== null &&
                field.default_value !== undefined
            ) {

                input.value =
                    field.default_value;

            }


            /*
            |--------------------------------------------------------------------------
            | REQUIRED
            |--------------------------------------------------------------------------
            */

            if (field.is_required) {

                input.required =
                    true;

            }


            /*
            |--------------------------------------------------------------------------
            | APPEND INPUT
            |--------------------------------------------------------------------------
            */

            wrapper.appendChild(
                input
            );


            /*
            |--------------------------------------------------------------------------
            | SECTION NOTE
            |--------------------------------------------------------------------------
            */

            if (field.section) {

                const section =
                    document.createElement('small');

                section.className =
                    'text-muted d-block mt-1';

                section.textContent =
                    field.section;

                wrapper.appendChild(
                    section
                );

            }


            return wrapper;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DOCUMENT FIELDS
        |--------------------------------------------------------------------------
        */

        async function loadDocumentFields(
            paymentItemId
        ) {

            resetDocumentFields();


            /*
            |--------------------------------------------------------------------------
            | CANCEL PREVIOUS REQUEST
            |--------------------------------------------------------------------------
            */

            if (
                fieldsRequestController
            ) {

                fieldsRequestController.abort();

            }


            fieldsRequestController =
                new AbortController();


            try {

                const response =
                    await fetch(
                        "{{ route('payment.additional.fields') }}" +
                        '?payment_item_id=' +
                        encodeURIComponent(
                            paymentItemId
                        ),
                        {
                            method: 'GET',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'

                            },

                            signal:
                                fieldsRequestController.signal

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | READ RESPONSE
                |--------------------------------------------------------------------------
                */

                const data =
                    await response.json();

                    console.log('ADDITIONAL PAYMENT API RESPONSE:', data);
console.log('RETURNED FIELDS:', data.fields);


                /*
                |--------------------------------------------------------------------------
                | VALIDATE RESPONSE
                |--------------------------------------------------------------------------
                */

                if (
                    !response.ok ||
                    data.status !== 'success'
                ) {

                    throw new Error(
                        data.message ||
                        'Unable to load document information.'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | DISPLAY SELLER INFORMATION
                |--------------------------------------------------------------------------
                |
                | If the backend supplies seller/member information,
                | display it automatically.
                |
                */

                displaySellerDetails(
                    data
                );


                /*
                |--------------------------------------------------------------------------
                | NO DOCUMENT
                |--------------------------------------------------------------------------
                */

                if (
                    !data.has_document ||
                    !data.document
                ) {

                    noDocumentFields.classList.remove(
                        'd-none'
                    );

                    documentFieldsLoaded =
                        true;

                    payButton.disabled =
                        false;

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | DOCUMENT INFORMATION
                |--------------------------------------------------------------------------
                */

                documentName.textContent =
                    data.document.name ||
                    '';

                documentInfo.classList.remove(
                    'd-none'
                );


                /*
                |--------------------------------------------------------------------------
                | MANUAL FIELDS
                |--------------------------------------------------------------------------
                */

                const fields =
                    Array.isArray(data.fields)
                        ? data.fields
                        : [];


                /*
                |--------------------------------------------------------------------------
                | NO MANUAL FIELDS
                |--------------------------------------------------------------------------
                */

                if (!fields.length) {

                    noDocumentFields.classList.remove(
                        'd-none'
                    );

                    documentFieldsLoaded =
                        true;

                    payButton.disabled =
                        false;

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | RENDER MANUAL FIELDS
                |--------------------------------------------------------------------------
                */

                fields.forEach(
                    function (field) {

                        const fieldElement =
                            renderField(
                                field
                            );

                        documentFields.appendChild(
                            fieldElement
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SHOW MANUAL FIELDS
                |--------------------------------------------------------------------------
                */

                documentFieldsSection.classList.remove(
                    'd-none'
                );


                /*
                |--------------------------------------------------------------------------
                | DOCUMENT FIELDS LOADED
                |--------------------------------------------------------------------------
                */

                documentFieldsLoaded =
                    true;

                payButton.disabled =
                    false;

            } catch (error) {

                /*
                |--------------------------------------------------------------------------
                | IGNORE CANCELLED REQUEST
                |--------------------------------------------------------------------------
                */

                if (
                    error.name ===
                    'AbortError'
                ) {

                    return;

                }


                documentFieldsLoaded =
                    false;

                payButton.disabled =
                    true;


                console.error(
                    'Document fields error:',
                    error
                );


                documentFields.innerHTML =
                    '';


                const errorBox =
                    document.createElement(
                        'div'
                    );

                errorBox.className =
                    'alert alert-danger';

                errorBox.textContent =
                    error.message ||
                    'Unable to load document information.';


                documentFields.appendChild(
                    errorBox
                );


                documentFieldsSection.classList.remove(
                    'd-none'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | COLLECT MANUAL DOCUMENT FIELD VALUES
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | This collects ONLY fields rendered in #documentFields.
        |
        | Seller/member system fields are NOT collected here.
        |
        */

        function collectDocumentFieldValues() {

            const values = {};


            const fields =
                documentFields.querySelectorAll(
                    'input[name], textarea[name], select[name]'
                );


            fields.forEach(
                function (field) {

                    if (!field.name) {

                        return;

                    }


                    const match =
                        field.name.match(
                            /^document_field_values\[(.+)\]$/
                        );


                    if (!match) {

                        return;

                    }


                    const fieldKey =
                        match[1];


                    let value =
                        field.value;


                    if (
                        typeof value ===
                        'string'
                    ) {

                        value =
                            value.trim();

                    }


                    values[fieldKey] =
                        value;

                }
            );


            return values;

        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE REQUIRED MANUAL FIELDS
        |--------------------------------------------------------------------------
        */

        function validateDocumentFields() {

            const requiredFields =
                documentFields.querySelectorAll(
                    'input[name][required], textarea[name][required], select[name][required]'
                );


            for (
                const field
                of requiredFields
            ) {

                let value =
                    field.value;


                if (
                    typeof value ===
                    'string'
                ) {

                    value =
                        value.trim();

                }


                if (!value) {

                    field.focus();

                    return false;

                }

            }


            return true;

        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT ITEM CHANGE
        |--------------------------------------------------------------------------
        */

        paymentSelect.addEventListener(
            'change',
            async function () {

                const paymentItemId =
                    this.value;


                const option =
                    this.options[
                        this.selectedIndex
                    ];


                /*
                |--------------------------------------------------------------------------
                | RESET
                |--------------------------------------------------------------------------
                */

                resetDocumentFields();

                resetPaymentButton();


                /*
                |--------------------------------------------------------------------------
                | NOTHING SELECTED
                |--------------------------------------------------------------------------
                */

                if (!paymentItemId) {

                    paymentDetails.classList.add(
                        'd-none'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT ITEM INFORMATION
                |--------------------------------------------------------------------------
                */

                const name =
                    option.dataset.name ||
                    '';

                const description =
                    option.dataset.description ||
                    '';

                const amount =
                    parseFloat(
                        option.dataset.amount ||
                        0
                    );


                itemName.textContent =
                    name;


                itemDescription.textContent =
                    description ||
                    'No description available.';


                /*
                |--------------------------------------------------------------------------
                | DISPLAY AMOUNT
                |--------------------------------------------------------------------------
                |
                | DISPLAY ONLY.
                |
                | The backend determines the authoritative amount from
                | payment_items.amount.
                |
                */

                itemAmount.textContent =
                    '₦' +
                    amount.toLocaleString(
                        'en-NG',
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );


                paymentDetails.classList.remove(
                    'd-none'
                );


                /*
                |--------------------------------------------------------------------------
                | LOAD DOCUMENT + SELLER INFORMATION
                |--------------------------------------------------------------------------
                */

                await loadDocumentFields(
                    paymentItemId
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PAY NOW
        |--------------------------------------------------------------------------
        */

        payButton.addEventListener(
            'click',
            async function () {

                const paymentItemId =
                    paymentSelect.value;


                /*
                |--------------------------------------------------------------------------
                | PAYMENT ITEM REQUIRED
                |--------------------------------------------------------------------------
                */

                if (!paymentItemId) {

                    alert(
                        'Please select a payment item.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | DOCUMENT INFORMATION MUST BE LOADED
                |--------------------------------------------------------------------------
                */

                if (!documentFieldsLoaded) {

                    alert(
                        'The document information has not finished loading. Please try again.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | VALIDATE REQUIRED MANUAL FIELDS
                |--------------------------------------------------------------------------
                */

                if (
                    !validateDocumentFields()
                ) {

                    alert(
                        'Please complete all required document information.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | COLLECT MANUAL VALUES
                |--------------------------------------------------------------------------
                */

                const documentFieldValues =
                    collectDocumentFieldValues();


                console.log(
                    'DOCUMENT FIELD VALUES:',
                    documentFieldValues
                );


                /*
                |--------------------------------------------------------------------------
                | PREVENT DOUBLE CLICK
                |--------------------------------------------------------------------------
                */

                payButton.disabled =
                    true;

                payButtonText.textContent =
                    'Processing...';

                paySpinner.classList.remove(
                    'd-none'
                );


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE FORMDATA
                    |--------------------------------------------------------------------------
                    */

                    const formData =
                        new FormData();


                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT ITEM
                    |--------------------------------------------------------------------------
                    */

                    formData.append(
                        'payment_item_id',
                        paymentItemId
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CSRF TOKEN
                    |--------------------------------------------------------------------------
                    */

                    formData.append(
                        '_token',
                        '{{ csrf_token() }}'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | MANUAL DOCUMENT FIELDS ONLY
                    |--------------------------------------------------------------------------
                    |
                    | Seller/member system fields are deliberately NOT
                    | submitted from the browser.
                    |
                    | They are resolved securely by DocumentGenerationService
                    | from the authenticated user, profile and membership.
                    |
                    */

                    Object.keys(
                        documentFieldValues
                    ).forEach(
                        function (fieldKey) {

                            formData.append(
                                'document_field_values[' +
                                fieldKey +
                                ']',
                                documentFieldValues[fieldKey]
                            );

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | DEBUG FORMDATA
                    |--------------------------------------------------------------------------
                    |
                    | Remove this console logging after testing.
                    |--------------------------------------------------------------------------
                    */

                    console.log(
                        'FORM DATA BEING SENT:'
                    );


                    for (
                        const [
                            key,
                            value
                        ]
                        of formData.entries()
                    ) {

                        console.log(
                            key + ' =',
                            value
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | INITIALIZE PAYMENT
                    |--------------------------------------------------------------------------
                    */

                    const response =
                        await fetch(
                            "{{ route('payment.additional.initialize') }}",
                            {
                                method: 'POST',

                                /*
                                |--------------------------------------------------------------------------
                                | DO NOT SET CONTENT-TYPE MANUALLY
                                |--------------------------------------------------------------------------
                                |
                                | FormData automatically sets the correct
                                | multipart/form-data boundary.
                                |--------------------------------------------------------------------------
                                */

                                headers: {

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest'

                                },

                                body:
                                    formData

                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | READ RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    const data =
                        await response.json();

                        console.log('ADDITIONAL PAYMENT API RESPONSE:', data);
console.log('RETURNED FIELDS:', data.fields);


                    console.log(
                        'PAYMENT INITIALIZATION RESPONSE:',
                        data
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | INITIALIZATION ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !response.ok ||
                        !data.success
                    ) {

                        throw new Error(
                            data.message ||
                            'Unable to initialize payment.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PAYSTACK AUTHORIZATION URL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !data.authorization_url
                    ) {

                        throw new Error(
                            'No Paystack authorization URL was returned.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | REDIRECT TO PAYSTACK
                    |--------------------------------------------------------------------------
                    */

                    window.location.href =
                        data.authorization_url;

                } catch (error) {

                    console.error(
                        'Payment initialization error:',
                        error
                    );


                    alert(
                        error.message ||
                        'Unable to process payment.'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    payButton.disabled =
                        false;

                    payButtonText.textContent =
                        'Pay Now';

                    paySpinner.classList.add(
                        'd-none'
                    );

                }

            }
        );

    });

</script>

@endsection
