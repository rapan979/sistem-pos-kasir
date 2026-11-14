# 🎨 PEDOMAN UI/UX SISTEM KASIR (UI/UX DESIGN GUIDELINES)

Pedoman ini memastikan antarmuka kasir cepat digunakan, tidak melelahkan mata, dan ramah terhadap pengguna pemula maupun senior (usia lanjut).

---

## 1. Tata Letak Layar Kasir (*Layout Architecture*)

Antarmuka kasir menggunakan pembagian **2 Kolom Responsif**:

```
┌────────────────────────────────────────────────────────────────────────┐
│ HEADER: Logo • Es Teh Nusantara • Jam Realtime • Info Shift • Logout   │
├──────────────────────────────────────┬─────────────────────────────────┤
│ KOLOM KIRI: KATALOG MENU (~60%)      │ KOLOM KANAN: KERANJANG (~40%)   │
│ - Bar Pencarian Cepat                │ - Header Pesanan + Clear All    │
│ - Tab Kategori (Pill Buttons)        │ - List Item (Qty +/-, Hapus)    │
│ - Grid Kartu Minuman Es Teh:         ├─────────────────────────────────┤
│   • Ikon & Kategori                  │ PANEL BAYAR (STICKY BOTTOM):    │
│   • Nama Minuman                     │ - TOTAL BELANJA (Besar & Jelas) │
│   • Rentang Harga                    │ - Tombol Metode: [Tunai] [QRIS] │
│   • Klik Kartu -> Buka Modal         │ - Tombol Uang Cepat (10k, 20k)  │
│                                      │ - Input Uang & Kembalian Realtime│
│                                      │ - TOMBOL BAYAR & CETAK NOTA     │
└──────────────────────────────────────┴─────────────────────────────────┘
```

---

## 2. Prinsip Touch-First (Ramah Sentuhan Jari)
1. **Target Sentuh Minimum:**  
   Seluruh tombol, opsi variasi, kuantitas $+/-$, dan tombol pecahan uang memiliki ukuran fisik minimal **$44 \times 44\text{px}$** dengan jarak (*spacing*) minimal **$8\text{px}$** antar tombol.
2. **Umpan Balik Visual Sentuhan (*Tactile Feedback*):**  
   Setiap tombol wajib memiliki efek transisi halus saat ditekan (`active:scale-95` atau `active:scale-[0.98]`).
3. **Papan Ketik Cepat Uang Tunai:**  
   Kasir tidak perlu mengetik angka rupiah secara manual untuk nominal lazim; antarmuka menyediakan tombol cepat:  
   `[Uang Pas]`, `[Rp 10.000]`, `[Rp 20.000]`, `[Rp 50.000]`, `[Rp 100.000]`.

---

## 3. Skema Warna Brand & Desain Otentik (*Taste Standard*)
* **Aturan Anti-Slop:**  
  ❌ DILARANG menggunakan gradien ungu-kebiruan klise AI (*generic blue-purple AI gradient*).  
  ❌ DILARANG menggunakan *glow* berlebihan atau kartu melayang yang mengaburkan batas tombol.  
  ❌ DILARANG menerapkan bentuk lonjong (*pill shape*) secara berlebihan di semua komponen.
* **Palet Warna Otentik Gerai Es Teh:**
  * **Hijau Daun Teh (*Primary Brand*):** `Emerald 700` (`#047857`) dan `Emerald 800` (`#065f46`). Memberikan kesan segar dan dapat dipercaya.
  * **Cokelat/Amber Teh (*Accent & Topping*):** `Amber 600` (`#d97706`) dan `Amber 700` (`#b45309`). Mewakili warna seduhan teh manis dan topping boba.
  * **Netral Bersih (*Surface & Background*):** `Stone 100` (`#f5f5f4`) dan `Stone 50` (`#fafaf9`). Mencegah kelelahan mata kasir saat menatap layar berjam-jam.
  * **Teks Kontras Tinggi:** `Stone 900` (`#1c1917`) pada latar terang. Rasio kontras wajib $\ge 4.5:1$.

---

## 4. Format Cetak Struk Thermal 58mm
Nota penjualan dirancang khusus untuk kertas thermal 58mm dengan isolasi gaya cetak `@media print`:
* Font nota menggunakan gaya monospace (`Courier New`, `monospace`) berukuran 10–11px.
* Elemen pembatas menggunakan garis putus-putus (`border-dashed`).
* Elemen struk:
  1. **Header:** Nama Usaha ("ES TEH NUSANTARA"), Alamat Gerai, Nomor Kontak.
  2. **Info Transaksi:** Nomor Nota, Tanggal/Jam, Nama Kasir.
  3. **Tabel Item:** Nama Minuman & Ukuran Cup, Catatan Topping/Gula, Kuantitas $\times$ Harga Satuan, Subtotal.
  4. **Ringkasan:** TOTAL, Metode Pembayaran, Bayar, Kembalian.
  5. **Footer:** Pesan ucapan terima kasih ("Segar & Nikmat Setiap Hari").
