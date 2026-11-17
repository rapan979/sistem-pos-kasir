<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Seeder akun untuk simulasi lokal dengan 2 role (owner & kasir).
// Akun admin lama otomatis dibersihkan agar sesuai arsitektur final.
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus akun contoh 'admin' lama jika masih tersisa dari seeder tahap awal.
        User::where('email', 'admin@esteh.test')->delete();

        $activeBusiness = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();
        $inactiveBusiness = Business::where('license_key', 'ETN-2026-NONAKTIF')->firstOrFail();

        $geraiPusat = Outlet::where('business_id', $activeBusiness->id)
            ->where(function ($q) {
                $q->where('name', 'Gerai Pusat')->orWhere('name', 'Gerai Utama');
            })
            ->firstOrFail();
        $geraiCabang2 = Outlet::where('business_id', $activeBusiness->id)
            ->where('name', 'Gerai Cabang 2')
            ->firstOrFail();

        $accounts = [
            [
                'email' => 'owner@esteh.test',
                'name' => 'Owner Es Teh',
                'password' => 'owner12345',
                'role' => 'owner',
                'business_id' => $activeBusiness->id,
                'outlet_id' => null,
                'is_active' => true,
            ],
            [
                'email' => 'kasir@esteh.test',
                'name' => 'Kasir Gerai Pusat',
                'password' => 'kasir12345',
                'role' => 'kasir',
                'business_id' => $activeBusiness->id,
                'outlet_id' => $geraiPusat->id,
                'is_active' => true,
            ],
            [
                'email' => 'kasir2@esteh.test',
                'name' => 'Kasir Cabang 2',
                'password' => 'kasir12345',
                'role' => 'kasir',
                'business_id' => $activeBusiness->id,
                'outlet_id' => $geraiCabang2->id,
                'is_active' => true,
            ],
            [
                'email' => 'unpaid@esteh.test',
                'name' => 'Owner Belum Bayar',
                'password' => 'unpaid12345',
                'role' => 'owner',
                'business_id' => $inactiveBusiness->id,
                'outlet_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'business_id' => $account['business_id'],
                    'outlet_id' => $account['outlet_id'],
                    'name' => $account['name'],
                    'password' => Hash::make($account['password']),
                    'role' => $account['role'],
                    'is_active' => $account['is_active'],
                ]
            );
        }
    }
}
