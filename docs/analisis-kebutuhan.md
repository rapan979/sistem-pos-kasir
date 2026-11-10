Berikut adalah dokumen Analisis Kebutuhan Sistem yang disusun dengan bahasa yang mudah dipahami, terstruktur, dan sangat cocok untuk Anda jadikan bahan diskusi atau laporan kepada pembimbing PKL di tempat magang.

---

# Analisis Kebutuhan Sistem

**Proyek:** Pengembangan Sistem Kasir (POS) untuk Usaha Es Teh
**Teknologi:** Website (Laravel & MySQL)

### 1. Gambaran Umum Sistem

Sistem ini adalah sebuah aplikasi Point of Sale (POS) atau kasir berbasis website yang dibangun menggunakan *framework* Laravel dan *database* MySQL. Aplikasi ini dirancang khusus untuk mempermudah operasional usaha minuman kekinian (es teh), di mana kecepatan pelayanan sangat diutamakan. Sistem ini akan menggantikan proses pencatatan nota dan rekap manual dengan proses digital yang terpusat.

### 2. Tujuan Sistem

Tujuan utama dari pembuatan sistem kasir ini adalah:

* Mempercepat proses pelayanan kasir saat menerima pesanan pelanggan.
* Meminimalkan kesalahan perhitungan total belanja dan uang kembalian.
* Mempermudah pemilik usaha (*owner*) dalam memantau pendapatan harian secara *real-time* tanpa harus datang ke gerai/booth.
* Merapikan pencatatan data penjualan agar tidak ada riwayat transaksi yang hilang.

### 3. Permasalahan yang Ingin Diselesaikan

Jika usaha es teh masih menggunakan pencatatan manual (kertas dan kalkulator), masalah yang sering muncul antara lain:

* **Antrean Lambat:** Kasir butuh waktu lama untuk menghitung pesanan dengan banyak variasi (beda ukuran, beda topping).
* **Kesalahan Hitung (Human Error):** Sering terjadi salah hitung uang kembalian, terutama saat jam sibuk.
* **Rekapitulasi Sulit:** Saat tutup toko (*closing*), kasir atau pemilik harus menghitung ulang nota kertas satu per satu untuk mencocokkan uang di laci.
* **Kecurangan:** Sulit melacak jika ada pesanan yang dijual tetapi uangnya tidak masuk ke laci kasir, karena tidak ada sistem keamanan.

### 4. Pengguna dan Hak Akses

Sistem ini dirancang untuk digunakan oleh beberapa jenis peran (*role*), yaitu:

* **Owner / Admin:**
* Hak akses penuh ke seluruh fitur aplikasi.
* Bisa menambah, mengubah, atau menghapus daftar menu dan harga.
* Bisa melihat seluruh laporan penjualan dan riwayat transaksi.
* Bisa membuat dan mengelola akun kasir.


* **Kasir (Cashier):**
* Hak akses terbatas.
* Hanya bisa mengakses halaman kasir (membuat pesanan) dan melihat riwayat transaksi pada *shift* atau hari tersebut.
* Tidak bisa mengubah harga dasar atau menghapus data menu.


* **Pelanggan (Customer):**
* Bukan pengguna yang *login* ke dalam sistem, tetapi sebagai pihak yang berinteraksi dengan kasir dan menerima bukti transaksi (struk).



### 5. Kebutuhan Fungsional

Kebutuhan fungsional adalah apa saja yang **harus bisa dilakukan** oleh sistem:

* Sistem dapat melakukan *login* dan *logout* sesuai *role* pengguna.
* Sistem dapat menyimpan, mengedit, dan menghapus data kategori (misal: *Fresh Tea, Milk Tea*) dan data produk (nama menu, variasi ukuran, dan harga).
* Sistem dapat memasukkan pesanan ke dalam keranjang belanja (*cart*).
* Sistem dapat menghitung total belanja secara otomatis, termasuk tambahan biaya untuk *topping*.
* Sistem dapat menghitung uang kembalian jika pembayaran menggunakan uang tunai.
* Sistem dapat menampilkan laporan omzet harian.

### 6. Kebutuhan Nonfungsional

Ini adalah hal-hal yang mendukung kinerja sistem agar nyaman digunakan:

* **Keamanan:** *Password* pengguna harus dienkripsi. Kasir tidak boleh bisa mengakses halaman laporan milik *owner*.
* **Kemudahan Penggunaan (User Friendly):** Tampilan halaman kasir harus sederhana, menggunakan tombol besar, dan tidak butuh banyak "klik" agar kasir bisa melayani pesanan dengan cepat.
* **Kecepatan (Performance):** Aplikasi website harus ringan dan cepat memuat data agar tidak menyebabkan antrean pembeli.

---

### 7. Daftar Fitur Utama (MVP - Minimum Viable Product)

Ini adalah fitur-fitur wajib yang akan Anda kerjakan pertama kali agar aplikasi bisa langsung dites dan dipakai untuk jualan:

1. **Manajemen Pengguna (Auth):** Fitur *login/logout* dan pembagian hak akses (Admin & Kasir).
2. **Kelola Katalog Menu:** Fitur CRUD (*Create, Read, Update, Delete*) untuk kategori, produk, variasi ukuran, dan harga *topping*.
3. **Halaman Kasir (Point of Sale):** Halaman utama tempat kasir menekan gambar/nama produk, menambah ke keranjang, dan melakukan *checkout*.
4. **Kalkulator Transaksi:** Perhitungan otomatis total bayar dan fitur *input* uang tunai untuk melihat kembalian. Menyediakan opsi pencatatan pembayaran QRIS/Transfer (secara manual).
5. **Riwayat Transaksi:** Tabel yang berisi daftar nota yang sudah lunas.
6. **Laporan Sederhana:** Halaman yang menampilkan total cup terjual dan total uang masuk hari ini.

### 8. Fitur Pengembangan (Tahap Selanjutnya)

Fitur-fitur ini sangat bagus, tetapi **tidak wajib dibuat di awal**. Fitur ini akan ditambahkan setelah fitur utama (MVP) selesai dan berjalan lancar:

* **Integrasi Printer Thermal:** Mencetak struk secara otomatis langsung dari *browser*. *(Catatan: Perlu konfirmasi ke pembimbing apakah ada perangkat printer yang bisa dipinjam untuk testing).*
* **Manajemen Stok Bahan Baku:** Menghitung sisa biang teh, gula, dan *cup* plastik secara otomatis setiap ada transaksi. *(Catatan: Perlu diskusi dengan pembimbing karena logika pemotongan stok minuman lebih rumit dibanding barang eceran).*
* **QRIS Dinamis (Payment Gateway):** Memunculkan *barcode* QRIS otomatis dengan nominal yang pas (misal via Midtrans/Xendit) sehingga kasir tidak perlu cek mutasi manual.
* **Barcode Scanner:** Pemindai *barcode* (biasanya jarang dipakai di usaha es teh, tapi bisa dikembangkan jika ada minuman kemasan botol jadi).
* **Multi-outlet / Cabang:** Mengelola banyak *booth* es teh dalam satu aplikasi.
* **Pelanggan B2B (Pesanan Partai Besar):** Fitur untuk mencatat pesanan *catering* atau acara dalam jumlah ratusan *cup* yang biasanya menggunakan DP (Down Payment) atau pelunasan di belakang.

### 9. Kesimpulan

Untuk proyek PKL ini, pengembangan sistem kasir usaha es teh berbasis Laravel sebaiknya berfokus pada **Fitur Utama (MVP)** terlebih dahulu. Membangun halaman kasir yang cepat, akurat dalam menghitung transaksi, dan mampu mencatat omzet harian adalah prioritas mutlak. Jika waktu PKL dan progres pengerjaan masih memungkinkan, barulah sistem ditingkatkan dengan menambahkan fitur-fitur kompleks seperti manajemen stok, integrasi QRIS otomatis, dan cetak *printer thermal*. Pendekatan bertahap ini akan memastikan proyek PKL Anda selesai tepat waktu dengan hasil yang berfungsi dengan baik (bebas *error*).