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
use App\Models\EventTicket;
use Carbon\Carbon;
use App\Services\TicketCancelRefundService;
use Illuminate\Support\Collection;
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
       $orders = $orders->where('payment_status','!=',0)->latest()->paginate(30);
       
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

        // If refunded BEFORE fulfillment (not yet completed), prevent completion
        if ($request->status == 1 && $order->payment_status == 5 && $order->status_id != 1) {
            return response()->json([
                'errors' => ['This order was refunded before fulfillment and cannot be completed.'],
            ], 400);
        }

        // Check if status is set to Complete (1) and payment status is not captured
        if ($request->status == 1 && $order->payment_status != 1) {
            return response()->json([
                'errors' => ['Please capture order payment before Completing Order Fulfillment. Orders cannot be fulfilled until the payment has been captured.'],
            ], 422);
        }
            
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
            
            if($request->status == 1){
                
                // Post order data logic
                if (in_array($order->order_from, [0, 4, 5])) {
                    $this->post_order_data_POS($order);
                } else {
                    $this->post_order_data($order);
                }
            
            }
        }
    
        $order->save();
    
        // Refund logic if status = 2
        if ($request->status == 2) {
            //$this->processRefund($order);
            $this->refund($id,false);
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
    
        $admin = User::where('role_id', 3)->first();
        $order = Order::with([
            'orderstatus',
            'orderitems',
            'getway',
            'user',
            'shippingwithinfo',
            'ordermeta',
            'schedule'
        ])->findOrFail($id);
    
        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', '=', 'App\Lib\Stripe')
            ->firstOrFail();
    
        $ordermeta = json_decode($order->ordermeta->value ?? '{}');
        $gatewayData = json_decode($gateway->data ?? '{}');
    
        // Prepare payment data
        $paymentData = [
            'test_mode' => $gateway->test_mode,
            'currency'  => $gateway->currency_name ?? 'USD',
            'amount'    => $order->total,
            'transaction_id' => $order->transaction_id,
            'application_fee_amount' => $ordermeta->booster_platform_fee ?? 0,
            'card_fee_amount' => $ordermeta->credit_card_fee ?? 0,
        ];
    
        // Configure Stripe
        \Stripe\Stripe::setApiKey(
            $gateway->test_mode == 1 ? $gatewayData->test_secret_key : $gatewayData->secret_key
        );
    
        $transactionId = $paymentData['transaction_id'] ?? null;
        if (!$transactionId) {
            throw new \Exception('Transaction ID not found');
        }
    
        $paymentStatus = 0;
        $transactionLog = null;
        $paymentId = $transactionId;
    
        try {
            // --- Handle PaymentIntent (pi_) ---
            if (str_starts_with($transactionId, 'pi_')) {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);
    
                if ($paymentIntent->status === 'requires_capture') {
                    $paymentIntent = $paymentIntent->capture();
                }
    
                $paymentStatus = $paymentIntent->status === 'succeeded' ? 1 : 0;
                $transactionLog = $paymentIntent;
                $paymentId = $paymentIntent->id;
            }
    
            // --- Handle Charge (ch_) ---
            elseif (str_starts_with($transactionId, 'ch_')) {
                $charge = \Stripe\Charge::retrieve($transactionId);
                
                // Case: Legacy charge (no payment_intent)
                if (empty($charge->payment_intent)) {
                    if ($charge->status === 'pending') {
                        $charge = \Stripe\Charge::capture($transactionId);
                    }
                    $paymentStatus = $charge->status === 'succeeded' ? 1 : 0;
                    $transactionLog = $charge;
                    $paymentId = $charge->id;
                } else {
                    // Capture from PaymentIntent
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
                    if ($paymentIntent->status === 'requires_capture') {
                        $paymentIntent = $paymentIntent->capture();
                    }
                    $paymentStatus = $paymentIntent->status === 'succeeded' ? 1 : 0;
                    $transactionLog = $paymentIntent;
                    $paymentId = $paymentIntent->id;
                }
            }
    
            // --- Invalid format ---
            else {
                throw new \Exception('Invalid transaction ID format.');
            }
    
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error("Stripe InvalidRequest for order {$id}: " . $e->getMessage());
            throw new \Exception('Stripe capture error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error("Capture failed for order {$id}: " . $e->getMessage());
            throw $e;
        }
    

        // --- Update order if payment successful ---
        if ($paymentStatus === 1) {
            // $order->update([
            //     'payment_status' => 1,
            //     'status_id' => 3,
            //     'captured_at' => now()->setTimezone(config('app.timezone')),
            // ]);
            $order->payment_status = 1;
            $order->status_id = 3;
            $order->captured_at = now()->setTimezone(config('app.timezone'));
             $order->save();    
            // Log transaction
            Ordermeta::create([
                'order_id' => $order->id,
                'key' => 'transcation_log',
                'value' => json_encode($transactionLog),
            ]);
    
            $order->orderlasttrans()->update([
                'key' => 'last_transcation_log',
                'value' => json_encode($transactionLog),
            ]);
            
            // Sync to financial manager
            if (in_array($order->order_from, [0, 4, 5])) {
                $this->post_order_data_POS($order, 'capture');
            } else {
                $this->post_order_data($order, 'capture');
            }
    
            // Send notification
            \App\Lib\NotifyToUser::sendEmail($order, $admin->email, 'admin');
        }
    
        return redirect()->back();
    }





    protected function resolveOrderProcessingFees(Order $order, ?array $ordermeta = null): array
    {
        $credit_card_fee = 0;
        $booster_platform_fee = 0;

        $shippingWithInfo = $order->shippingwithinfo;
        if ($shippingWithInfo && !empty($shippingWithInfo->info)) {
            $shipping_data = json_decode($shippingWithInfo->info, true) ?: [];

            if (($shippingWithInfo->shipping_driver ?? '') === 'local') {
                $credit_card_fee = (float) ($shipping_data['credit_card_fee'] ?? 0);
                $booster_platform_fee = (float) ($shipping_data['booster_platform_fee'] ?? 0);
            }
        }

        if ($credit_card_fee == 0 && $booster_platform_fee == 0 && !empty($ordermeta)) {
            $credit_card_fee = (float) ($ordermeta['credit_card_fee'] ?? 0);
            $booster_platform_fee = (float) ($ordermeta['booster_platform_fee'] ?? 0);
        }

        return [
            'credit_card_fee' => $credit_card_fee,
            'booster_platform_fee' => $booster_platform_fee,
        ];
    }

    public function post_order_data($order,$post_type = 'capture'){

        $order_date = Carbon::parse($order->created_at)->format('Y-m-d');
        $qty = $order->orderitems[0]['qty'];
        $product_amount = $order->orderitems[0]['amount'];
        $sub_total = $product_amount*$qty;
        $sales_tax = $order->tax;
        $order_total = $order->total;

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '', true) ?: [];
        
        $name = explode(' ', $ordermeta['name'] ?? '');

        $gateway=Getway::find($order->getway_id);

         $contact_manager_data = array(
									'first_name' => $name[0] ?? '',
									'last_name' => $name[1]??'',
									'user_id' =>  $ordermeta['wpuid']??0,
									'phone_number' => $ordermeta['phone'] ?? '',					
									'booster_name' => $name[0] ?? '',
									'country' =>   $ordermeta['billing']['country'] ?? '',									
									'address_1' => $ordermeta['billing']['address'] ?? '',
									'address_2' =>  '',
									'city' => $ordermeta['billing']['city'] ?? '',
									'state' =>  $ordermeta['billing']['state'] ?? '',
									'zip' =>  $ordermeta['billing']['post_code'] ?? '',													
									'email' =>  $ordermeta['email'] ?? '',                   
									'booster_id' =>Tenant('club_id'),
									'booster_level_id' => 4,
									'contact_tags' => '',
                                    'customer_tag' => 'online store customer',
                                    'addedsource' => 'storetool',
								);	  

        $fees = $this->resolveOrderProcessingFees($order, $ordermeta);
        $credit_card_fee = $fees['credit_card_fee'];
        $booster_platform_fee = $fees['booster_platform_fee'];
        $processing_fees = $credit_card_fee + $booster_platform_fee;

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
        } else {
            $ordermeta = [];
        }

        $gateway=Getway::find($order->getway_id);

        $fees = $this->resolveOrderProcessingFees($order, is_array($ordermeta) ? $ordermeta : null);
        $credit_card_fee = $fees['credit_card_fee'];
        $booster_platform_fee = $fees['booster_platform_fee'];
        $processing_fees = $credit_card_fee + $booster_platform_fee;

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
        abort_if(!getpermission('order'), 401);

        if (!$silent && request()->has('partial_refund_payment')) {
            return $this->processPartialRefund($id);
        }

        $admin_details = User::where('role_id', 3)->first();
        $to = $admin_details->email ?? '';

        $order = Order::with('orderstatus', 'orderlasttrans', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')->findOrFail($id);

        if ((int) $order->payment_status === 5) {
            return $this->refundResponse($silent, false, 'This order has already been refunded.');
        }

        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', '=', 'App\Lib\Stripe')
            ->first();

        if (!$gateway) {
            return $this->refundResponse($silent, false, 'Stripe payment gateway is not configured.');
        }

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $gateway_data_info = json_decode($gateway->data ?? '{}');

        if (!$gateway_data_info) {
            return $this->refundResponse($silent, false, 'Stripe gateway credentials are missing.');
        }

        try {
            Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);

            $transactionId = $order->transaction_id;
            if (!$transactionId) {
                throw new \Exception('No transaction ID provided');
            }

            $chargeId = null;
            if (str_starts_with($transactionId, 'pi_')) {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);

                if ($paymentIntent->status !== 'succeeded') {
                    throw new \Exception('Payment is not in a refundable state.');
                }

                $chargeId = $paymentIntent->latest_charge;
            } elseif (str_starts_with($transactionId, 'ch_')) {
                $chargeId = $transactionId;
            } else {
                throw new \Exception('Invalid transaction ID format');
            }

            if (!$chargeId) {
                throw new \Exception('Unable to resolve Stripe charge for refund.');
            }

            $charge = \Stripe\Charge::retrieve($chargeId);

            if ($charge->refunded) {
                $cancelledTickets = $this->finalizeRefundedOrder($order, $charge->toArray());

                $refundAmountDollars = $this->calculateRefundNetTotal($order, $ordermeta);
                $stripeRefundId = $charge->refunds->data[0]->id ?? null;

                return $this->refundResponse($silent, true, 'Order refunded successfully', $order, $to, $refundAmountDollars, $stripeRefundId, $cancelledTickets);
            }

            $refundAmountDollars = $this->calculateRefundNetTotal($order, $ordermeta);
            $coverFee = (float) ($ordermeta->cover_fee ?? 0);

            $refundAmountCents = max(1, (int) round($refundAmountDollars * 100));

            $refundParams = [
                'charge' => $chargeId,
                'amount' => $refundAmountCents,
            ];

            if ($coverFee <= 0) {
                $refundParams['refund_application_fee'] = true;
            }

            $refundParams['reverse_transfer'] = true;

            $refund = \Stripe\Refund::create($refundParams);

            if (!in_array($refund->status, ['succeeded', 'pending'], true)) {
                throw new \Exception('Stripe refund was not completed.');
            }

            $cancelledTickets = $this->finalizeRefundedOrder($order, $refund->toArray());

            return $this->refundResponse($silent, true, 'Order refunded successfully', $order, $to, $refundAmountDollars, $refund->id, $cancelledTickets);
        } catch (\Throwable $e) {
            \Log::error('Order refund failed', [
                'order_id' => $id,
                'transaction_id' => $order->transaction_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return $this->refundResponse($silent, false, $e->getMessage());
        }
    }

    protected function finalizeRefundedOrder(Order $order, array $transactionLog): Collection
    {
        $order->payment_status = 5;
        $order->status_id = $order->status_id == 1 ? 1 : 2;
        $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));
        $order->save();

        $transcation_log = new Ordermeta;
        $transcation_log->order_id = $order->id;
        $transcation_log->key = 'transcation_log';
        $transcation_log->value = json_encode($transactionLog);
        $transcation_log->save();

        if ($order->orderlasttrans) {
            $order->orderlasttrans()->update([
                'key' => 'last_transcation_log',
                'value' => json_encode($transactionLog),
            ]);
        }

        return $this->cancelEventTicketsForOrder($order);
    }

    protected function cancelEventTicketsForOrder(Order $order, ?array $refundedItems = null): Collection
    {
        $cancelledTickets = collect();

        if ($refundedItems === null) {
            $tickets = EventTicket::where('order_id', $order->id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('id')
                ->get();

            foreach ($tickets as $ticket) {
                $ticket->status = 'cancelled';
                $ticket->used_at = null;
                $ticket->save();
                $cancelledTickets->push($ticket);
            }

            return $cancelledTickets;
        }

        foreach ($refundedItems as $item) {
            $itemId = (int) ($item['item_id'] ?? 0);
            $qty = (int) ($item['qty'] ?? 0);

            if ($itemId <= 0 || $qty <= 0) {
                continue;
            }

            $tickets = EventTicket::where('order_id', $order->id)
                ->where('order_item_id', $itemId)
                ->where('status', '!=', 'cancelled')
                ->orderBy('id')
                ->limit($qty)
                ->get();

            foreach ($tickets as $ticket) {
                $ticket->status = 'cancelled';
                $ticket->used_at = null;
                $ticket->save();
                $cancelledTickets->push($ticket);
            }
        }

        return $cancelledTickets;
    }

    protected function sendTicketCancelledRefundEmails(
        Order $order,
        Collection $cancelledTickets,
        ?float $orderRefundTotal = null,
        ?string $stripeRefundId = null,
        ?array $refundDetails = null
    ): void {
        if ($cancelledTickets->isEmpty()) {
            return;
        }

        $context = [
            'reference_id' => $this->buildRefundReferenceId($order, $stripeRefundId),
            'order_refund_total' => $orderRefundTotal,
            'item_refunds' => $this->buildTicketRefundItemMap($refundDetails['items'] ?? []),
        ];

        try {
            app(TicketCancelRefundService::class)->sendCancelledEmailsForTickets($cancelledTickets, $order, $context);
        } catch (\Throwable $e) {
            \Log::error('Ticket cancel refund emails failed after order refund.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function buildTicketRefundItemMap(array $refundItems): array
    {
        $itemRefunds = [];

        foreach ($refundItems as $item) {
            $itemId = (int) ($item['item_id'] ?? 0);
            $qty = max(1, (int) ($item['qty'] ?? 1));

            if ($itemId <= 0) {
                continue;
            }

            $itemRefunds[$itemId] = [
                'amount_per_unit' => round((float) ($item['amount'] ?? 0) / $qty, 2),
                'tax_per_unit' => round((float) ($item['tax'] ?? 0) / $qty, 2),
            ];
        }

        return $itemRefunds;
    }

    protected function calculateRefundNetTotal(Order $order, $ordermeta): float
    {
        $ordermeta = is_array($ordermeta) ? (object) $ordermeta : $ordermeta;
        $applicationFee = (float) ($ordermeta->booster_platform_fee ?? 0);
        $cardFee = (float) ($ordermeta->credit_card_fee ?? 0);
        $coverFee = (float) ($ordermeta->cover_fee ?? 0);

        return $coverFee > 0
            ? ((float) $order->total - $coverFee)
            : ((float) $order->total - $applicationFee - $cardFee);
    }

    protected function buildRefundReferenceId(Order $order, ?string $stripeRefundId = null): string
    {
        $suffix = '0000';

        if (!empty($stripeRefundId)) {
            $digits = preg_replace('/\D/', '', $stripeRefundId);
            $suffix = substr($digits, -4) ?: substr(md5($stripeRefundId), 0, 4);
        } else {
            $log = json_decode(optional($order->orderlasttrans)->value ?? '{}', true);
            $logId = is_array($log) ? ($log['id'] ?? null) : null;

            if (!empty($logId)) {
                $digits = preg_replace('/\D/', '', $logId);
                $suffix = substr($digits, -4) ?: substr(md5($logId), 0, 4);
            }
        }

        return $order->invoice_no . 'rfd' . str_pad($suffix, 4, '0', STR_PAD_LEFT);
    }

    protected function refundResponse(bool $silent, bool $success, string $message, ?Order $order = null, ?string $adminEmail = null, ?float $refundAmount = null, ?string $stripeRefundId = null, ?Collection $cancelledTickets = null)
    {
        if ($success && $order) {
            $order = Order::with('orderstatus', 'orderlasttrans', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')->findOrFail($order->id);

            try {
                $this->post_order_data($order, 'refund');
            } catch (\Throwable $e) {
                \Log::error('post_order_data failed after refund', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            if (!$silent) {
                $this->sendTicketCancelledRefundEmails(
                    $order,
                    $cancelledTickets ?? collect(),
                    $refundAmount ?? $this->calculateRefundNetTotal($order, json_decode(optional($order->ordermeta)->value ?? '{}')),
                    $stripeRefundId
                );

                NotifyToUser::sendEmail($order, $adminEmail ?? '', 'admin');

                if ($order->notify_driver == 'mail') {
                    $ordermeta = json_decode($order->ordermeta->value ?? '');
                    if (!empty($ordermeta)) {
                        $mail_to = $ordermeta->email ?? '';
                    } else {
                        $mail_to = $order->user->email ?? '';
                    }

                    if (!empty($mail_to)) {
                        NotifyToUser::sendEmail($order, $mail_to, 'user');
                    }
                }
            }

            if ($silent) {
                return true;
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'refund' => [
                        'invoice_no' => $order->invoice_no,
                        'amount' => $refundAmount ?? $this->calculateRefundNetTotal($order, json_decode(optional($order->ordermeta)->value ?? '{}')),
                        'reference_id' => $this->buildRefundReferenceId($order, $stripeRefundId),
                    ],
                ]);
            }

            $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}', true);
            $receiptEmail = $ordermeta['email'] ?? $order->user->email ?? '';

            return redirect()->back()->with('refund_success', [
                'invoice_no' => $order->invoice_no,
                'amount' => $refundAmount ?? $this->calculateRefundNetTotal($order, (object) $ordermeta),
                'email' => $receiptEmail,
                'reference_id' => $this->buildRefundReferenceId($order, $stripeRefundId),
            ]);
        }

        if ($silent) {
            throw new \Exception($message);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return redirect()->back()->with('error', $message);
    }

    public function processPartialRefund($id)
    {
        abort_if(!getpermission('order'), 401);

        $admin_details = User::where('role_id', 3)->first();
        $adminEmail = $admin_details->email ?? '';

        $order = Order::with('orderstatus', 'orderlasttrans', 'orderitems.term', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')->findOrFail($id);

        if ((int) $order->payment_status === 5) {
            return $this->partialRefundResponse(false, 'This order has already been fully refunded.');
        }

        $selectedItems = json_decode(request()->input('partial_refund_items', '[]'), true);
        if (!is_array($selectedItems) || empty($selectedItems)) {
            return $this->partialRefundResponse(false, 'Please select at least one item to refund.');
        }

        try {
            $refundDetails = $this->buildPartialRefundDetails($order, $selectedItems);
        } catch (\Throwable $e) {
            return $this->partialRefundResponse(false, $e->getMessage());
        }

        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', '=', 'App\Lib\Stripe')
            ->first();

        if (!$gateway) {
            return $this->partialRefundResponse(false, 'Stripe payment gateway is not configured.');
        }

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $gateway_data_info = json_decode($gateway->data ?? '{}');

        if (!$gateway_data_info) {
            return $this->partialRefundResponse(false, 'Stripe gateway credentials are missing.');
        }

        $netRefundable = $this->calculateRefundNetTotal($order, $ordermeta);
        $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);

        if (($alreadyRefunded + $refundDetails['grand_total']) > ($netRefundable + 0.01)) {
            return $this->partialRefundResponse(false, 'Refund amount exceeds the remaining refundable balance for this order.');
        }

        try {
            Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_secret_key : $gateway_data_info->secret_key);

            $transactionId = $order->transaction_id;
            if (!$transactionId) {
                throw new \Exception('No transaction ID provided');
            }

            $chargeId = null;
            if (str_starts_with($transactionId, 'pi_')) {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);

                if ($paymentIntent->status !== 'succeeded') {
                    throw new \Exception('Payment is not in a refundable state.');
                }

                $chargeId = $paymentIntent->latest_charge;
            } elseif (str_starts_with($transactionId, 'ch_')) {
                $chargeId = $transactionId;
            } else {
                throw new \Exception('Invalid transaction ID format');
            }

            if (!$chargeId) {
                throw new \Exception('Unable to resolve Stripe charge for refund.');
            }

            $refundAmountCents = max(1, (int) round($refundDetails['grand_total'] * 100));

            $refundParams = [
                'charge' => $chargeId,
                'amount' => $refundAmountCents,
                'reverse_transfer' => true,
            ];

            $refund = \Stripe\Refund::create($refundParams);

            if (!in_array($refund->status, ['succeeded', 'pending'], true)) {
                throw new \Exception('Stripe refund was not completed.');
            }

            $cancelledTickets = $this->finalizePartialRefundedOrder($order, $refund->toArray(), $refundDetails);

            return $this->partialRefundResponse(true, 'Partial refund completed successfully', $order, $refundDetails, $refund->id, $adminEmail, $cancelledTickets);
        } catch (\Throwable $e) {
            \Log::error('Partial order refund failed', [
                'order_id' => $id,
                'transaction_id' => $order->transaction_id ?? null,
                'error' => $e->getMessage(),
            ]);

            return $this->partialRefundResponse(false, $e->getMessage());
        }
    }

    protected function getOrderItemUnitAmount($orderItem): float
    {
        $variations = json_decode($orderItem->info ?? '');
        $options = $variations->options ?? [];
        $amount = (float) $orderItem->amount;

        if (!is_array($options) && is_object($options) && isset($options->varition_options)) {
            $amount = (float) $options->price;
        }

        return $amount;
    }

    protected function getOrderItemsSubtotal(Order $order): float
    {
        $subtotal = 0;

        foreach ($order->orderitems as $orderItem) {
            $subtotal += $this->getOrderItemUnitAmount($orderItem) * (int) $orderItem->qty;
        }

        return $subtotal;
    }

    protected function getPartialRefundedQuantities(Order $order): array
    {
        $meta = Ordermeta::where('order_id', $order->id)->where('key', 'partial_refunded_items')->first();

        return json_decode($meta->value ?? '{}', true) ?: [];
    }

    protected function getTotalPartialRefundedAmount(Order $order): float
    {
        $meta = Ordermeta::where('order_id', $order->id)->where('key', 'partial_refund_logs')->first();
        $logs = json_decode($meta->value ?? '[]', true) ?: [];

        return (float) array_sum(array_map(function ($log) {
            return (float) ($log['amount'] ?? 0);
        }, $logs));
    }

    protected function buildPartialRefundDetails(Order $order, array $selectedItems): array
    {
        $subtotal = $this->getOrderItemsSubtotal($order);
        $refundedQuantities = $this->getPartialRefundedQuantities($order);
        $items = [];
        $itemTotal = 0;
        $taxTotal = 0;

        foreach ($selectedItems as $selectedItem) {
            $itemId = (int) ($selectedItem['item_id'] ?? 0);
            $qty = (int) ($selectedItem['qty'] ?? 0);

            if ($itemId <= 0 || $qty <= 0) {
                throw new \Exception('Invalid item selection for partial refund.');
            }

            $orderItem = $order->orderitems->firstWhere('id', $itemId);
            if (!$orderItem) {
                throw new \Exception('One or more selected items were not found on this order.');
            }

            $alreadyRefundedQty = (int) ($refundedQuantities[$itemId] ?? 0);
            $remainingQty = (int) $orderItem->qty - $alreadyRefundedQty;

            if ($qty > $remainingQty) {
                throw new \Exception('Selected quantity exceeds the remaining refundable quantity for one or more items.');
            }

            $unitAmount = $this->getOrderItemUnitAmount($orderItem);
            $lineTotal = $unitAmount * (int) $orderItem->qty;
            $lineTaxTotal = $subtotal > 0 ? (($lineTotal / $subtotal) * (float) ($order->tax ?? 0)) : 0;
            $refundLineAmount = $unitAmount * $qty;
            $refundLineTax = ((int) $orderItem->qty > 0)
                ? ($lineTaxTotal / (int) $orderItem->qty) * $qty
                : 0;

            $items[] = [
                'item_id' => $itemId,
                'qty' => $qty,
                'label' => $qty . ' x ' . ($orderItem->term->title ?? 'Item'),
                'amount' => round($refundLineAmount, 2),
                'tax' => round($refundLineTax, 2),
            ];

            $itemTotal += $refundLineAmount;
            $taxTotal += $refundLineTax;
        }

        if (empty($items)) {
            throw new \Exception('Please select at least one item to refund.');
        }

        return [
            'items' => $items,
            'item_total' => round($itemTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($itemTotal + $taxTotal, 2),
        ];
    }

    protected function finalizePartialRefundedOrder(Order $order, array $transactionLog, array $refundDetails): Collection
    {
        $refundedQuantities = $this->getPartialRefundedQuantities($order);

        foreach ($refundDetails['items'] as $item) {
            $itemId = (int) $item['item_id'];
            $refundedQuantities[$itemId] = (int) ($refundedQuantities[$itemId] ?? 0) + (int) $item['qty'];
        }

        $partialRefundedItemsMeta = Ordermeta::firstOrNew([
            'order_id' => $order->id,
            'key' => 'partial_refunded_items',
        ]);
        $partialRefundedItemsMeta->value = json_encode($refundedQuantities);
        $partialRefundedItemsMeta->save();

        $partialRefundLogsMeta = Ordermeta::where('order_id', $order->id)->where('key', 'partial_refund_logs')->first();
        $logs = json_decode($partialRefundLogsMeta->value ?? '[]', true) ?: [];
        $logs[] = [
            'amount' => $refundDetails['grand_total'],
            'items' => $refundDetails['items'],
            'stripe_refund_id' => $transactionLog['id'] ?? null,
            'refunded_at' => Carbon::now()->setTimezone(config('app.timezone'))->toDateTimeString(),
        ];

        if ($partialRefundLogsMeta) {
            $partialRefundLogsMeta->value = json_encode($logs);
            $partialRefundLogsMeta->save();
        } else {
            $partialRefundLogsMeta = new Ordermeta;
            $partialRefundLogsMeta->order_id = $order->id;
            $partialRefundLogsMeta->key = 'partial_refund_logs';
            $partialRefundLogsMeta->value = json_encode($logs);
            $partialRefundLogsMeta->save();
        }

        $transcation_log = new Ordermeta;
        $transcation_log->order_id = $order->id;
        $transcation_log->key = 'transcation_log';
        $transcation_log->value = json_encode($transactionLog);
        $transcation_log->save();

        if ($order->orderlasttrans) {
            $order->orderlasttrans()->update([
                'key' => 'last_transcation_log',
                'value' => json_encode($transactionLog),
            ]);
        }

        $allItemsFullyRefunded = true;
        foreach ($order->orderitems as $orderItem) {
            $refundedQty = (int) ($refundedQuantities[$orderItem->id] ?? 0);
            if ($refundedQty < (int) $orderItem->qty) {
                $allItemsFullyRefunded = false;
                break;
            }
        }

        if ($allItemsFullyRefunded) {
            $order->payment_status = 5;
            $order->status_id = $order->status_id == 1 ? 1 : 2;
            $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));
        }

        $order->save();

        return $this->cancelEventTicketsForOrder($order, $refundDetails['items'] ?? []);
    }

    protected function partialRefundResponse(bool $success, string $message, ?Order $order = null, ?array $refundDetails = null, ?string $stripeRefundId = null, ?string $adminEmail = null, ?Collection $cancelledTickets = null)
    {
        if ($success && $order && $refundDetails) {
            $order = Order::with('orderstatus', 'orderlasttrans', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')->findOrFail($order->id);

            try {
                $this->post_order_data($order, 'refund');
            } catch (\Throwable $e) {
                \Log::error('post_order_data failed after partial refund', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->sendTicketCancelledRefundEmails(
                $order,
                $cancelledTickets ?? collect(),
                $refundDetails['grand_total'] ?? null,
                $stripeRefundId,
                $refundDetails
            );

            NotifyToUser::sendEmail($order, $adminEmail ?? '', 'admin');

            if ($order->notify_driver == 'mail') {
                $ordermeta = json_decode($order->ordermeta->value ?? '');
                $mail_to = $ordermeta->email ?? $order->user->email ?? '';

                if (!empty($mail_to)) {
                    NotifyToUser::sendEmail($order, $mail_to, 'user');
                }
            }

            $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}', true);
            $receiptEmail = $ordermeta['email'] ?? $order->user->email ?? '';

            return redirect()->back()->with('partial_refund_success', [
                'invoice_no' => $order->invoice_no,
                'items' => $refundDetails['items'],
                'tax_total' => $refundDetails['tax_total'],
                'amount' => $refundDetails['grand_total'],
                'email' => $receiptEmail,
                'reference_id' => $this->buildRefundReferenceId($order, $stripeRefundId),
            ]);
        }

        return redirect()->back()->with('error', $message);
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
            $admin_details = User::where('role_id', 3)->first();
            $adminEmail = $admin_details->email ?? '';
        
            $gateway = Getway::where('status', '!=', 0)
                ->where('namespace', '=', 'App\Lib\Stripe')->first();
        
            if (!$gateway) {
                return response()->json(['error' => 'Stripe gateway not found'], 400);
            }
        
            $gateway_data_info = json_decode($gateway->data);
        
            foreach ($orders as $order) {
                if (in_array($order->risk_level, ['normal', 'low']) && in_array($order->payment_status, [4, 5])) {
                    $status = 3; // Default to pending fulfillment
        
                    if ($order->payment_status == 5) {
                        // Already refunded, just update status
                        $order->status_id = $status;
                        $order->save();
                        $updated[] = $order->id;
                        continue;
                    }
        
                    // Capture authorized payment
                    $ordermeta = json_decode($order->ordermeta->value ?? '');
                    $payment_data = [
                        'test_mode' => $gateway->test_mode,
                        'currency' => $gateway->currency_name ?? 'USD',
                        'getway_id' => $gateway->id,
                        'amount' => $order->total,
                        'transaction_id' => $order->transaction_id,
                        'application_fee_amount' => $ordermeta->booster_platform_fee ?? 0,
                        'card_fee_amount' => $ordermeta->credit_card_fee ?? 0,
                    ];
        
                    if (!empty($gateway->data)) {
                        foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                            $payment_data[$key] = $info;
                        }
                    }
        
                    Stripe::setApiKey($gateway->test_mode == 1 
                        ? $gateway_data_info->test_secret_key 
                        : $gateway_data_info->secret_key);
        
                    $transactionId = $payment_data['transaction_id'] ?? null;
                    if (!$transactionId) continue;
        
                    $paymentstatus = 0;
                    $transaction_log = null;
                    $payment_id = $transactionId;
        
                    try {
                        $paymentIntent = null;
                        $charge = null;
        
                        if (str_starts_with($transactionId, 'pi_')) {
                            $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);
        
                            if ($paymentIntent->status === 'requires_capture') {
                                $captured = $paymentIntent->capture();
                                $paymentstatus = $captured->status === 'succeeded' ? 1 : 0;
                                $transaction_log = $captured;
                            } else {
                                $paymentstatus = $paymentIntent->status === 'succeeded' ? 1 : 0;
                                $transaction_log = $paymentIntent;
                            }
                            $payment_id = $paymentIntent->id;
        
                        } elseif (str_starts_with($transactionId, 'ch_')) {
                            $charge = \Stripe\Charge::retrieve($transactionId);
        
                            // Handle legacy charges without PaymentIntent
                            if (empty($charge->payment_intent)) {
                                if ($charge->refunded) {
                                    \Log::info('Charge already refunded for order ' . $order->id . ', syncing local status');

                                    $order->payment_status = 5;
                                    $order->status_id = 2; // Assuming 2 is refunded/canceled
                                    $order->refunded_at = now();
                                    $order->save();

                                    $updated[] = $order->id;
                                    continue;
                                }
                                if ($charge->status === 'succeeded' && $charge->captured) {
                                    $paymentstatus = 1;
                                    $transaction_log = $charge;
                                } elseif ($charge->status === 'succeeded' && !$charge->captured) {
                                    $captured_charge = $charge->capture();
                                    $paymentstatus = $captured_charge->status === 'succeeded' ? 1 : 0;
                                    $transaction_log = $captured_charge;
                                } else {
                                    throw new \Exception('Charge not in a capturable state: ' . $charge->status);
                                }
                            } else {
                                // Has PaymentIntent, proceed as before
                                $paymentIntent = \Stripe\PaymentIntent::retrieve($charge->payment_intent);
        
                                if ($paymentIntent->status === 'requires_capture') {
                                    $captured = $paymentIntent->capture();
                                    $paymentstatus = $captured->status === 'succeeded' ? 1 : 0;
                                    $transaction_log = $captured;
                                } else {
                                    $paymentstatus = $paymentIntent->status === 'succeeded' ? 1 : 0;
                                    $transaction_log = $paymentIntent;
                                }
                                $payment_id = $paymentIntent->id;
                            }
        
                        } else {
                            throw new \Exception('Invalid transaction ID format');
                        }
        
                        if ($paymentstatus == 1) {

                            $order->payment_status = $paymentstatus;
                            $order->status_id = $status;
                            $order->captured_at = now();
                            $order->save();

        
                            $logData = json_encode([
                                'payment_status' => $paymentstatus,
                                'payment_id' => $payment_id,
                                'transaction_log' => $transaction_log,
                            ]);
        
                            $transcation_log = new Ordermeta;
                            $transcation_log->order_id = $order->id;
                            $transcation_log->key = 'transcation_log';
                            $transcation_log->value = $logData;
                            $transcation_log->save();
        
                            $order->orderlasttrans()->update([
                                'key' => 'last_transcation_log',
                                'value' => $logData,
                            ]);
        
                            $post_type = 'capture';
                            if (in_array($order->order_from, [0, 4, 5])) {
                                $this->post_order_data_POS($order, $post_type);
                            } else {
                                $this->post_order_data($order, $post_type);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error capturing payment for order ' . $order->id . ': ' . $e->getMessage());
                        continue;
                    }
        
                    // Notifications
                    if ($adminEmail) {
                        \App\Lib\NotifyToUser::sendEmail($order, $adminEmail, 'admin');
                    }
                    if ($order->notify_driver == 'mail') {
                        $userTo = json_decode($order->ordermeta->value ?? '{}')->email ?? $order->user->email ?? '';
                        if ($userTo) \App\Lib\NotifyToUser::sendEmail($order, $userTo, 'user');
                    }
                    $updated[] = $order->id;
                }
            }
        
        $message = empty($updated) 
                ? 'No eligible low-risk orders.'
                : 'Captured authorized low-risk orders.';
        
            return response()->json([
                'message' => $message,
                'captured_orders' => $updated
            ]);
        }

        // 3️⃣ COMPLETE FULFILLMENT
        elseif ($method === 'complete_fulfillment') {

        $completeStatusId = 1; // Complete
    
        // product_type categories
        $product_type = Category::where('type', 'product_type')->select('id','slug')->get();
        $p_types = $product_type->pluck('id')->all();
    
        // load nested relations (important)
        $orders = Order::with(['orderitems.term.termcategories'])
            ->whereIn('id', $ids)
            ->get();
    
        $updated = [];
    
        foreach ($orders as $order) {
    
            // ✅ Paid only
            if ((int)$order->payment_status !== 1) continue;
    
            // ❌ Skip refunded before fulfillment
            if ((int)$order->payment_status === 5 && (int)$order->status_id !== 1) continue;
    
            // ✅ Decide Digital/Goods/Mixed from items (same as blade)
            $selected_product_type = collect($order->orderitems ?? [])
                ->flatMap(fn($item) => $item->term?->termcategories?->pluck('category_id') ?? [])
                ->filter(fn($id) => in_array($id, $p_types))
                ->unique()
                ->values()
                ->all();
    
            // Must be exactly one product_type and it must be digital_product
            if (count($selected_product_type) !== 1) continue;
    
            $pt = $product_type->firstWhere('id', $selected_product_type[0]);
            if (!$pt || $pt->slug !== 'digital_product') continue;
    
            // ✅ Now mark complete
            $order->status_id = $completeStatusId;
            $order->save();
    
            if (in_array($order->order_from, [0, 4, 5])) {
                $this->post_order_data_POS($order, 'capture');
            } else {
                $this->post_order_data($order, 'capture');
            }
    
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
