<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel product_variations.
// Satu baris = satu ukuran dan satu harga dari satu produk,
// misalnya "Large" Rp7.000 dari produk "Es Teh Original".
// Harga di sini adalah harga saat ini. Harga yang dipakai
// di transaksi lama TIDAK diambil dari sini, tapi dari
// kolom price_at_transaction di tabel transaction_details.
class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'variation_name',
        'barcode',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    // Relasi: variasi ini milik satu produk.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Komposisi resep bahan baku (BOM) untuk ukuran ini.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // Relasi: variasi ini pernah dipakai di banyak detail transaksi.
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Item pesanan B2B yang memilih variasi produk ini.
     */
    public function b2bOrderItems(): HasMany
    {
        return $this->hasMany(B2bOrderItem::class);
    }
}
