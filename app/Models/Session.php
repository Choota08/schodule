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

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get all schedules for this session
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }
}
