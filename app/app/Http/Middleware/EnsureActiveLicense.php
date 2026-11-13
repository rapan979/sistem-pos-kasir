<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware gerbang lisensi: memastikan usaha UMKM memiliki lisensi aktif dan belum kedaluwarsa.
// Jika status 'inactive', 'expired', atau 'suspended', sistem langsung mengunci akses
// dan mengarahkan pengguna ke halaman aktivasi / penyelesaian pembayaran.
class EnsureActiveLicense
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Jika belum login, serahkan ke middleware auth bawaan Laravel.
        if (! $user) {
            return $next($request);
        }

        $business = $user->business;

        // Jika user tidak terikat ke usaha apa pun
        if (! $business) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akun tidak terikat dengan data usaha mana pun.',
            ]);
        }

        // Cek apakah lisensi aktif dan masa berlakunya masih valid
        if (! $business->hasActiveLicense()) {
            // Beri izin jika rute yang diakses adalah halaman lisensi atau logout
            if ($request->routeIs('license.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('license.locked');
        }

        return $next($request);
    }
}
