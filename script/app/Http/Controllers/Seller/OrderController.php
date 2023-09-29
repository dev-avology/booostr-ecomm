<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Category;
use App\Lib\NotifyToUser;
use App\Models\User;
use App\Models\Orderstock;
use App\Models\Price;
use Auth;
use DB;
use App\Models\Getway;

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
       $orders=$orders->latest()->paginate(30);
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
        $order_status=Category::where([['type','status'],['status',1]])->orderBy('featured','ASC')->get();
        if ($info->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }
        
        return view('seller.order.show',compact('info','ordermeta','order_status','riders'));
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
        $order_status=Category::where([['type','status'],['status',1]])->orderBy('featured','ASC')->get();

        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }

        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders'));
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
        abort_if(!getpermission('order'),401);

        DB::beginTransaction();
        try { 

        $info=Order::findorFail($id);
        if ($request->mail_notify) {
            $info->with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule');
        }
        $info->status_id=$request->status;
        $info->payment_status=$request->payment_status;
        $info->save();

        if ($info->order_method == 'delivery') {
            if ($request->rider) {
                $arr=['user_id'=>$request->rider ?? null];
                if ($request->rider_notify) {
                    $arr=['user_id'=>$request->rider ?? null,'status_id'=>3];
                }

                $info->shipping()->update($arr);
            }
        }

        if ($request->mail_notify) {
            
            NotifyToUser::makeNotifyToUser($info);
        }

        if ($request->rider_notify) {
          
            NotifyToUser::makeNotifyToRider($info);
        }

        if ($request->status == 1) {
          $deletable_ids=[];

          $prices=Orderstock::where('order_id',$id)->whereHas('price')->with('price')->get();
          foreach ($prices as $key => $row) {
             $current_stock=$row->price->qty;
             $order_stock=$row->qty;

             if ($order_stock >= $current_stock) {
                 $new_stock=0;
                 $stock_status=0;
             }
             else{
                $new_stock=$current_stock-$order_stock;
                $stock_status=1;
             }
             $price_row=Price::find($row->price_id);
             if (!empty($price_row)) {
                 $price_row->qty=$new_stock;
                 $price_row->stock_status=$stock_status;
                 $price_row->save();
             }
             array_push($deletable_ids,$row->id);
          }

          if (count($deletable_ids) != 0) {
               Orderstock::whereIn('id',$deletable_ids)->delete();
          } 

        }

           DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
            $errors['errors']['error']='Opps something wrong';
            return response()->json($errors,401);
        } 
        return response()->json('Order Updated');
    }


    public function print($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);
        $ordermeta=json_decode($order->ordermeta->value ?? '');
        $order_status=Category::where([['type','status'],['status',1]])->orderBy('featured','ASC')->get();

        if ($order->order_method == 'delivery') {
           $riders=User::where('role_id',5)->latest()->get();
        }
        else{
            $riders=[];
        }
        return view('seller.order.invoice_print',compact('order','ordermeta','order_status','riders'));
    }

    public function capture($id)
    {
        abort_if(!getpermission('order'),401);
        $order = Order::with('orderstatus','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($id);

        $gateway=Getway::where('status','!=',0)->where('namespace','=','App\Lib\Stripe')->first();
        $ordermeta=json_decode($order->ordermeta->value ?? '');

        $gateway_data_info = json_decode($gateway->data);
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['currency']   = $gateway->currency_name ?? 'USD';
        $payment_data['getway_id']  = $gateway->id;
        $payment_data['amount']  = $order->total;
        $payment_data['transaction_id']  = $order->transaction_id;

        $payment_data['application_fee_amount']  = $ordermeta->booster_platform_fee??0;
        $payment_data['card_fee_amount']  = $ordermeta->credit_card_fee??0;

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

        $paymentresult= $gateway->namespace::capture_payment($payment_data);

        if ($paymentresult['payment_status'] == '1') {
            $order->payment_status = 1;
            $order->save();
        }
        return redirect()->back();
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       abort_if(!getpermission('order'),401);

      

       if ($request->method == 'delete') {
          Order::whereIn('id',$request->ids)->delete();
          $msg='Status Order Deleted';
          return response()->json();
       }
       else{
         Order::whereIn('id',$request->ids)->update(['status_id' => $request->method]);
         $msg='Status Updated Successfully';

        
       }


       
    }
}
