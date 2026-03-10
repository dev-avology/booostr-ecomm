<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Price;
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
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Option;
use DB;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\ProductForm;
use App\Models\Ordermeta;
use App\Models\Orderstock;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;

class CheckoutController extends Controller
{

    public function cart()
    {
        // $tax=optionfromcache('tax');
        // if ($tax == null) {
        //     $tax=0;
        // }

        // Cart::setGlobalTax($tax);

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
            return redirect()->to($redirect_url)->with(['type' => 'error','message' => 'Oops something went wrong']);
        }
        $domain=tenant('domain');
        if($request->has('guest')){
            $customer=[
                "guest"=>($request->guest??"")
            ];
        }else{
            $customer=[
                "name"=>($request->name??""),
                "email"=>($request->email),
                "phone"=>($request->phone??""),
                "address"=>($request->address??""),
                "city"=>($request->city??""),
                "state"=>($request->state??""),
                "country"=>($request->country??""),
                "zip"=>($request->zip??""),
                "wpuid"=>($request->wpuid??"")
            ];
        }

        return redirect()->to("//".$domain->domain.'/direct_checkout/'.$cartid.'/'.$redirect_url.'/?'.http_build_query($customer));

    }


    public function redirect_to_checkout_form(Request $request,$cartid,$redirect_url='/')
    {
        if (empty($cartid)) {
           // return redirect()->to($redirect_url)->with(['type' => 'error','message' => 'Oops something went wrong']);
            return response()->json(['type' => 'error',"status" => true, "message" => "Oops something went wrong", "url" => $redirect_url]);
        }
        $domain=tenant('domain');
        if($request->has('guest')){
            $customer=[
                "guest"=>($request->guest??"")
            ];
        }else{
            $customer=[
                "name"=>($request->name??""),
                "email"=>($request->email),
                "phone"=>($request->phone??""),
                "address"=>($request->address??""),
                "city"=>($request->city??""),
                "state"=>($request->state??""),
                "country"=>($request->country??""),
                "zip"=>($request->zip??""),
                "wpuid"=>($request->wpuid??"")
            ];
        }

       // $url = "https://".$domain->domain.'/direct_checkout_form/'.$cartid.'/'.$redirect_url.'/?'.http_build_query($customer);
        $url = "https://".$domain->domain.'/direct/checkout_form/'.$cartid.'/'.$redirect_url.'/?'.http_build_query($customer);

        return response()->json(["status" => true, "message" => "checkout url", "url" => $url]);

        //return redirect()->to();
    }


    public function direct_checkout_form(Request $request,$cartid='',$redirect_url='/'){
        Session::flush();

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        //  if ($validator->fails()) {
        //     return redirect()->away($redirect_url.'/?type=error&message='.$validator->errors()->first());
        // }

        $customer=[
            "name"=>($request->name??""),
            "email"=>($request->email),
            "phone"=>($request->phone??""),
            "address"=>($request->address??""),
            "city"=>($request->city??""),
            "state"=>($request->state??""),
            "country"=>($request->country??""),
            "zip"=>($request->zip??""),
            "wpuid"=>($request->wpuid??"")
        ];

        if($request->has('guest')){
            $customer["guest"]=($request->guest??"");
        }
         
        

        Session::put('customer_data',$customer);

       return redirect()->route('direct.checkout_form',['cartid'=>$cartid,'redirect_url'=>$redirect_url]);
    }

    public function direct_checkout_form_to(Request $request,$cartid='',$redirect_url='/')
    {
        Session::flush();

        $customer=[
            "name"=>($request->name??""),
            "email"=>($request->email),
            "phone"=>($request->phone??""),
            "address"=>($request->address??""),
            "city"=>($request->city??""),
            "state"=>($request->state??""),
            "country"=>($request->country??""),
            "zip"=>($request->zip??""),
            "wpuid"=>($request->wpuid??"")
        ];

        if($request->has('guest')){
            $customer["guest"]=($request->guest??"");
        }
         
        Session::put('customer_data',$customer);



        if(Session::has('redirect_url')){
            $redirect_url=Session::get('redirect_url');
        }else{
            $redirect_url = str_replace(['slash','{slash}'],['/','/'],$redirect_url);
            $redirect_url=!empty(base64_decode($redirect_url))?base64_decode($redirect_url):"/";
            Session::put('redirect_url',$redirect_url);
        }

        if(Session::has('cartid')){
            Session::put('cartid',$cartid);
        }

       if(Session::has('customer_data')){
        $customer = Session::get('customer_data');
       }else{
         $redirect_url = str_replace('cart','store',$redirect_url);
         return redirect()->away($redirect_url);
       }   


        // $sata = $this->syncFormData('HQjnYclZmO', 138);

        // dd(json_decode($sata,true));

        Cart::instance($cartid);
        //load cart in session
        Cart::checkout_restore($cartid);
        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }
       
        $club_info = tenant_club_info();
        $address = explode(',',$club_info['address']);
       // $store_state = trim($address[count($address)-2]);
        $store_state = isset($address[count($address)-2])?trim($address[count($address)-2]):'';

        if(isset($customer['state']) && ($customer['state'] == '' || $store_state != trim($customer['state']))){
            $tax = 0;
            Cart::setGlobalTax($tax);
        }else{
            Cart::setGlobalTax(0);
           $content = Cart::content();
            if ($content && $content->count()) {
                $content->each(function ($item, $key) {
                   if($item->options->tax == 1){
                       $item->setTaxRate(getTaxRate());
                   }
                });
            }
        }


        $states_data = json_decode(file_get_contents(resource_path('us_states.json')),true);

        $order_settings=get_option('order_settings',true);
        if ($order_settings->shipping_amount_type != 'distance') {
            $locations=Location::where([['status',1]])->whereHas('shippings')->with('shippings')->get();
        }else{
            $locations=[];
        }
        $getways=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();

        $order_method=$request->t ?? 'delivery';

        $invoice_data=optionfromcache('invoice_data');


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
        
        // Fetch all product types once
        $product_type = Category::where('type', 'product_type')
            ->select('id','slug','name')
            ->get();
        
        $p_types = $product_type->pluck('id')->all();
        
        $cartTermIds = Cart::instance('default')->content()->pluck('id')->all();
        
        $terms = Term::with('termcategories')
            ->whereIn('id', $cartTermIds)
            ->get();
        
        // Get flat product type IDs from cart
        $selected_product_type = $terms->flatMap(function ($term) use ($p_types) {
            return $term->termcategories
                ->pluck('category_id')
                ->intersect($p_types);
        })->unique()->values()->all();
        
        $count = count($selected_product_type);
        
        // ✅ Determine order type
        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };
        
       // dump($order_type);






        $payment_data['currency']   = $getways->currency_name ?? 'USD';
        $payment_data['test_mode']  = $getways->test_mode ?? 0;
        $payment_data['charge']     = $getways->charge ?? 0;
        $payment_data['getway_id']  = $getways->id ?? '';
        if (!empty($getways->data)) {
            foreach (json_decode($getways->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
         
           $payment_data['publishable_key'] = ($getways->test_mode == 1) ? $payment_data['test_publishable_key'] : $payment_data['publishable_key'];
           $payment_data['secret_key'] = ($getways->test_mode == 1) ? $payment_data['test_secret_key'] : $payment_data['secret_key'];
        }
      
        
       $free_shipping=Option::where('key','free_shipping')->first() ;

       $free_shipping = $free_shipping ? (int)$free_shipping->value : 0;

       $shipping_price = 0;
       
       $min_cart_total=Option::where('key','min_cart_total')->first();
       $min_cart_total = $min_cart_total ? (int)$min_cart_total->value : 100;

       $shipping_methods = null;
       $subtotal = Cart::subtotal();

       if($free_shipping){
       
         if((float)$subtotal >= (float)$min_cart_total){
                $shipping_methods = ['method_type'=>'free_shipping','label'=>'Free Shipping','pricing'=>0,'base_pricing'=>0];
                $shipping_price = 0;
            }
       }
       
       if(empty($shipping_methods)){
          $shipping_methods= json_decode(Option::where('key','shipping_method')->first()->value,true);

          if($shipping_methods['method_type'] == 'per_item'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::count() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'weight_based'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::weight() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'flat_rate'){


         if(is_array($shipping_methods['pricing'])){
             foreach($shipping_methods['pricing'] as $index){


            $from = (float)$index['from']??0;
            $to = (float) $index['to'] > 0 ?(float) $index['to']: PHP_INT_MAX;

            if($subtotal > $from && $subtotal <= $to){
                $shipping_price = (float)$index['price'];
            }
             }
         }

        }
       }

      

        $total =  Cart::total() + $shipping_price;

        $credit_card_fee = credit_card_fee($total);

        $booster_platform_fee = booster_club_chagre($total);

        $grand_total = $total;
       // $grand_total = $total+$credit_card_fee + $booster_platform_fee;
       $cover_fee = $grand_total + $credit_card_fee + $booster_platform_fee;
         
       $credit_card_fee1 = credit_card_fee($cover_fee);

       $booster_platform_fee1 = booster_club_chagre($cover_fee);

       $cover_fee =  $credit_card_fee1 + $booster_platform_fee1;


        return view('store.checkout.checkout-form',compact('locations','states_data','getways','request','order_method','order_settings','invoice_data','page_data','pickup_order','pre_order','source_code','payment_data','shipping_methods','shipping_price','customer','order_type','credit_card_fee','booster_platform_fee','cover_fee'));
    }


    public function direct_checkout_to(Request $request,$cartid='',$redirect_url='/'){
        Session::flush();
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        //  if ($validator->fails()) {
        //     return redirect()->away($redirect_url.'/?type=error&message='.$validator->errors()->first());
        // }

        $customer=[
            "name"=>($request->name??""),
            "email"=>($request->email),
            "phone"=>($request->phone??""),
            "address"=>($request->address??""),
            "city"=>($request->city??""),
            "state"=>($request->state??""),
            "country"=>($request->country??""),
            "zip"=>($request->zip??""),
            "wpuid"=>($request->wpuid??"")
        ];

        if($request->has('guest')){
            $customer["guest"]=($request->guest??"");
        }
         
        

        Session::put('customer_data',$customer);

       return redirect()->route('direct.checkout',['cartid'=>$cartid,'redirect_url'=>$redirect_url]);
    }

    public function direct_checkout(Request $request,$cartid='',$redirect_url='/')
    {

        if(Session::has('redirect_url')){
            $redirect_url=Session::get('redirect_url');
        }else{
            $redirect_url = str_replace(['slash','{slash}'],['/','/'],$redirect_url);
            $redirect_url=!empty(base64_decode($redirect_url))?base64_decode($redirect_url):"/";
            Session::put('redirect_url',$redirect_url);
        }

        if(Session::has('cartid')){
            Session::put('cartid',$cartid);
        }

       if(Session::has('customer_data')){
        $customer = Session::get('customer_data');
       }else{
         $redirect_url = str_replace('cart','store',$redirect_url);
         return redirect()->away($redirect_url);
       }   


        // $sata = $this->syncFormData('HQjnYclZmO', 138);

        // dd(json_decode($sata,true));

        Cart::instance($cartid);
        //load cart in session
        Cart::checkout_restore($cartid);
        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }
       
        $club_info = tenant_club_info();
        $address = explode(',',$club_info['address']);
       // $store_state = trim($address[count($address)-2]);
        $store_state = isset($address[count($address)-2])?trim($address[count($address)-2]):'';

        if(isset($customer['state']) && ($customer['state'] == '' || $store_state != trim($customer['state']))){
            $tax = 0;
            Cart::setGlobalTax($tax);
        }else{
            Cart::setGlobalTax(0);
           $content = Cart::content();
            if ($content && $content->count()) {
                $content->each(function ($item, $key) {
                   if($item->options->tax == 1){
                       $item->setTaxRate(getTaxRate());
                   }
                });
            }
        }


        $states_data = json_decode(file_get_contents(resource_path('us_states.json')),true);

        $order_settings=get_option('order_settings',true);
        if ($order_settings->shipping_amount_type != 'distance') {
            $locations=Location::where([['status',1]])->whereHas('shippings')->with('shippings')->get();
        }else{
            $locations=[];
        }
        $getways=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();

        $order_method=$request->t ?? 'delivery';

        $invoice_data=optionfromcache('invoice_data');


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
        
        // Fetch all product types once
        $product_type = Category::where('type', 'product_type')
            ->select('id','slug','name')
            ->get();
        
        $p_types = $product_type->pluck('id')->all();
        
        $cartTermIds = Cart::instance('default')->content()->pluck('id')->all();
        
        $terms = Term::with('termcategories')
            ->whereIn('id', $cartTermIds)
            ->get();
        
        // Get flat product type IDs from cart
        $selected_product_type = $terms->flatMap(function ($term) use ($p_types) {
            return $term->termcategories
                ->pluck('category_id')
                ->intersect($p_types);
        })->unique()->values()->all();
        
        $count = count($selected_product_type);
        
        // ✅ Determine order type
        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };
        
       // dump($order_type);






        $payment_data['currency']   = $getways->currency_name ?? 'USD';
        $payment_data['test_mode']  = $getways->test_mode ?? 0;
        $payment_data['charge']     = $getways->charge ?? 0;
        $payment_data['getway_id']  = $getways->id ?? '';
        if (!empty($getways->data)) {
            foreach (json_decode($getways->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
         
           $payment_data['publishable_key'] = ($getways->test_mode == 1) ? $payment_data['test_publishable_key'] : $payment_data['publishable_key'];
           $payment_data['secret_key'] = ($getways->test_mode == 1) ? $payment_data['test_secret_key'] : $payment_data['secret_key'];
        }
      
        
       $free_shipping=Option::where('key','free_shipping')->first() ;

       $free_shipping = $free_shipping ? (int)$free_shipping->value : 0;

       $shipping_price = 0;
       
       $min_cart_total=Option::where('key','min_cart_total')->first();
       $min_cart_total = $min_cart_total ? (int)$min_cart_total->value : 100;

       $shipping_methods = null;
       $subtotal = Cart::subtotal();

       if($free_shipping){
       
         if((float)$subtotal >= (float)$min_cart_total){
                $shipping_methods = ['method_type'=>'free_shipping','label'=>'Free Shipping','pricing'=>0,'base_pricing'=>0];
                $shipping_price = 0;
            }
       }
       
       if(empty($shipping_methods)){
          $shipping_methods= json_decode(Option::where('key','shipping_method')->first()->value,true);

          if($shipping_methods['method_type'] == 'per_item'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::count() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'weight_based'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::weight() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'flat_rate'){


         if(is_array($shipping_methods['pricing'])){
             foreach($shipping_methods['pricing'] as $index){


            $from = (float)$index['from']??0;
            $to = (float) $index['to'] > 0 ?(float) $index['to']: PHP_INT_MAX;

            if($subtotal > $from && $subtotal <= $to){
                $shipping_price = (float)$index['price'];
            }
             }
         }

        }
       }

      

        $total =  Cart::total() + $shipping_price;

        $credit_card_fee = credit_card_fee($total);

        $booster_platform_fee = booster_club_chagre($total);

        $grand_total = $total;
       // $grand_total = $total+$credit_card_fee + $booster_platform_fee;
       $cover_fee = $grand_total + $credit_card_fee + $booster_platform_fee;
         
       $credit_card_fee1 = credit_card_fee($cover_fee);

       $booster_platform_fee1 = booster_club_chagre($cover_fee);

       $cover_fee =  $credit_card_fee1 + $booster_platform_fee1;


        return view('store.checkout.checkout',compact('locations','states_data','getways','request','order_method','order_settings','invoice_data','page_data','pickup_order','pre_order','source_code','payment_data','shipping_methods','shipping_price','customer','order_type','credit_card_fee','booster_platform_fee','cover_fee'));
    }


    public function direct_new_checkout(Request $request,$cartid='',$redirect_url='/')
    {

        if(Session::has('redirect_url')){
            $redirect_url=Session::get('redirect_url');
        }else{
            $redirect_url = str_replace(['slash','{slash}'],['/','/'],$redirect_url);
            $redirect_url=!empty(base64_decode($redirect_url))?base64_decode($redirect_url):"/";
            Session::put('redirect_url',$redirect_url);
        }

        if(Session::has('cartid')){
            Session::put('cartid',$cartid);
        }

       if(Session::has('customer_data')){
        $customer = Session::get('customer_data');
       }else{
         $redirect_url = str_replace('cart','store',$redirect_url);
         return redirect()->away($redirect_url);
       }   


        // $sata = $this->syncFormData('HQjnYclZmO', 138);

        // dd(json_decode($sata,true));

        Cart::instance($cartid);
        //load cart in session
        Cart::checkout_restore($cartid);
        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }
       
        $club_info = tenant_club_info();
        $address = explode(',',$club_info['address']);
       // $store_state = trim($address[count($address)-2]);
        $store_state = isset($address[count($address)-2])?trim($address[count($address)-2]):'';

        if(isset($customer['state']) && ($customer['state'] == '' || $store_state != trim($customer['state']))){
            $tax = 0;
            Cart::setGlobalTax($tax);
        }else{
            Cart::setGlobalTax(0);
           $content = Cart::content();
            if ($content && $content->count()) {
                $content->each(function ($item, $key) {
                   if($item->options->tax == 1){
                       $item->setTaxRate(getTaxRate());
                   }
                });
            }
        }


        $states_data = json_decode(file_get_contents(resource_path('us_states.json')),true);

        $order_settings=get_option('order_settings',true);
        if ($order_settings->shipping_amount_type != 'distance') {
            $locations=Location::where([['status',1]])->whereHas('shippings')->with('shippings')->get();
        }else{
            $locations=[];
        }
        $getways=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();

        $order_method=$request->t ?? 'delivery';

        $invoice_data=optionfromcache('invoice_data');


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
        
        // Fetch all product types once
        $product_type = Category::where('type', 'product_type')
            ->select('id','slug','name')
            ->get();
        
        $p_types = $product_type->pluck('id')->all();
        
        $cartTermIds = Cart::instance('default')->content()->pluck('id')->all();
        
        $terms = Term::with('termcategories')
            ->whereIn('id', $cartTermIds)
            ->get();
        
        // Get flat product type IDs from cart
        $selected_product_type = $terms->flatMap(function ($term) use ($p_types) {
            return $term->termcategories
                ->pluck('category_id')
                ->intersect($p_types);
        })->unique()->values()->all();
        
        $count = count($selected_product_type);
        
        // ✅ Determine order type
        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };
        
       // dump($order_type);






        $payment_data['currency']   = $getways->currency_name ?? 'USD';
        $payment_data['test_mode']  = $getways->test_mode ?? 0;
        $payment_data['charge']     = $getways->charge ?? 0;
        $payment_data['getway_id']  = $getways->id ?? '';
        if (!empty($getways->data)) {
            foreach (json_decode($getways->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
         
           $payment_data['publishable_key'] = ($getways->test_mode == 1) ? $payment_data['test_publishable_key'] : $payment_data['publishable_key'];
           $payment_data['secret_key'] = ($getways->test_mode == 1) ? $payment_data['test_secret_key'] : $payment_data['secret_key'];
        }
      
        
       $free_shipping=Option::where('key','free_shipping')->first() ;

       $free_shipping = $free_shipping ? (int)$free_shipping->value : 0;

       $shipping_price = 0;
       
       $min_cart_total=Option::where('key','min_cart_total')->first();
       $min_cart_total = $min_cart_total ? (int)$min_cart_total->value : 100;

       $shipping_methods = null;
       $subtotal = Cart::subtotal();

       if($free_shipping){
       
         if((float)$subtotal >= (float)$min_cart_total){
                $shipping_methods = ['method_type'=>'free_shipping','label'=>'Free Shipping','pricing'=>0,'base_pricing'=>0];
                $shipping_price = 0;
            }
       }
       
       if(empty($shipping_methods)){
          $shipping_methods= json_decode(Option::where('key','shipping_method')->first()->value,true);

          if($shipping_methods['method_type'] == 'per_item'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::count() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'weight_based'){

            $shipping_price = $shipping_methods['base_pricing'] + Cart::weight() * $shipping_methods['pricing'];

        }else if($shipping_methods['method_type'] == 'flat_rate'){


         if(is_array($shipping_methods['pricing'])){
             foreach($shipping_methods['pricing'] as $index){


            $from = (float)$index['from']??0;
            $to = (float) $index['to'] > 0 ?(float) $index['to']: PHP_INT_MAX;

            if($subtotal > $from && $subtotal <= $to){
                $shipping_price = (float)$index['price'];
            }
             }
         }

        }
       }

        // ===== In-Person Pickup settings () =====
        $pickup_details = [];
        $allow_inperson_pickup = 0;
        
        $inperson_pickup = Option::where('key', 'inperson_pickup_details')->first();
        
        if ($inperson_pickup && !empty($inperson_pickup->value)) {
            $pickup_details = json_decode($inperson_pickup->value, true) ?: [];
            $allow_inperson_pickup = (int)($pickup_details['enabled'] ?? 0);
        }
        

        $shipping_options = [];
        
        // Regular shipping option (calculated above)
        $shipping_options[] = [
            'key'   => $shipping_methods['method_type'],
            'label' => $shipping_methods['label'],
            'price' => (float) $shipping_price,
            'info'  => $shipping_methods,
        ];
      
        // Pickup option (only if enabled)
        if ($allow_inperson_pickup === 1) 
        { 
            $shipping_options[] =
             [ 'key' => 'inperson_pickup', 
               'label' => 'In-Person Pick Up',
              'price' => 0, 'info' => [ 'method_type' => 'inperson_pickup', 'label' => 'In-Person Pick Up', 'pricing' => 0, 'base_pricing' => 0, 
              'details' => $pickup_details 
            ], 
            ]; 
        }

        $total =  Cart::total() + $shipping_price;

        $credit_card_fee = credit_card_fee($total);

        $booster_platform_fee = booster_club_chagre($total);

        $grand_total = $total;
       // $grand_total = $total+$credit_card_fee + $booster_platform_fee;
       $cover_fee = $grand_total + $credit_card_fee + $booster_platform_fee;
         
       $credit_card_fee1 = credit_card_fee($cover_fee);

       $booster_platform_fee1 = booster_club_chagre($cover_fee);

       $cover_fee =  $credit_card_fee1 + $booster_platform_fee1;


        return view('store.checkout.new-checkout',compact('locations','states_data','getways','request','order_method','order_settings','invoice_data','page_data','pickup_order','pre_order','source_code','payment_data','shipping_methods','shipping_price','customer','order_type','credit_card_fee','booster_platform_fee',
        'cover_fee','shipping_options','pickup_details','allow_inperson_pickup'));
    }

    public function newMakeOrder(Request $request)
    {


        $redirect_url=Session::has('redirect_url')?Session::get('redirect_url'):'https://www.boostr.co';

        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }

       $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|max:20',
            'shipping_method' => 'required',
       ]);

       $sms_consent = $request->has('sms_consent') ? 1 : 0;

       $order_method='delivery';
       $notify_driver='mail';
       $order_settings=get_option('order_settings',true);

       // add discount
        $subtotal = Cart::subtotal();
     
        $shipping_price = 0;
        $shipping_method_label = '';
         $pickup_details = [];
        $allow_inperson_pickup = 0;
        
        $inperson_pickup = Option::where('key', 'inperson_pickup_details')->first();
        if ($inperson_pickup && !empty($inperson_pickup->value)) {
            $pickup_details = json_decode($inperson_pickup->value, true) ?? [];
            $allow_inperson_pickup = (int)($pickup_details['enabled'] ?? 0);
        }
        
        if (!empty($request->shipping_method)) {
            
        // ✅ In-Person Pickup selected
        if ($request->shipping_method === 'inperson_pickup' && $allow_inperson_pickup === 1) {
            $shipping_price = 0;
            $shipping_method_label = 'In-Person Pick Up';
            $order_method = 'pickup';   // important
        }
        // ✅ Free shipping
        else if ($request->shipping_method === 'free_shipping') {
            $shipping_price = 0;
            $shipping_method_label = 'Free Shipping';
            $order_method = 'delivery';
        }
        // ✅ Normal delivery shipping methods
        else {
            $order_method = 'delivery';

            $shippingDetails = json_decode(Option::where('key','shipping_method')->first()->value,true);

            if ($shippingDetails['method_type'] == 'per_item') {
                $shipping_price = $shippingDetails['base_pricing'] + Cart::count() * $shippingDetails['pricing'];
                $shipping_method_label = $shippingDetails['label'];

            } else if ($shippingDetails['method_type'] == 'weight_based') {
                $shipping_price = $shippingDetails['base_pricing'] + Cart::weight() * $shippingDetails['pricing'];
                $shipping_method_label = $shippingDetails['label'];

            } else if ($shippingDetails['method_type'] == 'flat_rate') {
                if (is_array($shippingDetails['pricing'])) {
                    foreach ($shippingDetails['pricing'] as $index) {
                        $from = (float)($index['from'] ?? 0);
                        $to   = ((float)($index['to'] ?? 0) > 0) ? (float)$index['to'] : PHP_INT_MAX;

                        if ($subtotal > $from && $subtotal <= $to) {
                            $shipping_price = (float)$index['price'];
                            $shipping_method_label = $shippingDetails['label'];
                        }
                    }
                }
            }
        }

        } else {
            $order_method = 'delivery';
        }


        
        // if (Session::has('couponDiscount')) {
        //     $sessionDiscountArr = Session::get('couponDiscount');
        //     $total_amount = number_format($sessionDiscountArr['totalDiscount'],2);

        // } else {
        //     // $subtotal = Cart::subtotal();
        //     $total_amount=str_replace(',','',Cart::total());
        // }

        
       $subtotal = Cart::subtotal();
       $total_amount=str_replace(',','',Cart::total());
       $tax = Cart::tax();


       $total_discount=str_replace(',','',Cart::discount());

       $total_amount =  $total_amount + $shipping_price;

       $credit_card_fee = credit_card_fee($total_amount);

       $booster_platform_fee = booster_club_chagre($total_amount);

       //$total_amount = $total_amount+$credit_card_fee + $booster_platform_fee;


       $cover_fee = 0;
       if($request->cover_fee_checkbox){
        $cover_fee = $total_amount + $credit_card_fee + $booster_platform_fee;
            
        $credit_card_fee = credit_card_fee($cover_fee);

        $booster_platform_fee = booster_club_chagre($cover_fee);

        $cover_fee =  $credit_card_fee + $booster_platform_fee;  
        
       }

       $total_amount = $total_amount+$cover_fee;

       $revenue = $total_amount-($tax + $booster_platform_fee + $credit_card_fee);

       $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
       //Process Payment
        $gateway_data_info = json_decode($gateway->data);

        $booostr_stripe_account = $gateway_data_info->stripe_account_id;

        Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);

        $total_application_fee = $credit_card_fee + $booster_platform_fee;

        $club_receives = $total_amount - $total_application_fee;



        $paymentIntent = PaymentIntent::create([
            'amount' => round($total_amount * 100),
            'currency' => 'usd',
            'capture_method' => 'manual',
            'application_fee_amount' => round($cover_fee * 100),
            'transfer_data' => [
                'destination' => $booostr_stripe_account,
            ],
            'metadata' => [
                'credit_card_fee' => number_format($credit_card_fee, 2),
                'booster_platform_fee' => number_format($booster_platform_fee, 2),
                'total_fees' => number_format($total_application_fee, 2),
                'club_receives' => number_format($club_receives, 2),
            ],
            'automatic_payment_methods' => ['enabled' => true],
        ]);



        DB::beginTransaction();
        try {
          if (Auth::check() == false && !$request->has('guest')) {
    
        $user = User::firstOrNew(['email' => $request->email]);

        // create only if new
        if (!$user->id) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role_id = 4;
            $user->password = \Hash::make($request->email);
        }
    
        // ✅ ALWAYS update meta (new + existing)
        $existingMeta = [];
        if (!empty($user->meta)) {
            $existingMeta = json_decode($user->meta, true) ?: [];
        }
    
        $existingMeta['wpuid'] = $request->wpuid ?? 0;
        $existingMeta['sms_consent'] = $sms_consent;

        $user->meta = json_encode($existingMeta);
        $user->save();
    
        Auth::loginUsingId($user->id);
        }
            $order = new Order;
            if (Auth::check() == true) {
                $order->user_id = Auth::id();
            }

            $couponcode = null;

            if(Session::has('couponDiscountCode')){
                $couponcode = Session::get('couponDiscountCode');
            }    


            $notify_driver = 'mail';

            $order->getway_id = $gateway->id;
            $order->status_id = 3;
            $order->tax = str_replace(',', '', Cart::tax());
            $order->discount = $total_discount;
            $order->coupon_code = $couponcode;
            $order->total = $total_amount;
            $order->order_method = $order_method ?? 'delivery';
            $order->notify_driver = $notify_driver;
            $order->transaction_id = $paymentIntent->id;
            $order->payment_status = 0;
            $order->placed_at = Carbon::now()->setTimezone(config('app.timezone'));
            $order->save();
            
            
             // ✅ Save pickup details if in-person pickup selected
             if ($request->shipping_method === 'inperson_pickup') {
                \App\Models\Ordermeta::updateOrCreate(
                    ['order_id' => $order->id, 'key' => 'inperson_pickup_details'],
                    ['value' => json_encode($pickup_details)]
                );
            }
            // $credit_card_processing_method = Option::where('key','credit_card_processing_method')->first();
            // $credit_card_processing_method = $credit_card_processing_method ? $credit_card_processing_method->value : 'manual';

            // if($credit_card_processing_method == 'auto' && $paymentresult['risk_level'] == 'normal'){
            
            //          $payment_data['transaction_id'] =  $order->transaction_id;
                         
            //             // dd($payment_data);
                         
            //          $paymentresult= $gateway->namespace::capture_payment($payment_data);
                     
            //     if($paymentresult['payment_status'] == 1){
            //        $order->payment_status =$paymentresult['payment_status'];
            //        $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
            //        $order->save(); 
            //     }
            // } 


            if($couponcode != null){
                $coupon = Coupon::where('code',$couponcode)->first();
                $coupon->used_count =  $coupon->used_count + 1 ;
                $coupon->save();
            }


            $oder_items = [];
            $total_weight = 0;
            $priceids = [];
            $cartid = null;

            foreach (Cart::content() as $row) {

                $data['order_id'] = $order->id;
                $data['term_id'] = $row->id;
                $data['info'] = json_encode([
                    'sku' => $row->options->sku ?? '',
                    'options' => $row->options->options ?? []
                ]);

            //    if(isset($row->options->price_id)){
            //     array_push($priceids, ['order_id' => $order->id, 'price_id' => $row->options->price_id, 'qty' => $row->qty]);
            //    }

                if($row->options->options == []){
                        array_push($priceids, ['order_id' => $order->id, 'price_id' => $row->options->price_id[0], 'qty' => $row->qty]);
                }else{
                    foreach ($row->options->options as $optionVal) {
                        array_push($priceids, ['order_id' => $order->id, 'price_id' => $optionVal->id, 'qty' => (int)$row->qty]);
                    }
                }

                $data['qty'] = $row->qty;
                $data['amount'] = $row->price;
                $total_weight = $total_weight + $row->weight;
                array_push($oder_items, $data);
                $cartid = $row->instance;
            }

            $order->orderitems()->insert($oder_items);

            if ($request->order_method == 'table') {
                $order->ordertable()->attach($request->table);
            }
             if ($order_method == 'delivery') {
                $delivery_info['address'] = $request->shipping['address'].' '. $request->shipping['city'].', '.$request->shipping['state'].', '.$request->shipping['country'];
                $delivery_info['post_code'] = $request->shipping['post_code'];
                $delivery_info['shipping_method'] = $request->shipping_method;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['credit_card_fee'] = $credit_card_fee;
                $delivery_info['booster_platform_fee'] = $booster_platform_fee;
                $delivery_info['cover_fee'] = $cover_fee;

                $order->shipping()->create([
                    'location_id' => $request->location,
                    'shipping_price' => $shipping_price,
                    'lat' => $request->my_lat ?? null,
                    'long' => $request->my_long ?? null,
                    'weight' => $total_weight,
                    'info' => json_encode($delivery_info)
                ]);
            }

            if (!empty($request->name) || !empty($request->email) || !empty($request->phone) || !empty($request->comment)) {
                $customer_info['name'] = $request->name;
                $customer_info['email'] = $request->email;
                $customer_info['phone'] = $request->phone;
                $customer_info['wpuid'] = $request->wpuid??0;
                $customer_info['note'] = $request->comment ?? "";
                $customer_info['billing'] = $request->billing ?? "";
                $customer_info['shipping'] = $request->shipping ?? "";
                $customer_info['credit_card_fee'] = $credit_card_fee;
                $customer_info['booster_platform_fee'] = $booster_platform_fee;
                $customer_info['cover_fee'] = $cover_fee;
                $customer_info['cart_id'] = $cartid;
                $customer_info['sms_consent'] = $sms_consent;

                 \App\Models\Ordermeta::updateOrCreate(
                    ['order_id' => $order->id, 'key' => 'orderinfo'],
                    ['value' => json_encode($customer_info)]
                );
                // $transcation_log = new Ordermeta;
                // $transcation_log->order_id = $order->id;
                // $transcation_log->key = 'transcation_log';
                // $transcation_log->value = json_encode($paymentresult['transaction_log']);
                // $transcation_log->save();
    
                // $order->orderlasttrans()->create([
                //     'key' => 'last_transcation_log',
                //     'value' => json_encode($paymentresult['transaction_log'])
                // ]);
            }

            if (count($priceids) != 0) {
                $order->orderstockitems()->insert($priceids);
            }


          
            DB::commit();
            
            $club_info = tenant_club_info();
            
             $name = explode(' ',$request->name);

            $contact_manager_data = array(
                'first_name' => $name[0],
                'last_name' => $name[1]??'',
                'user_id' =>  $request->wpuid ??0,
                'phone_number' => $request->phone,					
                'booster_name' => $name[0],
                'country' =>   $request->billing['country'],									
                'address_1' => $request->billing['address'],
                'address_2' =>  '',
                'city' => $request->billing['city'],
                'state' =>  $request->billing['state'],
                'zip' =>  $request->billing['post_code'],													
                'email' =>  $request->email,                   
                'booster_id' =>Tenant('club_id'),
                'booster_level_id' => 4,
                'customer_tag' => 'online store customer',
                'addedsource' => 'storetool',
                'opt_in_tools' => $sms_consent,
            );	
            
           
            $subtotal = 0;
            
            foreach ($order->orderitems ?? [] as $row){
                $subtotal = $subtotal + $row->amount*$row->qty;
            }

           $user_recipt = [
                'contact_mgr_data'=>$contact_manager_data,
                'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
                'receipt_title'=>$request->name,
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  $request->wpuid ??0,
                'amount'=>$order->total,
                'revenue'=>$revenue,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
                'order_total'=>$order->total,
                'order_subtotal'=>$subtotal,
            ];

        //   $recipt =  $this->send_order_recipt($user_recipt);

        //     \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
        //     \App\Lib\NotifyToUser::sendEmail($order, $request->email, 'user');

            $prices=Orderstock::where('order_id',$order->id)->whereHas('price')->with('price')->get();

            foreach ($prices as $key => $row) {
                $current_stock=$row->price->qty;
                $order_stock=$row->qty;

                if ($order_stock >= $current_stock) {
                    $new_stock=0;
                    $stock_status=0;
                }else{
                    $new_stock=$current_stock-$order_stock;
                    $stock_status=1;
                }

                $price_row=Price::find($row->price_id);
                if (!empty($price_row) && $price_row->stock_manage ) {
                    $price_row->qty=$new_stock;
                    $price_row->stock_status=$stock_status;
                    $price_row->save();
                }
               // array_push($deletable_ids,$row->id);
            }

            if(Session::has('cart') && $cartid != null){

                $this->syncFormData($cartid,$order->id);

                Cart::destroy($cartid);
            }

            $parts = parse_url($redirect_url);

            if (isset($parts['scheme'], $parts['host'], $parts['path'])) {
                $redirect_url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
            }

            if(Session::has('couponDiscount')){
                Session::forget('couponDiscount');
            }

            if(strpos($redirect_url, 'login-customizer') !== false){
               $redirect_url = str_replace('login-customizer', 'listing/'.tenant('id'), $redirect_url);
            }


          //  return redirect()->away($redirect_url . '/?tab=thankyou&club_id='.Tenant('club_id').'&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no);

            return redirect()->route('checkout.payment', $order->invoice_no);

        } catch (\Throwable $th) {
            DB::rollback();

        // dd($th);die;
          
            return redirect()->away($redirect_url . '/?type=error&message=Oops something wrong while saving order data');
        }
        return redirect()->away($redirect_url);

    }


    // private function sync_contact_to_crm($contact_manager_data)
    // {
    //   $postData = json_encode($contact_manager_data);
    
    //     $url = env("WP_API_URL");
    //   $url = ($url != '') 
    // ? $url . '/add-pos-contact' 
    // : "https://staging3.booostr.co/wp-json/store-api/v1/add-pos-contact";
    
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_USERAGENT, 'Tenant store');
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    //     $response = curl_exec($ch);
    // // dd($response);die;
    //     if (curl_errno($ch)) {
    //         \Log::error('CRM sync cURL error: ' . curl_error($ch));
    //     }
    
    //     curl_close($ch);
    
    //     \Log::info('CRM sync response', [
    //         'response' => $response
    //     ]);
    
    //     return $response;
    // }
    
    
    function paymentPage($order_id)
    {

        $order = Order::with(['ordermeta', 'orderitems'])->findOrFail($order_id);

        $gateway = Getway::where('status','!=',0)->where('namespace','App\Lib\Stripe')->first();
        $gateway_data = json_decode($gateway->data);
        
        $publishable_key = $gateway->test_mode ? $gateway_data->test_publishable_key : $gateway_data->publishable_key;

        $meta = json_decode($order->ordermeta->value, true);
        
        $client_secret = null;
        
        Stripe::setApiKey($gateway->test_mode ? $gateway_data->test_secret_key : $gateway_data->secret_key);
         
        if($order->transaction_id){
            
         $paymentIntent = PaymentIntent::retrieve($order->transaction_id);
        
          if($paymentIntent->status !== 'succeeded'){
            $client_secret = $paymentIntent->client_secret;
          }
          
        }
        

        // Shipping info
        $shipping = $meta['shipping'] ?? [];
        
        // Fees (from your JSON)
        $credit_card_fee = $meta['credit_card_fee'] ?? 0;
        $booster_platform_fee = $meta['booster_platform_fee'] ?? 0;
        $cover_fee = $meta['cover_fee'] ?? 0;
        
        // Delivery fee (you might store it somewhere else or calculate)
        $delivery_fee = $order->total - $order->orderitems->sum('amount') - $cover_fee; 
        
        // Subtotal
        $subtotal = $order->orderitems->sum('amount');
        
        // Discount
        $discount = $order->discount ?? 0;
        
        // Tax
        $tax = $order->tax ?? 0;
        
        // Total
        $total = $order->total;

        return view('store.checkout.payment', compact(
            'order',
            'subtotal',
            'discount',
            'tax',
            'delivery_fee',
            'cover_fee',
            'total',
            'client_secret',
             'publishable_key'
        ));

    }

    function processPayment(Request $request, $order_id)
    {
        
     $redirect_url=Session::has('redirect_url')?Session::get('redirect_url'):'https://www.boostr.co';

        try {
            
            $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($order_id);
            
             $gateway = Getway::where('status','!=',0)->where('namespace','App\Lib\Stripe')->first();
             $gateway_data = json_decode($gateway->data);
             
         $publishable_key = $gateway->test_mode ? $gateway_data->test_publishable_key : $gateway_data->publishable_key;
         Stripe::setApiKey($gateway->test_mode ? $gateway_data->test_secret_key : $gateway_data->secret_key);
        
                $ordermeta=json_decode($order->ordermeta->value ?? '',true);

        if($order->transaction_id){
            
            $paymentIntent = PaymentIntent::retrieve($order->transaction_id);
            
             $riskLevel = 'not_assessed';
             
            // dd($paymentIntent);
             
             if ($paymentIntent->latest_charge) {
                 
                $charge = Charge::retrieve([
                    'id' => $paymentIntent->latest_charge,
                    'expand' => ['outcome', 'balance_transaction'],
                ]);
        
                $riskLevel = $charge->outcome->risk_level ?? 'N/A';
            }
            
             
                $credit_card_processing_method = Option::where('key','credit_card_processing_method')->first();
                $credit_card_processing_method = $credit_card_processing_method ? $credit_card_processing_method->value : 'manual';
    
                if($credit_card_processing_method == 'auto' && $riskLevel == 'normal' && $paymentIntent->status === 'requires_capture'){
                
                            // dd($payment_data);
                     $captured = $paymentIntent->capture();
                     
                    if($captured->status === 'succeeded'){
                       $order->payment_status =1;
                       $order->risk_level = $riskLevel;
                       $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
                       $order->save(); 
                    }
                    
                }else if($paymentIntent->status === 'requires_capture'){
                   $order->payment_status =4;
                   $order->risk_level = $riskLevel;
                   $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
                   $order->save();  
                }else if($paymentIntent->status === 'succeeded'){
                   $order->payment_status =1;
                   $order->risk_level = $riskLevel;
                   $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
                   $order->save();  
                }
                
                
            
            //   if($paymentIntent->status !== 'succeeded'){
            //     $client_secret = $paymentIntent->client_secret;
            //   }
        }
    
        $paymentIntent = PaymentIntent::retrieve(['id' => $order->transaction_id,'expand' => ['charges.data.outcome', 'charges.data.review']]);

        if($paymentIntent->status === 'succeeded' || $paymentIntent->status === 'requires_capture' ){
          
          
          if(isset($ordermeta['email']) && $ordermeta['cart_id'] !== '' ){

                $this->syncFormData($ordermeta['cart_id'],$order->id);

            }  
           //  dd($order);
            
            $reciptdata = $this->order_recipt_data($order_id);
       
            $recipt =  $this->send_order_recipt($reciptdata);

            \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
            
            \App\Lib\NotifyToUser::sendEmail($order, $ordermeta['email'], 'user');

        }
    
    
        $parts = parse_url($redirect_url);
        
        if (isset($parts['scheme'], $parts['host'], $parts['path'])) {
        	$redirect_url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
        }
        
        if(strpos($redirect_url, 'login-customizer') !== false){
           $redirect_url = str_replace('login-customizer', 'listing/'.tenant('id'), $redirect_url);
        }
        
        
        $url = $redirect_url . '/?tab=thankyou&club_id='.Tenant('club_id').'&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no;

        return redirect()->away($redirect_url . '/?tab=thankyou&club_id='.Tenant('club_id').'&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no);
    
        } catch (\Exception $e) {
            
            dd($e);die;
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
    public function order_recipt_data($order_id){
        
      $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($order_id);
        
        $order_date = Carbon::parse($order->created_at)->format('Y-m-d');
        $qty = $order->orderitems[0]['qty'];
        $product_amount = $order->orderitems[0]['amount'];
        $sub_total = $product_amount*$qty;
        $sales_tax = $order->tax;
        $order_total = $order->total;

        $ordermeta=json_decode($order->ordermeta->value ?? '',true);
        
        $name = explode(' ',$ordermeta['name']);
        
        $club_info = tenant_club_info();
        
        $shippingPrice = $order->shippingwithinfo['shipping_price']??0;
        $jsonString = $order->shippingwithinfo->info ?? null;
        
        
        // Decode the JSON string into a PHP array
        $shipping_data = json_decode($jsonString, true);

        $credit_card_fee = $shipping_data['credit_card_fee'] ?? 0;
        $booster_platform_fee = $shipping_data['booster_platform_fee'] ??0;
        $processing_fees = $credit_card_fee+$booster_platform_fee;

        $revenue = $order_total-($sales_tax+$processing_fees);
       
       $sub_total = $revenue - $shippingPrice;
       
               $contact_manager_data = array(
									'first_name' => $name[0],
									'last_name' => $name[1]??'',
									'user_id' =>  $ordermeta['wpuid']??0,
									'phone_number' => $ordermeta['phone'],					
									'booster_name' => $name[0],
									'country' =>   $ordermeta['billing']['country'],									
									'address_1' => $ordermeta['billing']['address'],
									'address_2' =>  '',
									'city' => $ordermeta['billing']['city'],
									'state' =>  $ordermeta['billing']['state'],
									'zip' =>  $ordermeta['billing']['post_code'],													
									'email' =>  $ordermeta['email'],                   
									'booster_id' =>Tenant('club_id'),
									'booster_level_id' => 4,
									'contact_tags' => '',
                                    'customer_tag' => 'online store customer',
                                    'addedsource' => 'storetool',
                                    'opt_in_tools' => $ordermeta['sms_consent'] ?? 0,
								);
								
								
			$user_recipt = [
                'contact_mgr_data'=>$contact_manager_data,
                'opt_in_tools' => (int) ($ordermeta['sms_consent'] ?? 0),
                'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
                'receipt_title'=>$ordermeta['name'],
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  $ordermeta['wpuid']??0,
                'amount'=>$order_total,
                'revenue'=>$revenue,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
                'order_total'=>$order->total,
                'order_subtotal'=>$sub_total,
            ];				
								
								
			return $user_recipt;					
      
    }
    
    

    public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        $status = $request->query('redirect_status');

        if ($status === 'succeeded') {
            return view('store.checkout.payment_success', [
                'paymentIntentId' => $paymentIntentId,
            ]);
        }

        return redirect()->with('error', 'Payment failed or canceled.');
    }



    public function makeOrder(Request $request)
    {

        $redirect_url=Session::has('redirect_url')?Session::get('redirect_url'):'https://www.boostr.co';

        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }

       $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|max:20',
            'shipping_method' => 'required',
            'stripeToken' => 'required',
       ]);
       $order_method='delivery';
       $notify_driver='mail';
       $order_settings=get_option('order_settings',true);

       // add discount
        $subtotal = Cart::subtotal();
     
        $shipping_price = 0;
        $shipping_method_label = '';
        $pickup_details = [];
        $allow_inperson_pickup = 0;
        
        $inperson_pickup = Option::where('key', 'inperson_pickup_details')->first();
        if ($inperson_pickup && !empty($inperson_pickup->value)) {
            $pickup_details = json_decode($inperson_pickup->value, true) ?? [];
            $allow_inperson_pickup = (int)($pickup_details['enabled'] ?? 0);
        }
        
        if (!empty($request->shipping_method)) {
            
        // ✅ In-Person Pickup selected
        if ($request->shipping_method === 'inperson_pickup' && $allow_inperson_pickup === 1) {
            $shipping_price = 0;
            $shipping_method_label = 'In-Person Pick Up';
            $order_method = 'pickup';   // important
        }
        // ✅ Free shipping
        else if ($request->shipping_method === 'free_shipping') {
            $shipping_price = 0;
            $shipping_method_label = 'Free Shipping';
            $order_method = 'delivery';
        }
        // ✅ Normal delivery shipping methods
        else {
            $order_method = 'delivery';

            $shippingDetails = json_decode(Option::where('key','shipping_method')->first()->value,true);

            if ($shippingDetails['method_type'] == 'per_item') {
                $shipping_price = $shippingDetails['base_pricing'] + Cart::count() * $shippingDetails['pricing'];
                $shipping_method_label = $shippingDetails['label'];

            } else if ($shippingDetails['method_type'] == 'weight_based') {
                $shipping_price = $shippingDetails['base_pricing'] + Cart::weight() * $shippingDetails['pricing'];
                $shipping_method_label = $shippingDetails['label'];

            } else if ($shippingDetails['method_type'] == 'flat_rate') {
                if (is_array($shippingDetails['pricing'])) {
                    foreach ($shippingDetails['pricing'] as $index) {
                        $from = (float)($index['from'] ?? 0);
                        $to   = ((float)($index['to'] ?? 0) > 0) ? (float)$index['to'] : PHP_INT_MAX;

                        if ($subtotal > $from && $subtotal <= $to) {
                            $shipping_price = (float)$index['price'];
                            $shipping_method_label = $shippingDetails['label'];
                        }
                    }
                }
            }
        }

        } else {
            $order_method = 'delivery';
        }

        
        // if (Session::has('couponDiscount')) {
        //     $sessionDiscountArr = Session::get('couponDiscount');
        //     $total_amount = number_format($sessionDiscountArr['totalDiscount'],2);

        // } else {
        //     // $subtotal = Cart::subtotal();
        //     $total_amount=str_replace(',','',Cart::total());
        // }

        
       $subtotal = Cart::subtotal();
       $total_amount=str_replace(',','',Cart::total());
       $tax = Cart::tax();


       $total_discount=str_replace(',','',Cart::discount());

       $total_amount =  $total_amount + $shipping_price;

       $credit_card_fee = credit_card_fee($total_amount);

       $booster_platform_fee = booster_club_chagre($total_amount);

       //$total_amount = $total_amount+$credit_card_fee + $booster_platform_fee;

      


       $cover_fee = 0;
       if($request->cover_fee_checkbox){
        $cover_fee = $total_amount + $credit_card_fee + $booster_platform_fee;
            
        $credit_card_fee = credit_card_fee($cover_fee);

        $booster_platform_fee = booster_club_chagre($cover_fee);

        $cover_fee =  $credit_card_fee + $booster_platform_fee;  
        
       }

       $total_amount = $total_amount+$cover_fee;

       $revenue = $total_amount-($tax + $booster_platform_fee + $credit_card_fee);

       $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
       //Process Payment
        $gateway_data_info = json_decode($gateway->data);
        $payment_data['currency']   = strtoupper($gateway->currency_name) ?? 'USD';
        $payment_data['email']      = $request->email;
        $payment_data['name']       = $request->name;
        $payment_data['phone']      = $request->phone;
        $payment_data['billName']   = 'Boostr Sale';
        $payment_data['amount']     = $total_amount;
        $payment_data['application_fee_amount']  = $booster_platform_fee;
        $payment_data['credit_card_fee']  = $credit_card_fee;
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['charge']     = $gateway->charge ?? 0;
        $payment_data['pay_amount'] =  str_replace(',','',number_format($total_amount*$gateway->rate+$gateway->charge ?? 0,2));
        $payment_data['getway_id']  = $gateway->id;
        $payment_data['stripeToken']=$request->stripeToken;
        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

       $paymentresult= $gateway->namespace::charge_payment($payment_data);
      //$paymentresult= ['payment_status'=>4,'payment_id'=>'sffsdf43534'];

        if($paymentresult['payment_status'] != 4){
            return redirect()->back()->with(["error"=>"Sorry, we couldnt charge your card, please try another card"]);
        }

        DB::beginTransaction();
        try {
            if (Auth::check() == false && !$request->has('guest')) {
                $user = User::firstOrNew(['email' => $request->email]);
                if (!$user->id) {
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->phone = $request->phone;
                    $user->role_id = 4;
                    $user->meta = json_encode(['wpuid'=>$request->wpuid]);
                    $user->password = \Hash::make($request->email);
                    $user->save();
                }
                Auth::loginUsingId($user->id);
            }
            $order = new Order;

            if (Auth::check() == true) {
                $order->user_id = Auth::id();
            }

            $couponcode = null;

            if(Session::has('couponDiscountCode')){
                $couponcode = Session::get('couponDiscountCode');
            }    


            $notify_driver = 'mail';

            $order->getway_id = $gateway->id;
            $order->status_id = 3;
            $order->tax = str_replace(',', '', Cart::tax());
            $order->discount = $total_discount;
            $order->coupon_code = $couponcode;
            $order->total = $total_amount;
            $order->order_method = $order_method ?? 'delivery';
            $order->notify_driver = $notify_driver;
            $order->transaction_id = $paymentresult['payment_id'];
            $order->payment_status =$paymentresult['payment_status'];
            $order->risk_level = $paymentresult['risk_level'];
            $order->placed_at = Carbon::now()->setTimezone(config('app.timezone'));
            $order->save();

             // ✅ Save pickup details if in-person pickup selected
             if ($request->shipping_method === 'inperson_pickup') {
                \App\Models\Ordermeta::updateOrCreate(
                    ['order_id' => $order->id, 'key' => 'inperson_pickup_details'],
                    ['value' => json_encode($pickup_details)]
                );
            }
            
            $credit_card_processing_method = Option::where('key','credit_card_processing_method')->first();
            $credit_card_processing_method = $credit_card_processing_method ? $credit_card_processing_method->value : 'manual';

            if($credit_card_processing_method == 'auto' && $paymentresult['risk_level'] == 'normal'){
            
                     $payment_data['transaction_id'] =  $order->transaction_id;
                         
                        // dd($payment_data);
                         
                     $paymentresult= $gateway->namespace::capture_payment($payment_data);
                     
                if($paymentresult['payment_status'] == 1){
                   $order->payment_status =$paymentresult['payment_status'];
                   $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
                   $order->save(); 
                }
            } 


            if($couponcode != null){
                $coupon = Coupon::where('code',$couponcode)->first();
                $coupon->used_count =  $coupon->used_count + 1 ;
                $coupon->save();
            }


            $oder_items = [];
            $total_weight = 0;
            $priceids = [];
            $cartid = null;

            foreach (Cart::content() as $row) {

                $data['order_id'] = $order->id;
                $data['term_id'] = $row->id;
                $data['info'] = json_encode([
                    'sku' => $row->options->sku ?? '',
                    'options' => $row->options->options ?? []
                ]);

            //    if(isset($row->options->price_id)){
            //     array_push($priceids, ['order_id' => $order->id, 'price_id' => $row->options->price_id, 'qty' => $row->qty]);
            //    }

                if($row->options->options == []){
                        array_push($priceids, ['order_id' => $order->id, 'price_id' => $row->options->price_id[0], 'qty' => $row->qty]);
                }else{
                    foreach ($row->options->options as $optionVal) {
                        array_push($priceids, ['order_id' => $order->id, 'price_id' => $optionVal->id, 'qty' => (int)$row->qty]);
                    }
                }

                $data['qty'] = $row->qty;
                $data['amount'] = $row->price;
                $total_weight = $total_weight + $row->weight;
                array_push($oder_items, $data);
                $cartid = $row->instance;
            }

            $order->orderitems()->insert($oder_items);

            if ($request->order_method == 'table') {
                $order->ordertable()->attach($request->table);
            }
            if ($order_method == 'delivery') {
                $delivery_info['address'] = $request->shipping['address'].' '. $request->shipping['city'].', '.$request->shipping['state'].', '.$request->shipping['country'];
                $delivery_info['post_code'] = $request->shipping['post_code'];
                $delivery_info['shipping_method'] = $request->shipping_method;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['credit_card_fee'] = $credit_card_fee;
                $delivery_info['booster_platform_fee'] = $booster_platform_fee;
                $delivery_info['cover_fee'] = $cover_fee;

                $order->shipping()->create([
                    'location_id' => $request->location,
                    'shipping_price' => $shipping_price,
                    'lat' => $request->my_lat ?? null,
                    'long' => $request->my_long ?? null,
                    'weight' => $total_weight,
                    'info' => json_encode($delivery_info)
                ]);
            }

            if (!empty($request->name) || !empty($request->email) || !empty($request->phone) || !empty($request->comment)) {
                $customer_info['name'] = $request->name;
                $customer_info['email'] = $request->email;
                $customer_info['phone'] = $request->phone;
                $customer_info['wpuid'] = $request->wpuid??0;
                $customer_info['note'] = $request->comment ?? "";
                $customer_info['billing'] = $request->billing ?? "";
                $customer_info['shipping'] = $request->shipping ?? "";
                $customer_info['credit_card_fee'] = $credit_card_fee;
                $customer_info['booster_platform_fee'] = $booster_platform_fee;
                $customer_info['cover_fee'] = $cover_fee;

                \App\Models\Ordermeta::updateOrCreate(
                    ['order_id' => $order->id, 'key' => 'orderinfo'],
                    ['value' => json_encode($customer_info)]
                );

                $transcation_log = new Ordermeta;
                $transcation_log->order_id = $order->id;
                $transcation_log->key = 'transcation_log';
                $transcation_log->value = json_encode($paymentresult['transaction_log']);
                $transcation_log->save();
    
                $order->orderlasttrans()->create([
                    'key' => 'last_transcation_log',
                    'value' => json_encode($paymentresult['transaction_log'])
                ]);
            }

            if (count($priceids) != 0) {
                $order->orderstockitems()->insert($priceids);
            }


          
            DB::commit();
            
            $club_info = tenant_club_info();

           $name = explode(' ',$request->name);

            $contact_manager_data = array(
                'first_name' => $name[0],
                'last_name' => $name[1]??'',
                'user_id' =>  $request->wpuid ??0,
                'phone_number' => $request->phone,					
                'booster_name' => $name[0],
                'country' =>   $request->billing['country'],									
                'address_1' => $request->billing['address'],
                'address_2' =>  '',
                'city' => $request->billing['city'],
                'state' =>  $request->billing['state'],
                'zip' =>  $request->billing['post_code'],													
                'email' =>  $request->email,                   
                'booster_id' =>Tenant('club_id'),
                'booster_level_id' => 4,
                'customer_tag' => 'online store customer',
                'addedsource' => 'storetool',
            );	  

            $subtotal = 0;
            
            foreach ($order->orderitems ?? [] as $row){
                $subtotal = $subtotal + $row->amount*$row->qty;
            }

            $user_recipt = [
                'contact_mgr_data'=>$contact_manager_data,
                'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
                'receipt_title'=>$request->name,
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  $request->wpuid ??0,
                'amount'=>$order->total,
                'revenue'=>$revenue,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
                'order_total'=>$order->total,
                'order_subtotal'=>$subtotal,
            ];

           $recipt =  $this->send_order_recipt($user_recipt);

            \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
            \App\Lib\NotifyToUser::sendEmail($order, $request->email, 'user');

            $prices=Orderstock::where('order_id',$order->id)->whereHas('price')->with('price')->get();
            foreach ($prices as $key => $row) {
                $current_stock=$row->price->qty;
                $order_stock=$row->qty;

                if ($order_stock >= $current_stock) {
                    $new_stock=0;
                    $stock_status=0;
                }else{
                    $new_stock=$current_stock-$order_stock;
                    $stock_status=1;
                }
                $price_row=Price::find($row->price_id);
                if (!empty($price_row) && $price_row->stock_manage ) {
                    $price_row->qty=$new_stock;
                    $price_row->stock_status=$stock_status;
                    $price_row->save();
                }
               // array_push($deletable_ids,$row->id);
            }

            if(Session::has('cart') && $cartid != null){

                $this->syncFormData($cartid,$order->id);

                Cart::destroy($cartid);
            }

            $parts = parse_url($redirect_url);

            if (isset($parts['scheme'], $parts['host'], $parts['path'])) {
                $redirect_url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
            }

            if(Session::has('couponDiscount')){
                Session::forget('couponDiscount');
            }

            if(strpos($redirect_url, 'login-customizer') !== false){
               $redirect_url = str_replace('login-customizer', 'listing/'.tenant('id'), $redirect_url);
            }


            if($request->has('form_checkout')){
                
                return response("
    <script>
        window.opener.postMessage({
            type: 'ORDER_RESPONSE',
            success: 'Order placed successfully',
            invoice_id: '{$order->invoice_no}'
        }, '*');
        window.close();
    </script>
", 200)->header('Content-Type', 'text/html');

            }else{
                return redirect()->away($redirect_url . '/?tab=thankyou&club_id='.Tenant('club_id').'&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no);
            }

            //return redirect()->away($redirect_url . '/?tab=thankyou&club_id='.Tenant('club_id').'&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no);
        } catch (\Throwable $th) {
            DB::rollback();

        // dd($th); die;
          
            return redirect()->away($redirect_url . '/?type=error&message=Oops something wrong while saving order data');
        }
        return redirect()->away($redirect_url);

    }




    public function syncFormData($cartid, $orderId)
    {
        $productFormData = ProductForm::where('cart_id', $cartid)->get();

        if(isset($productFormData) && !empty($productFormData)){

            $server_output = [];
        
            foreach($productFormData as $form){
                $formData = [];
                $data = unserialize($form->form_data);
                $data['order_id'] = $orderId;


                $info = Term::find($form->product_id);
                $data['product_title'] = $info->title;

                // $formData[$form->form_id] = array(
                //     'data'=>serialize($data),
                //     'product_id'=>$form->product_id,
                //     'order_id'=>$orderId
                // );

                $formData['form_id'] = $form->form_id;
                $formData['data'] = serialize($data);
                $formData['product_id'] = $form->product_id;
                $formData['booostr_id'] = $form->club_id;
                $formData['order_id'] = $orderId;
                $formData['product_title'] =$info->title;
           
                

            $jsonData = json_encode($formData);
        
            $url = env("WP_CLUB_URL");
        
            $url = ($url != '') ? $url."wp-json/store-api/v1/sync-store-form-data/": "https://staging3.booostr.co/wp-json/store-api/v1/sync-store-form-data/";
    

            $ch = curl_init();
        
            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData))
            );
        
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
            $server_output[] = curl_exec($ch);
        
            curl_close($ch);

            ProductForm::find($form->id)->delete();

        }
            $form_res = '';
    
            return $server_output;
        }

    }
    



    private function send_order_recipt($data){


        $postData = json_encode($data);
        // dd($postData);die;

        $url = env("WP_API_URL");
        
        $url = ($url != '') ? $url.'/user-recipt' : "https://staging3.booostr.co/wp-json/store-api/v1/user-recipt";

        // dd($url);die;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);     
        curl_setopt($ch, CURLOPT_USERAGENT, 'Tantent store');   
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Encode data as URL-encoded 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); // Set content type header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);


        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }
    
    public function success()
    {
        $order = Order::find(Session::get('order_id'));
      
        $cartid = Session::get('cartid');
    
        if(Session::has('cart') && $cartid != null){
            $this->syncFormData($cartid, $order->id);
            Cart::destroy($cartid);
        }
        
        \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
    
        $orderMetaRow = \App\Models\Ordermeta::where('order_id', $order->id)
            ->where('key', 'orderinfo')
            ->first();
        
        $ordermeta = json_decode($orderMetaRow->value ?? '{}', true);
        
        if(!empty($ordermeta['email'])){
            \App\Lib\NotifyToUser::sendEmail($order, $ordermeta['email'], 'user');
        }
    }

    public function fail()
    {
        abort_if(!Session::has('order_id'),404);

        Session::forget('payment_info');
        Session::forget('fund_callback');
        Order::destroy(Session::get('order_id'));
        Session::forget('order_id');

        Session::flash('error','Payment Fail');
        return redirect('/checkout');


    }
    
    public function makepayment(Request $request)
    {
        abort_if(!Session::has('stripe_credentials'), 404);
        $credentials=Session::get('stripe_credentials');

        $stripe = Omnipay::create('Stripe');
        $token = $request->stripeToken;
        $gateway = $credentials['publishable_key'];
        $secret_key = $credentials['secret_key'];
        $main_amount = $credentials['amount'];

        $stripe->setApiKey($secret_key);

        if($token){
            $response = $stripe->purchase([
                'amount' => $main_amount,
                'currency' => $credentials['currency'],
                'token' => $token,
            ])->send();
        }


        if ($response->isSuccessful()) {
            $arr_body = $response->getData();
            $data['payment_id'] = $arr_body['id'];
            $data['payment_method'] = "stripe";
            $data['getway_id'] = $credentials['getway_id'];
            $data['payment_type'] = $credentials['payment_type'];

            $data['amount'] = $credentials['main_amount'];
            $data['charge'] = $credentials['charge'];
            $data['status'] = 1;
            $data['payment_status'] = 1;
            $data['is_fallback'] = $credentials['is_fallback'];
            Session::put('payment_info',$data);
            Session::forget('stripe_credentials');
            return redirect(Stripe::redirect_if_payment_success());
        }
        else{
            $data['payment_status'] = 0;
            Session::put('payment_info',$data);
           Session::forget('stripe_credentials');
           return redirect(Stripe::redirect_if_payment_faild());
        }
    }


    function get_stripe_paymentIntent(Request $request){

        if(Session::has('cartid')){
           $cartid =  Session::get('cartid');

           $total_amount = Cart::total();

           $cover_fee = 0;
           $credit_card_fee = credit_card_fee($total_amount);
    
           $booster_platform_fee = booster_club_chagre($total_amount);

           if($request->cover_fee_checkbox){
            $total_amount = $total_amount + $credit_card_fee + $booster_platform_fee;

            $credit_card_fee = credit_card_fee($total_amount);
    
            $booster_platform_fee = booster_club_chagre($total_amount);

            $total_amount = $total_amount + $credit_card_fee + $booster_platform_fee;

            $cover_fee =  $credit_card_fee + $booster_platform_fee;  
            
           }

           $total_application_fee = $credit_card_fee + $booster_platform_fee;

           $club_receives = $total_amount - $total_application_fee;

           $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();

           $gateway_data_info = json_decode($gateway->data);

           $booostr_stripe_account = $gateway_data_info->stripe_account_id;


           Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);

           $intent = Stripe::paymentIntent::create([
            'amount' => round($total_amount * 100), // Convert to cents
            'currency' => 'usd',
            'payment_method_types' => ['card_present', 'card'],
            'capture_method' => 'automatic',
            'application_fee_amount' => round($cover_fee * 100),
            'transfer_data' => [
                'destination' => $booostr_stripe_account, // Booostr's Stripe account
            ],
            'metadata' => [
                'credit_card_fee' => number_format($credit_card_fee, 2),
                'booster_platform_fee' => number_format($booster_platform_fee, 2),
                'total_fees' => number_format($total_application_fee, 2),
                'club_receives' => number_format($club_receives, 2),
            ],
        ]);

           return response()->json(['total_amount' => $total_amount, 'client_secret' => $intent->client_secret], 200);

        }else{
            return response()->json(['error' => 'Cart ID not found'], 404);
        }

    }


    public function thanks()
    {
        abort_if(!Session::has('invoice_no'),404);
        $orderno=Session::get('invoice_no');
        SEOMeta::setTitle($orderno.' - Thanks');
        return view(baseview('thanks'),compact('orderno'));
    }

    public function applyTax(Request $request)
    {
        $club_info = tenant_club_info();
        
        $address = explode(',',$club_info['address']);
        //$state = trim($address[count($address)-2]);
        $store_state = isset($address[count($address)-2])?trim($address[count($address)-2]):'';

        if($request->shipping_state == '' || $store_state != trim($request->shipping_state)){
            $tax = 0;
            Cart::setGlobalTax($tax);
        }else{
            Cart::setGlobalTax(0);
           $content = Cart::content();
            if ($content && $content->count()) {
                $content->each(function ($item, $key) {
                   // dump($item);
                   if($item->options->tax == 1){
                       $item->setTaxRate(getTaxRate());
                   }
                });
            }
        }

       $discount =  Session::has('couponDiscount') ? Session::get('couponDiscount')['onlydiscount'] : 0;

      $total_amount =  Cart::total() + $request->shipping_price;

       $credit_card_fee = credit_card_fee($total_amount);

       $booster_platform_fee = booster_club_chagre($total_amount);

       $total_amount = $total_amount;
       //$total_amount = $total_amount+$credit_card_fee + $booster_platform_fee;

       $productcartdata['cart_shipping_price'] = $request->shipping_price;
       $productcartdata['cart_subtotal'] = Cart::subtotal();
       $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total()+ $request->shipping_price;
        $productcartdata['cart_credit_card_fee'] = $credit_card_fee;
        $productcartdata['cart_booster_platform_fee'] = $booster_platform_fee;
        $productcartdata['cart_grand_total'] = $total_amount;

        return response()->json($productcartdata);
    }

}
