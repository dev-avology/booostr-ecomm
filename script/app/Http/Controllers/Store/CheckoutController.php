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
        // echo "<pre>";print_r(Cart::content());echo"</pre>";
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
        //dd($locations->shippings);
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


        // if(Session::has('stripe_credentials')){
        //     $Info=Session::get('stripe_credentials');
        //     if (tenant() != null) {
        //       return view(baseview('payments/stripe'),compact('Info'));
        //     }
        //    return view('merchant.plan.payment.stripe',compact('Info'));
        // }

        // $publishable_key=$array['publishable_key'];
        // $secret_key=$array['secret_key'];
        // $currency=$array['currency'];
        // $email=$array['email'];
        // $amount=$array['amount'];
        // $totalAmount=$array['pay_amount'];
        // $name=$array['name'];
        // $billName=$array['billName'];
        // $test_mode=$array['test_mode'];
        // $data['publishable_key']=$publishable_key;
        // $data['secret_key']=$secret_key;
        // $data['payment_mode']='stripe';
        // $data['amount']=$totalAmount;
        // $data['test_mode']=$test_mode;
       
        // $data['charge']=$array['charge'];
        // $data['main_amount']=$array['amount'];
        // $data['getway_id']=$array['getway_id'];
        // $data['is_fallback']=$array['is_fallback'] ?? 0;
        // $data['payment_type']=$array['payment_type'] ?? '';
        // $data['currency']=$array['currency'];
        

        // Session::put('stripe_credentials',$data);

        return view('store.checkout.checkout',compact('locations','getways','request','order_method','order_settings','invoice_data','meta','page_data','pickup_order','pre_order','source_code'));
    }


    public function makeOrder(Request $request)
    {
       
        if (count(Cart::content()) == 0) {
            return redirect('/checkout');
        } 

       $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|max:20',
            'comment' => 'max:200',
            'payment_method' => 'required|max:50',
            
       ]);

       $order_method='delivery';

       $notify_driver='mail';

       $order_settings=get_option('order_settings',true);


        if ($request->order_method == 'table') {
            $validated = $request->validate([
              'table' => 'required|max:100',
            ]);
            $order_method=$request->order_method;
        }

        if ($request->pre_order == 1) {
            $validated = $request->validate([
              'date' => 'required|max:100',
              'time' => 'required|max:100',
              
            ]);
        }

       $shipping_price=0;
       if ($request->order_method == 'delivery') {
            $validated = $request->validate([
              'address' => 'required|max:250',
              'post_code' => 'max:20',
              'address' => 'required|max:250',
            ]);

            if ($order_settings->shipping_amount_type != 'distance') {
               $validated = $request->validate([
                  'shipping_method' => 'required|max:100',
                  
               ]);

               $shipping_method=Category::where('status',1)->where('type','shipping')->findorFail($request->shipping_method);
               $shipping_price=$shipping_method->slug;
               
            }
            else{
                $shipping_price=$request->shipping_fee ?? 0;
            }

           

           $order_method=$request->order_method;
       }
       else{
        $order_method='pickup';
       }

       if ($request->password && tenant('customer_modules') == 'on' && Auth::check() == false) {
            $validated = $request->validate([
               'password' => 'min:8|max:50',
               'email' => 'required|string|email|max:50|unique:users',
            ]);

            $user=new User;
            $user->name=$request->name;
            $user->email=$request->email;
            $user->role_id=4;
            $user->password=\Hash::make($request->password);
            $user->save();
            
            Auth::loginUsingId($user->id);

       } 

       $total_amount=str_replace(',','',Cart::total());
       $total_discount=str_replace(',','',Cart::discount());
       $total_amount=$total_amount+$shipping_price;

       $gateway=Getway::where('status',1)->findOrFail($request->payment_method);

        DB::beginTransaction();
        try { 

        $order=new Order;

        if (Auth::check() == true) {
              
              $order->user_id=Auth::id();
        }

        $notify_driver=$order_settings->order_method ?? 'mail';
        if ($notify_driver == 'fmc') {
          if (tenant('push_notification') != 'on') {
            $notify_driver='mail';
          }
        }

        $order->getway_id=$request->payment_method;
        $order->status_id=3;
        $order->tax=str_replace(',','',Cart::tax());
        $order->discount=$total_discount;
        $order->total=$total_amount;
        $order->order_method=$order_method ?? 'delivery';
        $order->notify_driver=$notify_driver;
        $order->save();

        $oder_items=[];
        $total_weight=0;
        $priceids=[];

        foreach(Cart::content() as $row){
            $data['order_id']=$order->id;
            $data['term_id']=$row->id;
            $data['info']=json_encode([
                'sku'=>$row->options->sku ?? '',
                'options'=>$row->options->options ?? []
            ]);

            foreach ($row->options->price_id ?? [] as $key => $r) {

                array_push($priceids,['order_id'=>$order->id,'price_id'=>$r,'qty'=>$row->qty]);
            }

            $data['qty']=$row->qty;
            $data['amount']=$row->price;
            $total_weight=$total_weight+$row->weight;
            array_push($oder_items,$data);
        }



        $order->orderitems()->insert($oder_items);
        if ($request->pre_order == 1) {
            $order->schedule()->create(['date'=>$request->date,'time'=>$request->time]);
        }

        if ($request->order_method == 'table') {
            $order->ordertable()->attach($request->table);
        }
        if ($request->order_method == 'delivery') {
            $delivery_info['address']=$request->address;
            $delivery_info['post_code']=$request->post_code;


            $order->shipping()->create([
                'location_id'=>$request->location,
                'shipping_id'=>$request->shipping_method,
                'shipping_price'=>$shipping_price,
                'lat'=>$request->my_lat ?? null,
                'long'=>$request->my_long ?? null,
                'weight'=>$total_weight,
                'info'=>json_encode($delivery_info)
            ]);
        }

        if (!empty($request->name) || !empty($request->email) || !empty($request->phone) || !empty($request->comment)) {
           $customer_info['name']=$request->name;
           $customer_info['email']=$request->email;
           $customer_info['phone']=$request->phone;
           $customer_info['note']=$request->comment;

           $order->ordermeta()->create([
            'key'=>'orderinfo',
            'value'=>json_encode($customer_info)
           ]);
        }

        
        if (count($priceids) != 0) {
            $order->orderstockitems()->insert($priceids);
        }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            
            $errors['errors']['error']='Opps something wrong';
            return response()->json($errors,401);
        }  

        Session::forget('fund_callback');
        Session::put('fund_callback',[
            'success_url' => '/order-success',
            'cancel_url' => '/order-fail'
        ]);

        Session::put('order_id',$order->id);

        $payment_data['currency']   = $gateway->currency_name ?? 'USD';
        $payment_data['email']      = $request->email;
        $payment_data['name']       = $request->name;
        $payment_data['phone']      = $request->phone;
        $payment_data['billName']   = 'Order No: '.$order->invoice_no;
        $payment_data['amount']     = $total_amount;
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['charge']     = $gateway->charge ?? 0;
        $payment_data['pay_amount'] =  str_replace(',','',number_format($total_amount*$gateway->rate+$gateway->charge ?? 0,2));
        $payment_data['getway_id']  = $gateway->id;
       
       

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }
       
        return $gateway->namespace::make_payment($payment_data);
        

    }

    public function success()
    {
        abort_if(!Session::has('payment_info') || !Session::has('order_id'),404);

        

        $payment_info=Session::get('payment_info');

        $order=Order::with('ordermeta')->findorFail(Session::get('order_id'));
        $order->transaction_id = $payment_info['payment_id'];
        
        $order->payment_status = $payment_info['payment_status'];
        $order->save();

        Session::forget('payment_info');
        Session::forget('order_id');
        Session::forget('fund_callback');
        Session::put('invoice_no',$order->invoice_no);
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

    

}
