<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipCard;

class MembershipVerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VERIFY MEMBERSHIP CARD
    |--------------------------------------------------------------------------
    */

    public function verify($qrToken)
    {
        $card = MembershipCard::with([
            'membership',
            'profile',
            'category',
        ])
            ->where(
                'qr_token',
                $qrToken
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | INVALID QR CODE
        |--------------------------------------------------------------------------
        */

        if (!$card) {

            return view(
                'verification.membership',
                [
                    'card' => null,
                    'valid' => false,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK CARD STATUS
        |--------------------------------------------------------------------------
        */

        $valid = $card->status === 'active';


        /*
        |--------------------------------------------------------------------------
        | CHECK EXPIRY
        |--------------------------------------------------------------------------
        */

        if (
            $card->expires_at &&
            $card->expires_at->isPast()
        ) {

            $valid = false;
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFY MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            !$card->membership ||
            $card->membership->status !== 'active'
        ) {

            $valid = false;
        }


        return view(
            'verification.membership',
            compact(
                'card',
                'valid'
            )
        );
    }
}
