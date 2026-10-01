<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemWriteInAmount extends Model
{
    protected $table = 'cart_item_write_in_amounts';

    protected $fillable = [
        'cart_id',
        'rowid',
        'product_id',
        'write_in_amount',
    ];
}
