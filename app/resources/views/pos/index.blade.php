<!DOCTYPE html>
<html lang="id" class="h-full bg-[#faf9f6]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Layar Kasir - Es Teh Perkasa</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0e2a1d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Gaya Cetak Nota Thermal 58mm Bersih */
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
<body class="h-full flex flex-col font-sans antialiased text-stone-900 select-none bg-[#faf9f6]">

    <!-- TOPBAR KASIR MINIMALIS & ELEGAN -->
    <header class="bg-white border-b border-stone-200 px-4 py-2.5 flex items-center justify-between shrink-0 z-30 shadow-xs">
        <!-- Logo & Identitas Gerai -->
        <div class="flex items-center gap-3">
            <img src="/images/logo.jpg" alt="Logo Es Teh Perkasa" class="w-9 h-9 rounded-xl object-contain border border-stone-200 bg-[#0e2a1d] shadow-xs">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-extrabold text-sm sm:text-base text-stone-900 tracking-tight leading-tight">Es Teh Perkasa</h1>
                    <span class="px-2 py-0.5 bg-stone-100 text-stone-600 text-[10px] font-bold rounded-md uppercase tracking-wider">
                        Mesin Kasir
                    </span>
                </div>
                <p class="text-xs text-stone-500 flex items-center gap-2 mt-0.5">
                    <span>{{ $outlet->name ?? 'Gerai Utama' }}</span>
                    <span>&bull;</span>
                    <span id="current-clock" class="font-mono text-stone-600 font-medium">--:--:-- WIB</span>
                </p>
            </div>
        </div>

        <!-- Aksi Kasir & Shift -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Tombol Riwayat Nota Shift Ini -->
            <button type="button" onclick="openHistoryModal()"
                class="h-9 px-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition active:scale-95 cursor-pointer border border-stone-200">
                <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="hidden sm:inline">Riwayat Nota</span>
            </button>

            <!-- Indikator Shift Kasir -->
            <div id="shift-status-pill" class="flex items-center">
                @if ($activeShift)
                    <button type="button" onclick="openCloseShiftModal()"
                        class="h-9 px-3 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-2 transition active:scale-95 cursor-pointer text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div class="text-left hidden sm:block">
                            <span class="text-[10px] text-emerald-700 block leading-none">Shift Aktif</span>
                            <span class="font-bold text-xs leading-tight">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</span>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-800 underline sm:ml-1">Tutup Kasir</span>
                    </button>
                @else
                    <button type="button" onclick="openStartShiftModal()"
                        class="h-9 px-3 bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs rounded-xl flex items-center gap-1.5 transition active:scale-95 shadow-xs cursor-pointer">
                        <span>⚠️</span> Buka Kasir
                    </button>
                @endif
            </div>

            <!-- Petugas Kasir -->
            <div class="hidden lg:flex items-center gap-2 bg-stone-50 px-2.5 py-1.5 rounded-xl border border-stone-200 text-xs">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-bold text-stone-800">{{ $user->name }}</span>
            </div>

            <!-- Tombol Logout -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Keluar Kasir"
                    class="h-9 w-9 sm:w-auto sm:px-3 bg-stone-100 hover:bg-rose-50 text-stone-500 hover:text-rose-700 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition active:scale-95 border border-stone-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
             KOLOM KIRI: KATALOG MENU BERGAMBAR (60%)
             ============================================== -->
        <section class="flex-1 flex flex-col bg-[#faf9f6] overflow-hidden border-b lg:border-b-0 lg:border-r border-stone-200">
            <!-- Filter Kategori & Pencarian Menu -->
            <div class="p-3 sm:p-4 bg-white border-b border-stone-200 shrink-0 space-y-3">
                <!-- Bar Pencarian Cepat -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="menu-search" placeholder="Cari es teh, susu, leci..."
                        class="w-full pl-10 pr-8 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0e2a1d] focus:border-[#0e2a1d] transition">
                    <button type="button" id="clear-search" onclick="clearSearch()" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-stone-700">
                        ✕
                    </button>
                </div>

                <!-- Tab Kategori Menu Minimalis -->
                <div class="flex items-center gap-2 overflow-x-auto pb-0.5 scrollbar-none" id="category-pills">
                    <button type="button" onclick="filterCategory('all')" data-cat="all"
                        class="cat-pill active px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer bg-[#0e2a1d] text-white shadow-xs">
                        Semua Menu
                    </button>
                    @foreach ($categories as $cat)
                        <button type="button" onclick="filterCategory('{{ $cat->id }}')" data-cat="{{ $cat->id }}"
                            class="cat-pill px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-stone-100 text-stone-600 hover:bg-stone-200 hover:text-stone-900">
                            {{ $cat->name }} ({{ $cat->products->count() }})
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Grid Kartu Menu Minuman Bergambar Asli -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-3 sm:gap-4" id="products-grid">
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
                            <div class="product-card group bg-white rounded-2xl border border-stone-200 hover:border-stone-400 hover:shadow-md transition-all duration-200 p-3 flex flex-col justify-between active:scale-[0.98] cursor-pointer"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-cat="{{ $category->id }}"
                                data-image="{{ $imgSrc }}"
                                data-variations='@json($product->variations)'
                                onclick="openProductModal({{ $product->id }})">
                                
                                <div>
                                    <!-- Foto Produk Berkualitas -->
                                    <div class="w-full h-32 sm:h-36 rounded-xl bg-stone-50 border border-stone-100 flex items-center justify-center overflow-hidden mb-2.5 relative">
                                        <img src="{{ $imgSrc }}" alt="{{ $product->name }}" 
                                            class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-xs text-[10px] font-bold text-stone-600 shadow-xs">
                                            {{ $category->name }}
                                        </span>
                                    </div>

                                    <!-- Nama Produk -->
                                    <h2 class="font-bold text-stone-900 text-sm leading-snug line-clamp-2">
                                        {{ $product->name }}
                                    </h2>
                                </div>

                                <div class="mt-3 pt-2 border-t border-stone-100 flex items-center justify-between">
                                    <span class="text-xs font-black text-stone-900 font-mono">
                                        {{ $priceLabel }}
                                    </span>
                                    <span class="w-7 h-7 rounded-lg bg-[#0e2a1d] text-white flex items-center justify-center text-xs font-bold group-hover:bg-[#1a4a34] transition shadow-xs">
                                        +
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="col-span-full py-16 text-center text-stone-400">
                            <p class="text-4xl mb-2">🍵</p>
                            <p class="font-bold text-sm text-stone-600">Belum ada menu di sistem kasir ini.</p>
                        </div>
                    @endforelse
                </div>

                <div id="no-search-results" class="hidden py-16 text-center text-stone-400">
                    <p class="text-4xl mb-2">🔍</p>
                    <p class="font-bold text-sm text-stone-600">Menu es teh tidak ditemukan.</p>
                    <p class="text-xs text-stone-400 mt-1">Coba kata kunci lain seperti original, leci, susu, atau lemon.</p>
                </div>
            </div>
        </section>

        <!-- ==============================================
             KOLOM KANAN: KERANJANG & CHECKOUT DENGAN NUMPAD (40%)
             ============================================== -->
        <section class="w-full lg:w-[420px] xl:w-[450px] flex flex-col bg-white shrink-0 border-l border-stone-200 z-20">
            <!-- Header Keranjang -->
            <div class="px-4 py-3 border-b border-stone-200 flex items-center justify-between bg-stone-50/60">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-extrabold text-stone-900">Pesanan Kasir</span>
                    <span id="cart-item-count" class="px-2 py-0.5 bg-stone-200 text-stone-700 text-xs font-bold rounded-full">
                        0 item
                    </span>
                </div>
                <button type="button" onclick="clearCart()"
                    class="text-xs text-stone-400 hover:text-rose-600 transition flex items-center gap-1 font-medium cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Kosongkan
                </button>
            </div>

            <!-- Daftar Item di Keranjang (Scrollable) -->
            <div id="cart-items-container" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-2.5">
                <!-- State Kosong Default -->
                <div id="empty-cart-state" class="h-full min-h-[180px] flex flex-col items-center justify-center text-center text-stone-400 py-10">
                    <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center text-2xl mb-2 text-stone-400">
                        🛒
                    </div>
                    <p class="font-bold text-stone-700 text-sm">Pesanan Belum Ada</p>
                    <p class="text-xs text-stone-400 max-w-[220px] mt-0.5">
                        Pilih menu es teh pada katalog di sebelah kiri.
                    </p>
                </div>

                <!-- Kontainer Item Terisi JS -->
                <div id="cart-items-list" class="space-y-2 hidden"></div>
            </div>

            <!-- Panel Pembayaran Terstruktur (Inspirasi Kedai Senja) -->
            <div class="border-t border-stone-200 bg-stone-50/80 p-4 space-y-3.5 shrink-0">
                <!-- Total Tagihan Besar -->
                <div class="flex justify-between items-baseline pt-1">
                    <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Tagihan</span>
                    <span id="cart-total-display" class="text-3xl font-black text-stone-900 font-mono tracking-tight">
                        Rp 0
                    </span>
                </div>

                <!-- Tab Metode Bayar: [ Tunai ] vs [ Non-tunai (QRIS) ] -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-stone-200/60 rounded-xl">
                    <button type="button" onclick="selectPaymentMethod('cash')" id="btn-pay-cash"
                        class="py-2.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-900 shadow-xs">
                        <span>💵</span> Tunai
                    </button>
                    <button type="button" onclick="selectPaymentMethod('qris')" id="btn-pay-qris"
                        class="py-2.5 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition cursor-pointer text-stone-600 hover:text-stone-900">
                        <span>📱</span> Non-tunai (QRIS)
                    </button>
                </div>

                <!-- ==============================================
                     KOTAK BAYAR TUNAI + KEYPAD NUMPAD SENTUH BESAR
                     ============================================== -->
                <div id="cash-calculator-box" class="space-y-2.5">
                    <!-- Tombol Cepat Pecahan Uang -->
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="setCashExact()"
                            class="py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-800 transition active:scale-95 shadow-2xs">
                            Uang Pas
                        </button>
                        <button type="button" onclick="setCashAmount(50000)"
                            class="py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-800 transition active:scale-95 shadow-2xs">
                            50.000
                        </button>
                        <button type="button" onclick="setCashAmount(100000)"
                            class="py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-800 transition active:scale-95 shadow-2xs">
                            100.000
                        </button>
                        <button type="button" onclick="setCashAmount(200000)"
                            class="py-2 bg-white hover:bg-stone-100 border border-stone-300 rounded-lg text-[11px] font-bold text-stone-800 transition active:scale-95 shadow-2xs">
                            200.000
                        </button>
                    </div>

                    <!-- Input Nominal Diterima & Kembalian -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Uang Diterima</label>
                            <input type="number" id="cash-received-input" placeholder="0" min="0" step="500"
                                oninput="calculateChange()"
                                class="w-full px-3 py-2 bg-white border border-stone-300 rounded-xl text-base font-mono font-bold text-stone-900 focus:outline-none focus:ring-2 focus:ring-[#0e2a1d]">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block mb-1">Kembalian</label>
                            <div id="change-display-box" class="px-3 py-2 bg-stone-100 border border-stone-200 rounded-xl text-base font-mono font-bold text-stone-600 flex items-center justify-between">
                                <span id="change-amount-display">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- KEYPAD NUMERIK TOUCHSCREEN (Mempermudah Kasir di HP / Tablet) -->
                    <div class="pt-1">
                        <div class="grid grid-cols-3 gap-1.5 p-2 bg-white border border-stone-200 rounded-2xl shadow-2xs text-sm font-mono font-bold">
                            <button type="button" onclick="numpadInput('1')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">1</button>
                            <button type="button" onclick="numpadInput('2')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">2</button>
                            <button type="button" onclick="numpadInput('3')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">3</button>
                            <button type="button" onclick="numpadInput('4')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">4</button>
                            <button type="button" onclick="numpadInput('5')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">5</button>
                            <button type="button" onclick="numpadInput('6')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">6</button>
                            <button type="button" onclick="numpadInput('7')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">7</button>
                            <button type="button" onclick="numpadInput('8')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">8</button>
                            <button type="button" onclick="numpadInput('9')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">9</button>
                            <button type="button" onclick="numpadInput('000')" class="h-11 bg-amber-50 hover:bg-amber-100 text-amber-900 active:scale-95 rounded-xl flex items-center justify-center transition border border-amber-200">000</button>
                            <button type="button" onclick="numpadInput('0')" class="h-11 bg-stone-50 hover:bg-stone-100 active:scale-95 rounded-xl text-stone-800 flex items-center justify-center transition border border-stone-100">0</button>
                            <button type="button" onclick="numpadBackspace()" class="h-11 bg-rose-50 hover:bg-rose-100 text-rose-700 active:scale-95 rounded-xl flex items-center justify-center transition border border-rose-200 text-xs">⌫ Hapus</button>
                        </div>
                    </div>
                </div>

                <!-- Kotak Info Khusus QRIS -->
                <div id="qris-info-box" class="hidden p-3 bg-amber-50/70 border border-amber-200 rounded-xl space-y-1">
                    <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                        <span>📱</span> Pembayaran Non-tunai (QRIS)
                    </span>
                    <p class="text-[11px] text-amber-800 leading-relaxed">
                        Setelah menekan tombol di bawah, kode QR simulasi lokal akan muncul untuk divalidasi pembayarannya.
                    </p>
                </div>

                <!-- Tombol Eksekusi Checkout Utama -->
                <button type="button" id="btn-checkout" onclick="initiateCheckout()" disabled
                    class="w-full min-h-[48px] py-3.5 px-4 bg-[#0e2a1d] hover:bg-[#153b2a] disabled:bg-stone-300 disabled:text-stone-400 disabled:cursor-not-allowed text-white font-bold rounded-xl text-sm shadow-sm transition active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                    <span id="btn-checkout-text">✓ Konfirmasi Lunas</span>
                </button>
            </div>
        </section>
    </main>

    <!-- ==============================================
         MODAL 1: PILIH VARIASI UKURAN, TOPPING & GULA
         ============================================== -->
    <div id="product-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-stone-200 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
            <!-- Header Modal dengan Foto Produk -->
            <div class="p-4 border-b border-stone-100 flex items-center justify-between bg-stone-50/70">
                <div class="flex items-center gap-3">
                    <img id="modal-product-img" src="/images/products/es-teh-original.jpg" alt="Produk" class="w-12 h-12 rounded-xl object-contain bg-white border border-stone-200 p-1">
                    <div>
                        <h3 id="modal-product-name" class="font-bold text-base text-stone-900 leading-tight">Nama Menu</h3>
                        <p class="text-xs text-stone-500">Pilih ukuran cup & topping ekstra</p>
                    </div>
                </div>
                <button type="button" onclick="closeProductModal()" class="w-8 h-8 rounded-full bg-stone-200 hover:bg-stone-300 text-stone-600 flex items-center justify-center text-sm font-bold cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Body Modal (Pilihan Variasi) -->
            <div class="p-4 overflow-y-auto space-y-4 text-xs">
                <!-- 1. Pilihan Ukuran Cup -->
                <div>
                    <label class="font-extrabold text-stone-800 text-xs block mb-2 uppercase tracking-wider">1. Ukuran Cup (Wajib)</label>
                    <div id="modal-variations-list" class="grid grid-cols-2 gap-2"></div>
                </div>

                <!-- 2. Pilihan Topping Berbayar -->
                <div>
                    <label class="font-extrabold text-stone-800 text-xs block mb-2 uppercase tracking-wider">2. Topping Ekstra (Opsional)</label>
                    <div class="space-y-1.5" id="modal-toppings-list">
                        @foreach ($toppings as $top)
                            <label class="flex items-center justify-between p-3 rounded-xl border border-stone-200 hover:bg-stone-50 cursor-pointer transition">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="modal_toppings" value="{{ $top->id }}" data-price="{{ $top->price }}" data-name="{{ $top->name }}"
                                        onchange="recalculateModalPrice()"
                                        class="w-4 h-4 rounded border-stone-300 text-[#0e2a1d] focus:ring-[#0e2a1d]">
                                    <span class="font-semibold text-stone-800 text-xs">{{ $top->name }}</span>
                                </div>
                                <span class="font-bold text-stone-900 font-mono">+Rp {{ number_format($top->price, 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Takaran Gula & Level Es -->
                <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-stone-100">
                    <div>
                        <label class="font-bold text-stone-700 text-[11px] block mb-1">Takaran Gula</label>
                        <select id="modal-sugar-level" class="w-full px-2.5 py-2 border border-stone-300 rounded-xl bg-white text-xs">
                            <option value="Normal">Normal (100%)</option>
                            <option value="Less Sugar">Sedikit Gula (50%)</option>
                            <option value="No Sugar">Tanpa Gula (0%)</option>
                            <option value="Extra Sugar">Lebih Manis</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-stone-700 text-[11px] block mb-1">Level Es</label>
                        <select id="modal-ice-level" class="w-full px-2.5 py-2 border border-stone-300 rounded-xl bg-white text-xs">
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
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Subtotal</span>
                    <span id="modal-subtotal-price" class="text-xl font-black text-stone-900 font-mono">Rp 0</span>
                </div>
                <button type="button" onclick="addToCartFromModal()"
                    class="py-2.5 px-5 bg-[#0e2a1d] hover:bg-[#163e2c] text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 flex items-center gap-1.5 cursor-pointer">
                    <span>+</span> Tambah ke Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL 2: POP-UP QRIS SIMULASI LOKAL (KEDAI SENJA STYLE)
         ============================================== -->
    <div id="qris-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-stone-200 space-y-4 text-center">
            <div class="space-y-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-200">
                    QRIS Dinamis Simulasi
                </span>
                <h3 class="text-lg font-black text-stone-900">Scan QRIS Pembayaran</h3>
                <p class="text-xs text-stone-500">Minta pembeli melakukan scan QR di bawah ini.</p>
            </div>

            <!-- Gambar QR Code Simulasi -->
            <div class="p-4 bg-white border border-stone-200 rounded-2xl inline-block shadow-2xs">
                <svg class="w-48 h-48 mx-auto" viewBox="0 0 100 100" fill="none">
                    <!-- Simulasi Pola QR Code Elegan -->
                    <rect width="100" height="100" fill="white" />
                    <rect x="10" y="10" width="25" height="25" fill="#0e2a1d" />
                    <rect x="15" y="15" width="15" height="15" fill="white" />
                    <rect x="18" y="18" width="9" height="9" fill="#0e2a1d" />
                    
                    <rect x="65" y="10" width="25" height="25" fill="#0e2a1d" />
                    <rect x="70" y="15" width="15" height="15" fill="white" />
                    <rect x="73" y="18" width="9" height="9" fill="#0e2a1d" />
                    
                    <rect x="10" y="65" width="25" height="25" fill="#0e2a1d" />
                    <rect x="15" y="70" width="15" height="15" fill="white" />
                    <rect x="18" y="73" width="9" height="9" fill="#0e2a1d" />
                    
                    <!-- Titik-titik Data -->
                    <rect x="42" y="12" width="6" height="6" fill="#0e2a1d" />
                    <rect x="52" y="18" width="6" height="6" fill="#0e2a1d" />
                    <rect x="45" y="28" width="8" height="8" fill="#0e2a1d" />
                    <rect x="12" y="42" width="6" height="6" fill="#0e2a1d" />
                    <rect x="25" y="48" width="6" height="6" fill="#0e2a1d" />
                    <rect x="40" y="40" width="20" height="20" fill="#0e2a1d" rx="4" />
                    <rect x="45" y="45" width="10" height="10" fill="white" rx="2" />
                    <rect x="68" y="42" width="8" height="8" fill="#0e2a1d" />
                    <rect x="80" y="48" width="8" height="8" fill="#0e2a1d" />
                    <rect x="42" y="68" width="8" height="8" fill="#0e2a1d" />
                    <rect x="55" y="75" width="6" height="6" fill="#0e2a1d" />
                    <rect x="72" y="70" width="12" height="12" fill="#0e2a1d" />
                </svg>
            </div>

            <!-- Tagihan QRIS -->
            <div class="space-y-0.5">
                <span class="text-[11px] text-stone-500 uppercase tracking-wider block">Total Tagihan</span>
                <span id="qris-modal-amount" class="text-2xl font-black text-stone-900 font-mono">Rp 0</span>
            </div>

            <!-- Tombol Aksi QRIS -->
            <div class="space-y-2 pt-1">
                <button type="button" onclick="confirmQrisPayment()"
                    class="w-full py-3 bg-[#0e2a1d] hover:bg-[#153b2a] text-white font-bold rounded-xl text-xs shadow-sm transition active:scale-95 cursor-pointer">
                    ✓ Konfirmasi Pembayaran Berhasil (Simulasi)
                </button>
                <button type="button" onclick="closeQrisModal()"
                    class="w-full py-2 bg-stone-100 hover:bg-stone-200 text-stone-600 font-semibold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL 3: PEMBAYARAN BERHASIL & STRUK THERMAL 58MM (REF 15)
         ============================================== -->
    <div id="receipt-modal" class="hidden fixed inset-0 z-50 bg-stone-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-stone-200 flex flex-col space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <!-- Icon Check Sukses & Status -->
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl font-bold mx-auto mb-1.5 shadow-md shadow-emerald-500/20">
                    ✓
                </div>
                <h3 id="success-lunas-amount" class="text-xl font-black text-stone-900 font-mono">Rp 0 Lunas</h3>
                <p id="success-breakdown-text" class="text-xs text-stone-500 font-medium">Tunai Rp 0 &bull; Kembalian Rp 0</p>
            </div>

            <!-- KERTAS NOTA THERMAL 58MM -->
            <div id="thermal-receipt-area" class="bg-stone-50 p-3.5 rounded-2xl border border-dashed border-stone-300 font-mono text-xs text-stone-900 leading-tight space-y-2">
                <div class="text-center space-y-0.5">
                    <p class="font-extrabold text-sm tracking-wider uppercase">ES TEH PERKASA</p>
                    <p class="text-[10px] text-stone-600">{{ $outlet->name ?? 'Gerai Utama' }}</p>
                    <p class="text-[10px] text-stone-500">{{ $outlet->address ?? 'Kota Pelayanan' }}</p>
                    <p class="text-[10px] text-stone-500">Telp: {{ $outlet->phone ?? '081111222333' }}</p>
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

            <!-- Tombol Aksi Nota (Ref 15) -->
            <div class="space-y-2 pt-1">
                <button type="button" onclick="closeReceiptModal()"
                    class="w-full py-3 bg-[#0e2a1d] hover:bg-[#163f2d] text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer shadow-xs">
                    <span>→</span> Pesanan Berikutnya
                </button>
                <button type="button" onclick="window.print()"
                    class="w-full py-2.5 bg-white hover:bg-stone-100 border border-stone-300 text-stone-800 font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer">
                    <span>🖨️</span> Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL 4: RIWAYAT NOTA SHIFT AKTIF & VOID
         ============================================== -->
    <div id="history-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-5 shadow-2xl border border-stone-200 flex flex-col space-y-4 max-h-[85vh]">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <div>
                    <h3 class="font-bold text-sm text-stone-900">Riwayat Nota Shift Aktif</h3>
                    <p class="text-xs text-stone-500">Daftar transaksi kasir yang tercatat pada shift ini.</p>
                </div>
                <button type="button" onclick="closeHistoryModal()" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 flex items-center justify-center text-sm font-bold cursor-pointer">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 text-xs">
                @forelse ($recentTransactions as $tx)
                    <div class="p-3 rounded-xl border border-stone-200 bg-stone-50/70 flex items-center justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-stone-900">{{ $tx->invoice_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $tx->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $tx->payment_status }}
                                </span>
                            </div>
                            <p class="text-[11px] text-stone-500 mt-0.5">
                                {{ $tx->created_at->format('H:i') }} WIB &bull; Metode: {{ strtoupper($tx->payment_method) }}
                            </p>
                            <p class="font-mono font-extrabold text-stone-900 mt-1">
                                Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                            </p>
                        </div>

                        @if ($tx->payment_status === 'paid')
                            <button type="button" onclick="promptVoid({{ $tx->id }}, '{{ $tx->invoice_number }}')"
                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg border border-rose-200 text-[11px] transition active:scale-95 cursor-pointer">
                                Batalkan (Void)
                            </button>
                        @else
                            <span class="text-[11px] text-stone-400 italic">Dibatalkan</span>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-stone-400">
                        <p class="text-3xl mb-1">🧾</p>
                        <p class="text-xs">Belum ada nota transaksi pada shift ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL 5: BUKA SHIFT KASIR (MODAL AWAL LACI)
         ============================================== -->
    <div id="start-shift-modal" class="{{ $activeShift ? 'hidden' : '' }} fixed inset-0 z-50 bg-stone-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-stone-200 space-y-4">
            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl bg-[#0e2a1d] text-white flex items-center justify-center text-xl mx-auto mb-2 shadow-xs">
                    💵
                </div>
                <h3 class="font-bold text-base text-stone-900">Buka Shift Kasir</h3>
                <p class="text-xs text-stone-500 mt-1">Masukkan modal awal uang kembalian di laci kasir.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-stone-700 block">Modal Awal Laci (Rp)</label>
                <input type="number" id="shift-opening-cash" value="0" min="0" step="5000"
                    class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-base font-mono font-bold text-stone-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0e2a1d]">
                <div class="grid grid-cols-3 gap-1.5 pt-1">
                    <button type="button" onclick="setShiftOpeningPreset(0)" class="py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold rounded-lg">Rp 0</button>
                    <button type="button" onclick="setShiftOpeningPreset(50000)" class="py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold rounded-lg">50.000</button>
                    <button type="button" onclick="setShiftOpeningPreset(100000)" class="py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-semibold rounded-lg">100.000</button>
                </div>
            </div>

            <button type="button" onclick="submitOpenShift()"
                class="w-full py-3 bg-[#0e2a1d] hover:bg-[#163f2d] text-white font-bold rounded-xl text-xs transition active:scale-95 shadow-xs cursor-pointer">
                Buka Kasir & Mulai Jualan
            </button>
        </div>
    </div>

    <!-- ==============================================
         MODAL 6: TUTUP SHIFT KASIR (REKAP UANG FISIK)
         ============================================== -->
    <div id="close-shift-modal" class="hidden fixed inset-0 z-50 bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-stone-200 space-y-4">
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
                    class="w-full px-3 py-2.5 bg-white border border-stone-300 rounded-xl text-base font-mono font-bold text-stone-900 focus:outline-none focus:ring-2 focus:ring-[#0e2a1d]">
            </div>

            <button type="button" onclick="submitCloseShift()"
                class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition active:scale-95 shadow-xs cursor-pointer">
                Tutup Shift & Hitung Rekap
            </button>
        </div>
    </div>

    <!-- ==============================================
         LOGIKA APLIKASI KASIR POS LENGKAP & REAKTIF
         ============================================== -->
    <script>
        // State Kasir
        let cart = [];
        let activePaymentMethod = 'cash';
        let cashReceived = 0;
        let currentModalProduct = null;
        let selectedVariation = null;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Jam Realtime Topbar
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            const clockEl = document.getElementById('current-clock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Filter Kategori & Pencarian Menu
        function filterCategory(catId) {
            document.querySelectorAll('.cat-pill').forEach(btn => {
                if (btn.dataset.cat === String(catId)) {
                    btn.className = 'cat-pill active px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition cursor-pointer bg-[#0e2a1d] text-white shadow-xs';
                } else {
                    btn.className = 'cat-pill px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition cursor-pointer bg-stone-100 text-stone-600 hover:bg-stone-200 hover:text-stone-900';
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
            const activeCatBtn = document.querySelector('.cat-pill.bg-\\[\\#0e2a1d\\]');
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

        // 3. Modal Pilih Variasi & Topping
        function openProductModal(productId) {
            const card = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (!card) return;

            const name = card.dataset.name;
            const imgSrc = card.dataset.image;
            const variations = JSON.parse(card.dataset.variations || '[]');

            currentModalProduct = { id: productId, name, variations };
            document.getElementById('modal-product-name').textContent = name;
            document.getElementById('modal-product-img').src = imgSrc;

            const varContainer = document.getElementById('modal-variations-list');
            varContainer.innerHTML = '';

            variations.forEach((v, idx) => {
                const isFirst = (idx === 0);
                if (isFirst) selectedVariation = v;

                const div = document.createElement('div');
                div.className = `p-3 rounded-2xl border text-center cursor-pointer transition active:scale-95 variation-choice ${isFirst ? 'border-[#0e2a1d] bg-stone-100 text-stone-900 font-bold shadow-2xs' : 'border-stone-200 bg-white text-stone-700'}`;
                div.dataset.id = v.id;
                div.dataset.price = v.price;
                div.dataset.name = v.variation_name;
                div.onclick = () => selectVariation(v);
                div.innerHTML = `
                    <div class="font-bold text-xs">${v.variation_name}</div>
                    <div class="text-xs font-mono font-black text-stone-900 mt-0.5">Rp ${v.price.toLocaleString('id-ID')}</div>
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
                    el.className = 'p-3 rounded-2xl border text-center cursor-pointer transition active:scale-95 variation-choice border-[#0e2a1d] bg-stone-100 text-stone-900 font-bold shadow-2xs';
                } else {
                    el.className = 'p-3 rounded-2xl border text-center cursor-pointer transition active:scale-95 variation-choice border-stone-200 bg-white text-stone-700';
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
                div.className = 'p-3 rounded-2xl border border-stone-200 bg-white hover:border-stone-300 transition flex items-center justify-between gap-2 shadow-2xs';
                div.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-stone-900 truncate">
                            ${item.product_name} <span class="text-stone-500 font-semibold">(${item.variation_name})</span>
                        </div>
                        <div class="text-[11px] text-stone-400 truncate mt-0.5">
                            ${toppingText ? `<span class="text-amber-800 font-semibold">${toppingText} &bull; </span>` : ''}
                            ${noteText}
                        </div>
                        <div class="text-xs font-mono font-black text-stone-900 mt-1">
                            Rp ${item.subtotal.toLocaleString('id-ID')}
                        </div>
                    </div>

                    <!-- Counter Qty Sentuh Besar -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="changeQty(${index}, -1)"
                            class="w-7 h-7 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs flex items-center justify-center active:scale-90 cursor-pointer">
                            -
                        </button>
                        <span class="w-6 text-center font-bold text-xs font-mono">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${index}, 1)"
                            class="w-7 h-7 rounded-lg bg-[#0e2a1d] hover:bg-[#1a4a34] text-white font-bold text-xs flex items-center justify-center active:scale-90 cursor-pointer">
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
            activePaymentMethod = method;
            const btnCash = document.getElementById('btn-pay-cash');
            const btnQris = document.getElementById('btn-pay-qris');
            const calcBox = document.getElementById('cash-calculator-box');
            const qrisBox = document.getElementById('qris-info-box');
            const checkoutText = document.getElementById('btn-checkout-text');

            if (method === 'cash') {
                btnCash.className = 'py-2.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-900 shadow-xs';
                btnQris.className = 'py-2.5 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition cursor-pointer text-stone-600 hover:text-stone-900';
                calcBox.classList.remove('hidden');
                qrisBox.classList.add('hidden');
                checkoutText.textContent = '✓ Konfirmasi Lunas';
            } else {
                btnQris.className = 'py-2.5 px-3 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer bg-white text-stone-900 shadow-xs';
                btnCash.className = 'py-2.5 px-3 rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition cursor-pointer text-stone-600 hover:text-stone-900';
                calcBox.classList.add('hidden');
                qrisBox.classList.remove('hidden');
                checkoutText.textContent = '📱 Buka QRIS Simulasi';
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

        // NUMPAD TOUCHSCREEN INPUT HANDLERS
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
                changeBox.className = 'px-3 py-2 bg-emerald-50 border border-emerald-300 rounded-xl text-base font-mono font-bold text-emerald-800 flex items-center justify-between';
                checkoutBtn.disabled = false;
            } else {
                const lack = Math.abs(diff);
                changeDisplay.textContent = '- Rp ' + lack.toLocaleString('id-ID');
                changeBox.className = 'px-3 py-2 bg-rose-50 border border-rose-300 rounded-xl text-base font-mono font-bold text-rose-700 flex items-center justify-between';
                checkoutBtn.disabled = true;
            }
        }

        // 6. Alur Checkout (Tunai vs QRIS)
        function initiateCheckout() {
            if (cart.length === 0) return;

            const grandTotal = cart.reduce((acc, i) => acc + i.subtotal, 0);

            if (activePaymentMethod === 'qris') {
                document.getElementById('qris-modal-amount').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                document.getElementById('qris-modal').classList.remove('hidden');
                return;
            }

            // Eksekusi Tunai
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
            const paidAmount = (activePaymentMethod === 'cash')
                ? parseInt(receivedInput.value || 0)
                : grandTotal;

            if (activePaymentMethod === 'cash' && paidAmount < grandTotal) {
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
                    checkoutBtn.textContent = '✓ Konfirmasi Lunas';
                    return;
                }

                // Tampilkan Modal Sukses & Pratinjau Struk (Ref 15)
                showReceiptModal(data, [...cart]);

                // Reset keranjang
                cart = [];
                receivedInput.value = '';
                renderCart();

            } catch (err) {
                alert('Terjadi kendala jaringan/server saat transaksi.');
                console.error(err);
            } finally {
                checkoutBtn.textContent = '✓ Konfirmasi Lunas';
            }
        }

        // 7. Modal Sukses & Pratinjau Nota 58mm (Inspirasi Ref 15)
        function showReceiptModal(txData, items) {
            document.getElementById('success-lunas-amount').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID') + ' Lunas';
            
            const methodLabel = txData.payment_method === 'cash' ? 'Tunai' : 'QRIS';
            document.getElementById('success-breakdown-text').textContent = 
                `${methodLabel} Rp ${txData.paid_amount.toLocaleString('id-ID')} • Kembalian Rp ${txData.change_amount.toLocaleString('id-ID')}`;

            document.getElementById('rc-invoice').textContent = txData.invoice_number;
            document.getElementById('rc-date').textContent = txData.created_at;
            document.getElementById('rc-total').textContent = 'Rp ' + txData.total_amount.toLocaleString('id-ID');
            document.getElementById('rc-method').textContent = methodLabel;
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
                        <span>${i.quantity}x Rp ${i.unitPrice.toLocaleString('id-ID')} ${topText}</span>
                    </div>
                `;
                list.appendChild(itemDiv);
            });

            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
        }

        // 8. Riwayat Nota & Pembatalan (Void)
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

        // 9. Buka & Tutup Shift Kasir
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
