# 🗄️ RANCANGAN BASIS DATA BERSIH (DATABASE DESIGN)

Rancangan database sistem kasir ini disederhanakan murni untuk operasional kasir 1 gerai (*Single-Store POS*), menghapus ketergantungan lisensi dan multi-cabang yang tidak diperlukan.

---

## 1. Diagram Relasi Entitas (ERD Sederhana)

```
[categories] 1 ──── N [products] 1 ──── N [product_variations]
                                                  │
                                                  │ (pilihan ukuran)
                                                  ▼
[users] 1 ──── N [cashier_shifts] 1 ──── N [transactions]
                                                  │
                                                  ▼ 1:N
                                         [transaction_details] 1 ──── N [transaction_detail_toppings]
                                                  ▲
                                                  │
                                             [toppings]
```

---

## 2. Rincian Kamus 9 Tabel Inti

### 1. `users` (Pengguna Sistem Kasir)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `name` | VARCHAR(100) | Nama kasir / pemilik |
| `email` | VARCHAR(255) (UNIQUE) | Alamat email login |
| `password` | VARCHAR(255) | Hash kata sandi |
| `role` | ENUM('owner', 'kasir') | Peran pengguna |
| `is_active` | BOOLEAN | Status akun aktif (default true) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 2. `categories` (Kategori Menu Es Teh)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `name` | VARCHAR(50) | Nama kategori (Fresh Tea, Milk Tea, Fruit Tea) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 3. `products` (Daftar Minuman Es Teh)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `category_id` | BIGINT UNSIGNED (FK) | Relasi ke `categories.id` (`onDelete restrict`) |
| `name` | VARCHAR(100) | Nama produk (Es Teh Original, Thai Tea, dll) |
| `sku_barcode` | VARCHAR(50) (NULLABLE) | Kode SKU / Barcode untuk scanner |
| `image` | VARCHAR(255) (NULLABLE) | Path gambar produk |
| `is_available` | BOOLEAN | Ketersediaan menu (default true) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 4. `product_variations` (Ukuran Cup & Harga Jual)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `product_id` | BIGINT UNSIGNED (FK) | Relasi ke `products.id` (`onDelete cascade`) |
| `variation_name` | VARCHAR(50) | Nama ukuran (Regular, Large, Jumbo) |
| `barcode` | VARCHAR(50) (NULLABLE) | Barcode spesifik ukuran cup |
| `price` | INTEGER UNSIGNED | Harga jual ukuran ini (dalam Rupiah) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 5. `toppings` (Topping Berbayar)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `name` | VARCHAR(50) | Nama topping (Boba, Cincau, Cheese Cream) |
| `price` | INTEGER UNSIGNED | Harga ekstra topping (dalam Rupiah) |
| `is_available` | BOOLEAN | Ketersediaan topping (default true) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 6. `cashier_shifts` (Shift Laci Kasir)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `user_id` | BIGINT UNSIGNED (FK) | Kasir yang bertugas (`users.id`) |
| `opened_at` | DATETIME | Waktu buka shift |
| `closed_at` | DATETIME (NULLABLE) | Waktu tutup shift |
| `opening_cash` | INTEGER UNSIGNED | Modal awal laci (Rp) |
| `expected_cash` | INTEGER UNSIGNED (NULLABLE) | Ekspektasi uang kas sistem (Rp) |
| `actual_cash` | INTEGER UNSIGNED (NULLABLE) | Uang fisik aktual di laci (Rp) |
| `difference` | INTEGER (NULLABLE) | Selisih uang kas (aktual - ekspektasi) |
| `status` | ENUM('open', 'closed') | Status shift kasir |
| `notes` | VARCHAR(255) (NULLABLE) | Catatan serah terima kasir |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 7. `transactions` (Kepala Nota Penjualan)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `cashier_shift_id` | BIGINT UNSIGNED (FK) | Relasi ke `cashier_shifts.id` |
| `user_id` | BIGINT UNSIGNED (FK) | Kasir pencatat (`users.id`) |
| `invoice_number` | VARCHAR(30) (UNIQUE) | Nomor nota unik (`INV-YYYYMMDD-0001`) |
| `total_amount` | INTEGER UNSIGNED | Total belanja seluruh pesanan |
| `paid_amount` | INTEGER UNSIGNED | Nominal uang dibayarkan |
| `change_amount` | INTEGER UNSIGNED | Nominal uang kembalian |
| `payment_method` | ENUM('cash', 'qris') | Metode bayar |
| `payment_status` | ENUM('paid', 'void') | Status pembayaran |
| `paid_at` | DATETIME | Waktu pembayaran lunas |
| `voided_by` | BIGINT UNSIGNED (FK, NULLABLE) | Petugas yang membatalkan nota |
| `void_reason` | VARCHAR(255) (NULLABLE) | Alasan pembatalan transaksi |
| `voided_at` | DATETIME (NULLABLE) | Waktu pembatalan transaksi |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 8. `transaction_details` (Rincian Minuman Dibeli)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `transaction_id` | BIGINT UNSIGNED (FK) | Relasi ke `transactions.id` (`cascade`) |
| `product_variation_id` | BIGINT UNSIGNED (FK) | Relasi ke `product_variations.id` |
| `product_name` | VARCHAR(100) | Nama produk saat transaksi (kunci historis) |
| `variation_name` | VARCHAR(50) | Nama ukuran saat transaksi (kunci historis) |
| `quantity` | INTEGER UNSIGNED | Jumlah cup yang dibeli |
| `price_at_transaction` | INTEGER UNSIGNED | Harga variasi saat transaksi |
| `subtotal` | INTEGER UNSIGNED | Subtotal variasi minuman (`price * qty`) |
| `notes` | VARCHAR(255) (NULLABLE) | Catatan takaran gula dan level es |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

### 9. `transaction_detail_toppings` (Topping per Minuman)
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED (PK) | Auto increment |
| `transaction_detail_id` | BIGINT UNSIGNED (FK) | Relasi ke `transaction_details.id` (`cascade`) |
| `topping_id` | BIGINT UNSIGNED (FK) | Relasi ke `toppings.id` |
| `topping_name` | VARCHAR(50) | Nama topping saat transaksi |
| `quantity` | INTEGER UNSIGNED | Kuantitas topping |
| `price_at_transaction` | INTEGER UNSIGNED | Harga satuan topping saat transaksi |
| `subtotal` | INTEGER UNSIGNED | Subtotal topping (`price * qty`) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & modifikasi |

---

## 3. Data Seeder Awal (Menu Simulasi Es Teh)
* **Kategori:**
  * `Fresh Tea`: Es Teh Original (Reg: 5.000, Lrg: 7.000), Es Teh Melati (Reg: 5.000, Lrg: 7.000), Es Teh Lemon (Reg: 6.000, Lrg: 8.000).
  * `Milk Tea`: Milk Tea Original (Reg: 8.000, Lrg: 10.000), Thai Tea (Reg: 9.000, Lrg: 12.000), Es Teh Susu Gula Aren (Reg: 10.000, Lrg: 13.000).
  * `Fruit Tea`: Es Teh Apel (Reg: 7.000, Lrg: 9.000), Es Teh Stroberi (Reg: 8.000, Lrg: 10.000).
* **Topping:**
  * Boba (Rp 3.000), Cincau (Rp 2.000), Cheese Cream (Rp 4.000).
