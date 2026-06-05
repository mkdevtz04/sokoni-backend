<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->full_name,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'role'              => $this->role,
            'status'            => $this->status,
            'profile_picture'   => $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : null,
            'profile_completed' => $this->profile_completed,
            'phone_verified'    => $this->phone_verified_at !== null,
            'email_verified'    => $this->email_verified_at !== null,
            'created_at'        => $this->created_at,
        ];
    }
}
