<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'capacity' => (int) $this->capacity,
            'type' => $this->type ?? 'teori',
            'floor' => (int) ($this->floor ?? 1),
            'is_active' => (bool) ($this->is_active ?? true),
            'building' => $this->building ? [
                'id' => $this->building->id,
                'code' => $this->building->code,
                'name' => $this->building->name,
            ] : null,
        ];
    }
}
