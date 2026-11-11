<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model untuk tabel transaction_details.
// Satu baris = satu jenis barang dalam satu transaksi,
// misalnya 2 gelas "Es Teh Original Large".
// price_at_transaction MENYIMPAN harga saat transaksi terjadi,
// jadi kalau harga menu berubah besok, nota lama tidak ikut berubah.
class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_variation_id',
        'quantity',
        'price_at_transaction',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_transaction' => 'integer',
        'subtotal' => 'integer',
    ];

    // Relasi: detail ini milik satu transaksi.
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi: detail ini merujuk ke satu variasi produk.
    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
