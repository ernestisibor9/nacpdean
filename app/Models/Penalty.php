<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penalty extends Model
{
    protected $fillable = [
        'payment_item_id',
        'name',
        'description',
        'amount',
        'is_custom_amount',
        'action',
        'status',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'status'           => 'boolean',
        'is_custom_amount' => 'boolean',
    ];

    public function memberPenalties(): HasMany
    {
        return $this->hasMany(MemberPenalty::class);
    }

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }
}
