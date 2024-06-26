<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Term;
use Illuminate\Http\Request;
use Newsletter;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\App;
use App\Models\Order;

class WelcomeController extends Controller
{
    public function index()
    {
       // dd('sssssssssssssss');
         if(request()->get('sync') == 'orderdata'){
            tenancy()->runForMultiple(null, function ($tenant) {
                $this->syncContactData();
            });    
         }

        $info = get_option('theme',true);
        $services = Term::with('servicemeta')->where([
            ['type','service'],
            ['status', 1]
        ])->take(4)->get();
        $plans = Plan::where('status',1)->take(3)->get();
        $blogs = Term::with('preview','excerpt')->where([
            ['type','blog'],
            ['status',1]
        ])->latest()->take(3)->get();

        $demos = Term::with('meta')->where([
            ['type','theme_demo'],
            ['status',1]
        ])->latest()->take(3)->get();

        $seo=get_option('seo_home',true);

        JsonLdMulti::setTitle($seo->site_name ?? env('APP_NAME'));
        JsonLdMulti::setDescription($seo->matadescription ?? null);
        JsonLdMulti::addImage(asset('uploads/logo.png'));

        SEOMeta::setTitle($seo->site_name ?? env('APP_NAME'));
        SEOMeta::setDescription($seo->matadescription ?? null);
        SEOMeta::addKeyword($seo->tags ?? null);

        SEOTools::setTitle($seo->site_name ?? env('APP_NAME'));
        SEOTools::setDescription($seo->matadescription ?? null);
        SEOTools::setCanonical(url('/'));
        SEOTools::opengraph()->addProperty('keywords', $seo->matatag ?? null);
        SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
        SEOTools::twitter()->setTitle($seo->site_name ?? env('APP_NAME'));
        SEOTools::twitter()->setSite($seo->twitter_site_title ?? null);
        SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));

        return view('welcome',compact('info','services','plans','blogs','demos'));
    }

    public function syncContactData(){
        $orders=Order::with('user','ordermeta','orderitems','orderstatus')->withCount('orderitems');
        $orders=$orders->get();
        foreach($orders as $order){
            $ordermeta=json_decode($order->ordermeta->value ?? '');
            $club_info = tenant_club_info();
            
            if(!empty($ordermeta)){                
            $name = !empty($ordermeta->name) ? explode(' ',$ordermeta->name) : '' ;
        
            $contact_manager_data = array(
                'first_name' => $name[0]??'',
                'last_name' => $name[1]??'',
                'user_id' =>  !empty($ordermeta->wpuid) ? (int)$ordermeta->wpuid:0,
                'phone_number' => $ordermeta->phone??'',					
                'booster_name' => $name[0]??'',
                'country' =>   $ordermeta->billing->country??'',									
                'address_1' => $ordermeta->billing->address??'',
                'address_2' =>  '',
                'city' => $ordermeta->billing->city??'',
                'state' => $ordermeta->billing->state??'',
                'zip' =>  $ordermeta->billing->post_code??'',												
                'email' =>  $ordermeta->email??'',                   
                'booster_id' =>Tenant('club_id'),
                'booster_level_id' => 4,
                'customer_tag' => 'online store customer',
            );	 
            dump("======Order Metadata=======");
            dump($ordermeta);
           dump($contact_manager_data);
         }else{
            dump("======Order No Metadata=========");
         }

        }
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        \Config::set('newsletter.apiKey', env('MAILCHIMP_APIKEY'));
        \Config::set('newsletter.lists.subscribers.id', env('MAILCHIMP_LIST_ID'));

        Newsletter::subscribe($request->email);

       
        return response()->json('Subscribe Successfully');
        
    }

    public function demos()
    {
        $demos = Term::where([
            ['type','theme_demo'],
            ['status',1]
        ])->with('meta')->latest()->paginate(6);

        $seo=get_option('seo_gallery',true);

        JsonLdMulti::setTitle($seo->site_name ?? env('APP_NAME'));
        JsonLdMulti::setDescription($seo->matadescription ?? null);
        JsonLdMulti::addImage(asset('uploads/logo.png'));

        SEOMeta::setTitle($seo->site_name ?? env('APP_NAME'));
        SEOMeta::setDescription($seo->matadescription ?? null);
        SEOMeta::addKeyword($seo->tags ?? null);

        SEOTools::setTitle($seo->site_name ?? env('APP_NAME'));
        SEOTools::setDescription($seo->matadescription ?? null);
        SEOTools::setCanonical(url('/'));
        SEOTools::opengraph()->addProperty('keywords', $seo->matatag ?? null);
        SEOTools::opengraph()->addProperty('image', asset('uploads/logo.png'));
        SEOTools::twitter()->setTitle($seo->site_name ?? env('APP_NAME'));
        SEOTools::twitter()->setSite($seo->twitter_site_title ?? null);
        SEOTools::jsonLd()->addImage(asset('uploads/logo.png'));

        return view('demos',compact('demos'));

    }

    public function lang_switch(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);

        return response()->json('Successfully Changed');
    }

}
