<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'teacher_code',
        'subject_id',
        'subject_name_pending',
        'date_of_birth',
        'profile_photo',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTING
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | AUTO LOAD RELATIONS
    |--------------------------------------------------------------------------
    */

    protected $with = [
        'user',
        'subject',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFLICT CHECK
    |--------------------------------------------------------------------------
    */

    public function hasConflict($day, $sessionId)
    {
        return $this->schedules()
            ->where('day', $day)
            ->where('session_id', $sessionId)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }

        return asset('images/default-avatar.png');
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth
            ? $this->date_of_birth->age
            : null;
    }
}
