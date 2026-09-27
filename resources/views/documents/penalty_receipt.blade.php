@php
    /*
    |--------------------------------------------------------------------------
    | LOAD GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect($generatedDocument->field_values ?? []);

    /*
    |--------------------------------------------------------------------------
    | LOAD USER + PROFILE
    |--------------------------------------------------------------------------
    */

    $documentUser  = $generatedDocument->user ?? null;
    $memberProfile = $documentUser?->profile ?? null;

    /*
    |--------------------------------------------------------------------------
    | HELPER: FIRST NON-EMPTY VALUE
    |--------------------------------------------------------------------------
    */

    $firstFilled = function (...$values) {
        foreach ($values as $v) {
            if ($v !== null && $v !== '') {
                return $v;
            }
        }
        return null;
    };

    /*
    |--------------------------------------------------------------------------
    | PULL DYNAMIC VALUES
    |--------------------------------------------------------------------------
    */

    $receiptNo = $firstFilled(
        $fields->get('receipt_no'),
        $generatedDocument->document_number
    );

    $paymentDate = null;
    if ($generatedDocument->issued_at) {
        try {
            $paymentDate = \Carbon\Carbon::parse($generatedDocument->issued_at)
                ->format('j M Y');
        } catch (\Throwable $e) {
            $paymentDate = (string) $generatedDocument->issued_at;
        }
    }

    $memberName = $firstFilled(
        $fields->get('member_name'),
        $fields->get('payer_name'),
        $memberProfile?->business_name,
        trim(implode(' ', array_filter([
            $memberProfile?->first_name,
            $memberProfile?->middle_name,
            $memberProfile?->surname,
        ]))),
        $documentUser?->name
    );

    $membershipNumber = $firstFilled(
        $fields->get('membership_number'),
        $fields->get('membership_no')
    );

    $memberAddress = $firstFilled(
        $fields->get('member_address'),
        $fields->get('payer_address'),
        $memberProfile?->address,
        $memberProfile?->business_address
    );

    $email = $firstFilled(
        $fields->get('email'),
        $documentUser?->email
    );

    $phone = $firstFilled(
        $fields->get('phone'),
        $memberProfile?->phone,
        $documentUser?->phone
    );

    $purposeOfPayment = $firstFilled(
        $fields->get('purpose_of_payment'),
        $fields->get('penalty_name'),
        'Penalty Payment'
    );

    $paymentMode = $fields->get('payment_mode') ?? 'Bank Transfer';
    $bankName    = $fields->get('bank_name')    ?? 'Access Bank Plc';

    $transactionRef = $firstFilled(
        $fields->get('payment_reference'),
        $fields->get('paystack_reference'),
        $generatedDocument->tracking_code
    );

    $amountPaid = $firstFilled(
        $fields->get('amount_in_figure'),
        $fields->get('amount_paid'),
        $fields->get('amount')
    );

    if ($amountPaid !== null && is_numeric($amountPaid)) {
        $amountPaid = number_format((float) $amountPaid, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | PENALTIES LIST
    |--------------------------------------------------------------------------
    */

    $penaltiesList = $fields->get('penalties_list');

    if (!is_array($penaltiesList) || empty($penaltiesList)) {
        $singlePenalty = $fields->get('penalty_name');
        $penaltiesList = $singlePenalty ? [$singlePenalty] : [];
    }

    /*
    |--------------------------------------------------------------------------
    | PDF MODE
    |--------------------------------------------------------------------------
    */

    $isPdfMode = !empty($downloadMode);

    /*
    |--------------------------------------------------------------------------
    | RECEIPT BACKGROUND
    |--------------------------------------------------------------------------
    */

    $receiptBackground = null;
    $receiptPath = public_path('images/certificates/all-penalty-payment-receipt.jpg');

    if (file_exists($receiptPath)) {
        $receiptBackground = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($receiptPath));
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Penalty Payment Receipt - NACPDEAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body { margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #f5f5f5;
        }

        .receipt-document-wrapper {
            width: 100%;
            padding: 30px 0 50px 0;
        }

        .receipt-page {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0 auto 25px auto;
            padding: 0;
            overflow: hidden;
            background: #fff;
        }

        .receipt-background {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            object-fit: fill;
        }

        /*
        |--------------------------------------------------------------------------
        | DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        |
        | Coordinates are percentages relative to the 210mm × 297mm page.
        | Adjust each top/left/width value in small steps (0.5% at a time)
        | after comparing the rendered output with the client's sample receipt.
        |
        | The client's receipt has these values in fixed positions. Below is a
        | starting layout based on the sample receipt image you shared. Tweak to
        | align with your exact JPG.
        |--------------------------------------------------------------------------
        */

        .receipt-field {
            position: absolute;
            z-index: 10;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Receipt No (top-left under "Receipt No:") */
        .receipt-no {
            top: 25.5%;
            left: 27.0%;
            width: 25%;
            font-weight: bold;
            font-size: 11px;
        }

        /* Payment Date */
        .receipt-payment-date {
            top: 22.7%;
            left: 66.0%;
            width: 20%;
            font-size: 11px;
        }

        /* Purpose of Payment */
        .receipt-purpose {
            top: 24.0%;
            left: 66.0%;
            width: 28%;
            font-size: 11px;
            white-space: normal;
            line-height: 1.15;
        }

        /* Payer Name */
        .receipt-payer-name {
            top: 30.2%;
            left: 27.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Membership No */
        .receipt-membership-no {
            top: 32.5%;
            left: 27.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Address */
        .receipt-address {
            top: 34.8%;
            left: 27.0%;
            width: 25%;
            font-size: 11px;
            white-space: normal;
            line-height: 1.15;
        }

        /* Email */
        .receipt-email {
            top: 39.5%;
            left: 27.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Phone */
        .receipt-phone {
            top: 41.8%;
            left: 27.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Payment Mode */
        .receipt-payment-mode {
            top: 27.7%;
            left: 66.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Bank Name */
        .receipt-bank-name {
            top: 29.7%;
            left: 66.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Transaction Ref */
        .receipt-txn-ref {
            top: 31.8%;
            left: 66.0%;
            width: 25%;
            font-size: 11px;
        }

        /* Amount Paid */
        .receipt-amount {
            top: 33.9%;
            left: 66.0%;
            width: 25%;
            font-size: 11px;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | PENALTIES TABLE
        |--------------------------------------------------------------------------
        |
        | Each row is absolutely positioned so it lines up with the printed
        | S/N rows on the static receipt image.
        |--------------------------------------------------------------------------
        */

        .receipt-penalty-row {
            position: absolute;
            z-index: 10;
            left: 26.0%;
            width: 55%;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Row 1 baseline – adjust to match the "1" row on your artwork */
        .receipt-penalty-row-1 { top: 57.4%; }
        .receipt-penalty-row-2 { top: 59.4%; }
        .receipt-penalty-row-3 { top: 61.4%; }
        .receipt-penalty-row-4 { top: 63.4%; }
        .receipt-penalty-row-5 { top: 65.4%; }
        .receipt-penalty-row-6 { top: 67.4%; }
        .receipt-penalty-row-7 { top: 69.4%; }
        .receipt-penalty-row-8 { top: 71.4%; }
        .receipt-penalty-row-9 { top: 73.4%; }

        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {
            width: 100%;
            margin: 20px auto;
            text-align: center;
        }

        .document-actions a,
        .document-actions button {
            display: inline-block;
            padding: 10px 18px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            background: #10a653;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .receipt-document-wrapper {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .receipt-page {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
            }

            .receipt-background {
                width: 210mm !important;
                height: 297mm !important;
            }

            .document-actions { display: none !important; }
        }

        /*
        |--------------------------------------------------------------------------
        | DOMPDF PDF MODE
        |--------------------------------------------------------------------------
        */

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body.pdf-document,
        html.pdf-document {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow: hidden !important;
        }

        body.pdf-document .receipt-page {
            position: relative !important;
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
        }

        body.pdf-document .receipt-background {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 210mm !important;
            height: 297mm !important;
        }

        body.pdf-document .document-actions { display: none !important; }
    </style>
</head>

<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">

<div class="receipt-document-wrapper">

    <div class="receipt-page">

        {{-- STATIC CLIENT ARTWORK --}}
        @if ($isPdfMode && $receiptBackground)
            <img src="{{ $receiptBackground }}"
                 class="receipt-background"
                 alt="All Penalty Payment Receipt">
        @else
            <img src="{{ asset('images/certificates/all-penalty-payment-receipt.png') }}"
                 class="receipt-background"
                 alt="All Penalty Payment Receipt">
        @endif

        {{-- RECEIPT NO --}}
        @if ($receiptNo)
            <div class="receipt-field receipt-no">{{ $receiptNo }}</div>
        @endif

        {{-- PAYMENT DATE --}}
        @if ($paymentDate)
            <div class="receipt-field receipt-payment-date">{{ $paymentDate }}</div>
        @endif

        {{-- PURPOSE OF PAYMENT --}}
        @if ($purposeOfPayment)
            <div class="receipt-field receipt-purpose">{{ $purposeOfPayment }}</div>
        @endif

        {{-- PAYER NAME --}}
        @if ($memberName)
            <div class="receipt-field receipt-payer-name">{{ $memberName }}</div>
        @endif

        {{-- MEMBERSHIP NO --}}
        @if ($membershipNumber)
            <div class="receipt-field receipt-membership-no">{{ $membershipNumber }}</div>
        @endif

        {{-- ADDRESS --}}
        @if ($memberAddress)
            <div class="receipt-field receipt-address">{{ $memberAddress }}</div>
        @endif

        {{-- EMAIL --}}
        @if ($email)
            <div class="receipt-field receipt-email">{{ $email }}</div>
        @endif

        {{-- PHONE --}}
        @if ($phone)
            <div class="receipt-field receipt-phone">{{ $phone }}</div>
        @endif

        {{-- PAYMENT MODE --}}
        <div class="receipt-field receipt-payment-mode">{{ $paymentMode }}</div>

        {{-- BANK NAME --}}
        <div class="receipt-field receipt-bank-name">{{ $bankName }}</div>

        {{-- TRANSACTION REF --}}
        @if ($transactionRef)
            <div class="receipt-field receipt-txn-ref">{{ $transactionRef }}</div>
        @endif

        {{-- AMOUNT --}}
        @if ($amountPaid)
            <div class="receipt-field receipt-amount">₦{{ $amountPaid }}</div>
        @endif

        {{-- PENALTIES TABLE --}}
        @foreach ($penaltiesList as $index => $penaltyName)
            @php $row = $index + 1; @endphp
            @if ($row <= 9)
                <div class="receipt-penalty-row receipt-penalty-row-{{ $row }}">
                    {{ $penaltyName }}
                </div>
            @endif
        @endforeach

    </div>

</div>

@if (!$printMode)
    <div class="document-actions">
        <button type="button" onclick="window.print()">🖨 Print Receipt</button>
        <a href="{{ route('member.documents.download', $generatedDocument) }}">⬇ Download Receipt</a>
    </div>
@endif

</body>
</html>
