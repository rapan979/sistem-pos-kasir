<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Usaha Baru - Sistem POS Kasir Es Teh Nusantara</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-stone-100 text-stone-900 font-sans flex flex-col justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-lg">
        <!-- Logo & Branding -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-600 text-white shadow-md shadow-amber-600/20 mb-3 ring-4 ring-amber-100">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
                    <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/>
                    <line x1="6" y1="2" x2="6" y2="4"/>
                    <line x1="10" y1="2" x2="10" y2="4"/>
                    <line x1="14" y1="2" x2="14" y2="4"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Daftarkan Usaha Es Teh</h1>
            <p class="mt-1 text-sm text-stone-600">Mulai kelola kasir dan stok bahan baku gerai Anda</p>
        </div>

        <!-- Kartu Form Registrasi -->
        <div class="mt-6 bg-white py-8 px-6 sm:px-10 shadow-xl shadow-stone-200/60 rounded-3xl border border-stone-200/80">
            <!-- Peringatan Ramah: Kebijakan Lisensi Tanpa Trial -->
            <div class="mb-5 p-3.5 bg-amber-50/80 border border-amber-200 text-amber-900 text-xs sm:text-sm rounded-xl flex items-start gap-2.5 leading-relaxed">
                <span class="text-base shrink-0">ℹ️</span>
                <div>
                    <strong class="font-semibold block text-amber-950">Kebijakan Lisensi Resmi:</strong>
                    Usaha baru terdaftar dengan status awal <span class="font-bold">belum aktif (tanpa trial)</span>. Setelah mendaftar, Anda dapat langsung mengaktifkan lisensi lewat simulator lokal atau kode aktivasi resmi.
                </div>
            </div>

            <!-- Alert Notifikasi Error -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm rounded-xl flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Usaha -->
                <div>
                    <label for="business_name" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                        Nama Usaha / Brand Minuman <span class="text-rose-500">*</span>
                    </label>
                    <input id="business_name" name="business_name" type="text" required
                        value="{{ old('business_name') }}"
                        placeholder="Contoh: Es Teh Melati Podomoro"
                        class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Nama Pemilik & No HP (2 kolom di tablet/desktop) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="owner_name" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                            Nama Pemilik <span class="text-rose-500">*</span>
                        </label>
                        <input id="owner_name" name="owner_name" type="text" required
                            value="{{ old('owner_name') }}"
                            placeholder="Nama lengkap Anda"
                            class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                            Nomor WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <input id="phone" name="phone" type="tel" required
                            value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Alamat Gerai (Opsional) -->
                <div>
                    <label for="address" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                        Alamat Gerai Utama <span class="text-xs text-stone-400 font-normal">(Opsional)</span>
                    </label>
                    <input id="address" name="address" type="text"
                        value="{{ old('address') }}"
                        placeholder="Contoh: Jl. Pahlawan No. 12, Semarang"
                        class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Email Login -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                        Email Akun Owner <span class="text-rose-500">*</span>
                    </label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                        value="{{ old('email') }}"
                        placeholder="pemilik@usahaanda.com"
                        class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Kata Sandi & Konfirmasi (2 kolom di tablet/desktop) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                            placeholder="Minimal 6 karakter"
                            class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs sm:text-sm font-semibold text-stone-700 mb-1">
                            Ulangi Sandi <span class="text-rose-500">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                            placeholder="Ulangi kata sandi"
                            class="w-full min-h-[44px] px-3.5 py-2 text-stone-900 bg-white border border-stone-300 rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full min-h-[46px] py-2.5 px-4 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold text-sm rounded-xl shadow-md shadow-amber-600/25 transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                        <span>Daftarkan Usaha & Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Tautan Kembali ke Login -->
            <div class="mt-6 text-center text-xs sm:text-sm text-stone-500">
                Sudah memiliki akun usaha?
                <a href="{{ route('login') }}" class="font-bold text-amber-700 hover:text-amber-800 underline underline-offset-2 ml-1">
                    Masuk ke Sistem
                </a>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-stone-400">
            Sistem POS Kasir Es Teh &copy; 2026 PT Perkasa Solusindo &bull; Versi 1.0 (PKL)
        </p>
    </div>
</body>
</html>
