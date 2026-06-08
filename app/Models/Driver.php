<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'driver_application_id',
        'status',
        'current_latitude',
        'current_longitude',
        'location_updated_at',
        'rating',
        'total_deliveries',
    ];

    protected function casts(): array
    {
        return [
            'current_latitude'    => 'float',
            'current_longitude'   => 'float',
            'location_updated_at' => 'datetime',
            'rating'              => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(DriverApplication::class, 'driver_application_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
