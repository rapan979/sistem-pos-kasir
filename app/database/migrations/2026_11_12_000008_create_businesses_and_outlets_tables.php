<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 8 — Tabel usaha UMKM berlisensi, pembayaran langganan, dan cabang (outlets).
// Lisensi dimulai dari awal tanpa masa trial: status awal 'inactive' sampai pembayaran
// langganan terverifikasi atau diaktifkan melalui kode lisensi resmi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('owner_name', 100);
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('license_key', 50)->unique();
            $table->string('plan_name', 50)->default('Bulanan');
            $table->enum('license_status', ['inactive', 'active', 'expired', 'suspended'])
                ->default('inactive');
            $table->timestamp('license_expires_at')->nullable();
            $table->string('tripay_merchant_code', 100)->nullable();
            $table->string('tripay_api_key', 255)->nullable();
            $table->string('tripay_private_key', 255)->nullable();
            $table->boolean('tripay_is_sandbox')->default(true);
            $table->timestamps();

            $table->index('license_status');
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('reference', 100)->unique();
            $table->string('merchant_ref', 100)->unique();
            $table->string('plan_name', 50);
            $table->unsignedInteger('duration_days');
            $table->unsignedInteger('amount');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_url', 255)->nullable();
            $table->string('qr_url', 255)->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])
                ->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('receipt_footer', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('businesses');
    }
};
