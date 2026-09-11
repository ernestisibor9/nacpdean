<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'member_profile_id',
        'membership_category_id',
        'membership_number',
        'membership_position',
        'status',
        'issued_at',
        'expires_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'approved_at' => 'datetime',
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
    |
    | Used by DocumentGenerationService:
    |
    | $membership->membershipCategory
    |
    */

    public function membershipCategory(): BelongsTo
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

    /* |-------------------------------------------------------------------------- | MEMBERSHIP CARDS |-------------------------------------------------------------------------- | | A membership can now have multiple active cards: | | 1. Membership Card | 2. National Executive Card | 3. Task Force Card | */
    public function cards(): HasMany
    {
        return $this->hasMany(MembershipCard::class, 'membership_id');
    }

    /*
    |--------------------------------------------------------------------------
    | OPERATIONAL RIGHTS DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function operationalRightsDocuments(): HasMany
    {
        return $this->hasMany(
            OperationalRightsDocument::class,
            'membership_id'
        );
    }


    /*
|--------------------------------------------------------------------------
| VIOLATIONS
|--------------------------------------------------------------------------
*/

    public function violations(): HasMany
    {
        return $this->hasMany(
            Violation::class,
            'membership_id'
        );
    }
}
