<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Customer;
use Illuminate\Database\Seeder;

// Seeder pelanggan contoh (reguler dan B2B/catering) untuk Es Teh Nusantara.
class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('license_key', 'ETN-2026-AKTIF')->firstOrFail();

        $customers = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'type' => 'reguler',
                'company_name' => null,
            ],
            [
                'name' => 'Siti Rahma',
                'phone' => '081234567891',
                'type' => 'reguler',
                'company_name' => null,
            ],
            [
                'name' => 'Dewi Catering',
                'phone' => '081298765432',
                'type' => 'b2b',
                'company_name' => 'PT Maju Bersama',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::whereNull('business_id')
                ->where('name', $customer['name'])
                ->update(['business_id' => $business->id]);

            Customer::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'name' => $customer['name'],
                ],
                [
                    'phone' => $customer['phone'],
                    'type' => $customer['type'],
                    'company_name' => $customer['company_name'],
                ]
            );
        }
    }
}
