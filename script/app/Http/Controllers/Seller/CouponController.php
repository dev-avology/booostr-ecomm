<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Auth;
class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(!getpermission('products'),401);
        $posts=Coupon::latest();
        if (isset($request->src)) {
          $posts=  $posts->where('code','LIKE','%'.$request->src.'%');
        }
        $posts=$posts->paginate(30);
        return view("seller.coupon.index",compact('posts','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!getpermission('products'),401);
        return view("seller.coupon.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->min_amount_option;

        abort_if(!getpermission('products'),401);
         if (postlimitcheck() == false) {
            $errors['errors']['error']='Maximum post limit exceeded';
            return response()->json($errors,401);
        }
        $validated = $request->validate([
        'code' => 'required|unique:coupons,code',
        'code_name' => 'required|unique:coupons,coupon_code_name',
        'discount_type' => 'required',
        'start_from' => 'required'
        //  'start_from'=>'required|max:100',
        //  'will_expire'=>'required|max:100',
        ]);

        if($request->coupon_first == 'specific_product_or_cat'){
            $validated = $request->validate([
                'choose_specific_product_or_category' => 'required',
            ]);
        }

        if($request->choose_specific_product_or_category == 'product' || $request->choose_specific_product_or_category == 'category'){
            $validated = $request->validate([
                'coupon_id' => 'required',
            ]);
        }

        $coupon=new Coupon;
        $coupon->code=$request->code;
        $coupon->coupon_code_name=$request->code_name;

       
        $coupon->value=$request->price ?? 0;
        $coupon->is_percentage=$request->discount_type ?? 0;
        

        // $coupon->is_conditional=$request->is_conditional ?? 0;

        // if ($request->min_amount_option == 1) {
        //     $min_amount=$request->min_amount ?? 0;
        // }
        // else{
        //     $min_amount=0;
        // }

        $coupon->min_amount=$request->min_amount ?? 0;
        $coupon->min_amount_option=$request->min_amount_option;

        // if($request->date_checkbox == 1){
        $coupon->start_from=$request->start_from ?? now();
        $coupon->will_expire=$request->will_expire ?? null;

        $coupon->max_use=$request->max_use ?? 0;
        // }else{
        //     $coupon->date_checkbox=$request->date_checkbox ?? 0;
        //     $coupon->start_from='';
        //     $coupon->will_expire='';
        // }


        // $coupon->is_featured=$request->is_featured;
        if($request->coupon_first == 'all'){
            $coupon->coupon_for_name='all';
            $coupon->coupon_for_id = null;
        }else {
            $coupon->coupon_for_name=$request->choose_specific_product_or_category ?? '';
            $coupon->coupon_for_id=json_encode($request->coupon_id) ?? null;
        }
        // $coupon->status=$request->status;

        $coupon->save();
        return response()->json(['msg'=>'Coupon Created','redirect_to'=>route('seller.coupon.index')]);
    }
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!getpermission('products'),401);
        $info= Coupon::findorFail($id);

       

        return view("seller.coupon.edit",compact('info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {      
        abort_if(!getpermission('products'),401);

         if (postlimitcheck() == false) {
            $errors['errors']['error']='Maximum post limit exceeded';
            return response()->json($errors,401);
        }
        $validated = $request->validate([
         'code' => 'required',
         'code_name' => 'required',
         'discount_type' => 'required',
         'start_from' => 'required'
        ]);

        if($request->coupon_first == 'specific_product_or_cat'){
            $validated = $request->validate([
                'choose_specific_product_or_category' => 'required',
            ]);
        }

        // if($request->choose_specific_product_or_category == 'product' || $request->choose_specific_product_or_category == 'category'){
        //     $validated = $request->validate([
        //         'coupon_id' => 'required',
        //     ]);
        // }

        $coupon=Coupon::find($id);
        $coupon->code=$request->code;
        $coupon->coupon_code_name=$request->code_name;

       
        $coupon->value=$request->price ?? 0;
        $coupon->is_percentage=$request->discount_type ?? 0;
        

        // $coupon->is_conditional=$request->is_conditional ?? 0;

        // if ($request->min_amount_option == 1) {
        //     $min_amount=$request->min_amount ?? 0;
        // }
        // else{
        //     $min_amount=0;
        // }

        $coupon->min_amount=$request->min_amount ?? 0;
        $coupon->min_amount_option=$request->min_amount_option;
        // return ($request->max_use);

        // if($request->date_checkbox == 1){
        $coupon->start_from=$request->start_from ?? now();
        $coupon->will_expire=$request->will_expire ?? null;
        $coupon->max_use=$request->max_use ?? 0;

        // }else{
        //     $coupon->date_checkbox=$request->date_checkbox ?? 0;
        //     $coupon->start_from='';
        //     $coupon->will_expire='';
        // }


        // $coupon->is_featured=$request->is_featured;
        if($request->coupon_first == 'all'){
            $coupon->coupon_for_name='all';
            $coupon->coupon_for_id= null;
        }else {
            $coupon->coupon_for_name=$request->choose_specific_product_or_category ?? '';
            $coupon->coupon_for_id=json_encode($request->coupon_id) ?? null;
        }
        // $coupon->status=$request->status;

        $coupon->save();

       return response()->json('Coupon Updated Successfully...!!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(!getpermission('products'),401);
        Coupon::destroy($id);

        return back();
    }
}
