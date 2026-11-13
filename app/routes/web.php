<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LicenseController;
use Illuminate\Support\Facades\Route;

// Redirect akar '/': Pengalihan cerdas sesuai status login & lisensi
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    if (! $user->business?->hasActiveLicense()) {
        return redirect()->route('license.locked');
    }

    return redirect()->route($user->isOwner() ? 'dashboard' : 'pos.index');
});

// Rute Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Pengguna Terautentikasi (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute Layar Kunci & Aktivasi Lisensi (Bisa diakses saat lisensi belum aktif)
    Route::prefix('license')->name('license.')->group(function () {
        Route::get('/locked', [LicenseController::class, 'locked'])->name('locked');
        Route::post('/simulate', [LicenseController::class, 'simulatePayment'])->name('simulate');
        Route::post('/activate', [LicenseController::class, 'activateWithKey'])->name('activate');
    });

    // Rute Operasional (WAJIB MEMILIKI LISENSI AKTIF)
    Route::middleware('license.active')->group(function () {
        // Area Khusus Owner (Dashboard Pengelola)
        Route::middleware('role:owner')->group(function () {
            Route::get('/dashboard', function () {
                return view('dashboard');
            })->name('dashboard');
        });

        // Area Layar Kasir POS (Bisa diakses oleh Kasir maupun Owner)
        Route::middleware('role:owner,kasir')->group(function () {
            Route::get('/pos', function () {
                return view('pos.index');
            })->name('pos.index');
        });
    });
});
