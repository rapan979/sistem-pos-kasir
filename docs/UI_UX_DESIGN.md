# 🎨 PEDOMAN UI/UX SISTEM KASIR — ES TEH PERKASA (UI/UX DESIGN GUIDELINES)

Pedoman ini mengatur **gaya visual (*visual style & theme*)**, interaksi sentuh, dan hierarki komponen untuk Sistem POS Kasir **Es Teh Perkasa**.

> [!IMPORTANT]
> **Prinsip Inti Integrasi Data & Roadmap:**
> Yang diadopsi dari referensi desain adalah **tema warna, gaya kartu (*card style*), bayangan halus (*soft shadow*), dan tata letak visualnya saja**. 
> **Seluruh isi data, kategori, daftar produk minuman, harga, fitur shift kasir, dan alur transaksi TETAP 100% menggunakan data riil dan roadmap proyek kita** (tidak meniru data tiruan/dummy dari gambar).

---

## 1. Identitas Visual & Palet Warna Brand

Palet warna diturunkan langsung dari identitas logo **Es Teh Perkasa** untuk menciptakan kesatuan visual menyeluruh:

| Elemen | Kode Warna | Deskripsi & Penggunaan |
| :--- | :--- | :--- |
| **Latar Belakang (*Background*)** | `#F8F6F0` (Krem Terang / *Off-White*) | Memberikan kesan hangat, bersih, lapang, dan nyaman untuk mata kasir yang menatap layar berjam-jam. Digunakan sebagai dasar halaman utama POS dan latar kolom kanan halaman login. |
| **Warna Utama (*Primary Brand*)** | `#1B4332` (Hijau Gelap / *Dark Forest Green*) | Diambil dari outline daun dan teks logo. Digunakan pada sidebar aktif, tombol aksi utama (*"BAYAR"*, *"Masuk"*), ikon penting, dan tombol bulat tambah `(+)`. |
| **Warna Aksen (*Accent*)** | `#E88A1A` (Jingga Amber / *Tea Liquid*) | Diambil dari warna seduhan es teh pada logo. Digunakan secara terukur untuk badge *"Terlaris"*, status aktif/pending, dan aksen sorotan. |
| **Warna Permukaan (*Surface*)** | `#FFFFFF` (Putih Bersih) | Digunakan pada kontainer Kartu Menu (*Card*), panel keranjang pesanan sebelah kanan, dan kotak form login agar foto produk dan teks kontras maksimal. |
| **Warna Teks (*Typography*)** | `#2D2D2D` (Abu-abu Arang / *Dark Charcoal*) | Menggantikan warna hitam pekat (`#000000`) agar keterbacaan lebih lembut, modern, dan bernuansa premium. |
| **Garis Pembatas (*Borders*)** | `#EBE6DD` (Krem Netral Halus) | Garis tepi subtil 1px untuk membatasi elemen tanpa memberikan kesan kaku atau tebal. |
| **Bayangan Halus (*Soft Shadow*)** | `rgba(27, 67, 50, 0.04)` s/d `0.08` | *Ambient drop shadow* bertingkat lembut (`box-shadow: 0 4px 16px ...`) agar kartu terasa melayang di atas latar krem terang. |

---

## 2. Tipografi & Tata Huruf

* **Font Utama:** Sans-Serif modern berkarakter tebal dan bersih — **Plus Jakarta Sans** / **Inter** / `system-ui`.
* **Hierarki Font:**
  * Judul Halaman / Nama Brand: `text-xl` s/d `text-2xl font-bold tracking-tight text-[#1B4332]`
  * Nama Menu Minuman: `text-sm font-semibold text-[#2D2D2D]`
  * Harga Minuman: `text-sm font-bold text-[#1B4332]`
  * Total Belanja: `text-xl` s/d `text-2xl font-black text-[#1B4332]`
  * Label / Keterangan: `text-xs font-medium text-[#6B7280]`

---

## 3. Arsitektur Tata Letak Layar Kasir (POS)

Menerapkan tata visual modern berbasis kartu dengan pembagian area kerja ergonomis:

```
┌───────────────┬──────────────────────────────────────────┬────────────────────────┐
│ SIDEBAR KIRI  │ KATALOG MENU TENGAH                      │ PANEL PESANAN KANAN    │
│ (Latar Putih/ │ (Latar Krem Terang #F8F6F0)              │ (Latar Putih #FFFFFF)  │
│  Border Kanan)│                                          │                        │
│               │ [🔍 Cari menu es teh...]                 │ Pesanan Aktif (Item)   │
│ [Logo Brand]  │                                          ├────────────────────────┤
│ Es Teh        │ [Semua] [Fresh Tea] [Fruit Tea] [MilkTea]│ Daftar Pesanan Riil:   │
│ Perkasa       │ (Data Kategori Riil Database)            │ Qty +/- & Hapus        │
│               │                                          │ Catatan Sugar/Ice      │
│ [⊞ Kasir POS] │ ┌──────────┐ ┌──────────┐ ┌──────────┐   │                        │
│ [🕒 Riwayat]   │ │Terlaris  │ │          │ │          │   ├────────────────────────┤
│ [🔄 Shift]     │ │[Foto Asli│ │[Foto Asli│ │[Foto Asli│   │ Subtotal:   Rp xx.xxx  │
│               │ │ Menu]    │ │ Menu]    │ │ Menu]    │   │ Diskon:    -Rp  x.xxx  │
│               │ │Es Teh Ori│ │Es Teh Lci│ │Thai Tea  │   │ Pajak:      Rp  x.xxx  │
│               │ │Rp 5k - 7k│ │Rp 7k - 9k│ │Rp 9k- 12k│   │ TOTAL:      Rp xx.xxx  │
│ [👤 Kasir     │ │      [+] │ │      [+] │ │      [+] │   ├────────────────────────┤
│  Aktif Auth]  │ └──────────┘ └──────────┘ └──────────┘   │ [ BAYAR ]  [Tunai/QRIS]│
│ [🚪 Keluar]   │ (Data Produk Riil Database Laravel)      │ *Touchscreen NumPad    │
└───────────────┴──────────────────────────────────────────┴────────────────────────┘
```

### Detail Komponen Layar Kasir:
1. **Sidebar Kiri (Navigasi & Profil Kasir):**
   - Menampilkan Logo resmi "Es Teh Perkasa" di bagian atas.
   - Menu aksi: Layar Kasir, Riwayat Nota (untuk void nota), Kontrol Buka/Tutup Shift kasir.
   - Profil kasir aktif dari session autentikasi Laravel (`auth()->user()->name`).
   - Tombol Keluar (Logout) yang aman.
2. **Katalog Menu Tengah (Data Database Laravel):**
   - **Pencarian Cepat:** Input bar memanjang dengan ikon lup dan sudut membulat (*rounded-full* / *rounded-xl*).
   - **Filter Kategori Dinamis:** Tombol pill yang memuat kategori riil dari database (`Semua`, `Fresh Tea`, `Fruit Tea`, `Milk Tea`).
   - **Kartu Produk (*Product Card*):**
     - Wadah putih bersih (`#FFFFFF`) dengan sudut melengkung 16px (*rounded-2xl*).
     - Efek bayangan halus (*soft ambient shadow*).
     - Foto asli minuman es teh dari folder `images/products/`.
     - Nama produk dan rentang harga riil dari database.
     - Tombol lingkaran aksi cepat `(+)` berwarna Hijau Gelap dengan tanda plus putih (`bg-[#1B4332] active:scale-95`).
     - Mengklik kartu membuka modal pemilihan varian ukuran (*Regular* / *Large*) dan preferensi gula/es sesuai business rules.
3. **Panel Pesanan / Keranjang Kanan:**
   - Berlatar putih murni (`#FFFFFF`) dengan batas halus.
   - Daftar item belanja kasir (bisa ubah kuantitas, hapus, catatan).
   - Ringkasan kalkulasi otomatis: Subtotal, Diskon Promo, Pajak, dan Total Belanja bercetak tebal.
   - Pilihan metode bayar: Tunai dan QRIS Simulasi.
   - **Keypad Sentuh Uang Tunai (*Touch NumPad*):** Terpasang pada panel bayar tunai (tombol 1–9, 0, 000, ⌫, Uang Pas) agar kasir di layar sentuh HP/tablet dapat menginput uang tunai tanpa repot memanggil keyboard bawaan perangkat.
   - **Tombol Bayar Utama:** Lebar penuh, kontras tinggi dengan warna solid Hijau Gelap (`#1B4332`).

---

## 4. Tata Letak Halaman Login Kasir (*Split-Screen*)

* **Kolom Kiri (Desktop/Tablet):**
  - Latar belakang solid **Hijau Gelap** (`#1B4332`).
  - Menampilkan Logo **Es Teh Perkasa** dan identitas brand di bagian tengah secara elegan dan bersih.
* **Kolom Kanan:**
  - Latar belakang **Krem Terang** (`#F8F6F0`).
  - Kartu form login berwarna **Putih Bersih** (`#FFFFFF`) dengan bayangan lembut.
  - Form terfokus khusus untuk akun kasir yang bertugas (sesuai route dan controller auth Laravel).
  - Tombol masuk solid **Hijau Gelap** (`#1B4332`) dengan hover gelap (`#143326`) dan target sentuh minimal 48px.

---

## 5. Standar Eksekusi (Anti AI Slop & Taste Standard)

* ❌ **Tidak ada gradien ungu / neon:** Seluruh nuansa warna menggunakan warna alami daun teh (`#1B4332`), air seduhan teh (`#E88A1A`), dan krem kertas struk (`#F8F6F0`).
* ❌ **Tidak ada emoji sebagai ikon:** Menggunakan foto asli menu dan ikon SVG garis tipis (*feather / lucide style*).
* ✅ **Ergonomi Layar Sentuh:** Semua target sentuh minimal $44 \times 44\text{px}$ dengan umpan balik visual (*tactile scale-95*).
* ✅ **Bahasa Lugas:** Seluruh salinan teks memakai istilah kasir Indonesia yang ramah (*"Semua"*, *"Fresh Tea"*, *"Fruit Tea"*, *"Milk Tea"*, *"Keranjang Kasir"*, *"Subtotal"*, *"Bayar"*).
