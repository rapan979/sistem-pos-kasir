<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem POS Kasir Es Teh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-amber-50/40 text-stone-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-amber-200 p-8 text-center space-y-4">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 text-amber-800 rounded-full text-3xl">
            🍹
        </div>
        <h1 class="text-2xl font-bold text-stone-900">Sistem POS Kasir Es Teh</h1>
        <p class="text-sm text-stone-500">
            Halaman ini disiapkan sementara. Desain tampilan login akan disesuaikan setelah Anda mengirimkan kriteria & referensi desain.
        </p>

        @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg text-left">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-3 text-left pt-2">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', 'owner@esteh.test') }}" required
                    class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">Kata Sandi</label>
                <input type="password" name="password" value="owner12345" required
                    class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Masuk Sistem
            </button>
        </form>

        <p class="text-xs text-stone-400">
            Akun uji: <code class="text-amber-700 font-mono">owner@esteh.test</code> (Owner Aktif) | <code class="text-amber-700 font-mono">unpaid@esteh.test</code> (Uji Kunci Lisensi)
        </p>
    </div>
</body>
</html>
