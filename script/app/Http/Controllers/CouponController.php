<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;
use Auth;
use Session;
use Cart;

class CouponController extends Controller
{
    public function applyCoupon(Request $request)
    {

        if (Cart::count() == 0) {
             $errors['errors']['error']='Please add some product in your cart';
             return response()->json($errors,401);
        }
        Cart::setGlobalDiscount(0);
        $validated = $request->validate([
          'coupon_code'=>'required|max:100'
        ]);
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
             $errors['errors']['error']='Oops this coupon is not available...';
             return response()->json($errors,401);
        }

        if ($coupon->is_conditional == 1) {
                
            if ($total_amount < $coupon->min_amount) {
                $errors['errors']['error']='The minumum order amount is '.number_format($coupon->min_amount,2).' for this coupon';
                    return response()->json($errors,401);
           }
        }

        $total=str_replace(',','',Cart::total());

        if ($coupon->is_percentage == 1) {
            $total_amount=$total_amount-$coupon->value;
            $total_discount=$coupon->value;

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
}
