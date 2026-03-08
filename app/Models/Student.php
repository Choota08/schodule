<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
        'age',
        'registered_at',
    ];

    /**
     * Get the user associated with this student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all classrooms this student is enrolled in
     */
    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student')
            ->withTimestamps();
    }

    /**
     * Get profile photo URL from related user
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->user?->profile_photo_url;
    }

    /**
     * Calculate age from date of birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get registration date (alias for created_at)
     */
    public function getRegisteredAtAttribute()
    {
        return $this->created_at;
    }
}
