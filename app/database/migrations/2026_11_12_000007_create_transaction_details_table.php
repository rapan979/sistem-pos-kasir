<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 7 dari 7 — Tabel transaction_details
// Rincian isi struk. Satu struk bisa punya banyak baris,
// contoh: 2 Es Teh Original Regular + 1 Thai Tea Large
// dicatat sebagai 2 baris di tabel ini.
// price_at_transaction: HARGA SAAT TRANSAKSI TERJADI.
// Ini yang paling penting: harga ini disalin dari
// product_variations saat checkout dan tidak pernah diubah
// lagi. Kalau bulan depan harga menu naik, nota lama tetap
// menampilkan harga lama. Jangan menghitung ulang nota
// lama dari harga terbaru.
// subtotal: quantity x price_at_transaction, dihitung
// saat checkout dan disimpan, bukan dihitung ulang
// setiap kali laporan dibuka.
// Kalau transaksi dihapus, detailnya ikut terhapus
// (cascade). Kalau variasi menu sudah pernah dipakai
// transaksi, variasi itu tidak boleh dihapus (restrict).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('product_variation_id')
                ->constrained()
                ->onDelete('restrict');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price_at_transaction');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
