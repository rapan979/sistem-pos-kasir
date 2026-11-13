<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model master bahan baku usaha untuk sistem resep (BOM).
 */
class Ingredient extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'unit',
        'min_stock',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outletStocks(): HasMany
    {
        return $this->hasMany(OutletIngredientStock::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
