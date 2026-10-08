<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceEventResource extends JsonResource
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
            'event_type' => $this->event_type,
            'timestamp' => $this->timestamp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'selfie_path' => $this->selfie_path,
        ];
    }
}
