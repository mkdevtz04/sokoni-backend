<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'business_name' => $this->business_name,
            'description'   => $this->description,
            'logo'          => $this->logo        ? 'storage/' . $this->logo        : null,
            'cover_image'   => $this->cover_image ? 'storage/' . $this->cover_image : null,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'region'        => $this->region,
            'district'      => $this->district,
            'ward'          => $this->ward,
            'is_approved'   => $this->is_approved,
            'is_open'       => $this->is_open,
            'rating'        => $this->rating,
            'total_reviews' => $this->total_reviews,
            'user_id'       => $this->user_id,
            'products_count'=> $this->whenCounted('products'),
        ];
    }
}
