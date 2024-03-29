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
use App\Models\Price;
use App\Models\Coupon;
use App\Models\Orderstock;
use App\Models\Option;
use Carbon\Carbon;
use Cart;
use DB;
use Auth;
use Exception;

class ProductController extends Controller
{


   public function categoryList(Request $request){

       //$posts=Category::where('type','category')->with('preview','icon','show_on')->withCount('products')->get();
       $posts = Category::where('type', 'category')->whereNull('category_id')
        ->with('preview', 'icon','recursiveChildren')
        ->withCount('products')
        ->whereDoesntHave('show_on', function ($query) {
            $query->where('type', 'show_on')->where('content', 'pos_only');
        })
        ->get();

       $product_count = Term::query()->where('type', 'product')->where('status', 1)
       ->whereIn('list_type', [0,1])->with('media', 'firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice')->count();

       return response()->json(["status" => true, "message" => "products", "result" => ['categories'=>$posts,'product_count'=>$product_count]]);
    }



    public function productList(Request $request)
    {
       $posts = Term::query()->where('type', 'product')->where('status', 1)
       ->whereIn('list_type', [0,1])->with('media','category','firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice')->selectRaw('*, (SELECT MAX(price) FROM prices WHERE term_id = terms.id) AS max_price, (SELECT MIN(price) FROM prices WHERE term_id = terms.id) AS min_price');

        if (!empty($request->category)) {
            $posts = $posts->whereHas('termcategories', function ($query) use ($request) {
                return $query->where('category_id', $request->category);
            });
        }
        $posts = $posts->latest()->paginate(50);
        return response()->json(["status" => true, "message" => "products", "result" => $posts]);
    }

    
    public function productDetail(Request $request,$id)
    {
        $info=Term::query()->where('type','product')->where('status',1)->whereIn('list_type', [0,1])->with('tags','brands','excerpt','description','preview','medias','optionwithcategories','price','prices','seo')->withCount('reviews')->where('id', $id)->first();
        if(empty($info)){
            return response()->json(["status" => false, "message" => "sorry, product not found", "result" => []]);
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

    public function search(Request $request)
    {
        $posts = Term::query()
            ->where('type', 'product')
            ->whereIn('list_type', [0,1])
            ->with('media', 'firstprice', 'lastprice')
            ->whereHas('firstprice')
            ->whereHas('lastprice')
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('full_id', 'like', '%' . $request->keyword . '%');
            })->latest()->paginate(100);

        return response()->json(["status" => true, "message" => "searched products", "result" => $posts]);
    }

    public function addtocart(Request $request)
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

    public function getcart(Request $request)
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

    public function removecart(Request $request,$id)
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

    public function CartQty(Request $request)
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
        return response()->json(["status" => true, "message" => 'Cart Updated  Sucessfullly', "result" => $productcartdata]);
    }

    // public function addQtyUpdateStockValidation($priceData,$reqQunatity,$cartid){

    //     $productcartdata['cartid'] = $cartid;
    //     $productcartdata['cart_content'] = Cart::content();
    //     $productcartdata['cart_subtotal'] = Cart::subtotal();
    //     $productcartdata['cart_tax'] = Cart::tax();
    //     $productcartdata['cart_total'] = Cart::total();
    //     $productcartdata['cart_count'] = Cart::count();

    //     if ($priceData->stock_manage == 1) {
    //         $orderStockSum = Orderstock::where('price_id', $priceData->id)->sum('qty');
    //         $remain_qty = $priceData->qty-(int)$orderStockSum;

    //         if ($reqQunatity > $priceData->qty) {
    //             Cart::restore($cartid);
    //             Cart::store($cartid);
            
    //             return response()->json(["status" => false, "message" => 'Maximum stock limit is ('.$priceData->qty.')','result'=>$productcartdata],404);
    //         }

    //         if ($remain_qty < $reqQunatity) {
    //             Cart::restore($cartid);
    //             Cart::store($cartid);

    //             return response()->json(["status" => false, "message" => 'Stock not available.','result'=>$productcartdata],404);
    //         }
    //     }
        
    //     if ($priceData->stock_status == 0) {
    //         Cart::restore($cartid);
    //         Cart::store($cartid);

    //         return response()->json(["status" => false, "message" => 'Oops Maximum stock limit exceeded','result'=>$productcartdata],404);
    //     }
    // }

    public function varidation($id)
    {
        $info = Term::query()->where('type', 'product')->with('productoptionwithcategories')->findorFail($id);
        return response()->json($info);
    }

    public function checkcustomer(Request $request)
    {
        $user = User::query()->where('email', $request->email)->first();
        if (empty($user)) {
            $errors['errors']['error'] = 'Customer Not available';
            return response()->json($errors, 401);
        }

        return response()->json(['user_id' => $user->id, 'email' => $user->email, 'name' => $user->name]);
    }

    public function makeCustomer(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|unique:users|max:100|email',
            'name' => 'required|max:100',
            'password' => 'required|min:6',
            'wallet' => 'required|max:100',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);
        $user->wallet = $request->wallet;
        $user->role_id = 4;
        $user->save();

        return response()->json(['user_id' => $user->id, 'email' => $request->email, 'name' => $request->name]);
    }

    public function applyTax()
    {
        $tax = get_option('tax');
        if ($tax == null) {
            $tax = 0;
        }
        Cart::setGlobalTax($tax);

        $productcartdata['cart_subtotal'] = Cart::subtotal();
        $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total();

        return response()->json($productcartdata);
    }

    public function makeorder(Request $request)
    {
        $total_amount = str_replace(',', '', Cart::total());
        $total_discount = str_replace(',', '', Cart::discount());

        if (!empty($request->coupon)) {
            $mydate = Carbon::now()->toDateString();
            $coupon = Coupon::where('code', $request->coupon)
                ->where('start_from', '<=', $mydate)
                ->where('will_expire', '>=', $mydate)
                ->where('status', 1)
                ->latest()
                ->first();
            if ($coupon == null) {
                $errors['errors']['error'] = 'Oops this coupon is not available...';
                return response()->json(["status" => 0, "message" => 'Oops this coupon is not available...', "result" => $errors], 401);
            }

            if ($coupon->is_conditional == 1) {

                if ($total_amount < $coupon->min_amount) {
                    $errors['errors']['error'] = 'The minumum order amount is ' . number_format($coupon->min_amount, 2) . ' for this coupon';
                    return response()->json(["status" => 0, "message" => 'Oops this coupon has minumum order imit', "result" => $errors], 401);
                }
            }

            if ($coupon->is_percentage == 0) {
                $total_amount = $total_amount - $coupon->value;
                $total_discount = $coupon->value;
            } else {
                Cart::setGlobalDiscount($coupon->value);
                $total_amount = str_replace(',', '', Cart::total());
                $total_discount = str_replace(',', '', Cart::discount());
            }
        }

        $validated = $request->validate([
            'payment_id' => 'max:100',
            'payment_id' => 'max:100',
            'note' => 'max:300',
            'order_status' => 'required'
        ]);

        $notify_driver = 'mail';

        if ($request->customer_id) {
            $notify_driver = get_option('order_settings', true)->order_method ?? 'mail';
        }

        if ($request->order_method == 'table') {
            $validated = $request->validate([
                'table' => 'required|max:100',
            ]);
        }

        if ($request->pre_order == 1) {
            $validated = $request->validate([
                'date' => 'required|max:100',
                'time' => 'required|max:100',
            ]);
        }

        DB::beginTransaction();
        try {

            $order = new Order;
            $order->transaction_id = $request->payment_id;
            $order->getway_id = $request->getway;
            $order->user_id = $request->customer_id;
            $order->payment_status = $request->payment_status;
            $order->status_id = $request->order_status;
            $order->tax = str_replace(',', '', Cart::tax());
            $order->discount = $total_discount;
            $order->total = $total_amount;
            $order->order_method = $request->order_method;
            $order->order_from = 'admin';
            $order->notify_driver = $notify_driver;
            $order->save();

            $oder_items = [];
            $total_weight = 0;
            $priceids = [];

            foreach (Cart::content() as $row) {
                $data['order_id'] = $order->id;
                $data['term_id'] = $row->id;
                $data['info'] = json_encode([
                    'sku' => $row->options->sku ?? '',
                    'options' => $row->options->options ?? []
                ]);
                $data['qty'] = $row->qty;
                $data['amount'] = $row->price;
                $total_weight = $total_weight + $row->weight;
                array_push($oder_items, $data);

                foreach ($row->options->price_id ?? [] as $key => $r) {
                    array_push($priceids, ['order_id' => $order->id, 'price_id' => $r, 'qty' => $row->qty]);
                }
            }

            $order->orderitems()->insert($oder_items);
            if ($request->pre_order == 1) {
                $order->schedule()->create(['date' => $request->date, 'time' => $request->time]);
            }

            if ($request->order_method == 'table') {
                $order->ordertable()->attach($request->table);
            }
            if ($request->order_method == 'delivery') {
                $delivery_info['address'] = $request->delivery_address;
                $delivery_info['post_code'] = $request->postal_code;

                $order->shipping()->create([
                    'location_id' => $request->location,
                    'shipping_id' => $request->shipping_method,
                    'shipping_price' => $request->shipping_price,
                    'weight' => $total_weight,
                    'info' => json_encode($delivery_info)
                ]);
            }

            if (!empty($request->name) || !empty($request->email) || !empty($request->phone) || !empty($request->note)) {
                $customer_info['name'] = $request->name;
                $customer_info['email'] = $request->email;
                $customer_info['phone'] = $request->phone;
                $customer_info['note'] = $request->note;

                $order->ordermeta()->create([
                    'key' => 'orderinfo',
                    'value' => json_encode($customer_info)
                ]);
            }
            if (count($priceids) != 0) {
                $order->orderstockitems()->insert($priceids);
            }

            Cart::destroy();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
            $errors['errors']['error'] = 'Oops something wrong';
            return response()->json(["status" => 0, "message" => 'Oops something wrong', "result" => $errors], 401);
        }

        return response()->json(["status" => true, "message" => 'Order Placed']);
    }

    public function resend_invoice(Request $request){
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($request->invoiceid);

        if($order){
            $email_to = $order->user->email;
            \App\Lib\NotifyToUser::customermail($order,$email_to);
            return response()->json(["status" => 'true', "message" => 'Order Placed']);
        }else{
            return response()->json(["status" => 'false', "message" => 'Order Placed failed']);
        }
    }

    public function getInvoiceInfo(Request $request){
        $info = Order::with('orderlasttrans','orderitems','shippingwithinfo','ordermeta')->find($request->invoice_no);
       
    if($info){
        $shipping_method = json_decode($info->shippingwithinfo->info ?? '');

        $shipping_details = ['shipping_driver' => $info->shippingwithinfo->shipping_driver,'tracking_no' => $info->shippingwithinfo->tracking_no,'shipping_method' => $shipping_method->shipping_label == 'Free Shipping' ? $shipping_method->shipping_label : $shipping_method->shipping_label .' Shipping'];

        $orderlasttrans=json_decode($info->orderlasttrans->value ?? '');
        $timestamp = $orderlasttrans->created ?? '';

        $createdAt = Carbon::parse($info->placed_at)->format('m/d/Y h:i A');

        $amount_refunded = $orderlasttrans->amount_refunded;
        $lastdigit = $orderlasttrans->source->last4;
        $card_number = str_pad($lastdigit, 16, "*", STR_PAD_LEFT);
        $order_data = [];
        $ordermeta=json_decode($info->ordermeta->value ?? '');

        $billing_name = $ordermeta->name;
        $billing_email = $ordermeta->email;
        $billing_phone = $ordermeta->phone;

        $billing_add = $ordermeta->billing->address;
        $billing_city = $ordermeta->billing->city;
        $billing_state = $ordermeta->billing->state;
        $billing_country = $ordermeta->billing->country;
        $billing_post_code = $ordermeta->billing->post_code;

        $new_billing_address = $billing_name . '<br>' . $billing_add . '<br>' . $billing_city . ', ' . $billing_state . ' ' . $billing_post_code . '<br>' . $billing_country . '<br>' . $billing_phone . '<br>' . $billing_email;
        $order_data['billing_address'] = $new_billing_address;

        $shippping_name = $ordermeta->shipping->name;
        $shippping_phone = $ordermeta->shipping->phone;
        $shippping_address = $ordermeta->shipping->address;
        $shippping_city = $ordermeta->shipping->city;
        $shippping_state = $ordermeta->shipping->state;
        $shippping_country = $ordermeta->shipping->country;
        $shippping_post_code = $ordermeta->shipping->post_code;

        $new_shiiping_address = $shippping_name . '<br>' . $shippping_address . '<br>' . $shippping_city . ', ' . $shippping_state . ' ' . $shippping_post_code . '<br>' . $shippping_country . '<br>' . $shippping_phone . '<br>' . $billing_email;

        $order_data['shipping_address'] = $new_shiiping_address;
        $order_data['amount_refunded'] = currency_formate($amount_refunded/100??0);
        $order_data['invoice_no'] = $info->invoice_no;

        if ($info->payment_status == '1') {
            $authorized = 'Paid';
        } elseif ($info->payment_status == '4') {
            $authorized = 'Authorized';
        } elseif ($info->payment_status == '5') {
            $authorized = 'Refunded';
        }

        $order_data['payment_status'] = $authorized;
        $payment_information = ['status' => $authorized,'card' => $card_number,'name' => $billing_name, 'amount' => currency_formate($info->total)];
        $order_data['payment_card_info'] = $payment_information;


        $items = [];
        $subtotal = 0; 
        foreach ($info->orderitems ?? [] as $row){
            $product_name = []; 
            $variations = json_decode($row->info);
            $options = $variations->options ?? [];
            $product_name['name'] = $row->term->title ;
                foreach ($options ?? [] as $key => $item){

                    $product_options = $item->varition_options;
                    foreach($item->varitions as $sel_val){
                       
                       $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                            return $x->id == $sel_val->pivot->productoption_id;
                        } );


                        $product_name['name'] .= '<br><strong>'.reset($cur_opt_name)->category->name.': </strong>'.$sel_val->name;
                    }
                }
                              
                $product_name['amount'] = currency_formate($row->amount) ;
            
                $product_name['qty'] = $row->qty ;
            
                $product_name['total'] =  currency_formate($row->amount * $row->qty) ;
            $subtotal = $subtotal + $row->amount*$row->qty; 
            $items[] = $product_name;
        }

        $order_data['sub_total'] = currency_formate($subtotal ?? 0) ;
        $order_data['discount'] = '-'.currency_formate($info->discount);
        $order_data['tax'] = currency_formate($info->tax);
        $order_data['created_at'] = $createdAt;
        $club_info = tenant()->club_info;

        $club_email=json_decode($club_info ?? '');
        $order_data['club_email'] = $club_email->club_email;

        $shipping_price=$info->shippingwithinfo->shipping_price ?? 0;
        $order_data['shipping_price'] = currency_formate($shipping_price); 
        $order_data['grand_total'] = currency_formate($info->total); 
        $order_data['product_list'] = $items;
        $order_data['shipping_details'] = $shipping_details;

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
        $address['post_code'] = $decode_address->post_code ?? '';
        $address['store_legal_email'] = $decode_address->store_legal_email ?? '';

        $club_info = tenant()->club_info;

        $club_info=json_decode($club_info ?? '');

        $address['club_url'] = $club_info->club_url;

        $order_data['club_address'] = $address ?? '';

              
    
            return response()->json(["status" => 'true', "message" => 'Order data fetched successfully','data' =>$order_data]);
        }else{
            return response()->json(["status" => 'false', "message" => 'Something went wrong']);
        }
    }

    public function getBannerImage(Request $request){
      $banner = Option::where('key','banner_logo')->first();
      $banner_title = Option::where('key','banner_title')->first();
      $banner_button_text = Option::where('key','banner_button_text')->first();
      $banner_button_url = Option::where('key','banner_url')->first();
    //   $banner['banner_title'] = $banner_title->value ?? '';
    //   $banner['banner_button_text'] = $banner_button_text->value ?? '';
    //   $banner['banner_button_url'] = $banner_button_url->value ?? '';
      $banner['banner_url'] = $banner_button_url->value ?? '';
      if($banner){
          return response()->json(["status" => 'true', "message" => 'Order data fetched successfully','data' =>$banner]);
        }else{
            return response()->json(["status" => 'false', "message" => 'Something went wrong']);
        }
    }

    public function getFooterLinks(){
        $footerLink = Term::where('type', 'page')->get();
        foreach($footerLink as $link){
            $link['link'] = "https://".tenant()->domain->domain."/page/".$link['slug'];
        }

        if($footerLink){
            return response()->json(["status" => 'true', "message" => 'Footer link get successfully','data' =>$footerLink]);
          }else{
            return response()->json(["status" => 'false', "message" => 'Something went wrong']);
        }
    }
}
