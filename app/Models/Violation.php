<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    protected $fillable = [
        'user_id',
        'membership_id',
        'payment_item_id',
        'violation_type',
        'audience',
        'operational_category',
        'vehicle_type',
        'vehicle_registration',
        'driver_name',
        'driver_phone',
        'description',
        'amount',
        'status',
        'vehicle_confiscated',
        'warehouse_sealed',
        'blacklisted',
        'referred_to_police',
        'referred_to_nscdc',
        'referred_to_nis',
        'referred_to_foreign_affairs',
        'referred_to_embassy',
        'recorded_by',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',

        'vehicle_confiscated' => 'boolean',
        'warehouse_sealed' => 'boolean',
        'blacklisted' => 'boolean',

        'referred_to_police' => 'boolean',
        'referred_to_nscdc' => 'boolean',
        'referred_to_nis' => 'boolean',
        'referred_to_foreign_affairs' => 'boolean',
        'referred_to_embassy' => 'boolean',

        'resolved_at' => 'datetime',
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
    | PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(
            PaymentItem::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RECORDED BY
    |--------------------------------------------------------------------------
    */

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVED BY
    |--------------------------------------------------------------------------
    */

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'resolved_by'
        );
    }
}
