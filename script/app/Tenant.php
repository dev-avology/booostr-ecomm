<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $fillable = [
        'id', 'order_id','user_id','will_expire','data','status','auto_renew','uid','long'
    ];
   

    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class,'user_id');
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class,'order_id');
    }

    public function orderwithplan()
    {
        return $this->belongsTo(\App\Models\Order::class,'order_id')->with('plan');
    }

    public function domain()
    {
        return $this->hasOne(\App\Models\Domain::class);
    }

    public function domains()
    {
        return $this->hasMany(\App\Models\Domain::class);
    }
    public function active_domains()
    {
        return $this->hasMany(\App\Models\Domain::class)->where('status',1);
    }

    public function subdomain()
    {
        return $this->hasOne(\App\Models\Domain::class)->where('type',2);
    }

    public function customdomain()
    {
        return $this->hasOne(\App\Models\Domain::class)->where('type',3);
    }

    public function tenantorderlog()
    {
        return $this->hasMany(\App\Models\Tenantorder::class);
    }

    public function tenantmeta()
    {
        return $this->hasOne(\App\Models\Tenantmeta::class,'tenant_id');
    }
}
