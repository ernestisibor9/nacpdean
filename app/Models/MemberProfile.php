<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberProfile extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'membership_category_id',
        'membership_number',

        'surname',
        'first_name',
        'middle_name',

        'phone',
        'photo',

        'date_of_birth',
        'gender',
        'nationality',

        'address',
        'city',
        'state',
        'lga',

        'business_name',
        'business_registration_number',
        'business_type',
        'business_address',


        'status',

        'admin_comment',

        'submitted_at',
        'approved_at',
        'rejection_reason',

    ];


    protected $casts = [

        'submitted_at' => 'datetime',

        'approved_at' => 'datetime',

        'date_of_birth' => 'date',

    ];


    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipCategory()
    {
        return $this->belongsTo(
            MembershipCategory::class,
            'membership_category_id'
        );
    }
}
