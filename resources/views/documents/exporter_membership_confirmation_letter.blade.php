@php
    /*
    |--------------------------------------------------------------------------
    | LOAD GENERATED DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    $fields = collect(
        $generatedDocument->field_values ?? []
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD USER
    |--------------------------------------------------------------------------
    */

    $documentUser = $generatedDocument->user ?? null;


    /*
    |--------------------------------------------------------------------------
    | LOAD MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    $memberProfile = null;

    if ($documentUser) {

        $memberProfile = $documentUser->profile ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | FULL MEMBER NAME
    |--------------------------------------------------------------------------
    */

    $memberName = trim(
        (string) $fields->get(
            'member_name',
            ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO PROFILE FULL NAME
    |--------------------------------------------------------------------------
    */

    if (!$memberName && $memberProfile) {

        $memberName = trim(
            (string) (
                $memberProfile->full_name
                ?? ''
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD FULL NAME FROM PROFILE NAMES
    |--------------------------------------------------------------------------
    */

    if (!$memberName && $memberProfile) {

        $memberName = trim(
            implode(
                ' ',
                array_filter([
                    $memberProfile->first_name ?? null,
                    $memberProfile->middle_name ?? null,
                    $memberProfile->last_name ?? null,
                ])
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FINAL FALLBACK TO USER NAME
    |--------------------------------------------------------------------------
    */

    if (!$memberName && $documentUser) {

        $memberName = trim(
            (string) (
                $documentUser->name
                ?? ''
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REPRESENTATIVE NAME
    |--------------------------------------------------------------------------
    */

    $representativeName = $fields->get(
        'representative_name',
        $fields->get(
            'contact_name',
            ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | FALLBACK REPRESENTATIVE
    |--------------------------------------------------------------------------
    */

    if (!$representativeName && $memberProfile) {

        $representativeName = trim(
            (string) (
                $memberProfile->representative_name
                ?? $memberProfile->contact_name
                ?? ''
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP NUMBER
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


    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO ACTUAL MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    $membership = null;

    if ($documentUser) {

        $membership = \App\Models\Membership::query()
            ->where(
                'user_id',
                $documentUser->id
            )
            ->latest('id')
            ->first();


        if (
            !$membershipNumber &&
            $membership
        ) {

            $membershipNumber =
                $membership->membership_number;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ISSUED DATE
    |--------------------------------------------------------------------------
    */

    $issuedDate = $fields->get(
        'issued_at',
        ''
    );


    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO MEMBERSHIP ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if (
        !$issuedDate &&
        $membership &&
        $membership->issued_at
    ) {

        $issuedDate =
            $membership->issued_at;
    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK TO GENERATED DOCUMENT ISSUED DATE
    |--------------------------------------------------------------------------
    */

    if (
        !$issuedDate &&
        $generatedDocument->issued_at
    ) {

        $issuedDate =
            $generatedDocument->issued_at;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT ISSUE DATE
    |--------------------------------------------------------------------------
    */

    $issuedDateFormatted = '';

    if ($issuedDate) {

        try {

            $issuedDateFormatted =
                \Carbon\Carbon::parse(
                    $issuedDate
                )->format('jS F Y');

        } catch (\Throwable $e) {

            $issuedDateFormatted =
                (string) $issuedDate;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REFERENCE NUMBER
    |--------------------------------------------------------------------------
    */

    $refNo = $fields->get(
        'ref_no',
        $fields->get(
            'reference_number',
            $generatedDocument->document_number ?? ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | PDF MODE
    |--------------------------------------------------------------------------
    */

    $isPdfMode = !empty($downloadMode);


    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION LETTER BACKGROUND
    |--------------------------------------------------------------------------
    |
    | This confirmation letter uses ONE static client artwork image.
    |
    | File:
    |
    | public/images/certificates/exporter-confirmation-letter.jpg
    |
    */

    $confirmationBackground = null;

    $confirmationPath = public_path(
        'images/certificates/exporter-confirmation-letter.jpg'
    );

    if (file_exists($confirmationPath)) {

        $confirmationBackground =
            'data:image/jpeg;base64,' .
            base64_encode(
                file_get_contents($confirmationPath)
            );
    }

@endphp


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Membership Confirmation Letter - NACPDEAN
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | BASIC RESET
        |--------------------------------------------------------------------------
        */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;

            padding: 0;
        }


        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #000000;

            background: #f5f5f5;
        }


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT WRAPPER
        |--------------------------------------------------------------------------
        */

        .confirmation-document-wrapper {

            width: 100%;

            padding:
                30px
                0
                50px
                0;
        }


        /*
        |--------------------------------------------------------------------------
        | SINGLE A4 CONFIRMATION LETTER
        |--------------------------------------------------------------------------
        */

        .confirmation-page {

            position: relative;

            width: 210mm;

            height: 297mm;

            margin:
                0
                auto
                25px
                auto;

            padding: 0;

            overflow: hidden;

            background: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | STATIC CLIENT ARTWORK
        |--------------------------------------------------------------------------
        */

        .confirmation-background {

            position: absolute;

            z-index: 1;

            top: 0;

            left: 0;

            display: block;

            width: 210mm;

            height: 297mm;

            min-width: 210mm;

            min-height: 297mm;

            max-width: 210mm;

            max-height: 297mm;

            margin: 0;

            padding: 0;

            border: 0;

            object-fit: fill;
        }


        /*
        |--------------------------------------------------------------------------
        | DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        |
        | Coordinates and fonts below were measured directly from the
        | reference letter PDF's embedded text (real vector text, word
        | -level bounding boxes extracted via pdftotext -bbox). Percentages
        | are relative to the confirmation-page (210mm x 297mm), so they
        | scale correctly at any render size.
        |
        | Reference letter body copy uses "Bookman Old Style" throughout
        | (regular for values, bold for labels/headings, bold red for
        | the membership number). If that font isn't installed on your
        | PDF renderer (dompdf, wkhtmltopdf, etc.), install it or swap
        | in the closest match — Georgia is the nearest common fallback.
        |
        | Every field below carries white-space: nowrap + ellipsis so a
        | long value truncates cleanly instead of wrapping onto a second
        | line and overlapping whatever sits below it on the artwork.
        |--------------------------------------------------------------------------
        */

        .confirmation-field {

            position: absolute;

            z-index: 10;

            color: #000000;

            font-family:
                "Bookman Old Style",
                "URW Bookman",
                Georgia,
                serif;

            line-height: 1.2;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        /*
        |--------------------------------------------------------------------------
        | ADDRESSEE COMPANY NAME
        |--------------------------------------------------------------------------
        |
        | The line right under "The Managing Director" at the top of the
        | letter. Measured value box: left 12.9%–41.0%, top 22.9%–24.7%
        | (regular weight, black)
        |
        */

        .confirmation-addressee-name {

            top: 22.6%;

            left: 12.9%;

            width: 40%;

            font-size: 17px;

            font-weight: normal;

            text-transform: uppercase;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        |
        | Measured value box: left 19.2%–35.5%, top 16.0%–17.7%
        | (regular weight, black — the "Date:" label itself is static
        | artwork, so this only needs to hold e.g. "25th June 2026")
        |
        */

        .confirmation-date {

            top: 15.9%;

            left: 19.2%;

            width: 30%;

            font-size: 17px;

            font-weight: normal;

            text-align: left;

        }


        /*
        |--------------------------------------------------------------------------
        | REFERENCE NUMBER
        |--------------------------------------------------------------------------
        |
        | Measured value box: left 17.9%–52.2%, top 17.7%–19.5%
        | (bold weight, black — "Ref:" label is static artwork).
        |
        | NOTE: on a previous version of this letter, this field was
        | rendering a long auto-generated value that wrapped onto two
        | lines and overlapped "The Managing Director" line below it.
        | If your $refNo can be long, keep an eye on this — the
        | nowrap+ellipsis above will now truncate it instead of
        | overlapping, but a truncated reference number may not be
        | what you want either. If reference numbers are meant to be
        | short/fixed-format, this is fine as-is.
        |
        */

        .confirmation-reference {

            top: 17.3%;

            left: 17.9%;

            width: 45%;

            font-size: 17px;

            font-weight: bold;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER / COMPANY NAME (WELCOME PARAGRAPH)
        |--------------------------------------------------------------------------
        |
        | The name inserted into "...we are delighted to welcome
        | ______ as a valued member...". Measured against the
        | reference PDF, it sits on its own line at top 41.5%–43.2%
        | (regular weight, black).
        |
        | NOTE: a live screenshot of this letter previously showed
        | this landing on the SAME line as "as a valued member..."
        | rather than its own line above it — meaning the deployed
        | background image has tighter line spacing here than the
        | reference PDF this coordinate was measured from. Nudged up
        | slightly (was 41.1%) as a starting correction; adjust in
        | small steps against your actual artwork if it's still off.
        |
        */

        .confirmation-welcome-name {

            top: 42.0%;

            left: 12.1%;

            width: 40%;

            font-size: 17px;

            font-weight: normal;

            text-transform: uppercase;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBER / COMPANY NAME (BULLET LIST)
        |--------------------------------------------------------------------------
        |
        | Measured value box: left 41.2%–69.3%, top 60.7%–62.5%
        | (regular weight, NOT bold, black)
        |
        */

        .confirmation-member-name {

            top: 61.1%;

            left: 41.2%;

            width: 40%;

            font-size: 17px;

            font-weight: normal;

            text-transform: uppercase;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | REPRESENTATIVE NAME
        |--------------------------------------------------------------------------
        |
        | Measured value box: left 51.6%–76.0%, top 62.6%–64.3%
        | (regular weight, black)
        |
        */

        .confirmation-representative {

            top: 62.2%;

            left: 51.6%;

            width: 40%;

            font-size: 17px;

            font-weight: normal;

            text-align: left;
        }


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIP NUMBER
        |--------------------------------------------------------------------------
        |
        | Measured value box: left 43.6%–64.0%, top 64.6%–66.3%
        | (bold weight, pure red #FF0000 — not #C00000)
        |
        */

        .confirmation-membership-number {

            top: 65.0%;

            left: 43.6%;

            width: 35%;

            font-size: 17px;

            font-weight: bolder;

            color: #FF0000;

            text-align: left;

            font-family: 'tahoma'
        }


        /*
        |--------------------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------------------
        */

        .document-actions {

            width: 100%;

            margin:
                20px
                auto;

            text-align: center;
        }


        .document-actions a,
        .document-actions button {

            display: inline-block;

            padding:
                10px
                18px;

            margin:
                0
                5px;

            border: none;

            border-radius: 5px;

            background: #10a653;

            color: #ffffff;

            text-decoration: none;

            cursor: pointer;

            font-size: 14px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }


        .document-actions a:hover,
        .document-actions button:hover {

            opacity: .9;
        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 768px) {

            .confirmation-document-wrapper {

                padding: 10px;
            }


            .confirmation-page {

                width: 100%;

                height: auto;

                aspect-ratio: 210 / 297;

                margin-bottom: 15px;
            }


            .confirmation-background {

                width: 100%;

                height: 100%;

                min-width: 100%;

                min-height: 100%;

                max-width: 100%;

                max-height: 100%;
            }


            .confirmation-date,
            .confirmation-reference {

                font-size: 11px;
            }


            .confirmation-addressee-name,
            .confirmation-welcome-name,
            .confirmation-member-name {

                font-size: 11px;
            }


            .confirmation-representative {

                font-size: 11px;
            }


            .confirmation-membership-number {

                font-size: 10px;
            }
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


            html,
            body {

                width: 210mm !important;

                height: 297mm !important;

                margin: 0 !important;

                padding: 0 !important;

                background: #ffffff !important;
            }


            .confirmation-document-wrapper {

                width: 210mm !important;

                height: 297mm !important;

                margin: 0 !important;

                padding: 0 !important;
            }


            .confirmation-page {

                position: relative !important;

                width: 210mm !important;

                height: 297mm !important;

                margin: 0 !important;

                padding: 0 !important;

                overflow: hidden !important;

                page-break-before: avoid !important;

                page-break-inside: avoid !important;

                page-break-after: avoid !important;
            }


            .confirmation-background {

                position: absolute !important;

                top: 0 !important;

                left: 0 !important;

                width: 210mm !important;

                height: 297mm !important;

                min-width: 210mm !important;

                min-height: 297mm !important;

                max-width: 210mm !important;

                max-height: 297mm !important;
            }


            .document-actions {

                display: none !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DOMPDF PDF MODE
        |--------------------------------------------------------------------------
        |
        | ONE A4 PORTRAIT PAGE ONLY.
        |
        */

        @page {

            size: A4 portrait;

            margin: 0;
        }


        html.pdf-document,
        body.pdf-document {

            width: 210mm !important;

            height: 297mm !important;

            min-width: 210mm !important;

            max-width: 210mm !important;

            margin: 0 !important;

            padding: 0 !important;

            background: #ffffff !important;
        }


        body.pdf-document {

            overflow: hidden !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF DOCUMENT WRAPPER
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .confirmation-document-wrapper {

            width: 210mm !important;

            height: 297mm !important;

            margin: 0 !important;

            padding: 0 !important;

            overflow: hidden !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF SINGLE PAGE
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .confirmation-page {

            position: relative !important;

            width: 210mm !important;

            height: 297mm !important;

            min-width: 210mm !important;

            min-height: 297mm !important;

            max-width: 210mm !important;

            max-height: 297mm !important;

            margin: 0 !important;

            padding: 0 !important;

            overflow: hidden !important;

            background: #ffffff !important;

            page-break-before: avoid !important;

            page-break-inside: avoid !important;

            page-break-after: avoid !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF BACKGROUND
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .confirmation-background {

            position: absolute !important;

            z-index: 1 !important;

            top: 0 !important;

            left: 0 !important;

            display: block !important;

            width: 210mm !important;

            height: 297mm !important;

            min-width: 210mm !important;

            min-height: 297mm !important;

            max-width: 210mm !important;

            max-height: 297mm !important;

            margin: 0 !important;

            padding: 0 !important;

            border: 0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | PDF DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .confirmation-field {

            position: absolute !important;

            z-index: 10 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | HIDE BUTTONS IN PDF
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .document-actions {

            display: none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | FINAL PAGE PROTECTION
        |--------------------------------------------------------------------------
        */

        body.pdf-document
        .confirmation-document-wrapper,
        body.pdf-document
        .confirmation-page,
        body.pdf-document
        .confirmation-background {

            page-break-before: avoid !important;

            page-break-inside: avoid !important;

            page-break-after: avoid !important;
        }


    </style>

</head>


<body class="{{ $isPdfMode ? 'pdf-document' : '' }}">


    {{-- ================================================================
         DOCUMENT WRAPPER
    ================================================================= --}}

    <div class="confirmation-document-wrapper">


        {{-- ==========================================================
             SINGLE CONFIRMATION LETTER
        =========================================================== --}}

        <div class="confirmation-page">


            {{-- ======================================================
                 STATIC CLIENT ARTWORK
            ======================================================= --}}

            @if ($isPdfMode && !empty($confirmationBackground))

                <img
                    src="{{ $confirmationBackground }}"
                    class="confirmation-background"
                    alt="NACPDEAN Confirmation Letter"
                >

            @else

                <img
                    src="{{ asset('images/certificates/exporter-confirmation-letter.jpg') }}"
                    class="confirmation-background"
                    alt="NACPDEAN Confirmation Letter"
                >

            @endif


            {{-- ======================================================
                 DYNAMIC DATE
            ======================================================= --}}

            @if ($issuedDateFormatted)

                <div class="confirmation-field confirmation-date">

                    {{ $issuedDateFormatted }}

                </div>

            @endif


            {{-- ======================================================
                 DYNAMIC REFERENCE NUMBER
            ======================================================= --}}

            {{--  @if ($refNo)

                <div class="confirmation-field confirmation-reference">

                    {{ $refNo }}

                </div>

            @endif  --}}


            {{-- ======================================================
                 DYNAMIC ADDRESSEE NAME
                 (the line under "The Managing Director")
            ======================================================= --}}

            {{--  @if ($memberName)

                <div class="confirmation-field confirmation-addressee-name">

                    {{ $memberName }}

                </div>

            @endif  --}}


            {{-- ======================================================
                 DYNAMIC MEMBER / COMPANY NAME
                 (the "...delighted to welcome ______ as a valued
                 member..." paragraph)
            ======================================================= --}}

            @if ($memberName)

                <div class="confirmation-field confirmation-welcome-name">

                    {{ $memberName }}

                </div>

            @endif


            {{-- ======================================================
                 DYNAMIC MEMBER / COMPANY NAME (BULLET LIST)
            ======================================================= --}}

            @if ($memberName)

                <div class="confirmation-field confirmation-member-name">

                    {{ $memberName }}

                </div>

            @endif


            {{-- ======================================================
                 DYNAMIC REPRESENTATIVE NAME
            ======================================================= --}}

            @if ($representativeName)

                <div class="confirmation-field confirmation-representative">

                    {{ $representativeName }}

                </div>

            @endif


            {{-- ======================================================
                 DYNAMIC MEMBERSHIP NUMBER
            ======================================================= --}}

            @if ($membershipNumber)

                <div class="confirmation-field confirmation-membership-number">

                    {{ $membershipNumber }}

                </div>

            @endif


        </div>


    </div>


    {{-- ================================================================
         ACTION BUTTONS
    ================================================================= --}}

    @if (!$printMode)

        <div class="document-actions">


            <button
                type="button"
                onclick="window.print()"
            >

                🖨 Print Confirmation Letter

            </button>


            <a
                href="{{ route(
                    'member.documents.download',
                    $generatedDocument
                ) }}"
            >

                ⬇ Download Confirmation Letter

            </a>


        </div>

    @endif


</body>

</html>
