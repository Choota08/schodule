<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Schedule extends Model
{
    protected $fillable = [
        'class_room_id',
        'subject_id',
        'sub_subject_id',
        'teacher_id',
        'session_id',
        'day',
    ];

    /**
     * Get the classroom for this schedule
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * Get the subject for this schedule
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the sub-subject for this schedule
     */
    public function subSubject()
    {
        return $this->belongsTo(SubSubject::class);
    }

    /**
     * Get the teacher for this schedule
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the session for this schedule
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Scope to filter schedules by day
     */
    public function scopeByDay(Builder $query, $day)
    {
        return $query->where('day', $day);
    }

    /**
     * Scope to filter schedules by teacher
     */
    public function scopeByTeacher(Builder $query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to filter schedules by classroom
     */
    public function scopeByClass(Builder $query, $classId)
    {
        return $query->where('class_room_id', $classId);
    }

    /**
     * Check if a classroom has a schedule conflict
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

    /**
     * Check if a teacher has a schedule conflict
     */
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
