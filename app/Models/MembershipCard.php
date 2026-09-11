<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipCard extends Model
{
    protected $table = 'membership_cards';

    protected $fillable = [
        'membership_id',
        'card_type',
        'member_profile_id',
        'card_number',
        'membership_number',
        'membership_category_id',
        'issued_at',
        'expires_at',
        'qr_token',
        'status',
        'replaced_card_id',
        'generated_at',
    ];

protected $casts = [
    'issued_at' => 'date',
    'expires_at' => 'date',
    'generated_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    public function membership(): BelongsTo
    {
        return $this->belongsTo(
            Membership::class,
            'membership_id'
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
    | CATEGORY
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
    | REPLACED CARD
    |--------------------------------------------------------------------------
    */

    public function replacedCard(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCard::class,
            'replaced_card_id'
        );
    }
}
