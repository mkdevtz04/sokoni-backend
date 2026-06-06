<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorEarning extends Model
{
    protected $fillable = [
        'vendor_id', 'order_id', 'gross_amount', 'commission', 'net_amount', 'status',
    ];

    protected $casts = [
        'gross_amount' => 'float',
        'commission'   => 'float',
        'net_amount'   => 'float',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
