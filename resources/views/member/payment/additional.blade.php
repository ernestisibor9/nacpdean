@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Payments')

@section('member')

<style>

    .payment-page {
        min-height: calc(100vh - 60px);
        background: #f5f7f6;
        padding: 30px 0 50px;
    }

    .payment-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0,0,0,.05);
        overflow: hidden;
    }

    .payment-box-header {
        padding: 25px 28px;
        background: linear-gradient(
            135deg,
            #064e3b,
            #047857
        );
        color: #fff;
    }

    .payment-box-header h2 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 800;
    }

    .payment-box-header p {
        margin: 0;
        color: rgba(255,255,255,.8);
        font-size: 14px;
    }

    .payment-box-body {
        padding: 28px;
    }

    .payment-label {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }

    .payment-select {
        width: 100%;
        min-height: 52px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 0 15px;
        font-size: 14px;
        color: #374151;
        background: #fff;
    }

    .payment-select:focus {
        border-color: #047857;
        outline: none;
        box-shadow: 0 0 0 3px rgba(4,120,87,.1);
    }

    .payment-summary {
        margin-top: 24px;
        padding: 22px;
        background: #f8faf9;
        border: 1px solid #edf0ee;
        border-radius: 14px;
    }

    .payment-summary-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .payment-summary-name {
        color: #111827;
        font-size: 18px;
        font-weight: 800;
        margin-top: 5px;
    }

    .payment-summary-description {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
        margin-top: 5px;
    }

    .payment-summary-amount {
        color: #047857;
        font-size: 28px;
        font-weight: 900;
        margin-top: 15px;
    }

    .pay-button {
        width: 100%;
        margin-top: 22px;
        min-height: 50px;
        border: 0;
        border-radius: 10px;
        background: #047857;
        color: #fff;
        font-weight: 800;
        font-size: 14px;
    }

    .pay-button:hover {
        background: #065f46;
    }

    .recent-payment {
        border-bottom: 1px solid #f0f1f2;
        padding: 15px 0;
    }

    .recent-payment:last-child {
        border-bottom: 0;
    }

    .recent-payment-title {
        font-weight: 800;
        color: #374151;
        font-size: 14px;
    }

    .recent-payment-meta {
        color: #6b7280;
        font-size: 12px;
        margin-top: 4px;
    }

    .paid-badge {
        background: #ecfdf5;
        color: #047857;
        border-radius: 20px;
        padding: 5px 9px;
        font-size: 11px;
        font-weight: 800;
    }

</style>


<div class="payment-page">

    <div class="container-fluid px-4">

        @if(session('success'))

            <div class="alert alert-success mb-4">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-danger mb-4">

                <i class="fas fa-exclamation-circle me-2"></i>

                {{ session('error') }}

            </div>

        @endif


        <div class="row g-4">

            {{-- =====================================================
                 PAYMENT FORM
            ====================================================== --}}

            <div class="col-lg-7">

                <div class="payment-box">

                    <div class="payment-box-header">

                        <h2>
                            Make a Payment
                        </h2>

                        <p>
                            Select the service or document you want to pay for.
                        </p>

                    </div>


                    <div class="payment-box-body">

                        <form
                            method="POST"
                            action="{{ route('payment.additional.initialize') }}"
                        >

                            @csrf


                            <div>

                                <label class="payment-label">

                                    What would you like to pay for?

                                </label>


                                <select
                                    name="payment_item_id"
                                    id="paymentItem"
                                    class="payment-select"
                                    required
                                >

                                    <option value="">
                                        -- Select Payment --
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


                            <div
                                id="paymentSummary"
                                class="payment-summary"
                                style="display:none;"
                            >

                                <div class="payment-summary-label">
                                    Selected Payment
                                </div>

                                <div
                                    id="paymentName"
                                    class="payment-summary-name"
                                ></div>


                                <div
                                    id="paymentDescription"
                                    class="payment-summary-description"
                                ></div>


                                <div
                                    id="paymentAmount"
                                    class="payment-summary-amount"
                                >
                                    ₦0.00
                                </div>

                            </div>


                            <button
                                type="submit"
                                id="payButton"
                                class="pay-button"
                                disabled
                            >

                                <i class="fas fa-lock me-2"></i>

                                Pay Now

                            </button>

                        </form>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 RECENT PAYMENTS
            ====================================================== --}}

            <div class="col-lg-5">

                <div class="payment-box">

                    <div class="payment-box-header">

                        <h2>
                            Recent Payments
                        </h2>

                        <p>
                            Your recent membership-related payments.
                        </p>

                    </div>


                    <div class="payment-box-body">

                        @forelse ($recentPayments as $payment)

                            <div class="recent-payment">

                                <div class="d-flex justify-content-between gap-3">

                                    <div>

                                        <div class="recent-payment-title">

                                            {{ $payment->paymentItem->name
                                                ?? $payment->description
                                                ?? ucfirst($payment->payment_type) }}

                                        </div>


                                        <div class="recent-payment-meta">

                                            ₦{{ number_format(
                                                (float) $payment->amount,
                                                2
                                            ) }}

                                            &nbsp; • &nbsp;

                                            {{ $payment->created_at->format('d M Y') }}

                                        </div>

                                    </div>


                                    @if ($payment->status === 'paid')

                                        <span class="paid-badge">
                                            PAID
                                        </span>

                                    @elseif ($payment->status === 'pending')

                                        <span class="badge bg-warning">
                                            PENDING
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            FAILED
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-4">

                                <i class="fas fa-receipt text-muted mb-2"
                                    style="font-size:30px;"></i>

                                <p class="text-muted mb-0">

                                    No additional payments yet.

                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const select =
            document.getElementById(
                'paymentItem'
            );

        const summary =
            document.getElementById(
                'paymentSummary'
            );

        const name =
            document.getElementById(
                'paymentName'
            );

        const description =
            document.getElementById(
                'paymentDescription'
            );

        const amount =
            document.getElementById(
                'paymentAmount'
            );

        const button =
            document.getElementById(
                'payButton'
            );


        select.addEventListener(
            'change',
            function () {

                const option =
                    this.options[
                        this.selectedIndex
                    ];


                if (!this.value) {

                    summary.style.display =
                        'none';

                    button.disabled =
                        true;

                    return;
                }


                const selectedName =
                    option.dataset.name
                    || '';


                const selectedDescription =
                    option.dataset.description
                    || '';


                const selectedAmount =
                    parseFloat(
                        option.dataset.amount
                        || 0
                    );


                name.textContent =
                    selectedName;


                description.textContent =
                    selectedDescription;


                amount.textContent =
                    '₦' +
                    selectedAmount.toLocaleString(
                        'en-NG',
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    );


                summary.style.display =
                    'block';


                button.disabled =
                    selectedAmount <= 0;

            });

    });

</script>

@endsection
