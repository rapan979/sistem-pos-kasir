<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Kasir - Es Teh Nusantara</title>
    <meta name="theme-color" content="#047857">
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 text-stone-900 font-sans antialiased selection:bg-emerald-100 selection:text-emerald-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        
        <!-- Identitas & Logo Brand Es Teh Nusantara -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-800 text-white shadow-md shadow-emerald-900/15 mb-3 border border-emerald-700">
                <!-- Ikon Cangkir Daun Teh Segar -->
                <svg class="w-8 h-8 text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
                    <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/>
                    <line x1="6" y1="2" x2="6" y2="4"/>
                    <line x1="10" y1="2" x2="10" y2="4"/>
                    <line x1="14" y1="2" x2="14" y2="4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight leading-tight">Es Teh Nusantara</h1>
            <p class="mt-1 text-xs sm:text-sm text-stone-600">Sistem Kasir (Point of Sale) Gerai Minuman</p>
        </div>

        <!-- Kartu Masuk Kasir -->
        <div class="mt-6 bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm">
            
            <!-- Notifikasi Berhasil (misal setelah logout) -->
            @if (session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm rounded-xl flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notifikasi Kesalahan Input -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm rounded-xl flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4" autocomplete="on">
                @csrf

                <!-- Input Alamat Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-stone-700 mb-1.5 uppercase tracking-wider">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email', 'kasir@esteh.test') }}"
                            placeholder="nama@esteh.test"
                            class="w-full min-h-[48px] pl-10 pr-3.5 py-2.5 text-stone-900 bg-stone-50 border border-stone-300 rounded-xl text-sm transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700">
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-stone-700 mb-1.5 uppercase tracking-wider">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            value="kasir12345"
                            placeholder="••••••••"
                            class="w-full min-h-[48px] pl-10 pr-12 py-2.5 text-stone-900 bg-stone-50 border border-stone-300 rounded-xl text-sm transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700">
                        <button type="button" id="togglePasswordBtn" aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-700 focus:outline-none cursor-pointer">
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeSlashIcon" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Opsi Ingat Sesi -->
                <div class="flex items-center pt-0.5">
                    <label class="flex items-center cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox" checked
                            class="w-4 h-4 text-emerald-700 rounded border-stone-300 focus:ring-emerald-700">
                        <span class="ml-2 text-xs text-stone-600 font-medium">Ingat saya di perangkat kasir ini</span>
                    </label>
                </div>

                <!-- Tombol Masuk Utama (Emerald 700 Touch-First) -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full min-h-[48px] py-3 px-4 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white font-bold text-sm rounded-xl shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Layar Kasir</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Bantuan Demonstrasi PKL: Tombol 1-Ketuk Akun Kasir / Owner -->
            <div class="mt-6 pt-5 border-t border-stone-100">
                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-wider mb-2.5 text-center">
                    Pilih Cepat Akun Simulasi
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="fillAccount('kasir@esteh.test', 'kasir12345')"
                        class="p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-100 text-emerald-950 font-medium text-center transition cursor-pointer active:scale-95">
                        <span class="block font-bold text-emerald-900">🛒 Akun Kasir</span>
                        <span class="text-[10px] text-emerald-700 mt-0.5 block">Gerai Booth</span>
                    </button>
                    <button type="button" onclick="fillAccount('owner@esteh.test', 'owner12345')"
                        class="p-2.5 rounded-xl border border-amber-200 bg-amber-50/70 hover:bg-amber-100 text-amber-950 font-medium text-center transition cursor-pointer active:scale-95">
                        <span class="block font-bold text-amber-900">👑 Akun Pemilik</span>
                        <span class="text-[10px] text-amber-700 mt-0.5 block">Akses Penuh</span>
                    </button>
                </div>
            </div>

            <!-- Tautan Registrasi Akun Toko Baru -->
            <div class="mt-5 text-center text-xs text-stone-500">
                Belum punya akun kasir?
                <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-800 underline underline-offset-2 ml-1">
                    Daftar Akun Baru
                </a>
            </div>
        </div>

        <!-- Footer Identitas Proyek PKL -->
        <p class="mt-6 text-center text-xs text-stone-400">
            Sistem POS Kasir Es Teh Nusantara &bull; PT Perkasa Solusindo
        </p>
    </div>

    <!-- Script Sederhana: Toggle Password & Autofill Cepat -->
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

        function fillAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
