@extends('member.member_dashboard')

@section('title')
NACPDEAN - Payment Successful
@endsection

@section('member')

<style>
    .payment-success-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }

    .success-card {
        background: #fff;
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
    }

    /* ============================================================
       SUCCESS HEADER
    ============================================================ */

    .success-header {
        background: linear-gradient(135deg, #198754, #157347);
        color: #fff;
        padding: 42px 25px;
        text-align: center;
    }

    .success-icon {
        width: 78px;
        height: 78px;
        background: rgba(255, 255, 255, 0.15);
        border: 3px solid rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
    }

    .success-icon i {
        font-size: 42px;
    }

    .success-header h2 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .success-header p {
        margin: 0;
        opacity: 0.92;
        font-size: 15px;
    }


    /* ============================================================
       BODY
    ============================================================ */

    .payment-body {
        padding: 35px;
    }


    /* ============================================================
       NEXT STEP - VERY PROMINENT
    ============================================================ */

    .next-step {
        background: linear-gradient(
            135deg,
            #f0fff7,
            #ffffff
        );

        border: 2px solid #198754;
        border-radius: 15px;

        padding: 28px 25px;

        text-align: center;

        margin-bottom: 35px;

        position: relative;
        overflow: hidden;
    }

    .next-step::before {
        content: "";

        position: absolute;

        top: 0;
        left: 0;

        width: 6px;
        height: 100%;

        background: #198754;
    }

    .next-step-icon {
        width: 52px;
        height: 52px;

        background: #198754;
        color: #fff;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 14px;

        font-size: 25px;
    }

    .next-step h4 {
        font-weight: 800;
        font-size: 22px;
        color: #14532d;
        margin-bottom: 8px;
    }

    .next-step p {
        color: #5f6b66;
        font-size: 14px;

        max-width: 650px;

        margin: 0 auto 22px;

        line-height: 1.6;
    }

    .next-step-label {
        display: inline-block;

        background: #d1e7dd;
        color: #0f5132;

        font-size: 11px;
        font-weight: 800;

        padding: 6px 12px;

        border-radius: 20px;

        margin-bottom: 12px;

        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .application-btn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        gap: 9px;

        background: #0d6efd;
        border: 0;

        color: #fff;

        padding: 14px 32px;

        border-radius: 9px;

        font-size: 16px;
        font-weight: 800;

        min-width: 250px;

        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.25);

        transition: all .2s ease;
    }

    .application-btn:hover {
        background: #0b5ed7;
        color: #fff;

        transform: translateY(-2px);

        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.35);
    }

    .application-btn i {
        font-size: 19px;
    }


    /* ============================================================
       AMOUNT
    ============================================================ */

    .amount-section {
        text-align: center;

        padding: 5px 0 28px;

        border-bottom: 1px solid #eee;

        margin-bottom: 28px;
    }

    .amount-label {
        color: #6c757d;
        font-size: 14px;

        margin-bottom: 5px;
    }

    .amount {
        font-size: 38px;
        font-weight: 800;

        color: #212529;

        letter-spacing: -1px;
    }

    .payment-status {
        display: inline-flex;

        align-items: center;

        gap: 6px;

        background: #d1e7dd;
        color: #0f5132;

        padding: 7px 14px;

        border-radius: 30px;

        font-size: 12px;
        font-weight: 700;

        margin-top: 10px;
    }


    /* ============================================================
       SECTION TITLE
    ============================================================ */

    .section-title {
        font-size: 15px;

        font-weight: 700;

        color: #212529;

        margin-bottom: 15px;
    }


    /* ============================================================
       DETAILS
    ============================================================ */

    .details-card {
        border: 1px solid #e9ecef;

        border-radius: 12px;

        overflow: hidden;

        background: #fff;
    }

    .detail-row {
        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 20px;

        padding: 15px 18px;

        border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
        border-bottom: 0;
    }

    .detail-label {
        color: #6c757d;

        font-size: 13px;

        font-weight: 500;
    }

    .detail-value {
        color: #212529;

        font-size: 14px;

        font-weight: 600;

        text-align: right;

        word-break: break-word;
    }

    .reference {
        font-family: monospace;

        background: #f8f9fa;

        padding: 5px 8px;

        border-radius: 5px;

        font-size: 12px;
    }


    /* ============================================================
       BALANCE
    ============================================================ */

    .balance-card {
        margin-top: 25px;

        background: #f8f9fa;

        border: 1px solid #e9ecef;

        border-radius: 12px;

        padding: 20px;

        text-align: center;
    }

    .balance-label {
        color: #6c757d;

        font-size: 13px;

        margin-bottom: 4px;
    }

    .balance-amount {
        font-size: 24px;

        font-weight: 800;

        color: #212529;
    }


    /* ============================================================
       MOBILE
    ============================================================ */

    @media (max-width: 576px) {

        .payment-success-wrapper {
            width: 100%;
        }

        .success-header {
            padding: 35px 20px;
        }

        .success-header h2 {
            font-size: 25px;
        }

        .payment-body {
            padding: 22px 16px;
        }

        .amount {
            font-size: 32px;
        }

        .next-step {
            padding: 25px 18px;
        }

        .next-step h4 {
            font-size: 20px;
        }

        .application-btn {
            width: 100%;

            min-width: unset;

            padding: 15px 20px;

            font-size: 15px;
        }

        .detail-row {
            align-items: flex-start;

            flex-direction: column;

            gap: 5px;
        }

        .detail-value {
            text-align: left;
        }
    }
</style>

<div class="container py-4 py-md-5">

<div class="payment-success-wrapper">

    <div class="success-card">


        {{-- ========================================================
             SUCCESS HEADER
        ========================================================= --}}
        <div class="success-header">

            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <h2>
                Payment Successful
            </h2>

            <p>
                Your payment has been received and successfully processed.
            </p>

        </div>


        {{-- ========================================================
             PAYMENT BODY
        ========================================================= --}}
        <div class="payment-body">


            {{-- ====================================================
                 IMPORTANT NEXT STEP
                 THIS IS INTENTIONALLY AT THE TOP
            ===================================================== --}}
            <div class="next-step">

                <div class="next-step-icon">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <div class="next-step-label">
                    Next Step
                </div>

                <h4>
                    Complete Your Application Profile
                </h4>

                <p>
                    Your payment is confirmed. The next step is to
                    complete your application form and provide your
                    profile information so that your membership
                    application can be processed.
                </p>

                <a href="{{ route('member.profile') }}"
                   class="btn application-btn">

                    <i class="bi bi-arrow-right-circle-fill"></i>

                    <span>
                        COMPLETE APPLICATION FORM
                    </span>

                </a>

            </div>


            {{-- ====================================================
                 AMOUNT PAID
            ===================================================== --}}
            <div class="amount-section">

                <div class="amount-label">
                    Amount Paid
                </div>

                <div class="amount">
                    ₦{{ number_format((float) $credit->amount, 2) }}
                </div>

                <div class="payment-status">

                    <i class="bi bi-check-circle-fill"></i>

                    Payment Confirmed

                </div>

            </div>


            {{-- ====================================================
                 PAYMENT DETAILS
            ===================================================== --}}
            <div class="section-title">
                Payment Details
            </div>

            <div class="details-card">


                {{-- REFERENCE --}}
                <div class="detail-row">

                    <div class="detail-label">
                        Transaction Reference
                    </div>

                    <div class="detail-value">

                        <span class="reference">
                            {{ data_get($credit->gateway, 'paystack', 'N/A') }}
                        </span>

                    </div>

                </div>


                {{-- PAYMENT --}}
                @if ($credit->paymentItem)

                    <div class="detail-row">

                        <div class="detail-label">
                            Payment
                        </div>

                        <div class="detail-value">
                            {{ $credit->paymentItem->name }}
                        </div>

                    </div>

                @endif


                {{-- PAYMENT TYPE --}}
                @if ($credit->paymentItem && $credit->paymentItem->type)

                    <div class="detail-row">

                        <div class="detail-label">
                            Payment Type
                        </div>

                        <div class="detail-value">
                            {{ ucfirst($credit->paymentItem->type) }}
                        </div>

                    </div>

                @endif


                {{-- DESCRIPTION --}}
                <div class="detail-row">

                    <div class="detail-label">
                        Description
                    </div>

                    <div class="detail-value">
                        {{ $debit->narration ?? 'Payment received' }}
                    </div>

                </div>


                {{-- STATUS --}}
                <div class="detail-row">

                    <div class="detail-label">
                        Status
                    </div>

                    <div class="detail-value">

                        <span class="badge bg-success px-3 py-2">

                            <i class="bi bi-check-circle me-1"></i>

                            PAID

                        </span>

                    </div>

                </div>


                {{-- DATE --}}
                <div class="detail-row">

                    <div class="detail-label">
                        Payment Date
                    </div>

                    <div class="detail-value">
                        {{ $credit->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                    </div>

                </div>

            </div>


            {{-- ====================================================
                 ACCOUNT BALANCE
            ===================================================== --}}
            <div class="balance-card">

                <div class="balance-label">
                    Current Account Balance
                </div>

                <div class="balance-amount">
                    ₦{{ number_format((float) $balance, 2) }}
                </div>

            </div>


        </div>

    </div>

</div>

</div>

@endsection
