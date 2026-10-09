<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RescheduleRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'request_code',
        'schedule_id',
        'requester_id',
        'target_date',
        'target_day',
        'target_start_time',
        'target_end_time',
        'target_room_id',
        'duration_type',
        'reason',
        'urgency',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'is_active',
    ];

    protected $casts = [
        'target_date' => 'date',
        'reviewed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function targetRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'target_room_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
