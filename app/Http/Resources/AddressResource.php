<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'label'          => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone'          => $this->phone,
            'region'         => $this->region,
            'district'       => $this->district,
            'ward'           => $this->ward,
            'street'         => $this->street,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'is_default'     => $this->is_default,
        ];
    }
}
