<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSalesCrmSync extends Model
{
    protected $fillable = [
        'product_id',
        'sync_type',
        'sync_mode',
        'continuous_sync_enabled',
        'is_ticket_product',
        'contact_tags',
        'crm_list_name',
        'filter_state',
        'sync_status',
        'last_synced_at',
        'last_processed_at',
        'last_processed_record_id',
        'total_synced_contacts',
        'created_by',
    ];

    protected $casts = [
        'continuous_sync_enabled' => 'boolean',
        'is_ticket_product' => 'boolean',
        'filter_state' => 'array',
        'last_synced_at' => 'datetime',
        'last_processed_at' => 'datetime',
    ];

    public function syncedContacts()
    {
        return $this->hasMany(ProductSalesCrmSyncContact::class, 'product_sales_crm_sync_id');
    }

    public function isContinuousActive(): bool
    {
        return $this->continuous_sync_enabled
            && $this->sync_type === 'continuous'
            && in_array($this->sync_status, ['active', 'syncing'], true);
    }
}
