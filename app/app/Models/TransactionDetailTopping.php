<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model rincian topping berbayar yang menempel pada satu item transaksi.
 */
class TransactionDetailTopping extends Model
{
    protected $fillable = [
        'transaction_detail_id',
        'topping_id',
        'topping_name',
        'quantity',
        'price_at_transaction',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_transaction' => 'integer',
        'subtotal' => 'integer',
    ];

    public function transactionDetail(): BelongsTo
    {
        return $this->belongsTo(TransactionDetail::class);
    }

    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }
}
