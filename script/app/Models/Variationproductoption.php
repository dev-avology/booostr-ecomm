<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class Variationproductoption extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'price_id',
        'productoption_id',
        'category_id',
    ];

}
