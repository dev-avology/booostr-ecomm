<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Category;
use App\Lib\NotifyToUser;
use App\Models\User;
use App\Models\Orderstock;
use App\Models\Ordermeta;
use App\Models\Price;
use App\Models\Ordershipping;
use Auth;
use DB;
use App\Models\Getway;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       abort_if(!getpermission('order'),401);
       $status=Category::where('type','status')->orderBy('featured','ASC')->withCount('orderstatus')->latest()->get();
       $request_status=$request->status ?? null;
       $orders=Order::with('user','ordermeta','orderitems','orderstatus')->withCount('orderitems');

       $product_type = Category::where('type', 'product_type')->select('id','slug', 'name')->orderBy('id', 'ASC')->get();

       if (!empty($request->status)) {
          $orders=$orders->where('status_id',$request->status);
       }
       if (!empty($request->payment_status)) {
          $orders=$orders->where('payment_status',$request->payment_status);
       }

       if (!empty($request->start)) {
           $start = date("Y-m-d",strtotime($request->start));
          
          $orders=$orders->where('created_at','>=',$start);
       }

       if (!empty($request->end)) {
           $end = date("Y-m-d",strtotime($request->end));
           $orders=$orders->where('created_at','<=',$end);
       }

       if ($request->src) {
           $orders=$orders->where('invoice_no',$request->src);
       }
       $orders=$orders->latest()->paginate(30);
       return view('seller.order.index',compact('request','status','product_type','request_status','orders'));
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!getpermission('order'),401);

        $info=Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule','ordertable')->findorFail($id);
        $ordermeta=json_decode($info->ordermeta->value ?? '');
        $order_status=Category::where([['type','status'],['status',1]])->where('id','!=',3)->orderBy('featured','ASC')->get();
        if ($info->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }

        // dd($info);
        
        return view('seller.order.show',compact('info','ordermeta','order_status','riders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!getpermission('order'),401);

        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
        $ordermeta=json_decode($order->ordermeta->value ?? '');
        $order_status=Category::where([['type','status'],['status',1]])->where('id','!=',3)->orderBy('featured','ASC')->get();

        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }

        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, $order_user_id)
    {
        abort_if(!getpermission('order'),401);
        list($id, $user_id) = explode('_', $order_user_id);

        DB::beginTransaction();
        try { 

        $info = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

        $info->status_id=$request->status;
        $info->save();


        if($request->status == 2){

            $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
    
            $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
            $ordermeta=json_decode($order->ordermeta->value ?? '');
    
            $gateway_data_info = json_decode($gateway->data);
            $payment_data['test_mode']  = $gateway->test_mode;
            $payment_data['currency']   = $gateway->currency_name ?? 'USD';
            $payment_data['getway_id']  = $gateway->id;
            $payment_data['amount']  = $order->total;
            $payment_data['transaction_id']  = $order->transaction_id;
            $payment_data['application_fee_amount']  = (float) $ordermeta->booster_platform_fee??0;
            $payment_data['card_fee_amount']  = (float) $ordermeta->credit_card_fee??0;
            $payment_data['refund_application_fee']  = true;
            $payment_data['refund_card_fee']  = true;
    
            if (!empty($gateway->data)) {
                foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                    $payment_data[$key] = $info;
                };
            }
    
            $paymentresult= $gateway->namespace::refund_payment($payment_data);
    
            if ($paymentresult['payment_status'] == '1') {
                $order->payment_status = 5;
                $order->status_id = 2;
                $order->save();
    
                $transcation_log = new Ordermeta;
                $transcation_log->order_id = $order->id;
                $transcation_log->key = 'transcation_log';
                $transcation_log->value = json_encode($paymentresult['transaction_log']);
                $transcation_log->save();
    
                $order->orderlasttrans()->update([
                    'key' => 'last_transcation_log',
                    'value' => json_encode($paymentresult['transaction_log'])
                ]);
    
            // send email to admin
    
            $order_status = 'Order Cancel & Refund';
            $admin_details = User::where('role_id',3)->first();
    
            \App\Lib\NotifyToUser::makeNotifyToAdmin($order,$admin_details->email,$mail_from=null,$type='tenant_order_notification',$order_status);
    
            // send email to user
    
                if ($order->notify_driver == 'mail') {
                    $ordermeta=json_decode($order->ordermeta->value ?? '');
                    if (!empty($ordermeta)) {
                        $mail_to=$ordermeta->email ?? '';
                    }
                    else{
                        $mail_to=$order->user->email ?? '';
                    }
                    $mail_from=Auth::user()->email;
                    $order['order_cancel_and_refund'] = 'Order cancel & refund';
                    \App\Lib\NotifyToUser::customermail($order,$mail_to);
                }
            }else{
                $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
                $order->payment_status = 6;
                $order->status_id = 2;
                $order->save();
            }
        }else{

            // currently comment the user email because the reason of testing admin email
            $admin_details = User::where('role_id',3)->first();
            \App\Lib\NotifyToUser::makeNotifyToAdmin($info, $admin_details->email);

            if ($request->status == 1) {

                $this->post_order_data($info);

                $shippingArray = [
                    'shipping_driver' => $request->shipping_service ?? $request->chooseTracking,
                    'tracking_no' => $request->tacking_number
                ];

                $orderShipping = Ordershipping::where('order_id', $id)->update($shippingArray);

                $info = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

                $user_info =  NotifyToUser::makeNotifyToUser($info);

                $deletable_ids=[];

                $prices=Orderstock::where('order_id',$id)->whereHas('price')->with('price')->get();
                foreach ($prices as $key => $row) {
                    $current_stock=$row->price->qty;
                    $order_stock=$row->qty;

                    if ($order_stock >= $current_stock) {
                        $new_stock=0;
                        $stock_status=0;
                    }
                    else{
                        $new_stock=$current_stock-$order_stock;
                        $stock_status=1;
                    }
                    $price_row=Price::find($row->price_id);
                    if (!empty($price_row)) {
                        $price_row->qty=$new_stock;
                        $price_row->stock_status=$stock_status;
                        $price_row->save();
                    }
                    array_push($deletable_ids,$row->id);
                }
                if (count($deletable_ids) != 0) {
                    Orderstock::whereIn('id',$deletable_ids)->delete();
                } 
            }
        }

           DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $errors['errors']['error']='Opps something wrong';
            return response()->json($errors,401);
        } 
        return response()->json('Order Updated');
    }


    public function print($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
        $ordermeta=json_decode($order->ordermeta->value ?? '');
        $order_status=Category::where([['type','status'],['status',1]])->orderBy('featured','ASC')->get();

        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }
        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders'));
    }

    public function capture($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

        $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
        $ordermeta=json_decode($order->ordermeta->value ?? '');

        $gateway_data_info = json_decode($gateway->data);
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['currency']   = $gateway->currency_name ?? 'USD';
        $payment_data['getway_id']  = $gateway->id;
        $payment_data['amount']  = $order->total;
        $payment_data['transaction_id']  = $order->transaction_id;

        $payment_data['application_fee_amount']  = $ordermeta->booster_platform_fee??0;
        $payment_data['card_fee_amount']  = $ordermeta->credit_card_fee??0;

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

    

        $paymentresult= $gateway->namespace::capture_payment($payment_data);
        //$paymentresult= ['payment_status'=>1,'payment_id'=>'sffsdf43534','transaction_log'=>$tran_log];


        if ($paymentresult['payment_status'] == '1') {
            $order->payment_status = 1;
            $order->status_id = 4;
            $order->save();


            $transcation_log = new Ordermeta;
            $transcation_log->order_id = $order->id;
            $transcation_log->key = 'transcation_log';
            $transcation_log->value = json_encode($paymentresult['transaction_log']);
            $transcation_log->save();

            $order->orderlasttrans()->update([
                'key' => 'last_transcation_log',
                'value' => json_encode($paymentresult['transaction_log'])
            ]);
        }


        $order_status = 'Order captured';
        $admin_details = User::where('role_id',3)->first();
        \App\Lib\NotifyToUser::makeNotifyToAdmin($order,$admin_details->email,$mail_from=null,$type='tenant_order_notification',$order_status);

        return redirect()->back();
    }



    public function post_order_data($order){

        $order_date = Carbon::parse($order->created_at)->format('Y-m-d');
        $qty = $order->orderitems[0]['qty'];
        $product_amount = $order->orderitems[0]['amount'];
        $sub_total = $product_amount*$qty;
        $sales_tax = $order->tax;
        $order_total = $order->total;

        $ordermeta=json_decode($order->ordermeta->value ?? '',true);
        
        $name = explode(' ',$ordermeta['name']);

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
								);	  

         //$jsonString = $order->shippingwithinfo['info'];

        $jsonString = $order->shippingwithinfo['info'];
        // Decode the JSON string into a PHP array
        $shipping_data = json_decode($jsonString, true);

        $credit_card_fee = $shipping_data['credit_card_fee'];
        $booster_platform_fee = $shipping_data['booster_platform_fee'];
        $processing_fees = $credit_card_fee+$booster_platform_fee;

        $customer_contact_data = $order->user;

        $net_recieved_amount = $order_total-($sales_tax+$processing_fees);

        $shipped_and_fullfilldate = Carbon::parse($order->updated_at)->format('Y-m-d');



        $postData = json_encode(['contact_mgr_data'=>$contact_manager_data,
                                'category_type'=> 'Booostr Ecommerce',
                                'booster_id' =>Tenant('club_id'),
                                'coaid'=>95,
                                'contactname'=>$ordermeta['name'],
                                'memo'=>'Booostr Ecommerce',
                                'user_id' =>  $ordermeta['wpuid']??0,
                                'revenue_name'=>'4-850 Booostr Ecommerce',
                                'transaction_type'=> 'I',
                                'sales_tax_collected' => $sales_tax > 0 ? 'Yes':'No',
                                'net_revenue'=>$net_recieved_amount,
                                'transaction_amount'=>$order_total,
                                'expense_category'=>'Revenue',
                                'receipts_issued'=> 'Yes',
                                'status'=>1,
                                'created'=>$order->created_at,
                                'modified'=>$order->updated_at,
                                'invoicenumber'=>$order->invoice_no,
                                'invoicreatedate'=>$shipped_and_fullfilldate,
                                'invoiceprocessingfee'=>$processing_fees,
                                'invoicesalestax'=> $sales_tax]);

        // 'order_date' => $order_date, 
        // 'order_subtotal' => $sub_total,
        // 'sales_tax' =>$sales_tax,
        // 'order_total' => $order_total,
        // 'processing_stripe_and_boostr_fees' => $processing_fees,
        // 'customer_contact_data' => $customer_contact_data,
        // 'chart_of_accounts' => 'Booostr Ecommerce',
        // 'under_net_recieved'=> $net_recieved_amount,
        // 'net_recieved_shipped_full_fill_date' => $shipped_and_fullfilldate,
        // 'date_of_payment' => $shipped_and_fullfilldate
                                         
        $url = env("WP_fINITIAL_MANAGER_URL");
        
        $url = ($url != '') ? $url : "https://staging3.booostr.co/wp-json/ec/v1/financial-manager";

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
        //Log::info($response);
       // dd($response);
        return $response;
    }


    public function refund($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

        $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
        $ordermeta=json_decode($order->ordermeta->value ?? '');

        $gateway_data_info = json_decode($gateway->data);
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['currency']   = $gateway->currency_name ?? 'USD';
        $payment_data['getway_id']  = $gateway->id;
        $payment_data['amount']  = $order->total;
        $payment_data['transaction_id']  = $order->transaction_id;
        $payment_data['application_fee_amount']  = (float) $ordermeta->booster_platform_fee??0;
        $payment_data['card_fee_amount']  = (float) $ordermeta->credit_card_fee??0;
        $payment_data['refund_application_fee']  = true;
        $payment_data['refund_card_fee']  = true;

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

        //$paymentresult= $gateway->namespace::refund_payment($payment_data);

$tran_log='{"id":"ch_3O56KFGVOc8S2Ta10txkyYkx","object":"charge","amount":4700,"amount_captured":4700,"amount_refunded":4700,"application":null,"application_fee":"fee_1O56PN2cxSrLS5j9AHDdu2QO","application_fee_amount":284,"balance_transaction":"txn_3O56KFGVOc8S2Ta10ellEY1M","billing_details":{"address":{"city":null,"country":null,"line1":null,"line2":null,"postal_code":"24242","state":null},"email":null,"name":null,"phone":null},"calculated_statement_descriptor":"BOOOSTR.CO","captured":true,"created":1698237403,"currency":"usd","customer":null,"description":null,"destination":"acct_1L0lG32cxSrLS5j9","dispute":null,"disputed":false,"failure_balance_transaction":null,"failure_code":null,"failure_message":null,"fraud_details":[],"invoice":null,"livemode":false,"metadata":[],"on_behalf_of":"acct_1L0lG32cxSrLS5j9","order":null,"outcome":{"network_status":"approved_by_network","reason":null,"risk_level":"normal","risk_score":37,"seller_message":"Payment complete.","type":"authorized"},"paid":true,"payment_intent":null,"payment_method":"card_1O56KCGVOc8S2Ta1U4Qn16U8","payment_method_details":{"card":{"amount_authorized":4700,"brand":"visa","capture_before":1698842203,"checks":{"address_line1_check":null,"address_postal_code_check":"pass","cvc_check":"pass"},"country":"US","exp_month":2,"exp_year":2042,"extended_authorization":{"status":"disabled"},"fingerprint":"Ac6yIC2uuUHTelfH","funding":"credit","incremental_authorization":{"status":"unavailable"},"installments":null,"last4":"4242","mandate":null,"multicapture":{"status":"unavailable"},"network":"visa","network_token":{"used":false},"overcapture":{"maximum_amount_capturable":4700,"status":"unavailable"},"three_d_secure":null,"wallet":null},"type":"card"},"receipt_email":null,"receipt_number":null,"receipt_url":"https:\/\/pay.stripe.com\/receipts\/payment\/CAcaFwoVYWNjdF8xR2lSZFVHVk9jOFMyVGExKKWa5KkGMgYNjL_yieI6LBbYzpJLT9Rloq-fajDbdmzxbyvERh1ywWaUFzvEfMNLO1Lh9wx1_SejNd_A","refunded":true,"refunds":{"object":"list","data":[{"id":"re_3O56KFGVOc8S2Ta106BwZDzZ","object":"refund","amount":4700,"balance_transaction":"txn_3O56KFGVOc8S2Ta10f1XmMD0","charge":"ch_3O56KFGVOc8S2Ta10txkyYkx","created":1698237731,"currency":"usd","metadata":[],"payment_intent":null,"reason":null,"receipt_number":null,"source_transfer_reversal":null,"status":"succeeded","transfer_reversal":"trr_1O56PXGVOc8S2Ta1dpPe2bFs"}],"has_more":false,"total_count":1,"url":"\/v1\/charges\/ch_3O56KFGVOc8S2Ta10txkyYkx\/refunds"},"review":null,"shipping":null,"source":{"id":"card_1O56KCGVOc8S2Ta1U4Qn16U8","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":"24242","address_zip_check":"pass","brand":"Visa","country":"US","customer":null,"cvc_check":"pass","dynamic_last4":null,"exp_month":2,"exp_year":2042,"fingerprint":"Ac6yIC2uuUHTelfH","funding":"credit","last4":"4242","metadata":[],"name":null,"tokenization_method":null,"wallet":null},"source_transfer":null,"statement_descriptor":null,"statement_descriptor_suffix":null,"status":"succeeded","transfer":"tr_3O56KFGVOc8S2Ta103SBiX8j","transfer_data":{"amount":null,"destination":"acct_1L0lG32cxSrLS5j9"},"transfer_group":"group_ch_3O56KFGVOc8S2Ta10txkyYkx"}';

        $transactionData = json_decode($tran_log, true);
		
		$paymentresult= ['payment_status'=>1,'payment_id'=>'ch_3O56KFGVOc8S2Ta10txkyYkx','transaction_log'=>$transactionData];

        if ($paymentresult['payment_status'] == '1') {
            $order->payment_status = 5;
            $order->status_id = 2;
            $order->save();

            $transcation_log = new Ordermeta;
            $transcation_log->order_id = $order->id;
            $transcation_log->key = 'transcation_log';
            $transcation_log->value = json_encode($paymentresult['transaction_log']);
            $transcation_log->save();

            $order->orderlasttrans()->update([
                'key' => 'last_transcation_log',
                'value' => json_encode($paymentresult['transaction_log'])
            ]);

        // send email to admin

        $order_status = 'Order Cancel & Refund';
        $admin_details = User::where('role_id',3)->first();

        \App\Lib\NotifyToUser::makeNotifyToAdmin($order,$admin_details->email,$mail_from=null,$type='tenant_order_notification',$order_status);

        // send email to user

        if ($order->notify_driver == 'mail') {
            $ordermeta=json_decode($order->ordermeta->value ?? '');
            if (!empty($ordermeta)) {
                $mail_to=$ordermeta->email ?? '';
            }
            else{
                $mail_to=$order->user->email ?? '';
            }
            $mail_from=Auth::user()->email;
            $order['order_cancel_and_refund'] = 'Order cancel & refund';
            \App\Lib\NotifyToUser::customermail($order,$mail_to);
        }
     }
        return redirect()->back();
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       abort_if(!getpermission('order'),401);

      

       if ($request->method == 'delete') {
          Order::whereIn('id',$request->ids)->delete();
          $msg='Status Order Deleted';
          return response()->json();
       }
       else{
         Order::whereIn('id',$request->ids)->update(['status_id' => $request->method]);
         $msg='Status Updated Successfully';

        
       }


       
    }
}
