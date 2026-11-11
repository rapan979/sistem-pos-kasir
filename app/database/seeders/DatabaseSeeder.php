<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Seeder utama. File ini MENIMPA DatabaseSeeder.php bawaan Laravel
// (yang isinya masih kosong). Urutannya penting:
// 1. User dulu, 2. Category, 3. Product (butuh Category),
// 4. Customer. Jalankan dengan: php artisan db:seed
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
