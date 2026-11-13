<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 11 — Gudang bahan baku, stok per cabang, resep (BOM), dan riwayat mutasi stok.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name', 100);
            $table->string('unit', 20);
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'name']);
        });

        Schema::create('outlet_ingredient_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->decimal('stock', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['outlet_id', 'ingredient_id']);
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('topping_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->decimal('quantity_used', 12, 2);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->enum('type', ['masuk', 'keluar_transaksi', 'batal_transaksi', 'penyesuaian']);
            $table->decimal('quantity', 12, 2);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('outlet_ingredient_stocks');
        Schema::dropIfExists('ingredients');
    }
};
