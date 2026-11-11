<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

// Seeder 3 kategori utama dari hasil riset.
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Fresh Tea', 'Milk Tea', 'Fruit Tea'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
