<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'payment_item_id',
        'debit_transaction_id',
        'narration',
        'type',
        'status',
        'amount',
        'transaction_id',
        'gateway',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway' => 'array',
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
    | PAYMENT ITEM
    |--------------------------------------------------------------------------
    */

    public function paymentItem(): BelongsTo
    {
        return $this->belongsTo(PaymentItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ORIGINAL DEBIT
    |--------------------------------------------------------------------------
    |
    | Used by a CREDIT transaction to identify the debit it settles.
    |
    */

    public function debitTransaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class,
            'debit_transaction_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREDIT TRANSACTION
    |--------------------------------------------------------------------------
    |
    | Used by a DEBIT transaction to find the credit that settled it.
    |
    */

    public function creditTransaction()
    {
        return $this->hasOne(
            Transaction::class,
            'debit_transaction_id'
        );
    }

    /*
|--------------------------------------------------------------------------
| GENERATED DOCUMENTS
|--------------------------------------------------------------------------
|
| Documents issued as a result of this financial transaction.
|
*/

    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }
}
