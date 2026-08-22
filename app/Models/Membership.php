<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Membership extends Model
{
    protected $fillable = [

        'user_id',

        'member_profile_id',

        'membership_category_id',

        'membership_number',

        'status',

        'issued_at',

        'expires_at',

        'approved_at',

        'approved_by',

    ];


    protected $casts = [

        'issued_at' =>
            'date',

        'expires_at' =>
            'date',

        'approved_at' =>
            'datetime',

    ];


    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER PROFILE
    |--------------------------------------------------------------------------
    */

    public function profile(): BelongsTo
    {
        return $this->belongsTo(
            MemberProfile::class,
            'member_profile_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategory::class,
            'membership_category_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVED BY
    |--------------------------------------------------------------------------
    */

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CARD
    |--------------------------------------------------------------------------
    */

    public function card(): HasOne
    {
        return $this->hasOne(
            MembershipCard::class,
            'membership_id'
        );
    }
}
