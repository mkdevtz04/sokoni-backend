<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'user_id', 'vendor_id', 'status',
        'subtotal', 'delivery_fee', 'total',
        'payment_status', 'payment_method',
        'delivery_address_id', 'delivery_note',
        'confirmed_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal'      => 'float',
        'delivery_fee'  => 'float',
        'total'         => 'float',
        'confirmed_at'  => 'datetime',
        'delivered_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class, 'vendor_id', 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'delivery_address_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public static function generateNumber(): string
    {
        return 'SKN-' . strtoupper(substr(uniqid(), -8));
    }
}
