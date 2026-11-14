<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

// Redirect akar '/': Langsung ke Layar Kasir POS jika sudah login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route('pos.index');
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

    // Area Layar Kasir POS (Pusat Operasional Utama Kasir & Owner)
    Route::middleware('role:owner,kasir')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/shift/open', [PosController::class, 'openShift'])->name('pos.shift.open');
        Route::post('/pos/shift/close', [PosController::class, 'closeShift'])->name('pos.shift.close');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::post('/pos/transaction/{id}/void', [PosController::class, 'voidTransaction'])->name('pos.void');
    });

    // Area Khusus Owner (Dashboard Pengelola)
    Route::middleware('role:owner')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });
});
