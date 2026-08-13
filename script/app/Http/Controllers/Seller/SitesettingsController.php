<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use Auth;
use Illuminate\Support\str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Storage;
class SitesettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       abort_if(!getpermission('website_settings'),401);
        return view('seller.settings.sitesettings');
    }

    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
      
       abort_if(!getpermission('website_settings'),401);

       if ($slug == 'general') {
         
         $tenantInfo = tenant_club_pro_live_info();
         $senderEmail = Option::where('key', 'store_sender_email')->first();
         
         $club_address=Option::where('key','invoice_data')->first();

         $decode_address=json_decode($club_address->value);

         $address['store_legal_name'] = $decode_address->store_legal_name ?? '';
         $address['store_legal_phone'] = $decode_address->store_legal_phone ?? '';
         $address['store_legal_house'] = $decode_address->store_legal_house ?? '';
         $address['store_legal_address'] = $decode_address->store_legal_address ?? '';

         $address['store_legal_city'] = $decode_address->store_legal_city ?? '';
         $address['country'] = $decode_address->country ?? '';
         $address['state'] = $decode_address->state ?? '';
         $address['post_code'] = $decode_address->post_code;
         $address['store_legal_email'] = $decode_address->store_legal_email ?? '';


         if (!empty($tenantInfo['club_email']) && ($tenantInfo['club_email'] !== $senderEmail->value || $tenantInfo['club_email'] !== $address['store_legal_email'] )) {
            $address['store_legal_email'] = $tenantInfo['club_email'];

             $senderEmail->update(['value' => $tenantInfo['club_email']]);


             $club_address->value=json_encode($address);
             $club_address->save();
             TenantCacheClear('invoice_data');

         }
         
         $club_info = tenant_club_info();

         $lat_lang = explode(',',$club_info['lat_lang']);
         $address = [];

         $club_address=Option::where('key','invoice_data')->first();

         $decode_address=json_decode($club_address->value);

         $address['store_legal_name'] = $decode_address->store_legal_name ?? '';
         $address['store_legal_phone'] = $decode_address->store_legal_phone ?? '';
         $address['store_legal_house'] = $decode_address->store_legal_house ?? '';
         $address['store_legal_address'] = $decode_address->store_legal_address ?? '';

         $address['store_legal_city'] = $decode_address->store_legal_city ?? '';
         $address['country'] = $decode_address->country ?? '';
         $address['state'] = $decode_address->state ?? '';
         $address['post_code'] = $decode_address->post_code;
         $address['store_legal_email'] = $decode_address->store_legal_email ?? '';

         // $store_state = trim($address[count($address)-2]);
         // $store_country = trim($address[count($address)-1]);
      //   $phone_number = $club_info['phone_number'];

           $languages=Option::where('key','languages')->first();
           $languages=json_decode($languages->value ?? '');


           $senderEmail = Option::where('key', 'store_sender_email')->first();
           $store_sender_email= $senderEmail->value??'';
           $store_name = $club_info['club_name'];
           
           $invoice_data=Option::where('key','invoice_data')->first();
           $invoice_data=json_decode($invoice_data->value ?? '');
           // Sync Time zone from Booostr club-timezone API (club_id dynamic per tenant).
           $timezone = $this->syncClubTimezoneFromWp();
           $default_language=Option::where('key','default_language')->first();
           $weight_type=Option::where('key','weight_type')->first();
           $measurment_type=Option::where('key','measurment_type')->first();

           $currency_info=Option::where('key','currency_data')->first();
           $currency_info=json_decode($currency_info->value ?? '');

           $average_times=Option::where('key','average_times')->first();
           $average_times=json_decode($average_times->value ?? '');

           $order_method=Option::where('key','order_method')->first();
           $order_method=$order_method->value ?? '';

           $order_settings=Option::where('key','order_settings')->first();
           $order_settings=json_decode($order_settings->value ?? ''); 

           $whatsapp_no=Option::where('key','whatsapp_no')->first();
           
           $whatsapp_settings=Option::where('key','whatsapp_settings')->first();
           $whatsapp_settings=json_decode($whatsapp_settings->value ?? '');

           $shipping_method=Option::where('key','shipping_method')->first();

           $banner_logo=Option::where('key','banner_logo')->first();
         //   $banner_button_text=Option::where('key','banner_button_text')->first();
           //$banner_title=Option::where('key','banner_title')->first();
          // $banner_button_url=Option::where('key','banner_button_url')->first();
          // $shipping_method=$shipping_method ?? '';

          $bannerUrls=Option::where('key','banner_url')->first();
          $bannerUrlValue= $bannerUrls->value ?? '';

          $tax=Option::where('key','tax')->first();

          $tax =  $tax ? $tax->value: 0.00; 

          $free_shipping=Option::where('key','free_shipping')->first() ;
          $free_shipping = $free_shipping ? $free_shipping->value : 0;

          $min_cart_total=Option::where('key','min_cart_total')->first();
          $min_cart_total = $min_cart_total ? $min_cart_total->value : 0.00;

          $credit_card_processing_method=Option::where('key','credit_card_processing_method')->first();
          $credit_card_processing_method = $credit_card_processing_method ? $credit_card_processing_method->value : 'manual';

          $inperson_pickup = Option::where('key', 'inperson_pickup_details')->first();
          $pickup_details = $inperson_pickup ? json_decode($inperson_pickup->value) : (object)[];
            
            // Allow in person pickup ka simple flag bhi (toggle ke liye)
           $allow_in_person_pickup = $pickup_details->enabled ?? 0;
            

            Option::where('key', 'allow_in_person_pickup')->delete();
            
           return view('seller.settings.general',compact('languages','lat_lang','address','store_name','measurment_type','tax','free_shipping','min_cart_total','shipping_method','store_sender_email','invoice_data','timezone','default_language','weight_type','currency_info','average_times','order_method','order_settings','whatsapp_no','whatsapp_settings',
           'banner_logo','bannerUrlValue','credit_card_processing_method',
           'inperson_pickup', 'pickup_details', 'allow_in_person_pickup'));
       }
      
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($id == 'general') {
           $validated = $request->validate([
             //   'store_name' => 'required|max:100',
            //    'store_sender_email' => 'required|email|max:50',
            //    'latitude' => 'required|max:50',
            //    'longitude' => 'required|max:50',
            //    'logo' => 'mimes:png|max:200',
           //     'favicon' => 'mimes:ico|max:50',
            ///    'notification_icon' => 'mimes:png|max:100',
              //  'banner' => 'mimes:png|max:200',
              // 'store_legal_name' => 'required|max:50',
              // 'store_legal_phone' => 'required|max:20',
              // 'store_legal_email' => 'required|email|max:50',
             //  'store_legal_address' => 'required|max:50',
               // 'store_legal_house' => 'required|max:50',
             //  'store_legal_city' => 'required|max:30',
             //  'country' => 'required|max:100',
              // 'state' => 'required|max:100',
            //   'post_code' => 'required|max:50',
              //  'timezone' => 'required|max:50',
               // 'default_language' => 'required|max:50',
               // 'weight_type' => 'required|max:50', 
           ]);

         //   $tenant=Tenant();
         //   $tenant->store_name=$request->store_name;
         //   $tenant->lat=$request->latitude;
         //   $tenant->long=$request->longitude;
         //   $tenant->save();

           $path = 'uploads/'.tenant('uid');

         //   $store_sender_email=Option::where('key','store_sender_email')->first();
         //   if (empty($store_sender_email)) {
         //      $store_sender_email=new Option;
         //      $store_sender_email->key='store_sender_email';
         //      $store_sender_email->autoload=1;
         //   }
         //   $store_sender_email->value=$request->store_sender_email;
         //   $store_sender_email->save();

         //   TenantCacheClear('store_sender_email');

         //   if ($request->hasFile('logo')) {
         //    $logo      = $request->file('logo');
         //    $logo->move($path, 'logo.png');
         //   }

         //   if ($request->hasFile('favicon')) {
         //    $favicon      = $request->file('favicon');
         //    $favicon->move($path, 'favicon.ico');
         //   }

         //   if ($request->hasFile('notification_icon')) {
         //    $notification_icon      = $request->file('notification_icon');
         //    $notification_icon->move($path, 'notification_icon.png');
         //   }

         //   if ($request->hasFile('banner')) {
         //    $banner      = $request->file('banner');
         //    $banner->move($path, 'banner.png');
         //   }

        $pickup = [
            'enabled'              => $request->allow_in_person_pickup ?? 0,
            'use_store_address'    => $request->use_store_address_for_pickup ?? 'yes',
            'address_line1'        => $request->pickup_line1 ?? '',
            'address_line2'        => $request->pickup_line2 ?? '',
            'city'                 => $request->pickup_city ?? '',
            'state'                => $request->pickup_state ?? '',
            'zip'                  => $request->pickup_zip ?? '',
            'instructions'         => $request->pickup_instructions ?? '',
            // phone optional tha client image mein nahi dikha, to abhi skip kiya (chahiye to add kar lena)
        ];
        
        Option::updateOrCreate(
            ['key' => 'inperson_pickup_details'],
            ['value' => json_encode($pickup), 'autoload' => 1]
        );
        
        TenantCacheClear('inperson_pickup_details');
            
            
         if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $filename = $banner->getClientOriginalName();

            $filename = str_replace(' ', '-', $filename);
            $banner->move($path, $filename);

            $banner_logo=Option::where('key','banner_logo')->first();
            if (!empty($banner_logo)) {
               Option::where('key', 'banner_logo')->update(['value' => env('APP_URL').'/'.$path.'/'.$filename]);
            }else{
               $bannerLogo=new Option;
               $bannerLogo->key='banner_logo';
               $bannerLogo->value=env('APP_URL').'/'.$path.'/'.$filename;
               $bannerLogo->autoload=1;
               $bannerLogo->save();
            }
           }

          $club_decode_json = json_decode(tenant()->club_info);
          $storeName = Str::slug($club_decode_json->club_name, '-');
          $newClubUrl = env('WP_CLUB_URL');

          $bannerUrl=Option::where('key','banner_url')->first();
          if($bannerUrl){
            if($request->manage_banner){
               if($request->manage_banner == 'product' && (!empty($request->banner_type))){
                 $productId = $request->banner_type; 
                 $pUrl = $newClubUrl . 'all-booster-clubs/listing/'.$storeName.'?tab=product_detail&product_id=' . $productId . '&store_name=' . $storeName;

                 Option::where('key', 'banner_url')->update(['value' => $pUrl]);
  
               }elseif($request->manage_banner=='category' && (!empty($request->banner_type))){
  
                 $categoryName = Str::slug($request->banner_type, '-');
  
                 $catUrl = $newClubUrl . 'all-booster-clubs/listing/'.$storeName.'?tab=store&category=' . $categoryName;

                 Option::where('key', 'banner_url')->update(['value' => $catUrl]);
  
               }elseif($request->manage_banner=='custom' && (!empty($request->custom_url))){
                  Option::where('key', 'banner_url')->update(['value' => $request->custom_url]);
               }
            }
          }else{

            if($request->manage_banner){
               if($request->manage_banner == 'product' && (!empty($request->banner_type))){
                 $productId = $request->banner_type; 
                 $pUrl = $newClubUrl . 'all-booster-clubs/listing/'.$storeName.'?tab=product_detail&product_id=' . $productId . '&store_name=' . $storeName;
  
                 $productBanner=new Option;
                 $productBanner->key='banner_url';
                 $productBanner->value= $pUrl;
                 $productBanner->autoload=1;
                 $productBanner->save();
  
               }elseif($request->manage_banner=='category' && (!empty($request->banner_type))){
  
                 $categoryName = Str::slug($request->banner_type, '-'); 
  
                 $catUrl = $newClubUrl . 'all-booster-clubs/listing/'.$storeName.'?tab=store&category=' . $categoryName;
  
                 $categoryBanner=new Option;
                 $categoryBanner->key='banner_url';
                 $categoryBanner->value= $catUrl;
                 $categoryBanner->autoload=1;
                 $categoryBanner->save();
  
               }elseif($request->manage_banner=='custom' && (!empty($request->custom_url))){
                 $categoryBanner=new Option;
                 $categoryBanner->key='banner_url';
                 $categoryBanner->value= $request->custom_url;
                 $categoryBanner->autoload=1;
                 $categoryBanner->save();
               }
           }
          }

         //   if($request->banner_button_text){
         //    $bannerTitle=new Option;
         //    $bannerTitle->key='banner_button_text';
         //    $bannerTitle->value=$request->banner_button_text;
         //    $bannerTitle->autoload=1;
         //    $bannerTitle->save();
         //   }

         //   if($request->banner_button_url){
         //    $bannerTitle=new Option;
         //    $bannerTitle->key='banner_button_url';
         //    $bannerTitle->value=$request->banner_button_url;
         //    $bannerTitle->autoload=1;
         //    $bannerTitle->save();
         //   }



           $invoice_info['store_legal_name']=$request->store_legal_name ?? '';
           $invoice_info['store_legal_phone']=$request->store_legal_phone ?? '';
           $invoice_info['store_legal_address']=$request->store_legal_address ?? '';
           $invoice_info['store_legal_house']=$request->store_legal_house ?? '';
           $invoice_info['store_legal_city']=$request->store_legal_city ?? '';
           $invoice_info['country']=$request->country ?? '';
           $invoice_info['state']=$request->state ?? '';
           $invoice_info['post_code']=$request->post_code ?? '';
           $invoice_info['store_legal_email']=$request->store_legal_email ?? '';
           
           $invoice_data=Option::where('key','invoice_data')->first();
           if (empty($invoice_data)) {
              $invoice_data=new Option;
              $invoice_data->key='invoice_data';
              
           }
           $invoice_data->value=json_encode($invoice_info);
           $invoice_data->save();
           TenantCacheClear('invoice_data');


         //   $timezone=Option::where('key','timezone')->first();
         //   if (empty($timezone)) {
         //      $timezone=new Option;
         //      $timezone->key='timezone';
         //      $timezone->autoload=1;
         //   }
         //   $timezone->value=$request->timezone;
         //   $timezone->save();



         //   $default_language=Option::where('key','default_language')->first();
         //   if (empty($default_language)) {
         //      $default_language=new Option;
         //      $default_language->key='default_language';
         //      $default_language->autoload=1;
         //   }
         //   $default_language->value=$request->default_language ?? 'en';
         //   $default_language->save();

           

         //   $weight_type=Option::where('key','weight_type')->first();
         //   if (empty($weight_type)) {
         //      $weight_type=new Option;
         //      $weight_type->key='weight_type';
         //      $weight_type->autoload=1;
         //   }
         //   $weight_type->value=$request->weight_type;
         //   $weight_type->save();

           $measurment_type=Option::where('key','measurment_type')->first();
           if (empty($measurment_type)) {
              $measurment_type=new Option;
              $measurment_type->key='measurment_type';
              $measurment_type->autoload=1;
           }
           $measurment_type->value=$request->measurment_type;
           $measurment_type->save();

           $order_method=Option::where('key','order_method')->first();
           if (empty($order_method)) {
              $order_method=new Option;
              $order_method->key='order_method';
           }
           $order_method->value=$request->order_method;
           $order_method->save();

           $whatsapp_no=Option::where('key','whatsapp_no')->first();
           if (empty($whatsapp_no)) {
              $whatsapp_no=new Option;
              $whatsapp_no->key='whatsapp_no';
              $whatsapp_no->autoload=1;
           }
           $whatsapp_no->value=$request->whatsapp_no??'';
           $whatsapp_no->save();

           
          

           $currency_info=Option::where('key','currency_data')->first();
           if (empty($currency_info)) {
              $currency_info=new Option;
              $currency_info->key='currency_data';
              $currency_info->autoload=1;
           }
           $currency_info->value=json_encode(array(
                'currency_name'=>$request->currency_name??'USD',
                'currency_position'=>$request->currency_position??'left',
                'currency_icon'=>$request->currency_icon??'$'
           ));

           $currency_info->save();

           

           $average_times=Option::where('key','average_times')->first();
           if (empty($average_times)) {
              $average_times=new Option;
              $average_times->key='average_times';
              $average_times->autoload=1;
           }
           $average_times->value=json_encode(array(
                'delivery_time'=>$request->delivery_time,
                'pickup_time'=>$request->pickup_time
           ));
           $average_times->save();

           $order_settings=Option::where('key','order_settings')->first();
           if (empty($order_settings)) {
              $order_settings=new Option;
              $order_settings->key='order_settings';
             
           }
           $order_settings->value=json_encode(array(
                'order_method'=>$request->order_method,
                'shipping_amount_type'=>$request->shipping_amount_type,
                'google_api'=>$request->google_api,
                'google_api_range'=>$request->google_api_range,
                'delivery_fee'=>$request->delivery_fee,
                'pickup_order'=>$request->pickup_order,
                'pre_order'=>$request->pre_order,
                'source_code'=> $request->source_code

           ));
           $order_settings->save();

           $whatsapp_settings=Option::where('key','whatsapp_settings')->first();
           if (empty($whatsapp_settings)) {
              $whatsapp_settings=new Option;
              $whatsapp_settings->key='whatsapp_settings';
              $whatsapp_settings->autoload=1;
             
           }
           $whatsapp_settings->value=json_encode(array(
                'whatsapp_no'=>$request->whatsapp_no,
                'shop_page_pretext'=>$request->shop_page_pretext,
                'other_page_pretext'=>$request->other_page_pretext,
                'whatsapp_status'=>$request->whatsapp_status,

           ));
           $whatsapp_settings->save();



           $tax_data=Option::where('key','tax')->first();
           // $shipping_method=$shipping_method ?? '';
            if (empty($tax_data)) {
                  $tax_data=new Option;
                  $tax_data->key='tax';
               }
         

               $tax_data->value=$request->tax??0.00;
            $tax_data->save();


           $shipping_price = array(
            'weight_based'=> 'perlb',
            'per_item'=> 'per_item',
            'flat_rate'=> 'flatrate_range',
           );

           $shipping_method=Option::where('key','shipping_method')->first();
           // $shipping_method=$shipping_method ?? '';
            if (empty($shipping_method)) {
                  $shipping_method=new Option;
                  $shipping_method->key='shipping_method';
               }
         
               if($request->shipping_method != ''){

               $shipping_method->value=json_encode(array(
               'method_type'=>$request->shipping_method,
               'label'=>$request->shipping_method_label??'',
               'pricing'=>$request->type_price["'".$shipping_price[$request->shipping_method]."'"],
               'base_pricing'=>$request->base_price["'".$shipping_price[$request->shipping_method]."'"]??0,

                  ));
               }else{
                  Option::where('key','shipping_method')->delete();
               }

            $shipping_method->save();


            
           $free_shipping=Option::where('key','free_shipping')->first() ;
           if (empty($free_shipping)) {
               $free_shipping=new Option;
               $free_shipping->key='free_shipping';
            }
            $free_shipping->value = $request->free_shipping;
            $free_shipping->save();
 
           $min_cart_total=Option::where('key','min_cart_total')->first();
           if (empty($min_cart_total)) {
            $min_cart_total=new Option;
            $min_cart_total->key='min_cart_total';
         }
         $min_cart_total->value = $request->min_cart_total;
         $min_cart_total->save();

         $credit_card_processing_method=Option::where('key','credit_card_processing_method')->first();
         if (empty($credit_card_processing_method)) {
         $credit_card_processing_method=new Option;
         $credit_card_processing_method->key='credit_card_processing_method';
         }
         $credit_card_processing_method->value = $request->credit_card_processing_method;
         $credit_card_processing_method->save();

          
         TenantCacheClear('tax');
         TenantCacheClear('shipping_method');
         TenantCacheClear('free_shipping');
         TenantCacheClear('min_cart_total');
         TenantCacheClear('whatsapp_settings');
         TenantCacheClear('average_times');
         TenantCacheClear('invoice_data');
         TenantCacheClear('autoload');
         TenantCacheClear('order_settings');
         TenantCacheClear('measurment_type');
         TenantCacheClear('credit_card_processing_method');
         bust_pos_api_cache();
           

           return response()->json('General Settings');
        }
        

    }

    /**
     * Fetch club timezone from WordPress and save/show it on general settings.
     * URL: https://app.booostr.co/wp-json/booostr/v1/club-timezone/{club_id}
     * Abbreviations (e.g. PST) are mapped to IANA zones so app.timezone stays valid.
     */
    private function syncClubTimezoneFromWp(): ?Option
    {
        $existing = Option::where('key', 'timezone')->first();
        $clubId = Tenant('club_id');

        if (empty($clubId)) {
            return $existing;
        }

        try {
            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->get('https://app.booostr.co/wp-json/booostr/v1/club-timezone/' . $clubId);

            if (!$response->successful()) {
                return $existing;
            }

            $apiTimezone = trim((string) ($response->json('timezone') ?? ''));
            // Blank API timezone (e.g. club 47254) → default America/Los_Angeles.
            $resolvedTimezone = $apiTimezone === ''
                ? 'America/Los_Angeles'
                : $this->resolveClubTimezoneValue($apiTimezone);

            if ($resolvedTimezone === '') {
                $resolvedTimezone = 'America/Los_Angeles';
            }

            if (empty($existing)) {
                $existing = new Option();
                $existing->key = 'timezone';
                $existing->autoload = 1;
            }

            if ((string) $existing->value !== $resolvedTimezone) {
                $existing->value = $resolvedTimezone;
                $existing->save();
                TenantCacheClear('timezone');
                TenantCacheClear('autoload');
            }

            return $existing;
        } catch (\Throwable $e) {
            Log::warning('Club timezone sync failed', [
                'club_id' => $clubId,
                'error' => $e->getMessage(),
            ]);

            return $existing;
        }
    }

    /**
     * Map WP club timezone abbreviations to PHP-valid IANA zones used in the select.
     * If value is already a known timezone identifier, keep it as-is.
     */
    private function resolveClubTimezoneValue(string $timezone): string
    {
        $timezone = trim($timezone);
        if ($timezone === '') {
            return '';
        }

        $abbrMap = [
            'UTC' => 'UTC',
            'GMT' => 'UTC',
            'EST' => 'America/New_York',
            'EDT' => 'America/New_York',
            'CST' => 'America/Chicago',
            'CDT' => 'America/Chicago',
            'MST' => 'America/Denver',
            'MDT' => 'America/Denver',
            'PST' => 'America/Los_Angeles',
            'PDT' => 'America/Los_Angeles',
            'AKST' => 'America/Anchorage',
            'AKDT' => 'America/Anchorage',
            'HST' => 'Pacific/Honolulu',
            'HAST' => 'Pacific/Honolulu',
            'HADT' => 'Pacific/Honolulu',
        ];

        $upper = strtoupper($timezone);
        if (isset($abbrMap[$upper])) {
            return $abbrMap[$upper];
        }

        // Already an IANA / PHP timezone id (e.g. America/Los_Angeles).
        try {
            new \DateTimeZone($timezone);
            return $timezone;
        } catch (\Throwable $e) {
            return '';
        }
    }

}
