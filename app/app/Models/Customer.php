<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel customers.
// Pelanggan itu opsional di setiap transaksi: transaksi
// boleh tanpa pelanggan (customer_id null), boleh juga
// dicatat atas nama pelanggan (reguler atau B2B/catering).
class Customer extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'type',
        'company_name',
    ];

    /**
     * Usaha tempat data pelanggan ini tersimpan.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    // Relasi: satu pelanggan punya banyak transaksi.
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Daftar pesanan B2B/catering milik pelanggan ini.
     */
    public function b2bOrders(): HasMany
    {
        return $this->hasMany(B2bOrder::class);
    }
}
