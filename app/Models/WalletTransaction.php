<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'balance_after', 'description', 'reference'];

    protected $casts = ['amount' => 'float', 'balance_after' => 'float'];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
