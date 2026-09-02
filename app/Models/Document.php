<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'template',
        'validity_type',
        'validity_value',
        'validity_date',
        'requires_form',
        'is_active',
    ];

    protected $casts = [
        'validity_date' => 'date',
        'requires_form' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(DocumentField::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function paymentItems(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }

}
