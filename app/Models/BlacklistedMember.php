<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistedMember extends Model
{
    //
    protected $fillable = [
        'membership_number',
        'member_name',
        'company_name',
        'state',
        'photo',
        'effective_date',
        'status',
        'reason',
        'blacklisted_until',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'blacklisted_until' => 'date',
    ];
}
