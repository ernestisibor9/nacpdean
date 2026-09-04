<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_item_id',
        'membership_category_id',
        'membership_category_fee_id',
        'payment_type',
        'member_fee_id',
        'fee_type',
        'amount',
        'description',
        'payment_reference',
        'paystack_reference',
        'reference',
        'paystack_authorization_url',
        'document_field_values',
        'gateway',
        'gateway_transaction_id',
        'gateway_status',
        'gateway_response',
        'renewal_document_id',
        'status',
        'paid_at',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'document_field_values' => 'array',
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

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }

    public function operationalRightsDocument(): HasOne
    {
        return $this->hasOne(
            OperationalRightsDocument::class,
            'payment_id'
        );
    }

    public function renewalDocument(): BelongsTo
    {
        return $this->belongsTo(
            GeneratedDocument::class,
            'renewal_document_id'
        );
    }
}
