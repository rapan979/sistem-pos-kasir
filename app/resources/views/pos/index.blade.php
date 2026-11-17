<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F6F0]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Layar Kasir - Es Teh Perkasa</title>
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
            body * {
                visibility: hidden !important;
            }
            #thermal-receipt-area, #thermal-receipt-area * {
                visibility: visible !important;
            }
            #thermal-receipt-area {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 58mm !important;
                margin: 0 !important;
                padding: 4mm 3mm !important;
                font-family: 'Courier New', Courier, monospace !important;
                font-size: 11px !important;
                line-height: 1.25 !important;
                color: #000 !important;
                background: #fff !important;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body class="h-full flex flex-col md:flex-row font-sans antialiased text-[#2D2D2D] select-none bg-[#F8F6F0]">

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

                <!-- Tombol Riwayat Nota -->
                <button type="button" onclick="openHistoryModal()"
                    class="w-full px-3.5 py-2.5 rounded-xl text-[#2D2D2D] hover:bg-[#F8F6F0] font-semibold text-xs flex items-center gap-3 transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat Nota</span>
                </button>

                <!-- Status Shift Kasir -->
                <div class="pt-2">
                    @if ($activeShift)
                        <button type="button" onclick="openCloseShiftModal()"
                            class="w-full p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/80 text-left transition active:scale-95 cursor-pointer">
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Shift Aktif
                                </span>
                                <span class="text-[10px] text-emerald-700 underline font-semibold">Tutup</span>
                            </div>
                            <div class="text-xs font-mono font-bold text-emerald-900 mt-1">
                                Modal: Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}
                            </div>
                        </button>
                    @else
                        <button type="button" onclick="openStartShiftModal()"
                            class="w-full p-2.5 rounded-xl bg-[#E88A1A]/10 hover:bg-[#E88A1A]/20 border border-[#E88A1A]/30 text-left transition active:scale-95 cursor-pointer">
                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-[#E88A1A] uppercase tracking-wider">
                                <span>⚠️</span>
                                Kasir Belum Buka
                            </div>
                            <div class="text-xs font-bold text-[#1B4332] mt-0.5 underline">
                                Klik untuk Buka Shift
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
        
        <!-- Header Atas Tengah: Search Bar & Kategori -->
        <div class="p-4 sm:p-5 pb-2 shrink-0 space-y-3">
            <!-- Pencarian Cepat Menu -->
            <div class="relative max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="menu-search" placeholder="Cari Menu..."
                    class="w-full pl-10 pr-8 py-2.5 bg-white border border-[#EBE6DD] rounded-xl text-xs sm:text-sm text-[#2D2D2D] placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1B4332] focus:border-[#1B4332] shadow-[0_2px_8px_rgba(27,67,50,0.03)] transition">
                <button type="button" id="clear-search" onclick="clearSearch()" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                    ✕
                </button>
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
                            
                            // Badge Terlaris untuk menu unggulan tertentu
                            $isPopular = in_array(strtolower($product->name), ['es teh original', 'es teh leci', 'thai tea', 'es teh susu gula aren']);
                        @endphp
                        <div class="product-card group bg-white rounded-2xl border border-[#EBE6DD] hover:border-stone-300 p-3.5 flex flex-col justify-between shadow-[0_4px_16px_rgba(27,67,50,0.04)] hover:shadow-[0_8px_24px_rgba(27,67,50,0.08)] transition-all duration-200 active:scale-[0.98] cursor-pointer relative"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-cat="{{ $category->id }}"
                            data-image="{{ $imgSrc }}"
                            data-variations='@json($product->variations)'
                            onclick="openProductModal({{ $product->id }})">
                            
                            <!-- Badge Terlaris (Oranye Amber #E88A1A) -->
                            @if ($isPopular)
                                <span class="absolute top-5 left-5 z-10 px-2 py-0.5 rounded-full bg-[#E88A1A] text-white text-[9px] font-extrabold uppercase tracking-wider shadow-2xs">
                                    Terlaris
                                </span>
                            @endif

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
                <div class="flex justify-between text-stone-600">
                    <span>Pajak (0%)</span>
                    <span class="font-mono font-semibold text-[#2D2D2D]">Rp 0</span>
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
                    <span>💵</span>
                    <span>Tunai</span>
                </button>
                <button type="button" id="btn-pay-qris" onclick="selectPaymentMethod('qris')"
                    class="py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer text-stone-600 hover:text-[#2D2D2D]">
                    <span>📱</span>
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
                <button type="button" onclick="closeProductModal()" class="w-7 h-7 rounded-full bg-[#F8F6F0] text-stone-400 hover:text-stone-700 flex items-center justify-center text-xs cursor-pointer">
                    ✕
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
         5. MODAL QRIS SIMULASI (VERIFIKASI KASIR)
         ============================================================== -->
    <div id="qris-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 border border-[#EBE6DD] shadow-2xl text-center space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="space-y-1">
                <span class="px-2.5 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-[10px] font-bold uppercase tracking-wider">
                    QRIS Statis Simulasi
                </span>
                <h3 class="text-base font-extrabold text-[#2D2D2D]">Pindai untuk Membayar</h3>
            </div>

            <!-- Kode QR Simulasi -->
            <div class="w-48 h-48 mx-auto p-3 bg-white rounded-2xl border-2 border-[#1B4332] flex items-center justify-center shadow-xs">
                <div class="space-y-2 text-center">
                    <div class="w-32 h-32 bg-stone-100 rounded-lg flex items-center justify-center text-xs font-mono font-bold text-stone-400 border border-stone-200">
                        [ QRIS PERKASA ]
                    </div>
                </div>
            </div>

            <div>
                <span class="text-xs text-stone-500">Nominal Tagihan:</span>
                <div class="text-xl font-extrabold font-mono text-[#1B4332]" id="qris-modal-amount">Rp 0</div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeQrisModal()"
                    class="py-2.5 rounded-xl border border-[#EBE6DD] text-stone-600 font-bold text-xs hover:bg-[#F8F6F0] transition active:scale-95 cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="confirmQrisPayment()"
                    class="py-2.5 rounded-xl bg-[#1B4332] hover:bg-[#143326] text-white font-bold text-xs transition active:scale-95 cursor-pointer shadow-xs">
                    ✓ Verifikasi Lunas
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
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl font-bold border border-emerald-200">
                    ✓
                </div>
                <h3 class="text-base font-extrabold text-[#1B4332]">Transaksi Berhasil!</h3>
                <p class="text-xs font-mono font-bold text-emerald-800" id="success-lunas-amount">Rp 0 Lunas</p>
                <p class="text-[11px] text-stone-400" id="success-breakdown-text">Tunai • Kembalian</p>
            </div>

            <!-- Preview Struk 58mm di Layar -->
            <div class="p-3 bg-[#F8F6F0] rounded-2xl border border-[#EBE6DD] font-mono text-[11px] space-y-2 text-[#2D2D2D] max-h-56 overflow-y-auto">
                <div class="text-center border-b border-[#EBE6DD] pb-2 space-y-0.5">
                    <div class="font-extrabold text-xs text-[#1B4332]">ES TEH PERKASA</div>
                    <div class="text-[9px] text-stone-500">{{ $outlet->address ?? 'Gerai Es Teh Perkasa' }}</div>
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
            </div>

            <!-- Aksi: Cetak Struk & Pesanan Baru -->
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button" onclick="window.print()"
                    class="py-2.5 rounded-xl border border-[#1B4332] text-[#1B4332] font-bold text-xs hover:bg-emerald-50 transition active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                    <span>🖨️</span>
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
         7. MODAL RIWAYAT NOTA & VOID
         ============================================================== -->
    <div id="history-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#EBE6DD]">
                <div>
                    <h3 class="font-extrabold text-sm text-[#1B4332]">Riwayat Nota Hari Ini</h3>
                    <p class="text-[11px] text-stone-500">Daftar transaksi nota shift yang sedang aktif</p>
                </div>
                <button type="button" onclick="closeHistoryModal()" class="w-7 h-7 rounded-full bg-[#F8F6F0] text-stone-400 hover:text-stone-700 flex items-center justify-center text-xs cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="overflow-y-auto max-h-72 space-y-2">
                @forelse ($recentTransactions as $tx)
                    <div class="p-3 rounded-2xl border border-[#EBE6DD] bg-[#F8F6F0]/40 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-mono font-bold text-[#1B4332]">{{ $tx->invoice_number }}</div>
                            <div class="text-[11px] text-stone-500">{{ $tx->created_at->format('H:i') }} WIB • {{ ucfirst($tx->payment_method) }}</div>
                            <div class="font-mono font-extrabold text-[#2D2D2D] mt-0.5">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            @if ($tx->payment_status === 'batal' || $tx->payment_status === 'void')
                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">
                                    Dibatalkan (Void)
                                </span>
                            @else
                                <button type="button" onclick="promptVoid({{ $tx->id }}, '{{ $tx->invoice_number }}')"
                                    class="px-2.5 py-1.5 rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 font-bold text-[11px] transition active:scale-95 cursor-pointer">
                                    Void Nota
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-stone-400 text-xs">
                        Belum ada transaksi pada shift ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==============================================================
         8. MODAL BUKA SHIFT
         ============================================================== -->
    <div id="start-shift-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4">
            <div class="space-y-1 text-center">
                <span class="text-2xl">🌅</span>
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
         9. MODAL TUTUP SHIFT & REKONSILIASI KAS
         ============================================================== -->
    <div id="close-shift-modal" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 border border-[#EBE6DD] shadow-2xl space-y-4">
            <div class="space-y-1 text-center">
                <span class="text-2xl">🌙</span>
                <h3 class="font-extrabold text-base text-[#1B4332]">Tutup Shift & Rekonsiliasi</h3>
                <p class="text-xs text-stone-500">Hitung total uang fisik tunai di laci kasir saat ini.</p>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700">Total Uang Fisik Kasir (Rp)</label>
                <input type="number" id="shift-actual-cash" placeholder="0" min="0"
                    class="w-full px-3 py-2.5 bg-[#F8F6F0] border border-[#EBE6DD] rounded-xl text-sm font-mono font-bold text-[#2D2D2D] focus:outline-none focus:ring-2 focus:ring-[#1B4332]">
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeCloseShiftModal()"
                    class="py-2.5 rounded-xl border border-[#EBE6DD] text-stone-600 font-bold text-xs hover:bg-[#F8F6F0]">
                    Batal
                </button>
                <button type="button" onclick="submitCloseShift()"
                    class="py-2.5 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs shadow-xs">
                    Tutup Kasir
                </button>
            </div>
        </div>
    </div>

    <!-- ELEMEN ISOLASI CETAK NOTA THERMAL 58MM (@media print) -->
    <div id="thermal-receipt-area" class="hidden">
        <div style="text-align: center; margin-bottom: 6px;">
            <div style="font-size: 13px; font-weight: bold;">ES TEH PERKASA</div>
            <div style="font-size: 9px;">{{ $outlet->address ?? 'Gerai Utama' }}</div>
            <div style="font-size: 9px; margin-top: 3px;">--------------------------------</div>
        </div>
        <div style="font-size: 9px; margin-bottom: 5px;">
            <div>Nota   : <span id="print-rc-invoice">-</span></div>
            <div>Waktu  : <span id="print-rc-date">-</span></div>
            <div>Kasir  : {{ $user->name }}</div>
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
                <div>Es Teh Perkasa - Rasa Otentik</div>
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
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            const el = document.getElementById('current-clock');
            if (el) el.textContent = timeStr;
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
                            class="w-7 h-7 ml-1 text-stone-400 hover:text-rose-600 flex items-center justify-center text-xs cursor-pointer">
                            ✕
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
                checkoutText.textContent = 'BAYAR TUNAI';
            } else {
                btnQris.className = 'py-2 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer bg-white text-[#1B4332] shadow-2xs';
                btnCash.className = 'py-2 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition cursor-pointer text-stone-600 hover:text-[#2D2D2D]';
                calcBox.classList.add('hidden');
                qrisBox.classList.remove('hidden');
                checkoutText.textContent = '📱 BUKA QRIS SIMULASI';
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

        // 6. Alur Checkout
        function initiateCheckout() {
            if (cart.length === 0) return;

            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);

            if (activePaymentMethod === 'qris') {
                document.getElementById('qris-modal-amount').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                document.getElementById('qris-modal').classList.remove('hidden');
                return;
            }

            executeCheckout();
        }

        function closeQrisModal() {
            document.getElementById('qris-modal').classList.add('hidden');
        }

        function confirmQrisPayment() {
            closeQrisModal();
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
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Menyimpan Transaksi...';

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
                    checkoutBtn.textContent = isCash ? 'BAYAR TUNAI' : '📱 BUKA QRIS SIMULASI';
                    return;
                }

                // Tampilkan Modal Sukses & Pratinjau Struk
                showReceiptModal(data, [...cart]);

                // Reset keranjang
                cart = [];
                receivedInput.value = '';
                renderCart();

            } catch (err) {
                alert('Terjadi kendala jaringan/server saat transaksi.');
                console.error(err);
            } finally {
                checkoutBtn.textContent = ((activePaymentMethod === 'tunai' || activePaymentMethod === 'cash') ? 'BAYAR TUNAI' : '📱 BUKA QRIS SIMULASI');
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
            document.getElementById('print-rc-total').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID');
            document.getElementById('print-rc-paid').textContent = 'Rp ' + txData.paid_amount.toLocaleString('id-ID');
            document.getElementById('print-rc-change').textContent = 'Rp ' + txData.change_amount.toLocaleString('id-ID');

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

        // 8. Riwayat Nota & Void
        function openHistoryModal() {
            document.getElementById('history-modal').classList.remove('hidden');
        }

        function closeHistoryModal() {
            document.getElementById('history-modal').classList.add('hidden');
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

        // 9. Buka & Tutup Shift
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

        function openCloseShiftModal() {
            document.getElementById('close-shift-modal').classList.remove('hidden');
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
    </script>
</body>
</html>
