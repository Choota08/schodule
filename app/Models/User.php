<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'kode_user',
        'name',
        'password',
        'role',
        'profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password' => 'hashed', // 🔥 otomatis hash jika set password
    ];

    protected $appends = [
        'profile_photo_url'
    ];

    // 🔥 Login pakai kode_user
    public function getAuthIdentifierName()
    {
        return 'kode_user';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->hasOne(\App\Models\Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(\App\Models\Teacher::class);
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
}
