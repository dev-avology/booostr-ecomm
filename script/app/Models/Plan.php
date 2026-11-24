<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
   use HasFactory;
   protected $table="plans";

   //if you want to add more plan data just add new value in array
   public $plandata=[
      'sub_domain'=>[
         'type' => 'option',
         'value'=>['Yes'=>'on','No'=>'off']
      ],
      'custom_domain'=>[
         'type' => 'option',
         'value'=>['Yes'=>'on','No'=>'off']
      ],
     
      'barcode'=>[
         'type' => 'option',
         'value'=>['Yes'=>'on','No'=>'off']
      ],
      'qr_code'=>[
         'type' => 'option',
         'value'=>['Yes'=>'on','No'=>'off']
      ],
      
      'customer_modules'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
      'pos'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
      'push_notification'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
      'image_optimization'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
      'custom_css_js'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
      'pwa'=>[
          'type' => 'option',
          'value'=>['Yes'=>'on','No'=>'off']
      ],
       'storage_limit'=>[
         'type' => 'number',
         'value'=> 0
      ],
      'post_limit'=>[
         'type' => 'number',
         'value'=> 0
      ],
      'staff_limit'=>[
         'type' => 'number',
         'value'=> 0
      ],
      
    ];

    public function active_users()
    {
    	return $this->hasMany(\App\Models\Order::class)->where('status',1);
    }
    public function orders()
    {
    	return $this->hasMany(\App\Models\Order::class);
    }

   
}
