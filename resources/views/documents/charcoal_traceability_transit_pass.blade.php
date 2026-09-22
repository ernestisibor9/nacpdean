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
    | TRANSIT NUMBER
    |--------------------------------------------------------------------------
    */

    $transitNo = $fields->get(
        'transit_no',
        $generatedDocument->document_number
    );


    /*
    |--------------------------------------------------------------------------
    | SELLER DETAILS
    |--------------------------------------------------------------------------
    */

    $sellerName           = $fields->get('seller_name', '');
    $sellerMembershipNo   = $fields->get('seller_membership_no', '');
    $sellerDealingRightNo = $fields->get('seller_dealing_right_no', '');
    $communityVillage     = $fields->get('community_village', '');
    $lga                  = $fields->get('lga', '');
    $state                = $fields->get('state', '');
    $phone                = $fields->get('phone', '');


    /*
    |--------------------------------------------------------------------------
    | MOVEMENT DETAILS
    |--------------------------------------------------------------------------
    */

    $driverName    = $fields->get('driver_name', '');
    $driverNumber  = $fields->get('driver_number', '');
    $destination   = $fields->get('destination', '');
    $vehicleNumber = $fields->get('vehicle_number', '');


    /*
    |--------------------------------------------------------------------------
    | BUYER DETAILS
    |--------------------------------------------------------------------------
    */

    $buyerName     = $fields->get('buyer_name', '');
    $buyerCategory = $fields->get('buyer_category', '');
    $buyerRightNo  = $fields->get('buyer_right_no', '');
    $buyerPhone    = $fields->get('buyer_phone', '');


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
        'images/documents/traceability-transit-pass.jpg'
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
    <title>{{ $transitNo }}</title>
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

        /* ------------------------------------------------------------
           STRIP ANY WHITE BACKGROUND FROM EVERY INNER ELEMENT
           ------------------------------------------------------------
           Forces every child of the receipt to be transparent so
           nothing can paint a white box over the client's artwork.
        ------------------------------------------------------------ */
        .nacp-certificate *:not(.nacp-certificate-background) {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        /* ------------------------------------------------------------
           DYNAMIC FIELDS
        ------------------------------------------------------------ */
        .nacp-certificate-field {
            position: absolute;
            z-index: 10;
            background: transparent !important;
            border: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
        }

        /* ------------------------------------------------------------
           FIELD POSITIONS
        ------------------------------------------------------------ */

        /* ---- TRANSIT NUMBER ---- */

        .nacp-transit-no {
            top: 7.0%;
            left: 6.55%;
            width: 21%;
            font-size: 11px;
            font-weight: bold;
        }

        /* ---- SELLER DETAILS ---- */

        .nacp-seller-name {
            top: 37.8%;
            left: 34.8%;
            width: 56.8%;
            font-size: 11px;
            font-weight: bold;
        }

        .nacp-seller-membership-no {
            top: 40.1%;
            left: 34.55%;
            width: 59.3%;
            font-size: 11px;
        }

        .nacp-seller-dealing-right-no {
            top: 42.4%;
            left: 27.25%;
            width: 59.3%;
            font-size: 11px;
        }

        .nacp-community-village {
            top: 44.75%;
            left: 34.2%;
            width: 55%;
            font-size: 11px;
        }

        .nacp-lga {
            top: 47.15%;
            left: 34.15%;
            width: 64.1%;
            font-size: 11px;
        }

        .nacp-state {
            top: 49.65%;
            left: 34.15%;
            width: 64.1%;
            font-size: 11px;
        }

        .nacp-phone {
            top: 51.97%;
            left: 34.15%;
            width: 64.1%;
            font-size: 11px;
        }

        /* ---- MOVEMENT DETAILS ---- */

        .nacp-driver-name {
            top: 59.93%;
            left: 22.0%;
            width: 26.5%;
            font-size: 11px;
        }

        .nacp-driver-number {
            top: 62.67%;
            left: 21.57%;
            width: 26.9%;
            font-size: 11px;
        }

        .nacp-destination {
            top: 65.0%;
            left: 21.9%;
            width: 26.9%;
            font-size: 11px;
        }

        .nacp-vehicle-number {
            top: 67.98%;
            left: 36.95%;
            width: 15.2%;
            font-size: 11px;
        }

        /* ---- BUYER DETAILS ---- */

        .nacp-buyer-name {
            top: 60.3%;
            left: 58.25%;
            width: 32.2%;
            font-size: 11px;
            font-weight: bold;
        }

        .nacp-buyer-category {
            top: 62.65%;
            left: 73.0%;
            width: 19.5%;
            font-size: 11px;
        }

        .nacp-buyer-right-no {
            top: 65.15%;
            left: 71.8%;
            width: 22.6%;
            font-size: 11px;
        }

        .nacp-buyer-phone {
            top: 67.25%;
            left: 58.25%;
            width: 32.2%;
            font-size: 11px;
        }

        /* ---- FOOTER ---- */

        .nacp-tracking-code {
            top: 85.4%;
            left: 38.0%;
            width: 18.05%;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        /* -------- QR CODE -------- */
        .nacp-certificate-qr {
            position: absolute;
            z-index: 20;
            left: 28.2%;
            top: 79.4%;
            width: 20mm;
            height: 20mm;
            display: block;
            text-align: center;
            background: transparent !important;
        }

        .nacp-certificate-qr img {
            display: block;
            width: 20mm;
            height: 20mm;
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

            .nacp-certificate *:not(.nacp-certificate-background) {
                background: transparent !important;
                background-color: transparent !important;
                background-image: none !important;
            }

            .nacp-certificate-field {
                position: absolute !important;
                z-index: 10 !important;
                background: transparent !important;
            }

            .nacp-certificate-qr { position: absolute !important; z-index: 20 !important; }

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

        body.pdf-document .nacp-certificate *:not(.nacp-certificate-background) {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
        }

        body.pdf-document .nacp-certificate-field {
            position: absolute !important;
            z-index: 10 !important;
            background: transparent !important;
        }

        body.pdf-document .nacp-certificate-qr { position: absolute !important; z-index: 20 !important; }

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
                alt="NACPDEAN Charcoal Traceability Transit Pass"
            >

        @else

            <img
                src="{{ $documentArtwork }}"
                class="nacp-certificate-background"
                alt="NACPDEAN Charcoal Traceability Transit Pass"
            >

        @endif


        {{-- ==========================================================
             TRANSIT NUMBER
        =========================================================== --}}

        @if ($transitNo)
            <div class="nacp-certificate-field nacp-transit-no">
                {{ $transitNo }}
            </div>
        @endif


        {{-- ==========================================================
             SELLER NAME
        =========================================================== --}}

        @if ($sellerName)
            <div class="nacp-certificate-field nacp-seller-name">
                {{ $sellerName }}
            </div>
        @endif


        {{-- ==========================================================
             SELLER MEMBERSHIP NUMBER
        =========================================================== --}}

        @if ($sellerMembershipNo)
            <div class="nacp-certificate-field nacp-seller-membership-no">
                {{ $sellerMembershipNo }}
            </div>
        @endif


        {{-- ==========================================================
             SELLER DEALING RIGHT
        =========================================================== --}}

        @if ($sellerDealingRightNo)
            <div class="nacp-certificate-field nacp-seller-dealing-right-no">
                {{ $sellerDealingRightNo }}
            </div>
        @endif


        {{-- ==========================================================
             COMMUNITY / VILLAGE
        =========================================================== --}}

        @if ($communityVillage)
            <div class="nacp-certificate-field nacp-community-village">
                {{ $communityVillage }}
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
             STATE
        =========================================================== --}}

        @if ($state)
            <div class="nacp-certificate-field nacp-state">
                {{ $state }}
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
             DRIVER NAME
        =========================================================== --}}

        @if ($driverName)
            <div class="nacp-certificate-field nacp-driver-name">
                {{ $driverName }}
            </div>
        @endif


        {{-- ==========================================================
             DRIVER NUMBER
        =========================================================== --}}

        @if ($driverNumber)
            <div class="nacp-certificate-field nacp-driver-number">
                {{ $driverNumber }}
            </div>
        @endif


        {{-- ==========================================================
             DESTINATION
        =========================================================== --}}

        @if ($destination)
            <div class="nacp-certificate-field nacp-destination">
                {{ $destination }}
            </div>
        @endif


        {{-- ==========================================================
             VEHICLE / TRUCK / TRAILER / CONTAINER
        =========================================================== --}}

        @if ($vehicleNumber)
            <div class="nacp-certificate-field nacp-vehicle-number">
                {{ $vehicleNumber }}
            </div>
        @endif


        {{-- ==========================================================
             BUYER NAME
        =========================================================== --}}

        @if ($buyerName)
            <div class="nacp-certificate-field nacp-buyer-name">
                {{ $buyerName }}
            </div>
        @endif


        {{-- ==========================================================
             BUYER CATEGORY
        =========================================================== --}}

        @if ($buyerCategory)
            <div class="nacp-certificate-field nacp-buyer-category">
                {{ $buyerCategory }}
            </div>
        @endif


        {{-- ==========================================================
             BUYER RIGHT NUMBER
        =========================================================== --}}

        @if ($buyerRightNo)
            <div class="nacp-certificate-field nacp-buyer-right-no">
                {{ $buyerRightNo }}
            </div>
        @endif


        {{-- ==========================================================
             BUYER PHONE
        =========================================================== --}}

        @if ($buyerPhone)
            <div class="nacp-certificate-field nacp-buyer-phone">
                {{ $buyerPhone }}
            </div>
        @endif


        {{-- ==========================================================
             TRACKING CODE
        =========================================================== --}}

        @if ($trackingCode)
            <div class="nacp-certificate-field nacp-tracking-code">
                {{ $trackingCode }}
            </div>
        @endif


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


@if (empty($printMode))

    <div class="document-actions">

        <button type="button" onclick="window.print()">
            🖨 Print Transit Pass
        </button>

        <a href="{{ route('member.documents.download', $generatedDocument) }}">
            ⬇ Download Transit Pass
        </a>

    </div>

@endif

</body>
</html>
