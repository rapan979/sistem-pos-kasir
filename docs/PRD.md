# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD)

---

## 1. Ringkasan Produk & Visi
* **Nama Produk:** Sistem POS Kasir Es Teh Perkasa
* **Konteks:** Proyek PKL di **PT Perkasa Solusindo**
* **Visi:** Menyediakan aplikasi kasir berbasis web PWA yang sangat cepat, mudah digunakan oleh kasir muda hingga sepuh, bebas dari kesalahan kalkulator kembalian, dan siap mencetak nota thermal 58mm untuk gerai UMKM minuman es teh.

---

## 2. Masalah yang Diselesaikan (*Problem Statement*)
1. **Kecepatan Transaksi:** Pembuatan minuman es teh berlangsung sangat cepat (1–3 menit per cup). Antarmuka kasir tidak boleh rumit atau memerlukan banyak klik.
2. **Kesalahan Uang Kembalian:** Pembayaran tunai sering salah hitung jika dilakukan secara manual di tengah antrean ramai.
3. **Selisih Uang Kasir:** Tanpa sistem pencatatan shift buka-tutup laci, uang fisik di laci kasir saat tutup toko sering tidak cocok dengan penjualan.
4. **Nota Kertas Rentan Rusak:** Pencatatan manual di kertas sering basah terkena tumpahan es teh atau hilang.

---

## 3. Ruang Lingkup Proyek (*Project Scope*)

### A. Fitur Utama yang Dikerjakan (*IN-SCOPE - MVP*)
1. **Layar Kasir (POS-First):**
   * Pengguna login langsung masuk ke Layar Kasir (`/pos`).
   * Tab filter kategori menu (*Semua, Teh Klasik, Teh Buah, Premium & Susu*) dan pencarian cepat.
   * Modal pemilihan ukuran cup (*Reguler / Large*), serta takaran gula dan level es.
   * Keranjang belanja dinamis dengan tombol tambah/kurang kuantitas instan $+/-$, hapus per item, dan kosongkan keranjang.
2. **Kalkulator Pembayaran Cepat:**
   * **Tunai:** Tombol pecahan uang cepat (Rp 10.000, Rp 20.000, Rp 50.000, Rp 100.000), Keypad Layar Sentuh (*Touch NumPad*), dan perhitungan kembalian otomatis.
   * **QRIS Simulasi:** Menampilkan kode QR simulasi lokal + tombol konfirmasi status lunas sebelum nota dicetak.
3. **Buka & Tutup Shift Kasir:**
   * Modal awal laci kasir dicatat saat buka shift.
   * Uang fisik aktual dihitung saat tutup shift, dengan laporan selisih otomatis (*difference*).
4. **Cetak Struk Thermal 58mm:**
   * Pratinjau nota kasir dan tombol cetak langsung memicu dialog printer (`window.print()`).
5. **Riwayat Transaksi & Pembatalan (*Void*):**
   * Menampilkan transaksi pada shift aktif.
   * Fitur pembatalan nota (*void*) dengan mencatat alasan (data nota tidak dihapus).
6. **Progressive Web App (PWA):**
   * File `manifest.json` dan Service Worker untuk caching asset dan instalasi aplikasi mandiri di HP Android / Windows Desktop.

### B. Fitur yang Disimpan untuk Fase Nanti (*OUT-OF-SCOPE / ROADMAP PHASE 2*)
* ⏳ **Topping Tambahan Berbayar (Phase 2):** Skema tabel `toppings` dan model backend telah siap, disembunyikan sementara dari layar kasir fase 1 agar fokus pada varian minuman.
* ❌ Pemesanan mandiri oleh pembeli via website (*Customer Web Order*).
* ❌ Sistem multi-cabang, distribusi stok antar gerai, dan franchise monitoring.
* ❌ Sistem lisensi komersial berbayar / voucher langganan.
* ❌ Payment gateway uang asli pihak ketiga.
* ❌ Modul gudang bahan baku resep kompleks (BOM per gram).

---

## 4. Persona Pengguna
* **Kasir Booth:** Menuntut kemudahan tombol sentuh besar, kecepatan memilih menu, dan kalkulator uang pas agar antrean pembeli cepat terlayani.
* **Pemilik Toko (*Owner*):** Menuntut kepastian bahwa uang fisik di laci saat tutup toko cocok dengan total penjualan di sistem kasir.

---

## 5. Kriteria Keberhasilan (*Success Metrics*)
* Waktu input pesanan dari memilih produk hingga siap cetak nota $\le 45\text{ detik}$.
* Perhitungan uang kembalian 100% akurat tanpa kesalahan manual.
* Format nota 58mm tercetak rapi dan proporsional di printer thermal.
