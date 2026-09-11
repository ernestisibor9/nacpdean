<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MembershipCard;
use App\Models\MembershipOfficerAppointment;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;

class MembershipCardController extends Controller
{
    public function index(QrCodeService $qrCodeService)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | GET ALL ACTIVE CARDS BELONGING TO THE LOGGED-IN MEMBER
        |--------------------------------------------------------------------------
        */

        $cards = MembershipCard::with([
            'membership',
            'profile',
            'category',
        ])
            ->whereHas('profile', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'active')
            ->orderByRaw("
                CASE card_type
                    WHEN 'membership' THEN 1
                    WHEN 'national_executive' THEN 2
                    WHEN 'task_force' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET APPROVED TASK FORCE APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | The membership_officer_appointments table is the authoritative
        | source for Task Force:
        |
        | - taskforce_id
        | - position
        | - level
        | - state
        |
        */

        $taskForceAppointments =
            MembershipOfficerAppointment::where(
                'appointment_type',
                'task_force'
            )
            ->where(
                'status',
                'approved'
            )
            ->whereIn(
                'membership_id',
                $cards
                    ->pluck('membership_id')
                    ->filter()
                    ->unique()
            )
            ->get()
            ->keyBy('membership_id');

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR CODE + ATTACH TASK FORCE APPOINTMENT
        |--------------------------------------------------------------------------
        */

        foreach ($cards as $card) {

            /*
            |--------------------------------------------------------------------------
            | QR CODE
            |--------------------------------------------------------------------------
            */

            $card->qr_image =
                $qrCodeService->generateMembershipCard(
                    $card->qr_token
                );

            /*
            |--------------------------------------------------------------------------
            | TASK FORCE APPOINTMENT
            |--------------------------------------------------------------------------
            |
            | Do NOT determine national/state from memberships.
            |
            | Use the approved appointment record.
            |
            */

            if ($card->card_type === 'task_force') {

                $card->taskForceAppointment =
                    $taskForceAppointments->get(
                        $card->membership_id
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN MEMBER CARD PAGE
        |--------------------------------------------------------------------------
        */

        return view(
            'member.membership-card',
            compact('cards')
        );
    }
}
