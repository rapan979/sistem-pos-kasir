<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 6 dari 7 — Tabel transactions
// Kepala struk / nota. Satu baris di tabel ini = satu struk.
// invoice_number: nomor nota, unik, format INV-YYYYMMDD-001,
// contoh INV-20261112-001. Nomor ini dibuat di kode aplikasi
// di dalam database transaction supaya tidak ada nomor ganda
// kalau dua kasir checkout bersamaan.
// user_id: kasir yang melayani. Kalau user sudah punya
// transaksi, user tidak boleh dihapus (restrict).
// customer_id: boleh kosong untuk pembeli umum. Kalau data
// pelanggan dihapus, transaksi tetap ada dan customer_id
// menjadi kosong (nullOnDelete).
// payment_method: tunai, qris, atau transfer. Keputusan
// 2026-11-11: e-wallet TIDAK dipisah, dicatat sebagai qris
// kalau bayar dengan scan QR, atau transfer kalau transfer.
// payment_status: pending = menunggu bayar, lunas = sudah dibayar,
// batal = dibatalkan. Transaksi batal tetap disimpan,
// jangan dihapus, supaya laporan tetap bisa dilacak.
// change_amount: kembalian = paid_amount - total_amount.
// Untuk qris/transfer, paid_amount = total_amount dan
// change_amount = 0.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->unsignedInteger('total_amount');
            $table->unsignedInteger('paid_amount');
            $table->unsignedInteger('change_amount')->default(0);
            $table->enum('payment_method', ['tunai', 'qris', 'transfer']);
            $table->enum('payment_status', ['pending', 'lunas', 'batal'])
                ->default('pending');
            $table->timestamps();

            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
