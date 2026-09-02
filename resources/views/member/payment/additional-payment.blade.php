@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')

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


                        {{-- =========================================================
                        | PAYMENT ITEM
                        ========================================================== --}}

                        <div class="mb-4">

                            <label for="payment_item_id" class="form-label fw-semibold">
                                Select Document / Payment
                            </label>

                            <select id="payment_item_id" class="form-select form-select-lg">

                                <option value="">
                                    -- Select a document to pay for --
                                </option>

                                @foreach ($paymentItems as $item)

                                    <option
                                        value="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-description="{{ $item->description }}"
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

                        <div id="paymentDetails" class="d-none">

                            <div class="border rounded-3 p-4 bg-light">


                                {{-- =================================================
                                | PAYMENT ITEM INFORMATION
                                ================================================== --}}

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


                                {{-- =================================================
                                | DOCUMENT INFORMATION
                                ================================================== --}}

                                <div id="documentInfo" class="d-none mb-4">

                                    <div class="alert alert-info mb-0">

                                        <div class="fw-semibold">
                                            Document
                                        </div>

                                        <div id="documentName"></div>

                                    </div>

                                </div>


                                {{-- =================================================
                                | DYNAMIC DOCUMENT FIELDS
                                ================================================== --}}

                                <div id="documentFieldsSection" class="d-none mb-4">

                                    <hr class="my-4">

                                    <h5 class="fw-bold mb-3">
                                        Document Information
                                    </h5>

                                    <div id="documentFields"></div>

                                </div>


                                {{-- =================================================
                                | NO MANUAL FIELDS
                                ================================================== --}}

                                <div id="noDocumentFields" class="d-none mb-4">

                                    <div class="alert alert-light border mb-0">

                                        No additional information is required for
                                        this document.

                                    </div>

                                </div>


                                {{-- =================================================
                                | AMOUNT + PAY BUTTON
                                ================================================== --}}

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <small class="text-muted d-block">
                                            Amount to Pay
                                        </small>

                                        <h3 id="itemAmount" class="fw-bold mb-0"></h3>

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
            | DOCUMENT FIELD STATE
            |--------------------------------------------------------------------------
            |
            | Pay Now remains disabled until the selected payment item's
            | document information has successfully loaded.
            |
            */

            let documentFieldsLoaded = false;


            /*
            |--------------------------------------------------------------------------
            | RESET DOCUMENT FIELDS
            |--------------------------------------------------------------------------
            */

            function resetDocumentFields() {

                documentFieldsLoaded = false;

                documentInfo.classList.add('d-none');

                documentFieldsSection.classList.add('d-none');

                noDocumentFields.classList.add('d-none');

                documentName.textContent = '';

                documentFields.innerHTML = '';

                payButton.disabled = true;

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE DOCUMENT FIELD
            |--------------------------------------------------------------------------
            */

            function renderField(field) {

                const wrapper =
                    document.createElement('div');

                wrapper.className = 'mb-3';


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
                    field.label || field.field_key;


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

                    required.textContent = '*';

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

                        emptyOption.value = '';

                        emptyOption.textContent =
                            '-- Select --';

                        input.appendChild(emptyOption);


                        if (Array.isArray(field.options)) {

                            field.options.forEach(function (option) {

                                const optionElement =
                                    document.createElement('option');


                                /*
                                |--------------------------------------------------------------------------
                                | OBJECT OPTION
                                |--------------------------------------------------------------------------
                                |
                                | Example:
                                |
                                | {
                                |     value: "dealer",
                                |     label: "Dealer"
                                | }
                                |
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
                                |
                                | Example:
                                |
                                | ["Dealer", "Supplier", "Exporter"]
                                |
                                */

                                else {

                                    optionElement.value =
                                        option ?? '';

                                    optionElement.textContent =
                                        option ?? '';

                                }


                                input.appendChild(optionElement);

                            });

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

                        input.type = 'number';

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

                        input.type = 'date';

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

                        input.type = 'time';

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

                        input.type = 'text';

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
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Laravel initializeAdditional() expects:
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
                    field.placeholder !== undefined
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

                    input.required = true;

                }


                /*
                |--------------------------------------------------------------------------
                | APPEND INPUT
                |--------------------------------------------------------------------------
                */

                wrapper.appendChild(input);


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

                    wrapper.appendChild(section);

                }


                return wrapper;

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD DOCUMENT FIELDS
            |--------------------------------------------------------------------------
            */

            async function loadDocumentFields(paymentItemId) {

                resetDocumentFields();


                try {

                    const response =
                        await fetch(
                            "{{ route('payment.additional.fields') }}" +
                            '?payment_item_id=' +
                            encodeURIComponent(paymentItemId),
                            {
                                method: 'GET',

                                headers: {

                                    'Accept': 'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest'

                                }
                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | READ RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    const data =
                        await response.json();


                    if (
                        !response.ok ||
                        data.status !== 'success'
                    ) {

                        throw new Error(
                            data.message ||
                            'Unable to load document fields.'
                        );

                    }


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

                        documentFieldsLoaded = true;

                        payButton.disabled = false;

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DOCUMENT INFORMATION
                    |--------------------------------------------------------------------------
                    */

                    documentName.textContent =
                        data.document.name || '';

                    documentInfo.classList.remove(
                        'd-none'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | GET MANUAL FIELDS
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

                        documentFieldsLoaded = true;

                        payButton.disabled = false;

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RENDER FIELDS
                    |--------------------------------------------------------------------------
                    */

                    fields.forEach(function (field) {

                        const fieldElement =
                            renderField(field);

                        documentFields.appendChild(
                            fieldElement
                        );

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW DOCUMENT FIELDS
                    |--------------------------------------------------------------------------
                    */

                    documentFieldsSection.classList.remove(
                        'd-none'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | MARK AS LOADED
                    |--------------------------------------------------------------------------
                    */

                    documentFieldsLoaded = true;

                    payButton.disabled = false;

                } catch (error) {

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
                        'Unable to load document fields.';


                    documentFields.appendChild(errorBox);


                    documentFieldsSection.classList.remove(
                        'd-none'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | COLLECT DOCUMENT FIELD VALUES
            |--------------------------------------------------------------------------
            |
            | Collect ONLY the fields rendered inside #documentFields.
            |
            | Example result:
            |
            | {
            |     container_number: "MSCU1234567"
            | }
            |
            */

            function collectDocumentFieldValues() {

                const values = {};


                const fields =
                    documentFields.querySelectorAll(
                        'input[name], textarea[name], select[name]'
                    );


                fields.forEach(function (field) {

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
                        typeof value === 'string'
                    ) {

                        value =
                            value.trim();

                    }


                    values[fieldKey] =
                        value;

                });


                return values;

            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE REQUIRED DOCUMENT FIELDS
            |--------------------------------------------------------------------------
            */

            function validateDocumentFields() {

                const requiredFields =
                    documentFields.querySelectorAll(
                        'input[name][required], textarea[name][required], select[name][required]'
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


            /*
            |--------------------------------------------------------------------------
            | PAYMENT ITEM CHANGE
            |--------------------------------------------------------------------------
            */

            paymentSelect.addEventListener(
                'change',
                async function () {

                    const option =
                        this.options[
                            this.selectedIndex
                        ];


                    resetDocumentFields();


                    /*
                    |--------------------------------------------------------------------------
                    | NOTHING SELECTED
                    |--------------------------------------------------------------------------
                    */

                    if (!this.value) {

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
                        option.dataset.name || '';

                    const description =
                        option.dataset.description || '';

                    const amount =
                        parseFloat(
                            option.dataset.amount || 0
                        );


                    itemName.textContent =
                        name;


                    itemDescription.textContent =
                        description ||
                        'No description available.';


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
                    | LOAD DOCUMENT FIELDS
                    |--------------------------------------------------------------------------
                    */

                    await loadDocumentFields(
                        this.value
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
                    | DOCUMENT FIELDS MUST BE LOADED
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
                    | VALIDATE REQUIRED FIELDS
                    |--------------------------------------------------------------------------
                    */

                    if (!validateDocumentFields()) {

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

                    payButton.disabled = true;

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
                        |
                        | IMPORTANT:
                        |
                        | We are deliberately NOT using JSON here.
                        |
                        | Laravel will receive:
                        |
                        | payment_item_id
                        |
                        | document_field_values[container_number]
                        |
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
                        | DOCUMENT FIELD VALUES
                        |--------------------------------------------------------------------------
                        */

                        Object.keys(
                            documentFieldValues
                        ).forEach(function (fieldKey) {

                            formData.append(
                                'document_field_values[' +
                                fieldKey +
                                ']',
                                documentFieldValues[fieldKey]
                            );

                        });


                        /*
                        |--------------------------------------------------------------------------
                        | DEBUG FORMDATA
                        |--------------------------------------------------------------------------
                        |
                        | Open browser console and you should see:
                        |
                        | payment_item_id = 98
                        | _token = ...
                        | document_field_values[container_number] = MSCU1234567
                        |
                        */

                        console.log(
                            'FORM DATA BEING SENT:'
                        );


                        for (
                            const [key, value]
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
                                    | DO NOT SET Content-Type MANUALLY
                                    |--------------------------------------------------------------------------
                                    |
                                    | The browser automatically sets:
                                    |
                                    | multipart/form-data;
                                    | boundary=...
                                    |
                                    | This is important for Laravel to correctly
                                    | parse document_field_values.
                                    |--------------------------------------------------------------------------
                                    */

                                    headers: {

                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest'

                                    },

                                    body: formData

                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | READ RESPONSE
                        |--------------------------------------------------------------------------
                        */

                        const data =
                            await response.json();


                        console.log(
                            'PAYMENT INITIALIZATION RESPONSE:',
                            data
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR
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
                        | PAYSTACK URL
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
