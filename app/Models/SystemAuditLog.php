<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAuditLog extends Model
{
    use HasUuids;

    // Entries carry their own `timestamp` column; the table has no created_at or updated_at.
    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'level',
        'actor_id',
        'actor_name',
        'module',
        'action',
        'details',
        'ip_address',
        'status',
    ];

    protected $casts = ['timestamp' => 'datetime'];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
