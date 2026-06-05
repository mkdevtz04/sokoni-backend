<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected $casts = ['balance' => 'float'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    public function credit(float $amount, string $description, ?string $reference = null): WalletTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type'          => 'credit',
            'amount'        => $amount,
            'balance_after' => $this->fresh()->balance,
            'description'   => $description,
            'reference'     => $reference,
        ]);
    }

    public function debit(float $amount, string $description, ?string $reference = null): WalletTransaction
    {
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type'          => 'debit',
            'amount'        => $amount,
            'balance_after' => $this->fresh()->balance,
            'description'   => $description,
            'reference'     => $reference,
        ]);
    }
}
