<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 3 dari 7 — Tabel products
// Daftar menu minuman saja, tanpa harga. Harga ada di tabel
// product_variations, karena satu menu bisa punya harga
// berbeda untuk ukuran Regular dan Large.
// category_id: kalau kategori masih punya produk, kategori
// tidak boleh dihapus (restrict), supaya menu tidak hilang.
// is_available: true = menu tampil dan bisa dipesan,
// false = menu sedang habis.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('name', 100);
            $table->string('image', 255)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
