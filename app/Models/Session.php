<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'note',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
