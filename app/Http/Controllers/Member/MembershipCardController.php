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
        |
        | A member can have more than one active card:
        |
        | - Membership Card
        | - National Executive Card
        | - Task Force Card
        |
        | Therefore, we load ALL active cards belonging to the
        | currently logged-in member.
        |
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
        | GET MEMBERSHIP IDS FROM THE MEMBER'S CARDS
        |--------------------------------------------------------------------------
        */

        $membershipIds = $cards
            ->pluck('membership_id')
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | GET APPROVED TASK FORCE APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | The membership_officer_appointments table is the authoritative
        | source for Task Force information.
        |
        | We use:
        |
        | - taskforce_id
        | - position
        | - level
        | - state
        |
        | IMPORTANT:
        |
        | We use groupBy() instead of keyBy().
        |
        | This prevents one approved Task Force appointment from
        | overwriting another appointment belonging to the same member.
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
                $membershipIds
            )
            ->orderBy('id')
            ->get()
            ->groupBy('membership_id');

        /*
        |--------------------------------------------------------------------------
        | GENERATE QR CODE + ATTACH TASK FORCE APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | Every active card receives its QR image.
        |
        | For Task Force cards, we also attach the approved Task Force
        | appointment records belonging to that membership.
        |
        */

        foreach ($cards as $card) {

            /*
            |--------------------------------------------------------------------------
            | GENERATE QR CODE
            |--------------------------------------------------------------------------
            |
            | The QR token already belongs to the card.
            |
            | We do not change the QR token or generate a new one.
            |
            */

            $card->qr_image =
                $qrCodeService->generateMembershipCard(
                    $card->qr_token
                );

            /*
            |--------------------------------------------------------------------------
            | TASK FORCE CARD
            |--------------------------------------------------------------------------
            |
            | Task Force information must come from the approved
            | MembershipOfficerAppointment record.
            |
            */

            if ($card->card_type === 'task_force') {

                $appointments =
                    $taskForceAppointments->get(
                        $card->membership_id,
                        collect()
                    );

                /*
                |--------------------------------------------------------------------------
                | ATTACH ALL APPROVED APPOINTMENTS
                |--------------------------------------------------------------------------
                |
                | This is a collection because one membership could
                | potentially have more than one approved appointment.
                |
                */

                $card->taskForceAppointments = $appointments;

                /*
                |--------------------------------------------------------------------------
                | PRIMARY TASK FORCE APPOINTMENT
                |--------------------------------------------------------------------------
                |
                | For the current card design, we can use the first
                | approved appointment as the primary appointment.
                |
                | The complete collection is still available through:
                |
                | $card->taskForceAppointments
                |
                */

                $card->taskForceAppointment =
                    $appointments->first();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN MEMBER CARD PAGE
        |--------------------------------------------------------------------------
        |
        | We continue using ONE page:
        |
        | member.membership-card
        |
        | The Blade page will determine which card design to display
        | based on:
        |
        | - card_type
        | - membership category
        | - Task Force level
        |
        | The member does NOT need separate menu items.
        |
        */

        return view(
            'member.membership-card',
            compact('cards')
        );
    }
}
