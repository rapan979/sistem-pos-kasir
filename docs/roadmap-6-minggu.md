# Roadmap Pengerjaan 6 Minggu (Final)

**Periode:** 12 November – 23 Desember 2026 (~6 Minggu)  
**Lingkungan Kerja:** Full pengembangan & uji di laptop lokal dulu → deploy ke server, domain HTTPS, database, & hardware PT Perkasa Solusindo setelah jadi dan *proper*.

---

### Minggu 1 (12 – 18 November 2026): Fondasi Database, Lisensi, Auth, & PWA
* **Target:**
  * Sinkronisasi seluruh dokumentasi di `docs/` dengan rancangan final.
  * Penambahan migration & model untuk **19 tabel** (termasuk penyesuaian 2 role `owner` & `kasir`).
  * Pembaruan seeder data simulasi **Es Teh Nusantara** (`active`, 2 cabang, menu + ukuran + topping, bahan baku + resep dasar, pelanggan) + akun uji `inactive` untuk tes kunci lisensi.
  * Login/Logout, Daftar Usaha Baru (tanpa trial → `inactive`), middleware 2 role & cek masa berlaku lisensi ketat, halaman aktivasi/bayar lisensi (+ command/endpoint integrasi PT).
  * Kerangka PWA (`manifest.json`, ikon app, service worker dasar) + layout utama Bahasa Indonesia ramah muda–sepuh (tombol 1-ketuk pindah *Mode Kasir* ↔ *Mode Kelola* untuk `Owner`).

### Minggu 2 (19 – 25 November 2026): Multi-Cabang, Pegawai, Katalog Menu, Topping, & Pelanggan
* **Target:**
  * Manajemen cabang (`outlets`) dan akun `Kasir` per cabang (oleh `Owner`).
  * CRUD Kategori, Produk (+ foto opsional, SKU/barcode, toggle `is_available`), Variasi Ukuran/Harga, dan **Topping Berbayar**.
  * Manajemen Pelanggan (`reguler` dan `b2b`).

### Minggu 3 (26 November – 2 Desember 2026): Layar Kasir (POS), Shift Kasir, & Riwayat Nota
* **Target:**
  * Buka & Tutup Shift Kasir (ramah usaha solo: modal Rp 0 cepat atau isi laci + rekap selisih uang saat tutup).
  * Layar POS mobile-first ramah sepuh (tombol besar, cari/filter kategori, pilih ukuran + topping + catatan gula/es gratis, tombol nominal tunai cepat + *Uang Pas*).
  * Checkout ketat (`Tunai`, serta `pending → lunas` untuk Transfer/QRIS statis), nomor nota aman bentrok (`INV-YYYYMMDD-001`), kunci `price_at_transaction`.
  * Riwayat transaksi & pembatalan (`void` dengan alasan tercatat).

### Minggu 4 (3 – 9 Desember 2026): Integrasi TriPay Sandbox & Dukungan Perangkat Kasir
* **Target:**
  * Integrasi `PaymentGatewayInterface` → `TriPayGatewayService` (Sandbox pribadi di `.env`) untuk:
    1. Aktivasi/perpanjang lisensi `Owner` (wajib status `PAID` baru aktif).
    2. QRIS Dinamis di kasir (status `pending` sampai `PAID` terkonfirmasi via tombol *"Cek Status Pembayaran"* di lokal atau callback HTTPS).
  * Halaman cetak struk thermal `58mm`/`80mm` + dukungan cetak **Bluetooth** (Web Bluetooth ESC/POS, RawBT Android, fallback print browser).
  * Halaman **Customer Display (`/display`)** sinkron *real-time* & dukungan **Barcode Scanner** di layar POS.

### Minggu 5 (10 – 16 Desember 2026): Gudang Bahan Baku (BOM) & Pesanan B2B
* **Target:**
  * Master bahan baku (`ingredients`), stok per cabang (`outlet_ingredient_stocks`), input stok masuk/penyesuaian, riwayat `stock_movements`, & peringatan stok menipis.
  * Pengaturan Resep (BOM) per variasi produk & topping → otomatis terpotong saat transaksi `lunas` & kembali saat `batal` (menu tanpa resep tetap dapat dijual).
  * Modul Pesanan Partai Besar (**B2B**): catat order acara/catering, jadwal ambil, DP & pelunasan yang terhubung ke `transactions`/omzet.

### Minggu 6 (17 – 23 Desember 2026): Laporan Lengkap, Pengujian, & Deploy ke Perusahaan
* **Target:**
  * Dashboard & Laporan `Owner`: omzet harian/bulanan (per cabang & gabungan), jumlah cup terjual, menu terlaris, rekap metode bayar, selisih shift kasir, & laporan pemakaian bahan (siap cetak / ekspor CSV).
  * *Automated testing* (isolasi usaha, proteksi 2 role, blokir lisensi belum bayar/expired, transaksi ketat, potong/balik stok BOM, callback TriPay) + uji menyeluruh di lokal.
  * Deploy ke **domain/hosting HTTPS, server, & database PT Perkasa Solusindo**, uji install **PWA di HP**, uji **printer thermal Bluetooth** fisik pinjaman perusahaan, serta uji callback otomatis TriPay Sandbox.
  * Finalisasi dokumen & panduan pemakaian untuk serah terima PKL.
