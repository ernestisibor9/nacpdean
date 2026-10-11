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

    /*
    |--------------------------------------------------------------------------
    | PAYMENT DATE — prefer issued_at, fall back to field value
    |--------------------------------------------------------------------------
    */

    $paymentDate = null;

    if ($generatedDocument->issued_at) {
        try {
            $paymentDate = \Carbon\Carbon::parse($generatedDocument->issued_at)
                ->format('j M Y');
        } catch (\Throwable $e) {
            $paymentDate = (string) $generatedDocument->issued_at;
        }
    }

    if (!$paymentDate) {
        $raw = $fields->get('payment_date');

        if ($raw) {
            try {
                $paymentDate = \Carbon\Carbon::parse($raw)->format('j M Y');
            } catch (\Throwable $e) {
                $paymentDate = (string) $raw;
            }
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
    $receiptPath = public_path('images/certificates/all-penalty-payment-receipt.png');

    if (file_exists($receiptPath)) {
        $receiptBackground = 'data:image/png;base64,' . base64_encode(file_get_contents($receiptPath));
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
        | Tune each top/left/width value in 0.2% – 0.5% steps as needed.
        |--------------------------------------------------------------------------
        */

        .receipt-field {
            position: absolute;
            z-index: 10;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* RECEIPT NO — sits on the "Receipt No:" line */
        .receipt-no {
            top: 26.6%;
            left: 29.8%;
            width: 30%;
            font-weight: bold;
            font-size: 11px;
        }

        /* PAYMENT DATE — inside payment box */
        .receipt-payment-date {
            top: 27.8%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
        }

        /* PURPOSE OF PAYMENT — allow wrap, tight leading */
        .receipt-purpose {
            top: 29.9%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
            white-space: normal;
            line-height: 1.15;
        }

        /* LEFT COLUMN: payer details */
        .receipt-payer-name {
            top: 30.84%;
            left: 30.5%;
            width: 26%;
            font-size: 11px;
        }

        .receipt-membership-no {
            top: 32.87%;
            left: 30.5%;
            width: 26%;
            font-size: 11px;
        }

        .receipt-address {
            top: 36.1%;
            left: 30.5%;
            width: 26%;
            font-size: 11px;
            white-space: normal;
            line-height: 1.15;
        }

        .receipt-email {
            top: 39.87%;
            left: 30.5%;
            width: 26%;
            font-size: 11px;
        }

        .receipt-phone {
            top: 42.0%;
            left: 30.5%;
            width: 26%;
            font-size: 11px;
        }

        /* RIGHT COLUMN: payment box */
        .receipt-payment-mode {
            top: 33.7%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
        }

        .receipt-bank-name {
            top: 35.9%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
        }

        .receipt-txn-ref {
            top: 38.1%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
        }

        .receipt-amount {
            top: 40.1%;
            left: 72.0%;
            width: 25%;
            font-size: 11px;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | QR CODE — sits INSIDE the RECEIPT AUTHENTICATION box
        |--------------------------------------------------------------------------
        */

        .receipt-qr-code {
            position: absolute;
            z-index: 10;
            top: 77.2%;
            left: 33.2%;
            width: 6.8%;
            height: auto;
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
            left: 30.5%;
            width: 55%;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Row baselines — tuned to sit ON each row line */
        .receipt-penalty-row-1 { top: 59.1%; }
        .receipt-penalty-row-2 { top: 60.9%; }
        .receipt-penalty-row-3 { top: 62.7%; }
        .receipt-penalty-row-4 { top: 64.5%; }
        .receipt-penalty-row-5 { top: 66.3%; }
        .receipt-penalty-row-6 { top: 68.1%; }
        .receipt-penalty-row-7 { top: 69.9%; }
        .receipt-penalty-row-8 { top: 71.7%; }
        .receipt-penalty-row-9 { top: 73.5%; }

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

        {{-- QR CODE — sits inside the RECEIPT AUTHENTICATION box --}}
        @if (!empty($qrCode))
            <img src="{{ $qrCode }}"
                 class="receipt-qr-code"
                 alt="Verification QR Code">
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
