<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'member_type',
        'description',
        'status',
        'document_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | FEES
    |--------------------------------------------------------------------------
    */

    public function fees(): HasMany
    {
        return $this->hasMany(
            MembershipCategoryFee::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LEGACY / PRIMARY DOCUMENT
    |--------------------------------------------------------------------------
    |
    | Kept for backward compatibility with the existing
    | membership_categories.document_id column.
    |
    */

    public function document(): BelongsTo
    {
        return $this->belongsTo(
            Document::class,
            'document_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP DOCUMENT SET
    |--------------------------------------------------------------------------
    |
    | This is the relationship the approval workflow should use.
    |
    | Example:
    |
    | EXPORTER
    | - Annual Membership Receipt
    | - Charcoal Lifting Right - Exporter
    | - Membership Certificate - Regular Exporter
    | - Membership Confirmation Letter
    |
    | RCG
    | - Annual Membership Receipt
    | - Charcoal Lifting Right - RCG
    | - Membership Certificate - RCG
    | - Membership Confirmation Letter
    |
    */

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            Document::class,
            'membership_category_documents'
        )
            ->withPivot([
                'sort_order',
                'status',
            ])
            ->wherePivot('status', true)
            ->orderBy('sort_order');
    }

    public function paymentItems(): BelongsToMany
    {
        return $this->belongsToMany(
            PaymentItem::class,
            'payment_item_categories',
            'membership_category_id',
            'payment_item_id'
        )->withTimestamps();
    }



}
