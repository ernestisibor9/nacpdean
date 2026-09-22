@php
    /*
    |--------------------------------------------------------------------------
    | GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );


    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    $documentUser = $generatedDocument->user ?? null;


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    $membership = null;

    if ($documentUser) {
        $membership = \App\Models\Membership::query()
            ->with(['profile', 'membershipCategory'])
            ->where('user_id', $documentUser->id)
            ->latest('id')
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | RECEIPT NUMBER
    |--------------------------------------------------------------------------
    */

    $receiptNo = $fields->get(
        'receipt_no',
        $generatedDocument->document_number
    );


    /*
    |--------------------------------------------------------------------------
    | MEMBER NAME
    |--------------------------------------------------------------------------
    */

    $memberName = '';

    foreach ([
        'member_name',
        'business_name',
        'company_name',
        'full_name',
        'name',
    ] as $key) {
        $value = $fields->get($key);

        if ($value !== null && trim((string) $value) !== '') {
            $memberName = trim((string) $value);
            break;
        }
    }

    if (!$memberName && $membership?->profile) {
        $profile = $membership->profile;

        foreach ([
            'business_name',
            'company_name',
            'full_name',
        ] as $key) {
            $value = $profile->{$key} ?? null;

            if ($value !== null && trim((string) $value) !== '') {
                $memberName = trim((string) $value);
                break;
            }
        }

        if (!$memberName) {
            $memberName = strtoupper(trim(
                ($profile->surname ?? '') . ' ' .
                ($profile->first_name ?? '') . ' ' .
                ($profile->middle_name ?? '')
            ));
        }
    }

    if (!$memberName && $documentUser) {
        $memberName = $documentUser->name ?? 'Member';
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER
    |--------------------------------------------------------------------------
    */

    $membershipNo = $fields->get(
        'membership_no',
        $fields->get(
            'membership_number',
            $membership?->membership_number ?? ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | LIFTING RIGHT NUMBER
    |--------------------------------------------------------------------------
    */

    $liftingRightNo = $fields->get('lifting_right_no', '');


    /*
    |--------------------------------------------------------------------------
    | PHONE
    |--------------------------------------------------------------------------
    */

    $phone = $fields->get(
        'phone',
        $membership?->profile?->phone ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | CONTAINER / TRUCK
    |--------------------------------------------------------------------------
    */

    $containerNumber = $fields->get('container_number', '');
    $truckNumber     = $fields->get('truck_number', '');


    /*
    |--------------------------------------------------------------------------
    | DEALER / SUPPLIER
    |--------------------------------------------------------------------------
    */

    $dealerSupplierName   = $fields->get('dealer_supplier_name', '');
    $supplierMembershipNo = $fields->get('supplier_membership_no', '');
    $dealingRightNo       = $fields->get('dealing_right_no', '');


    /*
    |--------------------------------------------------------------------------
    | LOCATION
    |--------------------------------------------------------------------------
    */

    $loadingPoint = $fields->get('loading_point', '');
    $state        = $fields->get('state', '');
    $lga          = $fields->get('lga', '');


    /*
    |--------------------------------------------------------------------------
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    $amountPaid     = $fields->get('amount_paid', '');
    $amountInFigure = $fields->get('amount_in_figure', '');
    $paymentDate    = $fields->get('payment_date', '');
    $paymentTime    = $fields->get('payment_time', '');


    /*
    |--------------------------------------------------------------------------
    | FORMAT AMOUNT
    |--------------------------------------------------------------------------
    */

    if (is_numeric($amountPaid)) {
        $amountPaid = '₦' . number_format((float) $amountPaid, 2);
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT DATE
    |--------------------------------------------------------------------------
    */

    if ($paymentDate) {
        try {
            $paymentDate = \Carbon\Carbon::parse($paymentDate)
                ->format('d-m-Y');
        } catch (\Throwable $e) {
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT TIME
    |--------------------------------------------------------------------------
    */

    if ($paymentTime) {
        try {
            $paymentTime = \Carbon\Carbon::parse($paymentTime)
                ->format('g:i A');
        } catch (\Throwable $e) {
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TRACKING CODE
    |--------------------------------------------------------------------------
    */

    $trackingCode = $fields->get(
        'tracking_code',
        $generatedDocument->tracking_code
    );


    /*
    |--------------------------------------------------------------------------
    | QR CODE
    |--------------------------------------------------------------------------
    */

    $documentQrCode = $qrCode ?? null;


    /*
    |--------------------------------------------------------------------------
    | PDF MODE
    |--------------------------------------------------------------------------
    */

    $isPdfMode = !empty($downloadMode);


    /*
    |--------------------------------------------------------------------------
    | STATIC CLIENT ARTWORK (BASE64 FOR DOMPDF)
    |--------------------------------------------------------------------------
    */

    $certificateBackground = null;

    $certificatePath = public_path(
        'images/documents/afforestation-compliance-payment.jpg'
    );

    if (file_exists($certificatePath)) {
        $certificateBackground =
            'data:image/jpeg;base64,' .
            base64_encode(file_get_contents($certificatePath));
    }

@endphp


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $receiptNo }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        /* -------- RESET -------- */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }

        body {
            background: #eeeeee;
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
        }

        /* -------- WRAPPER -------- */
        .nacp-certificate-wrapper {
            width: 100%;
            padding: 30px;
            margin: 0;
        }

        /* -------- CERTIFICATE -------- */
        .nacp-certificate {
            position: relative;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 0;
            overflow: hidden;
            background: #ffffff;
        }

        /* -------- STATIC BACKGROUND -------- */
        .nacp-certificate-background {
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

        /* -------- DYNAMIC FIELDS -------- */
        .nacp-certificate-field {
            position: absolute;
            z-index: 10;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
        }

        /* ------------------------------------------------------------
           FIELD POSITIONS
           ------------------------------------------------------------
           Tuned so each value sits exactly on the line drawn in the
           client's receipt artwork. If any value is slightly above or
           below its line, nudge the `top` by ±0.3% at a time.
        ------------------------------------------------------------ */

        /* ---- TOP HEADER ---- */

        .nacp-receipt-no {
            top: 12.4%;
            left: 72%;
            width: 24%;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
        }

        /* ---- SHIPPER DETAILS ---- */

        .nacp-member-name {
            top: 39.90%;
            left: 30%;
            width: 55%;
            font-size: 13px;
            font-weight: bold;
        }

        .nacp-membership-no {
            top: 42.5%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-lifting-right-no {
            top: 45.6%;
            left: 40%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-phone {
            top: 47.50%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-container-number {
            top: 50.52%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-truck-number {
            top: 53.1%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        /* ---- SUPPLIER DETAILS ---- */

        .nacp-dealer-name {
            top: 60.85%;
            left: 33%;
            width: 55%;
            font-size: 12px;
            font-weight: bold;
        }

        .nacp-supplier-membership-no {
            top: 64.0%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-dealing-right-no {
            top: 66.1%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-loading-point {
            top: 68.9%;
            left: 30%;
            width: 55%;
            font-size: 12px;
        }

        .nacp-state {
            top: 72.0%;
            left: 30%;
            width: 28%;
            font-size: 12px;
        }

        .nacp-lga {
            top: 71.7%;
            left: 62%;
            width: 28%;
            font-size: 12px;
        }

        /* ---- PAYMENT ---- */

        .nacp-amount-paid {
            top: 77.1%;
            left: 30%;
            width: 40%;
            font-size: 12px;
            font-weight: bold;
        }

        .nacp-amount-in-figure {
            top: 78.91%;
            left: 30%;
            width: 60%;
            font-size: 12px;
        }

        .nacp-payment-date {
            top: 80.3%;
            left: 30%;
            width: 30%;
            font-size: 12px;
        }

        .nacp-payment-time {
            top: 81.91%;
            left: 30%;
            width: 30%;
            font-size: 12px;
        }

        /* ---- FOOTER ---- */

        .nacp-tracking-code {
            top: 90.8%;
            left: 55%;
            width: 30%;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        /* -------- QR CODE -------- */
        .nacp-certificate-qr {
            position: absolute;
            z-index: 20;
            left: 68%;
            top: 82%;
            width: 28mm;
            height: 28mm;
            display: block;
            text-align: center;
        }

        .nacp-certificate-qr img {
            display: block;
            width: 28mm;
            height: 28mm;
            margin: 0 auto;
            border: 0;
        }

        /* -------- ACTION BUTTONS -------- */
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


        /* -------- PRINT -------- */
        @media print {

            @page { size: A4 portrait; margin: 0; }

            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            .nacp-certificate-wrapper {
                width: 210mm !important;
                height: 297mm !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .nacp-certificate {
                position: relative !important;
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                transform: none !important;
                page-break-inside: avoid !important;
            }

            .nacp-certificate-background {
                position: absolute !important;
                z-index: 1 !important;
                top: 0 !important;
                left: 0 !important;
                width: 210mm !important;
                height: 297mm !important;
            }

            .nacp-certificate-field { position: absolute !important; z-index: 10 !important; }
            .nacp-certificate-qr   { position: absolute !important; z-index: 20 !important; }

            .document-actions { display: none !important; }
        }


        /* -------- PDF MODE (DOMPDF) -------- */
        @page { size: A4 portrait; margin: 0; }

        html.pdf-document,
        body.pdf-document {
            width: 210mm !important;
            height: 297mm !important;
            min-width: 210mm !important;
            max-width: 210mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            overflow: hidden !important;
        }

        body.pdf-document { position: relative !important; }

        body.pdf-document .nacp-certificate-wrapper,
        body.pdf-document .nacp-certificate {
            position: relative !important;
            width: 210mm !important;
            height: 297mm !important;
            min-width: 210mm !important;
            max-width: 210mm !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
        }

        body.pdf-document .nacp-certificate-background {
            position: absolute !important;
            z-index: 1 !important;
            top: 0 !important;
            left: 0 !important;
            display: block !important;
            width: 210mm !important;
            height: 297mm !important;
        }

        body.pdf-document .nacp-certificate-field { position: absolute !important; z-index: 10 !important; }
        body.pdf-document .nacp-certificate-qr   { position: absolute !important; z-index: 20 !important; }

        body.pdf-document .document-actions { display: none !important; }

    </style>
</head>


<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">

<div class="nacp-certificate-wrapper">

    <div class="nacp-certificate">

        {{-- ==========================================================
             STATIC CLIENT ARTWORK (unchanged master file)
        =========================================================== --}}

        @if ($isPdfMode && !empty($certificateBackground))

            <img
                src="{{ $certificateBackground }}"
                class="nacp-certificate-background"
                alt="NACPDEAN Afforestation Compliance Payment Receipt"
            >

        @else

            <img
                src="{{ $documentArtwork }}"
                class="nacp-certificate-background"
                alt="NACPDEAN Afforestation Compliance Payment Receipt"
            >

        @endif


        {{-- ==========================================================
             RECEIPT NUMBER
        =========================================================== --}}

        {{--  @if ($receiptNo)
            <div class="nacp-certificate-field nacp-receipt-no">
                {{ $receiptNo }}
            </div>
        @endif  --}}


        {{-- ==========================================================
             MEMBER NAME
        =========================================================== --}}

        @if ($memberName)
            <div class="nacp-certificate-field nacp-member-name">
                {{ $memberName }}
            </div>
        @endif


        {{-- ==========================================================
             MEMBERSHIP NUMBER
        =========================================================== --}}

        @if ($membershipNo)
            <div class="nacp-certificate-field nacp-membership-no">
                {{ $membershipNo }}
            </div>
        @endif


        {{-- ==========================================================
             LIFTING RIGHT NUMBER
        =========================================================== --}}

        @if ($liftingRightNo)
            <div class="nacp-certificate-field nacp-lifting-right-no">
                {{ $liftingRightNo }}
            </div>
        @endif


        {{-- ==========================================================
             PHONE
        =========================================================== --}}

        @if ($phone)
            <div class="nacp-certificate-field nacp-phone">
                {{ $phone }}
            </div>
        @endif


        {{-- ==========================================================
             CONTAINER NUMBER
        =========================================================== --}}

        @if ($containerNumber)
            <div class="nacp-certificate-field nacp-container-number">
                {{ $containerNumber }}
            </div>
        @endif


        {{-- ==========================================================
             TRUCK NUMBER
        =========================================================== --}}

        @if ($truckNumber)
            <div class="nacp-certificate-field nacp-truck-number">
                {{ $truckNumber }}
            </div>
        @endif


        {{-- ==========================================================
             DEALER / SUPPLIER NAME
        =========================================================== --}}

        @if ($dealerSupplierName)
            <div class="nacp-certificate-field nacp-dealer-name">
                {{ $dealerSupplierName }}
            </div>
        @endif


        {{-- ==========================================================
             SUPPLIER MEMBERSHIP NUMBER
        =========================================================== --}}

        @if ($supplierMembershipNo)
            <div class="nacp-certificate-field nacp-supplier-membership-no">
                {{ $supplierMembershipNo }}
            </div>
        @endif


        {{-- ==========================================================
             DEALING RIGHT NUMBER
        =========================================================== --}}

        @if ($dealingRightNo)
            <div class="nacp-certificate-field nacp-dealing-right-no">
                {{ $dealingRightNo }}
            </div>
        @endif


        {{-- ==========================================================
             LOADING POINT
        =========================================================== --}}

        @if ($loadingPoint)
            <div class="nacp-certificate-field nacp-loading-point">
                {{ $loadingPoint }}
            </div>
        @endif


        {{-- ==========================================================
             STATE
        =========================================================== --}}

        @if ($state)
            <div class="nacp-certificate-field nacp-state">
                {{ $state }}
            </div>
        @endif


        {{-- ==========================================================
             LGA
        =========================================================== --}}

        @if ($lga)
            <div class="nacp-certificate-field nacp-lga">
                {{ $lga }}
            </div>
        @endif


        {{-- ==========================================================
             AMOUNT PAID
        =========================================================== --}}

        @if ($amountPaid)
            <div class="nacp-certificate-field nacp-amount-paid">
                {{ $amountPaid }}
            </div>
        @endif


        {{-- ==========================================================
             AMOUNT IN FIGURE
        =========================================================== --}}

        @if ($amountInFigure)
            <div class="nacp-certificate-field nacp-amount-in-figure">
                {{ $amountInFigure }}
            </div>
        @endif


        {{-- ==========================================================
             PAYMENT DATE
        =========================================================== --}}

        @if ($paymentDate)
            <div class="nacp-certificate-field nacp-payment-date">
                {{ $paymentDate }}
            </div>
        @endif


        {{-- ==========================================================
             PAYMENT TIME
        =========================================================== --}}

        @if ($paymentTime)
            <div class="nacp-certificate-field nacp-payment-time">
                {{ $paymentTime }}
            </div>
        @endif


        {{-- ==========================================================
             TRACKING CODE
        =========================================================== --}}

        {{--  @if ($trackingCode)
            <div class="nacp-certificate-field nacp-tracking-code">
                {{ $trackingCode }}
            </div>
        @endif  --}}


        {{-- ==========================================================
             QR CODE
        =========================================================== --}}

        @if (!empty($documentQrCode))
            <div class="nacp-certificate-qr">
                <img
                    src="{{ $documentQrCode }}"
                    alt="Document verification QR code"
                >
            </div>
        @endif

    </div>

</div>


{{-- ================================================================
     ACTION BUTTONS
================================================================= --}}

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
