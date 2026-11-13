<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model topping tambahan berbayar (contoh: Boba, Cincau, Cheese Cream).
 */
class Topping extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'price',
        'is_available',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_available' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function transactionDetailToppings(): HasMany
    {
        return $this->hasMany(TransactionDetailTopping::class);
    }
}
