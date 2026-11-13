<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Controller penanganan gerbang lisensi berbayar dan simulasi pembayaran langganan.
class LicenseController extends Controller
{
    /**
     * Tampilan layar terkunci jika lisensi 'inactive', 'expired', atau 'suspended'.
     */
    public function locked(): View|RedirectResponse
    {
        $user = Auth::user();
        $business = $user->business;

        // Jika lisensi sudah aktif, langsung arahkan ke beranda
        if ($business && $business->hasActiveLicense()) {
            return redirect()->route($user->isOwner() ? 'dashboard' : 'pos.index');
        }

        return view('license.locked', compact('business', 'user'));
    }

    /**
     * Simulasi pembayaran langganan lisensi (Khusus Lingkungan Lokal/Sandbox).
     */
    public function simulatePayment(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $business = $user->business;

        if (! $business) {
            return redirect()->route('login');
        }

        $durationDays = 30;
        $amount = 50000; // Rp 50.000 / bulan paket standar UMKM

        // 1. Catat transaksi pembayaran langganan
        SubscriptionPayment::create([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'reference' => 'SUB-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
            'merchant_ref' => 'INV-LIC-' . time(),
            'plan_name' => 'Bulanan',
            'duration_days' => $durationDays,
            'amount' => $amount,
            'payment_method' => 'SIMULATOR_QRIS',
            'payment_url' => null,
            'qr_url' => null,
            'status' => 'paid',
            'paid_at' => now(),
            'expired_at' => now()->addHours(24),
        ]);

        // 2. Perbarui status lisensi usaha menjadi aktif
        $newExpiry = ($business->license_expires_at && $business->license_expires_at->isFuture())
            ? $business->license_expires_at->addDays($durationDays)
            : now()->addDays($durationDays);

        $business->update([
            'license_status' => 'active',
            'license_expires_at' => $newExpiry,
        ]);

        return redirect()->route($user->isOwner() ? 'dashboard' : 'pos.index')
            ->with('success', 'Pembayaran lisensi berhasil disimulasikan! Status lisensi aktif hingga ' . $newExpiry->format('d M Y H:i'));
    }

    /**
     * Aktivasi manual menggunakan kode lisensi resmi dari PT Perkasa Solusindo.
     */
    public function activateWithKey(Request $request): RedirectResponse
    {
        $request->validate([
            'activation_code' => ['required', 'string', 'min:6'],
        ], [
            'activation_code.required' => 'Kode aktivasi lisensi wajib diisi.',
        ]);

        $user = Auth::user();
        $business = $user->business;
        $code = strtoupper(trim($request->input('activation_code')));

        // Validasi format kode resmi (contoh: ETN-AKTIF, PERKASA-2026, atau kode lisensi usaha itu sendiri)
        $validCodes = [
            'ETN-AKTIF',
            'PERKASA-2026',
            'ES-TEH-JUARA',
            strtoupper($business->license_key),
        ];

        if (! in_array($code, $validCodes, true)) {
            return back()->withErrors([
                'activation_code' => 'Kode aktivasi tidak valid atau sudah tidak berlaku.',
            ]);
        }

        $durationDays = 30;
        $newExpiry = ($business->license_expires_at && $business->license_expires_at->isFuture())
            ? $business->license_expires_at->addDays($durationDays)
            : now()->addDays($durationDays);

        $business->update([
            'license_status' => 'active',
            'license_expires_at' => $newExpiry,
        ]);

        return redirect()->route($user->isOwner() ? 'dashboard' : 'pos.index')
            ->with('success', 'Lisensi berhasil diaktifkan dengan kode resmi hingga ' . $newExpiry->format('d M Y H:i'));
    }
}
