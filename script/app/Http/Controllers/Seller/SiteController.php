<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\Term;
use App\Models\Category;
use Auth;
class SiteController extends Controller
{
    public function index()
    {
      abort_if(!getpermission('website_settings'),401);
      return view(baseview('admin/options'));
    }

    public function getBannerType($type){
      if($type == 'product'){
         $data['term'] = Term::where('type','product')->get();
         $data['type'] = "product";
         return response()->json($data);
      }else if($type == 'category'){
         $data['term'] = Category::where('type','category')->get();
         $data['type'] = "category";
        
         return response()->json($data);
      }else{
         // return 'ahish';
         $data['term'] = Category::where('type','category')->get();;
         $data['type'] = "custom";
        
         return response()->json($data);
      }
  }

    public function updatethemesettings(Request $request,$page_name)
    {
       abort_if(!getpermission('website_settings'),401);
      $theme=tenant('theme') != null ? tenant('theme') : 'theme.resto';
      $theme=str_replace('.','/',$theme);

      if ($page_name != 'site_settings' && $page_name != 'mailchimp') {
         $functions= resource_path('views/'.$theme.'/options.php');
         include($functions);
         abort_if(!in_array($page_name,$pages),404);
      }
       

       $option=Option::where('key',$page_name)->first();

       if (empty($option)) {
          $option=new Option;
          $option->key=$page_name;
          $data['meta']=$request->option;
          $option->value=json_encode($data);
       }
       else{
          $data=json_decode($option->value ?? '');
          $data->meta=$request->option;
          $option->value=json_encode($data);
       }

        
        $option->save();

       TenantCacheClear($page_name);

       return response()->json('Theme Option Updated');

    }
}
