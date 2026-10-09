<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'room';

    protected $fillable = [
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'roomId');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'roomId');
    }
}
