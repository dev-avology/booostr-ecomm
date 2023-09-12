<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Category;
use App\Models\Location;
use App\Models\Getway;
use Cart;
use Session;
use Auth;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Mail;
use App\Mail\ContactMail;


class CheckoutController extends Controller
{
    
    public function cart()
    {
        $tax=optionfromcache('tax');
        if ($tax == null) {
            $tax=0;
        }
        Cart::setGlobalTax($tax);

        $home_data=optionfromcache('cart_page');

        $seo=$home_data->seo ?? '';
        SEOMeta::setTitle($seo->site_title ?? '');
        SEOMeta::setDescription($seo->description ?? '');
       

        OpenGraph::setDescription($seo->description ?? '');
        OpenGraph::setTitle($seo->site_title ?? '');

        OpenGraph::addProperty('keywords', $seo->tags ?? '');

        TwitterCard::setTitle($seo->site_title ?? '');
        TwitterCard::setSite($seo->twitter_title ?? '');

        JsonLd::setTitle($seo->site_title ?? '');
        JsonLd::setDescription($seo->description ?? '');
        JsonLd::addImage($seo->meta_image ?? '');

        SEOTools::setTitle($seo->site_title ?? '');
        SEOTools::setDescription($seo->description ?? '');
        SEOTools::opengraph()->setUrl(url('/'));
      
        
        SEOTools::twitter()->setSite($seo->twitter_title ?? '');
        SEOTools::jsonLd()->addImage($seo->meta_image ?? '');
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? '');

        $page_data=$home_data->meta ?? '';

        return view(baseview('cart'),compact('page_data'));
    }


    public function redirect_to_checkout(Request $request,$cartid,$redirect_url='/')
    {
        if (empty($cartid)) {
            return redirect()->to($redirect_url)->with(['type' => 'error','message' => 'Opps something went wrong']);
        }
        $domain=tenant('domain');
        return redirect()->to("//".$domain->domain.'/direct_checkout/'.$cartid.'/'.$redirect_url);
        
    }

    public function direct_checkout(Request $request,$cartid,$redirect_url='/')
    {
        
        Cart::instance($cartid);
        //load cart in session
        Cart::checkout_restore($cartid);
        if(Cart::content()->isEmpty()){
            return redirect()->to($redirect_url)->with(['type' => 'error','message' => 'Opps Your cart is empty']);
        }
        
        Session::put('redirect_url',$redirect_url);

        $tax=optionfromcache('tax');
        if ($tax == null) {
            $tax=0;
        }
        Cart::setGlobalTax($tax);
        $order_settings=get_option('order_settings',true);
        if ($order_settings->shipping_amount_type != 'distance') {
            $locations=Location::where([['status',1]])->whereHas('shippings')->with('shippings')->get();
        }
        else{
            $locations=[];
        }
        
        $getways=Getway::where('status','!=',0)->get();

        $order_method=$request->t ?? 'delivery';
        
        $invoice_data=optionfromcache('invoice_data');
        
        $meta= !Auth::check() ? [] : json_decode(Auth::user()->meta ?? '');

        $home_data=optionfromcache('checkout_page');

        $seo=$home_data->seo ?? '';
        SEOMeta::setTitle($seo->site_title ?? '');
        SEOMeta::setDescription($seo->description ?? '');
       

        OpenGraph::setDescription($seo->description ?? '');
        OpenGraph::setTitle($seo->site_title ?? '');

        OpenGraph::addProperty('keywords', $seo->tags ?? '');

        TwitterCard::setTitle($seo->site_title ?? '');
        TwitterCard::setSite($seo->twitter_title ?? '');

        JsonLd::setTitle($seo->site_title ?? '');
        JsonLd::setDescription($seo->description ?? '');
        JsonLd::addImage($seo->meta_image ?? '');

        SEOTools::setTitle($seo->site_title ?? '');
        SEOTools::setDescription($seo->description ?? '');
        SEOTools::opengraph()->setUrl(url('/'));
    
        
        SEOTools::twitter()->setSite($seo->twitter_title ?? '');
        SEOTools::jsonLd()->addImage($seo->meta_image ?? '');
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? '');

        $page_data=$home_data->meta ?? '';

        $pickup_order=$order_settings->pickup_order ?? 'off';
        $pre_order=$order_settings->pre_order ?? 'off';
        $source_code=$order_settings->source_code ?? 'on';
        return view('store.checkout.checkout',compact('locations','getways','request','order_method','order_settings','invoice_data','meta','page_data','pickup_order','pre_order','source_code'));
    }

   

    public function thanks()
    {
        abort_if(!Session::has('invoice_no'),404);
        $orderno=Session::get('invoice_no');
        SEOMeta::setTitle($orderno.' - Thanks');
        return view(baseview('thanks'),compact('orderno'));
    }

    

}
