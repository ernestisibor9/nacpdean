@extends('member.member_dashboard')

@section('title', 'Payment Successful - NACPDEAN')

@section('member')

<div class="container-fluid py-4">


{{-- ============================================================
     SUCCESS HEADER
============================================================= --}}

<div class="row justify-content-center">

    <div class="col-xl-9 col-lg-10">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5">

                {{-- ==================================================
                     SUCCESS ICON
                =================================================== --}}

                <div class="text-center mb-4">

                    <div class="mx-auto mb-3"
                         style="
                            width:80px;
                            height:80px;
                            border-radius:50%;
                            background:#d1e7dd;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                         ">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="42"
                             height="42"
                             fill="#198754"
                             viewBox="0 0 16 16">

                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM7.293 10.707 12.854 5.146l-.708-.708L7.293 9.293 3.854 5.854l-.708.708 4.147 4.145z"/>

                        </svg>

                    </div>

                    <h3 class="fw-bold text-success mb-2">
                        Payment Successful
                    </h3>

                    <p class="text-muted mb-0">
                        Your payment has been successfully verified
                        and recorded.
                    </p>

                </div>


                {{-- ==================================================
                     PAYMENT DOCUMENT
                =================================================== --}}

                <div class="border rounded p-4 p-md-5 mb-4"
                     id="paymentDocument">

                    {{-- ==================================================
                         DOCUMENT HEADER
                    =================================================== --}}

                    <div class="text-center mb-4">

                        {{-- Replace with your actual logo later --}}

                        <h4 class="fw-bold mb-1">
                            NACPDEAN
                        </h4>

                        <div class="text-muted">
                            National Association of
                            Chemical & Pesticide Dealers,
                            Exporters & Allied Network
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold text-uppercase">
                            Payment Receipt
                        </h5>

                    </div>


                    {{-- ==================================================
                         MEMBER + PAYMENT INFORMATION
                    =================================================== --}}

                    <div class="row g-4">

                        {{-- ==========================================
                             MEMBER INFORMATION
                        =========================================== --}}

                        <div class="col-md-7">

                            <h6 class="fw-bold border-bottom pb-2 mb-3">
                                Member Information
                            </h6>

                            <div class="mb-3">

                                <small class="text-muted d-block">
                                    Member Name
                                </small>

                                <strong>
                                    {{ $user->name
                                        ?? trim(
                                            ($user->first_name ?? '') .
                                            ' ' .
                                            ($user->last_name ?? '')
                                        )
                                    }}
                                </strong>

                            </div>


                            @if($membership)

                                <div class="mb-3">

                                    <small class="text-muted d-block">
                                        Membership Number
                                    </small>

                                    <strong>
                                        {{ $membership->membership_number ?? 'N/A' }}
                                    </strong>

                                </div>

                            @endif


                            <div class="mb-3">

                                <small class="text-muted d-block">
                                    Email Address
                                </small>

                                <strong>
                                    {{ $user->email }}
                                </strong>

                            </div>

                        </div>


                        {{-- ==========================================
                             QR CODE
                        =========================================== --}}

                        <div class="col-md-5 text-center">

                            <h6 class="fw-bold border-bottom pb-2 mb-3">
                                Verification
                            </h6>

                            <div class="d-flex justify-content-center">

                                @if(isset($qrCode))

                                    {!! $qrCode !!}

                                @else

                                    <div class="border rounded p-4 text-muted">

                                        QR Code

                                        <br>

                                        <small>
                                            Verification QR
                                        </small>

                                    </div>

                                @endif

                            </div>

                            <small class="text-muted d-block mt-2">
                                Scan this QR code to verify this document.
                            </small>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- ==================================================
                         PAYMENT INFORMATION
                    =================================================== --}}

                    <h6 class="fw-bold border-bottom pb-2 mb-3">
                        Payment Information
                    </h6>

                    <div class="row g-4">

                        {{-- PAYMENT ITEM --}}

                        <div class="col-md-7">

                            <small class="text-muted d-block">
                                Payment Item
                            </small>

                            <strong class="fs-5">
                                {{ $paymentItem->name }}
                            </strong>

                            @if($paymentItem->description)

                                <div class="text-muted mt-1">
                                    {{ $paymentItem->description }}
                                </div>

                            @endif

                        </div>


                        {{-- AMOUNT --}}

                        <div class="col-md-5">

                            <small class="text-muted d-block">
                                Amount Paid
                            </small>

                            <strong class="fs-4 text-success">
                                ₦{{ number_format(
                                    (float) $payment->amount,
                                    2
                                ) }}
                            </strong>

                        </div>


                        {{-- PAYMENT DATE --}}

                        <div class="col-md-7">

                            <small class="text-muted d-block">
                                Payment Date
                            </small>

                            <strong>
                                {{ optional(
                                    $payment->updated_at
                                )->format('d M Y, h:i A') }}
                            </strong>

                        </div>


                        {{-- STATUS --}}

                        <div class="col-md-5">

                            <small class="text-muted d-block">
                                Payment Status
                            </small>

                            <span class="badge bg-success px-3 py-2">
                                PAID
                            </span>

                        </div>


                        {{-- PAYSTACK REFERENCE --}}

                        @if($reference)

                            <div class="col-12">

                                <small class="text-muted d-block">
                                    Payment Reference
                                </small>

                                <strong>
                                    {{ $reference }}
                                </strong>

                            </div>

                        @endif

                    </div>


                    <hr class="my-4">


                    {{-- ==================================================
                         VERIFICATION INFORMATION
                    =================================================== --}}

                    <div class="text-center">

                        <div class="fw-semibold">
                            This document is digitally verifiable.
                        </div>

                        <small class="text-muted">
                            Scan the QR code above to verify the
                            authenticity of this payment/document.
                        </small>

                    </div>

                </div>


                {{-- ====================================================
                     ACTION BUTTONS
                ===================================================== --}}

                <div class="d-flex flex-column flex-md-row
                            justify-content-center gap-2">

                    {{-- VIEW DOCUMENT --}}

                    <a href="#paymentDocument"
                       class="btn btn-outline-primary">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="16"
                             height="16"
                             fill="currentColor"
                             class="me-1"
                             viewBox="0 0 16 16">

                            <path d="M16 8s-3-5-8-5-8 5-8 5 3 5 8 5 8-5 8-5z"/>

                            <path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>

                        </svg>

                        View

                    </a>


                    {{-- PRINT --}}

                    <button type="button"
                            class="btn btn-primary"
                            onclick="printPaymentDocument()">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="16"
                             height="16"
                             fill="currentColor"
                             class="me-1"
                             viewBox="0 0 16 16">

                            <path d="M5 1a2 2 0 0 0-2 2v2h10V3a2 2 0 0 0-2-2H5z"/>

                            <path d="M1 7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v2H4v-2H3a2 2 0 0 1-2-2V7zm3 5v2h8v-2H4z"/>

                        </svg>

                        Print

                    </button>


                    {{-- DOWNLOAD --}}

                    @if(isset($downloadUrl))

                        <a href="{{ $downloadUrl }}"
                           class="btn btn-outline-success">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="16"
                                 height="16"
                                 fill="currentColor"
                                 class="me-1"
                                 viewBox="0 0 16 16">

                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6a.5.5 0 0 1 1 0V13a2 2 0 0 1-2 2H2.5a2 2 0 0 1-2-2v-2.6a.5.5 0 0 1 .5-.5z"/>

                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>

                            </svg>

                            Download

                        </a>

                    @else

                        <button type="button"
                                class="btn btn-outline-success"
                                onclick="downloadPaymentDocument()">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="16"
                                 height="16"
                                 fill="currentColor"
                                 class="me-1"
                                 viewBox="0 0 16 16">

                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.6a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6a.5.5 0 0 1 1 0V13a2 2 0 0 1-2 2H2.5a2 2 0 0 1-2-2v-2.6a.5.5 0 0 1 .5-.5z"/>

                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>

                            </svg>

                            Download

                        </button>

                    @endif


                    {{-- BACK --}}

                    <a href="{{ route('payment.additional') }}"
                       class="btn btn-outline-secondary">

                        Back to Payments

                    </a>

                </div>


                {{-- ====================================================
                     SECURITY NOTICE
                ===================================================== --}}

                <div class="alert alert-light border mt-4 mb-0 text-center">

                    <small class="text-muted">

                        <strong>Security Notice:</strong>
                        This document contains a unique QR code that
                        can be scanned to verify its authenticity.

                    </small>

                </div>

            </div>

        </div>

    </div>

</div>
```

</div>

{{-- ================================================================
PRINT / DOWNLOAD JAVASCRIPT
================================================================ --}}

<script>

function printPaymentDocument()
{
    const documentContent =
        document.getElementById('paymentDocument').innerHTML;

    const printWindow =
        window.open('', '_blank', 'width=900,height=700');

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>

        <head>

            <title>
                NACPDEAN Payment Receipt
            </title>

            <meta charset="UTF-8">

            <style>

                body {
                    font-family: Arial, sans-serif;
                    margin: 40px;
                    color: #212529;
                }

                .text-center {
                    text-align: center;
                }

                .text-muted {
                    color: #6c757d;
                }

                .text-success {
                    color: #198754;
                }

                .text-primary {
                    color: #0d6efd;
                }

                .fw-bold {
                    font-weight: 700;
                }

                .fw-semibold {
                    font-weight: 600;
                }

                .border-bottom {
                    border-bottom: 1px solid #dee2e6;
                }

                .border {
                    border: 1px solid #dee2e6;
                }

                .rounded {
                    border-radius: 6px;
                }

                .mb-1 {
                    margin-bottom: 4px;
                }

                .mb-2 {
                    margin-bottom: 8px;
                }

                .mb-3 {
                    margin-bottom: 16px;
                }

                .mb-4 {
                    margin-bottom: 24px;
                }

                .mt-1 {
                    margin-top: 4px;
                }

                .mt-2 {
                    margin-top: 8px;
                }

                .my-4 {
                    margin-top: 24px;
                    margin-bottom: 24px;
                }

                .p-4 {
                    padding: 24px;
                }

                .row {
                    display: flex;
                    flex-wrap: wrap;
                }

                .col-md-7 {
                    width: 58.333333%;
                }

                .col-md-5 {
                    width: 41.666667%;
                }

                .col-12 {
                    width: 100%;
                }

                small {
                    font-size: 12px;
                }

                svg {
                    max-width: 150px;
                    max-height: 150px;
                }

                @media print {

                    body {
                        margin: 0;
                    }

                }

            </style>

```
    </head>

    <body>

        ${documentContent}

    </body>

    </html>
`);

printWindow.document.close();

printWindow.focus();

setTimeout(function () {

    printWindow.print();

    printWindow.close();

}, 500);


}

function downloadPaymentDocument()
{
/*
|--------------------------------------------------------------------------
| Temporary download method
|--------------------------------------------------------------------------
|
| Later we will replace this with a proper Laravel PDF route.
|
*/


printPaymentDocument();


}

</script>

@endsection
