<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F6F0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Kasir - Es Teh Perkasa</title>
    <meta name="theme-color" content="#1B4332">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col md:flex-row text-[#2D2D2D] font-sans antialiased selection:bg-[#1B4332]/10 selection:text-[#1B4332] bg-[#F8F6F0]">

    <!-- SISI KIRI: SPLIT SCREEN HIJAU GELAP (#1B4332) DENGAN LOGO ES TEH PERKASA -->
    <div class="hidden md:flex md:w-1/2 lg:w-5/12 bg-[#1B4332] flex-col justify-between p-10 lg:p-14 text-white relative overflow-hidden">
        <!-- Dekorasi Subtle Ringan & Elegan -->
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-white/[0.03] pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 rounded-full bg-white/[0.03] pointer-events-none"></div>

        <!-- Header Atas: Nama Usaha -->
        <div class="relative z-10 flex items-center gap-3">
            <span class="px-3 py-1 rounded-full bg-white/10 text-emerald-200 border border-white/15 text-[11px] font-bold tracking-wider uppercase">
                Sistem Kasir POS
            </span>
        </div>

        <!-- Konten Tengah: Logo Es Teh Perkasa Terang & Elegan -->
        <div class="relative z-10 my-auto py-8 text-center flex flex-col items-center">
            <div class="p-4 bg-[#F8F6F0] rounded-3xl shadow-xl border border-white/20 max-w-[240px] mb-6">
                <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-full h-auto object-contain rounded-2xl">
            </div>
            <span class="px-3 py-1 rounded-full bg-[#E88A1A]/20 text-[#E88A1A] border border-[#E88A1A]/30 text-xs font-bold tracking-wider uppercase mb-3 inline-block">
                Rasa Otentik &bull; Pelayanan Cepat
            </span>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight leading-snug max-w-sm">
                Sistem POS (Point of Sale) Kasir Es Teh Perkasa
            </h1>
            <p class="text-xs text-emerald-100/80 mt-2 max-w-xs leading-relaxed">
                Operasional cepat, hitungan kembalian akurat, dan cetak struk instan untuk gerai Es Teh Perkasa.
            </p>
        </div>

        <!-- Footer Bawah Sisi Kiri -->
        <div class="relative z-10 text-[11px] text-emerald-200/50">
            &copy; 2026 Es Teh Perkasa &bull; Single-Store POS
        </div>
    </div>

    <!-- SISI KANAN: LATAR KREM TERANG (#F8F6F0) DENGAN KARTU PUTIH BERSIH (#FFFFFF) -->
    <div class="flex-1 flex flex-col justify-center px-4 py-8 sm:px-8 lg:px-14 xl:px-20 bg-[#F8F6F0]">
        <div class="max-w-md w-full mx-auto">
            
            <!-- Kartu Putih Bersih (Surface Card) -->
            <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-[0_4px_24px_rgba(27,67,50,0.05)] border border-[#EBE6DD] space-y-6">

                <!-- Header Form (Logo Tampil di Layar HP) -->
                <div class="space-y-2">
                    <div class="flex items-center gap-3 md:hidden">
                        <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-12 h-12 rounded-2xl object-contain border border-[#EBE6DD] bg-[#F8F6F0] p-1">
                        <div>
                            <h2 class="text-lg font-extrabold text-[#1B4332] tracking-tight">Es Teh Perkasa</h2>
                            <p class="text-[11px] text-stone-500">Sistem Kasir Operasional</p>
                        </div>
                    </div>
                    <div class="pt-1">
                        <h2 class="text-xl sm:text-2xl font-black text-[#1B4332] tracking-tight">Masuk Kasir</h2>
                        <p class="text-xs text-stone-500 mt-0.5">Silakan masuk dengan akun kasir bertugas untuk membuka sesi transaksi.</p>
                    </div>
                </div>

                <!-- Notifikasi Flash -->
                @if (session('success'))
                    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded-xl space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form Login Kasir -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Input Email Kasir -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Username
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
                                class="w-full min-h-[46px] pl-10 pr-3.5 py-2.5 bg-[#F8F6F0]/60 border border-[#EBE6DD] rounded-xl text-sm font-medium text-[#2D2D2D] placeholder-stone-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]">
                        </div>
                    </div>

                    <!-- Input Kata Sandi -->
                    <div>
                        <label for="password" class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider mb-1.5">
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
                                class="w-full min-h-[46px] pl-10 pr-12 py-2.5 bg-[#F8F6F0]/60 border border-[#EBE6DD] rounded-xl text-sm font-medium text-[#2D2D2D] placeholder-stone-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332]">
                            
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
                                class="w-4 h-4 text-[#1B4332] rounded border-[#EBE6DD] focus:ring-[#1B4332]">
                            <span class="ml-2 text-xs text-stone-600 font-medium">Ingat perangkat kasir ini</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk Utama Solid Hijau Gelap (#1B4332) -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full min-h-[48px] py-3.5 px-4 bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-sm rounded-xl shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                            <span>Masuk ke Layar Kasir</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Bantuan Cepat Pengujian: 1-Ketuk Akun Kasir Saja -->
                <div class="pt-3 border-t border-[#EBE6DD]">
                    <button type="button" onclick="fillKasir()"
                        class="w-full py-2.5 px-3 rounded-xl border border-[#EBE6DD] bg-[#F8F6F0] hover:bg-stone-100 text-[#2D2D2D] text-xs font-semibold flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer">
                        <span class="text-[#E88A1A]"></span>
                        <span>Isi Cepat Akun Kasir Uji</span>
                        <span class="text-[10px] text-stone-500 font-mono">(kasir@esteh.test)</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-[11px] text-stone-400 mt-6">
                Es Teh Perkasa &bull; Sistem Kasir Khusus 1 Gerai
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
