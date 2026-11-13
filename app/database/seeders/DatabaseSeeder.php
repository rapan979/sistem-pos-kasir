<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Seeder utama sistem POS Kasir Es Teh.
// Urutan eksekusi wajib mengikuti ketergantungan relasi tabel:
// 1. Usaha & cabang -> 2. Pengguna -> 3. Kategori -> 4. Produk & variasi
// -> 5. Topping, bahan baku, stok, & resep BOM -> 6. Pelanggan.
// Jalankan dengan: php artisan db:seed
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BusinessSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            InventoryAndToppingSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
