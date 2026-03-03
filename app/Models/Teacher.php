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
    | AUTO LOAD RELATIONS (Avoid N+1)
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

    // Relasi ke akun login
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Guru hanya 1 mapel
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Relasi ke jadwal
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES (Filtering API)
    |--------------------------------------------------------------------------
    */

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFLICT HELPER (Core Scheduling System)
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
