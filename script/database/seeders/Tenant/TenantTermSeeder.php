<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Term;
class TenantTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terms=array(
        array(
            "id" => 1,
            "full_id" => "0000001",
            "title" => "Terms and conditions",
            "slug" => "terms-and-conditions",
            "type" => "page",
            "is_variation" => 0,
            "status" => 1,
            "featured" => null,
            "created_at" => "2021-12-25 13:48:51",
            "updated_at" => "2022-01-09 14:14:18",
            "rating" => null
        ),
        array(
            "id" => 2,
            "full_id" => "0000002",
            "title" => "Privacy Policy",
            "slug" => "privacy-policy",
            "type" => "page",
            "is_variation" => 0,
            "status" => 1,
            "featured" => null,
            "created_at" => "2021-12-25 13:48:51",
            "updated_at" => "2022-01-09 14:14:18",
            "rating" => null
        ),
        array(
            "id" => 3,
            "full_id" => "000003",
            "title" => "Return Policy",
            "slug" => "return-policy",
            "type" => "page",
            "is_variation" => 0,
            "status" => 1,
            "featured" => null,
            "created_at" => "2021-12-25 13:48:51",
            "updated_at" => "2022-01-09 14:14:18",
            "rating" => null
        )
    );
        
        Term::insert($terms);
    }
}
