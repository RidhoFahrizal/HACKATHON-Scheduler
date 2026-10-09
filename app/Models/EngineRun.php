<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineRun extends Model
{
    protected $table = 'engine_runs';

    protected $fillable = [
        'schedule_id',
        'scope',
        'target_date',
        'target_week',
        'success',
        'options',
        'thinking_log',
    ];

    protected $casts = [
        'target_date' => 'date',
        'target_week' => 'integer',
        'success' => 'boolean',
        'options' => 'array',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
