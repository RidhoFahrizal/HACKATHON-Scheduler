<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'room';

    protected $fillable = [
        'building_id',
        'code',
        'name',
        'capacity',
        'type',
        'floor',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'floor' => 'integer',
        'is_active' => 'boolean',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'roomId');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'roomId');
    }
}
