<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'lecturer';

    protected $fillable = [
        'username',
        'email',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'lecturerId');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'lecturerId');
    }
}
