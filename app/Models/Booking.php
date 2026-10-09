<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'booking';

    protected $fillable = [
        'scheduleId',
        'day',
        'startSlot',
        'endSlot',
        'roomId',
    ];

    protected $casts = [
        'day' => 'integer',
        'startSlot' => 'integer',
        'endSlot' => 'integer',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'scheduleId');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'roomId');
    }
}
