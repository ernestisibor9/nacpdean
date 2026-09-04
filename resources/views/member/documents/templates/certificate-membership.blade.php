<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $document->name ?? 'Certificate of Membership' }}
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="{{ asset('documents/certificate-membership/certificate-member.css') }}"
    >
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Generated Document Data
    |--------------------------------------------------------------------------
    */

    $values = $generatedDocument->field_values ?? [];

    $certificateNumber =
        $generatedDocument->document_number
        ?? 'N/A';

    $memberName =
        $values['member_name']
        ?? $values['company_name']
        ?? $generatedDocument->user?->name
        ?? 'N/A';

    $membershipNumber =
        $values['membership_number']
        ?? $values['membership_no']
        ?? 'N/A';

    $issuedDate =
        $generatedDocument->issued_at
            ? $generatedDocument->issued_at->format('F d, Y')
            : 'N/A';

    $trackingCode =
        $generatedDocument->tracking_code
        ?? '';

@endphp


<div class="certificate-page">

    <div class="certificate">

        <div class="certificate-inner">

            <div class="certificate-content">


                {{-- Certificate Number --}}
                <div class="certificate-number">

                    Cert No:
                    {{ $certificateNumber }}

                </div>


                {{-- Curved Heading --}}
                <svg
                    class="certificate-title"
                    viewBox="0 0 900 220"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-label="Certificate of Membership"
                >

                    <defs>

                        <path
                            id="heading-curve"
                            d="M 65 185 Q 450 -25 835 185"
                        />

                    </defs>

                    <text>

                        <textPath
                            href="#heading-curve"
                            startOffset="50%"
                            text-anchor="middle"
                        >
                            Certificate of Membership
                        </textPath>

                    </text>

                </svg>


                {{-- Subtitle --}}
                <div class="certificate-subtitle">

                    This is to certify that

                </div>


                {{-- Member / Company Name --}}
                <div class="company-name">

                    {{ $memberName }}

                </div>


                {{-- Registered Text --}}
                <div class="registered-text">

                    is a Registered Export Member with

                </div>


                {{-- Association --}}
                <div class="association-name">

                    NATIONAL ASSOCIATION OF CHARCOAL PRODUCERS, DEALERS,<br>

                    EXPORTERS AND AFFORESTATION OF NIGERIA

                </div>


                {{-- Membership Number --}}
                <div class="membership-number">

                    MEMBERSHIP NO.:
                    {{ $membershipNumber }}

                </div>


                {{-- Date --}}
                <div class="dated">

                    Dated this:
                    {{ $issuedDate }}

                </div>


                {{-- Association Logos --}}
                <img
                    src="{{ asset('documents/certificate-membership/images/logos1.png') }}"
                    class="certificate-logos"
                    alt="Association logos"
                >


                {{-- President --}}
                <div class="president-block">

                    <div class="sign-line"></div>

                    <h5>
                        Edu Babatunde
                    </h5>

                    <p>
                        National President
                    </p>

                </div>


                {{-- Official Seal --}}
                <img
                    src="{{ asset('documents/certificate-membership/images/seal.jpg') }}"
                    class="official-seal"
                    alt="Official seal"
                >


                {{-- QR CODE --}}
                <div class="verification">

                    {!! QrCode::size(110)->generate(
                        route('documents.verify', $trackingCode)
                    ) !!}

                </div>


                {{-- Secretary General --}}
                <div class="secretary-block">

                    <div class="sign-line"></div>

                    <h5>
                        Ojei Uche Joseph
                    </h5>

                    <p>
                        National Secretary-General
                    </p>

                </div>


            </div>

        </div>

    </div>

</div>

</body>

</html>
