<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
