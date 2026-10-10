<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'username' => $this->username,
            'class' => $this->class,
            'nrp' => $this->nrp ?? '',
            'cohort_year' => $this->cohort_year !== null ? (int) $this->cohort_year : null,
            'department' => $this->department ?? '',
            'email' => $this->email ?? '',
        ];
    }
}
