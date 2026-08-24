<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItem extends Model
{
    protected $fillable = [

        'name',
        'code',
        'description',
        'type',
        'amount',
        'membership_category_id',
        'is_active',

    ];


    protected $casts = [

        'amount' => 'decimal:2',

        'is_active' => 'boolean',

    ];


    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORY
    |--------------------------------------------------------------------------
    */

    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategory::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY AMOUNT
    |--------------------------------------------------------------------------
    */

    public function getFormattedAmountAttribute(): string
    {
        return '₦' .
            number_format(
                (float) $this->amount,
                2
            );
    }
}
