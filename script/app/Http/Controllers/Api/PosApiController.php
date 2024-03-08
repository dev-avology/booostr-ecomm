<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Productoption;
use App\Models\Category;
use App\Models\Term;
use App\Models\Termcategory;
use App\Models\User;
use App\Models\Getway;
use App\Models\Location;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Ordermeta;
use App\Models\Price;
use Cookie;
use App\Models\Option;
use Carbon\Carbon;
use App\Models\Orderstock;
use Illuminate\Support\Facades\Session;
use App\Mail\PosUserEmail;
use Cart;
use Mail;
use DB;
use Auth;
use Validator;
use Exception;
use Stripe\Stripe;
use Stripe\Token;

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
 * @OA\Post(
 *     path="/api/storedata/pos-parent-category-product",
 *     tags={"Store"},
 *     summary="POS product list",
 *     operationId="posParentCategoryProduct",
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




    public function posParentCategoryProduct(Request $request)
    {
        $category = Category::find($request->category_id);
        $categoryIds = $category->recursiveChildrenIds();

        $termIds = Termcategory::whereIn('category_id', $categoryIds)->pluck('term_id')->toArray();
    
        $posts = Term::query()->where('type', 'product')->where('status', 1)
       ->whereIn('list_type', [2])->whereIn('id', $termIds)->with('media','category','firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice')->selectRaw('*, (SELECT MAX(price) FROM prices WHERE term_id = terms.id) AS max_price, (SELECT MIN(price) FROM prices WHERE term_id = terms.id) AS min_price');
    
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

        // Check if the required fields are present
        $rules = [
            'order_total' => 'required|numeric',
            'order_subtotal' => 'required|numeric',
            'order_tax' => 'required|numeric',
        
            'wpuid'         => 'required|numeric',
            
            'payment_method' => 'required|string',
            'payment_details' => 'required',

            'items' => 'required|array',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Required fields are missing'], 422);
        }


        $subtotal = $request->order_subtotal;
        $total_amount = $request->order_total;
        
        $shipping_price = 0;
        $shipping_method_label = '';
        
        $order_method='delivery';
        $notify_driver='mail';

        $credit_card_fee = 0.00;
        $booster_platform_fee = 0.00;

        if( $request->payment_method == 'card' ){ // Payment Method: CARD
            // Generate Stripe Token
            try {
                $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
                $gateway_data_info = json_decode($gateway->data);

                Stripe::setApiKey($gateway->test_mode == 1 ? $gateway_data_info->test_publishable_key : $gateway_data_info->publishable_key);

                $token = Token::create([
                    'card' => [
                        'number' => $request->payment_details['card_details']['cardNumber'],
                        'exp_month' => substr($request->payment_details['card_details']['expirationDate'], 0, 2),
                        'exp_year' => substr($request->payment_details['card_details']['expirationDate'], 3, 2),
                        'cvc' => $request->payment_details['card_details']['cvc'],
                    ],
                ]);
            } catch (InvalidRequestException $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            // Set Stripe API keys
            if( $gateway->test_mode ){
                $payment_data['test_publishable_key'] = $gateway_data_info->test_publishable_key;
                $payment_data['test_secret_key'] = $gateway_data_info->test_secret_key;
            }else{
                $payment_data['publishable_key'] = $gateway_data_info->publishable_key;
                $payment_data['secret_key'] = $gateway_data_info->secret_key;
            }

            // Set Payment Data
            $payment_data['currency']   = strtoupper($gateway->currency_name) ?? 'USD';
            $payment_data['name']       = $request->payment_details['card_details']['cardholderName'];
            $payment_data['billName']   = 'Boostr Sale';
            $payment_data['amount']     = $total_amount;
            $payment_data['application_fee_amount']  = 0.00;
            $payment_data['credit_card_fee']  = 0.00;
            $payment_data['test_mode']  = $gateway->test_mode;
            $payment_data['charge']     = 0.00;
            $payment_data['pay_amount'] =  str_replace(',','',number_format($total_amount ?? 0,2));
            $payment_data['getway_id']  = $gateway->id;
            $payment_data['stripeToken']=$token->id;
            $payment_data['pos']=true;

            // Charge Payment
            $chargePayment= $gateway->namespace::charge_payment($payment_data);
            
            // Return Payment Error Message
            if($chargePayment['payment_status'] != 1){
                return response()->json(['status' => false, 'message' => 'Sorry, we couldnt charge your card, please try another card', 'paymentresult'=>$chargePayment], 200);
            }

            $payment_data['transaction_id'] = $chargePayment['payment_id'];

            // Capture Payment
            $paymentresult= $gateway->namespace::capture_payment($payment_data);

            // Return Payment Error Message
            if($paymentresult['payment_status'] != 1){
                return response()->json(['status' => false, 'message' => 'Sorry, we couldnt charge your card, please try another card', 'paymentresult'=>$paymentresult], 200);
            }
        } else { // Payment Method: CASH
            $gateway=Getway::where('name','cash')->first();
        } // Payment Method specific code END

        DB::beginTransaction();
        try {
            // Insert New Order
            $order = new Order;
            $order->user_id = null;

            $notify_driver = 'mail';

            $order->getway_id = $gateway->id;
            $order->status_id = $request->payment_method == 'card' ? 4 : 1;
            $order->tax = $request->order_tax ?? 0;

            $order->discount = $request->discount ?? 0;
            $order->coupon_code = $request->coupon_code ?? null;

            $order->total = $total_amount ?? 0;
            $order->order_method = $order_method ?? 'delivery';
            $order->order_from = $request->payment_method == 'card' ? 4 : 5;  // 4 is for card and 5 is for cash
            $order->notify_driver = $notify_driver;
            $order->transaction_id = $request->payment_method == 'card' ? $paymentresult['payment_id'] : null;
            $order->payment_status = 1;
            $order->placed_at = Carbon::now()->setTimezone($request->timezone);
            $order->captured_at = Carbon::now()->setTimezone($request->timezone);
            $order->save();

            $oder_items = [];
            $total_weight = 0;
            $priceids = [];
            $cartid = null;

            // Save bought items data
            if($request->items){

                $termIds = collect($request->items)->pluck('id');
                
                // Retrieve terms with relationships
                $terms = Term::whereIn('id', $termIds)
                ->where('type', 'product')
                ->with(['excerpt', 'preview', 'firstprice'])
                ->get();
                            
                foreach ($request->items as $item) {
                    $info = $terms->firstWhere('id', $item['id']);
                    
                    if (!empty($info)) {
                        if (!empty($item['variation_id'])) {
                            $info->load(['prices' => function ($query) use ($item) {
                                $query->where('id', $item['variation_id']);
                            }]);
                        } else {
                            $info->setRelation('prices', collect());
                        }
                    }
                    
                    $data['order_id'] = $order->id;
                    $data['term_id'] = $item['id'];
                    $data['info'] = json_encode([
                        'sku' => $item->firstprice->sku ?? '',
                        'options' => $info->prices[0] ?? []
                    ]);
                    
                    $data['qty'] = $item['cart_quantity'];
                    $data['amount'] = $item['max_price'];
                    array_push($oder_items, $data);
                    
                    array_push($priceids, ['order_id' => $order->id, 'price_id' => $info->firstprice->id, 'qty' => $item['cart_quantity']]);
                    
                    $total_weight = $total_weight + $info->firstprice->weight;     
                }
            }
            
            $order->orderitems()->insert($oder_items);

            // Add empty delivery data. So we are able to use methods available to capture payment and the order details page does not break, adn to use other order relations functions
            $delivery_info['address'] = '';
            $delivery_info['post_code'] = '';
            $delivery_info['shipping_method'] = '';
            $delivery_info['shipping_label'] = '';
            $delivery_info['credit_card_fee'] = $credit_card_fee;
            $delivery_info['booster_platform_fee'] = $booster_platform_fee;
            $order->shipping()->create([
                'shipping_price' => 0.00,
                'weight' => $total_weight,
                'info' => json_encode($delivery_info)
            ]);

            // Save transaction Data
            if (!empty($request->payment_details['card_details']['cardholderName'])) {
                $customer_info['name'] = $request->payment_details['card_details']['cardholderName'];
                $customer_info['email'] = $request->email;
                $customer_info['phone'] = $request->phone;
                $customer_info['wpuid'] = null;
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
            
            $data = ['order_id' => $order->id,'order_date' => $order->placed_at];
            
            DB::commit();
            return response()->json(["status" => true, "message" => "Order Successfull.",'data'=>$data]);
        } catch (\Throwable $th) {
            DB::rollback();        
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
        $data['tenant_logo'] = env('WP_URL').tenant()->logo;

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
        'order_from' => ['4', '5'],
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
 *     requestBody={
 *         required=true,
 *         description="Order list",
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="key",
 *                     type="string",
 *                     description="Order key (required)",
 *                 ),
 *             ),
 *         ),
 *     },
 *     @OA\Response(
 *         response=200,
 *         description="Order list retrieved successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="boolean",
 *                 description="Indicates if an error occurred",
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Message from the server",
 *             ),
 *             @OA\Property(
 *                 property="result",
 *                 type="array",
 *                 description="List of orders",
 *                 @OA\Items(ref="#/components/schemas/Order"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
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
        $key = $request->input('key');

        $info = Order::with('orderlasttrans', 'orderitems', 'shippingwithinfo', 'ordermeta')
        ->whereIn('order_from', [4, 5]);

        if($key == 'latest'){
            $info->where('payment_status', 1)
                ->orderByDesc('created_at');
        } elseif ($key == 'complete') {
            $info->where('payment_status', 1);
        }

        $priceIds = [];
        $termData = [];

        $priceIds = Orderstock::select('price_id', DB::raw('COUNT(*) as count'))->groupBy('price_id')->pluck('price_id')->toArray();
        

        if(isset($priceIds)){
            $termIds = Price::whereIn('id',$priceIds)->pluck('term_id')->toArray();

            if(isset($termIds)){
                $termData = Term::with('media','firstprice','lastprice')->whereIn('id', $termIds)->where('type', 'product')->paginate(20);
            }   
        }

        $info = $info->paginate(20);

        if($info->isNotEmpty()){
            return response()->json(['error' => false, 'message' => 'Order list fetched successfully', 'result' => $info,'heighest_sell_terms' =>$termData]);
        } else {
            return response()->json(['error' => true, 'message' => 'No orders found', 'result' => null]);
        }
    }



public function posEmailSend(Request $request){
    $orderData = $request->all();

    if(!empty($orderData)){

        $wpuid = $orderData['wpuid'] ?? 0;
        $club_name = $orderData['club_name'] ?? '';
        $orderId = $orderData['orderId'] ?? '';
        $client_name = $orderData['client_name'] ?? '';
        $client_email = $orderData['client_email'] ?? '';
        $phone_number = '9149117623';

        $subject="Receipt for your purchase from ".$orderData['club_name']." on ".$orderData['created_at'];
        $mail = new PosUserEmail($orderData,$subject);
        $to = $orderData['client_email'] ?? '';
        // $to = 'ashishyadav.avology@gmail.com';
        $email = Mail::to($to)->send($mail);


        $club_info = tenant_club_info();

        $name = explode(' ',$client_name);

         $contact_manager_data = array(
             'first_name' => $name[0],
             'last_name' => $name[1]??'',
             'user_id' =>  $wpuid ??0,
             'phone_number' => $phone_number,					
             'booster_name' => $name[0],
             'country' =>   'USA',									
             'address_1' => 'Test Address Line 1',
             'address_2' =>  'Test Address Line 2',
             'city' => 'Alameda',
             'state' =>  'California',
             'zip' =>  '94501',													
             'email' =>  $client_email,                   
             'booster_id' =>Tenant('club_id'),
             'booster_level_id' => 4,
             'contact_tags' => '',
         );	  

         $user_recipt = [
             'contact_mgr_data'=>$contact_manager_data,
             'receipts_date'=>Carbon::now()->setTimezone(config('app.timezone')),
             'receipt_title'=>$client_name,
             'receipent_org'=>$club_name.' Store',
             'category'=>'ecommerce',
             'user_id' => $wpuid ??0,
             'club_id' =>Tenant('club_id'),
             'recurring'=>'one-time',
             'camp_id'=>$orderId,
         ];

        $recipt =  $this->send_order_recipts($user_recipt);

        if(isset($recipt)){
            return response()->json(['error'=>false,'message'=>'Email sent successfully.']);
        }else{
            return response()->json(['error'=>true,'message'=>'Some thing went wrong.']);
        }
    }
    return response()->json(['error'=>true,'message'=>'Some thing went wrong.']);
}


private function send_order_recipts($data){

    $postData = json_encode($data);

    $url = env("WP_API_URL");
    
    $url = ($url != '') ? $url.'/add-pos-contact' : "https://staging3.booostr.co/wp-json/store-api/v1/add-pos-contact";

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

}