<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT FIELDS
    |--------------------------------------------------------------------------
    */

    public function fields(): HasMany
    {
        return $this->hasMany(
            DocumentField::class
        )
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /*
|--------------------------------------------------------------------------
| PAYMENT ITEMS
|--------------------------------------------------------------------------
*/

    public function paymentItems(): BelongsToMany
    {
        return $this->belongsToMany(
            PaymentItem::class,
            'payment_item_documents',
            'document_id',
            'payment_item_id'
        )
            ->withPivot([
                'is_primary',
                'generate_after_payment',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATED DOCUMENTS
    |--------------------------------------------------------------------------
    */

    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(
            GeneratedDocument::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MEMBERSHIP CATEGORIES
    |--------------------------------------------------------------------------
    */

    public function membershipCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            MembershipCategory::class,
            'membership_category_documents'
        )
            ->withPivot([
                'sort_order',
                'status',
            ])
            ->orderBy('membership_category_documents.sort_order');
    }
}
