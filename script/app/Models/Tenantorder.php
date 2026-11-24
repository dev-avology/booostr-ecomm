<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenantorder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
		'order_id','tenant_id'
	];
}
