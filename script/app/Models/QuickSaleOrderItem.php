<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickSaleOrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'descriptor_id',
        'descriptor_name',
        'title',
        'unit_amount',
        'qty',
        'line_subtotal',
        'tax_amount',
        'line_total',
        'order_invoice_no',
        'order_placed_at',
        'payment_method',
        'order_from',
        'payment_status',
        'wpuid',
        'meta',
    ];

    protected $casts = [
        'unit_amount' => 'float',
        'qty' => 'integer',
        'line_subtotal' => 'float',
        'tax_amount' => 'float',
        'line_total' => 'float',
        'order_from' => 'integer',
        'payment_status' => 'integer',
        'wpuid' => 'integer',
        'order_placed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function descriptor()
    {
        return $this->belongsTo(QuickSaleDescriptor::class, 'descriptor_id');
    }
}
