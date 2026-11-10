# Flowchart dan Topologi Sistem

## 1. Flowchart Proses Transaksi

Flowchart berikut menggambarkan alur proses
transaksi pada sistem kasir usaha es teh,
mulai dari kasir login sampai transaksi selesai.

![Flowchart Transaksi](diagrams/flowchart.drawio.png)

Penjelasan Alur Transaksi:

Proses Login: Kasir memulai hari dengan membuka alamat website POS dan memasukkan kredensial. Jika salah, sistem akan menolak dan mengembalikan kasir ke halaman login. Jika benar, kasir diarahkan ke halaman utama (Dashboard POS).

Proses Pemesanan: Kasir melayani pembeli dengan memilih gambar atau nama menu es teh, menentukan variasi (seperti ukuran cup atau tambahan topping), lalu memasukkannya ke dalam keranjang virtual (cart).

Konfirmasi & Pembayaran: Kasir memeriksa kembali daftar pesanan bersama pelanggan. Setelah dikonfirmasi, kasir memilih metode pembayaran (misal: tunai atau QRIS).

Validasi Pembayaran: Jika uang yang dibayarkan kurang atau proses QRIS gagal, sistem akan menahan transaksi dan meminta kasir mengulang proses pembayaran.

Penyelesaian: Jika pembayaran berhasil divalidasi, data transaksi akan langsung disimpan permanen ke dalam database. Sistem kemudian memunculkan nota transaksi untuk dicetak, dan secara otomatis memperbarui angka pada laporan penjualan hari tersebut.

## 2. Topologi Sistem

Topologi sistem berikut menggambarkan hubungan
antara pengguna, perangkat, aplikasi Laravel,
database MySQL, dan komponen pendukung lainnya.

![Topologi Sistem](diagrams/Topologi-sistem.drawio.png)

Penjelasan Hubungan Komponen Sistem:

Perangkat Kasir (Client): Kasir beroperasi menggunakan Laptop atau Tablet yang menjalankan Web Browser (seperti Chrome atau Firefox).

Perangkat Keras Tambahan (Hardware): Barcode scanner dan printer thermal terhubung langsung ke laptop/tablet secara fisik (via USB) atau nirkabel (via Bluetooth). Keduanya berinteraksi dengan laptop lokal, bukan langsung ke server.

Catatan Integrasi: Penggunaan barcode scanner biasanya langsung berfungsi layaknya keyboard eksternal. Namun, untuk Printer Thermal, karena aplikasi berbasis web, pencetakan nota mungkin membutuhkan konfigurasi dialog print browser bawaan, ekstensi browser tambahan, atau driver lokal agar ukuran nota pas dan tercetak rapi tanpa header/footer bawaan browser.

Jaringan (Internet): Web browser pada perangkat kasir mengakses sistem melalui sebuah nama Domain/Subdomain (misal: kasir.esteh.com) melalui jaringan internet dengan protokol aman (HTTPS).

Infrastruktur Server: Permintaan dari domain diterima oleh Web Server (Apache/Nginx). Web Server menjalankan kode Aplikasi Laravel. Laravel kemudian berkomunikasi dengan Database MySQL untuk membaca daftar menu atau menyimpan data pesanan.

Backup & Pihak Ketiga: Database MySQL secara berkala disalin ke media Backup Database untuk keamanan data. Sementara itu, jika sistem nantinya menggunakan QRIS dinamis, Aplikasi Laravel akan berkomunikasi dengan server pihak ketiga melalui API Payment Gateway.

3. Kesimpulan
Rancangan sistem ini menunjukkan bahwa aplikasi POS berjalan secara terpusat di server (berbasis web), sehingga beban kerja hardware di gerai es teh sangat ringan karena hanya membutuhkan browser. Alur transaksinya dirancang linear dan divalidasi pada tahap login serta pembayaran untuk memastikan keamanan data. Topologi di atas juga memperlihatkan pemisahan yang jelas antara perangkat keras yang ada di lokasi jualan (client) dengan mesin pemroses yang berada di cloud (server), menjadikannya sangat fleksibel untuk dikembangkan atau digunakan pada banyak cabang sekaligus.