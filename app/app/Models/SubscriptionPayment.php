<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model riwayat tagihan aktivasi dan perpanjangan lisensi usaha.
 */
class SubscriptionPayment extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'reference',
        'merchant_ref',
        'plan_name',
        'duration_days',
        'amount',
        'payment_method',
        'payment_url',
        'qr_url',
        'status',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
