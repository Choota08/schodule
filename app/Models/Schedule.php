<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Schedule extends Model
{
    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT (Sesuai urutan input admin)
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'class_room_id',
        'subject_id',
        'sub_subject_id',
        'teacher_id',
        'session_id',
        'day',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function subSubject()
    {
        return $this->belongsTo(SubSubject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (Digunakan di Controller jika perlu)
    |--------------------------------------------------------------------------
    */

    public function scopeByDay(Builder $query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeByTeacher(Builder $query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass(Builder $query, $classId)
    {
        return $query->where('class_room_id', $classId);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFLICT CHECK (Digunakan di Controller)
    |--------------------------------------------------------------------------
    */

    public static function hasClassConflict($classId, $day, $sessionId, $ignoreId = null)
    {
        $query = self::query()
            ->where('class_room_id', $classId)
            ->where('day', $day)
            ->where('session_id', $sessionId);

        if (!is_null($ignoreId)) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public static function hasTeacherConflict($teacherId, $day, $sessionId, $ignoreId = null)
    {
        $query = self::query()
            ->where('teacher_id', $teacherId)
            ->where('day', $day)
            ->where('session_id', $sessionId);

        if (!is_null($ignoreId)) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
