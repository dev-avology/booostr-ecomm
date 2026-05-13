<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AppSequires extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'value'];
    
      public function setValueAttribute($value)
    {
        $this->attributes['value'] = Crypt::encryptString($value);
    }

    // 🔓 Decrypt when getting
    public function getValueAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value; 
        }
    }
}
