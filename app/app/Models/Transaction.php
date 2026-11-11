<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel transactions.
// Satu baris = satu struk / satu kali pembayaran di kasir.
// Total, dibayar, dan kembalian disimpan sebagai angka (rupiah),
// bukan dihitung ulang dari harga produk saat ini.
class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'change_amount' => 'integer',
    ];

    // Relasi: transaksi ini dibuat oleh satu user (kasir/owner).
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: transaksi ini atas nama satu pelanggan, atau tanpa pelanggan.
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: satu transaksi punya banyak detail barang.
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
