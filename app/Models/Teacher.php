<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_code',
        'subject_id',
        'subject_name_pending',
        'date_of_birth',
        'profile_photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['user', 'subject'];

    /**
     * Get the user associated with this teacher
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject this teacher teaches
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get all schedules for this teacher
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Scope to filter teachers by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Check if teacher has a schedule conflict
     */
    public function hasConflict($day, $sessionId)
    {
        return $this->schedules()
            ->where('day', $day)
            ->where('session_id', $sessionId)
            ->exists();
    }

    /**
     * Get profile photo URL from storage
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }

        return asset('images/default-avatar.png');
    }

    /**
     * Calculate age from date of birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }
}
