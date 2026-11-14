# ⚖️ ATURAN BISNIS & VALIDASI SISTEM KASIR (BUSINESS RULES)

Dokumen ini mendefinisikan aturan mutlak logika bisnis yang tidak boleh dilanggar dalam penulisan backend maupun frontend.

---

## 1. Aturan Akses & Pengalihan Rute
1. **Tidak Ada Gerbang Lisensi:** Seluruh rute operasional kasir bebas dari pemblokiran status lisensi/voucher langganan.
2. **Akses POS-First:** Begitu pengguna terautentikasi (login), rute akar `/` langsung mengarahkan ke Layar Kasir (`/pos`).

---

## 2. Aturan Shift Buka & Tutup Laci Kasir
1. **Wajib Shift Aktif:** Transaksi kasir hanya dapat diproses apabila terdapat shift kasir yang sedang berstatus `open` untuk petugas/gerai terkait.
2. **Buka Shift (Opening Cash):**
   * Kasir wajib menginput nominal modal awal laci saat memulai operasional.
   * Nilai modal awal diperbolehkan Rp 0 (untuk operasional solo/tanpa uang kembalian) atau nominal rupiah bulat.
3. **Tutup Shift (Closing Cash & Reconciliation):**
   * Kasir wajib menginput jumlah uang fisik aktual yang dihitung di dalam laci kasir saat tutup toko (`actual_cash`).
   * Sistem menghitung ekspektasi uang kas:  
     $$\text{expected\_cash} = \text{opening\_cash} + \sum \text{transaksi tunai lunas}$$
   * Sistem menghitung selisih uang kas:  
     $$\text{difference} = \text{actual\_cash} - \text{expected\_cash}$$
   * Status shift berubah menjadi `closed` dan mencatat waktu tutup (`closed_at`).

---

## 3. Aturan Menu, Variasi, & Topping
1. **Variasi Ukuran Produk:**
   * Setiap produk minuman wajib memiliki minimal 1 variasi ukuran (contoh: *Regular*).
   * Harga jual produk melekat pada variasi ukuran produk, bukan pada kepala produk.
2. **Topping Berbayar (Opsional):**
   * Pelanggan dapat memilih 0 atau lebih topping berbayar (contoh: *Boba +Rp3.000, Cincau +Rp2.000, Cheese Cream +Rp4.000*).
   * Harga topping dikalikan kuantitas produk yang dipesan.
3. **Kustomisasi Gratis:**
   * Pilihan takaran gula (*Normal, Less Sugar, No Sugar, Extra*) dan level es (*Normal Ice, Less Ice, No Ice, Warm*) dicatat sebagai catatan pesanan tanpa menambah biaya.

---

## 4. Aturan Pembayaran Tunai (Cash)
1. **Validasi Uang Cukup:**
   * Nominal uang tunai yang diterima dari pembeli wajib $\ge$ total belanja (`paid_amount >= total_amount`).
   * Tombol checkout wajib dinonaktifkan (*disabled*) jika uang tunai yang diinput kurang.
2. **Kalkulasi Kembalian:**
   * Kembalian dihitung otomatis secara realtime di layar kasir:  
     $$\text{change\_amount} = \text{paid\_amount} - \text{total\_amount}$$
3. **Tombol Pecahan Cepat:**
   * Antarmuka wajib menyediakan tombol nominal cepat: *Uang Pas*, *Rp 10.000*, *Rp 20.000*, *Rp 50.000*, *Rp 100.000* untuk mempercepat input uang.

---

## 5. Aturan Pembayaran QRIS Simulasi Lokal
1. **Alur QRIS Bertahap:**
   * Kasir memilih metode bayar QRIS.
   * Sistem menampilkan kode QR simulasi lokal di layar kasir.
   * Kasir menekan tombol *"Konfirmasi Pembayaran Sukses (Simulasi)"*.
2. **Kunci Pembayaran Lunas:**
   * Status transaksi baru berubah menjadi `paid` setelah konfirmasi simulasi berhasil.
   * Struk nota **HANYA BOLEH DICETAK** jika status transaksi sudah `paid`.
   * Nilai kembalian untuk QRIS otomatis Rp 0 (`change_amount = 0`).

---

## 6. Aturan Integritas Data Nota & Harga Historis
1. **Nomor Nota Unik (Invoice Number):**
   * Format nomor nota bersifat unik dan berurutan harian:  
     `INV-YYYYMMDD-XXXX` (contoh: `INV-20261114-0001`).
2. **Kunci Harga Historis (`price_at_transaction`):**
   * Harga variasi produk dan topping saat transaksi wajib disimpan permanen di tabel rincian transaksi (`transaction_details` dan `transaction_detail_toppings`).
   * Perubahan harga menu di masa depan tidak boleh mengubah nominal total pada nota lama.
3. **Atomic Transaction:**
   * Proses pembuatan nomor nota, penyimpanan transaksi, dan rincian item wajib dibungkus dalam `DB::transaction()` untuk mencegah data corrupt.

---

## 7. Aturan Pembatalan Transaksi (*Void*)
1. **No Hard Delete:** Data transaksi kasir yang pernah tersimpan tidak boleh dihapus dari database demi integritas audit keuangan.
2. **Pencatatan Alasan:**
   * Pembatalan mengubah `payment_status` dari `paid` menjadi `void`.
   * Sistem wajib mencatat identitas kasir yang membatalkan (`voided_by`), tanggal pembatalan (`voided_at`), dan alasan pembatalan (`void_reason`).
