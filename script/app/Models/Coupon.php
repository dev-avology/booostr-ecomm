<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'value',
        'coupon_code_name',
        'is_percentage',
        'min_amount',
        'min_amount_option',
        'start_from',
        'will_expire',
        'date_checkbox',
        'coupon_for_name',
        'coupon_for_id', 
    ];
}
