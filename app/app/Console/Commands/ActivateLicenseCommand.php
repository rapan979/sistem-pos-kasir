<?php

namespace App\Console\Commands;

use App\Models\Business;
use Illuminate\Console\Command;

// Perintah CLI untuk mengaktifkan atau memperpanjang masa aktif lisensi usaha UMKM.
// Sangat berguna untuk simulasi aktivasi dari pihak PT Perkasa Solusindo tanpa harus buka database.
// Contoh penggunaan: php artisan license:activate ETN-2026-NONAKTIF --days=30
class ActivateLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'license:activate 
                            {license_key : Kode lisensi usaha (misal ETN-2026-NONAKTIF)} 
                            {--days=30 : Durasi penambahan hari masa aktif}';

    /**
     * The console command description.
     */
    protected $description = 'Aktifkan atau perpanjang lisensi usaha UMKM secara langsung';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = trim($this->argument('license_key'));
        $days = (int) $this->option('days');

        $business = Business::where('license_key', $key)->first();

        if (! $business) {
            $this->error("Usaha dengan kode lisensi '{$key}' tidak ditemukan.");
            return Command::FAILURE;
        }

        $newExpiry = ($business->license_expires_at && $business->license_expires_at->isFuture())
            ? $business->license_expires_at->addDays($days)
            : now()->addDays($days);

        $business->update([
            'license_status' => 'active',
            'license_expires_at' => $newExpiry,
        ]);

        $this->info("✓ Lisensi usaha '{$business->name}' ({$business->license_key}) BERHASIL DIAKTIFKAN!");
        $this->line("  Status Baru : active");
        $this->line("  Berlaku s/d : " . $newExpiry->format('d F Y H:i:s'));

        return Command::SUCCESS;
    }
}
