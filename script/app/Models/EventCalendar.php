<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCalendar extends Model
{
    use HasFactory;

    protected $table = 'event_calendars';

    protected $fillable = [
        'external_event_id',
        'title',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'status',
    ];
}
