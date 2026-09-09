<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItemEligibility extends Model
{
    protected $table = 'payment_item_eligibility';

    protected $fillable = [
        'payment_item_id',
        'audience',
        'visibility',
        'member_type',
        'operational_category',
        'requires_membership',
        'requires_operational_right',
        'requires_violation',
        'is_active',
    ];

    protected $casts = [
        'requires_membership' => 'boolean',
        'requires_operational_right' => 'boolean',
        'requires_violation' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(
            PaymentItem::class
        );
    }
}
