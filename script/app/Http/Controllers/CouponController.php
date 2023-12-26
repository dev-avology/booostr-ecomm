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

        if (Cart::count() == 0) {
            return ['error'=>'count_error','msg' => "Coupon code can't apply"];
        }

        Cart::setGlobalDiscount(0);


        $coupon=Coupon::where('code',$request->coupon_code)->first();
        if(empty($coupon)){
            return ['error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
        }

        $total_amount=str_replace(',','',Cart::total());
        $total_discount=str_replace(',','',Cart::discount());
        $mydate= Carbon::now()->toDateString();
        $coupon=Coupon::where('code',$request->coupon_code)
                    ->where('start_from','<=',$mydate)
                    ->where('will_expire','>=',$mydate)
                    ->where('status',1)
                    ->latest()
                    ->first();
        if ($coupon == null) {
             return ['error'=>'not_exit_error','msg' => "Oops this coupon is not available..."];
        }

        if ($coupon->min_amount_option == 1) {
                
            if ($total_amount < $coupon->min_amount) {
                return ['error'=>'min_amount_error','msg' => 'The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon'];
           }
        }

        $total=str_replace(',','',Cart::total());

        $cartContent = Cart::content();
        if($coupon->coupon_for_name == 'product'){

            $product_id = $coupon->coupon_for_id;
            $exists = $cartContent->contains('id', $product_id);
            if ($exists) {

                return $this->returnCouponData($coupon,$total);

            } else {
                return ['error'=>'count_error','msg' => "Coupon code can't apply"];
            }
        }elseif($coupon->coupon_for_name == 'all'){

            return $this->returnCouponData($coupon,$total);

        }elseif($coupon->coupon_for_name == 'category'){

            $cartContent = Cart::content();
            $productIdsInCart = $cartContent->pluck('id')->toArray();
            $cat_id = $coupon->coupon_for_id;

            $termData = Term::with(['category' => function ($query) use ($cat_id) {
                $query->where('id', $cat_id);
            }])
            ->whereIn('id', $productIdsInCart)
            ->where('type', 'product')
            ->whereHas('category', function ($query) use ($cat_id) {
                $query->where('id', $cat_id);
            })
            ->get();

            if ($termData->count() > 0) {

                return $this->returnCouponData($coupon,$total);

            } else {
                
                return ['error'=>'count_error','msg' => "Coupon code can't apply"];

            }

        }
    }

    public function returnCouponData($coupon,$total){

        if ($coupon->is_percentage == 1) {
        
            $flat_discount=$coupon->value;
            $percent=($total*$flat_discount)/100;

            $totalPercentDis = $total-$percent;
            $cartDiscount = [
            'totalDiscount' => number_format($totalPercentDis, 2),
            'onlydiscount' => number_format($percent, 2)
            ];

            Session::put('couponDiscount',$cartDiscount);
            
            return ['type'=>1,'discountArr' => $cartDiscount];
        }

        else{
        
            $flatTotalDiscount = $total - $coupon->value;

            $cartDiscount = [
                'totalDiscount' => number_format($flatTotalDiscount, 2),
                'onlydiscount' => number_format($coupon->value, 2)
            ];

            Session::put('couponDiscount',$cartDiscount);
        
            return ['type'=>0,'discountArr' => $cartDiscount];
        }
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
