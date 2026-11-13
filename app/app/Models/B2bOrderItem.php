<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model rincian item minuman pada pesanan partai besar (B2B).
 */
class B2bOrderItem extends Model
{
    protected $fillable = [
        'b2b_order_id',
        'product_variation_id',
        'product_name',
        'variation_name',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'subtotal' => 'integer',
    ];

    public function b2bOrder(): BelongsTo
    {
        return $this->belongsTo(B2bOrder::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
