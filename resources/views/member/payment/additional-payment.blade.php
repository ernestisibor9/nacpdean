@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')

    <style>
        .nacp-payment-page {
            background: #f6f8fb;
            min-height: calc(100vh - 100px);
            padding-top: 35px;
            padding-bottom: 50px;
        }

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

        .nacp-details-header {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 20px 24px;
            border-bottom: 1px solid #e8edf2;
            background: linear-gradient(135deg,
                    #f5f9ff 0%,
                    #ffffff 100%);
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

        #sellerDetails .card {
            border: 1px solid #e7ebf0 !important;
            border-radius: 15px !important;
            background: #fbfcfd !important;
            box-shadow: none !important;
        }

        #sellerDetails .card-body {
            padding: 18px !important;
        }

        #sellerDetails .row>div {
            margin-bottom: 0;
        }

        #sellerDetails .row>div>div {
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

        #documentFieldsSection>h5 {
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

        #noDocumentFields .alert {
            border: 1px dashed #d8dee6 !important;
            border-radius: 13px;
            background: #fafbfc;
            color: #7d8795;
            font-size: 0.82rem;
            padding: 14px 16px;
        }

        .nacp-payment-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-top: 28px;
            padding: 20px 21px;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            background: linear-gradient(135deg,
                    #f8faff 0%,
                    #f5f8fc 100%);
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

        #payButton {
            min-width: 145px;
            min-height: 51px;
            border: 0;
            border-radius: 11px;
            background: linear-gradient(135deg,
                    #0d6efd,
                    #0959c9);
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

        .nacp-document-group {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e8ecf1;
        }

        .nacp-document-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 0;
        }

        .nacp-document-group-title {
            color: #273142;
            font-size: 0.92rem;
            font-weight: 750;
            margin-bottom: 17px;
        }

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

                        <div class="mb-4">

                            <h3 class="fw-bold mb-2">
                                Make Additional Payment
                            </h3>

                            <p class="text-muted mb-0">
                                Select the document or service you want to pay for.
                            </p>

                        </div>

                        <div class="mb-4">

                            <label for="payment_item_id" class="form-label fw-semibold">
                                Select Document / Payment
                            </label>

                            <select id="payment_item_id" class="form-select form-select-lg">

                                <option value="">
                                    -- Select a document to pay for --
                                </option>

                                @foreach ($paymentItems as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->name }}"
                                        data-description="{{ $item->description ?? '' }}" data-amount="{{ $item->amount }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        <div id="paymentDetails" class="d-none">

                            <div class="border rounded-3 p-4 bg-light">

                                <div class="mb-3">

                                    <small class="text-muted d-block mb-1">
                                        Payment Item
                                    </small>

                                    <h5 id="itemName" class="fw-bold mb-0"></h5>

                                </div>

                                <div class="mb-4">

                                    <small class="text-muted d-block mb-1">
                                        Description
                                    </small>

                                    <p id="itemDescription" class="mb-0"></p>

                                </div>

                                <div id="documentInfo" class="d-none mb-4">

                                    <div class="alert alert-info mb-0">

                                        <div class="fw-semibold">
                                            Document(s)
                                        </div>

                                        <div id="documentName"></div>

                                    </div>

                                </div>

                                <div id="sellerDetails" class="d-none mb-4">

                                    <hr class="my-4">

                                    <h5 class="fw-bold mb-3">
                                        Seller Details
                                    </h5>

                                    <div class="card border-0 bg-white">

                                        <div class="card-body">

                                            <div class="row g-3">

                                                <div class="col-md-6">

                                                    <label class="form-label text-muted mb-1">
                                                        Member Name
                                                    </label>

                                                    <div id="sellerMemberName" class="fw-semibold">
                                                        -
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <label class="form-label text-muted mb-1">
                                                        Membership No.
                                                    </label>

                                                    <div id="sellerMembershipNo" class="fw-semibold">
                                                        -
                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <label for="sellerDealingRightNo" class="form-label text-muted mb-1">
                                                        Dealing Right No. <span class="text-danger">*</span>
                                                    </label>

                                                    <input type="text" id="sellerDealingRightNo" name="dealing_right_no"
                                                        class="form-control" placeholder="Enter Dealing Right No." required>

                                                </div>

                                                <div class="col-md-6">

                                                    <label class="form-label text-muted mb-1">
                                                        Phone
                                                    </label>

                                                    <div id="sellerPhone" class="fw-semibold">
                                                        -
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div id="documentFieldsSection" class="d-none mb-4">

                                    <hr class="my-4">

                                    <h5 class="fw-bold mb-3">
                                        Document Information
                                    </h5>

                                    <div id="documentFields"></div>

                                </div>

                                <div id="noDocumentFields" class="d-none mb-4">

                                    <div class="alert alert-light border mb-0">
                                        No additional information is required for
                                        this document.
                                    </div>

                                </div>

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <small class="text-muted d-block">
                                            Amount to Pay
                                        </small>

                                        <h3 id="itemAmount" class="fw-bold mb-0"></h3>

                                    </div>

                                    <button type="button" id="payButton" class="btn btn-primary btn-lg px-4" disabled>

                                        <span id="payButtonText">
                                            Pay Now
                                        </span>

                                        <span id="paySpinner" class="spinner-border spinner-border-sm ms-2 d-none"
                                            role="status" aria-hidden="true"></span>

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
        document.addEventListener('DOMContentLoaded', function() {

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

            let documentFieldsLoaded = false;

            let fieldsRequestController = null;


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


            function resetPaymentButton() {

                payButton.disabled = true;

                payButtonText.textContent = 'Pay Now';

                paySpinner.classList.add('d-none');

            }


            function displaySellerDetails(data) {

                const seller =
                    data.seller ||
                    data.member ||
                    null;


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


                sellerDetails.classList.remove('d-none');

            }


            function renderField(field, documentCode) {

                const wrapper =
                    document.createElement('div');

                wrapper.className =
                    'mb-3';


                const label =
                    document.createElement('label');

                label.className =
                    'form-label fw-semibold';


                const fieldId =
                    'document_field_' +
                    documentCode +
                    '_' +
                    field.field_key;


                label.setAttribute(
                    'for',
                    fieldId
                );


                label.textContent =
                    field.label ||
                    field.field_key;


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


                let input;


                switch (field.field_type) {

                    case 'textarea':

                        input =
                            document.createElement('textarea');

                        input.rows = 4;

                        input.className =
                            'form-control';

                        break;


                    case 'select':

                        input =
                            document.createElement('select');

                        input.className =
                            'form-select';


                        const emptyOption =
                            document.createElement('option');

                        emptyOption.value = '';

                        emptyOption.textContent =
                            '-- Select --';

                        input.appendChild(
                            emptyOption
                        );


                        let options =
                            field.options || [];


                        if (typeof options === 'string') {

                            try {

                                options =
                                    JSON.parse(options);

                            } catch (error) {

                                options = [];

                            }

                        }


                        if (Array.isArray(options)) {

                            options.forEach(function(option) {

                                const optionElement =
                                    document.createElement('option');


                                if (
                                    option !== null &&
                                    typeof option === 'object'
                                ) {

                                    optionElement.value =
                                        option.value ??
                                        option.key ??
                                        option.code ??
                                        '';

                                    optionElement.textContent =
                                        option.label ??
                                        option.name ??
                                        option.value ??
                                        option.key ??
                                        option.code ??
                                        '';

                                } else {

                                    optionElement.value =
                                        option ?? '';

                                    optionElement.textContent =
                                        option ?? '';

                                }


                                input.appendChild(
                                    optionElement
                                );

                            });

                        }

                        break;


                    case 'number':

                        input =
                            document.createElement('input');

                        input.type =
                            'number';

                        input.className =
                            'form-control';

                        break;


                    case 'date':

                        input =
                            document.createElement('input');

                        input.type =
                            'date';

                        input.className =
                            'form-control';

                        break;


                    case 'time':

                        input =
                            document.createElement('input');

                        input.type =
                            'time';

                        input.className =
                            'form-control';

                        break;


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


                input.id =
                    fieldId;


                input.name =
                    'document_field_values[' +
                    documentCode +
                    '][' +
                    field.field_key +
                    ']';


                input.dataset.documentCode =
                    documentCode;


                input.dataset.fieldKey =
                    field.field_key;


                if (
                    field.placeholder !== null &&
                    field.placeholder !== undefined &&
                    field.placeholder !== ''
                ) {

                    input.placeholder =
                        field.placeholder;

                }


                if (
                    field.default_value !== null &&
                    field.default_value !== undefined &&
                    field.default_value !== ''
                ) {

                    input.value =
                        field.default_value;

                }


                if (field.is_required) {

                    input.required = true;

                }


                wrapper.appendChild(input);


                if (field.section) {

                    const section =
                        document.createElement('small');

                    section.className =
                        'text-muted d-block mt-1';

                    section.textContent =
                        field.section;

                    wrapper.appendChild(section);

                }


                return wrapper;

            }


            function renderDocumentGroup(doc) {

                const group =
                    document.createElement('div');

                group.className =
                    'nacp-document-group';


                const title =
                    document.createElement('div');

                title.className =
                    'nacp-document-group-title';

                title.textContent =
                    doc.name ||
                    doc.code ||
                    'Document';


                group.appendChild(title);


                const fields =
                    Array.isArray(doc.fields) ?
                    doc.fields :
                    [];


                fields.forEach(function(field) {

                    const fieldElement =
                        renderField(
                            field,
                            doc.code
                        );


                    group.appendChild(
                        fieldElement
                    );

                });


                return group;

            }


            async function loadDocumentFields(paymentItemId) {

                resetDocumentFields();


                if (fieldsRequestController) {

                    fieldsRequestController.abort();

                }


                fieldsRequestController =
                    new AbortController();


                try {

                    const response =
                        await fetch(
                            "{{ route('payment.additional.fields') }}" +
                            '?payment_item_id=' +
                            encodeURIComponent(paymentItemId), {
                                method: 'GET',

                                headers: {
                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest'
                                },

                                signal: fieldsRequestController.signal
                            }
                        );


                    const responseText =
                        await response.text();


                    let data;


                    try {

                        data =
                            JSON.parse(
                                responseText
                            );

                    } catch (parseError) {

                        console.error(
                            'DOCUMENT FIELDS RAW RESPONSE:',
                            responseText
                        );

                        throw new Error(
                            'The server returned an invalid response while loading the document information.'
                        );

                    }


                    console.log(
                        'ADDITIONAL PAYMENT API RESPONSE:',
                        data
                    );


                    if (
                        !response.ok ||
                        (
                            data.status !== 'success' &&
                            data.success !== true
                        )
                    ) {

                        throw new Error(
                            data.message ||
                            'Unable to load document information.'
                        );

                    }


                    displaySellerDetails(data);


                    let documents =
                        Array.isArray(data.documents) ?
                        data.documents :
                        [];


                    if (
                        !documents.length &&
                        data.document
                    ) {

                        documents = [{
                            ...data.document,
                            fields: Array.isArray(data.fields) ?
                                data.fields :
                                []
                        }];

                    }


                    if (!documents.length) {

                        noDocumentFields.classList.remove(
                            'd-none'
                        );

                        documentFieldsLoaded = true;

                        payButton.disabled = false;

                        return;

                    }


                    documentFields.innerHTML = '';


                    let hasManualFields =
                        false;


                    documents.forEach(function(doc) {

                        const fields =
                            Array.isArray(doc.fields) ?
                            doc.fields :
                            [];


                        if (fields.length > 0) {

                            hasManualFields = true;

                            documentFields.appendChild(
                                renderDocumentGroup(doc)
                            );

                        }

                    });


                    if (hasManualFields) {

                        documentFieldsSection.classList.remove(
                            'd-none'
                        );

                        noDocumentFields.classList.add(
                            'd-none'
                        );

                    } else {

                        documentFieldsSection.classList.add(
                            'd-none'
                        );

                        noDocumentFields.classList.remove(
                            'd-none'
                        );

                    }


                    documentName.textContent =
                        documents
                        .map(function(doc) {

                            return (
                                doc.name ||
                                doc.code ||
                                'Document'
                            );

                        })
                        .join(' + ');


                    documentInfo.classList.remove(
                        'd-none'
                    );


                    documentFieldsLoaded = true;

                    payButton.disabled = false;


                } catch (error) {

                    if (
                        error.name === 'AbortError'
                    ) {

                        return;

                    }


                    documentFieldsLoaded = false;

                    payButton.disabled = true;


                    console.error(
                        'Document fields error:',
                        error
                    );


                    documentFields.innerHTML = '';


                    const errorBox =
                        document.createElement('div');

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


            function collectDocumentFieldValues() {

                const values = {};


                const fields =
                    documentFields.querySelectorAll(
                        'input[name], textarea[name], select[name]'
                    );


                fields.forEach(function(field) {

                    const documentCode =
                        field.dataset.documentCode;

                    const fieldKey =
                        field.dataset.fieldKey;


                    if (
                        !documentCode ||
                        !fieldKey
                    ) {

                        return;

                    }


                    let value =
                        field.value;


                    if (
                        typeof value === 'string'
                    ) {

                        value =
                            value.trim();

                    }


                    if (
                        !values[documentCode]
                    ) {

                        values[documentCode] = {};

                    }


                    values[documentCode][fieldKey] =
                        value;

                });


                return values;

            }


            function validateDocumentFields() {

                const requiredFields =
                    documentFields.querySelectorAll(
                        'input[required], textarea[required], select[required]'
                    );


                for (
                    const field of requiredFields
                ) {

                    let value =
                        field.value;


                    if (
                        typeof value === 'string'
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


            paymentSelect.addEventListener(
                'change',
                async function() {

                    const paymentItemId =
                        this.value;


                    const option =
                        this.options[
                            this.selectedIndex
                        ];


                    resetDocumentFields();

                    resetPaymentButton();


                    if (!paymentItemId) {

                        paymentDetails.classList.add(
                            'd-none'
                        );

                        return;

                    }


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


                    itemAmount.textContent =
                        '₦' +
                        amount.toLocaleString(
                            'en-NG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        );


                    paymentDetails.classList.remove(
                        'd-none'
                    );


                    await loadDocumentFields(
                        paymentItemId
                    );

                }
            );


            payButton.addEventListener(
                'click',
                async function() {

                    const paymentItemId =
                        paymentSelect.value;


                    if (!paymentItemId) {

                        alert(
                            'Please select a payment item.'
                        );

                        return;

                    }


                    if (!documentFieldsLoaded) {

                        alert(
                            'The document information has not finished loading. Please try again.'
                        );

                        return;

                    }


                    if (!validateDocumentFields()) {

                        alert(
                            'Please complete all required document information.'
                        );

                        return;

                    }


                    const documentFieldValues =
                        collectDocumentFieldValues();


                    console.log(
                        'DOCUMENT FIELD VALUES:',
                        documentFieldValues
                    );


                    payButton.disabled = true;

                    payButtonText.textContent =
                        'Processing...';

                    paySpinner.classList.remove(
                        'd-none'
                    );


                    try {

                        const formData =
                            new FormData();


                        formData.append(
                            'payment_item_id',
                            paymentItemId
                        );


                        formData.append(
                            '_token',
                            '{{ csrf_token() }}'
                        );


                        Object.keys(
                            documentFieldValues
                        ).forEach(function(documentCode) {

                            const fields =
                                documentFieldValues[
                                    documentCode
                                ];


                            Object.keys(
                                fields
                            ).forEach(function(fieldKey) {

                                formData.append(
                                    'document_field_values[' +
                                    documentCode +
                                    '][' +
                                    fieldKey +
                                    ']',
                                    fields[fieldKey]
                                );

                            });

                        });


                        console.log(
                            'FORM DATA BEING SENT:'
                        );


                        for (
                            const [
                                key,
                                value
                            ] of formData.entries()
                        ) {

                            console.log(
                                key + ' =',
                                value
                            );

                        }


                        const response =
                            await fetch(
                                "{{ route('payment.additional.initialize') }}", {
                                    method: 'POST',

                                    headers: {

                                        'Accept': 'application/json',

                                        'X-Requested-With': 'XMLHttpRequest'

                                    },

                                    body: formData

                                }
                            );


                        const responseText =
                            await response.text();


                        console.log(
                            'PAYMENT INITIALIZATION RAW RESPONSE:',
                            responseText
                        );


                        let data;


                        try {

                            data =
                                JSON.parse(
                                    responseText
                                );

                        } catch (parseError) {

                            console.error(
                                'PAYMENT INITIALIZATION RESPONSE IS NOT JSON:',
                                responseText
                            );


                            throw new Error(
                                'The server returned an invalid response. Please try again.'
                            );

                        }


                        console.log(
                            'PAYMENT INITIALIZATION RESPONSE:',
                            data
                        );


                        if (
                            !response.ok ||
                            data.success !== true
                        ) {

                            throw new Error(
                                data.message ||
                                'Unable to initialize payment.'
                            );

                        }


                        if (
                            !data.authorization_url
                        ) {

                            throw new Error(
                                'No Paystack authorization URL was returned.'
                            );

                        }


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


                        payButton.disabled = false;

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
