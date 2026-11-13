<!DOCTYPE html>
<html lang="id" class="h-full bg-stone-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Layar Kasir - {{ $business->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#047857">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Gaya Khusus Cetak Nota Thermal 58mm */
        @media print {
            body * {
                visibility: hidden;
            }
            #thermal-receipt-area, #thermal-receipt-area * {
                visibility: visible;
            }
            #thermal-receipt-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
                margin: 0;
                padding: 4mm 3mm;
                font-family: 'Courier New', Courier, monospace;
                font-size: 11px;
                line-height: 1.25;
                color: #000;
                background: #fff;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-stone-900 select-none">

    <!-- TOPBAR KASIR (Kompak & Informatif) -->
    <header class="bg-emerald-800 text-white px-4 py-2.5 flex items-center justify-between shadow-md shrink-0 z-30">
        <!-- Identitas Usaha & Gerai -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-emerald-700 rounded-xl flex items-center justify-center font-bold text-lg shadow-inner">
                🍵
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-bold text-sm tracking-tight leading-tight">{{ $business->name }}</h1>
                    <span class="px-2 py-0.5 bg-emerald-900/80 text-emerald-200 text-[10px] font-semibold rounded-md uppercase tracking-wider">
                        Sistem Kasir
                    </span>
                </div>
                <p class="text-xs text-emerald-200/90 flex items-center gap-2 mt-0.5">
                    <span>{{ $outlet->name ?? 'Gerai Utama' }}</span>
                    <span>•</span>
                    <span id="current-clock" class="font-mono">--:--:-- WIB</span>
                </p>
            </div>
        </div>

        <!-- Status Shift, Kasir & Kontrol -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Indikator Shift Kasir -->
            <div id="shift-status-pill" class="flex items-center">
                @if ($activeShift)
                    <button type="button" onclick="openCloseShiftModal()"
                        class="bg-emerald-900 hover:bg-emerald-950 border border-emerald-600/60 text-white text-xs px-3 py-1.5 rounded-lg flex items-center gap-2 transition active:scale-95 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <div class="text-left hidden sm:block">
                            <p class="text-[10px] text-emerald-300 leading-none">Kasir Aktif</p>
                            <p class="font-semibold text-xs leading-tight">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</p>
                        </div>
                        <span class="text-[11px] underline sm:ml-1">Tutup Shift</span>
                    </button>
                @else
                    <button type="button" onclick="openStartShiftModal()"
                        class="bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition active:scale-95 shadow-sm">
                        <span>⚠️</span> Buka Shift Kasir
                    </button>
                @endif
            </div>

            <!-- Identitas Petugas Kasir -->
            <div class="hidden md:flex items-center gap-2 bg-emerald-900/60 px-2.5 py-1.5 rounded-lg border border-emerald-700/50 text-xs">
                <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium text-emerald-100">{{ $user->name }}</span>
            </div>

            <!-- Tombol Keluar (Logout) -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                    title="Keluar Akun"
                    class="h-9 px-3 bg-emerald-900/80 hover:bg-rose-700 text-emerald-100 hover:text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition active:scale-95 border border-emerald-700/60">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <!-- AREA UTAMA KASIR (2 KOLOM: KATALOG & KERANJANG) -->
    <main class="flex-1 flex flex-col lg:flex-row overflow-hidden relative">

        <!-- ==============================================
             KOLOM KIRI: KATALOG MENU ES TEH (60-65%)
             ============================================== -->
        <section class="flex-1 flex flex-col bg-stone-100 overflow-hidden border-b lg:border-b-0 lg:border-r border-stone-200">
            <!-- Filter Kategori & Pencarian Menu -->
            <div class="p-3 bg-white border-b border-stone-200 shrink-0 space-y-2.5">
                <!-- Pencarian Cepat -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="menu-search" placeholder="Cari nama es teh..."
                        class="w-full pl-9 pr-8 py-2 bg-stone-50 border border-stone-300 rounded-xl text-sm placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition">
                    <button type="button" id="clear-search" onclick="clearSearch()" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                        ✕
                    </button>
                </div>

                <!-- Tab Kategori Menu -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none" id="category-pills">
                    <button type="button" onclick="filterCategory('all')" data-cat="all"
                        class="cat-pill active px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-emerald-700 text-white shadow-sm">
                        Semua Menu
                    </button>
                    @foreach ($categories as $cat)
                        <button type="button" onclick="filterCategory('{{ $cat->id }}')" data-cat="{{ $cat->id }}"
                            class="cat-pill px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-stone-100 text-stone-600 hover:bg-stone-200">
                            {{ $cat->name }} ({{ $cat->products->count() }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Kartu Menu Minuman -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3" id="products-grid">
                    @forelse ($categories as $category)
                        @foreach ($category->products as $product)
                            @php
                                $minPrice = $product->variations->min('price') ?? 0;
                                $maxPrice = $product->variations->max('price') ?? 0;
                                $priceLabel = ($minPrice === $maxPrice) 
                                    ? 'Rp ' . number_format($minPrice, 0, ',', '.')
                                    : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
                            @endphp
                            <div class="product-card bg-white rounded-xl border border-stone-200/90 p-3 flex flex-col justify-between hover:border-emerald-500 hover:shadow-md transition active:scale-[0.98] cursor-pointer"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-cat="{{ $category->id }}"
                                data-variations='@json($product->variations)'
                                onclick="openProductModal({{ $product->id }})">
                                
                                <div>
                                    <!-- Ikon Produk -->
                                    <div class="w-full h-24 rounded-lg bg-amber-50/70 border border-amber-100 flex items-center justify-center text-3xl mb-2.5">
                                        🥤
                                    </div>

                                    <!-- Kategori & Judul -->
                                    <span class="text-[10px] font-medium text-emerald-700 uppercase tracking-wider block">
                                        {{ $category->name }}
                                    </span>
                                    <h2 class="font-bold text-stone-900 text-sm leading-snug line-clamp-2 mt-0.5">
                                        {{ $product->name }}
                                    </h2>
                                </div>

                                <div class="mt-2.5 pt-2 border-t border-stone-100 flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-800">
                                        {{ $priceLabel }}
                                    </span>
                                    <span class="w-6 h-6 rounded-md bg-stone-100 text-stone-700 flex items-center justify-center text-xs font-bold">
                                        +
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="col-span-full py-12 text-center text-stone-500">
                            <p class="text-3xl mb-2">🍵</p>
                            <p class="font-medium text-sm">Belum ada menu produk di sistem kasir ini.</p>
                        </div>
                    @endforelse
                </div>

                <div id="no-search-results" class="hidden py-12 text-center text-stone-500">
                    <p class="text-3xl mb-2">🔍</p>
                    <p class="font-medium text-sm">Tidak ada menu es teh yang cocok dengan pencarian.</p>
                </div>
            </div>
        </section>

        <!-- ==============================================
             KOLOM KANAN: KERANJANG BELANJA & PEMBAYARAN (35-40%)
             ============================================== -->
        <section class="w-full lg:w-96 xl:w-[410px] flex flex-col bg-white shrink-0 shadow-lg z-20">
            <!-- Header Keranjang -->
            <div class="px-4 py-3 border-b border-stone-200 flex items-center justify-between bg-stone-50/80">
                <div class="flex items-center gap-2">
                    <span class="text-base font-bold text-stone-900">Keranjang Kasir</span>
                    <span id="cart-item-count" class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">
                        0 item
                    </span>
                </div>
                <button type="button" onclick="clearCart()"
                    class="text-xs text-stone-400 hover:text-rose-600 transition flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Kosongkan
                </button>
            </div>

            <!-- Daftar Item di Keranjang (Scrollable) -->
            <div id="cart-items-container" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- State Kosong Default -->
                <div id="empty-cart-state" class="h-full flex flex-col items-center justify-center text-center text-stone-400 py-12">
                    <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center text-2xl mb-3 text-stone-400">
                        🛒
                    </div>
                    <p class="font-bold text-stone-700 text-sm">Pesanan Masih Kosong</p>
                    <p class="text-xs text-stone-400 max-w-[220px] mt-1">
                        Pilih menu es teh pada katalog di sebelah kiri untuk menambahkan ke pesanan.
                    </p>
                </div>

                <!-- Template Kontainer Item (Diisi JS) -->
                <div id="cart-items-list" class="space-y-2.5 hidden"></div>
            </div>

            <!-- Panel Total & Kalkulator Pembayaran Kasir (Fixed Bottom) -->
            <div class="border-t border-stone-200 bg-stone-50/70 p-4 space-y-3 shrink-0">
                <!-- Ringkasan Total Belanja -->
                <div class="space-y-1">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs font-medium text-stone-500 uppercase tracking-wider">Total Belanja</span>
                        <span id="cart-total-display" class="text-2xl font-black text-stone-900 font-mono">
                            Rp 0
                        </span>
                    </div>
                </div>

                <!-- Pilihan Metode Bayar (Tunai vs QRIS) -->
                <div>
                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider block mb-1.5">Metode Bayar</span>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="selectPaymentMethod('cash')" id="btn-pay-cash"
                            class="pay-method-btn active py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-emerald-600 text-white border-emerald-600 shadow-sm">
                            <span>💵</span> Tunai
                        </button>
                        <button type="button" onclick="selectPaymentMethod('qris')" id="btn-pay-qris"
                            class="pay-method-btn py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-700 border-stone-300 hover:bg-stone-100">
                            <span>📱</span> QRIS Statis
                        </button>
                    </div>
                </div>

                <!-- Kotak Perhitungan Uang Tunai & Kembalian (Muncul jika pilih Tunai) -->
                <div id="cash-calculator-box" class="space-y-2">
                    <!-- Tombol Cepat Pecahan Uang Rupiah -->
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="setCashExact()"
                            class="py-1.5 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-700 transition active:scale-95">
                            Uang Pas
                        </button>
                        <button type="button" onclick="setCashAmount(10000)"
                            class="py-1.5 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-700 transition active:scale-95">
                            10.000
                        </button>
                        <button type="button" onclick="setCashAmount(20000)"
                            class="py-1.5 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-700 transition active:scale-95">
                            20.000
                        </button>
                        <button type="button" onclick="setCashAmount(50000)"
                            class="py-1.5 bg-white hover:bg-emerald-50 hover:text-emerald-800 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-700 transition active:scale-95">
                            50.000
                        </button>
                    </div>

                    <!-- Input Uang Diterima & Bar Kembalian -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[11px] font-semibold text-stone-500 block mb-1">Diterima (Rp)</label>
                            <input type="number" id="cash-received-input" placeholder="0" min="0" step="500"
                                oninput="calculateChange()"
                                class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-sm font-mono font-bold text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-stone-500 block mb-1">Kembalian</label>
                            <div id="change-display-box" class="px-3 py-2 bg-stone-200/80 rounded-xl text-sm font-mono font-bold text-stone-600 flex items-center justify-between">
                                <span id="change-amount-display">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Eksekusi Checkout & Cetak Struk -->
                <button type="button" id="btn-checkout" onclick="executeCheckout()" disabled
                    class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-stone-300 disabled:text-stone-400 disabled:cursor-not-allowed text-white font-bold rounded-xl text-sm shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Selesaikan Transaksi & Cetak Nota</span>
                </button>
            </div>
        </section>
    </main>

    <!-- ==============================================
         MODAL 1: PILIH VARIASI UKURAN, TOPPING & GULA
         ============================================== -->
    <div id="product-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl border border-stone-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
            <!-- Header Modal -->
            <div class="p-4 border-b border-stone-200 flex items-center justify-between bg-stone-50">
                <div>
                    <h3 id="modal-product-name" class="font-bold text-base text-stone-900">Nama Menu</h3>
                    <p class="text-xs text-stone-500">Sesuaikan ukuran cup, topping & takaran gula/es</p>
                </div>
                <button type="button" onclick="closeProductModal()" class="w-8 h-8 rounded-full bg-stone-200 text-stone-600 hover:bg-stone-300 flex items-center justify-center text-sm font-bold">
                    ✕
                </button>
            </div>

            <!-- Body Modal (Pilihan Variasi) -->
            <div class="p-4 overflow-y-auto space-y-4 text-xs">
                <!-- 1. Pilihan Variasi Ukuran (Wajib 1) -->
                <div>
                    <label class="font-bold text-stone-800 text-xs block mb-2">1. Pilih Ukuran Cup (Wajib)</label>
                    <div id="modal-variations-list" class="grid grid-cols-2 gap-2"></div>
                </div>

                <!-- 2. Pilihan Topping Berbayar (Opsional) -->
                <div>
                    <label class="font-bold text-stone-800 text-xs block mb-2">2. Topping Tambahan (Opsional)</label>
                    <div class="space-y-1.5" id="modal-toppings-list">
                        @foreach ($toppings as $top)
                            <label class="flex items-center justify-between p-2.5 rounded-xl border border-stone-200 hover:bg-stone-50 cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="modal_toppings" value="{{ $top->id }}" data-price="{{ $top->price }}" data-name="{{ $top->name }}"
                                        onchange="recalculateModalPrice()"
                                        class="w-4 h-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-medium text-stone-800">{{ $top->name }}</span>
                                </div>
                                <span class="font-bold text-emerald-800">+Rp {{ number_format($top->price, 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Takaran Gula & Es (Gratis) -->
                <div class="grid grid-cols-2 gap-3 pt-1 border-t border-stone-100">
                    <div>
                        <label class="font-bold text-stone-800 text-[11px] block mb-1">Takaran Gula</label>
                        <select id="modal-sugar-level" class="w-full px-2.5 py-2 border border-stone-300 rounded-lg bg-white text-xs">
                            <option value="Normal">Normal (100%)</option>
                            <option value="Less Sugar">Sedikit Gula (50%)</option>
                            <option value="No Sugar">Tanpa Gula (0%)</option>
                            <option value="Extra Sugar">Lebih Manis</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-stone-800 text-[11px] block mb-1">Level Es</label>
                        <select id="modal-ice-level" class="w-full px-2.5 py-2 border border-stone-300 rounded-lg bg-white text-xs">
                            <option value="Normal">Normal Ice</option>
                            <option value="Less Ice">Sedikit Es (Less Ice)</option>
                            <option value="No Ice">Tanpa Es (Dingin)</option>
                            <option value="Hangat">Hangat (Warm)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="p-4 border-t border-stone-200 bg-stone-50 flex items-center justify-between shrink-0">
                <div>
                    <span class="text-[10px] text-stone-500 uppercase tracking-wider block">Subtotal Item</span>
                    <span id="modal-subtotal-price" class="text-lg font-black text-emerald-800 font-mono">Rp 0</span>
                </div>
                <button type="button" onclick="addToCartFromModal()"
                    class="py-2.5 px-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition active:scale-95 flex items-center gap-1.5 cursor-pointer">
                    <span>+</span> Tambah ke Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL 2: BUKA SHIFT KASIR (MODAL AWAL LACI)
         ============================================== -->
    <div id="start-shift-modal" class="{{ $activeShift ? 'hidden' : '' }} fixed inset-0 z-50 bg-stone-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-stone-200 space-y-4">
            <div class="text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl mx-auto mb-2">
                    💵
                </div>
                <h3 class="font-bold text-base text-stone-900">Buka Shift Kasir</h3>
                <p class="text-xs text-stone-500 mt-1">Masukkan modal awal uang kembalian pada laci kasir hari ini.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-semibold text-stone-700 block">Modal Awal Laci (Rp)</label>
                <input type="number" id="shift-opening-cash" value="0" min="0" step="5000"
                    class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-base font-mono font-bold text-stone-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600">
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" onclick="setShiftOpeningPreset(0)" class="py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-lg">Rp 0</button>
                    <button type="button" onclick="setShiftOpeningPreset(50000)" class="py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-lg">50.000</button>
                    <button type="button" onclick="setShiftOpeningPreset(100000)" class="py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold rounded-lg">100.000</button>
                </div>
            </div>

            <button type="button" onclick="submitOpenShift()"
                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition active:scale-95 shadow-sm cursor-pointer">
                Mulai Jualan Sekarang
            </button>
        </div>
    </div>

    <!-- ==============================================
         MODAL 3: TUTUP SHIFT KASIR (REKAP UANG FISIK)
         ============================================== -->
    <div id="close-shift-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h3 class="font-bold text-sm text-stone-900">Tutup Shift Kasir</h3>
                <button type="button" onclick="closeCloseShiftModal()" class="text-stone-400 hover:text-stone-700">✕</button>
            </div>

            <p class="text-xs text-stone-600 leading-relaxed">
                Hitung seluruh uang fisik di laci saat ini (modal awal + penerimaan uang kasir tunai).
            </p>

            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-stone-700 block">Uang Fisik Aktual di Laci (Rp)</label>
                <input type="number" id="shift-actual-cash" placeholder="0" min="0" step="5000"
                    class="w-full px-3 py-2.5 bg-white border border-stone-300 rounded-xl text-base font-mono font-bold text-stone-900 focus:outline-none focus:ring-2 focus:ring-emerald-600">
            </div>

            <button type="button" onclick="submitCloseShift()"
                class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition active:scale-95 shadow-sm cursor-pointer">
                Tutup Shift & Hitung Rekap
            </button>
        </div>
    </div>

    <!-- ==============================================
         MODAL 4: STRUK NOTA KASIR (THERMAL 58MM READY)
         ============================================== -->
    <div id="receipt-modal" class="hidden fixed inset-0 z-50 bg-stone-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-4 shadow-2xl border border-stone-200 flex flex-col space-y-4">
            <div class="flex items-center justify-between border-b border-stone-200 pb-2">
                <span class="text-xs font-bold text-stone-700">Pratinjau Nota Penjualan</span>
                <button type="button" onclick="closeReceiptModal()" class="text-stone-400 hover:text-stone-700 font-bold">✕</button>
            </div>

            <!-- KERTAS NOTA THERMAL 58MM -->
            <div id="thermal-receipt-area" class="bg-stone-50 p-4 rounded-xl border border-dashed border-stone-300 font-mono text-xs text-stone-900 leading-tight space-y-2">
                <div class="text-center space-y-0.5">
                    <p class="font-bold text-sm tracking-wider uppercase">{{ $business->name }}</p>
                    <p class="text-[10px] text-stone-600">{{ $outlet->name ?? 'Gerai Utama' }}</p>
                    <p class="text-[10px] text-stone-500">{{ $outlet->address ?? 'Kota Pelayanan' }}</p>
                    <p class="text-[10px] text-stone-500">Telp: {{ $outlet->phone ?? '-' }}</p>
                </div>

                <div class="border-t border-dashed border-stone-400 pt-1.5 text-[10px] space-y-0.5">
                    <div class="flex justify-between">
                        <span>No. Nota:</span>
                        <span id="rc-invoice" class="font-bold">INV-0000</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal:</span>
                        <span id="rc-date">--/--/--</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kasir:</span>
                        <span id="rc-cashier">{{ $user->name }}</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-stone-400 pt-1.5 space-y-1" id="rc-items-list">
                    <!-- Rincian Item Nota (Diisi JS) -->
                </div>

                <div class="border-t border-dashed border-stone-400 pt-1.5 text-[11px] space-y-0.5">
                    <div class="flex justify-between font-bold text-xs">
                        <span>TOTAL:</span>
                        <span id="rc-total">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span>Metode:</span>
                        <span id="rc-method" class="uppercase">TUNAI</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span>Bayar:</span>
                        <span id="rc-paid">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold">
                        <span>Kembali:</span>
                        <span id="rc-change">Rp 0</span>
                    </div>
                </div>

                <div class="border-t border-dashed border-stone-400 pt-2 text-center text-[10px] text-stone-500">
                    <p>Terima kasih atas kunjungan Anda!</p>
                    <p>Segar & Nikmat Setiap Hari</p>
                </div>
            </div>

            <!-- Tombol Aksi Nota -->
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="window.print()"
                    class="py-2.5 px-3 bg-stone-900 hover:bg-stone-800 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer">
                    <span>🖨️</span> Cetak Nota
                </button>
                <button type="button" onclick="closeReceiptModal()"
                    class="py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer">
                    <span>✓</span> Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         LOGIKA APLIKASI KASIR (VANILLA JS BERSIH & CEPAT)
         ============================================== -->
    <script>
        // State Kasir
        let cart = [];
        let activePaymentMethod = 'cash';
        let cashReceived = 0;
        let currentModalProduct = null;
        let selectedVariation = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Jam Realtime Topbar
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            const clockEl = document.getElementById('current-clock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 1. Filter Kategori & Pencarian Menu
        function filterCategory(catId) {
            document.querySelectorAll('.cat-pill').forEach(btn => {
                if (btn.dataset.cat === String(catId)) {
                    btn.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
                    btn.classList.remove('bg-stone-100', 'text-stone-600');
                } else {
                    btn.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
                    btn.classList.add('bg-stone-100', 'text-stone-600');
                }
            });

            applyFilters();
        }

        const searchInput = document.getElementById('menu-search');
        const clearSearchBtn = document.getElementById('clear-search');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                if (searchInput.value.trim().length > 0) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
                applyFilters();
            });
        }

        function clearSearch() {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            applyFilters();
        }

        function applyFilters() {
            const query = (searchInput ? searchInput.value.toLowerCase().trim() : '');
            const activeCatBtn = document.querySelector('.cat-pill.bg-emerald-700');
            const activeCat = activeCatBtn ? activeCatBtn.dataset.cat : 'all';

            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const cat = card.dataset.cat;

                const matchCat = (activeCat === 'all' || cat === activeCat);
                const matchSearch = (!query || name.includes(query));

                if (matchCat && matchSearch) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const noResult = document.getElementById('no-search-results');
            if (noResult) {
                if (visibleCount === 0 && cards.length > 0) {
                    noResult.classList.remove('hidden');
                } else {
                    noResult.classList.add('hidden');
                }
            }
        }

        // 2. Modal Pilih Variasi & Topping
        function openProductModal(productId) {
            const card = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (!card) return;

            const name = card.dataset.name;
            const variations = JSON.parse(card.dataset.variations || '[]');

            currentModalProduct = { id: productId, name, variations };
            document.getElementById('modal-product-name').textContent = name;

            // Render tombol variasi ukuran
            const varContainer = document.getElementById('modal-variations-list');
            varContainer.innerHTML = '';

            variations.forEach((v, idx) => {
                const isFirst = (idx === 0);
                if (isFirst) selectedVariation = v;

                const div = document.createElement('div');
                div.className = `p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice ${isFirst ? 'border-emerald-600 bg-emerald-50/70 text-emerald-900 font-bold' : 'border-stone-200 bg-white text-stone-700'}`;
                div.dataset.id = v.id;
                div.dataset.price = v.price;
                div.dataset.name = v.variation_name;
                div.onclick = () => selectVariation(v);
                div.innerHTML = `
                    <div class="font-bold text-xs">${v.variation_name}</div>
                    <div class="text-[11px] text-emerald-800 font-semibold mt-0.5">Rp ${v.price.toLocaleString('id-ID')}</div>
                `;
                varContainer.appendChild(div);
            });

            // Reset checkboxes topping & select
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
                    el.className = 'p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice border-emerald-600 bg-emerald-50/70 text-emerald-900 font-bold';
                } else {
                    el.className = 'p-2.5 rounded-xl border text-center cursor-pointer transition active:scale-95 variation-choice border-stone-200 bg-white text-stone-700';
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

            // Buat signature unik untuk mendeteksi item yang persis sama di keranjang
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

        // 3. Render Keranjang Belanja
        function renderCart() {
            const listContainer = document.getElementById('cart-items-list');
            const emptyState = document.getElementById('empty-cart-state');
            const countBadge = document.getElementById('cart-item-count');
            const totalDisplay = document.getElementById('cart-total-display');
            const checkoutBtn = document.getElementById('btn-checkout');

            if (cart.length === 0) {
                emptyState.classList.remove('hidden');
                listContainer.classList.add('hidden');
                listContainer.innerHTML = '';
                countBadge.textContent = '0 item';
                totalDisplay.textContent = 'Rp 0';
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
                div.className = 'p-3 rounded-xl border border-stone-200 bg-white hover:border-stone-300 transition flex items-center justify-between gap-2';
                div.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-stone-900 truncate">
                            ${item.product_name} <span class="text-emerald-700 font-semibold">(${item.variation_name})</span>
                        </div>
                        <div class="text-[11px] text-stone-500 truncate">
                            ${toppingText ? `<span class="text-amber-800 font-medium">${toppingText} • </span>` : ''}
                            ${noteText}
                        </div>
                        <div class="text-xs font-mono font-bold text-stone-800 mt-1">
                            Rp ${item.subtotal.toLocaleString('id-ID')}
                        </div>
                    </div>

                    <!-- Counter Qty -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="changeQty(${index}, -1)"
                            class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs flex items-center justify-center active:scale-90">
                            -
                        </button>
                        <span class="w-6 text-center font-bold text-xs font-mono">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${index}, 1)"
                            class="w-7 h-7 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs flex items-center justify-center active:scale-90">
                            +
                        </button>
                        <button type="button" onclick="removeItem(${index})" title="Hapus"
                            class="w-7 h-7 ml-1 text-stone-400 hover:text-rose-600 flex items-center justify-center text-xs">
                            ✕
                        </button>
                    </div>
                `;
                listContainer.appendChild(div);
            });

            countBadge.textContent = `${totalCups} item`;
            totalDisplay.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
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

        // 4. Perhitungan Pembayaran & Kembalian
        function selectPaymentMethod(method) {
            activePaymentMethod = method;
            const btnCash = document.getElementById('btn-pay-cash');
            const btnQris = document.getElementById('btn-pay-qris');
            const calcBox = document.getElementById('cash-calculator-box');

            if (method === 'cash') {
                btnCash.className = 'pay-method-btn active py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-emerald-600 text-white border-emerald-600 shadow-sm';
                btnQris.className = 'pay-method-btn py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-700 border-stone-300 hover:bg-stone-100';
                calcBox.classList.remove('hidden');
            } else {
                btnQris.className = 'pay-method-btn active py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-emerald-600 text-white border-emerald-600 shadow-sm';
                btnCash.className = 'pay-method-btn py-2.5 px-3 rounded-xl border text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-700 border-stone-300 hover:bg-stone-100';
                calcBox.classList.add('hidden');
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
                changeBox.className = 'px-3 py-2 bg-emerald-100 rounded-xl text-sm font-mono font-bold text-emerald-800 flex items-center justify-between';
                checkoutBtn.disabled = false;
            } else {
                const lack = Math.abs(diff);
                changeDisplay.textContent = '- Rp ' + lack.toLocaleString('id-ID');
                changeBox.className = 'px-3 py-2 bg-rose-100 rounded-xl text-sm font-mono font-bold text-rose-700 flex items-center justify-between';
                checkoutBtn.disabled = true;
            }
        }

        // 5. Eksekusi Checkout & Transaksi
        async function executeCheckout() {
            if (cart.length === 0) return;

            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);
            const receivedInput = document.getElementById('cash-received-input');
            const paidAmount = (activePaymentMethod === 'cash')
                ? parseInt(receivedInput.value || 0)
                : grandTotal;

            if (activePaymentMethod === 'cash' && paidAmount < grandTotal) {
                alert('Uang pembayaran kurang dari total belanja.');
                return;
            }

            const checkoutBtn = document.getElementById('btn-checkout');
            checkoutBtn.disabled = true;
            checkoutBtn.textContent = 'Memproses Transaksi...';

            const payload = {
                items: cart.map(i => ({
                    variation_id: i.variation_id,
                    quantity: i.quantity,
                    sugar_level: i.sugar_level,
                    ice_level: i.ice_level,
                    topping_ids: i.toppings.map(t => t.id)
                })),
                payment_method: activePaymentMethod,
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
                    checkoutBtn.textContent = 'Selesaikan Transaksi & Cetak Nota';
                    return;
                }

                // Tampilkan Modal Nota Struk
                showReceiptModal(data, [...cart]);

                // Reset keranjang
                cart = [];
                receivedInput.value = '';
                renderCart();

            } catch (err) {
                alert('Terjadi kesalahan jaringan atau server saat transaksi.');
                console.error(err);
            } finally {
                checkoutBtn.textContent = 'Selesaikan Transaksi & Cetak Nota';
            }
        }

        // 6. Modal Nota Struk Thermal
        function showReceiptModal(txData, items) {
            document.getElementById('rc-invoice').textContent = txData.invoice_number;
            document.getElementById('rc-date').textContent = txData.created_at;
            document.getElementById('rc-total').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID');
            document.getElementById('rc-method').textContent = txData.payment_method;
            document.getElementById('rc-paid').textContent = 'Rp ' + txData.paid_amount.toLocaleString('id-ID');
            document.getElementById('rc-change').textContent = 'Rp ' + txData.change_amount.toLocaleString('id-ID');

            const list = document.getElementById('rc-items-list');
            list.innerHTML = '';

            items.forEach(i => {
                const topText = i.toppings.length > 0 ? ` +${i.toppings.map(t=>t.name).join(',')}` : '';
                const itemDiv = document.createElement('div');
                itemDiv.className = 'space-y-0.5 text-[10px]';
                itemDiv.innerHTML = `
                    <div class="flex justify-between">
                        <span>${i.product_name} (${i.variation_name})</span>
                        <span>Rp ${i.subtotal.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="text-[9px] text-stone-500 flex justify-between">
                        <span>${i.quantity} x Rp ${i.unitPrice.toLocaleString('id-ID')} ${topText}</span>
                    </div>
                `;
                list.appendChild(itemDiv);
            });

            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
        }

        // 7. Buka & Tutup Shift Kasir
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
                    alert(`Shift berhasil ditutup!\nUang Fisik: Rp ${data.summary.actual_cash.toLocaleString('id-ID')}\nSelisih: Rp ${data.summary.difference.toLocaleString('id-ID')}`);
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
