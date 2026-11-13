# Analisis Kebutuhan Sistem (Final)

**Proyek:** Pengembangan Sistem Kasir (POS) Usaha Es Teh Berbasis Web PWA  
**Konteks PKL:** PT Perkasa Solusindo (Prototype Produk POS Berlisensi untuk UMKM)  
**Teknologi:** Website PWA (Laravel 13, Blade, Tailwind CSS v4, Vite, MySQL/MariaDB)  
**Target Durasi:** ~6 Minggu (12 November – 23 Desember 2026)

---

### 1. Gambaran Umum Sistem
Sistem ini adalah aplikasi **Point of Sale (POS)** atau kasir berbasis **Web PWA (Progressive Web App)** yang dibangun dengan *framework* Laravel dan *database* MySQL/MariaDB. Aplikasi dirancang khusus untuk operasional usaha minuman **Es Teh** milik UMKM, dengan sistem **lisensi berjangka waktu** yang menempel pada akun usaha agar siap dipasarkan/diserahkan kepada UMKM pelanggan PT Perkasa Solusindo.

Untuk kebutuhan simulasi dan demo proyek PKL, aplikasi diisi data contoh **1 usaha es teh simulasi (*Es Teh Nusantara*)** lengkap dengan 2 cabang, daftar menu, variasi ukuran, topping, bahan baku, resep, dan pelanggan contoh — di mana **seluruh data 100% dinamis dan dapat diubah bebas melalui web**.

### 2. Tujuan Sistem
* Mempercepat pelayanan kasir saat menerima pesanan (target pembuatan 1–3 menit per cup).
* Meminimalkan kesalahan hitung total belanja dan uang kembalian.
* Membantu pemilik usaha (*Owner*) memantau omzet, performa tiap cabang, selisih uang kasir, dan pemakaian bahan baku secara akurat.
* Menyediakan sistem POS **berlisensi** dengan aturan pembayaran yang ketat (belum bayar = belum aktif / belum diproses).
* Dapat dikerjakan dan diuji tuntas di lingkungan lokal terlebih dahulu, lalu siap di-*deploy* ke server, domain HTTPS, database, payment gateway, dan sistem PT Perkasa Solusindo.

### 3. Prinsip Desain & Pengalaman Pengguna (Muda sampai Sepuh)
* **100% Bahasa Indonesia Sehari-hari:** Menghindari istilah teknis rumit di layar kasir.
* **Touch-First & Ramah Pemula:** Tombol besar, huruf jelas, kontras tinggi, tombol nominal tunai cepat (*Uang Pas*, `Rp10.000`, `Rp20.000`, `Rp50.000`, `Rp100.000`).
* **Mendukung Mode Solo (Usaha Mikro) maupun Mode Tim (Berkembang):**
  * **Usaha Mikro (1 Orang):** Pemilik cukup memakai **1 akun `Owner` saja** tanpa wajib membuat akun `Kasir` atau mengisi resep bahan baku dulu. Begitu lisensi aktif, 1 gerai utama otomatis tersedia, dan pemilik dapat berpindah antara **Mode Kasir** dan **Mode Kelola & Laporan** dengan satu ketukan.
  * **Usaha Berkembang:** Pemilik dapat menambah cabang (`outlets`) dan membuat akun `Kasir` khusus untuk pegawai.

### 4. Pengguna, Hak Akses (2 Role), & Sistem Lisensi
Aplikasi tidak menggunakan `Super Admin` di dalam aplikasi kasir dan **tidak memiliki akun login untuk pembeli (`Customer`)**. Hanya ada **2 peran internal**:

* **`Owner` (Pemilik Usaha) — Akses Penuh:**
  * Mengelola profil usaha, seluruh cabang (`outlets`), dan akun `Kasir`.
  * Mengelola katalog menu (kategori, produk, variasi ukuran & harga, topping, foto, dan SKU/barcode).
  * Mengelola gudang bahan baku, resep (BOM), stok masuk/penyesuaian, pelanggan, dan pesanan partai besar (B2B).
  * Melihat seluruh laporan penjualan, shift kasir, dan mutasi bahan baku (per cabang maupun gabungan).
  * Mengecek status & masa berlaku **lisensi**, serta melakukan pembayaran perpanjangan langganan atau aktivasi kode lisensi.
  * Dapat langsung membuka **Mode Kasir** untuk ikut melayani penjualan.

* **`Kasir` (Penjaga Booth) — Fokus Layar Jualan:**
  * Akses terbatas hanya di cabang tempatnya bertugas.
  * Membuka dan menutup shift kasir (input modal awal laci dan hitung uang fisik saat tutup).
  * Melayani pesanan di layar POS, menghitung kembalian, menerima pembayaran, dan mencetak struk Bluetooth.
  * Melihat riwayat transaksi pada shift-nya serta membatalkan transaksi (`void`) dengan alasan yang tercatat.
  * **Diblokir dari:** pengaturan menu/harga, gudang bahan baku, pengelolaan akun, laporan owner, dan menu lisensi.

* **Pembeli (`Customer`):**
  * Bukan pengguna login; data pembeli hanya dicatat secara opsional (nama di cup atau data pesanan B2B).

* **Sistem Lisensi & Masa Berlaku (Tanpa Trial):**
  * Setiap akun usaha (`businesses`) terisolasi datanya per `business_id` dan memiliki status lisensi (`inactive`, `active`, `expired`, `suspended`) serta tanggal kadaluarsa (`license_expires_at`).
  * **Tidak ada masa *free trial*:** Pendaftaran akun usaha baru berstatus `inactive` (terkunci) sampai paket langganan dibayar/diaktifkan sejak awal.
  * Jika status bukan `active` atau tanggal berlaku sudah lewat, akses kasir & kelola usaha dikunci dan diarahkan ke halaman **"Aktivasi / Perpanjang Lisensi"** (via TriPay Sandbox atau kode aktivasi/integrasi PT).

### 5. Aturan Ketat Pembayaran & TriPay Sandbox
* **Prinsip Wajib: Belum Bayar = Belum Aktif / Belum Diproses** (berlaku di lokal maupun setelah *deploy*):
  * **Lisensi:** Belum dibayar → status usaha tetap `inactive`/`expired`, fitur terkunci. Baru menjadi `active` setelah status pembayaran terverifikasi **`PAID`**.
  * **Transaksi Kasir (Semua Metode):**
    * **Tunai:** Tidak dapat diselesaikan jika nominal bayar lebih kecil dari total belanja.
    * **QRIS Dinamis (TriPay Sandbox / Gateway):** Checkout awal masuk sebagai `pending` (stok belum terpotong, struk lunas belum dicetak, belum masuk omzet). Baru menjadi `lunas` dan diproses setelah server TriPay mengonfirmasi status **`PAID`** (di lokal lewat tombol *"Cek Status Pembayaran"* ke API Sandbox; di server HTTPS juga otomatis melalui *callback/webhook*).
    * **Transfer / QRIS Statis:** Ditahan di status `pending` sampai pembayaran dikonfirmasi masuk sesuai nominal total.
* **Arsitektur Siap Integrasi Perusahaan:** Kredensial payment gateway (sementara memakai TriPay Sandbox pribadi di `.env`) dan kunci integrasi PT dipisahkan melalui service interface sehingga tinggal ganti `.env` saat dihubungkan ke sistem PT Perkasa Solusindo.

### 6. Daftar Fitur Lengkap (Target 6 Minggu)
1. **Auth, Proteksi 2 Role, & Gerbang Lisensi:** Login/logout, pendaftaran akun usaha baru (tanpa trial), isolasi data per usaha, cek masa berlaku lisensi otomatis, serta halaman pembayaran/aktivasi lisensi (+ endpoint/command aktivasi untuk sistem PT).
2. **PWA Mobile-First & Dukungan Perangkat (*Scalable*):** Install langsung dari browser HP tanpa APK (saat HTTPS), tampilan responsif untuk Tablet/Laptop, kotak scan **Barcode Scanner**, halaman **Customer Display (`/display`)** sinkron *real-time*, dan cetak nota thermal Bluetooth **`58mm`/`80mm`** (Web Bluetooth ESC/POS, bridge Android RawBT, & dialog cetak browser).
3. **Multi-Cabang & Pegawai:** CRUD `outlets` dan akun `Kasir` per cabang.
4. **Katalog Menu Lengkap:** CRUD kategori (`Fresh Tea`, `Milk Tea`, `Fruit Tea`, dll.), produk (+ foto & SKU/barcode, status `is_available`), variasi ukuran & harga (`Regular`, `Large`), dan **topping berbayar** (`Boba`, `Cincau`, `Cheese Cream`).
5. **Halaman Kasir (POS) & Shift Kasir:** Buka/tutup shift kasir (mode cepat modal Rp 0 atau input laci), pilih menu + ukuran + topping + catatan gratis (*Less Sugar / Less Ice*), kalkulator tunai dengan tombol nominal cepat, pembayaran Tunai/Transfer/QRIS Statis/QRIS Dinamis TriPay, nomor nota unik (`INV-YYYYMMDD-001`), kunci harga historis (`price_at_transaction`), riwayat transaksi, dan pembatalan (`void`) tanpa hapus data.
6. **Gudang Bahan Baku & Sistem Resep (BOM):** Master bahan (`Cup 16oz`, `Cup 22oz`, `Biang Teh`, `Gula`, dll.), stok per cabang, stok masuk/penyesuaian, peringatan stok menipis, resep per variasi produk & topping (otomatis terpotong saat transaksi `lunas` dan kembali saat `batal`, tercatat di riwayat mutasi stok). Menu tanpa resep tetap dapat dijual normal.
7. **Pesanan Partai Besar (B2B):** Pencatatan order acara/catering, jadwal pengambilan, pembayaran uang muka (DP), dan pelunasan yang terhubung ke laporan omzet.
8. **Dashboard & Laporan Lengkap (`Owner`):** Omzet harian/bulanan (per cabang & gabungan), total cup terjual, menu terlaris (*best seller*), rekap per metode pembayaran, laporan selisih shift kasir, dan laporan pemakaian bahan baku (siap cetak / ekspor CSV).

### 7. Kebutuhan Nonfungsional
* **Keamanan:** Password di-hash; validasi role, isolasi `business_id`, dan validasi lisensi dilakukan di server (middleware/policy); kunci rahasia hanya di `.env`.
* **Keandalan Data:** Proses checkout, generate nomor nota, dan potong stok berjalan di dalam `DB::transaction()`.
* **Portabilitas & Deploy-Ready:** Berjalan penuh di lokal untuk pengembangan/testing, dan siap dipindahkan ke hosting/server/cloud PT Perkasa Solusindo (`PHP >= 8.3`, MySQL/MariaDB, HTTPS).
