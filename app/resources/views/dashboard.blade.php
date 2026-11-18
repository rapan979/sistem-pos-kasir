<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengelola - Es Teh Nusantara</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpg">
    <link rel="shortcut icon" href="/images/logo.jpg">
    <link rel="apple-touch-icon" href="/images/logo.jpg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 text-stone-800 min-h-screen">
    <!-- Navbar atas -->
    <header class="bg-white border-b border-stone-200 px-4 py-3 flex items-center justify-between shadow-sm sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <img src="/images/logo.jpg" alt="Logo" class="w-9 h-9 rounded-xl object-contain border border-stone-200 p-0.5">
            <div>
                <h1 class="font-bold text-stone-900 leading-tight">{{ auth()->user()->business->name }}</h1>
                <p class="text-xs text-stone-500">Mode Kelola (Dashboard Owner)</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Tombol 1-Ketuk Ganti ke Mode Kasir -->
            <a href="{{ route('pos.index') }}" 
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3.5 py-2 rounded-lg flex items-center gap-1.5 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Buka Mode Kasir (POS)</span>
            </a>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-stone-500 hover:text-stone-800 text-xs px-2.5 py-2 border border-stone-300 rounded-lg">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-5xl mx-auto p-4 md:p-6 space-y-6">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-stone-900">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-stone-600 leading-relaxed">
                Pondasi autentikasi dan gerbang lisensi usaha Anda sudah berjalan aktif. Usaha ini terdaftar dengan lisensi <code class="bg-amber-50 px-2 py-0.5 rounded font-mono text-amber-800">{{ auth()->user()->business->license_key }}</code> dan status <span class="font-bold text-emerald-600">AKTIF</span>.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div class="bg-amber-50/60 border border-amber-200 rounded-xl p-4">
                    <p class="text-xs text-amber-800 font-medium">Status Lisensi</p>
                    <p class="text-xl font-bold text-amber-900 capitalize">{{ auth()->user()->business->license_status }}</p>
                    <p class="text-[11px] text-amber-700 mt-1">Berlaku s/d {{ auth()->user()->business->license_expires_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="bg-sky-50/60 border border-sky-200 rounded-xl p-4">
                    <p class="text-xs text-sky-800 font-medium">Jumlah Cabang</p>
                    <p class="text-xl font-bold text-sky-900">{{ auth()->user()->business->outlets()->count() }} Gerai</p>
                    <p class="text-[11px] text-sky-700 mt-1">Multi-outlet siap kelola</p>
                </div>
                <div class="bg-emerald-50/60 border border-emerald-200 rounded-xl p-4">
                    <p class="text-xs text-emerald-800 font-medium">Peran Pengguna</p>
                    <p class="text-xl font-bold text-emerald-900 capitalize">{{ auth()->user()->role }}</p>
                    <p class="text-[11px] text-emerald-700 mt-1">Akses penuh pemilik</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
