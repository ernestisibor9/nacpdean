<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentItem extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'amount',
        'membership_category_id',
        'document_id',
        'is_active',
        'is_renewable',
        'renewal_payment_item_id',
    ];


    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_renewable' => 'boolean',
    ];


    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategory::class
        );
    }


    public function document(): BelongsTo
    {
        return $this->belongsTo(
            Document::class
        );
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            Document::class,
            'payment_item_documents',
            'payment_item_id',
            'document_id'
        )
            ->withPivot([
                'is_primary',
                'generate_after_payment',
            ])
            ->withTimestamps();
    }


    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class
        );
    }


    public function transactions(): HasMany
    {
        return $this->hasMany(
            Transaction::class
        );
    }

    public function renewalPaymentItem(): BelongsTo
    {
        return $this->belongsTo(
            PaymentItem::class,
            'renewal_payment_item_id'
        );
    }

    public function renewalPaymentItems(): HasMany
    {
        return $this->hasMany(
            PaymentItem::class,
            'renewal_payment_item_id'
        );
    }

    /**
     * Categories this payment item applies to.
     */
    public function membershipCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            MembershipCategory::class,
            'payment_item_categories',
            'payment_item_id',
            'membership_category_id'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | ELIGIBILITY
    |--------------------------------------------------------------------------
    */

    public function eligibilities(): HasMany
    {
        return $this->hasMany(
            PaymentItemEligibility::class
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
            Violation::class
        );
    }


        /*
    |--------------------------------------------------------------------------
    | ITEMS THAT RENEW THIS ITEM
    |--------------------------------------------------------------------------
    */

    public function renewedBy()
    {
        return $this->hasMany(
            self::class,
            'renewal_payment_item_id'
        );
    }

}
