<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

// Seeder 9 produk menu minuman beserta variasi ukuran dan harga baru Es Teh Perkasa.
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();

        $data = [
            'Teh Klasik' => [
                'Es Teh Original' => [4000, 6000, 'ETP-TK-001'],
                'Es Teh Melati' => [5000, 7000, 'ETP-TK-002'],
            ],
            'Teh Buah' => [
                'Es Teh Lemon' => [7000, 9000, 'ETP-TB-001'],
                'Es Teh Stroberi' => [8000, 10000, 'ETP-TB-002'],
                'Es Teh Apel' => [8000, 10000, 'ETP-TB-003'],
                'Es Teh Leci' => [9000, 12000, 'ETP-TB-004'],
            ],
            'Premium & Susu' => [
                'Milk Tea Original' => [10000, 13000, 'ETP-PS-001'],
                'Thai Tea' => [10000, 13000, 'ETP-PS-002'],
                'Es Teh Susu Gula Aren' => [12000, 15000, 'ETP-PS-003'],
            ],
        ];

        $imageMap = [
            'Es Teh Original' => 'images/products/es-teh-original.jpg',
            'Es Teh Melati' => 'images/products/es-teh-melati.jpg',
            'Es Teh Lemon' => 'images/products/es-teh-lemon.jpg',
            'Es Teh Stroberi' => 'images/products/es-teh-stroberi.jpg',
            'Es Teh Apel' => 'images/products/es-teh-apel.jpg',
            'Es Teh Leci' => 'images/products/es-teh-leci.jpg',
            'Milk Tea Original' => 'images/products/milktea-original.jpg',
            'Thai Tea' => 'images/products/thai-tea.jpg',
            'Es Teh Susu Gula Aren' => 'images/products/es-teh-susu-gula-aren.jpg',
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::where('business_id', $business->id)
                ->where('name', $categoryName)
                ->firstOrFail();

            foreach ($products as $productName => [$regularPrice, $largePrice, $skuBarcode]) {
                $product = Product::updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'name' => $productName,
                    ],
                    [
                        'category_id' => $category->id,
                        'sku_barcode' => $skuBarcode,
                        'image' => $imageMap[$productName] ?? null,
                        'is_available' => true,
                    ]
                );

                $variations = [
                    'Regular' => [$regularPrice, "{$skuBarcode}-R"],
                    'Large' => [$largePrice, "{$skuBarcode}-L"],
                ];

                foreach ($variations as $variationName => [$price, $barcode]) {
                    ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'variation_name' => $variationName,
                        ],
                        [
                            'barcode' => $barcode,
                            'price' => $price,
                        ]
                    );
                }
            }
        }
    }
}
