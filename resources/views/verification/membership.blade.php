<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Membership Verification | NACPDEAN
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
        }

        .verification-page {
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px 15px;
        }

        .verification-card {
            width: 100%;
            max-width: 650px;

            background: #fff;

            border-radius: 12px;

            box-shadow:
                0 10px 35px rgba(0, 0, 0, .12);

            overflow: hidden;
        }

        .verification-header {
            background: #00a651;

            color: #fff;

            text-align: center;

            padding: 25px 20px;
        }

        .verification-header img {
            width: 90px;
            height: 70px;

            object-fit: contain;

            margin-bottom: 10px;
        }

        .verification-header h2 {
            margin: 0;

            font-size: 22px;

            font-weight: 900;
        }

        .verification-header p {
            margin: 6px 0 0;

            font-size: 12px;

            opacity: .95;
        }

        .verification-body {
            padding: 30px;
        }

        .verification-status {
            text-align: center;

            margin-bottom: 25px;
        }

        .verification-status .icon {
            width: 70px;
            height: 70px;

            margin: 0 auto 12px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 32px;

            font-weight: 900;
        }

        .verification-status.valid .icon {
            background: #dff5e8;
            color: #00a651;
        }

        .verification-status.invalid .icon {
            background: #fde2e2;
            color: #dc3545;
        }

        .verification-status h3 {
            margin: 0;

            font-size: 22px;

            font-weight: 900;
        }

        .verification-status.valid h3 {
            color: #00a651;
        }

        .verification-status.invalid h3 {
            color: #dc3545;
        }

        .verification-status p {
            margin-top: 8px;

            color: #777;

            font-size: 13px;
        }

        .member-photo-wrapper {
            text-align: center;

            margin-bottom: 25px;
        }

        .member-photo {
            width: 130px;
            height: 160px;

            object-fit: cover;

            border-radius: 6px;

            border: 4px solid #fff;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, .18);
        }

        .member-photo-placeholder {
            width: 130px;
            height: 160px;

            margin: auto;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #eee;

            color: #999;

            font-size: 11px;

            font-weight: 700;
        }

        .member-name {
            text-align: center;

            margin-bottom: 25px;
        }

        .member-name h3 {
            margin: 0;

            font-size: 22px;

            font-weight: 900;

            text-transform: uppercase;
        }

        .member-name p {
            margin: 5px 0 0;

            color: #ed1c24;

            font-size: 12px;

            font-weight: 900;

            text-transform: uppercase;
        }

        .member-details {
            border: 1px solid #ddd;

            border-radius: 8px;

            overflow: hidden;

            margin-bottom: 25px;
        }

        .member-detail-row {
            display: flex;

            border-bottom: 1px solid #ddd;
        }

        .member-detail-row:last-child {
            border-bottom: none;
        }

        .member-detail-label {
            width: 40%;

            padding: 11px;

            background: #f5f5f5;

            color: #555;

            font-size: 11px;

            font-weight: 800;

            text-transform: uppercase;
        }

        .member-detail-value {
            width: 60%;

            padding: 11px;

            color: #111;

            font-size: 12px;

            font-weight: 700;

            word-break: break-word;
        }

        .verification-footer {
            text-align: center;

            border-top: 1px solid #ddd;

            padding-top: 20px;

            color: #777;

            font-size: 10px;

            line-height: 1.6;
        }

        .verification-footer strong {
            color: #00a651;
        }

        .verification-token {
            margin-top: 15px;

            padding: 10px;

            background: #f8f8f8;

            border-radius: 6px;

            font-size: 9px;

            color: #777;

            word-break: break-all;
        }

        @media (max-width: 576px) {

            .verification-page {
                padding: 15px 10px;
            }

            .verification-body {
                padding: 20px 15px;
            }

            .member-detail-row {
                display: block;
            }

            .member-detail-label,
            .member-detail-value {
                width: 100%;
            }

            .member-detail-label {
                padding-bottom: 5px;
            }

            .member-detail-value {
                padding-top: 5px;
            }

        }

        @media print {

            body {
                background: #fff;
            }

            .verification-page {
                padding: 0;
            }

            .verification-card {
                box-shadow: none;
                max-width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="verification-page">

    <div class="verification-card">


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="verification-header">

            <img
                src="{{ asset('frontend/assets/img/logo.png') }}"
                alt="NACPDEAN Logo"
            >

            <h2>
                NACPDEAN
            </h2>

            <p>
                National Association of Charcoal Producers,
                Dealers, Exporters and Afforestation of Nigeria
            </p>

        </div>


        <div class="verification-body">


            @if ($card)


                @php

                    $profile = $card->profile;

                    $membership = $card->membership;

                    $category = $card->category;

                    $fullName = strtoupper(
                        trim(
                            ($profile?->surname ?? '') . ' ' .
                            ($profile?->first_name ?? '') . ' ' .
                            ($profile?->middle_name ?? '')
                        )
                    );

                    $categoryName = $category?->name ?? 'NACPDEAN MEMBER';

                    /*
                     |--------------------------------------------------------------------------
                     | Determine whether membership is currently valid
                     |--------------------------------------------------------------------------
                     */

                    $isActive = true;

                    if (
                        isset($membership) &&
                        isset($membership->status)
                    ) {
                        $isActive =
                            strtolower($membership->status) === 'active';
                    }

                    if (
                        isset($card->expires_at) &&
                        $card->expires_at->isPast()
                    ) {
                        $isActive = false;
                    }

                @endphp


                {{-- =================================================
                     VALID / INVALID STATUS
                ================================================== --}}

                @if ($isActive)

                    <div class="verification-status valid">

                        <div class="icon">
                            ✓
                        </div>

                        <h3>
                            MEMBERSHIP VERIFIED
                        </h3>

                        <p>
                            This membership card is valid and
                            registered in the NACPDEAN database.
                        </p>

                    </div>

                @else

                    <div class="verification-status invalid">

                        <div class="icon">
                            !
                        </div>

                        <h3>
                            MEMBERSHIP EXPIRED
                        </h3>

                        <p>
                            This membership card is no longer
                            valid according to the NACPDEAN database.
                        </p>

                    </div>

                @endif


                {{-- =================================================
                     MEMBER PHOTO
                ================================================== --}}

                <div class="member-photo-wrapper">

                    @if ($profile?->photo)

                        <img
                            src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                            alt="{{ $fullName }}"
                            class="member-photo"
                        >

                    @else

                        <div class="member-photo-placeholder">

                            MEMBER PHOTO

                        </div>

                    @endif

                </div>


                {{-- =================================================
                     MEMBER NAME
                ================================================== --}}

                <div class="member-name">

                    <h3>
                        {{ $fullName ?: 'NACPDEAN MEMBER' }}
                    </h3>

                    <p>
                        {{ strtoupper($categoryName) }}
                    </p>

                </div>


                {{-- =================================================
                     MEMBER DETAILS
                ================================================== --}}

                <div class="member-details">


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Membership Number
                        </div>

                        <div class="member-detail-value">
                            {{ $card->membership_number ?? 'N/A' }}
                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Card Number
                        </div>

                        <div class="member-detail-value">
                            {{ $card->card_number ?? 'N/A' }}
                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Member
                        </div>

                        <div class="member-detail-value">
                            {{ $fullName ?: 'N/A' }}
                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Category
                        </div>

                        <div class="member-detail-value">
                            {{ $categoryName }}
                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            State
                        </div>

                        <div class="member-detail-value">
                            {{ $profile?->state ?? 'N/A' }}
                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Issue Date
                        </div>

                        <div class="member-detail-value">

                            {{ optional($card->issued_at)->format('d M Y') }}

                        </div>

                    </div>


                    <div class="member-detail-row">

                        <div class="member-detail-label">
                            Expiry Date
                        </div>

                        <div class="member-detail-value">

                            {{ optional($card->expires_at)->format('d M Y') }}

                        </div>

                    </div>


                </div>


                {{-- =================================================
                     VERIFICATION INFORMATION
                ================================================== --}}

                <div class="verification-footer">

                    <strong>
                        OFFICIAL NACPDEAN VERIFICATION
                    </strong>

                    <br>

                    This verification page confirms the membership
                    information associated with this QR code.

                    <br>

                    The information displayed is retrieved directly
                    from the NACPDEAN membership database.

                    <div class="verification-token">

                        Verification Token:

                        {{ $card->qr_token }}

                    </div>

                </div>


            @else


                {{-- =================================================
                     INVALID QR CODE
                ================================================== --}}

                <div class="verification-status invalid">

                    <div class="icon">
                        !
                    </div>

                    <h3>
                        MEMBERSHIP NOT FOUND
                    </h3>

                    <p>
                        The membership card could not be verified.
                    </p>

                </div>


                <div class="verification-footer">

                    <strong>
                        NACPDEAN MEMBERSHIP VERIFICATION
                    </strong>

                    <br>

                    The QR code you scanned does not correspond
                    to a valid membership card in our database.

                    <br>

                    Please contact NACPDEAN administration if you
                    believe this is an error.

                </div>


            @endif


        </div>

    </div>

</div>


</body>

</html>
