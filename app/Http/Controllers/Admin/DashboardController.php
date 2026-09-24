<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistedMember;
use App\Models\MemberProfile;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Admin dashboard view
public function index()
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD COUNTS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | 1. DRAFT APPLICANTS
    |--------------------------------------------------------------------------
    |
    | Members whose profile is still in 'draft' status —
    | they've started but haven't submitted.
    |
    */

    $draftApplicants = MemberProfile::where('status', 'draft')->count();

    /*
    |--------------------------------------------------------------------------
    | 2. EXPIRED MEMBERS
    |--------------------------------------------------------------------------
    |
    | Members whose latest membership has expired.
    |
    | We use `expires_at` (not `status`) so we catch memberships
    | whose expiration date has passed but whose status column
    | hasn't yet been updated by the cron.
    |
    | `distinct('user_id')` prevents counting the same member
    | multiple times if they have several past memberships.
    |
    */

    $expiredMembers = Membership::query()
        ->whereNotNull('expires_at')
        ->whereDate('expires_at', '<', today())
        ->distinct('user_id')
        ->count('user_id');

    /*
    |--------------------------------------------------------------------------
    | 3. APPROVED MEMBERS
    |--------------------------------------------------------------------------
    |
    | Members with an approved profile.
    |
    */

    $approvedMembers = MemberProfile::where('status', 'approved')->count();

    /*
    |--------------------------------------------------------------------------
    | 4. BLACKLISTED MEMBERS
    |--------------------------------------------------------------------------
    |
    | Members currently in the blacklist (not lifted).
    |
    */

    $blacklistedMembers = BlacklistedMember::where('status', 'blacklisted')->count();

    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view(
        'admin.index',
        compact(
            'draftApplicants',
            'expiredMembers',
            'approvedMembers',
            'blacklistedMembers'
        )
    );
}

    // Admin logout
    public function AdminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
