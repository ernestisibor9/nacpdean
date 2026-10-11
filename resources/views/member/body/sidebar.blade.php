@php

    use App\Models\MemberProfile;
    use App\Models\Payment;

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | CHECK MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    $profile = MemberProfile::where('user_id', $user->id)->first();

    /*
    |--------------------------------------------------------------------------
    | DETERMINE MEMBER STATUS
    |--------------------------------------------------------------------------
    */

    $memberStatus = $profile->status ?? 'draft';

    /*
    |--------------------------------------------------------------------------
    | CHECK WHETHER THE MEMBER HAS PAID
    |--------------------------------------------------------------------------
    |
    | Membership payment gate for the "Profile" menu.
    | A member must have at least ONE paid membership or
    | membership_renewal payment before they can see the
    | Profile menu item.
    |
    */

    $hasPaidMembership = Payment::where('user_id', $user->id)
        ->whereIn('payment_type', ['membership', 'membership_renewal'])
        ->where('status', 'paid')
        ->exists();

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


                {{-- DASHBOARD --}}

                <a class="nav-link" href="{{ route('member.member_dashboard') }}">

                    <div class="sb-nav-link-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>

                    Dashboard

                </a>


                {{-- =====================================================
                    APPLICATION / PROFILE
                ====================================================== --}}

                @if ($hasPaidMembership && in_array($memberStatus, ['draft', 'submitted', 'rejected'], true))
                    <div class="sb-sidenav-menu-heading">
                        Application
                    </div>

                    <a class="nav-link" href="{{ route('member.profile') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        Profile

                    </a>
                @endif


                {{-- =====================================================
                    APPROVED MEMBER
                ====================================================== --}}

                @if ($memberStatus === 'approved')

                    <div class="sb-sidenav-menu-heading">
                        Membership
                    </div>


                    {{-- PROFILE --}}

                    <a class="nav-link" href="{{ route('member.profile') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        Profile

                    </a>


                    {{-- =================================================
                        MEMBER SERVICES
                    ================================================== --}}

                    <div class="sb-sidenav-menu-heading">
                        Member Services
                    </div>


                    {{-- ID CARD --}}

                    <a class="nav-link" href="{{ route('membership.card') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-id-card"></i>
                        </div>

                        ID Card

                    </a>


                    {{-- DOCUMENTS --}}

                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                        data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">

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


                    {{-- =================================================
                        PENALTIES
                        -------------------------------------------------
                        Only visible when the member has an unpaid penalty.
                        The table is guarded with Schema::hasTable() so the
                        app runs even before any penalty has been assigned.
                    ================================================== --}}

                    @php
                        $unpaidPenaltyCount = 0;

                        if (\Illuminate\Support\Facades\Schema::hasTable('member_penalties')) {
                            $unpaidPenaltyCount = \App\Models\MemberPenalty::where('user_id', auth()->id())
                                ->where('payment_status', 'unpaid')
                                ->count();
                        }
                    @endphp

                    @if ($unpaidPenaltyCount > 0)
                        <a class="nav-link" href="{{ route('member.penalties.index') }}">

                            <div class="sb-nav-link-icon">
                                <i class="fas fa-gavel"></i>
                            </div>

                            Penalties

                            <span class="badge bg-danger ms-2">{{ $unpaidPenaltyCount }}</span>

                        </a>
                    @endif


                    {{-- LOGOUT --}}

                    <a class="nav-link" href="{{ route('member.logout') }}">

                        <div class="sb-nav-link-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>

                        Logout

                    </a>

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
