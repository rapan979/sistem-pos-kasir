<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model resep (BOM) yang menghubungkan variasi produk atau topping dengan takaran bahan baku.
 */
class Recipe extends Model
{
    protected $fillable = [
        'product_variation_id',
        'topping_id',
        'ingredient_id',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function topping(): BelongsTo
    {
        return $this->belongsTo(Topping::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
