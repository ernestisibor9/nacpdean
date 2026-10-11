<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateExecutivePosition extends Model
{
    //
        protected $fillable = [
        'code',
        'position',
        'state',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENTS
    |--------------------------------------------------------------------------
    */

    public function appointments()
    {
        return $this->hasMany(
            MembershipOfficerAppointment::class,
            'executive_id',
            'code'
        );
    }
}
