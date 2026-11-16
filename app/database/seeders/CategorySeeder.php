<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Database\Seeder;

// Seeder 3 kategori utama untuk usaha Es Teh Perkasa.
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();

        $categories = ['Teh Klasik', 'Teh Buah', 'Premium & Susu'];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $name,
                ]
            );
        }
    }
}
