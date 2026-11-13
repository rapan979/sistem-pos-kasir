<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 12 — Shift kasir, pesanan B2B, perluasan kolom transaksi & detail,
// serta tabel topping per item transaksi.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('restrict');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->unsignedInteger('opening_cash')->default(0);
            $table->unsignedInteger('expected_cash')->default(0);
            $table->unsignedInteger('actual_cash')->nullable();
            $table->integer('difference')->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->enum('type', ['reguler', 'b2b'])
                ->default('reguler')
                ->after('phone');
            $table->string('company_name', 120)
                ->nullable()
                ->after('type');
        });

        Schema::create('b2b_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('outlet_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('customer_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('order_code', 50)->unique();
            $table->string('event_name', 150);
            $table->date('event_date');
            $table->unsignedInteger('total_amount');
            $table->unsignedInteger('dp_amount')->default(0);
            $table->unsignedInteger('paid_total')->default(0);
            $table->enum('status', ['draft', 'dp_paid', 'in_progress', 'completed', 'cancelled'])
                ->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('event_date');
            $table->index('status');
        });

        Schema::create('b2b_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_order_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('product_variation_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('product_name', 100);
            $table->string('variation_name', 50);
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('subtotal');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('outlet_id')
                ->nullable()
                ->after('business_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('cashier_shift_id')
                ->nullable()
                ->after('outlet_id')
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('b2b_order_id')
                ->nullable()
                ->after('cashier_shift_id')
                ->constrained()
                ->onDelete('set null');
            $table->string('payment_channel', 50)
                ->default('kasir')
                ->after('payment_method');
            $table->string('payment_reference', 100)
                ->nullable()
                ->after('payment_channel')
                ->index();
            $table->string('payment_qr_url', 255)
                ->nullable()
                ->after('payment_reference');
            $table->timestamp('payment_expired_at')
                ->nullable()
                ->after('payment_qr_url');
            $table->timestamp('paid_at')
                ->nullable()
                ->after('payment_status');
            $table->foreignId('voided_by')
                ->nullable()
                ->after('paid_at')
                ->constrained('users')
                ->onDelete('set null');
            $table->string('void_reason', 255)
                ->nullable()
                ->after('voided_by');
            $table->timestamp('voided_at')
                ->nullable()
                ->after('void_reason');
            $table->text('notes')
                ->nullable()
                ->after('voided_at');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('product_name', 100)
                ->nullable()
                ->after('product_variation_id');
            $table->string('variation_name', 50)
                ->nullable()
                ->after('product_name');
            $table->string('notes', 255)
                ->nullable()
                ->after('subtotal');
        });

        Schema::create('transaction_detail_toppings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_detail_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('topping_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('topping_name', 100);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('price_at_transaction');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_detail_toppings');

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'variation_name', 'notes']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voided_by');
            $table->dropConstrainedForeignId('b2b_order_id');
            $table->dropConstrainedForeignId('cashier_shift_id');
            $table->dropConstrainedForeignId('outlet_id');
            $table->dropConstrainedForeignId('business_id');
            $table->dropIndex(['payment_reference']);
            $table->dropColumn([
                'payment_channel',
                'payment_reference',
                'payment_qr_url',
                'payment_expired_at',
                'paid_at',
                'void_reason',
                'voided_at',
                'notes',
            ]);
        });

        Schema::dropIfExists('b2b_order_items');
        Schema::dropIfExists('b2b_orders');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn(['type', 'company_name']);
        });

        Schema::dropIfExists('cashier_shifts');
    }
};
