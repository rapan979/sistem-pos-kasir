<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model pesanan partai besar (B2B / catering) dengan dukungan DP dan pelunasan.
 */
class B2bOrder extends Model
{
    protected $fillable = [
        'business_id',
        'outlet_id',
        'customer_id',
        'user_id',
        'order_code',
        'event_name',
        'event_date',
        'total_amount',
        'dp_amount',
        'paid_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_amount' => 'integer',
        'dp_amount' => 'integer',
        'paid_total' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(B2bOrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
