# LAPORAN AUDIT ENVIRONMENT & DOKUMEN SISTEM

**Proyek:** Pengembangan Sistem Kasir (POS) untuk Usaha Es Teh<br>
**Tanggal Audit:** 10 November 2026<br>
**Auditor:** Senior Laravel Developer & Development Environment Auditor<br>
**Status Workspace:** `C:\Users\HP 845 G8\Documents\PKL 2\Projek\sistem-pos-kasir`

---

## 1. Ringkasan Kondisi Workspace & Dokumentasi

### A. Kondisi Folder Workspace
- **Lokasi Direktori:** `C:\Users\HP 845 G8\Documents\PKL 2\Projek\sistem-pos-kasir`
- **Struktur Saat Ini:**
  - Hanya terdapat satu folder bernama `docs/` yang memuat seluruh file analisis, riset, rancangan database, dan diagram sistem (`docs/Diagrams/`).
  - Belum terdapat file inisialisasi Git (`.git/`).
  - Belum terdapat kerangka kerja aplikasi Laravel (tidak ditemukan file `artisan`, `composer.json`, folder `app`, `config`, dsb.).
  - Workspace dalam keadaan bersih dan siap untuk inisialisasi tahap awal.

### B. Ringkasan Dokumen Proyek
Seluruh dokumen pendukung pada direktori `docs/` telah diperiksa dan siap menjadi acuan implementasi teknis:
1. **`docs/riset.md`**: Memuat riset operasional usaha es teh di lapangan (varian menu *fresh tea*, *milk tea*, *fruit tea*, pembagian ukuran cup 16 oz dan 22 oz, kisaran harga Surabaya Rp3.000–Rp15.000, durasi operasional kasir 1–3 menit, pembayaran tunai & QRIS statis, serta kebutuhan rekap omzet harian).
2. **`docs/analisis-kebutuhan.md`**: Memuat latar belakang masalah pencatatan manual, batasan peran pengguna (*Owner/Admin* dan *Kasir*), kebutuhan fungsional & nonfungsional, serta batasan jelas fitur **MVP (Minimum Viable Product)** versus fitur pengembangan tahap lanjut (printer thermal, inventory bahan baku, dynamic QRIS, dan B2B).
3. **`docs/Flowchart.md` & `docs/Diagrams/`**: Memuat diagram alur transaksi kasir serta topologi sistem (Web Browser Client $\rightarrow$ HTTPS $\rightarrow$ Web Server Apache/Nginx $\rightarrow$ Laravel $\rightarrow$ MySQL). Dilengkapi diagram `.drawio` dan file visual `.png` (`Flowchart.drawio.png`, `Topologi-sistem.drawio.png`, `rancangan-database-final.drawio.png`).
4. **`docs/rancangan-database.md`**: Memuat skema 7 tabel inti untuk tahap MVP:
   - `users`: Autentikasi dan hak akses (*owner*, *admin*, *kasir*).
   - `categories`: Pengelompokan varian menu es teh.
   - `products`: Data master produk minuman.
   - `product_variations`: Relasi variasi ukuran cup (*regular/large*) dan harga produk.
   - `customers`: Data pembeli (opsional).
   - `transactions`: Kepala nota transaksi penjualan, total bayar, nominal bayar, kembalian, dan metode pembayaran.
   - `transaction_details`: Rincian item pesanan, kuantitas, harga saat transaksi (*price_at_transaction*), dan subtotal.

---

## 2. Hasil Pemeriksaan Aktual Environment

Pemeriksaan dilakukan secara langsung melalui terminal sistem tanpa memodifikasi file, tanpa mengubah konfigurasi sistem, dan tanpa mengubah variabel lingkungan.

### A. Tabel Status Software & Komponen

| Komponen / Perangkat Lunak | Status Ketersediaan | Versi Terdeteksi | Jalur Eksekusi (Path) / Catatan Lokasi |
| :--- | :--- | :--- | :--- |
| **PHP** | **PERLU DIPERIKSA LEBIH LANJUT** | PHP 8.2.12 (cli) | `C:\xampp\php\php.exe` (Belum terdaftar di Environment PATH) |
| **Composer** | **BELUM TERSEDIA** | Tidak Ditemukan | Tidak ditemukan di PATH, ProgramData, maupun direktori XAMPP |
| **MySQL / MariaDB** | **PERLU DIPERIKSA LEBIH LANJUT** | Ver 15.1 Distrib 10.4.32-MariaDB | `C:\xampp\mysql\bin\mysql.exe` (Belum di PATH & Service mati) |
| **Git** | **SUDAH TERSEDIA** | git version 2.56.0.windows.4 | `C:\Program Files\Git\cmd\git.exe` (Terdaftar di PATH) |
| **Node.js** | **SUDAH TERSEDIA** | v25.8.0 | `C:\Program Files\nodejs\node.exe` (Terdaftar di PATH) |
| **npm** | **SUDAH TERSEDIA** | 11.11.0 | `C:\Program Files\nodejs\npm.ps1` (Terdaftar di PATH) |
| **Visual Studio Code** | **SUDAH TERSEDIA** | 1.145.2 (x64) | `C:\Users\HP 845 G8\AppData\Local\Programs\Microsoft VS Code\bin\code.cmd` |
| **Ekstensi PHP** | **PERLU DIPERIKSA LEBIH LANJUT** | Konfigurasi `php.ini` | Driver MySQL aktif, namun ekstensi `zip`, `intl`, `gd` nonaktif |
| **Proyek Laravel** | **BELUM TERSEDIA** | Belum Ada | Belum diinisialisasi di folder workspace |

---

### B. Rincian Temuan Pemeriksaan Aktual

#### 1. PHP
- **Pemeriksaan Terminal Global:** Perintah `php -v` menghasilkan pesan error `CommandNotFoundException` karena perintah `php` belum terdaftar pada variabel lingkungan (`PATH`) Windows.
- **Pemeriksaan Lokasi Lokal:** Ditemukan instalasi PHP bawaan paket XAMPP pada `C:\xampp\php\php.exe`.
- **Versi Terpasang:** `PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)`.
- **Kompatibilitas:** Versi PHP 8.2.12 sudah memenuhi syarat minimum untuk menjalankan framework Laravel 10 maupun Laravel 11.

#### 2. Composer
- **Pemeriksaan Terminal:** Perintah `composer` tidak terdeteksi di terminal.
- **Pemeriksaan Direktori Umum:** Telah dilakukan penelusuran pada `C:\ProgramData\ComposerSetup`, `%APPDATA%\Composer`, `C:\xampp\php\composer.bat`, serta `C:\xampp\php\composer.phar`. Tidak ditemukan executable Composer di sistem.
- **Kondisi:** Composer belum terpasang di komputer.

#### 3. MySQL / MariaDB
- **Pemeriksaan Terminal:** Perintah `mysql` belum terdaftar pada variabel lingkungan (`PATH`).
- **Pemeriksaan Lokasi Lokal:** Server database terpasang di dalam paket XAMPP:
  - File biner klien: `C:\xampp\mysql\bin\mysql.exe`
  - File biner server: `C:\xampp\mysql\bin\mysqld.exe`
  - Versi terpasang: `10.4.32-MariaDB for Win64 (AMD64)`
- **Status Layanan (Port 3306):** Pengujian koneksi TCP ke `127.0.0.1:3306` menghasilkan status `False / TCP connect failed`. Layanan daemon MySQL/MariaDB pada XAMPP saat ini sedang dalam kondisi **berhenti (*stopped*)**.

#### 4. Git
- **Pemeriksaan Terminal:** Perintah `git --version` berhasil dieksekusi normal.
- **Lokasi Biner:** `C:\Program Files\Git\cmd\git.exe` (sudah terdaftar di System PATH).
- **Versi:** `git version 2.56.0.windows.4`.
- **Status Repositori:** Eksekusi `git status` menghasilkan `fatal: not a git repository`. Repositori Git lokal belum diinisialisasi pada workspace.

#### 5. Node.js dan npm
- **Pemeriksaan Terminal:** Berhasil dieksekusi dan sudah terdaftar di System PATH.
- **Node.js:** Versi `v25.8.0` (`C:\Program Files\nodejs\node.exe`).
- **npm:** Versi `11.11.0` (`C:\Program Files\nodejs\npm.ps1`).
- **Kondisi:** Siap digunakan untuk build tool aset frontend (Vite, Tailwind CSS, Alpine.js).

#### 6. Visual Studio Code
- **Pemeriksaan Terminal:** Terdaftar di PATH Pengguna (`code.cmd`).
- **Lokasi:** `C:\Users\HP 845 G8\AppData\Local\Programs\Microsoft VS Code\bin\code.cmd`.
- **Versi:** `1.145.2` (Commit: `c4453f8ea430795f941c1b2ff6c9321135a74a02`, Arsitektur x64).
- **Kondisi:** Siap digunakan sebagai kode editor utama.

#### 7. Ekstensi PHP (`C:\xampp\php\php.ini`)
Berdasarkan pembacaan modul aktif (`php -m`) dan konfigurasi file `C:\xampp\php\php.ini`:

| Ekstensi | Fungsi untuk Laravel / Database | Status Aktual |
| :--- | :--- | :--- |
| `pdo_mysql` | Koneksi database MySQL dengan PDO | **AKTIF** |
| `mysqli` & `mysqlnd` | Driver komunikasi MySQL asli | **AKTIF** |
| `mbstring` | Pemrosesan string multi-byte UTF-8 | **AKTIF** |
| `openssl` | Enkripsi data, token session, dan hashing | **AKTIF** |
| `fileinfo` | Validasi tipe MIME file/gambar | **AKTIF** |
| `ctype`, `tokenizer`, `xml`, `dom` | Parser internal framework Laravel | **AKTIF** |
| `bcmath`, `curl` | Komputasi presisi tinggi & request HTTP | **AKTIF** |
| `zip` | Ekstraksi file paket zip oleh Composer | **NONAKTIF** (tertulis `;extension=zip`) |
| `intl` | Format mata uang (IDR Rupiah) & internasionalisasi | **NONAKTIF** (tertulis `;extension=intl`) |
| `gd` | Manipulasi dan pengubahan ukuran foto menu produk | **NONAKTIF** (tertulis `;extension=gd`) |

#### 8. Proyek Laravel
- Folder `c:\Users\HP 845 G8\Documents\PKL 2\Projek\sistem-pos-kasir` belum memiliki kerangka kerja Laravel.
- Belum ada file `.env`, file `artisan`, maupun direktori standar Laravel (`app/`, `bootstrap/`, `config/`, `database/`, `routes/`, dll.).

---

## 3. Daftar Masalah yang Ditemukan (Issues)

Berikut daftar kendala aktual di lingkungan kerja yang perlu ditindaklanjuti sebelum pengembangan dimulai:

1. **PHP belum dikenali secara global di terminal:** Lokasi `C:\xampp\php` belum didaftarkan ke dalam variabel lingkungan `PATH` Windows.
2. **Composer belum terpasang di sistem:** Paket manajer Composer belum ada, sehingga instalasi paket Laravel belum dapat dijalankan.
3. **Ekstensi PHP penting masih nonaktif:** Ekstensi `zip`, `intl`, dan `gd` pada file `C:\xampp\php\php.ini` masih dalam keadaan tertutup tanda titik koma (`;`). Ketiadaan ekstensi `zip` dapat menghambat proses Composer saat mengekstrak paket zip dependensi Laravel.
4. **Layanan database MySQL belum berjalan:** Modul MySQL pada XAMPP belum dijalankan sehingga port 3306 belum aktif dan koneksi basis data belum dapat dilakukan.
5. **Jalur biner MySQL belum terdaftar di PATH:** `C:\xampp\mysql\bin` belum didaftarkan di `PATH` Windows (opsional, mempermudah perintah CLI MySQL).
6. **Workspace belum diinisialisasi Git:** Belum ada repositori Git lokal untuk melacak perubahan kode dan dokumentasi.
7. **Proyek Laravel belum diinisialisasi:** Folder proyek belum memuat fondasi framework Laravel.

---

## 4. Rekomendasi Teknis & Penyesuaian Stack Teknologi

*(Bagian ini memuat rekomendasi pemilihan versi perangkat lunak berdasarkan standar dan dokumentasi resmi terbaru per 2026, terpisah dari hasil pemeriksaan kondisi aktual di atas).*

### A. Rekomendasi Versi Komponen & Stack

1. **Rekomendasi Framework Laravel:**
   - **Laravel 10 dan Laravel 11:** **Tidak direkomendasikan** untuk proyek baru karena siklus rilis dan masa dukungannya telah terlewati.
   - **Laravel 13 (Kandidat Utama):** Menjadi kandidat utama apabila server target mendukung PHP minimal versi 8.3 (sesuai spesifikasi rilis Laravel 13).
   - **Laravel 12 (Alternatif Sementara):** Hanya menjadi alternatif sementara jika server produksi/deployment masih menggunakan PHP 8.2, dengan catatan masa dukungan perbaikan bug dan keamanannya terbatas.

2. **Rekomendasi PHP Runtime:**
   - **PHP 8.5 (Kandidat Utama):** Menjadi pilihan utama untuk stabilitas performa, fitur modern, dan siklus dukungan jangka panjang.
   - **PHP 8.4 (Alternatif):** Menjadi alternatif yang stabil dan matang jika server belum siap mengadopsi PHP 8.5.
   - *Catatan:* Keputusan akhir versi PHP lokal wajib disesuaikan dengan lingkungan server perusahaan.

3. **Rekomendasi Database MariaDB / MySQL:**
   - **Status Versi Lokal:** MariaDB 10.4.32 yang ditemukan pada instalasi XAMPP lokal sudah tidak lagi mendapatkan pemeliharaan resmi (*End of Life*).
   - **Kandidat Database Baru:** Untuk lingkungan pengembangan lokal yang didukung, disarankan menggunakan versi MariaDB LTS (*Long-Term Support*) yang masih aktif dipelihara, misalnya **MariaDB 11.4 LTS** atau **MariaDB 11.8 LTS**.
   - *Catatan:* Tetap utamakan kesesuaian dengan database yang digunakan pada server perusahaan.

4. **Rekomendasi Node.js & npm (Frontend Tooling):**
   - **Status Versi Lokal:** Node.js v25.8.0 yang terdeteksi merupakan versi ganjil dengan masa dukungan singkat dan sudah mencapai akhir masa hidup (*End of Life / EOL*).
   - **Rekomendasi:** Untuk keandalan proses build aset frontend, gunakan rilis LTS resmi:
     - **Node.js 24 LTS:** Sebagai pilihan konservatif yang matang dan stabil.
     - **Node.js 26 LTS:** Sebagai pilihan modern dengan siklus dukungan jangka panjang terbaru.

5. **Penjelasan dan Rekomendasi Ekstensi PHP:**
   - **`pdo_mysql`:** Pastikan tetap aktif untuk koneksi aplikasi ke database MySQL / MariaDB.
   - **`zip`:** Direkomendasikan aktif untuk membantu Composer menangani dan mengekstrak paket dependensi dengan optimal.
   - **`gd` dan `intl`:** Bersifat opsional/sesuai kebutuhan fitur atau package yang digunakan (misalnya `gd` untuk pengolahan gambar menu dan `intl` untuk lokalisasi angka/mata uang). Ekstensi-ekstensi ini bukan merupakan persyaratan wajib mutlak bagi inti framework Laravel tanpa dasar kebutuhan library tertentu.

---

### B. Keputusan Versi Teknologi Menunggu Konfirmasi Server Perusahaan

Sebelum melakukan upgrade versi atau modifikasi permanen pada lingkungan pengembangan lokal:
1. **Verifikasi Lingkungan Server Target:** Seluruh penetapan keputusan versi teknologi (PHP, MariaDB/MySQL, Laravel, dan Node.js) **wajib menunggu konfirmasi spesifikasi teknis server perusahaan / instansi tempat PKL**.
2. **Kesesuaian Lingkungan (Dev-Prod Parity):** Versi runtime dan basis data pada perangkat pengembang harus selaras dengan server target untuk mencegah kendala ketidakcocokan (*incompatibility*) saat tahap implementasi/deployment.
3. **Poin Konfirmasi dengan Pembimbing / Tim IT Perusahaan:**
   - Versi PHP yang tersedia/didukung di server perusahaan (apakah mendukung PHP 8.3/8.4/8.5 atau masih menggunakan PHP 8.2).
   - Jenis dan versi database server (MySQL 8.x atau MariaDB 10.x / 11.x LTS).
   - Penentuan versi framework Laravel (Laravel 13 sebagai target utama atau Laravel 12 sebagai alternatif transisi).

---

### C. Rekomendasi Urutan Persiapan Teknis

Setelah konfirmasi spesifikasi server perusahaan diperoleh, berikut urutan persiapan teknis yang disarankan:

#### Tahap 1: Konfigurasi PHP dan Penyesuaian `php.ini`
1. Tambahkan direktori PHP yang disepakati ke dalam `PATH` Environment Variables (User atau System) di Windows.
2. Buka file konfigurasi `php.ini`, lalu pastikan:
   - `pdo_mysql` aktif untuk koneksi database.
   - Aktifkan `extension=zip` untuk mendukung operasional Composer.
   - Aktifkan `extension=intl` dan `extension=gd` sesuai kebutuhan package atau fitur aplikasi.

#### Tahap 2: Pemasangan Composer
1. Unduh installer resmi Composer Windows (`Composer-Setup.exe`) dari [getcomposer.org](https://getcomposer.org/doc/00-intro.md).
2. Jalankan instalasi dan arahkan biner PHP ke executable PHP yang aktif.
3. Verifikasi instalasi di terminal:
   ```bash
   composer --version
   ```

#### Tahap 3: Menjalankan dan Mengonfigurasi Database (MySQL / MariaDB)
1. Buka aplikasi **XAMPP Control Panel** (atau service MariaDB LTS terpasang).
2. Nyalakan modul database dan pastikan berjalan lancar pada port `3306`.
3. (Opsional) Daftarkan direktori biner database ke Environment Variables `PATH` Windows untuk kemudahan akses CLI.

#### Tahap 4: Inisialisasi Repositori Git
1. Buka terminal pada folder root proyek (`sistem-pos-kasir`).
2. Jalankan inisialisasi repositori Git lokal:
   ```bash
   git init
   ```
3. Siapkan file `.gitignore` standar untuk mengamankan kredensial konfigurasi (`.env`) dan folder vendor/node_modules.

#### Tahap 5: Inisialisasi Proyek Laravel Sesuai Target Server
1. Lakukan pembuatan proyek Laravel sesuai hasil konfirmasi server:
   - Menggunakan **Laravel 13** (kandidat utama) apabila server mendukung PHP $\ge$ 8.3.
   - Menggunakan **Laravel 12** (alternatif sementara) apabila server masih berada di PHP 8.2.
   ```bash
   composer create-project laravel/laravel .
   ```
2. Konfigurasikan koneksi basis data pada `.env` (tanpa menyertakan password rahasia).
3. Jalankan pengujian server lokal Laravel:
   ```bash
   php artisan serve
   ```

---

### D. Tautan Dokumentasi Resmi

Informasi siklus rilis, kompatibilitas, dan panduan deployment merujuk pada dokumentasi resmi berikut:
- **Laravel Releases:** https://laravel.com/docs/13.x/releases
- **Laravel Deployment Guide:** https://laravel.com/docs/13.x/deployment
- **PHP Supported Versions:** https://www.php.net/supported-versions.php
- **MariaDB About & Release Schedule:** https://mariadb.org/about/
- **Node.js Previous Releases & EOL Schedule:** https://nodejs.org/en/about/previous-releases
- **Composer Official Documentation:** https://getcomposer.org/doc/00-intro.md
