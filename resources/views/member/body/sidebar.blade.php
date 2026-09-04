@php

    use App\Models\Payment;
    use App\Models\MemberProfile;

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | CHECK SUCCESSFUL PAYMENT
    |--------------------------------------------------------------------------
    */

    $hasPaid = Payment::where('user_id', $user->id)->where('status', 'paid')->exists();

    /*
    |--------------------------------------------------------------------------
    | CHECK MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    $profile = MemberProfile::where('user_id', $user->id)->first();

    /*
    |--------------------------------------------------------------------------
    | DETERMINE MEMBER STAGE
    |--------------------------------------------------------------------------
    |
    | 1 = Registered, payment not made
    | 2 = Payment made, awaiting approval
    | 3 = Admin approved
    |
    */

    if (!$hasPaid) {
        $memberStage = 1;
    } elseif ($profile && $profile->status === 'approved') {
        $memberStage = 3;
    } else {
        $memberStage = 2;
    }

@endphp


<div id="layoutSidenav_nav">

    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

        <div class="sb-sidenav-menu">

            <div class="nav">


                {{-- =====================================================
                    CORE
                ====================================================== --}}

                <div class="sb-sidenav-menu-heading">
                    Core
                </div>


                {{-- =====================================================
                    DASHBOARD
                    ALWAYS AVAILABLE
                ====================================================== --}}

                <a class="nav-link" href="{{ route('member.member_dashboard') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>

                    Dashboard

                </a>


                {{-- =====================================================
                    STAGE 1
                    REGISTERED BUT PAYMENT NOT MADE
                ====================================================== --}}

                @if ($memberStage === 1)
                    <div class="sb-sidenav-menu-heading">
                        Membership
                    </div>


                    {{--  <a class="nav-link"
                       href="{{ route('payment.index') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        Payment

                    </a>  --}}
                @endif


                {{-- =====================================================
                    STAGE 2
                    PAYMENT MADE
                    WAITING FOR ADMIN APPROVAL
                ====================================================== --}}

                @if ($memberStage === 2)
                    <div class="sb-sidenav-menu-heading">
                        Application
                    </div>


                    {{-- PAYMENT --}}

                    {{--  <a class="nav-link"
                       href="{{ route('payment.index') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        Payment

                    </a>  --}}


                    {{-- PROFILE --}}

                    <a class="nav-link" href="{{ route('member.profile') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        Profile

                    </a>


                    {{-- APPLICATION STATUS --}}

                    {{--  <a class="nav-link"
                       href="{{ route('member.application.status') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>

                        Application Status

                    </a>  --}}
                @endif


                {{-- =====================================================
                    STAGE 3
                    ADMIN APPROVED
                ====================================================== --}}

                @if ($memberStage === 3)
                    <div class="sb-sidenav-menu-heading">
                        Membership
                    </div>


                    {{-- PAYMENT --}}

                    {{--  <a class="nav-link"
                       href="{{ route('payment.index') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        Payment

                    </a>  --}}


                    {{-- PROFILE --}}

                    <a class="nav-link" href="{{ route('member.profile') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        Profile

                    </a>


                    {{-- APPLICATION STATUS --}}
                    {{--
                    <a class="nav-link"
                       href="{{ route('member.application.status') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>

                        Application Status

                    </a>  --}}


                    {{-- =================================================
                        FULL MEMBER SERVICES
                    ================================================== --}}

                    <div class="sb-sidenav-menu-heading">
                        Member Services
                    </div>


                    {{-- MEMBERSHIP --}}

                    <a class="nav-link" href="#">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>

                        Membership

                    </a>


                    {{-- ID CARD --}}

                    <a class="nav-link" href="{{ route('membership.card') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-id-card"></i>
                        </div>

                        ID Card

                    </a>


                    {{-- DOCUMENTS --}}


                {{-- Layouts --}}

                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts"
                    aria-expanded="false" aria-controls="collapseLayouts">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-columns"></i>
                    </div>

                    Documents

                    <div class="sb-sidenav-collapse-arrow">
                        <i class="fas fa-angle-down"></i>
                    </div>

                </a>


                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                    data-bs-parent="#sidenavAccordion">

                    <nav class="sb-sidenav-menu-nested nav">

                        <a class="nav-link" href="{{ route('payment.additional') }}">
                            Pay Documents
                        </a>

                        <a class="nav-link" href="{{ route('member.documents.index') }}">
                            My Documents
                        </a>

                    </nav>

                </div>


{{--
                    <a class="nav-link" href="{{ route('payment.additional') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        Documents

                    </a>  --}}


                    {{-- CERTIFICATES --}}

                    {{--  <a class="nav-link" href="{{ route('member.documents.index') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-certificate"></i>
                        </div>

                       My Certificates

                    </a>  --}}

                @endif


            </div>

        </div>


        {{-- =============================================================
            FOOTER
        ============================================================== --}}

        <div class="sb-sidenav-footer">

            <div class="small">
                Logged in as:
            </div>

            {{ $user->name ?? 'Member' }}

        </div>

    </nav>

</div>
