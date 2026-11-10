# Research Operasional Usaha Es Teh

**Konteks Dokumen:** Laporan riset sederhana ini disusun sebagai dasar acuan (kebutuhan sistem) dalam merancang aplikasi Point of Sale (POS) atau sistem kasir berbasis website untuk proyek PKL.

---

### 1. Menu dan Variasi Minuman

Berdasarkan pengamatan pada usaha es teh kekinian skala kecil dan menengah di Indonesia, minuman yang dijual saat ini tidak lagi sebatas teh manis biasa. Untuk menarik pelanggan, pemilik usaha mengembangkan berbagai variasi menu.

Varian menu teh ini secara umum terbagi ke dalam beberapa kategori:
*   **Teh Segar (Fresh Tea):** Menu dasar seperti es teh original, es teh melati (jasmine), dan es teh kampul (teh dengan irisan jeruk nipis/lemon).
*   **Teh Susu (Milk Tea):** Campuran teh dengan susu atau krimer, seperti teh tarik, Thai tea, kopi susu gula aren, dan matcha latte.
*   **Teh Buah (Fruit Tea):** Teh dengan perisa atau sirup buah, seperti teh leci (lychee tea) dan teh stroberi.
*   **Teh Kekinian (Flavor/Yoghurt):** Varian manis unik seperti teh rasa taro, teh cokelat, atau campuran teh dengan yoghurt.

### 2. Kategori Produk dan Kisaran Harga

Setiap usaha teh memiliki struktur produk yang memengaruhi cara kasir menginput pesanan. Biasanya produk dibedakan dari ukuran dan *topping*.

*   **Ukuran Cup (Size):** Mayoritas usaha menawarkan dua ukuran, yaitu ukuran Kecil/Reguler (misal 16 oz) dan Besar/Jumbo (misal 22 oz).
*   **Topping Tambahan:** Beberapa usaha membebaskan pelanggan untuk memilih tingkat gula (normal/less sugar) dan tingkat es tanpa biaya tambahan. Namun, untuk *topping* seperti cincau, boba, atau *cheese cream*, biasanya dikenakan biaya ekstra.

**Fakta Kisaran Harga di Lapangan:**
*(Catatan: Harga berikut diambil dari aplikasi pesan antar online untuk merek "Es Teh Solo" di area Surabaya dan sekitarnya. Harga ini bisa berbeda-beda tergantung merek usaha, lokasi, dan apakah pembeli datang langsung ke outlet atau membeli via online)*
*   **Es Teh Original:** Rp 5.000 - Rp 6.100 (Reguler) hingga Rp 7.000 - Rp 10.100 (Jumbo).
*   **Es Teh Kampul/Lemon:** Sekitar Rp 7.000 hingga Rp 10.100.
*   **Es Teh Rasa (Taro, Leci, Susu):** Berkisar antara Rp 9.500 hingga Rp 15.000.

### 3. Alur Pemesanan dan Transaksi

**Fakta Lapangan:** Operasional usaha es teh (berbentuk *booth* atau gerobak) sangat mengandalkan kecepatan. Durasi pembuatan minuman biasanya hanya 1-3 menit per gelas.

**Analisis Alur Transaksi:**
1.  **Pemesanan:** Pelanggan melihat spanduk menu, lalu menyebutkan pesanannya kepada kasir.
2.  **Input POS:** Kasir memilih produk di sistem aplikasi kasir, memasukkan ukuran minuman, lalu menekan jumlah pesanan (qty).
3.  **Pembayaran:** Sistem POS menampilkan total harga. Kasir menerima uang atau pelanggan melakukan *scan* QRIS. Kasir kemudian mencetak nota (jika menggunakan printer *thermal*).
4.  **Penyiapan Produk:** Kasir atau pegawai peracik membuat minuman sesuai daftar pesanan yang masuk.
5.  **Pesanan Selesai:** Minuman diserahkan kepada pelanggan. Jika terjadi kesalahan sebelum minuman dibuat, kasir dapat melakukan pembatalan (*void*) pesanan di sistem.

### 4. Metode Pembayaran

Agar sistem POS berjalan optimal untuk usaha es teh di Indonesia, sistem perlu mencatat metode pembayaran yang paling umum digunakan pembeli:
*   **Uang Tunai (Cash):** Pembayaran paling banyak digunakan. Kasir perlu memasukkan jumlah uang yang dibayarkan pelanggan, dan sistem harus bisa menampilkan angka "Kembalian" agar kasir tidak salah menghitung.
*   **QRIS:** Pelanggan *scan barcode* yang ada di meja kasir menggunakan *mobile banking* atau dompet digital (GoPay, OVO, Dana). Kasir biasanya mengecek mutasi di HP, lalu mencatatnya secara manual di POS sebagai pembayaran "QRIS".
*   **Transfer Bank / E-Wallet Khusus:** Digunakan untuk pemesanan dalam jumlah banyak (misal: pesanan borongan untuk acara).
*(Catatan Analisis: Pada usaha UMKM skala kecil, sistem pembayaran QRIS biasanya berjenis "Statis" atau manual, bukan Payment Gateway otomatis, sehingga aplikasi POS cukup mencatat tipe pembayarannya saja).*

### 5. Laporan Penjualan

Pemilik usaha (*owner*) tidak selalu bisa berada di gerobak atau *booth* setiap waktu. Oleh karena itu, fitur laporan pada sistem POS sangat dibutuhkan untuk memantau performa usahanya.

Informasi laporan yang paling dibutuhkan meliputi:
*   **Laporan Ringkas Harian:** Total pendapatan (omzet) hari tersebut dan jumlah cup yang berhasil terjual. Angka omzet tunai di sistem akan digunakan untuk mencocokkan jumlah uang fisik yang ada di laci kasir saat tutup *booth*.
*   **Laporan Produk Terlaris (Best Seller):** Grafik atau tabel yang menunjukkan menu apa yang paling laku. Ini sangat berguna bagi pemilik untuk menentukan stok bahan baku (biang teh, gula, dan rasa tertentu) untuk hari berikutnya.
*   **Laporan Metode Pembayaran:** Rincian pembagian pendapatan dari uang tunai dan QRIS, agar pemilik mudah memeriksa mutasi rekening bank miliknya.

### 6. Kesimpulan Hasil Research

**Rekomendasi untuk Sistem POS:**
Dari riset sederhana ini, dapat disimpulkan bahwa dalam merancang aplikasi kasir untuk usaha es teh berbasis website menggunakan Laravel, sistem tidak perlu dibuat terlalu rumit. Fokus utama yang perlu dikembangkan adalah:
1.  **Dinamis:** Sistem harus mengizinkan *admin* mengatur nama produk, ukuran (kategori), dan menentukan harganya sendiri.
2.  **Cepat & Praktis:** Halaman kasir (POS) harus sederhana dengan tombol yang besar dan tidak membutuhkan banyak "klik" agar pelayanan bisa cepat.
3.  **Kalkulator Otomatis:** Wajib ada perhitungan uang kembalian untuk pembayaran tunai.
4.  **Laporan Intuitif:** Harus tersedia halaman rekap penjualan harian dan daftar menu terlaris untuk mempermudah pemantauan oleh *owner*.

### 7. Daftar Sumber

*   Menu Es Teh Solo (Cabang Plinggisan) via GrabFood. Menunjukkan variasi harga menu reguler, jumbo, dan teh kekinian. Akses: 9 November 2026. Tautan: `https://food.grab.com/id/id/restaurant/es-teh-solo-plinggisan-delivery/6-C62DJPMDCVB1TN`
*   Menu Es Teh Solo (Cabang Tenggilis Mejoyo) via GoFood. Menunjukkan variasi teh original, *fresh tea series*, *matcha series*, hingga ukuran *cup* minuman. Akses: 9 November 2026. Tautan: `https://gofood.co.id/surabaya/restaurant/es-teh-solo-ubaya-tenggilis-mejoyo-63321c50-89f7-4363-a9b9-ada42c207bd1`
*   Menu Es Teh Solo Keputih Surabaya via GrabFood. Menunjukkan daftar teh buah (leci). Akses: 9 November 2026. Tautan: `https://food.grab.com/id/id/restaurant/es-teh-solo-keputih-jl-kejawan-gebang-no-25-delivery/6-C7JUVN3CLPLYN6`