<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 5 dari 7 — Tabel customers
// Data pelanggan, sifatnya opsional. Dipakai kalau pembeli
// mau dicatat namanya, misalnya untuk menulis nama di cup
// atau data langganan. Pembeli umum tetap bisa transaksi
// tanpa masuk tabel ini, karena customer_id di tabel
// transactions boleh kosong.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
