<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubject extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
