<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'business_name', 'description', 'logo', 'cover_image',
        'phone', 'email', 'region', 'district', 'ward', 'street',
        'latitude', 'longitude', 'is_approved', 'is_open', 'rating', 'total_reviews',
    ];

    protected $casts = [
        'is_approved'   => 'boolean',
        'is_open'       => 'boolean',
        'rating'        => 'float',
        'total_reviews' => 'integer',
        'latitude'      => 'float',
        'longitude'     => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id', 'user_id');
    }
}
