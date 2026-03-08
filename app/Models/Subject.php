<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    /**
     * Get all sub-subjects for this subject
     */
    public function subSubjects()
    {
        return $this->hasMany(SubSubject::class);
    }

    /**
     * Get all schedules for this subject
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Check if this subject has any sub-subjects
     */
    public function hasSubSubjects()
    {
        return $this->subSubjects()->exists();
    }
}
