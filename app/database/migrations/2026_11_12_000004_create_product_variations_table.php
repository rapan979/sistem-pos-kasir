<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 4 dari 7 — Tabel product_variations
// Ukuran dan harga tiap menu, contoh: Es Teh Leci Regular
// Rp7.000 dan Large Rp10.000.
// Satu produk tidak boleh punya dua variasi dengan nama
// yang sama, jadi product_id + variation_name dibuat unik.
// Kalau produk dihapus, variasinya ikut terhapus (cascade),
// karena variasi tidak ada artinya tanpa produknya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('variation_name', 50);
            $table->unsignedInteger('price');
            $table->timestamps();

            $table->unique(['product_id', 'variation_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
