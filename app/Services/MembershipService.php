<?php

namespace App\Services;

use App\Models\Membership;
use Carbon\Carbon;

class MembershipService
{
    /**
     * Get the expiration date for a new membership.
     *
     * Rule:
     * Membership expires December 31 of the issue year.
     */
    public function newMembershipExpiry(?Carbon $issuedAt = null): Carbon
    {
        $issuedAt = $issuedAt ?: now();

        return $issuedAt->copy()->endOfYear()->startOfDay();
    }

    /**
     * Get the expiration date for a membership renewal.
     *
     * Rule:
     * Renewal extends the membership to December 31
     * of the following year.
     */
    public function renewalExpiry(Membership $membership): Carbon
    {
        $currentExpiry = $membership->expires_at
            ? Carbon::parse($membership->expires_at)
            : now();

        return $currentExpiry
            ->copy()
            ->addYear()
            ->endOfYear()
            ->startOfDay();
    }
}
