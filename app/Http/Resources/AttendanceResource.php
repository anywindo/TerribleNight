<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'shift_id' => $this->shift_id,
            'date' => $this->getRawOriginal('date') ?? ($this->date instanceof \Carbon\Carbon ? $this->date->format('Y-m-d') : substr((string)$this->date, 0, 10)),
            'status' => $this->status,
            'events' => AttendanceEventResource::collection($this->whenLoaded('events')),
        ];
    }
}
