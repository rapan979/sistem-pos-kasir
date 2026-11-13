<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model akun usaha UMKM berlisensi (tanpa free trial).
 */
class Business extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'phone',
        'address',
        'license_key',
        'plan_name',
        'license_status',
        'license_expires_at',
        'tripay_merchant_code',
        'tripay_api_key',
        'tripay_private_key',
        'tripay_is_sandbox',
    ];

    protected $hidden = [
        'tripay_api_key',
        'tripay_private_key',
    ];

    protected $casts = [
        'license_expires_at' => 'datetime',
        'tripay_is_sandbox' => 'boolean',
    ];

    /**
     * Memastikan lisensi usaha aktif dan tanggal masa berlakunya belum terlewati.
     */
    public function hasActiveLicense(): bool
    {
        return $this->license_status === 'active'
            && $this->license_expires_at !== null
            && $this->license_expires_at->isFuture();
    }

    public function subscriptionPayments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function toppings(): HasMany
    {
        return $this->hasMany(Topping::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function b2bOrders(): HasMany
    {
        return $this->hasMany(B2bOrder::class);
    }
}
