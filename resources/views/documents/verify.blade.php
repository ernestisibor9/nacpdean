<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificate Verification</title>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 40px 20px;
        background: #f4f7f5;
        font-family: Arial, sans-serif;
        color: #222;
    }

    .verification-container {
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
    }

    .verification-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 40px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        position: relative;
    }

    /* TOP ACTIVE STATUS BADGE */
    .top-status-badge {
        display: inline-block;
        background-color: #e6f4ea;
        color: #10a653;
        border: 2px solid #10a653;
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 1.5px;
        padding: 8px 20px;
        border-radius: 30px;
        text-transform: uppercase;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(16, 166, 83, 0.15);
    }

    .verification-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .verification-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #10a653;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        font-weight: bold;
    }

    .verification-header h1 {
        margin: 0;
        color: #10a653;
        font-size: 30px;
    }

    .verification-header p {
        margin-top: 8px;
        color: #666;
        font-size: 15px;
    }

    .document-name {
        text-align: center;
        margin-bottom: 30px;
        font-size: 22px;
        font-weight: bold;
    }

    .details {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        overflow: hidden;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 15px 18px;
        border-bottom: 1px solid #eeeeee;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #777;
        font-size: 14px;
    }

    .detail-value {
        text-align: right;
        font-size: 15px;
        font-weight: bold;
        word-break: break-word;
    }

    .valid-status {
        color: #10a653;
    }

    .invalid-card {
        text-align: center;
    }

    .invalid-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #dc3545;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 35px;
        font-weight: bold;
    }

    .invalid-card h1 {
        margin: 0;
        color: #dc3545;
        font-size: 28px;
    }

    .invalid-card p {
        margin-top: 12px;
        color: #666;
    }

    .footer {
        margin-top: 25px;
        text-align: center;
        color: #777;
        font-size: 12px;
        line-height: 1.5;
    }

    @media (max-width: 600px) {
        body {
            padding: 20px 12px;
        }

        .verification-card {
            padding: 25px 18px;
        }

        .detail-row {
            display: block;
        }

        .detail-value {
            margin-top: 5px;
            text-align: left;
        }
    }
</style>
</head>

<body>

<div class="verification-container">

@if($valid && $generatedDocument)

    <div class="verification-card">

        <div class="verification-header">

            <!-- BOLD TOP STATUS BADGE -->
            <div class="top-status-badge">
                ● STATUS: ACTIVE
            </div>

            <div class="verification-icon">
                ✓
            </div>

            <h1>
                Certificate Verified
            </h1>

            <p>
                This document is authentic and currently valid.
            </p>

        </div>

        <div class="document-name">
            {{ $generatedDocument->document->name ?? 'Certificate' }}
        </div>

        <div class="details">

            <div class="detail-row">
                <div class="detail-label">
                    Certificate Number
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->document_number }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Tracking Code
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->tracking_code }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Member / Business
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->user->profile->business_name
                        ?? trim(
                            ($generatedDocument->user->profile->first_name ?? '') .
                            ' ' .
                            ($generatedDocument->user->profile->middle_name ?? '') .
                            ' ' .
                            ($generatedDocument->user->profile->surname ?? '')
                        )
                    }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Membership Number
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->user->membership?->membership_number
                        ?? $generatedDocument->user->profile->membership_number
                        ?? 'N/A'
                    }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Issue Date
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->issued_at
                        ? $generatedDocument->issued_at->format('F jS, Y')
                        : 'N/A'
                    }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Valid Until
                </div>
                <div class="detail-value">
                    {{ $generatedDocument->expires_at
                        ? $generatedDocument->expires_at->format('F jS, Y')
                        : 'N/A'
                    }}
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    Status
                </div>
                <div class="detail-value valid-status">
                    ✓ ACTIVE / VALID
                </div>
            </div>

        </div>

        <div class="footer">
            This certificate was verified using the NACPDEAN document verification system.
        </div>

    </div>

@else

    <div class="verification-card invalid-card">

        <div class="invalid-icon">
            !
        </div>

        <h1>
            Certificate Not Valid
        </h1>

        <p>
            The certificate could not be verified or is no longer valid.
        </p>

        @if($generatedDocument)

            <div class="details">

                <div class="detail-row">
                    <div class="detail-label">
                        Certificate Number
                    </div>
                    <div class="detail-value">
                        {{ $generatedDocument->document_number }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        Tracking Code
                    </div>
                    <div class="detail-value">
                        {{ $generatedDocument->tracking_code }}
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">
                        Status
                    </div>
                    <div class="detail-value">
                        {{ strtoupper($generatedDocument->status) }}
                    </div>
                </div>

            </div>

        @endif

        <div class="footer">
            If you believe this is an error, please contact NACPDEAN for verification.
        </div>

    </div>

@endif

</div>

</body>
</html>
