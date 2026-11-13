<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Ingredient;
use App\Models\OutletIngredientStock;
use App\Models\ProductVariation;
use App\Models\Recipe;
use App\Models\Topping;
use Illuminate\Database\Seeder;

// Seeder topping berbayar, bahan baku gudang, stok awal cabang, dan resep BOM otomatis
// untuk usaha contoh Es Teh Nusantara.
class InventoryAndToppingSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();
        $outlets = $business->outlets()->get();

        // 1. Topping berbayar
        $toppingData = [
            ['name' => 'Boba', 'price' => 3000],
            ['name' => 'Cincau', 'price' => 2000],
            ['name' => 'Cheese Cream', 'price' => 4000],
        ];

        $toppingsByName = [];
        foreach ($toppingData as $item) {
            $toppingsByName[$item['name']] = Topping::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $item['name'],
                ],
                [
                    'price' => $item['price'],
                    'is_available' => true,
                ]
            );
        }

        // 2. Bahan baku gudang (ingredients) beserta stok awal per cabang
        $ingredientData = [
            ['name' => 'Cup 16oz', 'unit' => 'pcs', 'min_stock' => 50, 'initial_stock' => 300],
            ['name' => 'Cup 22oz', 'unit' => 'pcs', 'min_stock' => 50, 'initial_stock' => 250],
            ['name' => 'Biang Teh', 'unit' => 'ml', 'min_stock' => 3000, 'initial_stock' => 20000],
            ['name' => 'Gula Cair', 'unit' => 'ml', 'min_stock' => 1000, 'initial_stock' => 10000],
            ['name' => 'Boba', 'unit' => 'gr', 'min_stock' => 500, 'initial_stock' => 3000],
            ['name' => 'Cincau', 'unit' => 'gr', 'min_stock' => 500, 'initial_stock' => 3000],
            ['name' => 'Cheese Cream', 'unit' => 'gr', 'min_stock' => 400, 'initial_stock' => 2500],
        ];

        $ingredientsByName = [];
        foreach ($ingredientData as $item) {
            $ingredient = Ingredient::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $item['name'],
                ],
                [
                    'unit' => $item['unit'],
                    'min_stock' => $item['min_stock'],
                ]
            );

            $ingredientsByName[$item['name']] = $ingredient;

            foreach ($outlets as $outlet) {
                OutletIngredientStock::firstOrCreate(
                    [
                        'outlet_id' => $outlet->id,
                        'ingredient_id' => $ingredient->id,
                    ],
                    [
                        'stock' => $item['initial_stock'],
                    ]
                );
            }
        }

        // 3. Resep BOM standar per ukuran produk (Regular 16oz vs Large 22oz)
        $variations = ProductVariation::whereHas('product', function ($query) use ($business) {
            $query->where('business_id', $business->id);
        })->get();

        $sizeRecipes = [
            'Regular' => [
                'Cup 16oz' => 1,
                'Biang Teh' => 150,
                'Gula Cair' => 30,
            ],
            'Large' => [
                'Cup 22oz' => 1,
                'Biang Teh' => 250,
                'Gula Cair' => 45,
            ],
        ];

        foreach ($variations as $variation) {
            $bom = $sizeRecipes[$variation->variation_name] ?? null;
            if (! $bom) {
                continue;
            }

            foreach ($bom as $ingredientName => $quantityUsed) {
                Recipe::updateOrCreate(
                    [
                        'product_variation_id' => $variation->id,
                        'topping_id' => null,
                        'ingredient_id' => $ingredientsByName[$ingredientName]->id,
                    ],
                    [
                        'quantity_used' => $quantityUsed,
                    ]
                );
            }
        }

        // 4. Resep BOM pemakaian bahan untuk setiap topping berbayar
        $toppingQuantities = [
            'Boba' => 30,
            'Cincau' => 40,
            'Cheese Cream' => 35,
        ];

        foreach ($toppingQuantities as $toppingName => $quantityUsed) {
            Recipe::updateOrCreate(
                [
                    'product_variation_id' => null,
                    'topping_id' => $toppingsByName[$toppingName]->id,
                    'ingredient_id' => $ingredientsByName[$toppingName]->id,
                ],
                [
                    'quantity_used' => $quantityUsed,
                ]
            );
        }
    }
}
