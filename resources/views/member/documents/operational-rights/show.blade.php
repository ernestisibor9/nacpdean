@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Index')

<style>
    /* ============================================================
   BACK BUTTON
============================================================ */

.document-back-wrapper {
    display: flex;
    align-items: center;

    margin-top: 25px;
    margin-bottom: 15px;
}


.document-back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    min-height: 42px;

    padding: 0 15px;

    border: 1px solid #d1d5db;
    border-radius: 9px;

    background: #ffffff;

    color: #374151;

    font-size: 13px;
    font-weight: 800;

    cursor: pointer;

    box-shadow: 0 2px 7px rgba(0, 0, 0, .04);

    transition:
        background .2s ease,
        border-color .2s ease,
        color .2s ease,
        transform .2s ease,
        box-shadow .2s ease;
}


.document-back-button i {
    color: #047857;
    font-size: 13px;
}


.document-back-button:hover {
    background: #ecfdf5;

    border-color: #047857;

    color: #047857;

    box-shadow: 0 5px 15px rgba(4, 120, 87, .10);

    transform: translateX(-2px);
}


.document-back-button:active {
    transform: translateX(0);
}


/* ============================================================
   MOBILE
============================================================ */

@media (max-width: 576px) {

    .document-back-wrapper {
        margin-top: 18px;
        margin-bottom: 12px;
    }


    .document-back-button {
        min-height: 40px;

        padding: 0 13px;

        font-size: 12px;
    }

}
</style>

@section('member')



{{-- ============================================================
     DOCUMENT AUTHENTICATION
============================================================= --}}

<div class="document-authentication-card">

    {{-- Header --}}
    <div class="authentication-header">

        <div class="authentication-header-icon">

            <i class="fas fa-shield-alt"></i>

        </div>

        <div>

            <h4>
                Document Authentication
            </h4>

            <p>
                Verify the authenticity and current status of this document.
            </p>

        </div>

    </div>

    {{-- ============================================================
     BACK BUTTON
============================================================= --}}

<div class="document-back-wrapper">

    <button
        type="button"
        class="document-back-button"
        onclick="window.history.back();"
    >

        <i class="fas fa-arrow-left"></i>

        <span>
            Back
        </span>

    </button>

</div>


    {{-- Body --}}
    <div class="authentication-body">

        <div class="authentication-intro">

            <div class="authentication-check">

                <i class="fas fa-qrcode"></i>

            </div>

            <div>

                <h5>
                    Scan to Verify
                </h5>

                <p>
                    Use your smartphone camera or QR scanner
                    to instantly verify this document through
                    the official NACPDEAN verification portal.
                </p>

            </div>

        </div>


        {{-- QR CODE --}}
        <div class="qr-code-wrapper">

            <div class="qr-code-frame">

                <img
                    src="{{ $qrCode['url'] }}"
                    alt="NACPDEAN Document Verification QR Code"
                    class="authentication-qr"
                >

            </div>

            <div class="qr-code-status">

                <span class="qr-status-dot"></span>

                Official Verification QR Code

            </div>

        </div>


        {{-- Verification URL --}}
        <div class="verification-url-box">

            <div class="verification-url-header">

                <div>

                    <i class="fas fa-link"></i>

                    Verification URL

                </div>

                <button
                    type="button"
                    class="copy-url-button"
                    onclick="copyVerificationUrl()"
                    title="Copy verification URL"
                >

                    <i class="fas fa-copy"></i>

                    <span>
                        Copy
                    </span>

                </button>

            </div>


            <div
                class="verification-url"
                id="verificationUrl"
            >
                {{ $verificationUrl }}
            </div>

        </div>


        {{-- Security Notice --}}
        <div class="authentication-notice">

            <div class="notice-icon">

                <i class="fas fa-info-circle"></i>

            </div>

            <div>

                <strong>
                    Verification Notice
                </strong>

                <p>
                    A valid document should display its official
                    document details and current status on the
                    NACPDEAN verification page.
                </p>

            </div>

        </div>

    </div>

</div>


<style>

    /* ============================================================
       DOCUMENT AUTHENTICATION CARD
    ============================================================ */

    .document-authentication-card {
        margin-top: 30px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0, 0, 0, .06);
    }


    /* ============================================================
       HEADER
    ============================================================ */

    .authentication-header {
        display: flex;
        align-items: center;
        gap: 15px;

        padding: 22px 26px;

        background:
            linear-gradient(
                135deg,
                #064e3b,
                #047857
            );

        color: #ffffff;
    }


    .authentication-header-icon {
        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background: rgba(255, 255, 255, .14);

        border: 1px solid rgba(255, 255, 255, .18);
    }


    .authentication-header-icon i {
        font-size: 21px;
    }


    .authentication-header h4 {
        margin: 0 0 4px;

        font-size: 19px;
        font-weight: 900;

        letter-spacing: .2px;
        text-transform: uppercase;
    }


    .authentication-header p {
        margin: 0;

        font-size: 13px;

        color: rgba(255, 255, 255, .78);
    }


    /* ============================================================
       BODY
    ============================================================ */

    .authentication-body {
        padding: 30px;
    }


    /* ============================================================
       INTRO
    ============================================================ */

    .authentication-intro {
        display: flex;
        align-items: flex-start;
        gap: 14px;

        max-width: 700px;

        margin: 0 auto 25px;
    }


    .authentication-check {
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 10px;

        background: #ecfdf5;
        color: #047857;
    }


    .authentication-check i {
        font-size: 18px;
    }


    .authentication-intro h5 {
        margin: 0 0 5px;

        color: #111827;

        font-size: 16px;
        font-weight: 900;
    }


    .authentication-intro p {
        margin: 0;

        color: #6b7280;

        font-size: 13px;
        line-height: 1.65;
    }


    /* ============================================================
       QR CODE
    ============================================================ */

    .qr-code-wrapper {
        text-align: center;

        margin: 25px auto 30px;
    }


    .qr-code-frame {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        padding: 16px;

        background: #ffffff;

        border: 1px solid #dfe5e2;

        border-radius: 16px;

        box-shadow:
            0 8px 25px rgba(0, 0, 0, .07);
    }


    .authentication-qr {
        display: block;

        width: 250px;
        height: 250px;

        object-fit: contain;

        border-radius: 5px;
    }


    .qr-code-status {
        display: flex;

        align-items: center;
        justify-content: center;

        gap: 7px;

        margin-top: 13px;

        color: #047857;

        font-size: 11px;
        font-weight: 800;

        text-transform: uppercase;
        letter-spacing: .4px;
    }


    .qr-status-dot {
        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: #10b981;

        box-shadow:
            0 0 0 4px rgba(16, 185, 129, .12);
    }


    /* ============================================================
       VERIFICATION URL
    ============================================================ */

    .verification-url-box {
        max-width: 700px;

        margin: 0 auto;

        background: #f8faf9;

        border: 1px solid #e5ebe8;

        border-radius: 12px;

        overflow: hidden;
    }


    .verification-url-header {
        display: flex;

        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 11px 14px;

        background: #f1f5f3;

        border-bottom: 1px solid #e5ebe8;

        color: #374151;

        font-size: 11px;
        font-weight: 900;

        text-transform: uppercase;
        letter-spacing: .4px;
    }


    .verification-url-header > div {
        display: flex;
        align-items: center;
        gap: 7px;
    }


    .verification-url-header i {
        color: #047857;
    }


    .copy-url-button {
        display: inline-flex;

        align-items: center;
        gap: 6px;

        padding: 6px 10px;

        border: 1px solid #d1d5db;

        border-radius: 7px;

        background: #ffffff;

        color: #374151;

        font-size: 11px;
        font-weight: 800;

        cursor: pointer;

        transition: all .2s ease;
    }


    .copy-url-button:hover {
        border-color: #047857;

        color: #047857;

        background: #ecfdf5;
    }


    .verification-url {
        padding: 14px 16px;

        color: #4b5563;

        font-size: 12px;

        line-height: 1.6;

        word-break: break-all;

        font-family:
            'SFMono-Regular',
            Consolas,
            'Liberation Mono',
            monospace;
    }


    /* ============================================================
       SECURITY NOTICE
    ============================================================ */

    .authentication-notice {
        display: flex;

        align-items: flex-start;

        gap: 12px;

        max-width: 700px;

        margin: 20px auto 0;

        padding: 14px 16px;

        background: #f0fdf4;

        border: 1px solid #bbf7d0;

        border-radius: 10px;
    }


    .notice-icon {
        color: #16a34a;

        font-size: 16px;

        margin-top: 1px;
    }


    .authentication-notice strong {
        display: block;

        margin-bottom: 3px;

        color: #166534;

        font-size: 12px;
        font-weight: 900;
    }


    .authentication-notice p {
        margin: 0;

        color: #4b5563;

        font-size: 11px;

        line-height: 1.6;
    }


    /* ============================================================
       MOBILE
    ============================================================ */

    @media (max-width: 576px) {

        .authentication-body {
            padding: 20px 16px;
        }


        .authentication-header {
            padding: 18px;
        }


        .authentication-header h4 {
            font-size: 16px;
        }


        .authentication-header p {
            font-size: 11px;
        }


        .authentication-header-icon {
            width: 42px;
            height: 42px;
        }


        .authentication-intro {
            margin-bottom: 20px;
        }


        .authentication-qr {
            width: 210px;
            height: 210px;
        }


        .qr-code-frame {
            padding: 12px;
        }


        .verification-url-header {
            align-items: flex-start;
        }


        .copy-url-button span {
            display: none;
        }


        .copy-url-button {
            padding: 7px 9px;
        }

    }

</style>


<script>

function copyVerificationUrl() {

    const urlElement =
        document.getElementById(
            'verificationUrl'
        );

    const button =
        document.querySelector(
            '.copy-url-button'
        );

    const url =
        urlElement.innerText.trim();


    navigator.clipboard
        .writeText(url)
        .then(function () {

            const original =
                button.innerHTML;


            button.innerHTML =
                '<i class="fas fa-check"></i> Copied';


            button.style.color =
                '#047857';


            button.style.borderColor =
                '#047857';


            setTimeout(
                function () {

                    button.innerHTML =
                        original;

                    button.style.color =
                        '';

                    button.style.borderColor =
                        '';

                },
                2000
            );

        })
        .catch(function () {

            alert(
                'Unable to copy the verification URL.'
            );

        });

}

</script>

@endsection

