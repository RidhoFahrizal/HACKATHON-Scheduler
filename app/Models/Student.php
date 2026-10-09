<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'student';

    protected $fillable = [
        'username',
        'class',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'studentSubject', 'studentId', 'subjectId');
    }

    public function studentSubjects(): HasMany
    {
        return $this->hasMany(StudentSubject::class, 'studentId');
    }
}
