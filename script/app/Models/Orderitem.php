<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Term;
use App\Models\Order;
use App\Models\EventTicket;

class Orderitem extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'term_id',
        'info',
        'qty',
        'amount',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class)->with('media');
    }


    public function termwithpreview()
    {
        return $this->belongsTo(Term::class,'term_id')->with('preview');
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    public function eventTicket()
    {
        return $this->hasOne(EventTicket::class, 'order_item_id', 'id');
    }
    
}
