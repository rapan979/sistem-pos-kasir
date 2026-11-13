<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 10 — Isolasi katalog menu per usaha (business_id), tambah SKU/barcode
// untuk dukungan scanner, dan tambah tabel toppings berbayar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->dropUnique(['name']);
            $table->unique(['business_id', 'name']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('sku_barcode', 80)
                ->nullable()
                ->after('name')
                ->index();
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->string('barcode', 80)
                ->nullable()
                ->after('variation_name')
                ->index();
        });

        Schema::create('toppings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name', 100);
            $table->unsignedInteger('price');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toppings');

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropColumn('barcode');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sku_barcode']);
            $table->dropColumn('sku_barcode');
            $table->dropConstrainedForeignId('business_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'name']);
            $table->unique('name');
            $table->dropConstrainedForeignId('business_id');
        });
    }
};
