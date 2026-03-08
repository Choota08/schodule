<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubject extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
    ];

    /**
     * Get the parent subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get all schedules using this sub-subject
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
