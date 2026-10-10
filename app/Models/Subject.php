<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'subject';

    protected $fillable = [
        'code',
        'name',
        'credits',
        'semester',
        'department',
        'lecturerId',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturerId');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'subjectId');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'studentSubject', 'subjectId', 'studentId');
    }
}
