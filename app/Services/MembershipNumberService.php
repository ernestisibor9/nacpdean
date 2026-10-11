<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\MembershipOfficerAppointment;
use RuntimeException;

class MembershipNumberService
{
    /**
     * Base membership number — exactly as stored.
     */
    public function forBase(Membership $membership): string
    {
        return (string) $membership->membership_number;
    }

    /**
     * Officer display number — computed at render time.
     *
     * National Executive:  {BASE}-NEM-XX         e.g. NMN-EXP-LAG-0013-NEM-29
     * State Executive:     {BASE}-SEM-{STATE}-XX e.g. NMN-EXP-LAG-0013-SEM-LAG-01
     * Task Force National: {BASE}-NTF-XX         e.g. NMN-EXP-LAG-0013-NTF-01
     * Task Force State:    {BASE}-STF-XX         e.g. NMN-EXP-LAG-0013-STF-01
     */
    public function forAppointment(
        Membership $membership,
        MembershipOfficerAppointment $appointment
    ): string {
        $base = $this->forBase($membership);

        /*
        |--------------------------------------------------------------------------
        | NATIONAL EXECUTIVE
        |--------------------------------------------------------------------------
        |
        | executive_id is stored as "NEM-11", "NEM-02", etc.
        |
        */

        if ($appointment->appointment_type === 'national_executive') {

            if (empty($appointment->executive_id)) {
                throw new RuntimeException(
                    'Missing executive_id for national_executive appointment.'
                );
            }

            return $base . '-' . $appointment->executive_id;
        }

        /*
        |--------------------------------------------------------------------------
        | STATE EXECUTIVE
        |--------------------------------------------------------------------------
        |
        | executive_id is stored as "SEM-LAG-01", "SEM-ABJ-02", etc.
        | We append it directly, exactly like the national executive.
        |
        */

        if ($appointment->appointment_type === 'state_executive') {

            if (empty($appointment->executive_id)) {
                throw new RuntimeException(
                    'Missing executive_id for state_executive appointment.'
                );
            }

            return $base . '-' . $appointment->executive_id;
        }

        /*
        |--------------------------------------------------------------------------
        | TASK FORCE
        |--------------------------------------------------------------------------
        |
        | Uses NTF (national) or STF (state) plus a 2-digit sequence
        | derived from the taskforce_id.
        |
        */

        if ($appointment->appointment_type === 'task_force') {

            $prefix = $appointment->level === 'state' ? 'STF' : 'NTF';

            $numericTail = '01';

            if (
                !empty($appointment->taskforce_id) &&
                preg_match('/(\d+)$/', $appointment->taskforce_id, $m)
            ) {
                $numericTail = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            }

            return $base . '-' . $prefix . '-' . $numericTail;
        }

        /*
        |--------------------------------------------------------------------------
        | UNKNOWN APPOINTMENT TYPE
        |--------------------------------------------------------------------------
        */

        throw new RuntimeException(
            'Unsupported appointment type: ' . $appointment->appointment_type
        );
    }
}
