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
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
        'age',
        'registered_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Student belongs to a User (for authentication)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Student can join multiple classes (for tutoring system)
    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student')
                    ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Get profile photo from related user
    public function getProfilePhotoUrlAttribute()
    {
        return $this->user?->profile_photo_url;
    }

    // Calculate age automatically
    public function getAgeAttribute()
    {
        return $this->date_of_birth
            ? $this->date_of_birth->age
            : null;
    }

    // Alias for registration date
    public function getRegisteredAtAttribute()
    {
        return $this->created_at;
    }
}
