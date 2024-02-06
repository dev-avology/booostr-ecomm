<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Productoption;
use App\Models\Category;
use App\Models\Term;
use App\Models\User;
use App\Models\Getway;
use App\Models\Location;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Price;
use Cookie;
use App\Models\Option;
use Carbon\Carbon;
use App\Models\Orderstock;
use Illuminate\Support\Facades\Session;
use Cart;
use DB;
use Auth;
use Validator;
use Exception;

class PosApiController extends Controller
{

/**
 * @OA\Post(
 *     path="/api/storedata/get_pos_category_list",
 *     tags={"Store"},
 *     summary="POS category list",
 *     operationId="getPosCategoryList",
 *     @OA\RequestBody(
 *         required=false,
 *         description="No request body for this endpoint",
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categories list successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="categories",
 *                 type="array",
 *                 description="List of categories",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */




    public function getPosCategoryList(Request $request){
       $posts = Category::where('type', 'category')->whereNull('category_id')
        ->with('preview', 'icon','recursiveChildren')
        ->withCount('products')
        ->whereDoesntHave('show_on', function ($query) {
            $query->where('type', 'show_on')->where('content', 'ecommerse_only');
        })
        ->get();

       $product_count = Term::query()->where('type', 'product')->where('status', 1)
       ->whereIn('list_type', [2])->with('media', 'firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice')->count();

       return response()->json(["status" => true, "message" => "Category list fetched successfully", "result" => ['categories'=>$posts,'product_count'=>$product_count]]);
    }



 /**
 * @OA\Post(
 *     path="/api/storedata/get_pos_product_list",
 *     tags={"Store"},
 *     summary="POS product list",
 *     operationId="posProductList",
 *     @OA\RequestBody(
 *         required=false,
 *         description="No request body for this endpoint",
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product list successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 description="List of products",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */





    public function posProductList(Request $request)
    {
       $posts = Term::query()->where('type', 'product')->where('status', 1)
       ->whereIn('list_type', [2])->with('media','category','firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice')->selectRaw('*, (SELECT MAX(price) FROM prices WHERE term_id = terms.id) AS max_price, (SELECT MIN(price) FROM prices WHERE term_id = terms.id) AS min_price');

        if (!empty($request->category_id) && $request->category_id != 'all') {
            $posts = $posts->whereHas('termcategories', function ($query) use ($request) {
                return $query->where('category_id', $request->category_id);
            });
        }

        $posts = $posts->latest()->paginate(50);
        return response()->json(["status" => true, "message" => "products", "result" => $posts]);
    }


 /**
 * @OA\Get(
 *     path="/api/storedata/pos-product/{id}",
 *     tags={"Store"},
 *     summary="POS product details",
 *     operationId="getPosProductDetails",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="product",
 *                 type="array",
 *                 description="Product details",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */


    
    public function posProductDetail(Request $request,$id)
    {
        $info=Term::query()->where('type','product')->where('status',1)->whereIn('list_type', [2])->with('tags','brands','excerpt','description','preview','medias','optionwithcategories','price','prices','seo')->withCount('reviews')->where('id', $id)->first();
        if(empty($info)){
            return response()->json(["status" => false, "message" => "sorry, product not found", "result" => []],404);
        }
        $medias=json_decode($info->medias->value ?? '');
        $preview=asset($info->preview->value ?? 'uploads/default.png');
        $galleries=[];
        array_push($galleries,$preview);

        foreach($medias ?? [] as $row){
            array_push($galleries,asset($row));
        }
        unset($info->medias);
        unset($info->preview);
        $info->gallery=$galleries;
        return response()->json(["status" => true, "message" => "products", "result" =>$info,"galleries"=>$galleries]);
        
    }


 /**
 * @OA\Post(
 *     path="/api/storedata/pos-make-order",
 *     tags={"Store"},
 *     summary="POS Make Order",
 *     operationId="posMakeOrder",
 *     @OA\RequestBody(
 *         required=true,
 *         description="POS Make Order request body",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="total",
 *                     type="number",
 *                     format="double",
 *                     description="Total amount (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="subtotal",
 *                     type="number",
 *                     format="double",
 *                     description="Subtotal amount (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="order_method",
 *                     type="string",
 *                     description="Order method (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="tax_amount",
 *                     type="number",
 *                     format="double",
 *                     description="Tax amount (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="transaction_id",
 *                     type="string",
 *                     description="Transaction ID (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="shipping_method",
 *                     type="string",
 *                     description="Shipping method (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="wpuid",
 *                     type="string",
 *                     description="WP User ID (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     description="Customer name (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     description="Customer email (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string",
 *                     description="Customer phone number (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="coupon_code",
 *                     type="string",
 *                     description="Coupon code",
 *                 ),
 *                 @OA\Property(
 *                     property="discount",
 *                     type="string",
 *                     description="Discount amount",
 *                 ),
 *                 @OA\Property(
 *                     property="order_items",
 *                     type="array",
 *                     description="List of order items",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="term_id", type="integer", description="Product term ID (required)"),
 *                         @OA\Property(property="qty", type="integer", description="Quantity (required)"),
 *                         @OA\Property(property="amount", type="number", format="double", description="Item amount (required)"),
 *                         @OA\Property(property="variation_id", type="integer", description="Variation ID"),
 *                     ),
 *                 ),
 *                 @OA\Property(
 *                     property="billing",
 *                     type="array",
 *                     description="Billing address details",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="address", type="string", description="Billing address (required)"),
 *                         @OA\Property(property="city", type="string", description="Billing city (required)"),
 *                         @OA\Property(property="state", type="string", description="Billing state (required)"),
 *                         @OA\Property(property="country", type="string", description="Billing country (required)"),
 *                         @OA\Property(property="post_code", type="string", description="Billing post code (required)"),
 *                     ),
 *                 ),
 *                 @OA\Property(
 *                     property="shipping",
 *                     type="array",
 *                     description="Shipping address details",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="address", type="string", description="Shipping address (required)"),
 *                         @OA\Property(property="city", type="string", description="Shipping city (required)"),
 *                         @OA\Property(property="state", type="string", description="Shipping state (required)"),
 *                         @OA\Property(property="country", type="string", description="Shipping country (required)"),
 *                         @OA\Property(property="post_code", type="string", description="Shipping post code (required)"),
 *                     ),
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */




    public function posMakeOrder(Request $request){

        $rules = [
            'total' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'tax_amount' => 'required|numeric',
        
            'order_method' => 'required|string',
            'transaction_id' => 'required|string',

            'shipping_method' => 'required',
            'wpuid'         => 'required',

            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',


            'billing' => 'required|array',
            'billing.*.address' => 'required',
            'billing.*.city' => 'required',
            'billing.*.state' => 'required',
            'billing.*.country' => 'required',
            'billing.*.post_code' => 'required',

            'order_items' => 'required|array',
            'order_items.*.term_id' => 'required|integer',
            'order_items.*.qty' => 'required|integer',
            'order_items.*.amount' => 'required|numeric',
        ];
         
        $validator = Validator::make($request->all(), $rules);
        
        if ($request->shipping_method == 'per_item') {
            $rules['cart_count'] = 'required';
        }
        
        if ($request->shipping_method == 'weight_based') {
            $rules['weight'] = 'required';
        }

        if (!empty($request->coupon_code)) {

            $coupon = Coupon::where('code',$request->coupon_code)->first();
            
            if(!$coupon){
                return response()->json(['error'=>false,'message'=>'Invailid coupon code']);
            }

            if(empty($request->discount)){
                $rules['discount'] = 'required|numeric';
            }
        }
        
        if ($request->order_method == 'delivery') {
            $rules['shipping'] = 'required|array';
            $rules['shipping.*.address'] = 'required';
            $rules['shipping.*.city'] = 'required';
            $rules['shipping.*.state'] = 'required';
            $rules['shipping.*.country'] = 'required';
            $rules['shipping.*.post_code'] = 'required';
        }
        
        if ($request->order_method == 'table') {
            $rules['table'] = 'required';
        }
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        
        $order_method='delivery';
 
        $subtotal = $request->subtotal;
        $total_amount=$request->total;
      
        $shipping_price = 0;
        $shipping_method_label = '';
        if ($request->order_method == 'delivery' && !empty($request->shipping_method)) {
        
        if($request->shipping_method == 'free_shipping'){
            $shipping_price = 0;
            $shipping_method_label = 'Free Shipping';
            }else{

            $shippingDetails= json_decode(Option::where('key','shipping_method')->first()->value,true);

            if($shippingDetails['method_type'] == 'per_item'){

                $shipping_price = $shippingDetails['base_pricing'] + $request->cart_count * $shippingDetails['pricing'];

                $shipping_method_label = $shippingDetails['label'];

            }else if($shippingDetails['method_type'] == 'weight_based'){

                $shipping_price = $shippingDetails['base_pricing'] + $request->weight * $shippingDetails['pricing'];
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

        $total_amount =  $total_amount + $shipping_price;

        // if($request->coupon_code){
        //     $total_amount =  $total_amount - $request->discount;
        // }

        $credit_card_fee = credit_card_fee($total_amount);

        $booster_platform_fee = booster_club_chagre($total_amount);

        DB::beginTransaction();
        try {
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

            // $tax=Option::where('key','tax')->first();
            // $tax = $tax->value ? $tax->value: 0.00;
            // $taxAmount = ($subtotal*$tax)/100;
            // $total_amount = $total_amount+$taxAmount;
             
            $order = new Order;
            $order->user_id = $user->id;
 
            $notify_driver = 'mail';

            $order->getway_id = null;
            $order->status_id = 3;
            $order->tax = $request->tax_amount ?? 0;

            $order->discount = $request->discount ?? 0;
            $order->coupon_code = $request->coupon_code ?? null;

            $order->total = $total_amount ?? 0;
            $order->order_method = $order_method ?? 'delivery';
            $order->order_from = 4;
            $order->notify_driver = $notify_driver;
            $order->transaction_id = $request->transaction_id;
            $order->payment_status = 4;
            $order->placed_at = Carbon::now()->setTimezone(config('app.timezone'));
            $order->save();
 
             
            if(!empty($request->coupon_code)){
                $coupon = Coupon::where('code',$request->coupon_code)->first();
                $coupon->used_count =  $coupon->used_count + 1 ;
                $coupon->save();
            }
 
            $oder_items = [];
            $total_weight = 0;
            $priceids = [];
            $cartid = null;

            if($request->order_items){

                $termIds = collect($request->order_items)->pluck('term_id');

                // Retrieve terms with relationships
                $terms = Term::whereIn('id', $termIds)
                    ->where('type', 'product')
                    ->where('status', 1)
                    ->with(['excerpt', 'preview', 'firstprice'])
                    ->get();
                
                foreach ($request->order_items as $items) {
                    $info = $terms->firstWhere('id', $items['term_id']);
                
                    if (!empty($info)) {
                        if (!empty($items['variation_id'])) {
                            $info->load(['prices' => function ($query) use ($items) {
                                $query->where('id', $items['variation_id']);
                            }]);
                        } else {
                            $info->setRelation('prices', collect());
                        }
                    }

                    $data['order_id'] = $order->id;
                    $data['term_id'] = $items['term_id'];
                    $data['info'] = json_encode([
                        'sku' => $info->firstprice->sku ?? '',
                        'options' => $info->prices[0] ?? []
                    ]);

                    $data['qty'] = $items['qty'];
                    $data['amount'] = $items['amount'];
                    array_push($oder_items, $data);

                    array_push($priceids, ['order_id' => $order->id, 'price_id' => $info->firstprice->id, 'qty' => $items['qty']]); 

                    $total_weight = $total_weight + $info->firstprice->weight;     
                }
            }
 
            $order->orderitems()->insert($oder_items);

            if ($request->order_method == 'table') {
                $order->ordertable()->attach($request->table);
            }
            if ($request->order_method == 'delivery') {
                $delivery_info['address'] = $request->shipping[0]['address'].' '. $request->shipping[0]['city'].', '.$request->shipping[0]['state'].', '.$request->shipping[0]['country'];

                $delivery_info['post_code'] = $request->shipping[0]['post_code'];

                $delivery_info['shipping_method'] = $request->shipping_method;
                $delivery_info['shipping_label'] = $shipping_method_label;
                $delivery_info['credit_card_fee'] = $credit_card_fee;
                $delivery_info['booster_platform_fee'] = $booster_platform_fee;

                $order->shipping()->create([
                    'shipping_price' => $shipping_price,
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

            //  $transcation_log = new Ordermeta;
            //  $transcation_log->order_id = $order->id;
            //  $transcation_log->key = 'transcation_log';
            //  $transcation_log->value = json_encode($paymentresult['transaction_log']);
            //  $transcation_log->save();
    
            //  $order->orderlasttrans()->create([
            //      'key' => 'last_transcation_log',
            //      'value' => json_encode($paymentresult['transaction_log'])
            //  ]);
            }
 
            if (count($priceids) != 0) {
                $order->orderstockitems()->insert($priceids);
            }
             
            $club_info = tenant_club_info();
 
            $name = explode(' ',$request->name);
 
            $contact_manager_data = array(
                'first_name' => $name[0],
                'last_name' => $name[1]??'',
                'user_id' =>  $request->wpuid ??0,
                'phone_number' => $request->phone,					
                'booster_name' => $name[0],
                'country' =>   $request->billing[0]['country'],									
                'address_1' => $request->billing[0]['address'],
                'address_2' =>  '',
                'city' => $request->billing[0]['city'],
                'state' =>  $request->billing[0]['state'],
                'zip' =>  $request->billing[0]['post_code'],													
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
            // \App\Lib\Helper\Ordernotification::makeNotifyToAdmin($order);
            // \App\Lib\NotifyToUser::sendEmail($order, $request->email, 'user');

            DB::commit();
            return response()->json(["status" => true, "message" => "Order create successfully."]);

         } catch (\Throwable $th) {
            DB::rollback();  
            // dd($th);         
            return response()->json(["status" => false, "message" => "Some thing went wrong."],404);
        }
    }

 /**
 * @OA\Post(
 *     path="/api/storedata/pos-get-store-details",
 *     tags={"Store"},
 *     summary="POS store details",
 *     operationId="posGetStoreDetails",
 *     @OA\RequestBody(
 *         required=false,
 *         description="No request body for this endpoint",
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Store details successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="Store",
 *                 type="array",
 *                 description="Store details",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */


    

    public function posGetStoreDetails(Request $request){
        $club_info = tenant_club_info();

        $club_address=Option::where('key','invoice_data')->first();

        $decode_address=json_decode($club_address->value);

        $data['club_address'] = $decode_address;
        $data['club_info'] = $club_info;


        $languages=Option::where('key','languages')->first();
        
        $languages=json_decode($languages->value ?? '');
        $data['languages'] = $languages;

                  
        $timezone=Option::where('key','timezone')->first();
        $data['timezone'] = $timezone->value;

        $default_language=Option::where('key','default_language')->first();
        $data['default_language'] = $default_language->value;

        $weight_type=Option::where('key','weight_type')->first();
        $data['weight_type'] = $weight_type->value;

        $measurment_type=Option::where('key','measurment_type')->first();
        $data['measurment_type'] = $measurment_type->value;

        $currency_info=Option::where('key','currency_data')->first();
        $currency_info=json_decode($currency_info->value ?? '');
        $data['currency_info'] = $currency_info;

        $average_times=Option::where('key','average_times')->first();
        $average_times=json_decode($average_times->value ?? '');
        $data['average_times'] = $average_times;

        $order_method=Option::where('key','order_method')->first();
        $order_method=$order_method->value ?? '';
        $data['order_method'] = $order_method;

        $order_settings=Option::where('key','order_settings')->first();
        $order_settings=json_decode($order_settings->value ?? ''); 
        $data['order_settings'] = $order_settings;

        $whatsapp_no=Option::where('key','whatsapp_no')->first();
        $data['whatsapp_no'] = $whatsapp_no->value;
          
        $whatsapp_settings=Option::where('key','whatsapp_settings')->first();
        $whatsapp_settings=json_decode($whatsapp_settings->value ?? '');
        $data['whatsapp_settings'] = $whatsapp_settings;

        $shipping_method=Option::where('key','shipping_method')->first();
        $shipping_method = json_decode($shipping_method->value);
        $data['shipping_method'] = $shipping_method;

        $banner_logo=Option::where('key','banner_logo')->first();
        $data['banner_logo'] = $banner_logo->value;
        

        $bannerUrls=Option::where('key','banner_url')->first();
        $bannerUrlValue= $bannerUrls->value ?? '';
        $data['bannerUrlValue'] = $bannerUrlValue;

        $tax=Option::where('key','tax')->first();
        $data['tax'] = $tax ? $tax->value: 0.00;


        $free_shipping=Option::where('key','free_shipping')->first() ;
        $free_shipping = $free_shipping ? $free_shipping->value : 0;
        $data['free_shipping'] = $free_shipping;

        $min_cart_total=Option::where('key','min_cart_total')->first();
        $min_cart_total = $min_cart_total ? $min_cart_total->value : 0.00;
        $data['min_cart_total'] = $min_cart_total;

        $data['Getway'] = Getway::all();

        if($data){
            return response()->json(["status" => true, "message" => 'Store data fetched successfully', "result" => $data]);
        }
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

 /**
 * @OA\Post(
 *     path="/api/storedata/cart/pos_add_to_cart",
 *     tags={"Store"},
 *     summary="POS Add to cart",
 *     operationId="posAddToCart",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Add to cart request body",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="id",
 *                     type="string",
 *                     description="Product ID (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="qty",
 *                     type="integer",
 *                     description="Quantity (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="variation_id",
 *                     type="string",
 *                     description="Variation ID (optional)",
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product add to cart successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="Cart",
 *                 type="array",
 *                 description="Add to cart",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="cartid",
 *         in="header",
 *         required=false,
 *         description="Cart id",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */




    public function posAddToCart(Request $request)
    {
        $cartid = !empty($request->header('cartid')) ? $request->header('cartid') : Str::random(10);
        $info = '';

        if ($request->id) {
            $info = Term::where('id', $request->id)->where('type', 'product')
            ->where('status', 1)
            ->with(['excerpt', 'preview','firstprice'])
            ->when($request->variation_id, function ($query) use ($request) {
                $query->with(['prices' => function ($subQuery) use ($request) {
                    $subQuery->where('id', $request->variation_id);
                }]);
            })
            ->first();
        }
        
        if (empty($info)) {
            return response()->json(["status" => 0, "message" => 'Oops product not available', "result" => []],404);
        }
        
        Cart::instance($cartid);
        Cart::restore($cartid);

        $cart_content=Cart::instance($cartid)->content();
        
        if ($info->is_variation == 1) {

            $price=$info->prices[0];
                            
            $exist_qty=0;

            foreach ($cart_content as $key => $row) {
                                        
               if (($row->id == $info->id) && ($row->options->options[0]->id == $price->id)) {
                   $row_qty=$row->qty ?? 0;
                   $exist_qty=(int)$row_qty;
               }
            }

            $exist_qty=$exist_qty+$request->qty;

            $weight=$price->weight ?? 0;

            $stockCheck = $this->addStockValidation($price,$exist_qty,$cartid);
            if($stockCheck){
                return $stockCheck;
            }

            $existingCartItem = Cart::search(function ($cartItem, $rowId) use ($info, $price) {
                return $cartItem->id == $info->id;
            });
            
            if ($existingCartItem->isNotEmpty() && (int)$request->variation_id == $existingCartItem->first()->options->options->first()->id) {
                $rowId = $existingCartItem->first()->rowId;
                Cart::update($rowId, $exist_qty);
            }else{
                $cart_item = Cart::add(
                        ['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $info->prices[0]['price'], 'weight' => $info->prices[0]['weight'], 
                        'options' => [
                            'tax' =>$info->prices[0]['tax'],
                            'options' => $info->prices, 'sku' => $info->prices[0]['sku'], 'stock' => null, 'price_id' => $info->prices[0]['id'],'short_description'=>($info->excerpt->value ?? ''),
                            'preview'=>asset($info->preview->value ?? 'uploads/default.png')
                            ]
                        ]);

                if($info->prices[0]['tax'] == 1){
                    $cart_item->setTaxRate(getTaxRate());
                }
            }

        } else {

            $exist_qty=0;

            foreach ($cart_content as $key => $row) {
               if ($row->id == $info->id) {
                   $row_qty=$row->qty ?? 0;
                   $exist_qty=(int)$row_qty;
               }
            }

            $exist_qty=$exist_qty+$request->qty;

            $price=$info->firstprice;
            $weight=$price->weight ?? 0;

            $stockCheck = $this->addStockValidation($price,$exist_qty,$cartid);
            if($stockCheck){
                return $stockCheck;
            }

            $existingCartItem = Cart::search(function ($cartItem, $rowId) use ($info, $price) {
                return $cartItem->id == $info->id ? $rowId:false;
            });

            if ($existingCartItem->isNotEmpty()) {
                $rowId = $existingCartItem->first()->rowId;
                Cart::update($rowId, $exist_qty);
            }else{
                $options = [
                    'sku' => $price->sku,
                    'stock' => $price->qty,
                    'tax'=>$price->tax,
                    'type'=>$price->tax,
                    'options' => [],
                    'short_description'=>($info->excerpt->value ?? ''),
                    'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                ];
    
                if ($price->stock_manage == 1 && $price->stock_status == 1) {
                    $options['stock'] = $price->qty;
                    $options['price_id'] = [$price->id];
                } else {
                    $options['stock'] = null;
                }
          
              $cart_item =  Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $price->price, 'weight' => $weight, 'options' => $options]);          
              
              if($price->tax == 1){
                $cart_item->setTaxRate(getTaxRate());
              }
            }
        }
        try {
            Cart::store($cartid);
        } catch (Exception $e) {
            Cart::updatestore($cartid);
        }
        $productcartdata['cartid'] = $cartid;
        $productcartdata['cart_content'] = Cart::content();
        $productcartdata['cart_subtotal'] = Cart::subtotal();
        $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total();
        $productcartdata['cart_count'] = Cart::count();
        return response()->json(["status" => true, "message" => 'Added to Cart Sucessfullly', "result" => $productcartdata]);
    }

    public function addStockValidation($price,$exist_qty,$cartid){

        $productcartdata['cartid'] = $cartid;
        $productcartdata['cart_content'] = Cart::content();
        $productcartdata['cart_subtotal'] = Cart::subtotal();
        $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total();
        $productcartdata['cart_count'] = Cart::count();

        if ($price->stock_manage == 1) {

            $orderStockSum = Orderstock::where('price_id', $price->id)->sum('qty');
            $remain_qty = $price->qty-(int)$orderStockSum;

            if ($exist_qty > $price->qty) {
                Cart::restore($cartid);
                Cart::store($cartid);

                return response()->json(["status" => 0, "message" => 'Maximum stock limit is ('.$price->qty.')', "result" => $productcartdata],404);
            }

            if ($remain_qty < $exist_qty) {
                Cart::restore($cartid);
                Cart::store($cartid);

                return response()->json(["status" => 0, "message" => 'Stock not available.', "result" => $productcartdata],404);
            }
        }
        
        if (($price->stock_status == 0)) {
            Cart::restore($cartid);
            Cart::store($cartid);

            return response()->json(["status" => 0, "message" => 'Oops Maximum stock limit exceeded', "result" => $productcartdata],404);

        }
    }

 /**
 * @OA\Post(
 *     path="/api/storedata/cart/pos_get_cart",
 *     tags={"Store"},
 *     summary="POS get cart",
 *     operationId="posGetCart",
 *     @OA\Response(
 *         response=200,
 *         description="Cart data",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="Cart",
 *                 type="array",
 *                 description="Cart data",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="cartid",
 *         in="header",
 *         required=true,
 *         description="Cart id",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */

    public function posGetCart(Request $request)
    {
        $cartid=!empty($request->header('cartid'))?$request->header('cartid'):"";
        if(empty($cartid)){
            return response()->json(["status" => 0, "message" => 'Oops cart not found', "result" => []],404);
        }
        //initialize cart
        Cart::instance($cartid);
        //load cart in session
        Cart::restore($cartid);
        if(Cart::content()->isEmpty()){
            return response()->json(["status" => false, "message" => 'Your cart is empty', "result" => []],404);
        }
        //resave cart
        try{
            Cart::store($cartid);
        }catch(Exception $e){
            Cart::updatestore($cartid);
        }
        $productcartdata['cartid'] = $cartid;
        $productcartdata['cart_content'] = Cart::content();
        $productcartdata['cart_subtotal'] = Cart::subtotal();
        $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total();
        $productcartdata['cart_count'] = Cart::count();
        return response()->json(["status" => true, "message" => 'Cart Data', "result" => $productcartdata]);
    }


     /**
 * @OA\Post(
 *     path="/api/storedata/cart/pos_remove_from_cart/{id}",
 *     tags={"Store"},
 *     summary="Pos Remove cart",
 *     operationId="posRemoveCart",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Removed From Cart Sucessfullly",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="product",
 *                 type="array",
 *                 description="Remove cart",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="cartid",
 *         in="header",
 *         required=true,
 *         description="Cart id",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */

 public function posRemoveCart(Request $request,$id)
 {
    $cartid=!empty($request->header('cartid'))?$request->header('cartid'):"";
    if(empty($cartid)){
        return response()->json(["status" => 0, "message" => 'Oops cart not found', "result" => []],404);
    }
    //initialize cart
    Cart::instance($cartid);
    //load cart in session
    Cart::restore($cartid);
    if(Cart::content()->isEmpty()){
        return response()->json(["status" => false, "message" => 'Your cart is empty', "result" => []],404);
    }
    $rowid=Cart::content()->filter(function ($cartItem, $rowId) use($id) {
        return $cartItem->rowId == $id?$rowId:false;
    });

    if($rowid->isNotEmpty()){
        Cart::remove($rowid->first()->rowId);//remove
    }
    try{
        Cart::store($cartid);
    }catch(Exception $e){
        Cart::updatestore($cartid);
    }
    $productcartdata['cartid'] = $cartid;
    $productcartdata['cart_content'] = Cart::content();
    $productcartdata['cart_subtotal'] = Cart::subtotal();
    $productcartdata['cart_tax'] = Cart::tax();
    $productcartdata['cart_total'] = Cart::total();
    $productcartdata['cart_count'] = Cart::count();
    return response()->json(["status" => true, "message" => 'Removed From Cart Sucessfullly', "result" => $productcartdata]);
 }


  /**
 * @OA\Post(
 *     path="/api/storedata/cart/pos_update_cart",
 *     tags={"Store"},
 *     summary="POS update cart",
 *     operationId="PosCartQty",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Update cart request body",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="id",
 *                     type="string",
 *                     description="Product ID (required)",
 *                 ),
 *                 @OA\Property(
 *                     property="qty",
 *                     type="integer",
 *                     description="Quantity (required)",
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart updated.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="Cart",
 *                 type="array",
 *                 description="Cart updated",
 *                 @OA\Items(type="string"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="cartid",
 *         in="header",
 *         required=false,
 *         description="Cart id",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */



 public function PosCartQty(Request $request)
 {
     $cartid=!empty($request->header('cartid'))?$request->header('cartid'):"";
     if(empty($cartid)){
         return response()->json(["status" => 0, "message" => 'Oops cart not found', "result" => []],404);
     }
     Cart::instance($cartid);
     Cart::restore($cartid);
     $id=$request->id;
     if(empty($request->id)||!isset($request->qty)){
         return response()->json(["status" => false, "message" => 'Your cart is empty', "result" => []],404);
     }
     if(Cart::content()->isEmpty()){
         return response()->json(["status" => false, "message" => 'Your cart is empty', "result" => []],404);
     }

     $cartFilter=Cart::content()->filter(function ($cartItem, $rowId) use($id) {
         return $cartItem->rowId == $id?$rowId:false;
     });

     $pId = '';

     if($cartFilter->isNotEmpty()){

         if(!empty($cartFilter->first()->options->options[0]['id'])){
             $pId = $cartFilter->first()->options->options[0]['id'];
         }else{
             $pId = $cartFilter->first()->options['price_id'][0];
         }
     }

     $priceData = Price::where('id',$pId)->first();

     if($priceData){
         $reqQunatity = $request->qty;
         $stockCheck = $this->addStockValidation($priceData,$reqQunatity,$cartid);
       
         if($stockCheck){
             return $stockCheck;
         }
     }

     $rowid=Cart::content()->filter(function ($cartItem, $rowId) use($id) {
         return $cartItem->rowId == $id?$rowId:false;
     });
     
     if($rowid->isNotEmpty()){
         Cart::update($rowid->first()->rowId, $request->qty);//QTY update
     }

     try{
         Cart::store($cartid);
     }catch(Exception $e){
         Cart::updatestore($cartid);
     }

     $productcartdata['cartid'] = $cartid;
     $productcartdata['cart_content'] = Cart::content();
     $productcartdata['cart_subtotal'] = Cart::subtotal();
     $productcartdata['cart_tax'] = Cart::tax();
     $productcartdata['cart_total'] = Cart::total();
     $productcartdata['cart_count'] = Cart::count();
     return response()->json(["status" => true, "message" => 'Cart Updated Sucessfullly', "result" => $productcartdata]);
 }


 /**
 * @OA\Post(
 *     path="/api/storedata/pos-order-info",
 *     tags={"Store"},
 *     summary="POS Order info",
 *     operationId="posOrderInfo",
 *     @OA\Response(
 *         response=200,
 *         description="Order info retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Success",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */

 public function posOrderInfo(Request $request)
 { 
    $websiteConditions = [
        'payment_status' => '1',
        'order_from' => '1',
    ];

    $posConditions = [
        'payment_status' => '1',
        'order_from' => '4',
    ];

    $data = [];

    $websiteRevenue = Order::where($websiteConditions)->sum('total');
    $posRevenue = Order::where($posConditions)->sum('total');
    $totalRevenue = $websiteRevenue+$posRevenue;

    $website_count = Order::where($websiteConditions)->count();
    $pos_count = Order::where($posConditions)->count();
    
    $data['website_order_revenue'] = $websiteRevenue;
    $data['pos_order_revenue'] = $posRevenue;
    $data['total_revenue'] = $totalRevenue;
    $data['website_order_count'] = $website_count;
    $data['pos_order_count'] = $pos_count;
    $data['total_order_count'] = $website_count + $pos_count;

    return response()->json(['error' => false, 'message' => 'Order info retrieved successfully', 'result' => $data]);
 }


  /**
 * @OA\Post(
 *     path="/api/storedata/pos-order-list",
 *     tags={"Store"},
 *     summary="POS Order list",
 *     operationId="posOrderList",
 *     @OA\Response(
 *         response=200,
 *         description="Order list retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Success",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 description="Status of the operation",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message",
 *             ),
 *         ),
 *     ),
 *     security={
 *         {"bearerAuth": {}},
 *     },
 *     @OA\Parameter(
 *         name="Apitoken",
 *         in="header",
 *         required=true,
 *         description="API Token for authentication",
 *         @OA\Schema(type="string"),
 *     ),
 *     @OA\Parameter(
 *         name="X-Tenant",
 *         in="header",
 *         required=true,
 *         description="Tenant identifier",
 *         @OA\Schema(type="string"),
 *     ),
 * )
 */

 public function posOrderList(Request $request){
    $info = Order::with('orderlasttrans', 'orderitems', 'shippingwithinfo', 'ordermeta')
    ->where('order_from', 4)
    ->get();

    if($info){
        return response()->json(['error'=>false,'message'=>'Order list fetched successfully','result'=>$info]);
    }
 }
}