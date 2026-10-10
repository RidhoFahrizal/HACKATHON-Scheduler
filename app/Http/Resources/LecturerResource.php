<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LecturerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'username' => $this->username,
            'email' => $this->email,
            'nip' => $this->nip ?? '',
            'code' => $this->code ?? '',
            'academic_title' => $this->academic_title ?? '',
            'department' => $this->department ?? '',
        ];
    }
}
