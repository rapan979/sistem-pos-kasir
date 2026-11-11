<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;

// Seeder produk dan variasi ukuran-harga.
// Harga contoh memakai rentang riset Rp3.000–Rp15.000.
// Harga ini BUKAN harga final perusahaan, hanya supaya
// halaman POS bisa langsung dites setelah seeder dijalankan.
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Struktur: kategori => [nama produk => [Regular, Large]]
        $data = [
            'Fresh Tea' => [
                'Es Teh Original' => [5000, 7000],
                'Es Teh Melati' => [5000, 7000],
                'Es Teh Lemon' => [6000, 8000],
            ],
            'Milk Tea' => [
                'Milk Tea Original' => [8000, 10000],
                'Thai Tea' => [9000, 12000],
                'Es Teh Susu Gula Aren' => [10000, 13000],
            ],
            'Fruit Tea' => [
                'Es Teh Apel' => [7000, 9000],
                'Es Teh Stroberi' => [8000, 10000],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();

            // Kalau kategori belum ada (misalnya CategorySeeder
            // belum dijalankan), lewati kategori ini supaya
            // tidak error. Jalankan CategorySeeder dulu.
            if (! $category) {
                continue;
            }

            foreach ($products as $productName => $prices) {
                $product = Product::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $productName,
                    ],
                    [
                        'image' => null,
                        'is_available' => true,
                    ]
                );

                ProductVariation::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variation_name' => 'Regular',
                    ],
                    ['price' => $prices[0]]
                );

                ProductVariation::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variation_name' => 'Large',
                    ],
                    ['price' => $prices[1]]
                );
            }
        }
    }
}
