<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductForm extends Model
{
    use HasFactory;

    protected $table = "product_form";
    protected $fillable = [
      'cart_id',
      'form_data',
      'form_id',
      'product_id'
  ];


}
