<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Lisensi Usaha - Sistem POS Kasir Es Teh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-amber-50/50 text-stone-800 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-xl border border-amber-200 overflow-hidden">
        <!-- Header status -->
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 p-6 text-white text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur rounded-full mb-3">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Lisensi Usaha Belum Aktif</h1>
            <p class="text-amber-100 text-sm mt-1">Sistem kasir & operasional memerlukan lisensi aktif untuk dapat digunakan.</p>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            @if (session('info'))
                <div class="p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-xl text-sm leading-relaxed">
                    {{ session('info') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Rincian Akun & Usaha -->
            <div class="bg-stone-50 rounded-xl p-4 border border-stone-200 text-sm space-y-2">
                <div class="flex justify-between py-1 border-b border-stone-200">
                    <span class="text-stone-500">Nama Usaha</span>
                    <span class="font-semibold text-stone-800">{{ $business->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-stone-200">
                    <span class="text-stone-500">Kode Lisensi Usaha</span>
                    <span class="font-mono font-bold text-amber-700">{{ $business->license_key }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-stone-200">
                    <span class="text-stone-500">Status Lisensi</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $business->license_status === 'inactive' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $business->license_status === 'inactive' ? 'Belum Diaktifkan' : 'Kedaluwarsa' }}
                    </span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-stone-500">Biaya Langganan</span>
                    <span class="font-bold text-stone-900">Rp 50.000 <span class="text-xs font-normal text-stone-500">/ 30 Hari</span></span>
                </div>
            </div>

            <!-- Opsi 1: Simulasi Pembayaran Lokal (Untuk pengujian PKL & laptop) -->
            <div class="border border-emerald-200 bg-emerald-50/60 rounded-xl p-5 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-emerald-900">Uji Coba Cepat (Simulasi Lokal)</h2>
                        <p class="text-xs text-emerald-700 leading-relaxed mt-0.5">
                            Gunakan tombol ini saat pengujian di laptop lokal untuk mensimulasikan pembayaran lunas tanpa uang asli. Lisensi otomatis aktif selama 30 hari.
                        </p>
                    </div>
                </div>
                <form action="{{ route('license.simulate') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-sm shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simulasikan Pembayaran Sukses (Aktifkan 30 Hari)</span>
                    </button>
                </form>
            </div>

            <!-- Opsi 2: Input Kode Aktivasi Resmi (Dari PT Perkasa Solusindo) -->
            <div class="border border-stone-200 rounded-xl p-5 space-y-3">
                <h2 class="text-sm font-bold text-stone-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Punya Kode Aktivasi Resmi?</span>
                </h2>
                <p class="text-xs text-stone-500">
                    Masukkan kode voucher / aktivasi dari admin perusahaan (Contoh kode tes: <code class="bg-stone-100 px-1.5 py-0.5 rounded font-mono text-amber-700">ETN-AKTIF</code>).
                </p>
                <form action="{{ route('license.activate') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="activation_code" placeholder="Masukkan kode lisensi..."
                        class="flex-1 rounded-lg border border-stone-300 px-3 py-2 text-sm uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                    <button type="submit"
                        class="py-2 px-4 bg-stone-800 hover:bg-stone-900 text-white font-semibold rounded-lg text-sm transition">
                        Aktifkan
                    </button>
                </form>
            </div>

            <!-- Tombol Keluar -->
            <div class="text-center pt-2">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs text-stone-500 hover:text-stone-800 underline">
                        Keluar dari Akun (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
