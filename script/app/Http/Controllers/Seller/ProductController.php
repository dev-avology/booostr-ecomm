<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Term;
use App\Models\Price;
use App\Models\Orderitem;
use App\Models\EventTicket;
use App\Services\TicketCancelRefundService;
use App\Services\ProductSalesCrmSyncService;
use App\Models\Productoption;
use App\Models\Variationproductoption;
use DB;
use DNS1D;
use DNS2D;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use Auth;
use Error;
use Google\Service\NetworkManagement\RerunConnectivityTestRequest;
use GuzzleHttp\Client;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(!getpermission('products'), 401);
       
        $posts = Term::query()->where('type', 'product')->with('media', 'price','formType')->withCount('orders');
        if (!empty($request->src) && !empty($request->type)) {
            $posts = $posts->where($request->type, 'LIKE', '%' . $request->src . '%');
        }

        $selected_per_page = request()->get('per_page', 10);

        $posts = $posts->orderBy('order','asc')->paginate($selected_per_page);

        $type = $request->type ?? '';

        return view("seller.product.index", compact('posts', 'request', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!getpermission('products'), 401);
        
        $formApiData = $this->formApi();

        // dd($formApiData);
    
        $attributes = Category::query()->where('type', 'parent_attribute')->with('categories')->latest()->get();
        $features = Category::query()->where('type', 'product_feature')->orderBy('menu_status', 'ASC')->get();
        $product_type = Category::query()->where('type', 'product_type')->orderBy('id', 'ASC')->get();

        return view("seller.product.create", compact('attributes', 'features', 'product_type','formApiData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    public function formApi(){
        $club_id = (int)Tenant('club_id');
        // $club_id = 36115;
      //  $url = "https://staging3.booostr.co/wp-json/store-api/v1/get-store-form/?club_id=".$club_id;

        $url = env("WP_CLUB_URL");
        
        $url = ($url != '') ? $url."wp-json/store-api/v1/get-store-form/?club_id=".$club_id : "https://staging3.booostr.co/wp-json/store-api/v1/get-store-form/?club_id=".$club_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        
        $forms = [];
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $forms = isset($responseData['forms'])?$responseData['forms']: [];
            } else {
                echo 'JSON decoding error: ' . json_last_error_msg();
            }
        }
        curl_close($ch);

        $forms = is_array($forms) ? []: json_decode($forms);
        return $forms; 
    } 


      public function store(Request $request)
    {
       
        abort_if(!getpermission('products'), 401);
        // if (postlimitcheck() == false) {
        //     $errors['errors']['error']='Maximum product limit exceeded';
        //     return response()->json($errors,401);
        // }

        if ($request->product_type == 1) {
            $child_attr = Category::where('type','child_attribute')->first();
            if(empty($child_attr)){
                return response()->json(['msg'=>'Please add child attribute','msg_alert'=>1]);
            }
        }

        

        $validated = $request->validate([
            'name' => 'required|max:100',
            // 'short_description' => 'max:100000',
        ]);

        if ($request->product_type != 1) {
            $rules = [
                'price' => 'required|max:100',
            ];
        
            if ($request->product_type == 2) {
                $rules['ticket_sale_start'] = 'required';
                $rules['ticket_sale_end'] = 'required';
            }
        
            $validated = $request->validate($rules);
        } else {
            $validated = $request->validate([
                'childattribute' => 'required',
            ]);
        }


        DB::beginTransaction();
        try {
            $term = new Term;
            $term->title = $request->name;
            $term->slug = $term->makeSlug($request->name, 'product');
            $term->type = 'product';
            $term->status = $request->status;
            $term->is_variation = $request->product_type;
            $term->list_type = $request->list_type;
            $term->save();

            if ($request->product_type == 2) {

                $term->meta()->create([
                    'key' => 'product_kind',
                    'value' => 'event_ticket'
                ]);
            
            
                $term->meta()->create([
                    'key' => 'ticket_sale_start',
                    'value' => $request->ticket_sale_start
                ]);
            
                $term->meta()->create([
                    'key' => 'ticket_sale_end',
                    'value' => $request->ticket_sale_end
                ]);
            
                $term->meta()->create([
                    'key' => 'ticket_fee',
                    'value' => '0.75'
                ]);
            
                if ($request->ticket_instructions) {
                    $term->meta()->create([
                        'key' => 'ticket_instructions',
                        'value' => $request->ticket_instructions
                    ]);
                }
            }

            if ($request->short_description) {
                $term->meta()->create(['key' => 'excerpt', 'value' => $request->short_description]);
            }

            if ($request->form_type) {
                $term->meta()->create(['key' => 'form_type', 'value' => $request->form_type]);
            }

            if ($request->form_fields) {
                $term->meta()->create(['key' => 'form_fields', 'value' => $request->form_fields]);
            }

            if ($request->preview) {
                $term->meta()->create(['key' => 'preview', 'value' => $request->preview]);
            }

            if ($request->categories) {
                $term->categories()->attach($request->categories);
            }

            if ($request->product_type != 1) {
                $term->price()->create([
                    'price' => $request->price,
                    'qty' => $request->qty,
                    'sku' => $request->sku,
                    'weight' => 0,
                    'stock_manage' => $request->stock_manage,
                    'stock_status' => $request->stock_status,
                    'tax' => $request->tax
                ]);
            } else {

                $product_options = [];
                $product_varitions = [];

            
              if( !isset($request->parentattribute)  || !isset($request->childattribute['childrens']) ){
                $term->status = 0;
                $term->save();
              }

                foreach ( $request->parentattribute ?? [] as $option) {
                    $group = Productoption::firstOrNew(['term_id'=>$term->id,'category_id'=>$option]);
                      // $group = Productoption::where('id', $keychild)->first();
                      //  $group->select_type = $request->optionattribute[$option]['select_type'];
                      //  $group->is_required = $request->optionattribute[$option]['select_type'];
                      $group->select_type = 0;
                      $group->is_required = 1;
                      $group->save();
                       $product_options[$option] = $group->id;
                   }
                      if (isset($request->childattribute['childrens'])) {
                          foreach ($request->childattribute['childrens'] ?? [] as $key => $child_row) {
                              $data['term_id'] = $term->id;
                              $data['price'] = $child_row['price'] ?? 0;
                              $data['qty'] = $child_row['qty'] ?? 0;
                              $data['sku'] = $child_row['sku'] ?? 0;
                              $data['weight'] = $child_row['weight'] ?? 0;
                              $data['stock_manage'] = $child_row['stock_manage'] ?? 0;
                              $data['stock_status'] = $child_row['stock_status'] ?? 0;
                              $data['tax'] = $request->tax ?? 1;
                              $varition = Price::create($data);
                              $varitions_data = [];
                              foreach($child_row['varition'] ?? [] as $key=>$opt){
                                  $varitions_data[] = ['productoption_id'=>$product_options[$key],'category_id'=>$opt] ;
                              }
                              $varition->varitions()->sync($varitions_data);
                              
                              array_push($product_varitions, $varition->id);
                          }
                  }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            $errors['errors']['error'] = 'Oops something wrong';
            return response()->json($errors, 401);
        }
        //return response()->json(['Product Created']);
        return response()->json(['msg'=>'Product Created','redirect_to'=>route('seller.product.index')]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(!getpermission('products'), 401);
        return view("seller.product.show");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $type = "general")
    {
        abort_if(!getpermission('products'), 401);
        $formApiData = $this->formApi();
        if ($type == 'general') {
            $info = Term::query()->where('type', 'product')->with('tags', 'excerpt', 'description', 'termcategories','formType','formFields', 'ticketInstructions',
            'ticketSaleStart','ticketSaleEnd')->findorFail($id);

            // dd($info);
            $selected_categories = [];
            $product_type = Category::query()->where('type', 'product_type')->select('id', 'name')->orderBy('id', 'ASC')->get();

            foreach ($info->termcategories as $key => $value) {
                array_push($selected_categories, $value->category_id);
            }
            $features = Category::query()->where('type', 'product_feature')->orderBy('menu_status', 'ASC')->get();

            return view("seller.product.edit", compact('info','product_type', 'selected_categories', 'features', 'id','formApiData'));
        }

        if ($type == 'price') {
            $info = Term::query()->where('type', 'product')->with('price','prices', 'productoptionwithcategories', 'termcategories')->findorFail($id);
        //    $attributes = Category::query()->where('type', 'parent_attribute')->with('categories')->latest()->get();
        $attributes = Category::query()
        ->where('type', 'parent_attribute')
        ->with(['categories' => function ($query) {
            $query->orderBy('position');
        }])
        ->latest()
        ->get();
        $product_type = Category::query()->where('type', 'product_type')->select('id', 'name')->orderBy('id', 'ASC')->get();

            $selected_categories = [];
            foreach ($info->termcategories as $key => $value) {
                array_push($selected_categories, $value->category_id);
            }
            $selected_product_type = '';
            foreach($product_type as $val){
                if(in_array($val->id, $selected_categories)){
                    $selected_product_type = $val->name;
                }
            }

            return view("seller.product.price", compact('info', 'id', 'attributes', 'selected_product_type'));
        }

        if ($type == 'image') {
            $info = Term::query()->where('type', 'product')->with('media', 'medias')->findorFail($id);
            $medias = json_decode($info->medias->value ?? '');

            return view("seller.product.image", compact('info', 'id', 'medias'));
        }

        if ($type == "seo") {
            $info = Term::query()->where('type', 'product')->with('seo')->findorFail($id);
            $seo = json_decode($info->seo->value ?? '');

            return view("seller.product.seo", compact('info', 'id', 'seo'));
        }

        if ($type == "discount") {
            $info = Term::query()->where('type', 'product')->with('discount')->findorFail($id);
            return view("seller.product.discount", compact('info', 'id'));
        }
        if ($type == "barcode") {
            abort_if(tenant('barcode') != 'on', 401);
            $info = Term::query()->where('type', 'product')->with('preview')->findorFail($id);
            return view("seller.product.product_based_barcode", compact('info', 'id'));
        }

        if ($type == "express-checkout") {
            $info = Term::query()->where('type', 'product')->with('price', 'productoptionwithcategories')->findorFail($id);
            return view("seller.product.express_checkout", compact('info', 'id'));
        }
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
        // dd($request->form_fields);
        abort_if(!getpermission('products'), 401);
        if ($request->type == 'general') {
            $validated = $request->validate([
                'name' => 'required|max:100',
                'slug' => 'required|max:100',
                // 'short_description' => 'max:500',
                // 'long_description' => 'max:10000',
            ]);


            DB::beginTransaction();
            try {
                $term = Term::where('type', 'product')->with('excerpt','description','termcategories','ticketInstructions','formType', 'formFields')->findOrFail($id);
                $term->title = $request->name;
                $term->slug = $request->slug;
                $term->status = $request->status;
                $term->featured = $request->featured;
                $term->list_type = $request->list_type;
                $term->save();

                if ($request->short_description) {
                    if (empty($term->excerpt)) {
                        $term->excerpt()->create(['key' => 'excerpt', 'value' => $request->short_description]);
                    } else {
                        $term->excerpt()->update(['value' => $request->short_description]);
                    }
                } else {
                    if (!empty($term->excerpt)) {
                        $term->excerpt()->delete();
                    }
                }

                if ($request->has('ticket_instructions')) {
                    if (empty($term->ticketInstructions)) {
                        $term->ticketInstructions()->create([
                            'key' => 'ticket_instructions',
                            'value' => $request->ticket_instructions
                        ]);
                    } else {
                        $term->ticketInstructions()->update([
                            'value' => $request->ticket_instructions
                        ]);
                    }
                }

                if ($request->form_type) {
                    if (empty($term->formType)) {
                        
                        $term->formType()->create(['key' => 'form_type', 'value' => $request->form_type]);
                    } else {
                       
                        $term->formType()->update(['value' => $request->form_type]);
                    }
                } else {
                    if (!empty($term->formType)) {
                        $term->formType()->delete();
                    }
                }


                if ($request->form_fields) {
                    if (empty($term->formFields)) {
                        
                        $term->formFields()->create(['key' => 'form_fields', 'value' => $request->form_fields]);
                    } else {
                       
                        $term->formFields()->update(['value' => $request->form_fields]);
                    }
                } else {
                    if (!empty($term->formFields)) {
                        $term->formFields()->delete();
                    }
                }

                

                if ($request->long_description) {
                    if (empty($term->description)) {
                        $term->description()->create(['key' => 'description', 'value' => $request->long_description]);
                    } else {
                        $term->description()->update(['value' => $request->long_description]);
                    }
                } else {
                    if (!empty($term->description)) {
                        $term->description()->delete();
                    }
                }

                $cats = [];
                foreach ($request->categories ?? [] as $r) {
                    if (!empty($r)) {
                        array_push($cats, $r);
                    }
                }

                !empty($term->categories) ? $term->categories()->sync($cats) : $term->categories()->attach($cats);
               
                if($term->is_variation){
                    $priceCount = Price::where('term_id',$term->id)->count();

                        if($priceCount == 0){
                            $term->status = 0;
                            $term->save();
                        }
                }

               
               
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                return $th;
                $errors['errors']['error'] = 'Oops something wrong';
                return response()->json($errors, 401);
            }

            return response()->json('Product Information Updated...!!');
        }

        if ($request->type == 'price') {

            DB::beginTransaction();
            try {

                if ($request->product_type != 1) {
                    $term = Term::where('type', 'product')->with('price')->findorFail($id);
                    $term->is_variation = $request->product_type;
                    $term->save();
                    //single price
                    $valid_price = preg_replace("/[^0-9.]/", "", $request->price);
                    if (empty($term->price)) {
                        $term->price()->create(['price' => $valid_price, 'qty' => $request->qty, 'sku' => $request->sku, 'weight' => 0, 'stock_manage' => $request->stock_manage, 'stock_status' => $request->stock_status,'tax' => $request->tax]);
                    } else {
                        $term->price()->update(['price' => $valid_price, 'qty' => $request->qty, 'sku' => $request->sku, 'weight' => 0, 'stock_manage' => $request->stock_manage, 'stock_status' => $request->stock_status,'tax' => $request->tax]);
                    }
                    //end single price
                } else {
                    $term = Term::where('type', 'product')->with('productoption', 'prices')->findorFail($id);
                    $term->is_variation = $request->product_type;
                    $term->save();

                    $updated_option_group = [];
                    $updated_child_row = [];

                    $product_options = [];
                    $product_varitions = [];
               //dd($request);


               if( !isset($request->parentattribute)  || !isset($request->childattribute) ){
                $term->status = 0;
                $term->save();
              }

                     foreach ($request->parentattribute ?? [] as $option) {
                      $group = Productoption::firstOrNew(['term_id'=>$term->id,'category_id'=>$option]);
                        // $group = Productoption::where('id', $keychild)->first();
                        //  $group->select_type = $request->optionattribute[$option]['select_type'];
                        //  $group->is_required = $request->optionattribute[$option]['select_type'];
                        $group->select_type = 0;
                        $group->is_required = 1;
                        $group->save();
                         $product_options[$option] = $group->id;
                     }



                     if (isset($request->childattribute['childrens'])) {
                            foreach ($request->childattribute['childrens'] ?? [] as $key => $child_row) {
                                $data['term_id'] = $term->id;
                                $data['price'] = $child_row['price'] ?? 0;
                                $data['qty'] = $child_row['qty'] ?? 0;
                                $data['sku'] = $child_row['sku'] ?? 0;
                                $data['weight'] = $child_row['weight'] ?? 0;
                                $data['stock_manage'] = $child_row['stock_manage'] ?? 0;
                                $data['stock_status'] = $child_row['stock_status'] ?? 0;
                                $data['tax'] = $request->tax ?? 1;
                                $varition = Price::create($data);
                                $varitions_data = [];
                                foreach($child_row['varition'] ?? [] as $key=>$opt){
                                    $varitions_data[] = ['productoption_id'=>$product_options[$key],'category_id'=>$opt] ;
                                }
                                $varition->varitions()->sync($varitions_data);
                                
                                array_push($product_varitions, $varition->id);
                            }
                    }


                    if (isset($request->childattribute['priceoption'])) {
                        foreach ($request->childattribute['priceoption'] ?? [] as $key => $child_row) {
                            $varition = Price::find($key);

                            $data['term_id'] = $term->id;
                            $data['price'] = $child_row['price'] ?? 0;
                            $data['qty'] = $child_row['qty'] ?? 0;
                            $data['sku'] = $child_row['sku'] ?? 0;
                            $data['weight'] = $child_row['weight'] ?? 0;
                            $data['stock_manage'] = $child_row['stock_manage'] ?? 0;
                            $data['stock_status'] = $child_row['stock_status'] ?? 0;
                            $data['tax'] = $request->tax ?? 1;
                            $varition->update($data);
                            $varitions_data = [];
                            foreach($child_row['varition'] ?? [] as $key=>$opt){
                                $varitions_data[] = ['productoption_id'=>$key,'category_id'=>$opt] ;
                            }
                            $varition->varitions()->sync($varitions_data);

                            array_push($product_varitions, $varition->id);
                            //array_push($productoptions, $data);
                        }
                    }


                    if (isset($request->childattribute['new_priceoption'])) {
                        foreach ($request->childattribute['new_priceoption'] ?? [] as $key => $child_row) {

                            $data['term_id'] = $term->id;
                            $data['price'] = $child_row['price'] ?? 0;
                            $data['qty'] = $child_row['qty'] ?? 0;
                            $data['sku'] = $child_row['sku'] ?? 0;
                            $data['weight'] = $child_row['weight'] ?? 0;
                            $data['stock_manage'] = $child_row['stock_manage'] ?? 0;
                            $data['stock_status'] = $child_row['stock_status'] ?? 0;
                            $data['tax'] = $request->tax ?? 1;
                            $varition = Price::create($data);
                            $varitions_data = [];
                            foreach($child_row['varition'] ?? [] as $key=>$opt){
                                $varitions_data[] = ['productoption_id'=>$product_options[$key],'category_id'=>$opt] ;
                            }
                            $varition->varitions()->sync($varitions_data);

                            array_push($product_varitions, $varition->id);
                            //array_push($productoptions, $data);
                        }
                    }





                    $deleteable_option = [];
                    $deleteable_prices = [];
                    foreach ($term->productoption ?? [] as $row) {
                        if (in_array($row->id, $product_options) == false) {
                            array_push($deleteable_option, $row->id);
                        }
                    }


                    foreach ($term->prices ?? [] as $row) {
                        if (in_array($row->id, $product_varitions) == false) {
                            array_push($deleteable_prices, $row->id);
                        }
                    }

                    if (count($deleteable_option) > 0) {
                        Productoption::whereIn('id', $deleteable_option)->delete();
                    }

                    if (count($deleteable_prices) > 0) {
                        Price::whereIn('id', $deleteable_prices)->delete();
                    }




                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $errors['errors']['error'] = 'Oops something wrong';
                //dd($th);
                // $errors['errors']['error'] = $th;
                return response()->json($errors, 401);
            }
            return response()->json('Product Price Updated...!!');
        }
        if ($request->type == 'images') {
            DB::beginTransaction();
            try {
                $term = Term::where('type', 'product')->with('media', 'medias')->findorFail($id);
                if ($request->preview) {
                    if (empty($term->media)) {
                        $term->media()->create(['key' => 'preview', 'value' => $request->preview]);
                    } else {
                        $term->media()->update(['value' => $request->preview]);
                    }
                } else {
                    if (!empty($term->media)) {
                        $term->media()->delete();
                    }
                }
                if ($request->multi_images) {
                    if (empty($term->medias)) {
                        $term->medias()->create(['key' => 'gallery', 'value' => json_encode($request->multi_images)]);
                    } else {
                        $term->medias()->update(['value' => json_encode($request->multi_images)]);
                    }
                } else {
                    if (!empty($term->description)) {
                        $term->medias()->delete();
                    }
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $errors['errors']['error'] = 'Oops something wrong';
                return response()->json($errors, 401);
            }
            return response()->json('Product Image Updated...!!');
        }


        if ($request->type == 'seo') {
            DB::beginTransaction();
            try {
                $term = Term::where('type', 'product')->with('seo')->findorFail($id);

                $data['preview'] = $request->preview;
                $data['title'] = $request->title;
                $data['tags'] = $request->tags;
                $data['description'] = $request->description;
                if (empty($term->seo)) {
                    $term->seo()->create(['key' => 'seo', 'value' => json_encode($data)]);
                } else {
                    $term->seo()->update(['value' => json_encode($data)]);
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $errors['errors']['error'] = 'Oops something wrong';
                return response()->json($errors, 401);
            }
            return response()->json('Product Seo Updated...!!');
        }

        if ($request->type == 'discount') {
            //  dd($request->all());

            DB::beginTransaction();
            try {
                $term = Term::where('type', 'product')->with('discount', 'prices')->findorFail($id);
                if (empty($term->discount)) {
                    $term->discount()->create(['special_price' => $request->special_price, 'price_type' => $request->price_type, 'ending_date' => $request->ending_date]);
                } else {
                    $term->discount()->update(['special_price' => $request->special_price, 'price_type' => $request->price_type, 'ending_date' => $request->ending_date]);
                }
                foreach ($term->prices as $key => $row) {
                    $price = Price::find($row->id);
                    $current_price = !empty($price->old_price) ? $price->old_price : $price->price;

                    if ($request->price_type == 1) {
                        $percentage = $current_price * $request->special_price / 100;
                        $new_price = $current_price - $percentage;
                    } else {
                        $new_price = $current_price - $request->special_price;
                    }
                    $price->price = $new_price;
                    $price->old_price = $current_price;
                    $price->save();
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $errors['errors']['error'] = 'Oops something wrong';
                return response()->json($errors, 401);
            }
            return response()->json('Product Discount Applied...!!');
        }

        if ($request->type == 'barcode') {
            $term = Term::where('type', 'product')->with('discount', 'prices')->findorFail($id);
            if ($request->barcode_type == 'QRCODE' || $request->barcode_type == 'PDF417') {
                $barcode = DNS2D::getBarcodePNG($term->full_id, $request->barcode_type);
            } else {
                $barcode = DNS1D::getBarcodePNG($term->full_id, $request->barcode_type);
            }


            return response()->json(['barcode' => $barcode]);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function multiDelete(Request $request)
    {
        abort_if(!getpermission('products'), 401);
        if ($request->ids) {
            if ($request->method == 'delete') {
                Term::query()->where('type', 'product')->where('id', $request->ids)->delete();

                return response()->json('Successfully Product Deleted...!!!');
            }elseif ($request->method == 'duplicate') {
                foreach ($request->ids as $id) {
                    $this->duplicateProduct($id);
                }

                return response()->json('Successfully Product duplicated...!!!');
            } else {
                foreach ($request->ids as $id) {

                    $product = Term::where('type', 'product')->find($id);
                    if (!empty($product)) {
                        $product->status = $request->method;
                        $product->save();
                    }
                }
                return response()->json('Successfully Product Deleted...!!!');
            }
        }

        return response()->json('Select Some product...!!!');
    }

    public function import(Request $request)
    {
        abort_if(!getpermission('products'), 401);
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlx,xls|max:2048'
        ]);

        Excel::import(new ProductImport,  $request->file('file'));

        return response()->json(['Product Imported Successfully']);
    }

    public function removeVariationPrice($id){
        $varPrice =  Price::where('id', $id)->first();

        $delete_res = Price::where('id', $id)->delete();
        
        $priceCount = Price::where('term_id',$varPrice->term_id)->count();

        if($priceCount == 0){
            $term = Term::find($varPrice->term_id);
            $term->status = 0;
            $term->save();
        }

        if($delete_res){
            return response()->json(['status' => 'success']);
        }
    }

    public function removeVariationAttribute($id){
        // Assuming $id is the ID you want to delete
        $productOption = Productoption::find($id);

        if ($productOption) {
            $priceIds = VariationProductOption::where('productoption_id', $id)
                ->distinct()
                ->pluck('price_id');

            // Delete records in both tables in a single query
            Price::whereIn('id', $priceIds)->delete();

            $priceCount = Price::where('term_id',$productOption->term_id)->count();

            if($priceCount == 0){
                $term = Term::find($varPrice->term_id);
                $term->status = 0;
                $term->save();
            }


            $productOption->delete();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'Some thing went wrong']);
    }

    public function clone($id){
      $new = $this->duplicateProduct($id);
      if($new != 0){
        return redirect()->route('seller.product.edit', $new);
      }else{
        return redirect()->back()->with('error','Oops somthing went wrong');
      }
    }

    private function duplicateProduct($id){

        $product = Term::where('type', 'product')->with('excerpt','price','media', 'medias','seo', 'description', 'termcategories','productoption','prices')->find($id);

      // dd($product->productoption);
        DB::beginTransaction();
        try {
            $term = new Term;
            $term->title = $product->title.' Copy ';
            $term->slug = $term->makeSlug($product->title.' Copy ', 'product');
            $term->type = 'product';
            $term->status = 0;
            $term->is_variation = $product->is_variation;
            $term->list_type = $product->list_type;
            $term->save();


            if (!empty($product->excerpt)) {
                $term->excerpt()->create(['key' => 'excerpt', 'value' => $product->excerpt->value]);
            }


            if (!empty($product->description)) {
                $term->description()->create(['key' => 'description', 'value' => $product->description]);
            }

            if(!empty($product->categories)){
                $term->categories()->attach($product->categories->pluck('id')->toArray());
            } 

            if (!empty($product->media)) {
                $term->media()->create(['key' => 'preview', 'value' => $product->media->value]);
            }

            if (!empty($product->medias)) {
                $term->medias()->create(['key' => 'gallery', 'value' => $product->medias->value]);
            }

            if ($product->is_variation != 1) {

                $term->price()->create([
                    'price' => $product->price->price,
                    'qty' => $product->price->qty,
                    'sku' => '',
                    'weight' => $product->price->weight,
                    'stock_manage' => 0,
                    'stock_status' => $product->price->stock_status,
                    'tax' => $product->price->tax
                ]);
            } else {

                $product_options = [];
                $product_varitions = [];

                foreach($product->productoption as $productoption){
                    
                    $option = Productoption::firstOrNew(['term_id'=>$term->id,'category_id'=>$productoption->category_id,'select_type'=>0,'is_required'=>1]);
                    $option->save();
                    $product_options[] = ['old'=>$productoption->id,'new'=>$option->id];
                }

                foreach($product->prices as $price){
                    $data = [];
                    $data['term_id'] = $term->id;
                    $data['price'] = $price->price;
                    $data['qty'] = $price->qty;
                    $data['sku'] = '';
                    $data['weight'] = $price->weight;
                    $data['stock_manage'] =  0;
                    $data['stock_status'] =  0;
                    $data['tax'] = $price->tax;
                    $varition = Price::create($data);
                    $varitions_data = [];
                    foreach($price->varitions as $old_varition){

                        $filteredvarition = array_filter($product_options, function($option) use($old_varition){
                            return $option['old'] == $old_varition->pivot->productoption_id;
                        });

                         $filteredvarition = reset($filteredvarition);
                         if(isset($filteredvarition['new'])){
                            $popt = $filteredvarition['new'];
                            $varitions_data[] = ['productoption_id'=>$popt,'category_id'=>$old_varition->pivot->category_id];                     
                         }
                    }
                    $varition->varitions()->sync($varitions_data);
                }

            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
        }
        
       return $term->id; 

    }

    public function updateproductOrder(Request $request){
        
        $data = $request->get('data');

        foreach($data as $key=>$val){
            Term::find($val)->update(['order'=>$key]);
        }

        return response()->json(["status" => true, "message" => "product list successfully", "result" => $data]);
    }
    
    
    // product history add
    
    public function salesHistory(Request $request, $id)
    {
        $product = Term::with(['price', 'media'])->findOrFail($id);
        if ($request->ajax() && (string) $request->query('crm_sync_groups', '') === '1') {
            $result = $this->syncCrmContactGroupsData($product->title);
            return response()->json($result, $result['success'] ? 200 : 500);
        }

        // Refresh contact groups from main app when the user lands on this page.
        $this->syncCrmContactGroupsData($product->title);

        $crmSyncStatus = $this->resolveProductCrmSyncStatus($id);
        $crmContactGroupOptions = $this->getCrmContactGroupOptions($product->title);

        $isTicketProduct = (int) $product->is_variation === 2;

        if ($isTicketProduct) {
            $sales = EventTicket::with(['order.ordermeta', 'orderItem'])
                ->where('term_id', $id);

            if ($request->filled('src')) {
                $search = $request->src;

                $sales->where(function ($q) use ($search) {
                    $q->where('attendee_name', 'LIKE', "%{$search}%")
                        ->orWhere('attendee_email', 'LIKE', "%{$search}%")
                        ->orWhere('attendee_phone', 'LIKE', "%{$search}%")
                        ->orWhere('ticket_uuid', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%")
                        ->orWhereHas('order', function ($orderQuery) use ($search) {
                            $orderQuery->where('invoice_no', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('order.ordermeta', function ($metaQuery) use ($search) {
                            $metaQuery->where('value', 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('orderItem', function ($itemQuery) use ($search) {
                            $itemQuery->where('info', 'LIKE', "%{$search}%");
                        });
                });
            }

            $sales = $sales->orderBy('id', 'desc')->paginate(20);

            return view('seller.product.sales-history', compact('product', 'sales', 'crmSyncStatus', 'crmContactGroupOptions'));
        }

        $sales = Orderitem::with(['order.ordermeta', 'eventTicket'])
            ->where('term_id', $id);
        
            if ($request->filled('src')) {
            
                $search = $request->src;
            
                $sales = $sales->where(function ($q) use ($search) {
            
                    $q->where('info', 'LIKE', "%{$search}%")
            
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('invoice_no', 'LIKE', "%{$search}%");
                    })
            
                    ->orWhereHas('order.ordermeta', function ($metaQuery) use ($search) {
                        $metaQuery->where('value', 'LIKE', "%{$search}%");
                    })
            
                    ->orWhereHas('eventTicket', function ($ticketQuery) use ($search) {
                        $ticketQuery->where('attendee_name', 'LIKE', "%{$search}%")
                            ->orWhere('attendee_email', 'LIKE', "%{$search}%")
                            ->orWhere('attendee_phone', 'LIKE', "%{$search}%")
                            ->orWhere('ticket_uuid', 'LIKE', "%{$search}%")
                            ->orWhere('status', 'LIKE', "%{$search}%");
                    });
            
                });
            }
        
        $sales = $sales->orderBy('id', 'desc')->paginate(20);
    
        return view('seller.product.sales-history', compact('product', 'sales', 'crmSyncStatus', 'crmContactGroupOptions'));
    }

    protected function getCrmContactGroupOptions(string $productTitle): array
    {
        $clubId = (int) tenant('club_id');
        $options = [];

        try {
            $groups = DB::table('contact_groups')
                ->where('club_id', $clubId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['name', 'group_id'])
                ->toArray();
        } catch (\Throwable $e) {
            $groups = [];
        }

        $existing = [];
        foreach ($groups as $group) {
            $name = trim((string) ($group->name ?? ''));
            $normalized = strtolower($name);
            if ($normalized === '' || isset($existing[$normalized])) {
                continue;
            }
            $options[] = [
                'name' => $name,
                'group_id' => isset($group->group_id) ? (string) $group->group_id : null,
            ];
            $existing[$normalized] = true;
        }

        return $options;
    }

    protected function syncCrmContactGroupsData(string $productTitle): array
    {
        $clubId = (int) tenant('club_id');
        $userId = Auth::id();
        $hasGroupIdColumn = \Illuminate\Support\Facades\Schema::hasColumn('contact_groups', 'group_id');

        $envWpUrl = trim((string) env('WP_CLUB_URL'));
        $candidateBaseUrls = [];

        if ($envWpUrl !== '') {
            $normalizedEnvBaseUrl = $envWpUrl;
            $wpJsonPos = stripos($normalizedEnvBaseUrl, '/wp-json/');
            if ($wpJsonPos !== false) {
                $normalizedEnvBaseUrl = substr($normalizedEnvBaseUrl, 0, $wpJsonPos);
            }
            $candidateBaseUrls[] = rtrim($normalizedEnvBaseUrl, '/');
        }

        $candidateBaseUrls[] = 'https://app.booostr.co';
        $candidateBaseUrls = array_values(array_unique(array_filter($candidateBaseUrls)));

        try {
            $payload = null;
            foreach ($candidateBaseUrls as $baseUrl) {
                $groupsApiUrl = $baseUrl . '/wp-json/store-api/v1/groups/?club_id=' . $clubId . '&_=' . time();
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->timeout(20)
                    ->acceptJson()
                    ->get($groupsApiUrl);

                if (!$response->successful()) {
                    continue;
                }

                $decoded = json_decode((string) $response->body(), true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                    break;
                }
            }

            if (!is_array($payload)) {
                return [
                    'success' => false,
                    'message' => 'Unable to fetch contact groups from remote API.',
                ];
            }

            $rows = $payload['data'] ?? [];
            if (is_string($rows)) {
                $rows = json_decode($rows, true);
            }
            if (!is_array($rows)) {
                $rows = [];
            }

            DB::transaction(function () use ($rows, $clubId, $userId, $hasGroupIdColumn) {
                $keptLocalIds = [];

                foreach ($rows as $row) {
                    $remoteGroupId = trim((string) ($row['id'] ?? ''));
                    $groupName = trim((string) ($row['name'] ?? ''));

                    if ($remoteGroupId === '' || $groupName === '') {
                        continue;
                    }

                    $existingByName = DB::table('contact_groups')
                        ->where('club_id', $clubId)
                        ->whereRaw('LOWER(name) = ?', [strtolower($groupName)])
                        ->first();

                    $existingByGroupId = null;
                    if ($hasGroupIdColumn) {
                        $existingByGroupId = DB::table('contact_groups')
                            ->where('club_id', $clubId)
                            ->where('group_id', $remoteGroupId)
                            ->first();
                    }

                    $target = $existingByGroupId ?: $existingByName;
                    $updateData = [
                        'name' => $groupName,
                        'is_active' => true,
                        'created_by' => $userId,
                        'updated_at' => now(),
                    ];
                    if ($hasGroupIdColumn) {
                        $updateData['group_id'] = $remoteGroupId;
                    }

                    if ($target) {
                        DB::table('contact_groups')
                            ->where('id', $target->id)
                            ->update($updateData);
                        $keptLocalIds[] = (int) $target->id;
                        continue;
                    }

                    $insertData = [
                        'club_id' => $clubId,
                        'name' => $groupName,
                        'is_active' => true,
                        'created_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if ($hasGroupIdColumn) {
                        $insertData['group_id'] = $remoteGroupId;
                    }

                    $keptLocalIds[] = (int) DB::table('contact_groups')->insertGetId($insertData);
                }

                $staleGroupsQuery = DB::table('contact_groups')->where('club_id', $clubId);
                if (!empty($keptLocalIds)) {
                    $staleGroupsQuery->whereNotIn('id', $keptLocalIds);
                }

                $staleGroupsQuery->delete();
            });

            return [
                'success' => true,
                'groups' => $this->getCrmContactGroupOptions($productTitle),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to sync contact groups right now.',
            ];
        }
    }

    public function crmSyncContactGroupsSync(Request $request, $id)
    {
        $product = Term::findOrFail($id);
        $result = $this->syncCrmContactGroupsData($product->title);
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    protected function parseCrmSyncedContactsPayload($payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($payload)) {
            return [];
        }

        $contacts = [];

        foreach ($payload as $contact) {
            if (!is_array($contact)) {
                continue;
            }

            $sourceType = trim((string) ($contact['source_type'] ?? ''));
            $sourceId = (int) ($contact['source_id'] ?? 0);

            if ($sourceType === '' || $sourceId <= 0) {
                continue;
            }

            $contacts[] = [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'email' => $contact['email'] ?? null,
            ];
        }

        return $contacts;
    }

    protected function validateCrmSyncScopeChange(Request $request, int $productId)
    {
        $syncService = app(ProductSalesCrmSyncService::class);
        $previousSync = $syncService->getEffectiveSyncConfigForProduct($productId);
        $newSyncMode = $request->input('sync_mode') === 'page' ? 'current_page' : 'all_results';

        if ($syncService->scopeChangeRequiresNewContactGroup(
            $previousSync,
            $newSyncMode,
            $request->input('crm_list_name')
        )) {
            return response()->json([
                'success' => false,
                'message' => 'To change from All Results to Only This Page Of Results, please create a new contact group.',
            ], 422);
        }

        return null;
    }

    protected function resolveProductCrmSyncStatus(int $productId): array
    {
        $syncService = app(ProductSalesCrmSyncService::class);
        $sync = $syncService->getActiveContinuousSyncForProduct($productId);

        if ($sync && $sync->sync_status === 'syncing') {
            $syncService->runInitialSync($sync);
            $sync = $syncService->getActiveContinuousSyncForProduct($productId);
        }

        return $syncService->formatStatusPayload($sync, $productId);
    }

    public function crmSyncStatus($id)
    {
        Term::findOrFail($id);

        $syncService = app(ProductSalesCrmSyncService::class);
        $sync = $syncService->getActiveContinuousSyncForProduct($id);

        return response()->json(
            $syncService->formatStatusPayload($sync, (int) $id)
        );
    }

    public function crmSyncRecordOneTime(Request $request, $id)
    {
        $product = Term::findOrFail($id);

        $syncService = app(ProductSalesCrmSyncService::class);
        $scopeValidationError = $this->validateCrmSyncScopeChange($request, (int) $id);
        if ($scopeValidationError) {
            return $scopeValidationError;
        }

        $activeContinuous = $syncService->getActiveContinuousSyncForProduct((int) $id);
        $convertingFromContinuous = (bool) $request->boolean('convert_from_continuous');
        $shouldConvertFromContinuous = $convertingFromContinuous || (bool) $activeContinuous;

        // Continuous -> one-time: run a final incremental check and stop continuous mode.
        if ($activeContinuous && $shouldConvertFromContinuous) {
            $syncService->syncContinuousForProduct((int) $id);
            $stopped = $syncService->stopContinuousSync((int) $id);

            // Fallback safety: ensure existing active config is not left running.
            if (!$stopped) {
                $activeContinuous->update([
                    'continuous_sync_enabled' => false,
                    'sync_status' => 'stopped',
                ]);
            }
        }

        $sync = $syncService->recordOneTimeSync($product, [
            'sync_mode' => $request->input('sync_mode') === 'page' ? 'current_page' : 'all_results',
            'contact_tags' => $request->input('contact_tags', ''),
            'crm_list_name' => $request->input('crm_list_name'),
            'crm_group_id' => $request->input('crm_group_id'),
            'total_synced_contacts' => (int) $request->input('total_synced_contacts', 0),
            'synced_contacts' => $this->parseCrmSyncedContactsPayload($request->input('synced_contacts')),
            'filter_state' => [
                'src' => $request->input('src'),
                'page' => (int) $request->input('page', 1),
                'per_page' => (int) $request->input('per_page', 20),
            ],
        ], Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'One-time sync recorded.',
            'status' => $syncService->formatStatusPayload($sync, (int) $id),
        ]);
    }

    public function crmSyncCreateContactGroup(Request $request, $id)
    {
        Term::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($validated['name']);
        if ($name === '') {
            return response()->json([
                'success' => false,
                'message' => 'Contact group name is required.',
            ], 422);
        }

        $clubId = (int) tenant('club_id');
        $userId = Auth::id();
        $remoteResult = app(ProductSalesCrmSyncService::class)->createContactGroupInCrm($name, $clubId);

        if (empty($remoteResult['success'])) {
            return response()->json([
                'success' => false,
                'message' => $remoteResult['message'] ?? 'Unable to create contact group in CRM.',
            ], 422);
        }

        $remoteGroupId = (string) $remoteResult['group_id'];

        try {
            $existing = DB::table('contact_groups')
                ->where('club_id', $clubId)
                ->where(function ($query) use ($name, $remoteGroupId) {
                    $query->whereRaw('LOWER(name) = ?', [strtolower($name)])
                        ->orWhere('group_id', $remoteGroupId);
                })
                ->first();

            if ($existing) {
                DB::table('contact_groups')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $name,
                        'group_id' => $remoteGroupId,
                        'is_active' => true,
                        'updated_at' => now(),
                    ]);

                $localId = (int) $existing->id;
            } else {
                $localId = (int) DB::table('contact_groups')->insertGetId([
                    'club_id' => $clubId,
                    'group_id' => $remoteGroupId,
                    'name' => $name,
                    'is_active' => true,
                    'created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $remoteResult['message'] ?? 'Contact group created.',
                'contact_group' => [
                    'id' => $localId,
                    'name' => $name,
                    'group_id' => $remoteGroupId,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact group was created in CRM but could not be saved locally.',
            ], 500);
        }
    }

    public function crmSyncEnableContinuous(Request $request, $id)
    {
        $product = Term::findOrFail($id);

        $scopeValidationError = $this->validateCrmSyncScopeChange($request, (int) $id);
        if ($scopeValidationError) {
            return $scopeValidationError;
        }

        $syncMode = $request->input('sync_mode') === 'page' ? 'current_page' : 'all_results';

        $sync = app(ProductSalesCrmSyncService::class)->enableContinuousSync($product, [
            'sync_mode' => $syncMode,
            'contact_tags' => $request->input('contact_tags', ''),
            'crm_list_name' => $request->input('crm_list_name'),
            'crm_group_id' => $request->input('crm_group_id'),
            'filter_state' => [
                'src' => $request->input('src'),
                'page' => (int) $request->input('page', 1),
                'per_page' => (int) $request->input('per_page', 20),
            ],
        ], Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Continuous sync enabled.',
            'status' => app(ProductSalesCrmSyncService::class)->formatStatusPayload($sync, (int) $id),
        ]);
    }

    public function crmSyncProcessBatch($id)
    {
        Term::findOrFail($id);

        $sync = app(ProductSalesCrmSyncService::class)->getActiveContinuousSyncForProduct($id);

        if (!$sync) {
            return response()->json([
                'success' => false,
                'message' => 'No active continuous sync found for this product.',
            ], 404);
        }

        $result = app(ProductSalesCrmSyncService::class)->processBatch($sync, true);

        $sync->refresh();

        return response()->json([
            'success' => true,
            'status' => app(ProductSalesCrmSyncService::class)->formatStatusPayload($sync, (int) $id),
            'batch' => $result,
        ]);
    }

    public function crmSyncStop($id)
    {
        Term::findOrFail($id);

        $sync = app(ProductSalesCrmSyncService::class)->stopContinuousSync($id);

        if (!$sync) {
            return response()->json([
                'success' => false,
                'message' => 'No active continuous sync found for this product.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Continuous sync stopped.',
            'status' => app(ProductSalesCrmSyncService::class)->formatStatusPayload(null, (int) $id),
        ]);
    }

    public function crmSyncPause($id)
    {
        Term::findOrFail($id);

        $syncService = app(ProductSalesCrmSyncService::class);
        $sync = $syncService->pauseContinuousSync($id);

        if (!$sync) {
            return response()->json([
                'success' => false,
                'message' => 'No active continuous sync found for this product.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Continuous sync paused.',
            'status' => $syncService->formatStatusPayload(null, (int) $id),
        ]);
    }

    public function crmSyncRestart($id)
    {
        Term::findOrFail($id);

        $syncService = app(ProductSalesCrmSyncService::class);
        $sync = $syncService->restartContinuousSync($id);

        if (!$sync) {
            return response()->json([
                'success' => false,
                'message' => 'No paused continuous sync found for this product.',
            ], 404);
        }

        $syncService->syncContinuousForProduct((int) $id);
        $sync->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Continuous sync restarted.',
            'status' => $syncService->formatStatusPayload($sync, (int) $id),
        ]);
    }
    
public function ticketStatusUpdate(Request $request)
{
    $ticket = EventTicket::findOrFail($request->ticket_id);

    if ($request->status === 'cancelled') {
        try {
            app(TicketCancelRefundService::class)->handle($ticket);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    $ticket->status = $request->status;
    $ticket->used_at = $request->status == 'used' ? now() : null;
    $ticket->save();

    return response()->json(['success' => true]);
}
}
