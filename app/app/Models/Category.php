<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel categories.
// Satu kategori punya banyak produk, misalnya kategori "Fresh Tea"
// punya produk "Es Teh Original", "Es Teh Melati", dan seterusnya.
class Category extends Model
{
    // Kolom yang boleh diisi lewat create() / update().
    // 'name' saja, karena id dan timestamps diisi otomatis oleh Laravel.
    protected $fillable = [
        'name',
    ];

    // Relasi: satu kategori punya banyak produk.
    // Dipakai misalnya untuk menampilkan semua produk dalam satu kategori.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
