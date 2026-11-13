---
name: pos-browser-testing
description: Panduan otomatisasi pengujian antarmuka browser untuk sistem POS kasir es teh (login, lisensi terkunci, navigasi shift, transaksi kasir, dan validasi hitungan uang).
---

# Panduan Pengujian Browser Otomatis Sistem POS Kasir

Skill ini digunakan saat melakukan uji coba antarmuka (UI testing) dan alur kerja aplikasi di browser Chrome / DevTools:

## 1. Skenario Uji Coba Wajib (Core Test Cases)
1. **Tes Login 2 Role:**
   - Login `owner@esteh.test` -> wajib diarahkan ke `/dashboard` (status lisensi aktif).
   - Login `kasir@esteh.test` -> wajib diarahkan ke `/pos` (toko/outlet Gerai Pusat).
   - Login salah password -> wajib muncul pesan error ramah tanpa crash.
2. **Tes Gerbang Lisensi Terkunci:**
   - Login `unpaid@esteh.test` -> wajib dialihkan ke `/license/locked`.
   - Coba buka `/dashboard` atau `/pos` secara paksa lewat URL -> wajib tetap dilempar kembali ke `/license/locked`.
   - Klik tombol simulasi pembayaran lisensi -> status lisensi menjadi `active` dan halaman dashboard langsung terbuka.
3. **Tes Perhitungan Kasir:**
   - Pilih menu Es Teh + Topping -> subtotal dan total belanja terhitung presisi rupiah.
   - Masukkan uang bayar tunai lebih kecil dari total -> tombol bayar dinonaktifkan / muncul peringatan.
   - Masukkan nominal uang pas / lebih -> uang kembalian terhitung otomatis tanpa selisih 1 rupiah pun.
4. **Tes Mode Cepat Owner:**
   - Dari dashboard, klik "Buka Mode Kasir" -> layar berpindah dalam < 1 detik.
   - Dari layar kasir, klik "Mode Kelola" -> kembali ke dashboard tanpa logout.
