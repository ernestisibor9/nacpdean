<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipCategoryDocument extends Model
{
    protected $fillable = [
        'membership_category_id',
        'document_id',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function membershipCategory(): BelongsTo
    {
        return $this->belongsTo(
            MembershipCategory::class,
            'membership_category_id'
        );
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(
            Document::class,
            'document_id'
        );
    }
}
