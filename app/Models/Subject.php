<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name'];

    public function subSubjects()
    {
        return $this->hasMany(SubSubject::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function hasSubSubjects()
    {
        return $this->subSubjects()->exists();
    }
}
