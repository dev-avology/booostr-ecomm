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

        Cart::setGlobalDiscount(0);
        Session::forget('couponDiscountCode');

        if (Cart::count() == 0) {
            return ['status'=>422,'error'=>'count_error','msg' => "Coupon code doesn't exit"];
        }

        $coupon=Coupon::where('code',$request->coupon_code)->first();
        if(empty($coupon)){
            return ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
        }


        $today = Carbon::now();

        $sub_total = Cart::subtotal();      

         if(!Carbon::parse($coupon->start_from)->lessThanOrEqualTo($today)){
            return ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
         }


        
        if ( $coupon->will_expire != null && Carbon::parse($coupon->will_expire)->lessThan($today) ) {
            return ['status'=>422,'error'=>'not_exit_error','msg' => "Oops this coupon is expired..."];
        }

        if ($coupon->min_amount_option == 1) {
              
                 
            if ($sub_total < $coupon->min_amount) {
                return ['status'=>422,'error'=>'min_amount_error','msg' => 'The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon'];
           }
        }

        $cartContent = Cart::content();

        if($coupon->coupon_for_name == 'product'){

            $product_ids = json_decode($coupon->coupon_for_id);

                
            $filteredCart = $cartContent->filter(function ($item) use ($product_ids) {
                return in_array($item->id, $product_ids);
            });
                
              
            if ($filteredCart->count() == 0) {
                return ['status'=>422,'error' => 'count_error', 'msg' => "Coupon code is not valid for your cart"];
            }
        

        }elseif($coupon->coupon_for_name == 'category'){


            $cat_id = json_decode($coupon->coupon_for_id);

            $termData = Term::where('type', 'product')->whereHas('category', 
            function ($query) use ($cat_id) {
                 $query->whereIn('id', $cat_id);
                 })->pluck('id')->toArray();

            $filteredCart = $cartContent->filter(function ($item) use ($termData) {
                return in_array($item->id, $termData);
             });
            
            
        if ($filteredCart->count() == 0) {
            return ['status'=>422,'error' => 'count_error', 'msg' => "Coupon code is not valid for your cart"];
        }

        }

        if($coupon->max_use > 0 && $coupon->max_use <= $coupon->used_count){
            return ['status'=>422,'error' => 'count_error', 'msg' => "Coupon code is max used"];
        }


        if ($coupon->is_percentage == 1) {
            $percent= $coupon->value;

        }else{

            $total= (float) Cart::subtotal();
            $flat_discount=$coupon->value;
            $percent=($flat_discount*100)/$total;
        }

        Cart::setGlobalDiscount($percent);
        Session::put('couponDiscountCode',$coupon->code);

        $data = [];
        $data['subtotal'] = Cart::subtotal();
        $data['discount'] = Cart::discount();
        $data['tax'] = Cart::tax();
        $data['total'] = Cart::total();

        return response()->json(['status'=>200,'result'=>$data]); 

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

    public function removeCouponSession(){
        Session::forget('couponDiscount');
        return true;
    }
}
