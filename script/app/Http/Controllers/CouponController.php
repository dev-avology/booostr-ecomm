<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Term;
use App\Models\Category;
use Carbon\Carbon;
use Auth;
use Session;
use Cart;

class CouponController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $today = Carbon::now();

        $sub_total = Cart::subtotal();      

        $cart_count = Cart::count();

        $error = false;


        Cart::setGlobalDiscount(0);
        Session::forget('couponDiscountCode');

        if (Cart::count() == 0) {
            $error = true;

            $res =  ['status'=>422,'error'=>'count_error','msg' => "Cart doesn't exit"];
        }

        $coupon=Coupon::where('code',$request->coupon_code)->first();
        if(!$error && empty($coupon)){
            $error = true;
            $res =  ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
        }


        if(!$error && !Carbon::parse($coupon->start_from)->lessThanOrEqualTo($today)){
            $error = true;
            $res = ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
         }


        if (!$error && $coupon->will_expire != null && Carbon::parse($coupon->will_expire)->lessThan($today) ) {
            $error = true;     
            $res = ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is expired..."];
        }

      

        if(!$error && $coupon->max_use > 0 && $coupon->max_use <= $coupon->used_count){
            $error = true; 
            $res = ['status'=>422,'error' => 'count_error', 'msg' => "Coupon code is max used"];
        }


        if(!$error){
            $couponType = $coupon->coupon_for_name;
            // return $couponType;

            switch ($couponType) {
                case 'product':
                    $discount = $this->DiscountCartProduct($coupon);
                    break;
                case 'category':
                    $discount = $this->DiscountCartCategoryProduct($coupon);
                    break;
                default:
                    $discount = $this->DiscountCart($coupon);
                    break;
            }
        
        if($discount['status'] == 422){
            $error = true;
            $res =$discount; 
        }
      }

        $data = [];
        $data['subtotal'] = Cart::subtotal();
        $data['discount'] = Cart::discount();
        $data['tax'] = Cart::tax();
        $data['total'] = Cart::total();
        $data['items_on_discount'] = Cart::content();
      
        if($error){
            $res['data'] = $data;
            return response()->json($res); 
        }else{
            return response()->json(['status'=>200,'result'=>$data]); 
        }
    }

   

    private function DiscountCart(Coupon $coupon){
      
        if ($coupon->min_amount_option == 1) {
            if ($sub_total <= $coupon->min_amount) {
                return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon'];
           }
        }


        if ($coupon->min_amount_option == 2) {

            if(Cart::count() <= $coupon->min_amount){
                return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order item count is '.number_format($coupon->min_amount,2).' for this coupon'];
            }
        }

        if ($coupon->is_percentage == 1) {
            $percent= $coupon->value;

        }else{

            $total= (float) Cart::subtotal();
            $flat_discount=$coupon->value;
            $percent=($flat_discount*100)/$total;
        }

        Cart::setGlobalDiscount($percent);

        $discount = Cart::discount();

        return ['status'=>200,'discount'=>$discount]; 
    }


    private function DiscountCartProduct(Coupon $coupon){

            $cartContent = Cart::content();
            
            $product_ids = json_decode($coupon->coupon_for_id);
                
            $filteredCart = $cartContent->filter(function ($item) use ($product_ids) {
                return in_array($item->id, $product_ids);
            });

            $filteredCartCount = $filteredCart->count();


           

            $filteredSubTotal = $filteredCart->sum(function ($item) {
                return $item->price * (int)$item->qty;
            });
        
            $filteredCount = $filteredCart->sum(function ($item) {
                return (int)$item->qty;
            });

            if ($filteredCartCount == 0) {
                return ['status'=>422,'error' => 'count_error', 'msg' => "Coupon code is not valid for your cart"];
            }


            if ($coupon->min_amount_option == 1) {
                if ($filteredSubTotal <= $coupon->min_amount) {
                    return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon, cart product subtotal:'.number_format($filteredSubTotal)];
               }
            }
    
    
            if ($coupon->min_amount_option == 2) {

                if((int)$filteredCount < (int)$coupon->min_amount){
                    return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order item count is '.(int)$coupon->min_amount.' for this coupon'];
                }
            }


            $filteredCart->each(function ($item, $key) use ($coupon) {
               
                if ($coupon->is_percentage == 1) {
                    $percent= $coupon->value;
                }else{
                    $total= (float) $item->price;
                    $flat_discount=$coupon->value;
                    $percent=($flat_discount*100)/$total;
                }

                $item->setDiscountRate($percent);

            });

            $discount = Cart::discount();
            return ['status'=>200,'discount'=>$discount]; 
    }


    private function DiscountCartCategoryProduct(Coupon $coupon){

        $cartContent = Cart::content();

        $cat_id = json_decode($coupon->coupon_for_id);

            $termData = Term::where('type', 'product')->whereHas('category', 
            function ($query) use ($cat_id) {
                 $query->whereIn('id', $cat_id);
                 })->pluck('id')->toArray();


            $filteredCart = $cartContent->filter(function ($item) use ($termData) {
                return in_array($item->id, $termData);
            });

            $filteredCartCount = $filteredCart->count();

            $filteredSubTotal = $filteredCart->sum(function ($item) {
               return $item->price * $item->qty;
            });

            $filteredCount = $filteredCart->sum(function ($item) {
                return (int)$item->qty;
            });


            if ($coupon->min_amount_option == 1) {
                if ($filteredSubTotal <= $coupon->min_amount) {
                    return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon'];
               }
            }
    
            if ($coupon->min_amount_option == 2) {
                if((int)$filteredCount < (int)$coupon->min_amount){
                    return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order item count is '.(int)$coupon->min_amount.' for this coupon'];
                }
            } 

            $filteredCart->each(function ($item, $key) use ($coupon) {
               
                if ($coupon->is_percentage == 1) {
                    $percent= $coupon->value;
                }else{
                    $total= (float) $item->price;
                    $flat_discount=$coupon->value;
                    $percent=($flat_discount*100)/$total;
                }
                $item->setDiscountRate($percent);
            });
           
            $discount = Cart::discount();
            return ['status'=>200,'discount'=>$discount]; 
    }

    public function getCouponType($type){

        if($type == 'product'){
           $data['term'] = Term::where('type','product')->get();
           $data['type'] = "product";
           return response()->json($data);
        }else if($type == 'category'){
           $data['term'] = Category::where('type','category')->get();
           $data['type'] = "category";
          
           return response()->json($data);
        }
    }

    public function generateCode(){
        $couponCode = getCouponCode();
        return response()->json(['status' => 200, 'code' => $couponCode]);
    }
}
