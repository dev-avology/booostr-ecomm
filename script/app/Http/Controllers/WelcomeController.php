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
use Carbon\Carbon;

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
            

            if($order->order_from == 4 || $order->order_from == 5){
                $customer_tag = 'POS store customer';
                $country = $ordermeta->billing->country??'USA';
                $address = $ordermeta->billing->address??'Test Address Line 1';
                $city = $ordermeta->billing->city??'Alameda';
                $state = $ordermeta->billing->state??'California';
                $post_code = $ordermeta->billing->post_code??'94501';
                $donor_name = isset($ordermeta->name)? $ordermeta->name.' (POS Order)':'Guest User'.' (POS Order)';
            }else{
                $customer_tag = 'online store customer';

                $country = $ordermeta->billing->country??'';
                $address = $ordermeta->billing->address??'';
                $city = $ordermeta->billing->city??'';
                $state = $ordermeta->billing->state??'';
                $post_code = $ordermeta->billing->post_code??'';
                $donor_name = isset($ordermeta->name)? $ordermeta->name.' (Online Order)':'Guest User'.' (Online Order)';

            }

            if(!empty($ordermeta)){                
            $name = !empty($ordermeta->name) ? explode(' ',$ordermeta->name) : '' ;
           

            $contact_manager_data = array(
                'first_name' => $name[0]??'',
                'last_name' => $name[1]??'',
                'user_id' =>  !empty($ordermeta->wpuid) ? (int)$ordermeta->wpuid:0,
                'phone_number' => $ordermeta->phone??'',					
                'booster_name' => $name[0]??'',
                'country' =>   $country,									
                'address_1' => $address,
                'address_2' =>  '',
                'city' => $city,
                'state' => $state,
                'zip' =>  $post_code,												
                'email' =>  $ordermeta->email??'',                   
                'booster_id' =>Tenant('club_id'),
                'booster_level_id' => 4,
                'customer_tag' => $customer_tag,
            );	 
            $user_recipt = [
                'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
                'receipt_title'=>$ordermeta->name,
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  !empty($ordermeta->wpuid) ? (int)$ordermeta->wpuid:0,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
                'order_total'=>$order->total,
            ];



            $order_date = Carbon::parse($order->created_at)->format('Y-m-d');
            $qty = $order->orderitems[0]['qty'];
            $product_amount = $order->orderitems[0]['amount'];
            $sub_total = $product_amount*$qty;
            $sales_tax = $order->tax;
            $order_total = $order->total;
        
            if(isset($order->ordermeta)){
                $ordermeta=json_decode($order->ordermeta->value ?? '',true);
            }
    
            $gateway=Getway::find($order->getway_id);
    
    
            //$jsonString = $order->shippingwithinfo['info'];
    
            $jsonString = $order->shippingwithinfo['info'];
            // Decode the JSON string into a PHP array
            $shipping_data = json_decode($jsonString, true);
    
            $credit_card_fee = $shipping_data['credit_card_fee'];
            $booster_platform_fee = $shipping_data['booster_platform_fee'];
            $processing_fees = $credit_card_fee+$booster_platform_fee;
    
            $net_recieved_amount = $order_total-($sales_tax+$processing_fees);
    
            $shipped_and_fullfilldate = Carbon::parse($order->updated_at)->format('Y-m-d');

            $postData = json_encode(['contact_mgr_data'=>$contact_manager_data,
            'user_recipt'=>$user_recipt,
            'category_type'=> 'Booostr Ecommerce',
            'booster_id' =>Tenant('club_id'),
            'coaid'=>41,
            'contactname'=>$ordermeta['name'],
            //'memo'=>'Booostr Ecommerce',
            'user_id' =>  $ordermeta['wpuid']??0,
            'revenue_name'=>'4-850 Booostr Ecommerce',
            'transaction_type'=>'I',
            'sales_tax_collected' => $sales_tax > 0 ? 'Yes':'No',
            'net_revenue'=>$net_recieved_amount,
            'transaction_amount'=>$order_total,
            'expense_category'=>'Revenue',
            'receipts_issued'=> 'Yes',
            'status'=>1,
            'donor_name'=>$donor_name,
            'created'=>$order->placed_at,
            'modified'=>Carbon::now()->setTimezone(config('app.timezone')),
            'payement_method'=>($gateway->name == 'cash') ? 0 : 3,
            'invoicenumber'=>$order->invoice_no,
            'invoicreatedate'=>$order->placed_at,
            'invoiceprocessingfee'=>$processing_fees,
            'invoicesalestax'=> $sales_tax,
            'invoiceopt'=>$order->invoice_no,
            'deposite_date'=>$order->captured_at,
            'transfer_refund_date'=> ($order->refunded_at != null) ? $order->refunded_at : null,
            'record_type' => ($order->refunded_at != null) ? 'refund' : 'capture',
          ]);



            $url = env("WP_API_URL");

            // $url = ($url != '') ? $url.'/financial-manager-pos' : "https://staging3.booostr.co/wp-json/store-api/v1/financial-manager-pos";
             $url = ($url != '') ? $url.'/user-recipt-sync' : "https://staging3.booostr.co/wp-json/store-api/v1/user-recipt-sync";
         
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
            
            dump("======Order Metadata=======");
            dump($response);
            dump($order);
           dump($user_recipt);
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
