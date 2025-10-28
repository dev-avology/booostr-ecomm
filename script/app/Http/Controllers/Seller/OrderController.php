<?php

namespace App\Http\Controllers\Seller;

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
use Illuminate\Support\Arr;
use Stripe\Stripe;

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
        $product_type = Category::where('type', 'product_type')->select('id','slug', 'name')->orderBy('id', 'ASC')->get();
        
        if ($info->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }

        // dd($info);
        
        return view('seller.order.show',compact('info','ordermeta','order_status','riders','product_type'));
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
        
        $product_type = Category::where('type', 'product_type')->select('id','slug', 'name')->orderBy('id', 'ASC')->get();
       
        $selected_product_type = [];

        foreach ($order->orderitems ?? [] as $row){
        $p_types = $product_type->pluck('id')->all();
        
        $selected_product_type[] = $row->term->termcategories
            ->pluck('category_id')
            ->intersect($p_types)
            ->values()
            ->all();
        }
     
        $selected_product_type = Arr::flatten($selected_product_type);

        $count = count($selected_product_type);

        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };

        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }

        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders','order_type'));
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
        abort_if(!getpermission('order'), 401);
        list($id, $user_id) = explode('_', $order_user_id);
    
        $admin_details = User::where('role_id', 3)->first();
        $to = $admin_details->email;
    
        $order = Order::with(['orderitems.term.termcategories', 'ordermeta', 'user', 'orderlasttrans', 'orderstatus'])->findOrFail($id);
    
        // Determine order type
        $product_type = Category::where('type', 'product_type')->get();
        $selected_product_type = [];
    
        foreach ($order->orderitems ?? [] as $item) {
            $p_types = $product_type->pluck('id')->all();
            $selected_product_type[] = $item->term->termcategories
                ->pluck('category_id')
                ->intersect($p_types)
                ->values()
                ->all();
        }
    
        $selected_product_type = Arr::flatten($selected_product_type);
        $count = count($selected_product_type);
    
        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional($product_type->firstWhere('id', $selected_product_type[0]))->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };
    
        // Check if status is set to Complete (1) and order type is Digital
        if ($request->status == 1 && $order_type == 'Digital') {
            $order->status_id = 1; // Set to Complete
            // Post order data logic
            if (in_array($order->order_from, [0, 4, 5])) {
                $this->post_order_data_POS($order);
            } else {
                $this->post_order_data($order);
            }

    
            // Notify Admin
            \App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');
    
            // Notify User if needed
            if ($order->notify_driver == 'mail') {
                $ordermeta = json_decode($order->ordermeta->value ?? '{}');
                $user_email = $ordermeta->email ?? $order->user->email ?? '';
                if ($user_email) {
                    \App\Lib\NotifyToUser::sendEmail($order, $user_email, 'user');
                }
            }
        } else {
            // For non-Digital orders or other statuses, update as requested
            $order->status_id = $request->status;
        }
    
        $order->save();
    
        // Refund logic if status = 2
        if ($request->status == 2) {
            $this->processRefund($order);
        }
    
        return response()->json(['message' => 'Order Updated']);
    }





    public function print($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
        $ordermeta=json_decode($order->ordermeta->value ?? '');
        $order_status=Category::where([['type','status'],['status',1]])->orderBy('featured','ASC')->get();
        $product_type = Category::where('type', 'product_type')->select('id','slug', 'name')->orderBy('id', 'ASC')->get();
       
        $selected_product_type = [];

        foreach ($order->orderitems ?? [] as $row){
        $p_types = $product_type->pluck('id')->all();
        
        $selected_product_type[] = $row->term->termcategories
            ->pluck('category_id')
            ->intersect($p_types)
            ->values()
            ->all();
        }
     
        $selected_product_type = Arr::flatten($selected_product_type);

        $count = count($selected_product_type);

        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };


        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }
        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders','product_type','order_type'));
    }
    
//     public function capture($id)
// {
//     abort_if(!getpermission('order'), 401);

//     $admin_details = User::where('role_id', 3)->first();
//     $to = $admin_details->email;

//     $order = Order::with('orderstatus', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'getway', 'schedule')->findOrFail($id);

//     $gateway = Getway::where('status', '!=', 0)->where('namespace', '=', 'App\Lib\Stripe')->first();
//     $ordermeta = json_decode($order->ordermeta->value ?? '');

//     $gateway_data_info = json_decode($gateway->data);
//     $payment_data['test_mode'] = $gateway->test_mode;
//     $payment_data['currency'] = $gateway->currency_name ?? 'USD';
//     $payment_data['getway_id'] = $gateway->id;
//     $payment_data['amount'] = $order->total;
//     $payment_data['transaction_id'] = $order->transaction_id;
//     $payment_data['application_fee_amount'] = $ordermeta->booster_platform_fee ?? 0;
//     $payment_data['card_fee_amount'] = $ordermeta->credit_card_fee ?? 0;

//     if (!empty($gateway->data)) {
//         foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
//             $payment_data[$key] = $info;
//         };
//     }

//     Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);

//         // Detect PaymentIntent or Charge
//         if (str_starts_with($payment_data['transaction_id'], 'pi_')) {
//             $paymentIntent = \Stripe\PaymentIntent::retrieve($payment_data['transaction_id']);
//         } elseif (str_starts_with($payment_data['transaction_id'], 'ch_')) {
//             $charge = \Stripe\Charge::retrieve($payment_data['transaction_id']);
//             $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
//         } else {
//             throw new \Exception('Invalid transaction ID format');
//         }

//         // Capture if required
//         if ($paymentIntent->status === 'requires_capture') {
//             $captured = $paymentIntent->capture();
//             $payment_status = $captured->status === 'succeeded' ? 1 : 0;
//             $transaction_log = $captured;
//         } else {
//             $payment_status = $paymentIntent->status === 'succeeded' ? 1 : 0;
//             $transaction_log = $paymentIntent;
//         }

//         $paymentresult = [
//             'payment_status'  => $payment_status,
//             'payment_id'      => $paymentIntent->id,
//             'transaction_log' => $transaction_log,
//         ];
//     } 
    

//     // ✅ Existing logic continues
//     if ($paymentresult['payment_status'] == '1') {
//         $order->payment_status = 1;
//         $order->status_id = 3;
//         $order->captured_at = Carbon::now()->setTimezone(config('app.timezone'));
//         $order->save();

//         $transcation_log = new Ordermeta;
//         $transcation_log->order_id = $order->id;
//         $transcation_log->key = 'transcation_log';
//         $transcation_log->value = json_encode($paymentresult['transaction_log']);
//         $transcation_log->save();

//         $order->orderlasttrans()->update([
//             'key' => 'last_transcation_log',
//             'value' => json_encode($paymentresult['transaction_log'])
//         ]);

//         $order = Order::with('orderstatus', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'getway', 'schedule')->findOrFail($id);

//         \App\Lib\NotifyToUser::sendEmail($order, $to, 'admin');
//     }

//     return redirect()->back();
// }


    public function capture($id)
    {
        abort_if(!getpermission('order'), 401);
        \Log::info("Capture called for order {$id}");

        $admin = User::where('role_id', 3)->first();
        $order = Order::with([ 'orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','schedule','orderlasttrans'])->find($id);
        if (!$order) {
            \Log::error("Order {$id} not found.");
            return back()->with('error', 'Order not found.');
        }
        
        $gateway = Getway::where('status', '!=', 0)->where('namespace', 'App\Lib\Stripe')->first();
        
        if (!$gateway) {
    
            \Log::error("Stripe gateway not found for order {$id}");
    
            return back()->with('error', 'Stripe gateway not configured.');
    
        }
    
        $gatewayData = json_decode($gateway->data ?? '{}');
        $secretKey = $gateway->test_mode == 1 ? ($gatewayData->test_secret_key ?? '') : ($gatewayData->secret_key ?? '');
    
        if (empty($secretKey)) {
    
            \Log::error("Stripe secret key missing for gateway id {$gateway->id}");
    
            return back()->with('error', 'Stripe secret key missing.');
    
        }
    
        \Stripe\Stripe::setApiKey($secretKey);
 
        $transactionId = $order->transaction_id ?? null;
        if (empty($transactionId)) {
    
            \Log::error("Order {$order->id} missing transaction_id.");
    
            return back()->with('error', 'Transaction ID not found for order.');
    
        }

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');

        $debug = [ 'order_id' => $order->id,'transaction_id' => $transactionId,'gateway_id' => $gateway->id,
            'gateway_test_mode' => $gateway->test_mode,
        ];
        \Log::info('Capture debug start', $debug);

        try {
            $paymentSucceeded = false;
            $transactionObject = null;
            $paymentId = $transactionId;

            if (str_starts_with($transactionId, 'pi_')) {
                $pi = \Stripe\PaymentIntent::retrieve($transactionId);
    
                \Log::info("PaymentIntent retrieved: {$pi->id} status {$pi->status}");
    
                if ($pi->status === 'requires_capture') {
                    $pi = $pi->capture();
                    \Log::info("PaymentIntent captured: {$pi->id} status {$pi->status}");
    
                }
    
                if ($pi->status === 'succeeded') {
                    $paymentSucceeded = true;
    
                    $transactionObject = $pi;
    
                    $paymentId = $pi->id;
    
                } else {
                    \Log::warning("PaymentIntent not succeeded, status={$pi->status} for order {$order->id}");
    
                }
    
            }
    
            elseif (str_starts_with($transactionId, 'ch_')) {
                $ch = \Stripe\Charge::retrieve($transactionId);
    
                \Log::info("Charge retrieved: {$ch->id} status {$ch->status} captured={$ch->captured} refunded=" . ($ch->refunded ? '1' : '0'));
    
                if (!empty($ch->payment_intent)) {
    
                    $pi = \Stripe\PaymentIntent::retrieve($ch->payment_intent);
    
                    \Log::info("Linked PaymentIntent {$pi->id} status {$pi->status}");
    
                    if ($pi->status === 'requires_capture') {
    
                        $pi = $pi->capture();
    
                        \Log::info("Linked PaymentIntent captured {$pi->id} status {$pi->status}");
    
                    }
                    if ($pi->status === 'succeeded') {
                        $paymentSucceeded = true;
    
                        $transactionObject = $pi;
    
                        $paymentId = $pi->id;
    
                    } else {
                        \Log::warning("Linked PI not succeeded status={$pi->status} for order {$order->id}");
    
                    }
    
                } else {

                    if ($ch->refunded) {
                        \Log::info("Charge already refunded for order {$order->id}. Syncing local state.");
                        $order->payment_status = 5;
                        $order->status_id = 2; // refunded/cancelled (adjust as needed)
                        $order->refunded_at = now();
                        $order->save();
                        return back()->with('info', 'Charge already refunded; local status synced.');
                    }
                    if ($ch->captured || $ch->status === 'succeeded') {
                        $paymentSucceeded = true;
                        $transactionObject = $ch;
                        $paymentId = $ch->id;
    
                    } elseif ($ch->status === 'pending' && !$ch->captured) {
                        $capturedCharge = $ch->capture();
                        \Log::info("Captured legacy charge {$capturedCharge->id} status {$capturedCharge->status}");
                        
                        if ($capturedCharge->status === 'succeeded') {
                            $paymentSucceeded = true;
                            $transactionObject = $capturedCharge;
                            $paymentId = $capturedCharge->id;
                        }
                    } else {
                        \Log::warning("Charge not capturable: status={$ch->status} captured={$ch->captured}");
    
                    }
    
                }
    
            }
            else {
                \Log::error("Unsupported transaction id format: {$transactionId}");
                return back()->with('error', 'Unsupported transaction id format.');
    
            }
            if ($paymentSucceeded) {
                DB::transaction(function () use ($order, $transactionObject, $paymentId, $admin) {
                    $order->payment_status = 1;
                    $order->status_id = 3;
                    $order->captured_at = now();
                    $order->save();

                    Ordermeta::updateOrCreate(
                        ['order_id' => $order->id, 'key' => 'transcation_log'],
                        ['value' => json_encode($transactionObject)]
                    );

                    Ordermeta::updateOrCreate(
                        ['order_id' => $order->id, 'key' => 'last_transcation_log'],
                        ['value' => json_encode($transactionObject)]
                    );
    
                });

                if (in_array($order->order_from, [0, 4, 5])) {
                    $this->post_order_data_POS($order, 'capture');
                } else {
                    $this->post_order_data($order, 'capture');
    
                }
                
                if (!empty($admin->email)) {
                    \App\Lib\NotifyToUser::sendEmail($order, $admin->email, 'admin');
    
                }
                \Log::info("Order {$order->id} successfully captured and updated. payment_id={$paymentId}");
    
                return back()->with('success', 'Payment captured and order updated.');
    
            }

            \Log::warning("Payment capture attempt did not result in succeeded status for order {$order->id}. TransactionObject status logged.");
    
            return back()->with('warning', 'Payment capture did not succeed. Check logs for details.');
    
        } catch (\Stripe\Exception\ApiErrorException $e) {
    
            \Log::error("Stripe API error for order {$order->id}: " . $e->getMessage());
    
            return back()->with('error', 'Stripe API error: ' . $e->getMessage());
    
        } catch (\Exception $e) {
    
            \Log::error("Capture failed for order {$order->id}: " . $e->getMessage());
    
            return back()->with('error', 'Capture failed: ' . $e->getMessage());
    
        }
    
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

        $gateway=Getway::find($order->getway_id);

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
        'donor_name'=>$ordermeta['name'].' (Online Order)',
        'created'=>$order->placed_at,
        'modified'=>Carbon::now()->setTimezone(config('app.timezone')),
        'payement_method'=>($gateway->name == 'cash') ? 0 : 3,
        'invoicenumber'=>$order->invoice_no,
        'invoicreatedate'=>$order->placed_at,
        'invoiceprocessingfee'=>$processing_fees,
        'invoicesalestax'=> $sales_tax,
        'invoiceopt'=>$order->invoice_no,
        'deposite_date'=>$order->captured_at,
        'transfer_refund_date'=> ($post_type == 'refund') ? $order->refunded_at : null,
        'record_type' => $post_type,
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


        // $financial_manager = env("WP_fINITIAL_MANAGER_URL");
        // $url = ($financial_manager != '') ? $financial_manager : "https://staging3.booostr.co/wp-json/ec/v1/financial-manager";


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
      //  dump("=========ONLINE=============");
      //  dd($response);
        return $response;
    }


    public function post_order_data_POS($order,$post_type = 'capture'){

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



        $postData = json_encode([
        'category_type'=> 'Booostr Ecommerce',
        'booster_id' =>Tenant('club_id'),
        'coaid'=>41,
        'contactname'=>isset($ordermeta['name'])?$ordermeta['name']:'Guest User',
        //'memo'=>'Booostr Ecommerce',
        'user_id' => 0,
        'revenue_name'=>'4-850 Booostr Ecommerce',
        'transaction_type'=>'I',
        'sales_tax_collected' => $sales_tax > 0 ? 'Yes':'No',
        'net_revenue'=>$net_recieved_amount,
        'transaction_amount'=>$order_total,
        'expense_category'=>'Revenue',
        'receipts_issued'=> 'Yes',
        'status'=>1,
        'donor_name'=>isset($ordermeta['name'])? $ordermeta['name'].' (POS Order)':'Guest User'.' (POS Order)',
        'created'=>$order->placed_at,
        'modified'=>Carbon::now()->setTimezone(config('app.timezone')),
        'payement_method'=>($gateway->name == 'cash') ? 0 : 3,
        'invoicenumber'=>$order->invoice_no,
        'invoicreatedate'=>$order->placed_at,
        'invoiceprocessingfee'=>$processing_fees,
        'invoicesalestax'=> $sales_tax,
        'invoiceopt'=>$order->invoice_no,
        'deposite_date'=>$order->captured_at,
        'transfer_refund_date'=> ($post_type == 'refund') ? $order->refunded_at : null,
        'record_type' => $post_type,
    ]);

   
    $url = env("WP_API_URL");

   // $url = ($url != '') ? $url.'/financial-manager-pos' : "https://staging3.booostr.co/wp-json/store-api/v1/financial-manager-pos";
    $url = ($url != '') ? $url.'/financial-manager' : "https://staging3.booostr.co/wp-json/store-api/v1/financial-manager";

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
   // dump("=========POS=============");
   // dd($response);
    return $response;
}



public function refund($id, $silent = false)
{
    abort_if(!getpermission('order'),401);

    $admin_details = User::where('role_id',3)->first();
    $to = $admin_details->email ?? '';

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

  Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);
 
  $transactionId = $payment_data['transaction_id'] ?? null;
    if (!$transactionId) {
        if (request()->wantsJson() || $silent) {
            throw new \Exception('No transaction ID provided');
        }
        return redirect()->back()->with('error', 'No transaction ID provided');
    }

    if (str_starts_with($transactionId, 'pi_')) {
        $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);
    } elseif (str_starts_with($transactionId, 'ch_')) {
        $charge = \Stripe\Charge::retrieve($transactionId);
        $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
    } else {
        if (request()->wantsJson() || $silent) {
            throw new \Exception('Invalid transaction ID format');
        }
        return redirect()->back()->with('error', 'Invalid transaction ID format');
    }

    $paymentstatus = $paymentIntent->status === 'succeeded' ? 1 : 0;

    $transaction_log = $paymentIntent;

    $payment_data['payment_intent'] = $paymentIntent;
    $payment_data['payment_status'] = $paymentstatus;

    if ($paymentstatus !== 1) {
        if (request()->wantsJson() || $silent) {
            throw new \Exception('Payment is not in a refundable state.');
        }
        return redirect()->back()->with('error', 'Payment is not in a refundable state.');
    }


    $chargeId = $paymentIntent->latest_charge;
    $payment_data['transaction_id'] = $chargeId;

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

        if (!$silent) {
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

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Order refunded successfully']);
            }
            return redirect()->back()->with('success', 'Order refunded successfully');
        }

        return true;
    } else {
        if (request()->wantsJson() || $silent) {
            throw new \Exception('Refund failed');
        }
         return redirect()->back();
    }
}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

public function destroy(Request $request)
{
    abort_if(!getpermission('order'), 401);

    $ids = $request->ids ?? [];
     $ids = array_filter(array_map('intval', (array) $ids), fn($id) => $id > 0);
    $ids = array_unique(array_values($ids));

    if (empty($ids)) {
        return response()->json(['error' => 'No valid orders selected'], 400);
    }
    // Always get method as string to avoid numeric/string issues
    $method = (string) $request->input('method', '');

    $orders = Order::with(['user', 'ordermeta'])->whereIn('id', $ids)->get();
    $adminEmail = User::where('role_id', 3)->value('email');

    $updated = [];

    // 1️⃣ DELETE UNCAPPED ORDERS
    if ($request->method == 'delete') {
        Order::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Orders Deleted']);
    }

    // 2️⃣ CAPTURE AUTHORIZED LOW RISK
    // elseif ($method === 'capture_authorized') {
    //     $admin_details = User::where('role_id', 3)->first();
    //     $adminEmail = $admin_details->email ?? '';

    //     $gateway = Getway::where('status', '!=', 0)
    //         ->where('namespace', '=', 'App\Lib\Stripe')->first();

    //     if (!$gateway) {
    //         return response()->json(['error' => 'Stripe gateway not found'], 400);
    //     }

    //     $gateway_data_info = json_decode($gateway->data);

    //     foreach ($orders as $order) {
    //         if (in_array($order->risk_level, ['normal', 'low']) && in_array($order->payment_status, [4, 5])) {
    //             $paymentstatus = 1; // Default to complete
    //             $status = 3; // Default to pending fulfillment

    //             if ($order->payment_status == 5) {
    //                 // Already refunded, just update status
    //                 $order->status_id = $status;
    //                 $order->save();
    //             } else {
    //                 // Capture authorized payment
    //                 $ordermeta = json_decode($order->ordermeta->value ?? '');
    //                 $payment_data = [
    //                     'test_mode' => $gateway->test_mode,
    //                     'currency' => $gateway->currency_name ?? 'USD',
    //                     'getway_id' => $gateway->id,
    //                     'amount' => $order->total,
    //                     'transaction_id' => $order->transaction_id,
    //                     'application_fee_amount' => $ordermeta->booster_platform_fee ?? 0,
    //                     'card_fee_amount' => $ordermeta->credit_card_fee ?? 0,
    //                 ];

    //                 Stripe::setApiKey($gateway->test_mode == 1 
    //                     ? $gateway_data_info->test_secret_key 
    //                     : $gateway_data_info->secret_key);

    //                 $transactionId = $payment_data['transaction_id'] ?? null;
    //                 if (!$transactionId) continue;

    //                 try {
    //                     if (str_starts_with($transactionId, 'pi_')) {
    //                         $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);
    //                     } elseif (str_starts_with($transactionId, 'ch_')) {
    //                         $charge = \Stripe\Charge::retrieve($transactionId);
    //                         $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
    //                     } else {
    //                         throw new \Exception('Invalid transaction ID format');
    //                     }

    //                     if ($paymentIntent->status === 'requires_capture') {
    //                         $captured = $paymentIntent->capture();
    //                         $paymentstatus = $captured->status === 'succeeded' ? 1 : 0;
    //                         $transaction_log = $captured;
    //                     } else {
    //                         $paymentstatus = $paymentIntent->status === 'succeeded' ? 1 : 0;
    //                         $transaction_log = $paymentIntent;
    //                     }

    //                     if ($paymentstatus == 1) {
    //                         $order->payment_status = $paymentstatus;
    //                         $order->status_id = $status;
    //                         $order->captured_at = now();
    //                         $order->save();

    //                         $transcation_log = new Ordermeta;
    //                         $transcation_log->order_id = $order->id;
    //                         $transcation_log->key = 'transcation_log';
    //                         $transcation_log->value = json_encode([
    //                             'payment_status' => $paymentstatus,
    //                             'payment_id' => $paymentIntent->id,
    //                             'transaction_log' => $transaction_log,
    //                         ]);
    //                         $transcation_log->save();

    //                         $order->orderlasttrans()->update([
    //                             'key' => 'last_transcation_log',
    //                             'value' => json_encode([
    //                                 'payment_status' => $paymentstatus,
    //                                 'payment_id' => $paymentIntent->id,
    //                                 'transaction_log' => $transaction_log,
    //                             ])
    //                         ]);

    //                         $post_type = 'capture';
    //                         if (in_array($order->order_from, [0, 4, 5])) {
    //                             $this->post_order_data_POS($order, $post_type);
    //                         } else {
    //                             $this->post_order_data($order, $post_type);
    //                         }
    //                     }
    //                 } catch (\Exception $e) {
    //                     continue;
    //                 }
    //             }

    //             // Notifications
    //             if ($adminEmail) {
    //                 \App\Lib\NotifyToUser::sendEmail($order, $adminEmail, 'admin');
    //             }
    //             if ($order->notify_driver == 'mail') {
    //                 $userTo = json_decode($order->ordermeta->value ?? '{}')->email ?? $order->user->email ?? '';
    //                 if ($userTo) \App\Lib\NotifyToUser::sendEmail($order, $userTo, 'user');
    //             }
    //             $updated[] = $order->id;
    //         }
    //     }

    //     return response()->json([
    //         'message' => 'Captured authorized low-risk orders.',
    //         'captured_orders' => $updated
    //     ]);
    // }
elseif ($method === 'capture_authorized') {
    
    $adminEmail = User::where('role_id', 3)->value('email');

    $gateway = Getway::where('status', '!=', 0)->where('namespace', 'App\Lib\Stripe')->first();

    if (!$gateway) {
        return response()->json(['error' => 'Stripe gateway not found'], 400);
    }

    $gateway_data_info = json_decode($gateway->data ?? '{}');
    $secretKey = $gateway->test_mode == 1? ($gateway_data_info->test_secret_key ?? ''): ($gateway_data_info->secret_key ?? '');

    if (empty($secretKey)) {
        \Log::error('Stripe secret key missing for gateway id: ' . $gateway->id);
        return response()->json(['error' => 'Stripe secret key missing'], 500);
    }
    \Stripe\Stripe::setApiKey($secretKey);
    $updated = [];
    $failed = [];
    $synced_refunds = [];
    $skipped = []; // Added for client requirement: track skipped orders (not low-risk)
    foreach ($orders as $order) {
        if (!is_object($order) || !method_exists($order, 'save')) {
            \Log::error('Order item is not an Eloquent model: ' . gettype($order));
            $failed[] = $order->id ?? null;
            continue;
        }
        if (!in_array($order->risk_level, ['normal', 'low']) || !in_array($order->payment_status, [4, 5])) {
            $skipped[] = $order->id; // Track skipped due to risk level or payment status
            continue;
        }
        if ($order->payment_status == 5) {
            try {
                $order->status_id = 2; // refunded/cancelled (adjust if needed)
                $order->refunded_at = now();
                $order->save();
                $synced_refunds[] = $order->id;
            } catch (\Exception $e) {
                \Log::error("Failed to sync refunded order {$order->id}: {$e->getMessage()}");
                $failed[] = $order->id;
            }
            continue;
        }

        $transactionId = $order->transaction_id;
        if (empty($transactionId)) {
            \Log::warning("Order {$order->id} missing transaction_id, skipping");
            $failed[] = $order->id;
            continue;
        }
        DB::beginTransaction();
        try {
            $paymentSucceeded = false;
            $payment_id = $transactionId;
            $transaction_log_object = null;
            if (str_starts_with($transactionId, 'pi_')) {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);
                if ($paymentIntent->status === 'requires_capture') {
                    $captured = $paymentIntent->capture();
                    $paymentSucceeded = ($captured->status === 'succeeded');
                    $transaction_log_object = $captured;
                    $payment_id = $captured->id ?? $payment_id;
                } elseif ($paymentIntent->status === 'succeeded') {
                    $paymentSucceeded = true;
                    $transaction_log_object = $paymentIntent;
                    $payment_id = $paymentIntent->id;
                } else {
                    \Log::info("Order {$order->id} PaymentIntent not capturable: status={$paymentIntent->status}");
                    throw new \Exception("PaymentIntent status not capturable: {$paymentIntent->status}");
                }
            }
            elseif (str_starts_with($transactionId, 'ch_')) {
                $charge = \Stripe\Charge::retrieve($transactionId);
                if (!empty($charge->payment_intent)) {
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
                    if ($paymentIntent->status === 'requires_capture') {
                        $captured = $paymentIntent->capture();
                        $paymentSucceeded = ($captured->status === 'succeeded');
                        $transaction_log_object = $captured;
                        $payment_id = $captured->id ?? $payment_id;
                    } elseif ($paymentIntent->status === 'succeeded') {
                        $paymentSucceeded = true;
                        $transaction_log_object = $paymentIntent;
                        $payment_id = $paymentIntent->id;
                    } else {
                        throw new \Exception("PaymentIntent status not capturable: {$paymentIntent->status}");
                    }
                } else {
                    if ($charge->refunded) {
                        $order->payment_status = 5;
                        $order->status_id = 2;
                        $order->refunded_at = now();
                        $order->save();
                        $synced_refunds[] = $order->id;
                        DB::commit();
                        continue;
                    }
                    if ($charge->captured || $charge->status === 'succeeded') {
                        $paymentSucceeded = true;
                        $transaction_log_object = $charge;
                        $payment_id = $charge->id;
                    } elseif ($charge->status === 'succeeded' && !$charge->captured) {
                        $captured_charge = $charge->capture();
                        $paymentSucceeded = ($captured_charge->status === 'succeeded');
                        $transaction_log_object = $captured_charge;
                        $payment_id = $captured_charge->id ?? $payment_id;
                    } else {
                        throw new \Exception('Legacy charge not capturable: ' . $charge->status);
                    }
                }
            } else {
                throw new \Exception('Invalid transaction ID format: ' . $transactionId);
            }
            if ($paymentSucceeded) {
                $order->payment_status = 1;
                $order->status_id = 3; // pending fulfillment
                $order->captured_at = now();
                $order->save(); // explicit save to avoid $fillable issues
                $logData = json_encode([
                    'payment_status' => 1,
                    'payment_id' => $payment_id,
                    'transaction_log' => $transaction_log_object,

                ]);
                Ordermeta::create([

                    'order_id' => $order->id,
                    'key' => 'transcation_log', // keep your existing key or change consistently
                    'value' => $logData,

                ]);

                $order->orderlasttrans()->updateOrCreate(
                    ['key' => 'last_transcation_log'],
                    ['value' => $logData]

                );
                $post_type = 'capture';
                if (in_array($order->order_from, [0, 4, 5])) {
                    $this->post_order_data_POS($order, $post_type);
                } else {

                    $this->post_order_data($order, $post_type);
                }
                if ($adminEmail) {

                    \App\Lib\NotifyToUser::sendEmail($order, $adminEmail, 'admin');

                }
                $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
                if ($order->notify_driver == 'mail') {
                    $userEmail = ($ordermeta->email ?? '') ?: ($order->user->email ?? '');
                    if ($userEmail) {

                        \App\Lib\NotifyToUser::sendEmail($order, $userEmail, 'user');
                    }
                }
                $updated[] = $order->id;
                \Log::info("Order {$order->id} captured and updated successfully.");

            } else {
                \Log::warning("Order {$order->id} capture attempted but not succeeded.");

                $failed[] = $order->id;
            }
            DB::commit();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();
            \Log::error("Stripe API error for order {$order->id}: {$e->getMessage()}");
            $failed[] = $order->id;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error processing capture for order {$order->id}: {$e->getMessage()}");
            $failed[] = $order->id;
        }
    }
    $message_parts = [];
    if (count($updated)) $message_parts[] = count($updated) . ' low-risk orders captured successfully.';
    if (count($synced_refunds)) $message_parts[] = count($synced_refunds) . ' refunded orders synced.';
    if (count($failed)) $message_parts[] = count($failed) . ' orders failed to process.';
    if (count($skipped)) $message_parts[] = count($skipped) . ' orders skipped (not low-risk).';
    if (empty($message_parts)) $message_parts[] = 'No eligible low-risk orders found.';
    return response()->json([
        'message' => implode(' ', $message_parts),
        'captured_orders' => $updated,
        'synced_refunds' => $synced_refunds,
        'failed_orders' => $failed,
        'skipped_orders' => $skipped, // Added for client requirement
    ]);

}

    // 3️⃣ COMPLETE FULFILLMENT
    elseif ($method === 'complete_fulfillment') {

        $completeStatusId = 1; // Complete
        $orders = Order::whereIn('id', $ids)->get();
        $updated = [];

        foreach ($orders as $order) {
            $order->status_id = $completeStatusId;
            $order->save();

            $updated[] = [
                'id' => $order->id,
                'status_id' => $order->status_id,
                'order_method' => $order->order_method,
            ];
        }

        if (empty($updated)) {
            return response()->json(['message' => 'No eligible orders to complete'], 200);
        }

        return response()->json([
            'message' => 'Completed fulfillment for eligible orders',
            'completed_orders' => $updated
        ]);
    }
    elseif ($method === 'cancel_order') {
        $updated = [];
        $admin_details = User::where('role_id', 3)->first();
        $adminEmail = $admin_details->email ?? '';
    
        foreach ($orders as $order) {
            if (!$order) continue;
    
            $ordermeta = json_decode($order->ordermeta->value ?? '');
            $user_email = $ordermeta->email ?? ($order->user->email ?? '');
    
            try {
                $refundSuccess = $this->refund($order->id, true); // silent mode
    
                if ($refundSuccess) {
                    $updated[] = $order->id;
    
                    // Send notifications after successful refund
                    \App\Lib\NotifyToUser::sendEmail($order, $adminEmail, 'admin');
    
                    if ($order->notify_driver == 'mail' && $user_email) {
                        \App\Lib\NotifyToUser::sendEmail($order, $user_email, 'user');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Refund failed for order '.$order->id.': '.$e->getMessage());
            }
        }
    
        return response()->json([
            'message' => 'Cancelled and refunded eligible orders',
            'cancelled_orders' => $updated
        ]);
    }


        // 5️⃣ PENDING
        elseif ($method === 'mark_pending') {
            $pendingStatusId = 3; // Assuming 3 is pending fulfillment
            foreach ($orders as $order) {
                $order->status_id = $pendingStatusId;
                $order->save();
                $updated[] = $order->id;
            }

            return response()->json([
                'message' => 'Marked orders as pending',
                'updated_orders' => $updated
            ]);
        }


    // 4️⃣ NUMERIC STATUS CHANGE
    elseif (is_numeric($method)) {
        foreach ($orders as $order) {
            $order->update(['status_id' => $method]);
            $updated[] = $order->id;
        }
        return response()->json(['message' => 'Orders updated', 'updated_orders' => $updated]);
    }

    // 5️⃣ INVALID ACTION
    else {
        return response()->json(['error' => 'Invalid action'], 400);
    }
}

}
