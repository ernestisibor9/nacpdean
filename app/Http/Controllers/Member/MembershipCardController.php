<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MembershipCard;
use Illuminate\Support\Facades\Auth;

class MembershipCardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW MEMBERSHIP CARD
    |--------------------------------------------------------------------------
    */

  public function index()
    {
        $user = Auth::user();

        $card = MembershipCard::with([
            'membership',
            'profile',
            'category',
        ])
            ->whereHas('profile', function ($query) use ($user) {

                $query->where('user_id', $user->id);

            })
            ->where('status', 'active')
            ->latest()
            ->first();

        return view(
            'member.membership-card',
            compact('card')
        );
    }
}
