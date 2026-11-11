<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 2 dari 7 — Tabel categories
// Kelompok menu, contoh: Fresh Tea, Milk Tea, Fruit Tea.
// Nama kategori dibuat unik supaya tidak ada dua kategori
// dengan nama yang sama di layar kasir.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
