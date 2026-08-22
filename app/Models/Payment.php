<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'membership_category_id',
        'membership_category_fee_id',
        'payment_type',
        'member_fee_id',
        'fee_type',
        'amount',
        'reference',
        'gateway',
        'gateway_transaction_id',
        'gateway_status',
        'gateway_response',
        'status',
        'paid_at',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategory::class,
            'membership_category_id'
        );
    }

    public function membershipCategoryFee(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategoryFee::class,
            'membership_category_fee_id'
        );
    }

    public function memberFee(): BelongsTo
    {
        return $this->belongsTo(
            MemberFee::class,
            'member_fee_id'
        );
    }
}
