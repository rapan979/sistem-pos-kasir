<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Database\Seeder;

// Seeder 3 kategori utama dari hasil riset untuk usaha Es Teh Nusantara.
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();

        foreach (['Fresh Tea', 'Milk Tea', 'Fruit Tea'] as $name) {
            // Kaitkan data lama (jika ada dari seeder awal) agar tidak menjadi yatim.
            Category::whereNull('business_id')
                ->where('name', $name)
                ->update(['business_id' => $business->id]);

            Category::firstOrCreate([
                'business_id' => $business->id,
                'name' => $name,
            ]);
        }
    }
}
