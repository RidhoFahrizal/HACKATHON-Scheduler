<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'schedule';

    protected $fillable = [
        'day',
        'startSlot',
        'endSlot',
        'subjectId',
        'lecturerId',
        'roomId',
        'semesterType',
    ];

    protected $casts = [
        'day' => 'integer',
        'startSlot' => 'integer',
        'endSlot' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subjectId');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturerId');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'roomId');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'scheduleId');
    }

    public function engineRuns(): HasMany
    {
        return $this->hasMany(EngineRun::class, 'schedule_id');
    }
}
