<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipCard;
use App\Models\MembershipOfficerAppointment;
use App\Models\User;
use App\Services\MembershipNumberService;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MembershipCardController extends Controller
{
    public function index(
        QrCodeService $qrCodeService,
        MembershipNumberService $numberService,
        $id = -1
    ) {
        /*
        |--------------------------------------------------------------------------
        | RESOLVE USER
        |--------------------------------------------------------------------------
        */

        if ($id == -1) {
            $user = Auth::user();
        } else {
            $user = User::find($id);

            if (!$user) {
                abort(404, 'Member not found.');
            }

            if (!in_array(Auth::user()->role, ['admin', 'super_admin'], true)) {
                $user = Auth::user();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BASE MEMBERSHIP
        |--------------------------------------------------------------------------
        */

        $membership = Membership::with('membershipCategory')
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        if (!$membership) {
            return view('member.membership-card', ['cards' => collect()]);
        }

        /*
        |--------------------------------------------------------------------------
        | BASE CARD
        |--------------------------------------------------------------------------
        */

        $baseCard = MembershipCard::with(['profile', 'category', 'membership'])
            ->where('membership_id', $membership->id)
            ->where('card_type', 'membership')
            ->where('status', 'active')
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | APPROVED APPOINTMENTS
        |--------------------------------------------------------------------------
        */

        $appointments = MembershipOfficerAppointment::where(
                'membership_id',
                $membership->id
            )
            ->where('status', 'approved')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | BUILD RUNTIME CARDS COLLECTION
        |--------------------------------------------------------------------------
        */

        $cards = collect();

        // ---------- BASE CARD ----------
        if ($baseCard) {

            $baseCard->qr_image = $qrCodeService->generateMembershipCard(
                $baseCard->qr_token
            );

            $cards->push((object) [
                'card'           => $baseCard,
                'appointment'    => null,
                'card_type'      => 'membership',
                'display_number' => $numberService->forBase($membership),
                'qr_image'       => $baseCard->qr_image,
            ]);
        }

        // ---------- OFFICER CARDS ----------
        foreach ($appointments as $appointment) {

            $cardType = match ($appointment->appointment_type) {
                'national_executive' => 'national_executive',
                'state_executive'    => 'state_executive',
                'task_force'         => 'task_force',
                default              => null,
            };

            if (!$cardType) {
                continue;
            }

            $qrImage = $baseCard?->qr_image
                ?? $qrCodeService->generateMembershipCard(
                    $baseCard?->qr_token ?? (string) Str::uuid()
                );

            $cards->push((object) [
                'card'           => $baseCard,
                'appointment'    => $appointment,
                'card_type'      => $cardType,
                'display_number' => $numberService->forAppointment($membership, $appointment),
                'qr_image'       => $qrImage,
            ]);
        }

        return view('member.membership-card', compact('cards', 'membership'));
    }
}
