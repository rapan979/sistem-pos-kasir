<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Controller autentikasi dan pendaftaran usaha UMKM baru.
// Menerapkan aturan: Usaha baru otomatis berlisensi 'inactive' tanpa masa trial.
class AuthController extends Controller
{
    /**
     * Tampilan form login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses otentikasi login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');
        $loginInput = $credentials['email'];
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        $attemptSuccess = false;
        if ($isEmail) {
            $attemptSuccess = Auth::attempt(['email' => $loginInput, 'password' => $credentials['password']], $remember);
        } else {
            // Autentikasi fleksibel: cek username (name) terlebih dahulu, lalu email
            $attemptSuccess = Auth::attempt(['name' => $loginInput, 'password' => $credentials['password']], $remember)
                || Auth::attempt(['email' => $loginInput, 'password' => $credentials['password']], $remember);
        }

        if (! $attemptSuccess) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'Username/email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Validasi status keaktifan akun pengguna
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda dinonaktifkan oleh pemilik usaha.',
            ]);
        }

        // Pengalihan langsung ke Layar Kasir POS
        return redirect()->intended(route('pos.index'));
    }

    /**
     * Tampilan form pendaftaran usaha UMKM baru.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran usaha baru (Tanpa Trial -> status langsung 'inactive').
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:120'],
            'owner_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'business_name.required' => 'Nama usaha es teh wajib diisi.',
            'owner_name.required' => 'Nama pemilik usaha wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        DB::transaction(function () use ($validated, &$user) {
            // 1. Buat data usaha baru dengan status 'inactive' (TANPA TRIAL)
            $business = Business::create([
                'name' => $validated['business_name'],
                'owner_name' => $validated['owner_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'license_key' => 'ETN-' . strtoupper(Str::random(8)),
                'plan_name' => 'Bulanan',
                'license_status' => 'inactive',
                'license_expires_at' => null,
                'tripay_is_sandbox' => true,
            ]);

            // 2. Buat gerai/outlet pertama secara otomatis
            $outlet = Outlet::create([
                'business_id' => $business->id,
                'name' => 'Gerai Utama',
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'],
                'receipt_footer' => 'Terima kasih atas kunjungan Anda!',
                'is_active' => true,
            ]);

            // 3. Buat akun pengguna dengan peran 'owner'
            $user = User::create([
                'business_id' => $business->id,
                'outlet_id' => $outlet->id,
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'owner',
                'is_active' => true,
            ]);
        });

        // Login otomatis setelah registrasi berhasil
        Auth::login($user);
        $request->session()->regenerate();

        // Karena lisensi baru berstatus 'inactive', arahkan langsung ke halaman aktivasi
        return redirect()->route('license.locked')->with('info', 'Pendaftaran usaha berhasil! Silakan selesaikan aktivasi lisensi untuk mulai menggunakan sistem kasir.');
    }

    /**
     * Proses keluar sistem (Logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
