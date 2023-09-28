<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Getway;
use Illuminate\Support\Facades\Http;

class TenantGetwaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $club_id = '36615';//tenant('club_id');
        $response = Http::withOptions([
            'verify' => false,
        ])->get(env('WP_API_URL').'/get_stripe_account_number', [
            'club_id' => $club_id
        ]);

        if ($response->successful()) {
            $stripe_info = $response->json();
        } else {
            $stripe_info = [];
        }

        $test_publishable_key = $stripe_info['test_publishable_key']??'ddddd';
        $test_secret_key = $stripe_info['test_secret_key']??'aaaaa';
        $publishable_key = $stripe_info['publishable_key']??'ddddd';
        $secret_key = $stripe_info['secret_key']??'8888';
        $stripe_account_id = $stripe_info['stripe_account_id']??'kkkkkkk';

        $getways = array(
            array('id' => '2','name' => 'stripe','logo' => 'uploads/21/04/1698367948712217.png','rate' => '10','charge' => '2','namespace' => 'App\\Lib\\Stripe','currency_name' => 'usd','is_auto' => '1','image_accept' => '0','test_mode' => '1','status' => '1','phone_required' => '0','data' => '{"test_publishable_key":"'.$test_publishable_key.'","test_secret_key":"'.$test_secret_key.'","publishable_key":"'.$publishable_key.'","secret_key":"'.$secret_key.'","stripe_account_id":"'.$stripe_account_id.'"}','created_at' => '2021-04-15 02:44:46','updated_at' => '2021-04-29 09:51:32')
        );               
        Getway::insert($getways);    }
}
