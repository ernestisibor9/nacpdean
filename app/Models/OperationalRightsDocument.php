<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalRightsDocument extends Model
{
    protected $fillable = [

        'user_id',

        'membership_id',

        'member_profile_id',

        'membership_category_id',

        'payment_id',

        'document_number',

        'reference_number',

        'authentication_code',

        'document_type',

        'document_title',

        'issued_at',

        'expires_at',

        'status',

        'qr_token',

        'pdf_path',

        'generated_at',

        'revoked_at',

        'revocation_reason',

    ];


    protected $casts = [

        'issued_at' =>
            'date',

        'expires_at' =>
            'date',

        'generated_at' =>
            'datetime',

        'revoked_at' =>
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
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    public function membership(): BelongsTo
    {
        return $this->belongsTo(
            Membership::class
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
    | PAYMENT
    |--------------------------------------------------------------------------
    */

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->expires_at
            && $this->expires_at->isFuture();
    }
}
