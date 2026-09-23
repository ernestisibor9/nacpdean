@extends('member.member_dashboard')

@section('title')
    Membership Renewal
@endsection

@section('member')
    <div class="container-fluid">

        {{-- ================================================================
        | PAGE HEADER
        ================================================================= --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="mb-1">
                    Membership Renewal
                </h4>

                <p class="text-muted mb-0">
                    Renew your expired NACPDEAN membership.
                </p>

            </div>

            <a href="{{ route('member.member_dashboard') }}" class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>

                Back to Dashboard

            </a>

        </div>


        {{-- ================================================================
        | SUCCESS / ERROR MESSAGES
        ================================================================= --}}

        @if (session('success'))
            <div class="alert alert-success">

                <i class="bi bi-check-circle me-1"></i>

                {{ session('success') }}

            </div>
        @endif


        @if (session('error'))
            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle me-1"></i>

                {{ session('error') }}

            </div>
        @endif


        {{-- ================================================================
        | RENEWAL CARD
        ================================================================= --}}

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card shadow-sm">

                    <div class="card-header">

                        <strong>
                            Membership Renewal Details
                        </strong>

                    </div>


                    <div class="card-body">


                        {{-- =================================================
                        | MEMBERSHIP INFORMATION
                        ================================================== --}}

                        <div class="row g-4">


                            {{-- Membership Number --}}

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Membership Number
                                </label>

                                <div class="fw-semibold">

                                    {{ $membership->membership_number }}

                                </div>

                            </div>


                            {{-- Category --}}

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Membership Category
                                </label>

                                <div class="fw-semibold">

                                    {{ $category->name }}

                                </div>

                            </div>


                            {{-- Status --}}

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Current Status
                                </label>

                                <div>

                                    <span class="badge bg-danger">

                                        {{ ucfirst($membership->status) }}

                                    </span>

                                </div>

                            </div>


                            {{-- Expiry Date --}}

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Expired On
                                </label>

                                <div class="fw-semibold">

                                    {{ $membership->expires_at ? \Carbon\Carbon::parse($membership->expires_at)->format('d M Y') : 'N/A' }}

                                </div>

                            </div>

                        </div>


                        <hr class="my-4">


                        {{-- =================================================
                        | RENEWAL FEE
                        ================================================== --}}

                        <div class="text-center py-3">

                            <div class="text-muted mb-2">

                                Membership Renewal Fee

                            </div>

                            <h2 class="fw-bold mb-1">

                                ₦{{ number_format((float) $membershipCategoryFee->amount, 2) }}

                            </h2>

                            <div class="text-muted">

                                {{ ucfirst(str_replace('_', ' ', $membershipCategoryFee->fee_type)) }}
                                membership renewal fee

                            </div>

                        </div>


                        <hr class="my-4">


                        {{-- =================================================
                        | IMPORTANT INFORMATION
                        ================================================== --}}

                        <div class="alert alert-info">

                            <div class="d-flex">

                                <div class="me-2">

                                    <i class="bi bi-info-circle"></i>

                                </div>

                                <div>

                                    <strong>
                                        What happens after payment?
                                    </strong>

                                    <ul class="mb-0 mt-2">

                                        <li>
                                            Your membership will become active.
                                        </li>

                                        <li>
                                            Your existing membership number will
                                            remain unchanged.
                                        </li>

                                        <li>
                                            Your membership will be renewed until
                                            the end of the current year.
                                        </li>

                                        <li>
                                            A new membership certificate will be
                                            generated automatically.
                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        | PAYMENT FORM
                        ================================================== --}}

                        <form method="POST" action="{{ route('membership.renewal.initialize') }}"
                            id="membershipRenewalForm">

                            @csrf

                            <input type="hidden" name="membership_category_id"
                                value="{{ $membership->membership_category_id }}">


                            <div class="d-grid">

                                <button type="submit" class="btn btn-primary btn-lg" id="renewMembershipButton">

                                    <i class="bi bi-credit-card me-1"></i>

                                    Renew Membership —
                                    ₦{{ number_format((float) $membershipCategoryFee->amount, 2) }}

                                </button>

                            </div>

                        </form>


                        <div id="renewalMessage" class="mt-3"></div>


                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
    | PAYMENT INITIALIZATION JAVASCRIPT
    ================================================================= --}}

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const form =
                    document.getElementById(
                        'membershipRenewalForm'
                    );

                const button =
                    document.getElementById(
                        'renewMembershipButton'
                    );

                const message =
                    document.getElementById(
                        'renewalMessage'
                    );


                if (!form) {
                    return;
                }


                form.addEventListener(
                    'submit',
                    async function(event) {

                        event.preventDefault();


                        /*
                        |--------------------------------------------------------------------------
                        | Disable button
                        |--------------------------------------------------------------------------
                        */

                        button.disabled = true;

                        button.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span>' +
                            ' Initializing Payment...';


                        message.innerHTML = '';


                        try {

                            /*
                            |--------------------------------------------------------------------------
                            | Send request
                            |--------------------------------------------------------------------------
                            */

                            const response =
                                await fetch(
                                    form.action, {
                                        method: 'POST',

                                        headers: {
                                            'X-CSRF-TOKEN': document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                .getAttribute('content'),

                                            'Accept': 'application/json',

                                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                                        },

                                        body: new URLSearchParams(
                                            new FormData(form)
                                        )
                                    }
                                );


                            const data =
                                await response.json();


                            /*
                            |--------------------------------------------------------------------------
                            | Payment initialization failed
                            |--------------------------------------------------------------------------
                            */
                            if (!response.ok || data.status !== 'success') {

                                throw new Error(
                                    data.message ||
                                    'Unable to initialize membership renewal payment.'
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Paystack authorization URL
                            |--------------------------------------------------------------------------
                            */

                            if (
                                data.authorization_url
                            ) {

                                window.location.href =
                                    data.authorization_url;

                                return;

                            }


                            throw new Error(
                                'Paystack authorization URL was not returned.'
                            );


                        } catch (error) {

                            console.error(
                                'Membership renewal error:',
                                error
                            );


                            message.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            ${error.message}
                        </div>
                    `;


                            button.disabled = false;

                            button.innerHTML =
                                '<i class="bi bi-credit-card me-1"></i>' +
                                ' Renew Membership — ' +
                                '₦{{ number_format((float) $membershipCategoryFee->amount, 2) }}';

                        }

                    }
                );

            }
        );
    </script>
@endsection
