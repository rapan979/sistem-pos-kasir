<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Migration 9 — Hubungkan tabel users ke usaha (business_id) dan cabang (outlet_id),
// serta ringkas hak akses menjadi 2 role: 'owner' dan 'kasir'.
return new class extends Migration
{
    public function up(): void
    {
        // Ubah akun lama yang sempat ber-role 'admin' menjadi 'owner' sebelum menyempitkan ENUM.
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'owner']);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('outlet_id')
                ->nullable()
                ->after('business_id')
                ->constrained()
                ->onDelete('set null');
            $table->boolean('is_active')
                ->default(true)
                ->after('role');
        });

        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'kasir') NOT NULL DEFAULT 'kasir'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'kasir') NOT NULL DEFAULT 'kasir'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('outlet_id');
            $table->dropConstrainedForeignId('business_id');
            $table->dropColumn('is_active');
        });
    }
};
