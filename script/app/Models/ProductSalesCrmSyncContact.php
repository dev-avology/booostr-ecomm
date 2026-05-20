<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSalesCrmSyncContact extends Model
{
    protected $fillable = [
        'product_sales_crm_sync_id',
        'product_id',
        'source_type',
        'source_id',
        'email',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function syncConfig()
    {
        return $this->belongsTo(ProductSalesCrmSync::class, 'product_sales_crm_sync_id');
    }
}
