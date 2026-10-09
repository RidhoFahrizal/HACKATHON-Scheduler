<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasUuids;

    protected $fillable = ['code', 'name', 'floors_count'];

    protected $casts = ['floors_count' => 'integer'];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'building_id');
    }
}
