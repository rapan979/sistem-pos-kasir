# ✅ TASK CHECKLIST: VIBE CODING SPRINT (POS SINGLE-STORE)

Gunakan daftar centang (*checklist*) ini sebagai panduan bertahap saat melakukan *vibe coding*. Ceklis satu per satu agar alur pengerjaan tetap fokus, rapi, dan tidak melompat-lompat.

---

## 📌 Fase 1: Fondasi Spesifikasi & Dokumen AI
- [x] Dokumen instruksi AI ([AGENT.md](file:///AGENT.md)) di root proyek.
- [x] Dokumen ruang lingkup ([docs/PRD.md](file:///docs/PRD.md)).
- [x] Dokumen aturan logika bisnis ([docs/BUSINESS_RULES.md](file:///docs/BUSINESS_RULES.md)).
- [x] Dokumen skema database 9 tabel ([docs/DATABASE_DESIGN.md](file:///docs/DATABASE_DESIGN.md)).
- [x] Dokumen pedoman UI/UX & palet teh ([docs/UI_UX_DESIGN.md](file:///docs/UI_UX_DESIGN.md)).
- [x] Arsipkan seluruh dokumen lama ke `docs/archive/`.

---

## 🗄️ Fase 2: Database & Seeder Data Menu
- [x] Konfigurasi koneksi database lokal (MySQL / SQLite).
- [x] Struktur 9 tabel inti kasir:
  - [x] `users` (Kasir & Owner)
  - [x] `categories` (Fresh Tea, Milk Tea, Fruit Tea)
  - [x] `products` (Daftar minuman es teh)
  - [x] `product_variations` (Ukuran cup Regular & Large + harga jual)
  - [x] `toppings` (Boba, Cincau, Cheese Cream)
  - [x] `cashier_shifts` (Modal awal laci & uang fisik tutup)
  - [x] `transactions` (Kepala nota penjualan)
  - [x] `transaction_details` (Item minuman dibeli + kunci harga historis)
  - [x] `transaction_detail_toppings` (Topping per item)
- [x] Seeder data awal terisi lengkap dan siap pakai (`php artisan db:seed`).

---

## 🖥️ Fase 3: Antarmuka Layar Kasir POS (Kiri & Kanan)
- [x] Rute langsung: Login mengarahkan langsung ke `/pos`.
- [x] Header Kasir:
  - [x] Identitas gerai & badge "Sistem Kasir".
  - [x] Jam realtime WIB.
  - [x] Status shift kasir (tombol Buka/Tutup Shift).
  - [x] Tombol Logout.
- [x] Katalog Minuman (Kolom Kiri):
  - [x] Tab kategori menu (*Semua, Fresh Tea, Milk Tea, Fruit Tea*).
  - [x] Kolom pencarian cepat nama produk.
  - [x] Grid kartu menu sentuh dengan target sentuh $\ge 44\text{px}$.
- [x] Modal Pemilihan Menu:
  - [x] Pilihan ukuran cup (*Regular / Large*).
  - [x] Pilihan topping berbayar (*Boba, Cincau, Cheese Cream*).
  - [x] Pilihan level gula (*Normal, Less Sugar, Tanpa Gula, Extra*).
  - [x] Pilihan level es (*Normal Ice, Less Ice, Tanpa Es, Hangat*).
  - [x] Tombol tambah ke keranjang dengan subtotal dinamis.
- [x] Keranjang Belanja (Kolom Kanan):
  - [x] Daftar item pesanan beserta rincian variasi ukuran & topping.
  - [x] Tombol kuantitas $+/-$ sentuh besar.
  - [x] Tombol hapus item dan tombol kosongkan keranjang.
  - [x] Tampilan state kosong jika belum ada pesanan.

---

## 💵 Fase 4: Kalkulator Pembayaran & QRIS Simulasi
- [x] Tampilan Total Belanja besar dan jelas.
- [x] Pemilih metode bayar: **Tunai** vs **QRIS**.
- [x] Kalkulator Tunai:
  - [x] Tombol pecahan cepat: *Uang Pas*, *Rp 10.000*, *Rp 20.000*, *Rp 50.000*, *Rp 100.000*.
  - [x] Input nominal uang diterima.
  - [x] Perhitungan kembalian otomatis realtime (hijau jika pas/lebih, merah jika kurang).
  - [x] Tombol bayar otomatis terkunci (*disabled*) jika uang kurang.
- [x] Alur QRIS Simulasi Lokal:
  - [x] Mode QRIS tidak memerlukan input kembalian.
  - [x] Transaksi QRIS tercatat dengan status lunas setelah konfirmasi kasir.
- [x] Backend Checkout (`PosController@checkout`):
  - [x] Nomor nota unik berurutan: `INV-YYYYMMDD-0001`.
  - [x] Kunci harga transaksi di `price_at_transaction`.
  - [x] Eksekusi aman dalam `DB::transaction()`.

---

## 🖨️ Fase 5: Cetak Nota Thermal 58mm & Riwayat Kasir
- [x] Pratinjau nota kasir otomatis muncul setelah pembayaran selesai.
- [x] Format struk thermal 58mm:
  - [x] Header nama gerai & nomor kontak.
  - [x] Nomor nota, tanggal/jam, nama kasir.
  - [x] Rincian item cup + topping + catatan gula/es.
  - [x] Total, metode bayar, uang diterima, dan kembalian.
  - [x] Footer terima kasih.
- [x] Tombol "Cetak Nota" langsung memicu dialog printer browser (`window.print()`) dengan CSS `@media print` 58mm bersih.
- [x] Tombol "Transaksi Baru" untuk mereset layar kasir.
- [x] Fitur pembatalan (*void*) nota pada shift aktif jika terjadi kesalahan.

---

## 🔒 Fase 6: Buka & Tutup Shift Kasir (Laci Uang)
- [x] Modal Buka Shift: Input modal awal laci (Rp 0 atau uang kembalian).
- [x] Modal Tutup Shift: Input uang fisik aktual di laci saat tutup toko.
- [x] Perhitungan selisih kas otomatis: $\text{aktual} - (\text{modal} + \text{penjualan tunai})$.

---

## 📱 Fase 7: PWA (Progressive Web App) Siap Pasang
- [x] File `manifest.json` dengan `display: standalone` dan tema hijau teh.
- [x] Ikon PWA `192x192` dan `512x512` di folder `public/icons/`.
- [x] Service Worker `sw.js` terdaftar di `app.js`.
- [x] Meta tags PWA di `<head>` layar kasir.
- [x] Tombol "Install App" muncul di browser (Chrome / Edge / Android).
