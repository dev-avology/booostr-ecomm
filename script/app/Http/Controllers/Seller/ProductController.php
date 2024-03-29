<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Term;
use App\Models\Price;
use App\Models\Productoption;
use App\Models\Variationproductoption;
use DB;
use DNS1D;
use DNS2D;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use Auth;
use Error;

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
        $posts = Term::query()->where('type', 'product')->with('media', 'price')->withCount('orders');
        if (!empty($request->src) && !empty($request->type)) {
            $posts = $posts->where($request->type, 'LIKE', '%' . $request->src . '%');
        }
        $posts = $posts->latest()->paginate(20);

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
        $attributes = Category::query()->where('type', 'parent_attribute')->with('categories')->latest()->get();
        $features = Category::query()->where('type', 'product_feature')->orderBy('menu_status', 'ASC')->get();
        $product_type = Category::query()->where('type', 'product_type')->orderBy('id', 'ASC')->get();

        return view("seller.product.create", compact('attributes', 'features', 'product_type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'short_description' => 'max:500',
        ]);

        if ($request->product_type != 1) {
            $validated = $request->validate([
                'price' => 'required|max:100',
            ]);
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

            if ($request->short_description) {
                $term->meta()->create(['key' => 'excerpt', 'value' => $request->short_description]);
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
                    'weight' => $request->weight,
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
        if ($type == 'general') {
            $info = Term::query()->where('type', 'product')->with('tags', 'excerpt', 'description', 'termcategories')->findorFail($id);
            $selected_categories = [];
            $product_type = Category::query()->where('type', 'product_type')->select('id', 'name')->orderBy('id', 'ASC')->get();

            foreach ($info->termcategories as $key => $value) {

                array_push($selected_categories, $value->category_id);
            }
            $features = Category::query()->where('type', 'product_feature')->orderBy('menu_status', 'ASC')->get();

            return view("seller.product.edit", compact('info','product_type', 'selected_categories', 'features', 'id'));
        }

        if ($type == 'price') {
            $info = Term::query()->where('type', 'product')->with('price','prices', 'productoptionwithcategories', 'termcategories')->findorFail($id);
            $attributes = Category::query()->where('type', 'parent_attribute')->with('categories')->latest()->get();
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
        abort_if(!getpermission('products'), 401);
        if ($request->type == 'general') {
            $validated = $request->validate([
                'name' => 'required|max:100',
                'slug' => 'required|max:100',
                'short_description' => 'max:500',
                'long_description' => 'max:10000',
            ]);


            DB::beginTransaction();
            try {
                $term = Term::where('type', 'product')->with('excerpt', 'description', 'termcategories')->findorFail($id);
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
                    $priceCount = Price::where('term_id',$term->term_id)->count();

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
                        $term->price()->create(['price' => $valid_price, 'qty' => $request->qty, 'sku' => $request->sku, 'weight' => $request->weight, 'stock_manage' => $request->stock_manage, 'stock_status' => $request->stock_status,'tax' => $request->tax]);
                    } else {
                        $term->price()->update(['price' => $valid_price, 'qty' => $request->qty, 'sku' => $request->sku, 'weight' => $request->weight, 'stock_manage' => $request->stock_manage, 'stock_status' => $request->stock_status,'tax' => $request->tax]);
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


               if( !isset($request->parentattribute)  || !isset($request->childattribute['childrens']) ){
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
                    if (!empty($term->description)) {
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


}
