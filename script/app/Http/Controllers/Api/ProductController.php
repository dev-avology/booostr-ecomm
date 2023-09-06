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
use Carbon\Carbon;
use Cart;
use DB;
use Auth;

class ProductController extends Controller
{

    public function productList(Request $request)
    {
        $posts = Term::query()->where('type', 'product')->with('media', 'firstprice', 'lastprice')->whereHas('firstprice')->whereHas('lastprice');
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
        $info=Term::query()->where('type','product')->where('status',1)->with('tags','brands','excerpt','description','preview','medias','optionwithcategories','price','seo')->withCount('reviews')->where('id', $id)->first();
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
            ->with('media', 'firstprice', 'lastprice')
            ->whereHas('firstprice')
            ->whereHas('lastprice')
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('full_id', 'like', '%' . $request->keyword . '%');
            })->latest()->paginate(100);

        return response()->json(["status" => true, "message" => "searched products", "result" => $posts]);
    }

    public function addtocart(Request $request){
        
        $cartid=!empty($request->header('cartid'))?$request->header('cartid'):Str::random(10);
        $info='';
        if ($request->id) {
            $info = Term::query()->where('id',$request->id)->where('type', 'product')->where('status', 1)->first();
        } 
        if(empty( $info)){
            return response()->json(["status" => 0, "message" => 'Opps product not available', "result" => []]);
        }
        
        if ($info->is_variation == 1) {
            $groups = [];
            foreach ($request->option ?? [] as $key => $option) {
                $option_values = [];
                foreach ($option as $k => $value) {
                    array_push($option_values, $value);
                }

                $group = Productoption::with(array('priceswithcategories' => function ($query) use ($option_values) {
                    return $query->whereIn('id', $option_values);
                }))->with('category')->where('id', $key)->first();

                array_push($groups, $group);
            }

            $final_price = 0;
            $final_weight = 0;
            $price_option = [];
            $priceids = [];
            foreach ($groups as $key => $row) {


                foreach ($row->priceswithcategories as $key => $value) {
                    if ($value->stock_manage == 1) {
                        array_push($priceids, $value->id);
                    }
                    $final_price = $final_price + $value->price;
                    $final_weight = $final_weight + $value->weight;

                    $price_option[$row->category->name][$value->id]['price'] = $value->price;
                    $price_option[$row->category->name][$value->id]['sku'] = $value->sku;
                    $price_option[$row->category->name][$value->id]['weight'] = $value->weight;
                    $price_option[$row->category->name][$value->id]['name'] = $value->category->name;
                }
            }

            //Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $final_price, 'weight' => $final_weight, 'options' => ['options' => $price_option, 'sku' => null, 'stock' => null, 'price_id' => $priceids]]);
        } else {
            $price = $info->firstprice;
            $weight = $price->weight ?? 0;
            $options = [
                'sku' => $price->sku,
                'stock' => $price->qty,
                'options' => [],
            ];
            if ($price->stock_manage == 1 && $price->stock_status == 1) {
                $options['stock'] = $price->qty;
                $options['price_id'] = [$price->id];
            } else {
                $options['stock'] = null;
            }
            Cart::session($cartid)->add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $price->price, 'weight' => $weight, 'options' => $options]);
        }
        $productcartdata['cartid'] = $cartid;
        $productcartdata['cart_content'] = Cart::session($cartid)->content();
        $productcartdata['cart_subtotal'] = Cart::session($cartid)->subtotal();
        $productcartdata['cart_tax'] = Cart::session($cartid)->tax();
        $productcartdata['cart_total'] = Cart::session($cartid)->total();
        return response()->json(["status" => true, "message" => 'Added to Cart Sucessfullly', "result" => $productcartdata]);
    }




    public function removecart(Request $request,$id)
    {
        $cartid=!empty($request->header('cartid'))?$request->header('cartid'):"";
        if(empty($cartid)){
            return response()->json(["status" => 0, "message" => 'Opps cart not found', "result" => []]);
        }

        $rowid=Cart::session($cartid)->search(function ($cartItem, $rowId) use($id) {
            return $cartItem->id === $id;
        });
        Cart::session($cartid)->remove($rowid);
        $productcartdata['cart_subtotal'] = Cart::session($cartid)->subtotal();
        $productcartdata['cart_tax'] = Cart::session($cartid)->tax();
        $productcartdata['cart_total'] = Cart::session($cartid)->total();

        return response()->json(["status" => true, "message" => 'Removed From Cart Sucessfullly', "result" => $productcartdata]);
    }

    public function CartQty(Request $request)
    {
        Cart::update($request->id, $request->qty);
        $productcartdata['cart_subtotal'] = Cart::subtotal();
        $productcartdata['cart_tax'] = Cart::tax();
        $productcartdata['cart_total'] = Cart::total();

        return response()->json(["status" => true, "message" => 'Cart Updated  Sucessfullly', "result" => $productcartdata]);
    }

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
                $errors['errors']['error'] = 'Opps this coupon is not available...';
                return response()->json(["status" => 0, "message" => 'Opps this coupon is not available...', "result" => $errors], 401);
            }

            if ($coupon->is_conditional == 1) {

                if ($total_amount < $coupon->min_amount) {
                    $errors['errors']['error'] = 'The minumum order amount is ' . number_format($coupon->min_amount, 2) . ' for this coupon';
                    return response()->json(["status" => 0, "message" => 'Opps this coupon has minumum order imit', "result" => $errors], 401);
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
            $errors['errors']['error'] = 'Opps something wrong';
            return response()->json(["status" => 0, "message" => 'Opps something wrong', "result" => $errors], 401);
        }

        return response()->json(["status" => true, "message" => 'Order Placed']);
    }
}
