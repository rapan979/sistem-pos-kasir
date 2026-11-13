<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk membatasi hak akses berdasarkan role ('owner' atau 'kasir').
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (contoh: 'owner', atau 'owner', 'kasir')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi pemilik usaha.',
            ]);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
