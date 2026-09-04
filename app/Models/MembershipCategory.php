<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function fees(): HasMany
    {
        return $this->hasMany(MembershipCategoryFee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function document()
{
    return $this->belongsTo(Document::class);
}

    // public function profiles(): HasMany
    // {
    //     return $this->hasMany(Profile::class);
    // }

    // public function memberships(): HasMany
    // {
    //     return $this->hasMany(Membership::class);
    // }
}
