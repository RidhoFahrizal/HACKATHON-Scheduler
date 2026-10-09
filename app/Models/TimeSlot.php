<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'slot_index',
        'start_time',
        'end_time',
        'is_blocked',
        'block_reason',
    ];

    protected $casts = [
        'slot_index' => 'integer',
        'is_blocked' => 'boolean',
    ];
}
