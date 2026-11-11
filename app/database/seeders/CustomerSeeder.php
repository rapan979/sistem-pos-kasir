<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

// Seeder 2 pelanggan contoh. Pelanggan itu opsional,
// jadi seeder ini hanya supaya ada data untuk dites,
// bukan kewajiban di setiap transaksi.
class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::firstOrCreate(
            ['name' => 'Budi Santoso'],
            ['phone' => '081234567890']
        );

        Customer::firstOrCreate(
            ['name' => 'Siti Rahma'],
            ['phone' => '081234567891']
        );
    }
}
