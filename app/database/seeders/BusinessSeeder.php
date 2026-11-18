<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

// Seeder data usaha UMKM dan cabang (outlets) untuk simulasi lokal.
// Menyediakan 2 contoh usaha:
// 1. Es Teh Nusantara (lisensi AKTIF — siap dites kasir, stok, BOM, dan TriPay Sandbox).
// 2. Es Teh Uji Lisensi (lisensi NONAKTIF — untuk menguji kunci akses tanpa trial).
class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $activeBusiness = Business::updateOrCreate(
            ['license_key' => 'ETN-2026-AKTIF'],
            [
                'name' => 'Es Teh Perkasa',
                'owner_name' => 'Owner Es Teh',
                'phone' => '081111222333',
                'address' => 'Jln. KedungRejo, Wedoroklurak, Candi, Sidoarjo',
                'plan_name' => 'Bulanan',
                'license_status' => 'active',
                'license_expires_at' => now()->addDays(30),
                'tripay_is_sandbox' => true,
            ]
        );

        $activeOutlets = [
            [
                'name' => 'Gerai Pusat',
                'address' => 'Jln. KedungRejo, Wedoroklurak, Candi, Sidoarjo',
                'phone' => '081111222333',
                'receipt_footer' => 'Terima kasih sudah jajan di Es Teh Perkasa!',
                'is_active' => true,
            ],
            [
                'name' => 'Gerai Cabang 2',
                'address' => 'Jl. Sudirman No. 25, Jakarta',
                'phone' => '081111222444',
                'receipt_footer' => 'Es Teh Perkasa Cabang 2 — Segar Setiap Hari',
                'is_active' => true,
            ],
        ];

        foreach ($activeOutlets as $outlet) {
            Outlet::updateOrCreate(
                [
                    'business_id' => $activeBusiness->id,
                    'name' => $outlet['name'],
                ],
                $outlet
            );
        }

        $inactiveBusiness = Business::updateOrCreate(
            ['license_key' => 'ETN-2026-NONAKTIF'],
            [
                'name' => 'Es Teh Uji Lisensi',
                'owner_name' => 'Pemilik Belum Bayar',
                'phone' => '081999888777',
                'address' => 'Jl. Simulasi No. 99',
                'plan_name' => 'Bulanan',
                'license_status' => 'inactive',
                'license_expires_at' => null,
                'tripay_is_sandbox' => true,
            ]
        );

        Outlet::updateOrCreate(
            [
                'business_id' => $inactiveBusiness->id,
                'name' => 'Gerai Utama',
            ],
            [
                'address' => 'Jl. Simulasi No. 99',
                'phone' => '081999888777',
                'receipt_footer' => 'Gerai Uji Kunci Lisensi',
                'is_active' => true,
            ]
        );
    }
}
