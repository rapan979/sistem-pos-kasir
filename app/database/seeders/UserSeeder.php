<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Seeder akun untuk testing.
// INI AKUN CONTOH SAJA, passwordnya sederhana supaya mudah dites.
// WAJIB diganti sebelum demo ke perusahaan / sebelum dipakai sungguhan.
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate dipakai supaya seeder aman dijalankan ulang:
        // kalau email sudah ada, datanya diperbarui, bukan diduplikasi.
        User::updateOrCreate(
            ['email' => 'owner@esteh.test'],
            [
                'name' => 'Owner Es Teh',
                'password' => Hash::make('owner12345'),
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@esteh.test'],
            [
                'name' => 'Admin Es Teh',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@esteh.test'],
            [
                'name' => 'Kasir Es Teh',
                'password' => Hash::make('kasir12345'),
                'role' => 'kasir',
            ]
        );
    }
}
