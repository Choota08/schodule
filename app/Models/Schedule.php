<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'class_room_id',
        'teacher_id',
        'subject_id',
        'sub_subject_id',
        'day',
        'session_id',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subSubject()
    {
        return $this->belongsTo(SubSubject::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
