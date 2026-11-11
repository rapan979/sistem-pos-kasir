<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 1 dari 7 — Sistem Kasir Es Teh
// Tujuan: menambah kolom role ke tabel users yang SUDAH dibuat oleh
// migration bawaan Laravel (0001_01_01_000000_create_users_table).
// Jangan mengedit migration bawaan yang sudah berstatus Ran.
// Role: owner = pemilik, admin = pengelola, kasir = hanya POS.
// Default kasir, supaya akun baru yang dibuat tanpa role tidak
// tiba-tiba punya akses owner.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin', 'kasir'])
                ->default('kasir')
                ->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
