<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

// Seeder 8 produk contoh beserta 16 variasi ukuran-harga dan SKU barcode
// untuk usaha Es Teh Nusantara.
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();

        $data = [
            'Fresh Tea' => [
                'Es Teh Original' => [5000, 7000, 'ETN-FT-001'],
                'Es Teh Melati' => [5000, 7000, 'ETN-FT-002'],
                'Es Teh Lemon' => [6000, 8000, 'ETN-FT-003'],
            ],
            'Milk Tea' => [
                'Milk Tea Original' => [8000, 10000, 'ETN-MT-001'],
                'Thai Tea' => [9000, 12000, 'ETN-MT-002'],
                'Es Teh Susu Gula Aren' => [10000, 13000, 'ETN-MT-003'],
            ],
            'Fruit Tea' => [
                'Es Teh Apel' => [7000, 9000, 'ETN-FR-001'],
                'Es Teh Stroberi' => [8000, 10000, 'ETN-FR-002'],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::where('business_id', $business->id)
                ->where('name', $categoryName)
                ->firstOrFail();

            foreach ($products as $productName => [$regularPrice, $largePrice, $skuBarcode]) {
                // Kaitkan produk lama (jika ada dari seeder awal) ke usaha Es Teh Nusantara.
                Product::whereNull('business_id')
                    ->where('category_id', $category->id)
                    ->where('name', $productName)
                    ->update(['business_id' => $business->id]);

                $product = Product::updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'category_id' => $category->id,
                        'name' => $productName,
                    ],
                    [
                        'sku_barcode' => $skuBarcode,
                        'image' => null,
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
