{{-- ============================================================
     NACPDEAN MEMBERSHIP ID CARD (SHARED PARTIAL)
============================================================ --}}
<div class="nacp-generated-id-card
    {{ $membershipDebitNotPaid ? 'id-card-unpaid' : (!$membershipIsActive ? 'id-card-expired' : '') }}">

    @if (!$membershipDebitNotPaid && !$membershipIsActive)
        <div class="id-card-expired-label">MEMBERSHIP EXPIRED</div>
    @endif

    @if ($membershipDebitNotPaid)
        <div class="id-card-unpaid-label">PAYMENT REQUIRED</div>
    @endif

    <div class="nacp-id-header">
        <div class="nacp-id-logo"><i class="fas fa-leaf"></i></div>
        <div class="nacp-id-header-text">
            <div class="nacp-id-organization">NACPDEAN</div>
            <div class="nacp-id-subtitle">NATIONAL MEMBERSHIP ID CARD</div>
        </div>
    </div>

    <div class="nacp-id-body">
        <div class="nacp-id-photo">
            @if ($profile && $profile->photo)
                <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}" alt="Member Photo">
            @else
                <div class="nacp-id-photo-placeholder"><i class="fas fa-user"></i></div>
            @endif
        </div>

        <div class="nacp-id-information">
            <div class="nacp-id-name">{{ $memberName }}</div>
            <div class="nacp-id-category">{{ $membershipCategory->name ?? 'Member' }}</div>

            <div class="nacp-id-detail">
                <span>Membership No.</span>
                <strong>{{ $membershipCard->membership_number }}</strong>
            </div>
            <div class="nacp-id-detail">
                <span>Card No.</span>
                <strong>{{ $membershipCard->card_number }}</strong>
            </div>
            <div class="nacp-id-detail">
                <span>Issued</span>
                <strong>{{ \Carbon\Carbon::parse($membershipCard->issued_at)->format('d M Y') }}</strong>
            </div>
            <div class="nacp-id-detail">
                <span>Expires</span>
                <strong>
                    {{ $membershipCard->expires_at
                        ? \Carbon\Carbon::parse($membershipCard->expires_at)->format('d M Y')
                        : 'Not available' }}
                </strong>
            </div>
        </div>
    </div>

    <div class="nacp-id-footer">
        @if ($membershipIsActive)
            <span><i class="fas fa-check-circle"></i> ACTIVE MEMBER</span>
        @else
            <span style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i> MEMBERSHIP EXPIRED</span>
        @endif
        <span>NACPDEAN</span>
    </div>

</div>
