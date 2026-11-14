# Rancangan Database Sistem Kasir (Final — 19 Tabel)

## 1. Gambaran Umum Database
Bayangkan database ini seperti lemari arsip yang rapi di usaha es teh. Untuk mendukung sistem **berlisensi (multi-UMKM)**, **multi-cabang**, **topping berbayar**, **stok bahan baku berbasis resep (BOM)**, **shift kasir**, dan **pesanan B2B**, arsip dibagi menjadi 4 kelompok besar dengan total **19 tabel inti**:

1. **Usaha, Lisensi, & Akun (4 tabel):** `businesses`, `subscription_payments`, `outlets`, `users`
2. **Menu & Topping (4 tabel):** `categories`, `products`, `product_variations`, `toppings`
3. **Gudang & Resep BOM (4 tabel):** `ingredients`, `outlet_ingredient_stocks`, `recipes`, `stock_movements`
4. **Kasir, Nota, & B2B (7 tabel):** `customers`, `cashier_shifts`, `transactions`, `transaction_details`, `transaction_detail_toppings`, `b2b_orders`, `b2b_order_items`

**Aturan Penting yang Tetap Dijaga:**
* **Kunci Harga Historis:** Harga saat transaksi disalin ke `price_at_transaction` dan `subtotal` disimpan permanen agar perubahan harga menu di masa depan tidak mengubah nota lama.
* **Isolasi Data UMKM:** Data usaha dipisahkan oleh `business_id` agar antar-UMKM tidak saling melihat data.
* **Audit & Keamanan:** Data transaksi yang dibatalkan (`batal`) tidak dihapus, dan master yang pernah dipakai transaksi dilindungi dengan `onDelete('restrict')`.

---

## 2. Daftar Lengkap 19 Tabel

### Kelompok A: Usaha, Lisensi, & Akun
1. **`businesses`**: Profil usaha UMKM, kode lisensi unik (`license_key`), paket (`plan_name`), status lisensi (`inactive`, `active`, `expired`, `suspended`), tanggal berakhir (`license_expires_at`), serta konfigurasi TriPay opsional per usaha.
2. **`subscription_payments`**: Catatan tagihan & riwayat pembayaran langganan/aktivasi lisensi usaha (referensi TriPay, nominal, durasi hari yang ditambahkan, status `pending`/`paid`/`expired`/`failed`).
3. **`outlets`**: Daftar cabang/booth milik suatu `business` (`name`, `address`, `phone`, `receipt_footer`, `is_active`). Saat usaha baru dibuat, otomatis dibuatkan 1 gerai utama.
4. **`users`**: Akun login pegawai internal dengan **`role` = `ENUM('owner', 'kasir')`**, terhubung ke `business_id` dan `outlet_id` (nullable untuk `owner`), plus status `is_active`.

### Kelompok B: Katalog Menu & Topping
5. **`categories`**: Kelompok menu per usaha (misal `Fresh Tea`, `Milk Tea`, `Fruit Tea`). Kombinasi `(business_id, name)` unik.
6. **`products`**: Daftar menu minuman per usaha (`category_id`, `name`, `sku_barcode` opsional untuk barcode scanner, `image` nullable, `is_available`).
7. **`product_variations`**: Ukuran gelas dan harga jual tiap produk (misal `Regular` Rp5.000, `Large` Rp7.000) + `barcode` opsional per ukuran. Kombinasi `(product_id, variation_name)` unik.
8. **`toppings`**: Daftar tambahan berbayar per usaha (misal `Boba`, `Cincau`, `Cheese Cream`) beserta `price` dan status `is_available`.

### Kelompok C: Gudang Bahan Baku & Sistem Resep (BOM)
9. **`ingredients`**: Master bahan baku usaha (`business_id`, `name` misal `Cup 16oz`, `Biang Teh`, `Gula Cair`, satuan `unit` seperti `pcs`/`ml`/`gram`, dan `min_stock` untuk batas peringatan stok menipis).
10. **`outlet_ingredient_stocks`**: Sisa stok nyata masing-masing bahan di tiap cabang (`outlet_id`, `ingredient_id`, `stock`). Kombinasi `(outlet_id, ingredient_id)` unik.
11. **`recipes`**: Takaran resep bahan baku (`ingredient_id`, `quantity_used`) untuk setiap 1 unit variasi produk (`product_variation_id`) atau 1 porsi topping (`topping_id`).
12. **`stock_movements`**: Riwayat mutasi stok tiap cabang (`masuk`, `keluar_transaksi`, `batal_transaksi`, `penyesuaian`) beserta pelaku (`user_id`), `quantity`, dan `notes` (referensi nota/keterangan).

### Kelompok D: Pelanggan, Shift Kasir, Transaksi, & B2B
13. **`customers`**: Data pelanggan opsional per usaha (`name`, `phone`, `type`: `reguler` / `b2b`, `company_name`). Pelanggan tidak login.
14. **`cashier_shifts`**: Catatan buka & tutup kasir per cabang (`outlet_id`, `user_id`, `opened_at`, `closed_at`, `opening_cash`, `expected_cash`, `actual_cash`, `difference`, `status`: `open`/`closed`, `notes`).
15. **`transactions`**: Kepala struk penjualan per usaha & cabang:
    * `invoice_number` (unik, format `INV-YYYYMMDD-001`)
    * `business_id`, `outlet_id`, `cashier_shift_id` (nullable), `b2b_order_id` (nullable), `user_id`, `customer_id` (nullable)
    * `total_amount`, `paid_amount`, `change_amount`
    * `payment_method`: `ENUM('tunai', 'qris', 'transfer')` *(e-wallet dicatat ke `qris` atau `transfer`)*
    * `payment_channel` (`kasir`, `qris_statis`, `tripay_qris`), `payment_reference`, `payment_qr_url`, `payment_expired_at`
    * `payment_status`: `ENUM('pending', 'lunas', 'batal')`
    * `paid_at`, `voided_by`, `void_reason`, `voided_at`, `notes`
16. **`transaction_details`**: Rincian item minuman dalam satu transaksi (`transaction_id`, `product_variation_id`, `product_name`, `variation_name`, `quantity`, `price_at_transaction`, `subtotal`, `notes` untuk catatan gratis seperti *Less Sugar, Less Ice*).
17. **`transaction_detail_toppings`**: Rincian topping yang menempel pada masing-masing baris minuman (`transaction_detail_id`, `topping_id`, `topping_name`, `quantity`, `price_at_transaction`, `subtotal`).
18. **`b2b_orders`**: Pesanan partai besar / catering (`business_id`, `outlet_id`, `customer_id`, `user_id`, `order_code` unik, `event_name`, `event_date`, `total_amount`, `dp_amount`, `paid_total`, `status`: `draft`/`dp_paid`/`in_progress`/`completed`/`cancelled`, `notes`).
19. **`b2b_order_items`**: Rincian minuman pada pesanan B2B (`b2b_order_id`, `product_variation_id`, `product_name`, `variation_name`, `quantity`, `unit_price`, `subtotal`, `notes`).

---

## 3. Ringkasan Relasi Utama (Foreign Key)
* `businesses` 1 → N `subscription_payments`, `outlets`, `users`, `categories`, `products`, `toppings`, `ingredients`, `customers`, `transactions`, `b2b_orders`
* `outlets` 1 → N `users`, `outlet_ingredient_stocks`, `stock_movements`, `cashier_shifts`, `transactions`, `b2b_orders`
* `categories` 1 → N `products` (`restrict`) → 1 → N `product_variations` (`cascade`)
* `product_variations` & `toppings` 1 → N `recipes` (`cascade`) ← N `ingredients`
* `cashier_shifts` 1 → N `transactions` (`nullOnDelete`)
* `transactions` 1 → N `transaction_details` (`cascade`) → 1 → N `transaction_detail_toppings` (`cascade`)
* `b2b_orders` 1 → N `b2b_order_items` (`cascade`) dan 1 → N `transactions` (`nullOnDelete`) untuk mencatat pembayaran DP & pelunasan.

---

## 4. Diagram ERD Awal (Fase Desain Pertama)
> *Catatan: Diagram visual di bawah adalah rancangan ERD fase awal (7 tabel MVP). Penyesuaian diagram visual untuk 19 tabel akan diperbarui terpisah.*

![ERD Database](Diagrams/rancangan-database-final.drawio.png)
