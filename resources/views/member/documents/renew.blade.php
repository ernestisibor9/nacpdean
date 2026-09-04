@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Renew Document')

@section('member')

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-md-8">

                <div class="card shadow-sm">

                    <div class="card-header">
                        <h5 class="mb-0">
                            Renew Document
                        </h5>
                    </div>

                    <div class="card-body">

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                ></button>
                            </div>
                        @endif

                        {{-- Error Message --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                ></button>
                            </div>
                        @endif

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">

                                <ul class="mb-0">

                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                </ul>

                            </div>
                        @endif


                        {{-- Expired Document Notice --}}
                        <div class="alert alert-warning">

                            <strong>
                                This document has expired.
                            </strong>

                            <br>

                            Complete the renewal payment to receive
                            a new valid document.

                        </div>


                        {{-- Original Document --}}
                        <div class="mb-4">

                            <h6>
                                Expired Document
                            </h6>

                            <p class="mb-1">

                                <strong>
                                    {{ $generatedDocument->document?->name ?? 'Document' }}
                                </strong>

                            </p>

                            <p class="text-muted mb-1">

                                Document No:
                                {{ $generatedDocument->document_number ?? 'N/A' }}

                            </p>

                            <p class="text-muted mb-0">

                                Expired:
                                {{ $generatedDocument->expires_at?->format('d M Y') ?? 'N/A' }}

                            </p>

                        </div>


                        <hr>


                        {{-- Renewal Payment --}}
                        <div class="mb-4">

                            <h6>
                                Renewal Payment
                            </h6>

                            <p class="mb-1">
                                {{ $paymentItem->name }}
                            </p>

                            <h4 class="mb-0">
                                ₦{{ number_format($paymentItem->amount, 2) }}
                            </h4>

                        </div>


                        {{-- Renewal Form --}}
                        <form
                            method="POST"
                            action="{{ route('payment.additional.initialize') }}"
                            id="renewDocumentForm"
                        >

                            @csrf


                            {{-- Payment Item --}}
                            <input
                                type="hidden"
                                name="payment_item_id"
                                value="{{ $paymentItem->id }}"
                            >


                            {{-- Original Generated Document --}}
                            <input
                                type="hidden"
                                name="renewal_document_id"
                                value="{{ $generatedDocument->id }}"
                            >


                            {{-- Dynamic Manual Fields --}}
                            @if ($fields->count())

                                <hr>

                                <h6 class="mb-3">
                                    Document Information
                                </h6>


                                @foreach ($fields as $field)

                                    @php

                                        $previousValue = $previousValues->get(
                                            $field->field_key,
                                            $field->default_value
                                        );

                                    @endphp


                                    <div class="mb-3">

                                        <label
                                            for="{{ $field->field_key }}"
                                            class="form-label"
                                        >

                                            {{ $field->label }}

                                            @if ($field->is_required)

                                                <span class="text-danger">
                                                    *
                                                </span>

                                            @endif

                                        </label>


                                        {{-- TEXTAREA --}}
                                        @if ($field->field_type === 'textarea')

                                            <textarea
                                                name="document_field_values[{{ $field->field_key }}]"
                                                id="{{ $field->field_key }}"
                                                class="form-control"
                                                placeholder="{{ $field->placeholder }}"
                                                @if ($field->is_required) required @endif
                                            >{{ old(
                                                'document_field_values.' . $field->field_key,
                                                $previousValue
                                            ) }}</textarea>


                                        {{-- SELECT --}}
                                        @elseif ($field->field_type === 'select')

                                            <select
                                                name="document_field_values[{{ $field->field_key }}]"
                                                id="{{ $field->field_key }}"
                                                class="form-select"
                                                @if ($field->is_required) required @endif
                                            >

                                                <option value="">
                                                    Select {{ $field->label }}
                                                </option>


                                                @foreach ($field->options ?? [] as $option)

                                                    @php

                                                        $optionValue = is_array($option)
                                                            ? ($option['value'] ?? '')
                                                            : $option;

                                                        $optionLabel = is_array($option)
                                                            ? ($option['label'] ?? $optionValue)
                                                            : $option;

                                                    @endphp


                                                    <option
                                                        value="{{ $optionValue }}"
                                                        @selected(
                                                            old(
                                                                'document_field_values.' . $field->field_key,
                                                                $previousValue
                                                            ) == $optionValue
                                                        )
                                                    >

                                                        {{ $optionLabel }}

                                                    </option>

                                                @endforeach

                                            </select>


                                        {{-- INPUT --}}
                                        @else

                                            <input
                                                type="{{ in_array(
                                                    $field->field_type,
                                                    ['number', 'date', 'time'],
                                                    true
                                                ) ? $field->field_type : 'text' }}"
                                                name="document_field_values[{{ $field->field_key }}]"
                                                id="{{ $field->field_key }}"
                                                class="form-control"
                                                value="{{ old(
                                                    'document_field_values.' . $field->field_key,
                                                    $previousValue
                                                ) }}"
                                                placeholder="{{ $field->placeholder }}"
                                                @if ($field->is_required) required @endif
                                            >

                                        @endif

                                    </div>

                                @endforeach

                            @else

                                <div class="alert alert-info">

                                    This document does not require any additional
                                    information.

                                </div>

                            @endif


                            {{-- Pay Button --}}
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                                id="renewDocumentButton"
                            >

                                <i class="bi bi-credit-card me-1"></i>

                                Pay ₦{{ number_format($paymentItem->amount, 2) }}
                                & Renew Document

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Paystack Redirect Script --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const form = document.getElementById('renewDocumentForm');

            const button = document.getElementById('renewDocumentButton');


            if (!form || !button) {
                return;
            }


            form.addEventListener('submit', async function (event) {

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | HTML5 VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!form.checkValidity()) {

                    form.reportValidity();

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SAVE ORIGINAL BUTTON TEXT
                |--------------------------------------------------------------------------
                */

                const originalButtonText =
                    button.innerHTML;


                /*
                |--------------------------------------------------------------------------
                | DISABLE BUTTON
                |--------------------------------------------------------------------------
                */

                button.disabled = true;

                button.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                    ></span>

                    Preparing Payment...
                `;


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | CSRF TOKEN
                    |--------------------------------------------------------------------------
                    */

                    const csrfTokenElement =
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        );


                    if (!csrfTokenElement) {

                        throw new Error(
                            'CSRF token was not found.'
                        );
                    }


                    const csrfToken =
                        csrfTokenElement.getAttribute(
                            'content'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SUBMIT FORM
                    |--------------------------------------------------------------------------
                    */

                    const response =
                        await fetch(
                            form.action,
                            {
                                method: 'POST',

                                headers: {

                                    'X-CSRF-TOKEN':
                                        csrfToken,

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                },

                                body:
                                    new FormData(form),
                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | READ RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    const data =
                        await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | PAYSTACK URL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response.ok &&
                        data.success &&
                        data.authorization_url
                    ) {

                        button.innerHTML = `
                            <span
                                class="spinner-border spinner-border-sm me-2"
                                role="status"
                                aria-hidden="true"
                            ></span>

                            Redirecting to Paystack...
                        `;


                        /*
                        |--------------------------------------------------------------------------
                        | REDIRECT MEMBER TO PAYSTACK
                        |--------------------------------------------------------------------------
                        */

                        window.location.href =
                            data.authorization_url;


                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PAYMENT INITIALIZATION FAILED
                    |--------------------------------------------------------------------------
                    */

                    const message =
                        data.message ||
                        'Unable to initialize payment. Please try again.';


                    alert(message);


                    button.disabled = false;

                    button.innerHTML =
                        originalButtonText;


                } catch (error) {

                    console.error(
                        'Renewal payment initialization error:',
                        error
                    );


                    alert(
                        'An error occurred while preparing your payment. Please try again.'
                    );


                    button.disabled = false;

                    button.innerHTML =
                        originalButtonText;

                }

            });

        });

    </script>

@endsection
