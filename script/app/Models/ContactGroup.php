<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactGroup extends Model
{
    protected $table = 'contact_groups';

    protected $fillable = [
        'club_id',
        'name',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'club_id' => 'integer',
        'is_active' => 'boolean',
        'created_by' => 'integer',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
