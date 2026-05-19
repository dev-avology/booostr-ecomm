<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ticket_uuid',
        'order_id',
        'order_item_id',
        'term_id',
        'attendee_name',
        'attendee_email',
        'attendee_phone',
        'event_name',
        'event_start_at',
        'event_end_at',
        'event_date',
        'event_time',
        'event_location',
        'status',
        'used_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(Orderitem::class, 'order_item_id');
    }
}
