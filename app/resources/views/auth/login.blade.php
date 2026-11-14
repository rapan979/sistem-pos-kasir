<!DOCTYPE html>
<html lang="id" class="h-full bg-[#fbfbfa]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Kasir - Es Teh Perkasa</title>
    <meta name="theme-color" content="#0e2a1d">
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col md:flex-row text-stone-900 font-sans antialiased selection:bg-emerald-100 selection:text-emerald-900">

    <!-- SISI KIRI: BANNER BESAR VISUAL / ELEGAN (Desktop 50%) -->
    <div class="hidden md:flex md:w-1/2 lg:w-3/5 relative bg-[#0e2a1d] overflow-hidden flex-col justify-between p-10 lg:p-14 text-white">
        <!-- Background Overlay Image -->
        <div class="absolute inset-0 opacity-40 mix-blend-overlay">
            <img src="/images/login-banner.jpg" alt="Es Teh Perkasa" class="w-full h-full object-cover">
        </div>
        <!-- Gradient vignette halus -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#091a12] via-transparent to-[#0e2a1d]/80"></div>

        <!-- Header Logo Atas -->
        <div class="relative z-10 flex items-center gap-3">
            <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-12 h-12 rounded-xl object-contain shadow-lg border border-white/10 bg-[#0e2a1d]">
            <div>
                <span class="font-bold text-base tracking-wider text-emerald-100 uppercase">Es Teh Perkasa</span>
                <span class="block text-[11px] text-emerald-300/80 tracking-widest uppercase">Point of Sale System</span>
            </div>
        </div>

        <!-- Kutipan Tagline Bawah -->
        <div class="relative z-10 max-w-lg space-y-3">
            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-semibold tracking-wider uppercase inline-block">
                Cepat &bull; Segar &bull; Andal
            </span>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Pelayanan Cepat, Rasa Otentik, Transaksi Tepat.
            </h1>
            <p class="text-sm text-emerald-100/80 leading-relaxed">
                Sistem kasir cerdas gerai minuman Es Teh Perkasa. Mempercepat transaksi kasir, kalkulasi kembalian otomatis, dan cetak nota instan.
            </p>
        </div>

        <!-- Footer Copyright Sisi Kiri -->
        <div class="relative z-10 text-xs text-emerald-200/50">
            &copy; 2026 Es Teh Perkasa &bull; PT Perkasa Solusindo
        </div>
    </div>

    <!-- SISI KANAN: FORM LOGIN MINIMALIS & ELEGAN (Desktop 50%, Mobile 100%) -->
    <div class="flex-1 flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-16 xl:px-20 bg-white md:bg-[#fbfbfa]">
        <div class="max-w-md w-full mx-auto space-y-6">

            <!-- Logo Mobile & Judul -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-12 h-12 rounded-2xl object-cover shadow-sm border border-stone-200 bg-[#0e2a1d]">
                    <div>
                        <h2 class="text-2xl font-black text-stone-900 tracking-tight">Es Teh Perkasa</h2>
                        <p class="text-xs text-stone-500">Sistem Kasir Operasional Gerai</p>
                    </div>
                </div>
                <div class="pt-3">
                    <h3 class="text-lg font-bold text-stone-900">Masuk Kasir</h3>
                    <p class="text-xs text-stone-500">Masukkan akun kasir Anda untuk membuka mesin kasir dan melayani transaksi.</p>
                </div>
            </div>

            <!-- Notifikasi Pesan -->
            @if (session('success'))
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded-xl space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Email Kasir -->
                <div>
                    <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Alamat Email Kasir
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email', 'kasir@esteh.test') }}"
                            placeholder="kasir@esteh.test"
                            class="w-full min-h-[48px] pl-10 pr-3.5 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 placeholder-stone-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0e2a1d] focus:border-[#0e2a1d]">
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            value="kasir12345"
                            placeholder="••••••••"
                            class="w-full min-h-[48px] pl-10 pr-12 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-900 placeholder-stone-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0e2a1d] focus:border-[#0e2a1d]">
                        
                        <button type="button" id="togglePasswordBtn" aria-label="Lihat kata sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-700 cursor-pointer">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeSlashIcon" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox" checked
                            class="w-4 h-4 text-[#0e2a1d] rounded border-stone-300 focus:ring-[#0e2a1d]">
                        <span class="ml-2 text-xs text-stone-600 font-medium">Ingat perangkat kasir ini</span>
                    </label>
                </div>

                <!-- Tombol Submit Utama -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full min-h-[48px] py-3 px-4 bg-[#0e2a1d] hover:bg-[#153b2a] text-white font-bold text-sm rounded-xl shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                        <span>Buka Mesin Kasir</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Bantuan Cepat Pengujian: 1-Ketuk Akun Kasir Saja -->
            <div class="pt-4 border-t border-stone-200">
                <button type="button" onclick="fillKasir()"
                    class="w-full py-2.5 px-3 rounded-xl border border-stone-200 bg-stone-50 hover:bg-stone-100 text-stone-800 text-xs font-semibold flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer">
                    <span>⚡ Isi Cepat Akun Kasir Uji</span>
                    <span class="text-[10px] text-stone-500 font-mono">(kasir@esteh.test)</span>
                </button>
            </div>

            <p class="text-center text-[11px] text-stone-400 pt-2">
                Es Teh Perkasa &bull; Sistem POS Kasir UMKM
            </p>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.classList.toggle('hidden', isPassword);
                eyeSlashIcon.classList.toggle('hidden', !isPassword);
            });
        }

        function fillKasir() {
            document.getElementById('email').value = 'kasir@esteh.test';
            document.getElementById('password').value = 'kasir12345';
        }
    </script>
</body>
</html>
