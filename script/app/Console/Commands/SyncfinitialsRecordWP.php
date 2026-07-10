<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Order;
use Carbon\Carbon;
use App\Models\Getway;

class SyncfinitialsRecordWP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:sync_finitials_record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill user receipt and financial manager sync for captured store orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        tenancy()->runForMultiple(null, function ($tenant) {
            $this->syncContactData();
        });  

        return 0;
    }


    public function syncContactData(){
        $orders=Order::with('user','ordermeta','orderitems','orderstatus','shippingwithinfo')->withCount('orderitems');
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
                $receipt_title = isset($ordermeta->name)? $ordermeta->name:'Guest User';
            }else{
                $customer_tag = 'online store customer';

                $country = $ordermeta->billing->country??'';
                $address = $ordermeta->billing->address??'';
                $city = $ordermeta->billing->city??'';
                $state = $ordermeta->billing->state??'';
                $post_code = $ordermeta->billing->post_code??'';
                $donor_name = isset($ordermeta->name)? $ordermeta->name.' (Online Order)':'Guest User'.' (Online Order)';
                $receipt_title = isset($ordermeta->name)? $ordermeta->name:'Guest User';

            }

            if(!empty($ordermeta)){                
                $name = !empty($ordermeta->name) ? explode(' ',$ordermeta->name) : '' ;
            }else{
              $name = '' ;
            }

            $item_count = $order->orderitems->count();

            if($item_count > 0){

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
                'email' =>  !empty($ordermeta->email) ? $ordermeta->email:'',                   
                'booster_id' =>Tenant('club_id'),
                'booster_level_id' => 4,
                'customer_tag' => $customer_tag,
            );
            
            $subtotal = 0;
            
            foreach ($order->orderitems ?? [] as $row){
                $subtotal = $subtotal + $row->amount*$row->qty;
            }

            $user_recipt = [
                'receipts_date'=>$order->placed_at,
                'receipt_title'=>$receipt_title,
                'receipent_org'=>$club_info['club_name'].' Store',
                'category'=>'ecommerce',
                'user_id' =>  !empty($ordermeta->wpuid) ? (int)$ordermeta->wpuid:0,
                'club_id' =>Tenant('club_id'),
                'recurring'=>'one-time',
                'camp_id'=>$order->invoice_no,
                'order_total'=>$order->total,
                'order_subtotal'=>$subtotal,
            ];



            $order_date = Carbon::parse($order->created_at)->format('Y-m-d');
           
            $sales_tax = $order->tax;
            $order_total = $order->total;
        
            if(isset($order->ordermeta)){
                $ordermeta=json_decode($order->ordermeta->value ?? '',true);
            }
    
            $gateway=Getway::find($order->getway_id);
    
            if($gateway && $gateway->name == 'cash'){
                $gateway_name = 0;
            }else{
                $gateway_name = 3;
            }

            
    
            //$jsonString = $order->shippingwithinfo['info'];
    
            // Null-safe: some orders have no shipping row; do not crash the whole sync.
            $jsonString = $order->shippingwithinfo?->info;
            $shipping_data = json_decode($jsonString ?? '{}', true);
            if (!is_array($shipping_data)) {
                $shipping_data = [];
            }
    
            $credit_card_fee = $shipping_data['credit_card_fee'] ?? 0;
            $booster_platform_fee = $shipping_data['booster_platform_fee'] ?? 0;
            $processing_fees = $credit_card_fee+$booster_platform_fee;
    
            $net_recieved_amount = $order_total-($sales_tax+$processing_fees);
    
            $shipped_and_fullfilldate = Carbon::parse($order->updated_at)->format('Y-m-d');

            if(is_order_syncable_to_financial_manager($order)){

                $fpostData = ['category_type'=> 'Booostr Ecommerce',
                'booster_id' =>Tenant('club_id'),
                'coaid'=>41,
                'contactname'=>isset($ordermeta['name']) ? $ordermeta['name'] : '',
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
                'modified'=>Carbon::now()->setTimezone(config('app.timezone'))->toDateTimeString(),
                'payement_method'=> $gateway_name,
                'invoicenumber'=>$order->invoice_no,
                'invoicreatedate'=>$order->placed_at,
                'invoiceprocessingfee'=>$processing_fees,
                'invoicesalestax'=> $sales_tax,
                'invoiceopt'=>$order->invoice_no,
                'deposite_date'=>$order->captured_at,
                'transfer_refund_date'=> ($order->refunded_at != null) ? $order->refunded_at : null,
                'record_type' => ($order->refunded_at != null) ? 'refund' : 'capture',
            ];

        }else{
            $fpostData = [];
        }

          $postData = json_encode([
            'contact_mgr_data'=>$contact_manager_data,
            'user_recipt'=>$user_recipt,
            'ftd_data'=> $fpostData,
          ]);

            $url = env("WP_API_URL");

            // Use the same working batch endpoint as TenantSyncService / tenant:sync-daily.
            // /user-recipt-sync returns WP 404 (rest_no_route).
            $url = ($url != '') ? $url.'/user-recipt-financial-manager-sync' : "";

            if ($url === '') {
                dump('WP_API_URL is empty; skipping order '.$order->id);
                continue;
            }
         
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
            dump([
                'contact_mgr_data'=>$contact_manager_data,
                'user_recipt'=>$user_recipt,
                'ftd_data'=> $fpostData,
            ]);
            dump($response);
            //dump($order);
           //dump($user_recipt);
         }else{
            dump("======Order No Metadata=========");
            dump("======".$order->id."=========");
         }

        }
    }


}
