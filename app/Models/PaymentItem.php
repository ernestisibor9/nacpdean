<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}
