<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel customers.
// Pelanggan itu opsional di setiap transaksi: transaksi
// boleh tanpa pelanggan (customer_id null), boleh juga
// dicatat atas nama pelanggan.
class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
    ];

    // Relasi: satu pelanggan punya banyak transaksi.
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
