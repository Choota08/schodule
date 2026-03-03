<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'note'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
}
