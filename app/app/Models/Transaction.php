<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model untuk tabel transactions.
// Satu baris = satu struk / satu kali pembayaran di kasir.
// Total, dibayar, dan kembalian disimpan sebagai angka (rupiah),
// bukan dihitung ulang dari harga produk saat ini.
class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'business_id',
        'outlet_id',
        'cashier_shift_id',
        'b2b_order_id',
        'user_id',
        'customer_id',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_channel',
        'payment_reference',
        'payment_qr_url',
        'payment_expired_at',
        'payment_status',
        'paid_at',
        'voided_by',
        'void_reason',
        'voided_at',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
        'change_amount' => 'integer',
        'payment_expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    /**
     * Usaha pemilik transaksi ini.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Cabang/gerai tempat transaksi berlangsung.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Shift kasir yang menaungi transaksi ini.
     */
    public function cashierShift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class);
    }

    /**
     * Pesanan B2B terkait jika transaksi merupakan DP/pelunasan pesanan besar.
     */
    public function b2bOrder(): BelongsTo
    {
        return $this->belongsTo(B2bOrder::class);
    }

    // Relasi: transaksi ini dibuat oleh satu user (kasir/owner).
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: transaksi ini atas nama satu pelanggan, atau tanpa pelanggan.
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Pengguna (Owner) yang membatalkan/void transaksi ini jika dibatalkan.
     */
    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    // Relasi: satu transaksi punya banyak detail barang.
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Cek apakah pembayaran transaksi ini sudah benar-benar lunas.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'lunas';
    }
}
