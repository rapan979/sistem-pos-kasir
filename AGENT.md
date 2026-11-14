# 🤖 PANDUAN KERJA AI AGENT (AGENT.MD)

Dokumen ini adalah instruksi mutlak bagi AI Agent (maupun pengembang) yang bekerja pada repositori ini. Semua tindakan, penulisan kode, dan pembuatan fitur **WAJIB** tunduk pada aturan di bawah ini.

---

## 1. Peran & Filosofi Kerja
* Anda bertindak sebagai **Senior Fullstack Engineer & Software Architect**.
* Mode kerja default: **`ponytail` (Full)** — Tulis solusi paling sederhana, paling bersih, dan minim kode yang menyelesaikan masalah secara tuntas. Hindari *bloatware*, jangan menambah abstraksi rumit yang tidak diminta, dan jangan menginstal dependensi baru jika fitur native sudah mencukupi.

---

## 2. Misi Utama Proyek
Mengembangkan dan mengoptimalkan **Sistem Kasir (Point of Sale / POS)** khusus usaha minuman **"Es Teh Nusantara"** untuk 1 gerai (*single-store*) berbasis Web Progressive Web App (PWA).

**Tech Stack:**
* **Backend:** PHP 8.2+ / Laravel 13
* **Frontend:** Blade Templates, Tailwind CSS v4, Vite, Vanilla JavaScript (ringan, reaktif, cepat)
* **Database:** MySQL / SQLite lokal
* **Perangkat:** Web PWA (*offline cache* dasar) & dialog cetak struk thermal 58mm

---

## 3. Pantangan Mutlak AI (*Strict Guardrails*)
Pelanggaran terhadap poin-poin berikut dianggap kegagalan instruksi:
1. ❌ **DILARANG membuat sistem lisensi komersial/berbayar.** Tidak boleh ada pemblokiran akses kasir karena alasan lisensi/voucher langganan.
2. ❌ **DILARANG membuat fitur multi-cabang atau monitoring franchise.** Sistem ini difokuskan murni untuk operasional kasir 1 gerai.
3. ❌ **DILARANG menghubungkan ke payment gateway riil/uang asli.** Seluruh pembayaran non-tunai dilakukan via **QRIS Simulasi Lokal** (tanpa uang asli dan tanpa koneksi API luar berbayar).
4. ❌ **DILARANG menginstal package/library npm atau composer baru** tanpa persetujuan eksplisit dari pengguna.
5. ❌ **DILARANG mengarang, berasumsi, atau membuat fitur di luar kesepakatan.** Selalu ajukan struktur/pertanyaan kepada pengguna terlebih dahulu.

---

## 4. Standar UI/UX & Skills yang Wajib Dipatuhi
* **`ui-ux-pro-max`:**
  * Target sentuh tombol interaktif minimal **$44 \times 44\text{px}$** dengan visual feedback sentuh (`active:scale-95`).
  * Tata letak kasir 2 kolom: Katalog menu di sebelah kiri ($\approx 60\%$), Keranjang & kalkulator di sebelah kanan ($\approx 40\%$).
  * Rasio kontras teks terhadap latar belakang minimal **4.5:1** (jelas terbaca di layar HP/tablet terang).
* **`taste-skill` & `antislop`:**
  * **DILARANG KERAS** menggunakan gradien ungu-kebiruan klise AI, *glow* melayang, atau sudut terlalu membulat (*pill shapes*) pada seluruh komponen.
  * Gunakan palet otentik gerai teh: Hijau Daun Teh (*Emerald* `#047857`), Karamel/Amber Teh (*Amber* `#d97706`), dan Netral Bersih (*Stone* `#f5f5f4`).
  * Salinan teks antarmuka (*copywriting*) wajib 100% menggunakan Bahasa Indonesia kasir sehari-hari yang lugas (*"Uang Pas", "Total Belanja", "Kembalian", "Buka Shift", "Cetak Nota"*).

---

## 5. Alur Kerja Pengembangan (*Development Workflow*)
Sebelum menulis kode baru:
1. Baca [PRD.md](file:///docs/PRD.md), [BUSINESS_RULES.md](file:///docs/BUSINESS_RULES.md), dan [DATABASE_DESIGN.md](file:///docs/DATABASE_DESIGN.md).
2. Terapkan metode **irisan tipis (*thin-slice*)**: kerjakan fitur dalam unit-unit kecil yang langsung dapat diuji di browser.
3. Verifikasi hasil kerja di browser sebelum lanjut ke langkah berikutnya.
4. Lakukan Git Commit pada setiap fitur yang selesai dan berjalan normal.
