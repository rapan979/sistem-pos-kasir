<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel products.
// Satu produk milik satu kategori, dan punya banyak variasi
// ukuran-harga, misalnya "Es Teh Original" punya variasi
// Regular Rp5.000 dan Large Rp7.000.
class Product extends Model
{
    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'sku_barcode',
        'image',
        'is_available',
    ];

    // Laravel mengubah nilai is_available menjadi true/false
    // secara otomatis, bukan 1/0.
    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Usaha pemilik produk ini.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // Relasi: produk ini milik satu kategori.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi: satu produk punya banyak variasi ukuran-harga.
    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }
}
