<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vendor_id', 'category_id', 'name', 'slug', 'description',
        'price', 'sale_price', 'unit', 'stock',
        'is_active', 'is_featured', 'rating', 'total_reviews', 'total_orders',
    ];

    protected $casts = [
        'price'         => 'float',
        'sale_price'    => 'float',
        'stock'         => 'integer',
        'is_active'     => 'boolean',
        'is_featured'   => 'boolean',
        'rating'        => 'float',
        'total_reviews' => 'integer',
        'total_orders'  => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class, 'vendor_id', 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images()->where('is_primary', true)->first()
            ?? $this->images()->first();
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }
}
