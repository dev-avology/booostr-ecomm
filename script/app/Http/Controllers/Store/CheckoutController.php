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
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Option;
use DB;
use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\Orderstock;
use Carbon\Carbon;

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
        return redirect()->to("//".$domain->domain.'/direct_checkout/'.$cartid.'/'.$redirect_url.'/?'.http_build_query($customer));

    }


    public function direct_checkout_to(Request $request,$cartid='',$redirect_url='/'){
       
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

        Session::put('customer_data',$customer);

       return redirect()->route('direct.checkout',['cartid'=>$cartid,'redirect_url'=>$redirect_url]);
    }

    public function direct_checkout(Request $request,$cartid='',$redirect_url='/')
    {

        if(Session::has('redirect_url')){
            $redirect_url=Session::get('redirect_url');
        }else{
            $redirect_url = str_replace('{slash}','/',$redirect_url);
            $redirect_url=!empty(base64_decode($redirect_url))?base64_decode($redirect_url):"/";
            Session::put('redirect_url',$redirect_url);
        }
        if(Session::has('cartid')){
            Session::put('cartid',$cartid);
        }

       if(Session::has('customer_data')){
        $customer = Session::get('customer_data');
       }   

        Cart::instance($cartid);
        //load cart in session
        Cart::checkout_restore($cartid);
        if(Cart::content()->isEmpty()){
            return redirect()->away($redirect_url.'/?type=error&message=Oops Your cart is empty');
        }
       
        $club_info = tenant_club_info();
        $address = explode(',',$club_info['address']);
        $store_state = trim($address[count($address)-2]);

        if($customer['state'] == '' || $store_state != trim($customer['state'])){
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
        }
        else{
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

        return view('store.checkout.checkout',compact('locations','states_data','getways','request','order_method','order_settings','invoice_data','page_data','pickup_order','pre_order','source_code','payment_data','shipping_methods','shipping_price','customer'));
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

       $subtotal = Cart::subtotal();

        $shipping_price = 0;
        $shipping_method_label = '';
        if ($request->order_method == 'delivery' && !empty($request->shipping_method)) {
           
           if($request->shipping_method == 'free_shipping'){
             $shipping_price = 0;
             $shipping_method_label = 'Free Shipping';
            }else{

            $shippingDetails= json_decode(Option::where('key','shipping_method')->first()->value,true);

            if($shippingDetails['method_type'] == 'per_item'){

                $shipping_price = $shippingDetails['base_pricing'] + Cart::count() * $shippingDetails['pricing'];

                $shipping_method_label = $shippingDetails['label'];

            }else if($shippingDetails['method_type'] == 'weight_based'){

                $shipping_price = $shippingDetails['base_pricing'] + Cart::weight() * $shippingDetails['pricing'];
                $shipping_method_label = $shippingDetails['label'];

            }else if($shippingDetails['method_type'] == 'flat_rate'){


             if(is_array($shippingDetails['pricing'])){
                 foreach($shippingDetails['pricing'] as $index){
    

                  $from = (float)$index['from']??0;
                  $to = (float) $index['to'] > 0 ?(float) $index['to']: PHP_INT_MAX;

                    if($subtotal > $from && $subtotal <= $to){
                        $shipping_price = (float)$index['price'];
                        $shipping_method_label = $shippingDetails['label'];
                    }
                 }
             }

            }

           }

        } else {
            $order_method = 'pickup';
        }

       $total_amount=str_replace(',','',Cart::total());
       $total_discount=str_replace(',','',Cart::discount());

       $total_amount =  $total_amount + $shipping_price;

       $credit_card_fee = credit_card_fee($total_amount);

       $booster_platform_fee = booster_club_chagre($total_amount);

       //$total_amount = $total_amount+$credit_card_fee + $booster_platform_fee;
       $total_amount = $total_amount;


       $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
       //Process Payment
        $gateway_data_info = json_decode($gateway->data);
        $payment_data['currency']   = $gateway->currency_name ?? 'USD';
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
      //  $paymentresult= ['payment_status'=>4,'payment_id'=>'sffsdf43534'];

        if($paymentresult['payment_status'] != 4){
            return redirect()->back()->with(["error"=>"Sorry, we couldnt charge your card, please try another card"]);
        }

        DB::beginTransaction();
        try {
            if (Auth::check() == false) {
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

            $notify_driver = 'mail';

            $order->getway_id = $gateway->id;
            $order->status_id = 3;
            $order->tax = str_replace(',', '', Cart::tax());
            $order->discount = $total_discount;
            $order->total = $total_amount;
            $order->order_method = $order_method ?? 'delivery';
            $order->notify_driver = $notify_driver;
            $order->transaction_id = $paymentresult['payment_id'];
            $order->payment_status =$paymentresult['payment_status'];
            $order->placed_at = Carbon::now()->setTimezone(config('app.timezone'));
            $order->save();

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

            //    dump($row);
            //    dump($row->options->price_id);
               if(isset($row->options->price_id)){
                array_push($priceids, ['order_id' => $order->id, 'price_id' => $row->options->price_id, 'qty' => $row->qty]);
               }

                // foreach ($row->options->price_id ?? [] as $key => $r) {

                //     array_push($priceids, ['order_id' => $order->id, 'price_id' => $r, 'qty' => $row->qty]);
                // }

               
                $data['qty'] = $row->qty;
                $data['amount'] = $row->price;
                $total_weight = $total_weight + $row->weight;
                array_push($oder_items, $data);
                $cartid = $row->instance;
            }

          //  dump($priceids);
           // dd($oder_items);

            $order->orderitems()->insert($oder_items);

            if ($request->order_method == 'table') {
                $order->ordertable()->attach($request->table);
            }
            if ($request->order_method == 'delivery') {
                $delivery_info['address'] = $request->shipping['address'].' '. $request->shipping['city'].', '.$request->shipping['state'].', '.$request->shipping['country'];
                $delivery_info['post_code'] = $request->shipping['post_code'];
                $delivery_info['shipping_method'] = $request->shipping_method;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['credit_card_fee'] = $credit_card_fee;
                $delivery_info['booster_platform_fee'] = $booster_platform_fee;

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

                $order->ordermeta()->create([
                    'key' => 'orderinfo',
                    'value' => json_encode($customer_info)
                ]);

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
                'contact_tags' => '',
            );	  



            $user_recipt = [
                'contact_mgr_data'=>$contact_manager_data,
                'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
                'receipt_title'=>$request->name,
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  $request->wpuid ??0,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
            ];

           $recipt =  $this->send_order_recipt($user_recipt);

            \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
            \App\Lib\NotifyToUser::sendEmail($order, $request->email, 'user');

            if(Session::has('cart') && $cartid != null){
                Cart::destroy($cartid);
            }

            $parts = parse_url($redirect_url);

            if (isset($parts['scheme'], $parts['host'], $parts['path'])) {
                $redirect_url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
            }


            return redirect()->away($redirect_url . '/?tab=thankyou&invoice_id='.$order->invoice_no.'&type=success&message=Thanks for your purchase. Your order number is ' . $order->invoice_no);
        } catch (\Throwable $th) {
            DB::rollback();

        //   dd($th);
          
            return redirect()->away($redirect_url . '/?type=error&message=Oops something wrong while saving order data');
        }
        return redirect()->away($redirect_url);

    }



    private function send_order_recipt($data){


        $postData = json_encode($data);

        $url = env("WP_API_URL");
        
        $url = ($url != '') ? $url.'/user-recipt' : "https://staging3.booostr.co/wp-json/store-api/v1/user-recipt";

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
        Cart::instance('default')->destroy();
        return \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
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
        $state = trim($address[count($address)-2]);

        if($request->shipping_state == '' || $state != trim($request->shipping_state)){
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
