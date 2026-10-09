<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Krs extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'krs';

    protected $fillable = [
        'studentSubjectId',
    ];

    public function studentSubject(): BelongsTo
    {
        return $this->belongsTo(StudentSubject::class, 'studentSubjectId');
    }
}
