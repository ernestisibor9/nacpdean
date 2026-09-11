<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipOfficerAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_id',
        'appointment_type',
        'executive_id',
        'taskforce_id',
        'position',
        'level',
        'state',
        'status',
        'appointed_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
    ];

    protected $casts = [
        'appointed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function membership()
    {
        return $this->belongsTo(
            Membership::class
        );
    }

    public function executivePosition()
    {
        return $this->belongsTo(
            NationalExecutivePosition::class,
            'executive_id',
            'code'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function rejectedBy()
    {
        return $this->belongsTo(
            User::class,
            'rejected_by'
        );
    }
}
