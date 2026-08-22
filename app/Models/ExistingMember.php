<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExistingMember extends Model
{
    protected $fillable = [
        'membership_number',
        'full_name',
        'category',
        'state_code',
        'phone',
        'email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
