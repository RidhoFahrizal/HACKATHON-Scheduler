<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code ?? '',
            'name' => $this->name,
            'credits' => (int) $this->credits,
            'sks' => (int) $this->credits,
            'semester' => $this->semester !== null ? (int) $this->semester : null,
            'department' => $this->department ?? '',
            'lecturerId' => $this->lecturerId,
        ];
    }
}
