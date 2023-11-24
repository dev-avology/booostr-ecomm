<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Categorymeta;
class TenantCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      $categories=array (
        0 => 
        array (
          'id' => 1,
          'name' => 'Complete',
          'slug' => '#028a74',
          'type' => 'status',
          'category_id' => NULL,
          'featured' => 1,
          'menu_status' => 0,
          'status' => 1,
          'created_at' => '2021-11-18 14:29:40',
          'updated_at' => '2021-11-18 14:35:42',
        ),
        1 => 
        array (
          'id' => 2,
          'name' => 'Cancel',
          'slug' => '#dc3545',
          'type' => 'status',
          'category_id' => NULL,
          'featured' => 2,
          'menu_status' => 0,
          'status' => 1,
          'created_at' => '2021-11-18 14:30:00',
          'updated_at' => '2021-11-18 14:36:26',
        ),
        2 => 
        array (
          'id' => 3,
          'name' => 'Pending',
          'slug' => '#ffc107',
          'type' => 'status',
          'category_id' => NULL,
          'featured' => 3,
          'menu_status' => 0,
          'status' => 1,
          'created_at' => '2021-11-18 14:30:37',
          'updated_at' => '2021-11-18 14:33:34',
        ),
       
        3 => 
        array (
          'id' => 4,
          'name' => 'Physical Product',
          'slug' => 'physical_product',
          'type' => 'product_type',
          'category_id' => NULL,
          'featured' => 0,
          'menu_status' => 0,
          'status' => 1,
          'created_at' => '2021-12-16 08:40:57',
          'updated_at' => '2021-12-16 08:40:57',
        ),
        4 => 
        array (
          'id' => 5,
          'name' => 'Digital Product',
          'slug' => 'digital_product',
          'type' => 'product_type',
          'category_id' => NULL,
          'featured' => 0,
          'menu_status' => 0,
          'status' => 1,
          'created_at' => '2021-12-16 08:40:57',
          'updated_at' => '2021-12-16 08:40:57',
        ),
      );

    Category::insert($categories);

    $metas= array();
    Categorymeta::insert($metas);


  }
}
