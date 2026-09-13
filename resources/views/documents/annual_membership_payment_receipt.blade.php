@php
    /*
    |--------------------------------------------------------------------------
    | LOAD GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );

    $documentUser = $generatedDocument->user ?? null;

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    $membershipNumber = $fields->get(
        'membership_number',
        $fields->get(
            'membership_no',
            $fields->get(
                'membership_id',
                ''
            )
        )
    );

    $membership = null;

    if ($documentUser) {

        $membership = \App\Models\Membership::query()
            ->with('membershipCategory')
            ->where('user_id', $documentUser->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (!$membershipNumber && $membership) {
            $membershipNumber = $membership->membership_number;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    |
    | The active membership record is authoritative.
    | Fallback to generated document field_values.
    |
    */

    $membershipCategory     = '';
    $membershipCategoryCode = '';

    if ($membership && $membership->membershipCategory) {

        $membershipCategory = strtoupper(
            trim((string) $membership->membershipCategory->name)
        );

        $membershipCategoryCode = strtoupper(
            trim((string) $membership->membershipCategory->code)
        );

    } else {

        $membershipCategory = strtoupper(
            trim(
                (string) $fields->get(
                    'membership_category',
                    $fields->get('category', '')
                )
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RCG CHECK
    |--------------------------------------------------------------------------
    */

    $isRCG =
        $membershipCategoryCode === 'RCG'
        || $membershipCategory === 'RCG'
        || str_contains($membershipCategory, 'RCG');

    /*
    |--------------------------------------------------------------------------
    | PAYMENT DATE
    |--------------------------------------------------------------------------
    */

    $paymentDate = $fields->get(
        'payment_date',
        $fields->get('paid_at', null)
    );

    $transactionReference = $fields->get(
        'transaction_reference',
        $fields->get('payment_reference', '')
    );

    $receiptPayment = null;

    if ($documentUser) {

        if ($transactionReference) {

            $receiptPayment = \App\Models\Payment::query()
                ->where('user_id', $documentUser->id)
                ->where(function ($query) use ($transactionReference) {
                    $query
                        ->where('reference', $transactionReference)
                        ->orWhere('payment_reference', $transactionReference)
                        ->orWhere('paystack_reference', $transactionReference);
                })
                ->where('status', 'paid')
                ->latest('id')
                ->first();
        }

        if (!$receiptPayment) {

            $receiptPayment = \App\Models\Payment::query()
                ->where('user_id', $documentUser->id)
                ->where('status', 'paid')
                ->where(function ($query) {
                    $query
                        ->where('payment_type', 'membership')
                        ->orWhere('payment_type', 'membership_renewal');
                })
                ->latest('paid_at')
                ->latest('id')
                ->first();
        }
    }

    if (!$paymentDate && $receiptPayment && $receiptPayment->paid_at) {
        $paymentDate = $receiptPayment->paid_at;
    }

    $paymentDateFormatted = '';

    if ($paymentDate) {
        try {
            $paymentDateFormatted = \Carbon\Carbon::parse($paymentDate)->format('d F Y');
        } catch (\Throwable $e) {
            $paymentDateFormatted = (string) $paymentDate;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIPT NUMBER
    |--------------------------------------------------------------------------
    */

    $receiptNumber = $fields->get(
        'receipt_number',
        $fields->get(
            'receipt_no',
            $fields->get('ref_no', $generatedDocument->document_number)
        )
    );

    /*
    |--------------------------------------------------------------------------
    | MEMBER INFO
    |--------------------------------------------------------------------------
    */

    $memberName   = $fields->get('member_name', '');
    $businessName = $fields->get('business_name', '');
    $phone        = $fields->get('phone', $fields->get('phone_number', ''));
    $email        = $fields->get('email', '');
    $state        = $fields->get('state', '');

    $dateJoined = $fields->get('date_joined', '');
    $dateJoinedFormatted = '';

    if ($dateJoined) {
        try {
            $dateJoinedFormatted = \Carbon\Carbon::parse($dateJoined)->format('d F Y');
        } catch (\Throwable $e) {
            $dateJoinedFormatted = (string) $dateJoined;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP YEAR
    |--------------------------------------------------------------------------
    */

    $membershipYear = $fields->get(
        'membership_year',
        $fields->get('membership_year_range', '')
    );

    $membershipYearNumber = $fields->get('membership_year_number', '');

    if (!$membershipYearNumber && $membershipYear) {
        if (preg_match('/\b(20\d{2})\b/', (string) $membershipYear, $matches)) {
            $membershipYearNumber = $matches[1];
        }
    }

    if (!$membershipYearNumber && $paymentDate) {
        try {
            $membershipYearNumber = \Carbon\Carbon::parse($paymentDate)->format('Y');
        } catch (\Throwable $e) {
            //
        }
    }

    if (!$membershipYear && $membershipYearNumber) {
        $membershipYear = "01 January {$membershipYearNumber} - 31 December {$membershipYearNumber}";
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT FOR
    |--------------------------------------------------------------------------
    */

    $paymentFor = $fields->get('payment_for', '');

    if (!$paymentFor && $membershipYearNumber) {
        $paymentFor = "Annual Membership Subscription {$membershipYearNumber}";
    }

    if (!$paymentFor) {
        $paymentFor = 'Annual Membership Subscription';
    }

    /*
    |--------------------------------------------------------------------------
    | AMOUNT PAID
    |--------------------------------------------------------------------------
    */

    $amountPaid = $fields->get(
        'amount_paid',
        $fields->get('amount', '')
    );

    if ($amountPaid === '' && $receiptPayment) {
        $amountPaid = $receiptPayment->amount;
    }

    if (is_numeric($amountPaid)) {
        $amountPaid = '₦' . number_format((float) $amountPaid, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION REFERENCE
    |--------------------------------------------------------------------------
    */

    if (!$transactionReference && $receiptPayment) {
        $transactionReference =
            $receiptPayment->reference
            ?: $receiptPayment->payment_reference
            ?: $receiptPayment->paystack_reference
            ?: '';
    }

    /*
    |--------------------------------------------------------------------------
    | AUTH CODE + VERIFICATION URL
    |--------------------------------------------------------------------------
    */

    $authenticationCode = $fields->get(
        'authentication_code',
        $generatedDocument->tracking_code
    );

    $verificationUrl = $fields->get(
        'verification_url',
        route('documents.verify', $generatedDocument->tracking_code)
    );

    /*
    |--------------------------------------------------------------------------
    | SERIAL TAG
    |--------------------------------------------------------------------------
    */

    $serialTag = $fields->get(
        'serial_number',
        str_pad(
            preg_replace('/\D/', '', (string) $receiptNumber) ?: '1',
            5,
            '0',
            STR_PAD_LEFT
        )
    );

    /*
    |--------------------------------------------------------------------------
    | CATEGORY FLAGS
    |--------------------------------------------------------------------------
    |
    | Retained for potential future use, but no longer rendered as ticks.
    |
    */

    $categoryBlob = strtoupper(
        trim($membershipCategory . ' ' . $membershipCategoryCode)
    );

    $isProducer = (bool) preg_match('/\b(PRODUCER|PRD)\b/', $categoryBlob);
    $isDealer   = (bool) preg_match('/\b(DEALER|DEA)\b/',   $categoryBlob);
    $isSupplier = (bool) preg_match('/\b(SUPPLIER|SLR|SAWMILL)\b/', $categoryBlob);
    $isExporter = (bool) preg_match('/\b(EXPORTER|EXP)\b/', $categoryBlob);

    /*
    |--------------------------------------------------------------------------
    | PDF MODE + BACKGROUND IMAGE
    |--------------------------------------------------------------------------
    */

    $isPdfMode = !empty($downloadMode);

    $receiptBackground = null;

    $receiptPath = public_path(
        'images/certificates/annual-membership-payment-receipt.jpg'
    );

    if (file_exists($receiptPath)) {
        $receiptBackground =
            'data:image/jpeg;base64,' .
            base64_encode(file_get_contents($receiptPath));
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Annual Membership Payment Receipt</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #eeeeee;
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
        }

        .receipt-wrapper {
            width: 100%;
            padding: 30px;
            margin: 0;
        }

        .receipt-card {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 0;
            overflow: hidden;
            background: #ffffff;
        }

        .receipt-background {
            display: block;
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .field {
            position: absolute;
            z-index: 10;
            font-family: Arial, Helvetica, sans-serif;
            color: #111111;
            line-height: 1.25;
            font-size: 10.5px;
            font-weight: 500;
        }

        /* ------------------------------------------------------------------
           LEFT COLUMN — values sit to the RIGHT of printed labels.
        ------------------------------------------------------------------ */

        .f-value-left {
            left: 60mm;
            width: 78mm;
        }

        .f-membership-id            { top: 95.5mm; }
        .f-member-name              { top: 101.5mm; }
        .f-business-name            { top: 107.8mm; }
        .f-membership-category-left { top: 114.0mm; }
        .f-phone                    { top: 119.9mm; }
        .f-email                    { top: 125.5mm; }
        .f-state                    { top: 131.5mm; }
        .f-date-joined              { top: 136.5mm; }

        .f-payment-for              { top: 156.9mm; }
        .f-membership-year          { top: 163.2mm; }
        .f-amount-paid              { top: 169.5mm; }
        .f-payment-date-left        { top: 174.9mm; }
        .f-transaction-ref          { top: 181.5mm; }

        /* ------------------------------------------------------------------
           RIGHT COLUMN
        ------------------------------------------------------------------ */

        .f-receipt-no {
            top: 82.5mm;
            left: 123mm;
            width: 80mm;
            font-size: 10.5px;
            font-weight: 700;
        }

        .f-payment-date-right {
            top: 98mm;
            left: 123mm;
            width: 80mm;
            font-size: 10.5px;
            font-weight: 500;
        }

        /* ------------------------------------------------------------------
           QR CODE
        ------------------------------------------------------------------ */

        .f-qr {
            position: absolute;
            z-index: 12;
            top: 174mm;
            left: 125.4mm;
            width: 26mm;
            height: 26mm;
            display: block;
        }

        .f-qr img {
            display: block;
            width: 26mm;
            height: 26mm;
            border: 0;
        }

        /* Verification code */
        .f-verify-code {
            top: 204mm;
            left: 152mm;
            width: 55mm;
            font-size: 10px;
            font-weight: 800;
            color: #1f7a3d;
            letter-spacing: .5px;
        }

        /* Membership year (red rosette) */
        .f-year-badge {
            top: 214mm;
            left: 160mm;
            width: 40mm;
            text-align: center;
            font-size: 26px;
            font-weight: 900;
            color: #e2231a;
            line-height: 1;
        }

        /* Serial tag (green thank-you bar) */
        .f-serial {
            top: 273mm;
            left: 158mm;
            width: 44mm;
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: .5px;
        }

        /* ------------------------------------------------------------------
           ACTION BUTTONS
        ------------------------------------------------------------------ */

        .document-actions {
            max-width: 210mm;
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
            color: #ffffff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .document-actions a:hover,
        .document-actions button:hover { opacity: .9; }

        /* ------------------------------------------------------------------
           MOBILE
        ------------------------------------------------------------------ */

        @media (max-width: 768px) {

            .receipt-wrapper { padding: 10px; }

            .receipt-card {
                transform: scale(0.42);
                transform-origin: top left;
            }
        }

        /* ------------------------------------------------------------------
           PRINT
        ------------------------------------------------------------------ */

        @media print {

            @page { size: A4 portrait; margin: 0; }

            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            .receipt-wrapper {
                width: 210mm !important;
                height: 297mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .receipt-card {
                width: 210mm !important;
                height: 297mm !important;
                transform: none !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            .receipt-background {
                width: 210mm !important;
                height: 297mm !important;
            }

            .document-actions { display: none !important; }
        }

        /* ------------------------------------------------------------------
           DOMPDF PDF MODE
        ------------------------------------------------------------------ */

        @page { size: A4 portrait; margin: 0; }

        html.pdf-document,
        body.pdf-document {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            overflow: hidden !important;
        }

        body.pdf-document .receipt-wrapper,
        body.pdf-document .receipt-card,
        body.pdf-document .receipt-background {
            width: 210mm !important;
            height: 297mm !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            page-break-before: avoid !important;
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }

        body.pdf-document .receipt-card {
            position: relative !important;
        }

        body.pdf-document .field,
        body.pdf-document .f-qr {
            position: absolute !important;
        }

        body.pdf-document .f-qr img {
            width: 26mm !important;
            height: 26mm !important;
        }

        body.pdf-document .document-actions {
            display: none !important;
        }

    </style>

</head>

<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">

    <div class="receipt-wrapper">

        <div class="receipt-card">

            {{-- =========================================================
                 STATIC CLIENT ARTWORK
            ========================================================== --}}

            @if ($isPdfMode && !empty($receiptBackground))

                <img
                    src="{{ $receiptBackground }}"
                    class="receipt-background"
                    alt="Annual Membership Payment Receipt"
                >

            @else

                <img
                    src="{{ asset('images/certificates/annual-membership-payment-receipt.jpg') }}"
                    class="receipt-background"
                    alt="Annual Membership Payment Receipt"
                >

            @endif

            {{-- =========================================================
                 NAME INFORMATION
            ========================================================== --}}

            <div class="field f-value-left f-membership-id">
                {{ $membershipNumber ?: '' }}
            </div>

            <div class="field f-value-left f-member-name">
                {{ $memberName ?: '' }}
            </div>

            <div class="field f-value-left f-business-name">
                {{ $businessName ?: '' }}
            </div>

            <div class="field f-value-left f-membership-category-left">
                {{ $membershipCategory ?: '' }}
            </div>

            <div class="field f-value-left f-phone">
                {{ $phone ?: '' }}
            </div>

            <div class="field f-value-left f-email">
                {{ $email ?: '' }}
            </div>

            <div class="field f-value-left f-state">
                {{ $state ?: '' }}
            </div>

            <div class="field f-value-left f-date-joined">
                {{ $dateJoinedFormatted ?: '' }}
            </div>

            {{-- =========================================================
                 PAYMENT DETAILS
            ========================================================== --}}

            <div class="field f-value-left f-payment-for">
                {{ $paymentFor }}
            </div>

            <div class="field f-value-left f-membership-year">
                {{ $membershipYear ?: '' }}
            </div>

            <div class="field f-value-left f-amount-paid">
                {{ $amountPaid ?: '' }}
            </div>

            <div class="field f-value-left f-payment-date-left">
                {{ $paymentDateFormatted ?: '' }}
            </div>

            <div class="field f-value-left f-transaction-ref">
                {{ $transactionReference ?: '' }}
            </div>

            {{-- =========================================================
                 RIGHT COLUMN
            ========================================================== --}}

            <div class="field f-receipt-no">
                {{ $receiptNumber }}
            </div>

            <div class="field f-payment-date-right">
                {{ $paymentDateFormatted ?: '' }}
            </div>

            {{-- =========================================================
                 QR CODE
            ========================================================== --}}

            @if (!empty($qrCode))

                <div class="f-qr">
                    <img
                        src="{{ $qrCode }}"
                        alt="Verification QR Code"
                    >
                </div>

            @endif

            {{-- Verification code --}}
            {{--  <div class="field f-verify-code">
                {{ $authenticationCode }}
            </div>  --}}

        </div>

    </div>

    @if (empty($printMode))

        <div class="document-actions">

            <button type="button" onclick="window.print()">
                🖨 Print Receipt
            </button>

            <a href="{{ route('member.documents.download', $generatedDocument) }}">
                ⬇ Download Receipt
            </a>

        </div>

    @endif

</body>
</html>
