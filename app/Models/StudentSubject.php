<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentSubject extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'studentSubject';

    protected $fillable = [
        'studentId',
        'subjectId',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'studentId');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subjectId');
    }
}
