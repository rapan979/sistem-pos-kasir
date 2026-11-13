<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Kasir (POS) - {{ auth()->user()->business->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 text-stone-800 min-h-screen">
    <header class="bg-emerald-700 text-white px-4 py-3 flex items-center justify-between shadow sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🛒</span>
            <div>
                <h1 class="font-bold text-base leading-tight">{{ auth()->user()->business->name }}</h1>
                <p class="text-xs text-emerald-100">
                    Gerai: {{ auth()->user()->outlet?->name ?? 'Semua Gerai (Owner)' }} | Kasir: {{ auth()->user()->name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Khusus Owner: Tombol 1-Ketuk Kembali ke Mode Kelola -->
            @if (auth()->user()->isOwner())
                <a href="{{ route('dashboard') }}" 
                    class="bg-white/20 hover:bg-white/30 text-white text-xs font-semibold px-3 py-2 rounded-lg flex items-center gap-1.5 transition">
                    <span>⚙️</span> Mode Kelola
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-black/20 hover:bg-black/30 text-white text-xs px-2.5 py-2 rounded-lg">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-4 md:p-8 text-center space-y-4">
        <div class="bg-white rounded-2xl p-8 border border-stone-200 shadow-sm space-y-3">
            <span class="text-4xl">🍵</span>
            <h2 class="text-xl font-bold text-stone-900">Layar Kasir POS</h2>
            <p class="text-sm text-stone-600 max-w-lg mx-auto">
                Autentikasi role <span class="font-bold text-emerald-700">"{{ auth()->user()->role }}"</span> dan verifikasi lisensi aktif berhasil! Antarmuka katalog produk dan keranjang belanja kasir akan kita bangun pada tahap sprint kasir berikutnya.
            </p>
        </div>
    </main>
</body>
</html>
