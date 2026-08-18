<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickSaleDescriptor extends Model
{
    protected $fillable = [
        'name',
        'price',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'float',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(QuickSaleOrderItem::class, 'descriptor_id');
    }
}
