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

        $admin_details = User::where('role_id',3)->first();
        $to = $admin_details->email;

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
                $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));
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

            $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
    
            \App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');
    
            // send email to user
            if ($order->notify_driver == 'mail') {
                $ordermeta=json_decode($order->ordermeta->value ?? '');
                if (!empty($ordermeta)) {
                    $mail_to=$ordermeta->email ?? '';
                }
                else{
                    $mail_to=$order->user->email ?? '';
                }
                \App\Lib\NotifyToUser::sendEmail($order, $mail_to, 'user');
            }

            }else{
                $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
                $order->payment_status = 6;
                $order->status_id = 2;
                $order->save();
            }

        }else {

            if ($request->status == 1) {

                $this->post_order_data($info);

                $shippingArray = [
                    'shipping_driver' => $request->shipping_service ?? $request->chooseTracking,
                    'tracking_no' => $request->tacking_number
                ];

                $orderShipping = Ordershipping::where('order_id', $id)->update($shippingArray);

                $info = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

                \App\Lib\NotifyToUser::sendEmail($info, $to, 'admin');
                
                if ($info->notify_driver == 'mail') {
                    $ordermeta=json_decode($info->ordermeta->value ?? '');
                    if (!empty($ordermeta)) {
                        $userTo=$ordermeta->email ?? '';
                    }
                    else{
                        $userTo=$info->user->email ?? '';
                    }
                    \App\Lib\NotifyToUser::sendEmail($info, $userTo, 'user');
                }

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

        $admin_details = User::where('role_id',3)->first();
        $to = $admin_details->email;
        
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
            $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
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

            $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

            \App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');
        }
        return redirect()->back();
    }



    public function post_order_data($order,$post_type = 'capture'){

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
        //'memo'=>'Booostr Ecommerce',
        'user_id' =>  $ordermeta['wpuid']??0,
        'revenue_name'=>'4-850 Booostr Ecommerce',
        'transaction_type'=> 'I',
        'sales_tax_collected' => $sales_tax > 0 ? 'Yes':'No',
        'net_revenue'=>$net_recieved_amount,
        'transaction_amount'=>$order_total,
        'expense_category'=>'Revenue',
        'receipts_issued'=> 'Yes',
        'status'=>1,
        'donor_name'=>$ordermeta['name'].' (Online Order)',
        'created'=>$order->placed_at,
        'modified'=>Carbon::now()->setTimezone(config('app.timezone'))->format('m-d-Y h:i A'),
        'invoicenumber'=>$order->invoice_no,
        'invoicreatedate'=>$order->placed_at,
        'invoiceprocessingfee'=>$processing_fees,
        'invoicesalestax'=> $sales_tax,
        'invoiceopt'=>$order->invoice_no,
        'deposite_date'=>$order->captured_at,
        'transfer_refund_date'=> ($post_type == 'refund') ? $order->refunded_at : null,
    ]);
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
        
        $url = env("WP_API_URL");
        
        $url = ($url != '') ? $url.'/financial-manager' : "https://staging3.booostr.co/wp-json/ec/v1/financial-manager";

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

        $admin_details = User::where('role_id',3)->first();
        $to = $admin_details->email;

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
            $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));
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

            $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
            
            $this->post_order_data($order,'refund');

            \App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');

            if ($order->notify_driver == 'mail') {
                $ordermeta=json_decode($order->ordermeta->value ?? '');
                if (!empty($ordermeta)) {
                    $mail_to=$ordermeta->email ?? '';
                }
                else{
                    $mail_to=$order->user->email ?? '';
                }
                \App\Lib\NotifyToUser::sendEmail($order, $mail_to, 'user');
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
