<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F6F0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Layar Kasir - Es Teh Perkasa</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpg">
    <link rel="shortcut icon" href="/images/logo.jpg">
    <link rel="apple-touch-icon" href="/images/logo.jpg">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1B4332">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Gaya Cetak Nota Thermal 58mm Bersih & Terisolasi */
        @media print {
            html, body {
                background: #fff !important;
                color: #000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            body > *:not(#thermal-receipt-area) {
                display: none !important;
            }
            #thermal-receipt-area {
                display: block !important;
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 58mm !important;
                margin: 0 !important;
                padding: 3mm 2mm !important;
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
                color: #000 !important;
                background: #fff !important;
            }
            #thermal-receipt-area * {
                visibility: visible !important;
            }
            @page {
                size: 58mm auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body class="h-full flex flex-col md:flex-row font-sans antialiased text-[#2D2D2D] select-none bg-[#F8F6F0]">

@if (! $activeShift)
    <!-- ==============================================================
         GERBANG MULAI SHIFT KASIR (SEDERHANA & MINIMALIS)
         ============================================================== -->
    <div id="shift-gate-screen" class="fixed inset-0 z-50 bg-[#F8F6F0] flex flex-col items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div class="max-w-sm w-full bg-white rounded-3xl p-6 sm:p-7 border border-[#EBE6DD] shadow-[0_8px_32px_rgba(27,67,50,0.08)] space-y-5 text-center animate-in fade-in zoom-in-95 duration-200">
            <!-- Brand Logo & Kasir Info -->
            <div class="space-y-2.5">
                <div class="p-2.5 bg-[#F8F6F0] rounded-2xl w-16 h-16 mx-auto border border-[#EBE6DD] flex items-center justify-center shadow-2xs">
                    <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-full h-full object-contain rounded-xl">
                </div>
                <div>
                    <h2 class="text-lg font-black text-[#1B4332] tracking-tight">Es Teh Perkasa</h2>
                    <p class="text-xs text-stone-500 font-medium">{{ $outlet->name ?? 'Gerai Pusat' }} &bull; Kasir: {{ $user->name }}</p>
                </div>
                
                <!-- Jam Realtime di Layar Gerbang -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#1B4332]/5 border border-[#1B4332]/10 text-xs font-mono font-bold text-[#1B4332]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="live-clock-text">--:--:-- WIB</span>
                </div>
            </div>

            <!-- Form Buka Shift -->
            <div class="space-y-2.5 text-left">
                <label class="block text-xs font-bold text-stone-700">Modal Awal Kas di Laci (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400 font-bold text-xs">Rp</span>
                    <input type="number" id="gate-opening-cash" value="100000" min="0" step="1000"
                        class="w-full pl-10 pr-3.5 py-2.5 bg-[#F8F6F0] border border-[#EBE6DD] rounded-xl text-base font-mono font-black text-[#1B4332] focus:outline-none focus:ring-2 focus:ring-[#1B4332]">
                </div>

                <!-- Preset Nominal -->
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" onclick="setGateOpeningPreset(0)" class="py-1.5 rounded-lg bg-[#F8F6F0] hover:bg-stone-200/70 border border-[#EBE6DD] text-xs font-mono font-bold text-stone-700 transition active:scale-95 cursor-pointer">Rp 0</button>
                    <button type="button" onclick="setGateOpeningPreset(50000)" class="py-1.5 rounded-lg bg-[#F8F6F0] hover:bg-stone-200/70 border border-[#EBE6DD] text-xs font-mono font-bold text-stone-700 transition active:scale-95 cursor-pointer">50k</button>
                    <button type="button" onclick="setGateOpeningPreset(100000)" class="py-1.5 rounded-lg bg-[#F8F6F0] hover:bg-stone-200/70 border border-[#EBE6DD] text-xs font-mono font-bold text-stone-700 transition active:scale-95 cursor-pointer">100k</button>
                    <button type="button" onclick="setGateOpeningPreset(200000)" class="py-1.5 rounded-lg bg-[#F8F6F0] hover:bg-stone-200/70 border border-[#EBE6DD] text-xs font-mono font-bold text-stone-700 transition active:scale-95 cursor-pointer">200k</button>
                </div>
            </div>

            <!-- Tombol Aksi Buka Shift & Masuk Kasir -->
            <div class="space-y-2 pt-1">
                <button type="button" onclick="submitGateOpenShift()" id="btn-gate-open-shift"
                    class="w-full py-3 px-4 rounded-xl bg-[#1B4332] hover:bg-[#143326] text-white font-extrabold text-sm shadow-md transition active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                    <span>Mulai Shift & Masuk Kasir</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-stone-400 hover:text-stone-600 font-semibold underline cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

    <!-- ==============================================================
         1. SIDEBAR KIRI: NAVIGASI & PROFIL KASIR (DESKTOP/TABLET)
         ============================================================== -->
    <aside class="w-full md:w-56 lg:w-60 bg-white border-b md:border-b-0 md:border-r border-[#EBE6DD] shrink-0 flex flex-col justify-between z-20">
        
        <!-- Bagian Atas: Logo Brand & Menu Navigasi -->
        <div class="p-4 space-y-5">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-10 h-10 rounded-2xl object-contain border border-[#EBE6DD] bg-[#F8F6F0] p-1 shadow-2xs">
                <div>
                    <h1 class="font-extrabold text-sm text-[#1B4332] tracking-tight leading-tight">Es Teh Perkasa</h1>
                    <p class="text-[10px] text-stone-500 font-medium">Sistem Kasir Operasional</p>
                </div>
            </div>

            <!-- Navigasi Menu POS -->
            <nav class="space-y-1">
                <a href="{{ route('pos.index') }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl bg-[#1B4332]/10 text-[#1B4332] font-bold text-xs flex items-center gap-3 transition">
                    <svg class="w-4 h-4 text-[#1B4332]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Menu POS</span>
                </a>

                <!-- Tombol Riwayat Pesanan 7 Hari -->
                <button type="button" onclick="openHistoryModal('all')"
                    class="w-full px-3.5 py-2.5 rounded-xl text-[#2D2D2D] hover:bg-[#F8F6F0] font-semibold text-xs flex items-center justify-between transition active:scale-95 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Riwayat (7 Hari)</span>
                    </div>
                    <span class="text-[10px] font-mono text-stone-400">7 Hari</span>
                </button>

                <!-- Tombol Riwayat Khusus Shift Ini -->
                <button type="button" onclick="openHistoryModal('shift')"
                    class="w-full px-3.5 py-2.5 rounded-xl text-[#2D2D2D] hover:bg-[#F8F6F0] font-semibold text-xs flex items-center justify-between transition active:scale-95 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Riwayat Shift Ini</span>
                    </div>
                    @if ($activeShift)
                        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800">#{{ $activeShift->id }}</span>
                    @else
                        <span class="text-[9px] text-stone-400">Tutup</span>
                    @endif
                </button>

                <!-- Status Shift Kasir & Tombol Tutup Shift -->
                <div class="pt-2">
                    @if ($activeShift)
                        <div class="space-y-2">
                            <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200/80">
                                <div class="flex items-center justify-between">
                                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Shift Aktif
                                    </span>
                                    <span class="text-[10px] font-mono font-bold text-emerald-700">#{{ $activeShift->id }}</span>
                                </div>
                                <div class="text-xs font-mono font-bold text-emerald-900 mt-1">
                                    Modal: Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Tombol Tutup Shift / Akhiri Jualan -->
                            <button type="button" onclick="openCloseShiftModal()"
                                class="w-full py-2 px-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-95 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Tutup Shift / Akhiri Jualan</span>
                            </button>
                        </div>
                    @else
                        <button type="button" onclick="openStartShiftModal()"
                            class="w-full p-2.5 rounded-xl bg-[#E88A1A]/10 hover:bg-[#E88A1A]/20 border border-[#E88A1A]/30 text-left transition active:scale-95 cursor-pointer">
                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-[#E88A1A] uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#E88A1A] animate-pulse"></span>
                                Kasir Belum Buka
                            </div>
                            <div class="text-xs font-bold text-[#1B4332] mt-0.5 underline">
                                Mulai Shift Kasir
                            </div>
                        </button>
                    @endif
                </div>
            </nav>
        </div>

        <!-- Bagian Bawah Sidebar: Profil Kasir & Jam -->
        <div class="p-4 border-t border-[#EBE6DD] bg-[#F8F6F0]/40 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-[#1B4332] text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <span class="block text-xs font-bold text-[#2D2D2D] truncate">{{ $user->name }}</span>
                        <span class="block text-[10px] text-stone-500">{{ $outlet->name ?? 'Gerai Utama' }}</span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" title="Keluar Kasir"
                        class="w-8 h-8 rounded-xl bg-white hover:bg-rose-50 text-stone-400 hover:text-rose-600 border border-[#EBE6DD] flex items-center justify-center transition active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Jam Realtime -->
            <div class="text-center">
                <span id="current-clock" class="font-mono text-[11px] font-semibold text-stone-500">
                    --:--:-- WIB
                </span>
            </div>
        </div>
    </aside>

    <!-- ==============================================================
         2. AREA TENGAH: KATALOG MENU (DATA RIIL DATABASE)
         ============================================================== -->
    <main class="flex-1 flex flex-col overflow-hidden bg-[#F8F6F0] relative">
        
        <!-- Header Atas Tengah: Search Bar -->
        <div class="p-4 sm:p-5 pb-2 shrink-0 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <!-- Pencarian Cepat Menu -->
                <div class="relative w-full max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="menu-search" placeholder="Cari Menu..."
                        class="w-full pl-10 pr-8 py-2.5 bg-white border border-[#EBE6DD] rounded-xl text-xs sm:text-sm text-[#2D2D2D] placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332] shadow-[0_2px_8px_rgba(27,67,50,0.03)] transition">
                    <button type="button" id="clear-search" onclick="clearSearch()" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Filter Kategori Pil (Pill Buttons) Berdasarkan Database Riil -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none" id="category-pills">
                <button type="button" onclick="filterCategory('all')" data-cat="all"
                    class="cat-pill active px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition cursor-pointer bg-[#1B4332] text-white shadow-xs">
                    Semua
                </button>
                @foreach ($categories as $cat)
                    <button type="button" onclick="filterCategory('{{ $cat->id }}')" data-cat="{{ $cat->id }}"
                        class="cat-pill px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-white text-[#2D2D2D] border border-[#EBE6DD] hover:bg-stone-50">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Grid Kartu Menu Minuman (Pure White Cards with Soft Shadow) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-5 pt-2">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-3.5 sm:gap-4" id="products-grid">
                @forelse ($categories as $category)
                    @foreach ($category->products as $product)
                        @php
                            $minPrice = $product->variations->min('price') ?? 0;
                            $maxPrice = $product->variations->max('price') ?? 0;
                            $priceLabel = ($minPrice === $maxPrice) 
                                ? 'Rp ' . number_format($minPrice, 0, ',', '.')
                                : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
                            $imgSrc = $product->image ? asset($product->image) : asset('images/products/es-teh-original.jpg');
                        @endphp
                        <div class="product-card group bg-white rounded-2xl border border-[#EBE6DD] hover:border-stone-300 p-3.5 flex flex-col justify-between shadow-[0_4px_16px_rgba(27,67,50,0.04)] hover:shadow-[0_8px_24px_rgba(27,67,50,0.08)] transition-all duration-200 active:scale-[0.98] cursor-pointer relative"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-cat="{{ $category->id }}"
                            data-image="{{ $imgSrc }}"
                            data-variations='@json($product->variations)'
                            onclick="openProductModal({{ $product->id }})">

                            <div>
                                <!-- Foto Produk Asli (Aspect Ratio Rapi) -->
                                <div class="w-full h-32 sm:h-36 rounded-xl bg-[#F8F6F0] flex items-center justify-center overflow-hidden mb-3 relative">
                                    <img src="{{ $imgSrc }}" alt="{{ $product->name }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                <!-- Judul Nama Minuman -->
                                <h3 class="font-bold text-[#2D2D2D] text-xs sm:text-sm leading-snug line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                            </div>

                            <!-- Baris Harga & Tombol Bulat Tambah (+) -->
                            <div class="mt-2.5 pt-2 border-t border-[#EBE6DD]/60 flex items-center justify-between">
                                <span class="text-xs font-bold text-[#1B4332] font-mono">
                                    {{ $priceLabel }}
                                </span>
                                <!-- Tombol Bulat Hijau Gelap (+) Sesuai Mockup -->
                                <span class="w-8 h-8 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-sm font-bold group-hover:bg-[#143326] transition shadow-2xs active:scale-95">
                                    +
                                </span>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="col-span-full py-12 text-center text-stone-400 text-xs">
                        Belum ada menu produk terdaftar di database.
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- ==============================================================
         3. PANEL KANAN: PESANAN & CHECKOUT (PUTIH BERSIH #FFFFFF)
         ============================================================== -->
    <aside class="w-full md:w-80 lg:w-96 bg-white border-t md:border-t-0 md:border-l border-[#EBE6DD] shrink-0 flex flex-col justify-between shadow-xs z-20">
        
        <!-- Header Keranjang Pesanan -->
        <div class="p-4 border-b border-[#EBE6DD] flex items-center justify-between">
            <div>
                <h2 class="font-bold text-sm text-[#1B4332]">Pesanan Baru</h2>
                <span id="cart-item-count" class="text-[11px] text-stone-500 font-medium">0 item</span>
            </div>
            <button type="button" onclick="clearCart()"
                class="text-xs text-stone-400 hover:text-rose-600 font-semibold transition active:scale-95 cursor-pointer">
                Kosongkan
            </button>
        </div>

        <!-- Daftar Item Pesanan (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2.5 min-h-[160px] max-h-[300px] md:max-h-none" id="cart-scroll-container">
            
            <!-- State Kosong -->
            <div id="empty-cart-state" class="py-12 text-center space-y-2 text-stone-400">
                <div class="w-12 h-12 mx-auto rounded-full bg-[#F8F6F0] flex items-center justify-center text-stone-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-stone-500">Pesanan Masih Kosong</p>
                <p class="text-[11px] text-stone-400 max-w-[200px] mx-auto">Pilih menu es teh di sebelah kiri untuk menambahkan ke pesanan.</p>
            </div>

            <!-- List Item Keranjang Dinamis -->
            <div id="cart-items-list" class="space-y-2.5 hidden"></div>
        </div>

        <!-- Panel Perhitungan & Pembayaran (Sticky Bottom) -->
        <div class="p-4 border-t border-[#EBE6DD] bg-[#F8F6F0]/30 space-y-3 shrink-0">
            
            <!-- Rincian Total -->
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-stone-600">
                    <span>Subtotal</span>
                    <span id="cart-subtotal-display" class="font-mono font-semibold text-[#2D2D2D]">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm sm:text-base font-extrabold text-[#1B4332] pt-1.5 border-t border-[#EBE6DD]">
                    <span>Total Belanja</span>
                    <span id="cart-total-display" class="font-mono text-lg">Rp 0</span>
                </div>
            </div>

            <!-- Pilihan Metode Bayar (Tunai vs QRIS) -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-[#F8F6F0] rounded-xl border border-[#EBE6DD]">
                <button type="button" id="btn-pay-cash" onclick="selectPaymentMethod('tunai')"
                    class="py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer bg-white text-[#1B4332] shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Tunai</span>
                </button>
                <button type="button" id="btn-pay-qris" onclick="selectPaymentMethod('qris')"
                    class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer text-stone-600 hover:text-[#2D2D2D]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>QRIS</span>
                </button>
            </div>

            <!-- Kotak Perhitungan Uang Tunai + Keypad Sentuh (Touch NumPad) -->
            <div id="cash-calculator-box" class="space-y-2.5">
                
                <!-- Pilihan Nominal Cepat Pecahan Uang Kertas -->
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" onclick="setCashAmount(10000)"
                        class="py-1.5 rounded-lg bg-white border border-[#EBE6DD] hover:border-stone-300 text-[11px] font-mono font-bold text-[#2D2D2D] transition active:scale-95 cursor-pointer">
                        10k
                    </button>
                    <button type="button" onclick="setCashAmount(20000)"
                        class="py-1.5 rounded-lg bg-white border border-[#EBE6DD] hover:border-stone-300 text-[11px] font-mono font-bold text-[#2D2D2D] transition active:scale-95 cursor-pointer">
                        20k
                    </button>
                    <button type="button" onclick="setCashAmount(50000)"
                        class="py-1.5 rounded-lg bg-white border border-[#EBE6DD] hover:border-stone-300 text-[11px] font-mono font-bold text-[#2D2D2D] transition active:scale-95 cursor-pointer">
                        50k
                    </button>
                    <button type="button" onclick="setCashAmount(100000)"
                        class="py-1.5 rounded-lg bg-white border border-[#EBE6DD] hover:border-stone-300 text-[11px] font-mono font-bold text-[#2D2D2D] transition active:scale-95 cursor-pointer">
                        100k
                    </button>
                </div>

                <!-- Input Nominal Uang Diterima & Kembalian -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Diterima (Rp)</label>
                        <input type="number" id="cash-received-input" placeholder="0" min="0" oninput="calculateChange()"
                            class="w-full px-2.5 py-1.5 bg-white border border-[#EBE6DD] rounded-xl font-mono font-bold text-sm text-[#2D2D2D] focus:outline-none focus:ring-1 focus:ring-[#1B4332]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Kembalian</label>
                        <div id="change-display-box" class="px-2.5 py-1.5 bg-white border border-[#EBE6DD] rounded-xl font-mono font-bold text-sm text-[#1B4332] flex items-center justify-between">
                            <span id="change-amount-display">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- KEYPAD ANGKA LAYAR SENTUH (TOUCHSCREEN NUMPAD) -->
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" onclick="numpadInput('1')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">1</button>
                    <button type="button" onclick="numpadInput('2')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">2</button>
                    <button type="button" onclick="numpadInput('3')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">3</button>
                    <button type="button" onclick="numpadInput('4')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">4</button>
                    <button type="button" onclick="numpadInput('5')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">5</button>
                    <button type="button" onclick="numpadInput('6')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">6</button>
                    <button type="button" onclick="numpadInput('7')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">7</button>
                    <button type="button" onclick="numpadInput('8')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">8</button>
                    <button type="button" onclick="numpadInput('9')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">9</button>
                    <button type="button" onclick="numpadInput('000')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-bold text-xs text-stone-600 hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">000</button>
                    <button type="button" onclick="numpadInput('0')" class="h-9 rounded-xl bg-white border border-[#EBE6DD] font-mono font-extrabold text-sm text-[#2D2D2D] hover:bg-stone-50 active:scale-95 cursor-pointer shadow-2xs">0</button>
                    <button type="button" onclick="numpadBackspace()" class="h-9 rounded-xl bg-[#F8F6F0] border border-[#EBE6DD] font-bold text-xs text-rose-600 hover:bg-rose-50 active:scale-95 cursor-pointer shadow-2xs">⌫</button>
                </div>
            </div>

            <!-- Kotak Info QRIS (Ditampilkan saat mode QRIS aktif) -->
            <div id="qris-info-box" class="hidden p-3 bg-white border border-[#EBE6DD] rounded-xl text-center space-y-1">
                <span class="inline-block px-2 py-0.5 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-[10px] font-bold uppercase">
                    Simulasi QRIS Gerai
                </span>
                <p class="text-[11px] text-stone-500">Tampilkan QR kepada pelanggan lalu konfirmasi lunas.</p>
            </div>

            <!-- Tombol Utama: BAYAR (Solid Hijau Gelap #1B4332) -->
            <button type="button" id="btn-checkout" onclick="initiateCheckout()" disabled
                class="w-full py-3.5 px-4 bg-[#1B4332] hover:bg-[#143326] disabled:bg-stone-200 disabled:text-stone-400 text-white font-extrabold text-sm rounded-xl shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed">
                <span id="btn-checkout-text">BAYAR</span>
            </button>
        </div>
    </aside>

    <!-- ==============================================================
         4. MODAL DETAIL VARIAN PRODUK (UKURAN & TOPPING)
         ============================================================== -->
    <div id="product-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-[#EBE6DD]">
                <h3 class="font-extrabold text-sm text-[#1B4332]" id="modal-product-name">Varian Minuman</h3>
                <button type="button" onclick="closeProductModal()" class="w-7 h-7 rounded-full bg-[#F8F6F0] text-stone-400 hover:text-stone-700 flex items-center justify-center cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Pilihan Ukuran Cup -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-stone-600 uppercase">Pilih Ukuran Cup</label>
                <div class="grid grid-cols-2 gap-2" id="modal-variations-container"></div>
            </div>

            <!-- Opsi Gula & Es -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Gula</label>
                    <select id="modal-sugar-level" class="w-full px-2.5 py-1.5 bg-[#F8F6F0] border border-[#EBE6DD] rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="Normal">Normal (100%)</option>
                        <option value="Less Sugar">Sedang (70%)</option>
                        <option value="No Sugar">Tanpa Gula</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Es Batu</label>
                    <select id="modal-ice-level" class="w-full px-2.5 py-1.5 bg-[#F8F6F0] border border-[#EBE6DD] rounded-xl text-xs font-semibold focus:outline-none">
                        <option value="Normal">Normal</option>
                        <option value="Less Ice">Sedikit Es</option>
                        <option value="No Ice">Tanpa Es</option>
                    </select>
                </div>
            </div>

            <!-- Subtotal & Konfirmasi Tambah -->
            <div class="pt-3 border-t border-[#EBE6DD] flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-stone-400 block uppercase">Subtotal</span>
                    <span class="font-mono font-extrabold text-base text-[#1B4332]" id="modal-subtotal-price">Rp 0</span>
                </div>
                <button type="button" onclick="addToCartFromModal()"
                    class="py-2.5 px-4 bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-xs rounded-xl shadow-xs active:scale-95 cursor-pointer">
                    + Tambah ke Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         5. MODAL QRIS DINAMIS (MERCHANT RESMI / MIDTRANS)
         ============================================================== -->
    <div id="qris-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 border border-[#EBE6DD] shadow-2xl text-center space-y-3.5 animate-in fade-in zoom-in-95 duration-150">
            <div class="space-y-1">
                <div class="flex items-center justify-center gap-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                        <span id="qris-modal-badge">QRIS Sementara (Simulasi)</span>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-[#2D2D2D]" id="qris-modal-title">Es Teh Perkasa</h3>
                <p class="text-[11px] text-stone-500 font-medium" id="qris-modal-subtitle">Gerai Pusat · QRIS Dinamis Otomatis</p>
            </div>

            <!-- Wadah Kode QR Murni -->
            <div class="p-3 bg-white rounded-2xl border-2 border-[#1B4332] flex items-center justify-center shadow-xs mx-auto w-fit min-w-[210px] min-h-[210px] relative">
                <!-- Indikator Loading -->
                <div id="qris-loading" class="flex flex-col items-center justify-center gap-2 text-stone-400 py-10 hidden">
                    <div class="w-7 h-7 border-3 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs font-semibold">Menyiapkan Kode QR...</span>
                </div>
                <!-- Canvas QR Code -->
                <canvas id="qris-canvas" class="rounded-lg"></canvas>
                <!-- Image QR Code Fallback -->
                <img id="qris-image" class="w-[210px] h-[210px] rounded-lg hidden" alt="QRIS Toko" />
            </div>

            <div class="space-y-0.5">
                <span class="text-[10px] font-bold text-stone-400 block uppercase tracking-wider">Total Tagihan (Nominal Pas)</span>
                <div class="text-2xl font-black font-mono text-[#1B4332]" id="qris-modal-amount">Rp 0</div>
                <p class="text-[11px] text-stone-500 pt-0.5" id="qris-instruction-text">Pelanggan dapat scan via BCA, Mandiri, BRI, GoPay, Dana, OVO, ShopeePay</p>
            </div>

            <!-- Pemberitahuan QRIS Sementara / Simulasi -->
            <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200 text-left">
                <p class="text-[11px] text-amber-900 leading-relaxed font-medium">
                    <span class="font-bold">Simulasi:</span> Kode QR ini merupakan QRIS sementara untuk keperluan simulasi transaksi kasir.
                </p>
            </div>

            @if (config('services.qris.mode') === 'midtrans' && ! config('services.midtrans.is_production', false))
            <!-- Panel Simulator Sandbox (Uji Coba Cepat Tanpa Uang Asli) -->
            <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80 text-left space-y-1.5">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="font-bold text-amber-900">
                        Mode Uji Coba Sandbox:
                    </span>
                    <a href="https://simulator.sandbox.midtrans.com/qris/index" target="_blank" rel="noopener"
                        class="text-[10px] font-bold text-amber-800 underline hover:text-amber-900 flex items-center gap-0.5">
                        <span>Buka Simulator</span>
                        <span>&rarr;</span>
                    </a>
                </div>
                <button type="button" onclick="copyQrisString()" id="btn-copy-qris"
                    class="w-full py-1 px-2 rounded-lg bg-white border border-amber-300 text-amber-900 font-bold text-[10px] hover:bg-amber-100/50 transition cursor-pointer flex items-center justify-center gap-1">
                    <span id="btn-copy-qris-text">Salin Kode QRIS (untuk Simulator)</span>
                </button>
            </div>
            @endif

            <div class="space-y-2 pt-1">
                <button type="button" onclick="confirmQrisPaymentManual()"
                    class="w-full py-3 rounded-xl bg-[#1B4332] hover:bg-[#143326] text-white font-extrabold text-sm shadow-sm transition active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Konfirmasi Pembayaran (Cetak Struk)</span>
                </button>
                <button type="button" onclick="closeQrisModal()"
                    class="w-full py-2 rounded-xl border border-[#EBE6DD] text-stone-600 font-bold text-xs hover:bg-[#F8F6F0] transition active:scale-95 cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         6. MODAL SUKSES & PREVIEW STRUK THERMAL 58MM
         ============================================================== -->
    <div id="receipt-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <!-- Icon & Status Berhasil -->
            <div class="text-center space-y-1">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-[#1B4332]">Transaksi Berhasil!</h3>
                <p class="text-xs font-mono font-bold text-emerald-800" id="success-lunas-amount">Rp 0 Lunas</p>
                <p class="text-[11px] text-stone-400" id="success-breakdown-text">Tunai • Kembalian</p>
            </div>

            <!-- Preview Struk 58mm di Layar -->
            <div class="p-3 bg-[#F8F6F0] rounded-2xl border border-[#EBE6DD] font-mono text-[11px] space-y-2 text-[#2D2D2D] max-h-56 overflow-y-auto">
                <div class="text-center border-b border-[#EBE6DD] pb-2 space-y-0.5">
                    <div class="font-extrabold text-xs text-[#1B4332]">ES TEH PERKASA</div>
                    <div class="text-[9px] text-stone-500">{{ !empty($outlet->address) ? $outlet->address : 'Jln. KedungRejo, Wedoroklurak, Candi, Sidoarjo' }}</div>
                    <div class="text-[9px] text-stone-400">Nota: <span id="rc-invoice">-</span> • <span id="rc-date">-</span></div>
                </div>

                <!-- List Item Nota -->
                <div id="rc-items-list" class="space-y-1.5 border-b border-[#EBE6DD] pb-2"></div>

                <!-- Rincian Biaya Nota -->
                <div class="space-y-0.5 pt-1 text-[10px]">
                    <div class="flex justify-between font-extrabold text-xs text-[#1B4332]">
                        <span>TOTAL</span>
                        <span id="rc-total">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Bayar (<span id="rc-method">Tunai</span>)</span>
                        <span id="rc-paid">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Kembalian</span>
                        <span id="rc-change">Rp 0</span>
                    </div>
                </div>

                <!-- Footer Struk -->
                <div class="text-center pt-2 text-[10px] text-stone-500 border-t border-[#EBE6DD] mt-2 space-y-0.5">
                    <div>Terima Kasih</div>
                    <div class="font-bold text-[#1B4332]">Es Teh Perkasa</div>
                </div>
            </div>

            <!-- Aksi: Cetak Struk & Pesanan Baru -->
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button" onclick="printReceipt()"
                    class="py-2.5 rounded-xl border border-[#1B4332] text-[#1B4332] font-bold text-xs hover:bg-emerald-50 transition active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                <button type="button" onclick="closeReceiptModal()"
                    class="py-2.5 rounded-xl bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-xs transition active:scale-95 cursor-pointer shadow-xs">
                    Pesanan Baru →
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         7. MODAL RIWAYAT PESANAN & NOTA (7 HARI TERAKHIR & SHIFT)
         ============================================================== -->
    <div id="history-modal" onclick="if(event.target === this) closeHistoryModal()"
        class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full border border-[#EBE6DD] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150 my-auto"
            style="max-height: 88vh; max-height: 88dvh; height: min(680px, 88vh);"
            onclick="event.stopPropagation()">
            <!-- 1. Header Modal & Tombol Close (Pinned di Atas) -->
            <div class="p-4 sm:p-5 pb-3.5 border-b border-[#EBE6DD] flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="font-extrabold text-sm sm:text-base text-[#1B4332]" id="history-modal-title">Riwayat Pesanan & Nota</h3>
                    <p class="text-[11px] text-stone-500" id="history-modal-desc">Daftar transaksi kasir 7 hari terakhir (semua shift)</p>
                </div>
                <button type="button" onclick="closeHistoryModal()"
                    class="w-8 h-8 rounded-full bg-[#F8F6F0] hover:bg-stone-200 text-stone-500 hover:text-stone-800 flex items-center justify-center transition active:scale-95 cursor-pointer"
                    title="Tutup (Esc)">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- 2. Toolbar Filter Tab & Search (Pinned di Bawah Header) -->
            <div class="px-4 sm:px-5 pt-3 pb-3 space-y-2.5 shrink-0 bg-white border-b border-[#EBE6DD]/60">
                <!-- Tab Filter Scope: 7 Hari Terakhir & Khusus Shift Ini -->
                @php
                    $shiftTxCount = $recentTransactions->filter(function($tx) use ($activeShift) {
                        return $activeShift && (int)$tx->cashier_shift_id === (int)$activeShift->id;
                    })->count();
                    $allTxCount = $recentTransactions->count();
                @endphp
                <div class="flex items-center gap-1.5 p-1 bg-[#F8F6F0] rounded-xl border border-[#EBE6DD]">
                    <button type="button" onclick="setHistoryScope('all')" id="tab-hist-all"
                        class="tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center transition cursor-pointer bg-white text-[#1B4332] shadow-2xs flex items-center justify-center gap-1.5">
                        <span>7 Hari Terakhir</span>
                        <span id="badge-count-all" class="px-1.5 py-0.5 rounded-full text-[10px] bg-stone-100 text-stone-600 font-mono">{{ $allTxCount }}</span>
                    </button>
                    <button type="button" onclick="setHistoryScope('shift')" id="tab-hist-shift"
                        class="tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-semibold text-center transition cursor-pointer text-stone-600 hover:text-[#2D2D2D] bg-transparent flex items-center justify-center gap-1.5">
                        <span>Khusus Shift Ini</span>
                        @if ($activeShift)
                            <span id="badge-count-shift" class="px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-mono font-bold">{{ $shiftTxCount }}</span>
                        @endif
                    </button>
                </div>

                <!-- Pencarian Cepat Nota -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="history-search-input" oninput="searchHistory()" placeholder="Cari No. Nota atau nama minuman..."
                        class="w-full pl-9 pr-8 py-2 bg-[#F8F6F0]/60 border border-[#EBE6DD] rounded-xl text-xs text-[#2D2D2D] placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1B4332]">
                    <button type="button" id="btn-clear-history-search" onclick="clearHistorySearch()" class="hidden absolute inset-y-0 right-0 pr-2.5 flex items-center text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 3. List Kartu Riwayat Nota Transaksi (Scrollable Terisolasi) -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-2.5 pr-2.5 sm:pr-3.5"
                id="history-items-container"
                style="min-height: 0; overflow-y: auto;">
                @forelse ($recentTransactions as $tx)
                    @php
                        $isCurrentShift = ($activeShift && (int)$tx->cashier_shift_id === (int)$activeShift->id);
                        $isToday = $tx->created_at->isToday();
                        $searchPayload = strtolower($tx->invoice_number . ' ' . $tx->payment_method . ' ' . $tx->details->pluck('product_name')->join(' '));
                    @endphp
                    <div class="history-tx-card p-3.5 rounded-2xl border border-[#EBE6DD] bg-[#F8F6F0]/40 space-y-2 text-xs transition"
                        data-current-shift="{{ $isCurrentShift ? '1' : '0' }}"
                        data-today="{{ $isToday ? '1' : '0' }}"
                        data-search="{{ $searchPayload }}">
                        <!-- Baris Atas: Info Nota, Waktu & Badge Metode -->
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono font-black text-[#1B4332] text-xs">{{ $tx->invoice_number }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $tx->payment_method === 'qris' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ ucfirst($tx->payment_method) }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-mono font-semibold bg-white border border-[#EBE6DD] text-stone-500">
                                        Shift #{{ $tx->cashier_shift_id ?? '-' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-stone-400 mt-0.5">
                                    {{ $tx->created_at->format('d/m/Y H:i') }} WIB &bull; Kasir: {{ $tx->user->name ?? 'Kasir' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-black text-sm text-[#2D2D2D]">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</div>
                                @if ($tx->payment_status === 'batal' || $tx->payment_status === 'void')
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">
                                        Dibatalkan (Void)
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">
                                        Lunas
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Rincian Menu Minuman yang Dipesan -->
                        <div class="p-2.5 rounded-xl bg-white border border-[#EBE6DD]/80 space-y-1">
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Rincian Minuman Dipesan:</span>
                            @foreach ($tx->details as $item)
                                <div class="flex items-center justify-between text-[11px] text-[#2D2D2D]">
                                    <span class="font-medium">&bull; {{ $item->quantity }}x {{ $item->product_name }} ({{ $item->variation_name }})</span>
                                    <span class="font-mono font-bold text-stone-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Baris Tombol Aksi: Cetak Ulang & Void -->
                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button type="button" onclick="reprintTx({{ json_encode([
                                'invoice_number' => $tx->invoice_number,
                                'created_at' => $tx->created_at->format('d/m/Y H:i') . ' WIB',
                                'total_amount' => $tx->total_amount,
                                'paid_amount' => $tx->paid_amount,
                                'change_amount' => $tx->change_amount,
                                'payment_method' => $tx->payment_method,
                                'cashier_name' => $tx->user->name ?? 'Kasir',
                                'items' => $tx->details->map(fn($d) => [
                                    'product_name' => $d->product_name,
                                    'variation_name' => $d->variation_name,
                                    'quantity' => $d->quantity,
                                    'unitPrice' => $d->price_at_transaction,
                                    'subtotal' => $d->subtotal,
                                    'toppings' => [],
                                ])
                            ]) }})"
                                class="px-2.5 py-1.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-white font-bold text-[11px] flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Cetak Nota</span>
                            </button>

                            @if ($tx->payment_status !== 'batal' && $tx->payment_status !== 'void')
                                <button type="button" onclick="promptVoid({{ $tx->id }}, '{{ $tx->invoice_number }}')"
                                    class="px-2.5 py-1.5 rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-[11px] transition active:scale-95 cursor-pointer">
                                    Void Nota
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                @endforelse

                <!-- Pesan Empty State yang Sesuai -->
                <div id="history-empty-all" class="py-10 text-center text-stone-400 text-xs hidden space-y-1">
                    <svg class="w-8 h-8 mx-auto text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="font-medium text-stone-500">Belum ada riwayat transaksi pada 7 hari terakhir.</p>
                </div>

                <div id="history-empty-shift" class="py-10 text-center text-stone-400 text-xs hidden space-y-1">
                    <svg class="w-8 h-8 mx-auto text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-medium text-stone-600">Belum ada transaksi pada shift ini @if ($activeShift)(#{{ $activeShift->id }})@endif.</p>
                    <p class="text-[10px] text-stone-400">Transaksi baru yang diselesaikan di kasir akan muncul di sini.</p>
                </div>

                <div id="history-empty-noshift" class="py-10 text-center text-stone-400 text-xs hidden space-y-1">
                    <svg class="w-8 h-8 mx-auto text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="font-medium text-stone-600">Shift kasir belum dibuka.</p>
                    <p class="text-[10px] text-stone-400">Silakan buka shift kasir terlebih dahulu untuk mencatat transaksi shift aktif.</p>
                </div>

                <div id="history-empty-filter" class="py-10 text-center text-stone-400 text-xs hidden space-y-1">
                    <svg class="w-8 h-8 mx-auto text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="font-medium text-stone-500">Tidak ditemukan nota yang sesuai pencarian.</p>
                </div>
            </div>

            <!-- 4. Footer Modal Pinned (Tutup button & ESC hint) -->
            <div class="px-4 py-3 sm:px-5 border-t border-[#EBE6DD] bg-[#F8F6F0]/70 flex items-center justify-between shrink-0">
                <span class="text-[11px] text-stone-400">
                    Klik di luar atau tekan <kbd class="px-1.5 py-0.5 rounded bg-white border border-[#EBE6DD] font-mono text-[10px] text-stone-600 shadow-2xs">ESC</kbd> untuk menutup
                </span>
                <button type="button" onclick="closeHistoryModal()"
                    class="py-1.5 px-4 rounded-xl bg-white hover:bg-stone-100 border border-[#EBE6DD] text-stone-700 font-bold text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         8. MODAL BUKA SHIFT
         ============================================================== -->
    <div id="start-shift-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4">
            <div class="space-y-1 text-center">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-base text-[#1B4332]">Buka Shift Mesin Kasir</h3>
                <p class="text-xs text-stone-500">Masukkan saldo awal uang kas fisik di laci kasir.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700">Modal Awal Kas (Rp)</label>
                <input type="number" id="shift-opening-cash" value="100000" min="0"
                    class="w-full px-3 py-2.5 bg-[#F8F6F0] border border-[#EBE6DD] rounded-xl text-sm font-mono font-bold text-[#2D2D2D] focus:outline-none focus:ring-2 focus:ring-[#1B4332]">
                
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" onclick="setShiftOpeningPreset(0)" class="py-1 rounded-lg bg-[#F8F6F0] text-[11px] font-bold text-stone-600">Rp 0</button>
                    <button type="button" onclick="setShiftOpeningPreset(50000)" class="py-1 rounded-lg bg-[#F8F6F0] text-[11px] font-bold text-stone-600">50k</button>
                    <button type="button" onclick="setShiftOpeningPreset(100000)" class="py-1 rounded-lg bg-[#F8F6F0] text-[11px] font-bold text-stone-600">100k</button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="document.getElementById('start-shift-modal').classList.add('hidden')"
                    class="py-2.5 rounded-xl border border-[#EBE6DD] text-stone-600 font-bold text-xs hover:bg-[#F8F6F0]">
                    Batal
                </button>
                <button type="button" onclick="submitOpenShift()"
                    class="py-2.5 rounded-xl bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-xs shadow-xs">
                    Mulai Shift
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         9. MODAL TUTUP SHIFT & REKONSILIASI KAS OTOMATIS
         ============================================================== -->
    <div id="close-shift-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 border border-[#EBE6DD] shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="space-y-1 text-center">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-extrabold text-base text-[#1B4332]">Tutup Shift & Rekap Pendapatan</h3>
                <p class="text-xs text-stone-500">Hasil pendapatan shift dihitung otomatis oleh sistem.</p>
            </div>

            <!-- Rekap Pendapatan Otomatis (Tunai & QRIS) -->
            <div class="p-3.5 rounded-2xl bg-[#F8F6F0] border border-[#EBE6DD] space-y-2 text-xs">
                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Ringkasan Pendapatan Shift Ini:</span>
                
                <div class="flex justify-between text-stone-600">
                    <span>Modal Awal Laci</span>
                    <span class="font-mono font-bold text-stone-800" id="close-shift-opening">Rp {{ number_format($activeShift->opening_cash ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-stone-600">
                    <span>Penjualan Tunai</span>
                    <span class="font-mono font-bold text-emerald-800" id="close-shift-cash">+ Rp {{ number_format($shiftCashSales ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-stone-600">
                    <span>Penjualan QRIS</span>
                    <span class="font-mono font-bold text-amber-800" id="close-shift-qris">+ Rp {{ number_format($shiftQrisSales ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <div class="pt-2 border-t border-[#EBE6DD] flex justify-between items-center text-xs">
                    <span class="font-bold text-stone-700">Total Omzet Penjualan (Tunai + QRIS)</span>
                    <span class="font-mono font-black text-sm text-[#1B4332]" id="close-shift-total-sales">Rp {{ number_format(($shiftCashSales ?? 0) + ($shiftQrisSales ?? 0), 0, ',', '.') }}</span>
                </div>

                <!-- Kartu Kas Fisik Seharusnya -->
                <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 flex justify-between items-center text-xs text-emerald-950 font-semibold">
                    <span>Uang Fisik Seharusnya di Laci:</span>
                    <span class="font-mono font-black text-sm text-emerald-900" id="close-shift-expected">Rp {{ number_format($expectedCash ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Input Uang Fisik Aktual di Laci (Auto Pre-filled) -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-bold text-stone-700">Total Uang Fisik Kasir (Rp)</label>
                    <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-100/60 px-2 py-0.5 rounded-full">Otomatis Terisi</span>
                </div>
                <input type="number" id="shift-actual-cash" value="{{ $expectedCash ?? 0 }}" min="0" oninput="calculateShiftDiff()"
                    class="w-full px-3 py-2.5 bg-white border border-[#EBE6DD] rounded-xl text-sm font-mono font-bold text-[#2D2D2D] focus:outline-none focus:ring-2 focus:ring-[#1B4332]">
                <div id="close-shift-diff-box" class="text-[11px] font-mono text-stone-500 flex justify-between pt-0.5">
                    <span>Selisih Kas Laci:</span>
                    <span id="close-shift-diff-text" class="font-bold text-emerald-700">Rp 0 (Cocok)</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button" onclick="closeCloseShiftModal()"
                    class="py-2.5 rounded-xl border border-[#EBE6DD] text-stone-600 font-bold text-xs hover:bg-[#F8F6F0] transition active:scale-95 cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitCloseShift()"
                    class="py-2.5 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs shadow-xs transition active:scale-95 cursor-pointer">
                    Konfirmasi Tutup Kasir
                </button>
            </div>
        </div>
    </div>

    <!-- ELEMEN ISOLASI CETAK NOTA THERMAL 58MM (@media print) -->
    <div id="thermal-receipt-area" class="hidden print:block">
        <div style="text-align: center; margin-bottom: 6px;">
            <div style="font-size: 13px; font-weight: bold;">ES TEH PERKASA</div>
            <div style="font-size: 9px;">{{ !empty($outlet->address) ? $outlet->address : 'Jln. KedungRejo, Wedoroklurak, Candi, Sidoarjo' }}</div>
            <div style="font-size: 9px; margin-top: 3px;">--------------------------------</div>
        </div>
        <div style="font-size: 9px; margin-bottom: 5px;">
            <div>Nota   : <span id="print-rc-invoice">-</span></div>
            <div>Waktu  : <span id="print-rc-date">-</span></div>
            <div>Kasir  : <span id="print-rc-cashier">{{ $user->name }}</span></div>
            <div>--------------------------------</div>
        </div>
        <div id="print-rc-items" style="font-size: 9px; margin-bottom: 5px;"></div>
        <div style="font-size: 9px; border-top: 1px dashed #000; padding-top: 4px;">
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 11px;">
                <span>TOTAL:</span>
                <span id="print-rc-total">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 2px;">
                <span>Bayar:</span>
                <span id="print-rc-paid">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kembalian:</span>
                <span id="print-rc-change">Rp 0</span>
            </div>
            <div style="text-align: center; margin-top: 8px; font-size: 9px;">
                <div>Terima Kasih</div>
                <div>Es Teh Perkasa</div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         LOGIKA APLIKASI KASIR (VANILLA JS REAKTIF & CEPAT)
         ============================================================== -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let categoriesData = @json($categories);
        let cart = [];
        let activePaymentMethod = 'tunai';
        let currentModalProduct = null;
        let selectedVariation = null;

        // 1. Jam Realtime WIB
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':') + ' WIB';
            const fullClock = `${dayName}, ${day} ${monthName} ${year} • ${timeStr}`;

            document.querySelectorAll('.live-clock-text').forEach(el => {
                el.textContent = fullClock;
            });
            const sidebarClock = document.getElementById('current-clock');
            if (sidebarClock) sidebarClock.textContent = fullClock;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Filter Kategori & Pencarian
        function filterCategory(catId) {
            document.querySelectorAll('.cat-pill').forEach(btn => {
                if (btn.dataset.cat === String(catId)) {
                    btn.className = 'cat-pill active px-4 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition cursor-pointer bg-[#1B4332] text-white shadow-xs';
                } else {
                    btn.className = 'cat-pill px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-white text-[#2D2D2D] border border-[#EBE6DD] hover:bg-stone-50';
                }
            });

            applyFilters();
        }

        const searchInput = document.getElementById('menu-search');
        const clearSearchBtn = document.getElementById('clear-search');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (clearSearchBtn) {
                    clearSearchBtn.classList.toggle('hidden', !this.value);
                }
                applyFilters();
            });
        }

        function clearSearch() {
            if (searchInput) {
                searchInput.value = '';
                clearSearchBtn.classList.add('hidden');
                applyFilters();
            }
        }

        function applyFilters() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const activeBtn = document.querySelector('.cat-pill.active');
            const activeCat = activeBtn ? activeBtn.dataset.cat : 'all';

            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const cat = card.dataset.cat;

                const matchCat = (activeCat === 'all' || activeCat === cat);
                const matchQuery = (!query || name.includes(query));

                card.style.display = (matchCat && matchQuery) ? 'flex' : 'none';
            });
        }

        // 3. Modal Varian Produk
        function openProductModal(productId) {
            let found = null;
            for (const c of categoriesData) {
                const p = c.products.find(x => x.id === productId);
                if (p) { found = p; break; }
            }
            if (!found) return;

            currentModalProduct = found;
            document.getElementById('modal-product-name').textContent = found.name;

            const varContainer = document.getElementById('modal-variations-container');
            varContainer.innerHTML = '';

            found.variations.forEach((v, idx) => {
                const isFirst = (idx === 0);
                if (isFirst) selectedVariation = v;

                const div = document.createElement('div');
                div.className = `p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice ${isFirst ? 'border-[#1B4332] bg-[#1B4332]/10 text-[#1B4332] font-bold' : 'border-[#EBE6DD] bg-white text-stone-700'}`;
                div.dataset.id = v.id;
                div.dataset.price = v.price;
                div.dataset.name = v.variation_name;
                div.onclick = () => selectVariation(v);
                div.innerHTML = `
                    <div class="font-bold text-xs">${v.variation_name}</div>
                    <div class="text-xs font-mono font-black mt-0.5">Rp ${v.price.toLocaleString('id-ID')}</div>
                `;
                varContainer.appendChild(div);
            });

            document.querySelectorAll('input[name="modal_toppings"]').forEach(cb => cb.checked = false);
            document.getElementById('modal-sugar-level').value = 'Normal';
            document.getElementById('modal-ice-level').value = 'Normal';

            recalculateModalPrice();
            document.getElementById('product-modal').classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('product-modal').classList.add('hidden');
            currentModalProduct = null;
            selectedVariation = null;
        }

        function selectVariation(v) {
            selectedVariation = v;
            document.querySelectorAll('.variation-choice').forEach(el => {
                if (parseInt(el.dataset.id) === v.id) {
                    el.className = 'p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice border-[#1B4332] bg-[#1B4332]/10 text-[#1B4332] font-bold';
                } else {
                    el.className = 'p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice border-[#EBE6DD] bg-white text-stone-700';
                }
            });
            recalculateModalPrice();
        }

        function recalculateModalPrice() {
            if (!selectedVariation) return;

            let total = selectedVariation.price;
            document.querySelectorAll('input[name="modal_toppings"]:checked').forEach(cb => {
                total += parseInt(cb.dataset.price || 0);
            });

            document.getElementById('modal-subtotal-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function addToCartFromModal() {
            if (!currentModalProduct || !selectedVariation) return;

            const selectedToppings = [];
            document.querySelectorAll('input[name="modal_toppings"]:checked').forEach(cb => {
                selectedToppings.push({
                    id: parseInt(cb.value),
                    name: cb.dataset.name,
                    price: parseInt(cb.dataset.price)
                });
            });

            const sugar = document.getElementById('modal-sugar-level').value;
            const ice = document.getElementById('modal-ice-level').value;

            const toppingKey = selectedToppings.map(t => t.id).sort().join('-');
            const itemKey = `${selectedVariation.id}_${toppingKey}_${sugar}_${ice}`;

            const existingIndex = cart.findIndex(i => i.itemKey === itemKey);

            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
                cart[existingIndex].subtotal = (cart[existingIndex].unitPrice * cart[existingIndex].quantity);
            } else {
                let unitPrice = selectedVariation.price;
                selectedToppings.forEach(t => unitPrice += t.price);

                cart.push({
                    itemKey: itemKey,
                    product_id: currentModalProduct.id,
                    product_name: currentModalProduct.name,
                    variation_id: selectedVariation.id,
                    variation_name: selectedVariation.variation_name,
                    unitPrice: unitPrice,
                    basePrice: selectedVariation.price,
                    toppings: selectedToppings,
                    sugar_level: sugar,
                    ice_level: ice,
                    quantity: 1,
                    subtotal: unitPrice
                });
            }

            closeProductModal();
            renderCart();
        }

        // 4. Render Keranjang Belanja
        function renderCart() {
            const listContainer = document.getElementById('cart-items-list');
            const emptyState = document.getElementById('empty-cart-state');
            const countBadge = document.getElementById('cart-item-count');
            const totalDisplay = document.getElementById('cart-total-display');
            const subtotalDisplay = document.getElementById('cart-subtotal-display');
            const checkoutBtn = document.getElementById('btn-checkout');

            if (cart.length === 0) {
                emptyState.classList.remove('hidden');
                listContainer.classList.add('hidden');
                listContainer.innerHTML = '';
                countBadge.textContent = '0 item';
                totalDisplay.textContent = 'Rp 0';
                subtotalDisplay.textContent = 'Rp 0';
                checkoutBtn.disabled = true;
                calculateChange();
                return;
            }

            emptyState.classList.add('hidden');
            listContainer.classList.remove('hidden');
            listContainer.innerHTML = '';

            let grandTotal = 0;
            let totalCups = 0;

            cart.forEach((item, index) => {
                grandTotal += item.subtotal;
                totalCups += item.quantity;

                const toppingText = item.toppings.length > 0 
                    ? '+ ' + item.toppings.map(t => t.name).join(', ') 
                    : '';
                const noteText = `${item.sugar_level}, ${item.ice_level}`;

                const div = document.createElement('div');
                div.className = 'p-3 rounded-2xl border border-[#EBE6DD] bg-white hover:border-stone-300 transition flex items-center justify-between gap-2 shadow-2xs';
                div.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-[#2D2D2D] truncate">
                            ${item.product_name} <span class="text-stone-500 font-semibold">(${item.variation_name})</span>
                        </div>
                        <div class="text-[10px] text-stone-400 truncate mt-0.5">
                            ${toppingText ? `<span class="text-[#E88A1A] font-semibold">${toppingText} • </span>` : ''}
                            ${noteText}
                        </div>
                        <div class="text-xs font-mono font-extrabold text-[#1B4332] mt-1">
                            Rp ${item.subtotal.toLocaleString('id-ID')}
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="changeQty(${index}, -1)"
                            class="w-7 h-7 rounded-lg bg-[#F8F6F0] hover:bg-stone-200 text-[#2D2D2D] font-bold text-xs flex items-center justify-center active:scale-90 cursor-pointer">
                            -
                        </button>
                        <span class="w-6 text-center font-bold text-xs font-mono text-[#2D2D2D]">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${index}, 1)"
                            class="w-7 h-7 rounded-lg bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-xs flex items-center justify-center active:scale-90 cursor-pointer">
                            +
                        </button>
                        <button type="button" onclick="removeItem(${index})" title="Hapus"
                            class="w-7 h-7 ml-1 text-stone-400 hover:text-rose-600 flex items-center justify-center cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                listContainer.appendChild(div);
            });

            countBadge.textContent = `${totalCups} item`;
            totalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            subtotalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            checkoutBtn.disabled = false;

            calculateChange();
        }

        function changeQty(index, delta) {
            if (!cart[index]) return;
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            } else {
                cart[index].subtotal = cart[index].unitPrice * cart[index].quantity;
            }
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            if (confirm('Kosongkan semua pesanan di keranjang?')) {
                cart = [];
                renderCart();
            }
        }

        // 5. Metode Bayar & Kalkulator Tunai
        function selectPaymentMethod(method) {
            activePaymentMethod = (method === 'cash' || method === 'tunai') ? 'tunai' : 'qris';
            const btnCash = document.getElementById('btn-pay-cash');
            const btnQris = document.getElementById('btn-pay-qris');
            const calcBox = document.getElementById('cash-calculator-box');
            const qrisBox = document.getElementById('qris-info-box');
            const checkoutText = document.getElementById('btn-checkout-text');

            if (activePaymentMethod === 'tunai') {
                btnCash.className = 'py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer bg-white text-[#1B4332] shadow-2xs';
                btnQris.className = 'py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer text-stone-600 hover:text-[#2D2D2D]';
                calcBox.classList.remove('hidden');
                qrisBox.classList.add('hidden');
                if (checkoutText) checkoutText.textContent = 'BAYAR TUNAI';
            } else {
                btnQris.className = 'py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer bg-white text-[#1B4332] shadow-2xs';
                btnCash.className = 'py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer text-stone-600 hover:text-[#2D2D2D]';
                calcBox.classList.add('hidden');
                qrisBox.classList.remove('hidden');
                if (checkoutText) checkoutText.textContent = 'TAMPILKAN QRIS';
            }

            calculateChange();
        }

        function setCashExact() {
            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);
            const input = document.getElementById('cash-received-input');
            input.value = grandTotal;
            calculateChange();
        }

        function setCashAmount(val) {
            const input = document.getElementById('cash-received-input');
            input.value = val;
            calculateChange();
        }

        // Keypad Layar Sentuh Handlers
        function numpadInput(digit) {
            const input = document.getElementById('cash-received-input');
            let current = input.value ? String(input.value) : '';
            if (digit === '000') {
                if (current.length > 0 && current !== '0') {
                    current += '000';
                }
            } else {
                if (current === '0') current = '';
                current += digit;
            }
            input.value = current;
            calculateChange();
        }

        function numpadBackspace() {
            const input = document.getElementById('cash-received-input');
            let current = input.value ? String(input.value) : '';
            if (current.length > 0) {
                current = current.slice(0, -1);
                input.value = current;
                calculateChange();
            }
        }

        function calculateChange() {
            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);
            const checkoutBtn = document.getElementById('btn-checkout');
            const input = document.getElementById('cash-received-input');
            const changeDisplay = document.getElementById('change-amount-display');
            const changeBox = document.getElementById('change-display-box');

            if (activePaymentMethod === 'qris') {
                checkoutBtn.disabled = (cart.length === 0);
                return;
            }

            const received = parseInt(input.value || 0);

            if (cart.length === 0) {
                changeDisplay.textContent = 'Rp 0';
                checkoutBtn.disabled = true;
                return;
            }

            const diff = received - grandTotal;

            if (diff >= 0) {
                changeDisplay.textContent = 'Rp ' + diff.toLocaleString('id-ID');
                changeBox.className = 'px-2.5 py-1.5 bg-emerald-50 border border-emerald-300 rounded-xl font-mono font-bold text-sm text-emerald-800 flex items-center justify-between';
                checkoutBtn.disabled = false;
            } else {
                const lack = Math.abs(diff);
                changeDisplay.textContent = '- Rp ' + lack.toLocaleString('id-ID');
                changeBox.className = 'px-2.5 py-1.5 bg-rose-50 border border-rose-300 rounded-xl font-mono font-bold text-sm text-rose-700 flex items-center justify-between';
                checkoutBtn.disabled = true;
            }
        }

        // 5b. Logika Integrasi QRIS (Merchant Dinamis / Midtrans Gateway)
        const qrisConfig = {
            mode: "{{ config('services.qris.mode', 'merchant') }}",
            merchantName: "{{ config('services.qris.merchant_name', 'Es Teh Perkasa') }}",
            baseStaticQris: "{{ config('services.qris.base_string') }}",
        };

        let currentMidtransOrderId = null;
        let currentMidtransQrString = null;
        let qrisPollingInterval = null;

        // CRC16-CCITT calculation for EMVCo QRIS (Polynomial 0x1021, init 0xFFFF)
        function crc16(str) {
            let crc = 0xFFFF;
            for (let c = 0; c < str.length; c++) {
                crc ^= str.charCodeAt(c) << 8;
                for (let i = 0; i < 8; i++) {
                    if ((crc & 0x8000) !== 0) {
                        crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
                    } else {
                        crc = (crc << 1) & 0xFFFF;
                    }
                }
            }
            let hex = crc.toString(16).toUpperCase();
            return hex.padStart(4, '0');
        }

        // Generate Dynamic QRIS EMVCo by inserting Tag 54 (amount) and recalculating CRC16
        function buildDynamicQrisString(staticQris, amount) {
            let qris = staticQris.replace("010211", "010212");
            qris = qris.slice(0, -8);
            const amountStr = String(amount);
            const tag54 = "54" + String(amountStr.length).padStart(2, '0') + amountStr;
            const splitIndex = qris.indexOf("5802ID");
            if (splitIndex !== -1) {
                qris = qris.slice(0, splitIndex) + tag54 + qris.slice(splitIndex);
            }
            qris += "6304";
            const newCrc = crc16(qris);
            return qris + newCrc;
        }

        function openQrisModal(amount) {
            if (cart.length === 0) return;

            const modal = document.getElementById('qris-modal');
            const amountEl = document.getElementById('qris-modal-amount');
            const loadingEl = document.getElementById('qris-loading');
            const canvasEl = document.getElementById('qris-canvas');
            const imgEl = document.getElementById('qris-image');

            amountEl.textContent = 'Rp ' + amount.toLocaleString('id-ID');
            modal.classList.remove('hidden');

            if (qrisConfig.mode === 'midtrans') {
                openMidtransQrisModal(amount);
            } else {
                // Mode Merchant Toko: Generate QR Dinamis dengan Nominal Pas
                loadingEl.classList.add('hidden');
                canvasEl.classList.remove('hidden');
                if (imgEl) imgEl.classList.add('hidden');

                const dynamicString = buildDynamicQrisString(qrisConfig.baseStaticQris, amount);
                currentMidtransQrString = dynamicString;

                if (window.QRCode && canvasEl) {
                    window.QRCode.toCanvas(canvasEl, dynamicString, {
                        width: 210,
                        margin: 1,
                        color: {
                            dark: '#000000',
                            light: '#ffffff'
                        }
                    }, function (err) {
                        if (err) console.error('Gagal render QRIS merchant:', err);
                    });
                }
            }
        }

        async function openMidtransQrisModal(amount) {
            if (cart.length === 0) return;

            const modal = document.getElementById('qris-modal');
            const amountEl = document.getElementById('qris-modal-amount');
            const loadingEl = document.getElementById('qris-loading');
            const canvasEl = document.getElementById('qris-canvas');
            const imgEl = document.getElementById('qris-image');
            const btnCopyText = document.getElementById('btn-copy-qris-text');

            amountEl.textContent = 'Rp ' + amount.toLocaleString('id-ID');
            loadingEl.classList.remove('hidden');
            canvasEl.classList.add('hidden');
            if (imgEl) {
                imgEl.classList.add('hidden');
                imgEl.src = '';
            }
            if (btnCopyText) btnCopyText.textContent = 'Salin Kode QRIS (untuk Simulator)';
            modal.classList.remove('hidden');

            // Hentikan polling sebelumnya jika ada
            stopQrisPolling();

            // Request pembuatan QRIS ke backend Midtrans
            const payload = {
                items: cart.map(i => ({
                    variation_id: i.variation_id,
                    quantity: i.quantity,
                    sugar_level: i.sugar_level,
                    ice_level: i.ice_level,
                    topping_ids: i.toppings.map(t => t.id)
                }))
            };

            try {
                const res = await fetch('{{ route("pos.qris.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    alert(data.message || 'Gagal membuat tagihan QRIS Midtrans.');
                    closeQrisModal(false);
                    return;
                }

                currentMidtransOrderId = data.order_id;
                currentMidtransQrString = data.qr_string;

                // Render QR code ke canvas menggunakan qrcode npm bundle
                if (data.qr_string && window.QRCode) {
                    window.QRCode.toCanvas(canvasEl, data.qr_string, {
                        width: 210,
                        margin: 1,
                        color: {
                            dark: '#000000',
                            light: '#ffffff'
                        }
                    }, function (err) {
                        loadingEl.classList.add('hidden');
                        if (err) {
                            console.error('Gagal render QR canvas:', err);
                            if (data.qr_url && imgEl) {
                                imgEl.src = data.qr_url;
                                imgEl.classList.remove('hidden');
                            }
                        } else {
                            canvasEl.classList.remove('hidden');
                        }
                    });
                } else if (data.qr_url && imgEl) {
                    loadingEl.classList.add('hidden');
                    imgEl.src = data.qr_url;
                    imgEl.classList.remove('hidden');
                } else {
                    loadingEl.classList.add('hidden');
                    canvasEl.classList.remove('hidden');
                }

                // Mulai polling status pembayaran ke Midtrans
                startQrisPolling(data.order_id);

            } catch (err) {
                console.error('Gagal memproses QRIS Midtrans:', err);
                alert('Terjadi kesalahan jaringan saat membuat QRIS.');
                closeQrisModal(false);
            }
        }

        function startQrisPolling(orderId) {
            stopQrisPolling();

            qrisPollingInterval = setInterval(async () => {
                if (!currentMidtransOrderId || currentMidtransOrderId !== orderId) {
                    stopQrisPolling();
                    return;
                }

                try {
                    const res = await fetch(`/pos/qris/status/${orderId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.success && data.status === 'settlement') {
                        // Pembayaran terdeteksi LUNAS!
                        stopQrisPolling();

                        // Tutup modal QRIS
                        closeQrisModal(false);

                        // Ambil rincian transaksi untuk ditampilkan & dicetak
                        const tx = data.transaction;
                        const txItems = tx.items || [];

                        // Tampilkan modal struk
                        showReceiptModal(tx, txItems);

                        // Tambahkan juga ke daftar riwayat pesanan (shift aktif & 7 hari)
                        prependHistoryCard(tx, txItems);

                        // Reset keranjang belanja
                        cart = [];
                        const receivedInput = document.getElementById('cash-received-input');
                        if (receivedInput) receivedInput.value = '';
                        renderCart();

                        // Auto-print struk thermal secara otomatis
                        setTimeout(() => {
                            printReceipt();
                        }, 600);
                    }
                } catch (err) {
                    console.warn('Gagal cek status QRIS:', err);
                }
            }, 2500);
        }

        function stopQrisPolling() {
            if (qrisPollingInterval) {
                clearInterval(qrisPollingInterval);
                qrisPollingInterval = null;
            }
        }

        function closeQrisModal(cancelOnServer = true) {
            stopQrisPolling();
            const modal = document.getElementById('qris-modal');
            if (modal) modal.classList.add('hidden');

            const orderToCancel = currentMidtransOrderId;
            currentMidtransOrderId = null;
            currentMidtransQrString = null;

            if (cancelOnServer && orderToCancel) {
                fetch(`/pos/qris/cancel/${orderToCancel}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                }).catch(() => {});
            }
        }

        function copyQrisString() {
            if (!currentMidtransQrString) {
                alert('Kode QRIS belum siap. Mohon tunggu sesaat.');
                return;
            }

            navigator.clipboard.writeText(currentMidtransQrString).then(() => {
                const btnText = document.getElementById('btn-copy-qris-text');
                if (btnText) {
                    const originalText = btnText.textContent;
                    btnText.textContent = 'Berhasil Disalin!';
                    setTimeout(() => {
                        btnText.textContent = originalText;
                    }, 2500);
                }
            }).catch(err => {
                console.error('Gagal menyalin:', err);
                prompt('Salin kode QRIS manual:', currentMidtransQrString);
            });
        }

        function confirmQrisPaymentManual() {
            stopQrisPolling();
            closeQrisModal(false);
            executeCheckout();
        }

        // 6. Alur Checkout
        function initiateCheckout() {
            if (cart.length === 0) return;

            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);

            if (activePaymentMethod === 'qris') {
                openQrisModal(grandTotal);
                return;
            }

            executeCheckout();
        }

        async function executeCheckout() {
            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);
            const receivedInput = document.getElementById('cash-received-input');
            const isCash = (activePaymentMethod === 'tunai' || activePaymentMethod === 'cash');
            const paidAmount = isCash
                ? parseInt(receivedInput.value || 0)
                : grandTotal;

            if (isCash && paidAmount < grandTotal) {
                alert('Uang pembayaran kurang dari total belanja.');
                return;
            }

            const checkoutBtn = document.getElementById('btn-checkout');
            const checkoutText = document.getElementById('btn-checkout-text');
            checkoutBtn.disabled = true;
            if (checkoutText) checkoutText.textContent = 'Menyimpan Transaksi...';

            const payload = {
                items: cart.map(i => ({
                    variation_id: i.variation_id,
                    quantity: i.quantity,
                    sugar_level: i.sugar_level,
                    ice_level: i.ice_level,
                    topping_ids: i.toppings.map(t => t.id)
                })),
                payment_method: isCash ? 'tunai' : 'qris',
                paid_amount: paidAmount
            };

            try {
                const res = await fetch('{{ route("pos.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    alert(data.message || 'Gagal memproses transaksi kasir.');
                    checkoutBtn.disabled = false;
                    if (checkoutText) {
                        checkoutText.textContent = isCash ? 'BAYAR TUNAI' : 'TAMPILKAN QRIS';
                    }
                    return;
                }

                // Tampilkan Modal Sukses & Pratinjau Struk
                showReceiptModal(data, [...cart]);

                // Tambahkan transaksi baru langsung ke Riwayat Pesanan (paling atas)
                prependHistoryCard(data, [...cart]);

                // Auto-print struk thermal secara otomatis jika QRIS
                if (!isCash) {
                    setTimeout(() => {
                        printReceipt();
                    }, 500);
                }

                // Reset keranjang
                cart = [];
                receivedInput.value = '';
                renderCart();

            } catch (err) {
                alert('Terjadi kendala jaringan/server saat transaksi.');
                console.error(err);
            } finally {
                if (checkoutText) {
                    checkoutText.textContent = ((activePaymentMethod === 'tunai' || activePaymentMethod === 'cash') ? 'BAYAR TUNAI' : 'TAMPILKAN QRIS');
                }
            }
        }

        // 7. Modal Sukses & Cetak Struk 58mm
        function showReceiptModal(txData, items) {
            document.getElementById('success-lunas-amount').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID') + ' Lunas';
            
            const isCash = (txData.payment_method === 'tunai' || txData.payment_method === 'cash');
            const methodLabel = isCash ? 'Tunai' : 'QRIS';
            document.getElementById('success-breakdown-text').textContent = 
                `${methodLabel} Rp ${txData.paid_amount.toLocaleString('id-ID')} • Kembalian Rp ${txData.change_amount.toLocaleString('id-ID')}`;

            document.getElementById('rc-invoice').textContent = txData.invoice_number;
            document.getElementById('rc-date').textContent = txData.created_at;
            document.getElementById('rc-total').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID');
            document.getElementById('rc-method').textContent = methodLabel;
            document.getElementById('rc-paid').textContent = 'Rp ' + txData.paid_amount.toLocaleString('id-ID');
            document.getElementById('rc-change').textContent = 'Rp ' + txData.change_amount.toLocaleString('id-ID');

            // Set cetak nota
            document.getElementById('print-rc-invoice').textContent = txData.invoice_number;
            document.getElementById('print-rc-date').textContent = txData.created_at;
            document.getElementById('print-rc-total').textContent = 'Rp ' + Number(txData.total_amount).toLocaleString('id-ID');
            document.getElementById('print-rc-paid').textContent = 'Rp ' + Number(txData.paid_amount).toLocaleString('id-ID');
            document.getElementById('print-rc-change').textContent = 'Rp ' + Number(txData.change_amount).toLocaleString('id-ID');
            const cashierElem = document.getElementById('print-rc-cashier');
            if (cashierElem && txData.cashier_name) {
                cashierElem.textContent = txData.cashier_name;
            }

            const list = document.getElementById('rc-items-list');
            const printList = document.getElementById('print-rc-items');
            list.innerHTML = '';
            printList.innerHTML = '';

            items.forEach(i => {
                const topText = i.toppings.length > 0 ? ` +${i.toppings.map(t=>t.name).join(',')}` : '';
                
                const itemDiv = document.createElement('div');
                itemDiv.className = 'space-y-0.5 text-[10px]';
                itemDiv.innerHTML = `
                    <div class="flex justify-between font-semibold">
                        <span>${i.product_name} (${i.variation_name})</span>
                        <span>Rp ${i.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="text-[9px] text-stone-500 flex justify-between">
                        <span>${i.quantity}x Rp ${i.unitPrice.toLocaleString('id-ID')} ${topText}</span>
                    </div>
                `;
                list.appendChild(itemDiv);

                const printDiv = document.createElement('div');
                printDiv.style.marginBottom = '3px';
                printDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between;">
                        <span>${i.product_name}</span>
                        <span>Rp ${i.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div style="font-size: 8px; color: #555;">${i.quantity}x @${i.unitPrice.toLocaleString('id-ID')} (${i.variation_name}${topText})</div>
                `;
                printList.appendChild(printDiv);
            });

            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
        }

        function printReceipt() {
            window.print();
        }

        function reprintTx(tx) {
            document.getElementById('print-rc-invoice').textContent = tx.invoice_number;
            document.getElementById('print-rc-date').textContent = tx.created_at;
            document.getElementById('print-rc-total').textContent = 'Rp ' + Number(tx.total_amount).toLocaleString('id-ID');
            document.getElementById('print-rc-paid').textContent = 'Rp ' + Number(tx.paid_amount).toLocaleString('id-ID');
            document.getElementById('print-rc-change').textContent = 'Rp ' + Number(tx.change_amount).toLocaleString('id-ID');
            const cashierElem = document.getElementById('print-rc-cashier');
            if (cashierElem && tx.cashier_name) {
                cashierElem.textContent = tx.cashier_name;
            }

            const printList = document.getElementById('print-rc-items');
            printList.innerHTML = '';

            tx.items.forEach(i => {
                const printDiv = document.createElement('div');
                printDiv.style.marginBottom = '3px';
                printDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between;">
                        <span>${i.product_name}</span>
                        <span>Rp ${Number(i.subtotal).toLocaleString('id-ID')}</span>
                    </div>
                    <div style="font-size: 8px; color: #555;">${i.quantity}x @${Number(i.unitPrice).toLocaleString('id-ID')} (${i.variation_name})</div>
                `;
                printList.appendChild(printDiv);
            });

            window.print();
        }

        // 8. Riwayat Pesanan & Void
        let currentHistoryScope = 'all';

        function openHistoryModal(scope = 'all') {
            setHistoryScope(scope);
            document.getElementById('history-modal').classList.remove('hidden');
        }

        function closeHistoryModal() {
            const modal = document.getElementById('history-modal');
            if (modal) modal.classList.add('hidden');
        }

        // Global Shortcut: ESC untuk menutup modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeHistoryModal();
                closeReceiptModal();
                const startShift = document.getElementById('start-shift-modal');
                if (startShift) startShift.classList.add('hidden');
                const closeShift = document.getElementById('close-shift-modal');
                if (closeShift) closeShift.classList.add('hidden');
            }
        });

        function setHistoryScope(scope) {
            currentHistoryScope = scope;
            const tabAll = document.getElementById('tab-hist-all');
            const tabShift = document.getElementById('tab-hist-shift');
            const desc = document.getElementById('history-modal-desc');

            if (scope === 'shift') {
                if (tabShift) {
                    tabShift.className = 'tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center transition cursor-pointer bg-white text-[#1B4332] shadow-2xs flex items-center justify-center gap-1.5';
                }
                if (tabAll) {
                    tabAll.className = 'tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-semibold text-center transition cursor-pointer text-stone-600 hover:text-[#2D2D2D] bg-transparent flex items-center justify-center gap-1.5';
                }
                if (desc) {
                    @if ($activeShift)
                        desc.textContent = 'Daftar transaksi kasir khusus shift aktif saat ini (#{{ $activeShift->id }})';
                    @else
                        desc.textContent = 'Shift kasir saat ini belum dibuka';
                    @endif
                }
            } else {
                if (tabAll) {
                    tabAll.className = 'tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center transition cursor-pointer bg-white text-[#1B4332] shadow-2xs flex items-center justify-center gap-1.5';
                }
                if (tabShift) {
                    tabShift.className = 'tab-hist-btn flex-1 py-2 px-3 rounded-xl text-xs font-semibold text-center transition cursor-pointer text-stone-600 hover:text-[#2D2D2D] bg-transparent flex items-center justify-center gap-1.5';
                }
                if (desc) {
                    desc.textContent = 'Daftar transaksi kasir 7 hari terakhir (semua shift)';
                }
            }

            applyHistoryFilters();
        }

        function clearHistorySearch() {
            const input = document.getElementById('history-search-input');
            const clearBtn = document.getElementById('btn-clear-history-search');
            if (input) {
                input.value = '';
                input.focus();
            }
            if (clearBtn) clearBtn.classList.add('hidden');
            applyHistoryFilters();
        }

        function searchHistory() {
            applyHistoryFilters();
        }

        function applyHistoryFilters() {
            const searchInput = document.getElementById('history-search-input');
            const clearBtn = document.getElementById('btn-clear-history-search');
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();

            if (clearBtn) {
                clearBtn.classList.toggle('hidden', query.length === 0);
            }

            const cards = document.querySelectorAll('.history-tx-card');
            let visibleCount = 0;
            let totalInScope = 0;
            const hasActiveShift = {{ $activeShift ? 'true' : 'false' }};

            cards.forEach(card => {
                const isCurrentShift = (card.dataset.currentShift === '1');
                const inScope = (currentHistoryScope === 'all') || (currentHistoryScope === 'shift' && isCurrentShift);

                if (inScope) {
                    totalInScope++;
                    let match = true;
                    if (query.length > 0) {
                        const searchData = card.dataset.search || '';
                        match = searchData.includes(query);
                    }

                    if (match) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                } else {
                    card.classList.add('hidden');
                }
            });

            const emptyFilter = document.getElementById('history-empty-filter');
            const emptyShift = document.getElementById('history-empty-shift');
            const emptyAll = document.getElementById('history-empty-all');
            const emptyNoShift = document.getElementById('history-empty-noshift');

            if (emptyFilter) emptyFilter.classList.add('hidden');
            if (emptyShift) emptyShift.classList.add('hidden');
            if (emptyAll) emptyAll.classList.add('hidden');
            if (emptyNoShift) emptyNoShift.classList.add('hidden');

            if (currentHistoryScope === 'shift') {
                if (!hasActiveShift) {
                    if (emptyNoShift) emptyNoShift.classList.remove('hidden');
                } else if (totalInScope === 0) {
                    if (emptyShift) emptyShift.classList.remove('hidden');
                } else if (visibleCount === 0 && query.length > 0) {
                    if (emptyFilter) emptyFilter.classList.remove('hidden');
                }
            } else {
                if (cards.length === 0 || totalInScope === 0) {
                    if (emptyAll) emptyAll.classList.remove('hidden');
                } else if (visibleCount === 0 && query.length > 0) {
                    if (emptyFilter) emptyFilter.classList.remove('hidden');
                }
            }
        }

        async function promptVoid(txId, invoiceNumber) {
            const reason = prompt(`Alasan pembatalan (Void) nota ${invoiceNumber}:`);
            if (!reason || reason.trim() === '') {
                alert('Alasan pembatalan wajib diisi.');
                return;
            }

            try {
                const res = await fetch(`/pos/transaction/${txId}/void`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason.trim() })
                });

                const data = await res.json();
                if (data.success) {
                    alert('Nota berhasil dibatalkan (void).');
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Gagal membatalkan transaksi.');
            }
        }

        // 9. Buka & Tutup Shift Otomatis
        let currentExpectedCash = {{ $expectedCash ?? 0 }};

        function openStartShiftModal() {
            document.getElementById('start-shift-modal').classList.remove('hidden');
        }

        function setShiftOpeningPreset(val) {
            document.getElementById('shift-opening-cash').value = val;
        }

        async function submitOpenShift() {
            const val = parseInt(document.getElementById('shift-opening-cash').value || 0);

            try {
                const res = await fetch('{{ route("pos.shift.open") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ opening_cash: val })
                });

                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Gagal membuka shift kasir.');
            }
        }

        async function openCloseShiftModal() {
            try {
                const res = await fetch('{{ route("pos.shift.summary") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    currentExpectedCash = data.expected_cash;
                    document.getElementById('close-shift-opening').textContent = 'Rp ' + data.opening_cash.toLocaleString('id-ID');
                    document.getElementById('close-shift-cash').textContent = '+ Rp ' + data.cash_sales.toLocaleString('id-ID');
                    document.getElementById('close-shift-qris').textContent = '+ Rp ' + data.qris_sales.toLocaleString('id-ID');
                    document.getElementById('close-shift-total-sales').textContent = 'Rp ' + data.total_sales.toLocaleString('id-ID');
                    document.getElementById('close-shift-expected').textContent = 'Rp ' + data.expected_cash.toLocaleString('id-ID');
                    document.getElementById('shift-actual-cash').value = data.expected_cash;
                    calculateShiftDiff();
                }
            } catch (e) {
                console.error('Gagal mengambil ringkasan shift realtime:', e);
            }
            document.getElementById('close-shift-modal').classList.remove('hidden');
        }

        function calculateShiftDiff() {
            const actual = parseInt(document.getElementById('shift-actual-cash').value || 0);
            const diff = actual - currentExpectedCash;
            const diffEl = document.getElementById('close-shift-diff-text');
            if (diff === 0) {
                diffEl.textContent = 'Rp 0 (Cocok)';
                diffEl.className = 'font-bold text-emerald-700';
            } else if (diff > 0) {
                diffEl.textContent = '+ Rp ' + diff.toLocaleString('id-ID') + ' (Lebih)';
                diffEl.className = 'font-bold text-amber-700';
            } else {
                diffEl.textContent = '- Rp ' + Math.abs(diff).toLocaleString('id-ID') + ' (Kurang)';
                diffEl.className = 'font-bold text-rose-700';
            }
        }

        function closeCloseShiftModal() {
            document.getElementById('close-shift-modal').classList.add('hidden');
        }

        async function submitCloseShift() {
            const actual = parseInt(document.getElementById('shift-actual-cash').value || 0);

            try {
                const res = await fetch('{{ route("pos.shift.close") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ actual_cash: actual })
                });

                const data = await res.json();
                if (data.success) {
                    alert(`Shift kasir ditutup!\nUang Fisik: Rp ${data.summary.actual_cash.toLocaleString('id-ID')}\nSelisih Kas: Rp ${data.summary.difference.toLocaleString('id-ID')}`);
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Gagal menutup shift kasir.');
            }
        }

        // 10. Handler Gerbang Shift & Filter Riwayat 7 Hari
        function setGateOpeningPreset(val) {
            const input = document.getElementById('gate-opening-cash');
            if (input) input.value = val;
        }

        async function submitGateOpenShift() {
            const input = document.getElementById('gate-opening-cash');
            const val = parseInt((input ? input.value : 0) || 0);
            const btn = document.getElementById('btn-gate-open-shift');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Membuka Shift Kasir...';
            }

            try {
                const res = await fetch('{{ route("pos.shift.open") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ opening_cash: val })
                });

                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal membuka shift kasir.');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<span>Mulai Shift & Masuk Kasir</span><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                    }
                }
            } catch (err) {
                alert('Gagal membuka shift kasir.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<span>Mulai Shift & Masuk Kasir</span><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>';
                }
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function prependHistoryCard(txData, items) {
            const container = document.getElementById('history-items-container');
            if (!container) return;

            const initialEmpty = document.getElementById('history-empty-initial');
            if (initialEmpty) {
                initialEmpty.remove();
            }

            const searchPayload = [
                txData.invoice_number || '',
                txData.payment_method || '',
                ...items.map(i => i.product_name || '')
            ].join(' ').toLowerCase();

            const isQris = (txData.payment_method === 'qris');
            const badgeClass = isQris ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800';
            const methodLabel = isQris ? 'Qris' : 'Tunai';

            const card = document.createElement('div');
            card.className = 'history-tx-card p-3.5 rounded-2xl border border-[#EBE6DD] bg-[#F8F6F0]/40 space-y-2 text-xs transition animate-in fade-in slide-in-from-top-2 duration-200';
            card.dataset.search = searchPayload;
            card.dataset.currentShift = '1';

            const badgeAll = document.getElementById('badge-count-all');
            if (badgeAll) {
                badgeAll.textContent = (parseInt(badgeAll.textContent || '0') + 1);
            }
            const badgeShift = document.getElementById('badge-count-shift');
            if (badgeShift) {
                badgeShift.textContent = (parseInt(badgeShift.textContent || '0') + 1);
            }

            const itemsHtml = items.map(item => `
                <div class="flex items-center justify-between text-[11px] text-[#2D2D2D]">
                    <span class="font-medium">&bull; ${item.quantity}x ${escapeHtml(item.product_name)} (${escapeHtml(item.variation_name || 'Regular')})</span>
                    <span class="font-mono font-bold text-stone-700">Rp ${Number(item.subtotal || 0).toLocaleString('id-ID')}</span>
                </div>
            `).join('');

            const reprintPayload = {
                invoice_number: txData.invoice_number,
                created_at: txData.created_at,
                total_amount: txData.total_amount,
                paid_amount: txData.paid_amount,
                change_amount: txData.change_amount,
                payment_method: txData.payment_method,
                cashier_name: txData.cashier_name || 'Kasir',
                items: items.map(d => ({
                    product_name: d.product_name,
                    variation_name: d.variation_name || 'Regular',
                    quantity: d.quantity,
                    unitPrice: d.unitPrice || d.price,
                    subtotal: d.subtotal,
                    toppings: (d.toppings || []).map(t => ({ name: t.name, price: t.price }))
                }))
            };

            const jsonString = JSON.stringify(reprintPayload).replace(/"/g, '&quot;');

            card.innerHTML = `
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-mono font-black text-[#1B4332] text-xs">${escapeHtml(txData.invoice_number)}</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${badgeClass}">
                                ${methodLabel}
                            </span>
                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-mono font-semibold bg-white border border-[#EBE6DD] text-stone-500">
                                Shift #${txData.cashier_shift_id || '-'}
                            </span>
                        </div>
                        <div class="text-[10px] text-stone-400 mt-0.5">
                            ${escapeHtml(txData.created_at)} &bull; Kasir: ${escapeHtml(txData.cashier_name || 'Kasir')}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-mono font-black text-sm text-[#2D2D2D]">Rp ${Number(txData.total_amount).toLocaleString('id-ID')}</div>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">
                            Lunas
                        </span>
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-white border border-[#EBE6DD]/80 space-y-1">
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Rincian Minuman Dipesan:</span>
                    ${itemsHtml}
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="reprintTx(${jsonString})"
                        class="px-2.5 py-1.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-white font-bold text-[11px] flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Nota</span>
                    </button>

                    <button type="button" onclick="promptVoid(${txData.transaction_id || 0}, '${escapeHtml(txData.invoice_number)}')"
                        class="px-2.5 py-1.5 rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-[11px] transition active:scale-95 cursor-pointer">
                        Void Nota
                    </button>
                </div>
            `;

            container.prepend(card);
            applyHistoryFilters();
        }
    </script>
</body>
</html>
