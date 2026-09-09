<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'transaction_id',
        'document_number',
        'tracking_code',
        'issued_at',
        'expires_at',
        'replaced_by_document_id',
        'status',
        'field_values',
    ];

    protected $casts = [
        'field_values' => 'array',
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /*
    |--------------------------------------------------------------------------
    | FINANCIAL TRANSACTION
    |--------------------------------------------------------------------------
    |
    | This is the INTERNAL transactions.id.
    | Never store the Paystack external transaction ID here.
    |
    */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | REPLACED DOCUMENT
    |--------------------------------------------------------------------------
    */

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(
            GeneratedDocument::class,
            'replaced_by_document_id'
        );
    }
}
