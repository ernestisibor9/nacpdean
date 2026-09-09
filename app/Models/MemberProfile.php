<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

        /*
        |--------------------------------------------------------------------------
        | APPLICANT DOCUMENTS
        |--------------------------------------------------------------------------
        */

        'cac_certificate',

        'cac_particulars_of_directors',

        'nepc_export_license',

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

        'submitted_at' =>
        'datetime',

        'approved_at' =>
        'datetime',

        'date_of_birth' =>
        'date',

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
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
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
    | MEMBERSHIP
    |--------------------------------------------------------------------------
    */

    public function membership(): HasOne
    {
        return $this->hasOne(
            Membership::class,
            'member_profile_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CARD
    |--------------------------------------------------------------------------
    */

    public function membershipCard(): HasOne
    {
        return $this->hasOne(
            MembershipCard::class,
            'member_profile_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
