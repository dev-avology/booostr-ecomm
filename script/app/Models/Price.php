<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class Price extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $fillable = [
        'price',
        'term_id',
        'productoption_id',
        'category_id',
        'old_price',
        'qty',
        'sku',
        'weight',
        'stock_manage',
        'stock_status',
        'tax',
       
        
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->select('id','name');
    }

    public function varitions(){
        return $this->belongsToMany(Category::class,'variationproductoptions')->select('id','name')->withPivot('productoption_id');
    }

    public function varitionOptions(){
        return $this->belongsToMany(Productoption::class,'variationproductoptions')->with('category');
    }

}
