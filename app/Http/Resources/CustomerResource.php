<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
            'rating' => (float) $this->rating,
            'review_count' => (int) $this->review_count,
            'category' => $this->businessType->name,
            'marker' => asset('storage/markers/' . $this->businessType->marker_icon),
            'photos' => CustomerPhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
