<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Term;
use App\Models\Productoption;
use Carbon\Carbon;
use Cart;
use DB;
use App\Models\Coupon;


class CartController extends Controller
{
    

    public function addtowishlist(Request $request){
        $getrowid=$request->getrowid ?? false;

        $info=Term::query()->where('type','product')->where('status',1)->webProducts()->with('preview','firstprice')->findOrFail($request->id);

        $wishlist = Cart::instance('wishlist')->content();
        $existingRow = $wishlist->firstWhere('id', $info->id);

        if ($existingRow) {
            Cart::instance('wishlist')->remove($existingRow->rowId);
            $action = 'removed';
            $message = __('Removed from Wishlist');
            $rowId = $existingRow->rowId;
        } else {
            $price=$info->firstprice;
            $weight=$price->weight ?? 0;
            $options=[
                'sku'=>$price->sku,
                'stock'=>$price->qty,
                'options'=>[],
                'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                'slug'=>$info->slug
            ];

            if ($price->stock_manage == 1 && $price->stock_status == 1) {
               $options['stock']=$price->qty;
            } else {
               $options['stock']=null;
            }

            $cart=Cart::instance('wishlist')->add(['id' => $info->id, 'name' => $info->title, 'qty' => 1, 'price' => $price->price, 'weight' => 0, 'options' => $options]);
            $action = 'added';
            $message = __('Added to Wishlist');
            $rowId = $cart->rowId ?? '';
        }

        $count=Cart::instance('wishlist')->count();
        $wishlist_ids = Cart::instance('wishlist')->content()->pluck('id')->map(function($id){ return (int)$id; })->values()->all();

        if ($getrowid != false) {
           $data['rowid']=$rowId;
           $data['count']=$count ?? 0;
           $data['action']=$action;
           $data['message']=$message;
           $data['wishlist_ids']=$wishlist_ids;

           return $data;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'action' => $action,
                'message' => $message,
                'count' => $count,
                'product_id' => (int)$info->id,
                'wishlist_ids' => $wishlist_ids,
                'status' => true
            ]);
        }

        return $count;
    }

    public function makediscount(Request $request)
    {
   
        if (Cart::count() == 0) {
             $errors['errors']['error']='Please add some product in your cart';
             return response()->json($errors,401);
        }
        $validated = $request->validate([
          'coupon'=>'required|max:100'
        ]);
        $total_amount=str_replace(',','',Cart::total());
        $total_discount=str_replace(',','',Cart::discount());
        $mydate= Carbon::now()->toDateString();
        $coupon=Coupon::where('code',$request->coupon)
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

        if ($coupon->is_percentage == 0) {
            $total_amount=$total_amount-$coupon->value;
            $total_discount=$coupon->value;

            $total=str_replace(',','',Cart::total());
            $flat_discount=$coupon->value;
            $percent=($flat_discount*100)/$total;
            
            Cart::setGlobalDiscount($percent);
        }
        else{
            Cart::setGlobalDiscount($coupon->value);
             
        }


        return response()->json('Coupon Applied');
    }

    public function express(Request $request)
    {
        
       if ($request->qty == 0) {
           $errors['errors']['error']='Minimum cart quantity 1';
           return response()->json($errors,401);
       }
        
        if ($request->term) {
            $info=Term::query()->where('type','product')->where('status',1)->webProducts()->with('preview')->findOrFail($request->term);
        }
        
        
        if ($info->is_variation == 1) {
                $groups=[];
                foreach ($request->option ?? [] as $key => $option) {
                    $option_values=[];
                    foreach ($option as $k => $value) {
                    array_push($option_values,$value);
                    }
                    
                    $group=Productoption::with(array('priceswithcategories'=>function($query) use ($option_values){
                            return $query->whereIn('id',$option_values);
                    }))->with('category')->where('id',$key)->first();

                    array_push($groups,$group);
                     
                 }
                   
                $final_price=0;
                $final_weight=0;   
                $price_option=[];
                $stock_status=true;
                $stock_limit=true;
                $priceids=[];
                foreach($groups as $mainid => $row){
                   
                    
                    foreach ($row->priceswithcategories as $key => $value) {
                        if ($value->stock_status == 0) {
                            $stock_status=false;
                        }
                        if ($value->stock_manage == 1) {
                            if ($value->qty < $request->qty) {
                               $stock_limit=false;
                            }

                            array_push($priceids,$value->id);
                        }
                        $final_price=$final_price+$value->price;
                        $final_weight=$final_weight+$value->weight;

                        $price_option[$row->category->name][$value->id]['price']=$value->price;
                        $price_option[$row->category->name][$value->id]['sku']=$value->sku;
                        $price_option[$row->category->name][$value->id]['weight']=$value->weight;
                        $price_option[$row->category->name][$value->id]['name']=$value->category->name;
                        $price_option[$row->category->name][$value->id]['price_id']=$value->id;
                         
                        
                    }
                }

                if ($final_price == 0) {
                    $errors['errors']['error']='Select a required variation';
                    return response()->json($errors,401);
                }

                if ($stock_status == false) {
                    $errors['errors']['error']='Stock Out';
                    return response()->json($errors,401);
                }

                 if ($stock_limit == false) {
                    $errors['errors']['error']='Maximum stock limit exceeded';
                    return response()->json($errors,401);
                }

                Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $final_price, 'weight' => $final_weight, 'options' => [
                    'options'=>$price_option,
                    'sku'=>null,
                    'stock'=>null,
                    'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                    'slug'=>$info->slug,
                    'price_id'=>$priceids
                    
                ]]);
               
                
        }
        else{
            $price=$info->firstprice;
            $weight=$price->weight ?? 0;
            $ticketOptions = $this->getTicketOptions($info->id);
            $options=[
                
                'sku'=>$price->sku,
                'stock'=>$price->qty,
                'options'=>[],
                'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                'slug'=>$info->slug,
                'product_kind' => $ticketOptions['product_kind'],
                'ticket_fee' => $ticketOptions['ticket_fee'],
       
                
               
            ];

            

            if ($price->stock_manage == 1 && $price->stock_status == 1) {
               $options['stock']    = $price->qty;
               $options['price_id'] = [$price->id];
            }
            else{
               $options['stock']=null;
            }

            Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $price->price, 'weight' => $weight, 'options' => $options]);
        }

       
        return redirect('/checkout');
        
    }

    public function addtocart(Request $request)
    {

       if ($request->qty == 0) {
           $errors['errors']['error']='Minimum cart quantity 1';
            return response()->json($errors,401);
       }
        
        if ($request->id) {
            $info=Term::query()->where('type','product')->where('status',1)->webProducts()->with('preview')->findOrFail($request->id);
        }
        Session::put('cartid', 'default');
        Cart::instance('default');
        if ($info->is_variation == 1) {
            $hasCombinationPrices = \App\Models\Price::where('term_id', $info->id)->whereHas('varitions')->exists();
            if ($hasCombinationPrices) {
                if (empty($request->variation_id)) {
                    $errors['errors']['error'] = __('Please select all required options');
                    return response()->json($errors, 401);
                }
                $varPrice = \App\Models\Price::where('term_id', $info->id)->where('id', $request->variation_id)->with('varitions')->first();
                if (!$varPrice) {
                    $errors['errors']['error'] = __('Selected variation not found');
                    return response()->json($errors, 404);
                }
                if ($varPrice->stock_status == 0) {
                    $errors['errors']['error'] = __('Selected variation is out of stock');
                    return response()->json($errors, 401);
                }

                $cart_content = Cart::instance('default')->content();
                $exist_qty = 0;
                foreach ($cart_content as $key => $row) {
                    if ($row->id == $info->id && isset($row->options->variation_id) && $row->options->variation_id == $varPrice->id) {
                        $exist_qty = (int)($row->qty ?? 0);
                    }
                }

                if ($varPrice->stock_manage == 1) {
                    if (($exist_qty + $request->qty) > $varPrice->qty) {
                        $errors['errors']['error'] = __('Maximum stock limit is ('.$varPrice->qty.')');
                        return response()->json($errors, 401);
                    }
                }

                $price_option = [];
                foreach ($varPrice->varitions as $sel_val) {
                    $optGroup = \App\Models\Productoption::with('category')->find($sel_val->pivot->productoption_id);
                    $groupName = $optGroup->category->name ?? 'Option';
                    $price_option[$groupName][$sel_val->id] = [
                        'name' => $sel_val->name,
                        'price' => $varPrice->price,
                        'sku' => $varPrice->sku,
                        'weight' => $varPrice->weight ?? 0,
                        'price_id' => $varPrice->id
                    ];
                }

                $price_to_use = (float) $varPrice->price;
                $write_in_amount_val = null;
                if ((int)($varPrice->is_write_in_amount_enabled ?? 0) === 1) {
                    $minAmount = max(0.75, (float)$varPrice->price);
                    $entered = (float)str_replace([',', '$'], '', $request->write_in_amount ?? 0);
                    if ($entered < $minAmount) {
                        $errors['errors']['error'] = __('Amount must be equal to or greater than minimum amount ($:min)', ['min' => number_format($minAmount, 2)]);
                        return response()->json($errors, 401);
                    }
                    $price_to_use = $entered;
                    $write_in_amount_val = $entered;
                }

                $ticketOptions = $this->getTicketOptions($info->id);
                $data = Cart::instance('default')->add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $price_to_use, 'weight' => $varPrice->weight ?? 0, 'options' => [
                    'options' => $price_option,
                    'variation_id' => $varPrice->id,
                    'sku' => $varPrice->sku,
                    'stock' => $varPrice->stock_manage == 1 ? $varPrice->qty : null,
                    'preview' => asset($info->preview->value ?? 'uploads/default.png'),
                    'slug' => $info->slug,
                    'price_id' => [$varPrice->id],
                    'cartid' => Session::get('cartid', 'default'),
                    'product_kind' => $ticketOptions['product_kind'],
                    'ticket_fee' => $ticketOptions['ticket_fee'],
                    'write_in_amount' => $write_in_amount_val,
                ]]);
            }
            else {
                $required_groups = \App\Models\Productoption::where('term_id', $info->id)->where('is_required', 1)->pluck('id')->toArray();
                foreach ($required_groups as $req_gid) {
                    $submitted = $request->option[$req_gid] ?? [];
                    $valid_values = is_array($submitted) ? array_filter($submitted, function($v) {
                        return !is_null($v) && $v !== '';
                    }) : (!empty($submitted) ? [$submitted] : []);
                    if (empty($valid_values)) {
                        $errors['errors']['error'] = __('Please select all required options');
                        return response()->json($errors, 401);
                    }
                }
                $groups=[];
                foreach ($request->option ?? [] as $key => $option) {
                    $option_values=[];
                    foreach ($option as $k => $value) {
                    array_push($option_values,$value);
                    }
                    
                    $group=Productoption::with(array('priceswithcategories'=>function($query) use ($option_values){
                            return $query->whereIn('id',$option_values);
                    }))->with('category')->where('id',$key)->first();

                    array_push($groups,$group);
                     
                 }
                   
                $final_price=0;
                $final_weight=0;   
                $price_option=[];
                $stock_status=true;
                $stock_limit=true;
                $priceids=[];
                foreach($groups as $mainid => $row){
                   
                    
                    foreach ($row->priceswithcategories as $key => $value) {
                        if ($value->stock_status == 0) {
                            $stock_status=false;
                        }
                        if ($value->stock_manage == 1) {
                            if ($value->qty < $request->qty) {
                               $stock_limit=false;
                            }

                            array_push($priceids,$value->id);
                        }
                        $final_price=$final_price+$value->price;
                        $final_weight=$final_weight+$value->weight;

                        $price_option[$row->category->name][$value->id]['price']=$value->price;
                        $price_option[$row->category->name][$value->id]['sku']=$value->sku;
                        $price_option[$row->category->name][$value->id]['weight']=$value->weight;
                        $price_option[$row->category->name][$value->id]['name']=$value->category->name;
                        $price_option[$row->category->name][$value->id]['price_id']=$value->id;
                         
                        
                    }
                }

                if ($final_price == 0) {
                    $errors['errors']['error']='Select a required variation';
                    return response()->json($errors,401);
                }

                if ($stock_status == false) {
                    $errors['errors']['error']='Stock Out';
                    return response()->json($errors,401);
                }

                 if ($stock_limit == false) {
                    $errors['errors']['error']='Maximum stock limit exceeded';
                    return response()->json($errors,401);
                }

                $ticketOptions = $this->getTicketOptions($info->id);
             $data =    Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $final_price, 'weight' => $final_weight, 'options' => [
                    'options'=>$price_option,
                    'sku'=>null,
                    'stock'=>null,
                    'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                    'slug'=>$info->slug,
                    'price_id'=>$priceids,
                    'cartid' => Session::get('cartid', 'default'),
                    'product_kind' => $ticketOptions['product_kind'],
                    'ticket_fee' => $ticketOptions['ticket_fee'],
                ]]);
             
      
            }
        }
        else{
            $cart_content=Cart::instance('default')->content();
                
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

            if ($price->stock_manage == 1) {
                if ($exist_qty > $price->qty){
                    $errors['errors']['error']='Maximum stock limit is ('.$price->qty.')';
                    return response()->json($errors,401);
                }
            }
            
            $ticketOptions = $this->getTicketOptions($info->id);

            $options=[
            
                'sku'=>$price->sku,
                'stock'=>$price->qty,
                'options'=>[],
                'preview'=>asset($info->preview->value ?? 'uploads/default.png'),
                'slug'=>$info->slug,
                'product_kind' => $ticketOptions['product_kind'],
                'ticket_fee' => $ticketOptions['ticket_fee'],
            
            ];
            

            if ($price->stock_manage == 1 && $price->stock_status == 1) {
               $options['stock']    = $price->qty;
               $options['price_id'] = [$price->id];
            }
            
            if ($price->stock_status == 0) {
                $errors['errors']['error']='Oops Maximum stock limit exceeded';
                return response()->json($errors,401);
                
            }
            
            // Ticket products store retail price (base + service fee) in prices.price.
            $cartPrice = (float) $price->price;
            if ((int)($price->is_write_in_amount_enabled ?? 0) === 1) {
                $minAmount = max(0.75, (float)$price->price);
                $entered = (float)str_replace([',', '$'], '', $request->write_in_amount ?? 0);
                if ($entered < $minAmount) {
                    $errors['errors']['error'] = __('Amount must be equal to or greater than minimum amount ($:min)', ['min' => number_format($minAmount, 2)]);
                    return response()->json($errors, 401);
                }
                $cartPrice = $entered;
                $options['write_in_amount'] = $cartPrice;
            }

            Cart::add(['id' => $info->id, 'name' => $info->title, 'qty' => $request->qty, 'price' => $cartPrice, 'weight' => $weight, 'options' => $options]);
        }

        $ticketFeeTotal = 0;

        foreach (Cart::content() as $cartItem) {
            if (($cartItem->options->product_kind ?? '') == 'event_ticket') {
                $ticketFeeTotal += ((float) ($cartItem->options->ticket_fee ?? 0)) * (int) $cartItem->qty;
            }
        }

        $productcartdata['cart_content']=Cart::content();
        $productcartdata['cart_subtotal']=booostr_price(Cart::subtotal());
        $productcartdata['cart_tax']= Cart::tax();
        $productcartdata['cart_total']= booostr_price(Cart::total());
        $productcartdata['cart_count']= Cart::count();
        $productcartdata['ticket_fee_total'] = number_format($ticketFeeTotal, 2);
        $productcartdata['cart_total_with_ticket_fee'] = number_format(((float) str_replace(',', '', Cart::total())) + $ticketFeeTotal, 2);
        
        // ✅ cart id session me store karo (ONLY ONCE)
        if (!session()->has('cartid')) {
            session()->put('cartid', 'default');
        }
        return response()->json($productcartdata);
        
    }

    private function getTicketOptions($termId)
    {
        $productKind = DB::table('termmetas')
            ->where('term_id', $termId)
            ->where('key', 'product_kind')
            ->value('value');
    
        $ticketFee = DB::table('termmetas')
            ->where('term_id', $termId)
            ->where('key', 'ticket_fee')
            ->value('value');
    
        $isTicket = trim((string) $productKind) === 'event_ticket';
    
        return [
            'product_kind' => $isTicket ? 'event_ticket' : 'product',
            'ticket_fee' => $isTicket ? (float) ($ticketFee ?: 0.75) : 0,
        ];
    }




    public function removecart($id)
    {
        Cart::remove($id);
        $productcartdata['cart_subtotal']=booostr_price(Cart::subtotal());
        $productcartdata['cart_tax']= Cart::tax();
        $productcartdata['cart_total']= booostr_price(Cart::total());
        $productcartdata['cart_count']= Cart::count();
        $productcartdata['cart_content']= Cart::content();
        return response()->json($productcartdata);
    }

    public function removeWishlist(Request $request,$id)
    {
       Cart::instance('wishlist')->remove($id);
       $count = Cart::instance('wishlist')->count();
       $wishlist_ids = Cart::instance('wishlist')->content()->pluck('id')->map(function($id){ return (int)$id; })->values()->all();
       if ($request->ajax()) {
           return response()->json([
               'count' => $count,
               'wishlist_ids' => $wishlist_ids
           ]);
       }
       return back();
    }

    public function CartQty(Request $request)
    {
        Cart::update($request->id, $request->qty);
        $productcartdata['cart_subtotal']=booostr_price(Cart::subtotal());
        $productcartdata['cart_tax']= Cart::tax();
        $productcartdata['cart_total']= booostr_price(Cart::total());
        $productcartdata['cart_count']= Cart::count();
        $productcartdata['cart_content']= Cart::content();
        return response()->json($productcartdata);
    }

    public function varidation($id)
    {
         $info=Term::query()->where('type','product')->with('productoptionwithcategories')->findorFail($id);
         return response()->json($info);
    }

}
