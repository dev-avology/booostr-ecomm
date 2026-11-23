<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;
use App\Models\Category;
use App\Models\Location;
use App\Models\Getway;
use Cart;
use Session;
use Auth;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Mail;
use App\Mail\ContactMail;


class PageController extends Controller
{
    public function home()
    {
        store_set_seo('Home');

        $store_banner_image = optionfromcache('banner_logo') ?: (get_option('banner_logo') ?: '');
        $store_banner_url = optionfromcache('banner_url') ?: (get_option('banner_url') ?: '');

        return view(baseview('index'), compact('store_banner_image', 'store_banner_url'));
    }

    public function category_product(Request $request)
    {
        $category=Category::with(['products' => function($q) {
            $q->webProducts();
        }])->where('slug',$request->slug)->where('status',1)->first();
        return response()->json($category);
    }

    public function products(Request $request)
    {
        $home_data=optionfromcache('products_page');
        store_set_seo('Products');

        $page_data=$home_data->meta ?? '';

        $page_title=$page_data->products_page_title ?? 'Products';
        $products_page_description=$page_data->products_page_description ?? '';
        $products_page_banner=$page_data->products_page_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->products_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->products_page_product_ads_link ?? '';

        return view(baseview('products'),compact('page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link','request'));
    }

    public function deals(Request $request)
    {
        $home_data=optionfromcache('deal_page');
        store_set_seo('Deals');

        $page_data=$home_data->meta ?? '';

        $page_title=$page_data->deal_page_title ?? 'Products';
        $products_page_description=$page_data->deal_page_description ?? '';
        $products_page_banner=$page_data->products_deal_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->deal_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->deal_page_product_ads_link ?? '';

        return view(baseview('deals'),compact('page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link','request'));
    }

    public function brand($slug)
    {
        $category=Category::where('type','brand')->with('preview','description')->where('slug',$slug)->where('status',1)->first();
        abort_if(empty($category),404);
        $categoryid=$category->id;

        $home_data=optionfromcache('products_page');
        store_set_seo($category->name ?? 'Brand', $category->description->content ?? null, !empty($category->preview->content) ? asset($category->preview->content) : null);

        $page_data=$home_data->meta ?? '';

        $page_title=$category->name ?? 'Products';
        $products_page_description=$category->description->content ?? '';
        $products_page_banner=$page_data->products_page_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->products_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->products_page_product_ads_link ?? '';

        return view(baseview('products'),compact('categoryid','page_data','page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link'));
    }

     public function featured($slug)
     {
        $category=Category::where('type','product_feature')->with('preview','description')->where('slug',$slug)->first();
        abort_if(empty($category),404);
        $categoryid=$category->id;

        $home_data=optionfromcache('products_page');
        store_set_seo($category->name ?? 'Featured', $category->description->content ?? null, !empty($category->preview->content) ? asset($category->preview->content) : null);

        $page_data=$home_data->meta ?? '';

        $page_title=$category->name ?? 'Products';
        $products_page_description=$category->description->content ?? '';
        $products_page_banner=$page_data->products_page_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->products_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->products_page_product_ads_link ?? '';

        return view(baseview('products'),compact('categoryid','page_data','page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link'));
    }

    public function tag($slug)
     {
        $category=Category::where('type','tag')->where('slug',$slug)->first();
        abort_if(empty($category),404);
        $categoryid=$category->id;

        $home_data=optionfromcache('products_page');
        store_set_seo($category->name ?? 'Tag');

        $page_data=$home_data->meta ?? '';

        $page_title=$category->name ?? 'Products';
        $products_page_description=$category->description->content ?? '';
        $products_page_banner=$page_data->products_page_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->products_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->products_page_product_ads_link ?? '';

        return view(baseview('products'),compact('categoryid','page_data','page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link'));
    }

    public function category($slug)
    {
        $category=Category::where('type','category')->with('preview','description')->where('slug',$slug)->where('status',1)->first();
        abort_if(empty($category),404);
        $categoryid=$category->id;
        $home_data=optionfromcache('products_page');
        store_set_seo($category->name ?? 'Category', $category->description->content ?? null, !empty($category->preview->content) ? asset($category->preview->content) : null);

        $page_data=$home_data->meta ?? '';

        $page_title=$category->name ?? 'Products';
        $products_page_description=$category->description->content ?? '';
        $products_page_banner=$page_data->products_page_banner ?? '';
        if (empty($products_page_banner)) {
            $products_page_banner = get_option('banner_logo') ?: '';
        }
        $products_page_product_ads_banner=$page_data->products_page_product_ads_banner ?? '';
        $products_page_product_ads_link=$page_data->products_page_product_ads_link ?? '';

        return view(baseview('products'),compact('categoryid','page_data','page_title','products_page_description','products_page_banner','products_page_product_ads_banner','products_page_product_ads_link'));
    }

    public function productView($slug)
    {
        $info=Term::query()->where('type','product')->where('status',1)->webProducts()->with('tags','category','brands','excerpt','description','preview','medias','optionwithcategories','price','prices','seo')->withCount('reviews')->where('slug',$slug)->first();
        abort_if(empty($info),404);
        $seo=json_decode($info->seo->value ?? '');

        $medias=json_decode($info->medias->value ?? '');

        

        $preview=asset($info->preview->value ?? 'uploads/default.png');
        $galleries=[];
        
        array_push($galleries,$preview);

        foreach($medias ?? [] as $row){
            array_push($galleries,$row);
        }

        $productDesc = !empty($seo->description) ? $seo->description : (!empty($info->excerpt->value) ? $info->excerpt->value : null);
        $previewImg = !empty($seo->preview) ? $seo->preview : $preview;
        store_set_seo($seo->title ?? $info->title, $productDesc, $previewImg, 'article');
        if (!empty($info->updated_at)) {
            SEOMeta::addMeta('article:published_time', $info->updated_at->toW3CString(), 'property');
        }
        foreach($galleries ?? [] as $row){
            OpenGraph::addImage($row);
            JsonLd::addImage($row);
        }

        $home_data=optionfromcache('product_page');

        $home_data=$home_data->meta ?? '';
        $product_page_banner = $home_data->product_page_banner ?? '';
        if (empty($product_page_banner)) {
            $product_page_banner = get_option('banner_logo') ?: '';
        }
       
        return view(baseview('details'),compact('info','galleries','home_data','product_page_banner'));
    }

    public function page($slug)
    {
        $info=Term::query()->where('type','page')->where('status',1)->where('slug',$slug)->with('meta')->first();
        abort_if(empty($info),404);
        $meta=json_decode($info->meta->value ?? '');

        store_set_seo($info->title ?? 'Page', $meta->page_excerpt ?? null);
        
        //return view(baseview('page'),compact('info','meta'));
        return view('store.checkout.page',compact('info','meta'));
    }

    public function menu()
    {
        $home_data=optionfromcache('menu_page');
        store_set_seo('Products');

        $page_data=$home_data->meta ?? '';
        return view(baseview('menu'),compact('page_data'));
    }

    public function cart()
    {
        $tax=optionfromcache('tax');
        if ($tax == null) {
            $tax=0;
        }
        Cart::setGlobalTax($tax);

        $home_data=optionfromcache('cart_page');
        store_set_seo('Cart');

        $page_data=$home_data->meta ?? '';

        return view(baseview('cart'),compact('page_data'));
    }

    public function wishlist()
    {

        $tax=optionfromcache('tax');
        if ($tax == null) {
            $tax=0;
        }
        Cart::setGlobalTax($tax);
        $contents=Cart::instance('wishlist')->content();

        $productIds = $contents->pluck('id')->toArray();
        $products = Term::query()->where('type','product')->where('status',1)->webProducts()->with('firstprice', 'preview')->whereIn('id', $productIds)->get()->keyBy('id');

        $home_data=optionfromcache('wishlist_page');
        store_set_seo('Wishlist');

        $page_data=$home_data->meta ?? '';
        return view(baseview('wishlist'),compact('contents','products','page_data'));
    }

    public function checkout(Request $request)
    {
        $tax=optionfromcache('tax');
        if ($tax == null) {
            $tax=0;
        }
        Cart::setGlobalTax($tax);
        $order_settings=get_option('order_settings',true);
        if ($order_settings->shipping_amount_type != 'distance') {
            $locations=Location::where([['status',1]])->whereHas('shippings')->with('shippings')->get();
        }
        else{
            $locations=[];
        }
        
        $getways=Getway::where('status','!=',0)->get();

        $order_method=$request->t ?? 'delivery';
        
        $invoice_data=optionfromcache('invoice_data');
        
        $meta= !Auth::check() ? [] : json_decode(Auth::user()->meta ?? '');

        

        $home_data=optionfromcache('checkout_page');
        store_set_seo('Checkout');

        $page_data=$home_data->meta ?? '';

        $pickup_order=$order_settings->pickup_order ?? 'off';
        $pre_order=$order_settings->pre_order ?? 'off';
        $source_code=$order_settings->source_code ?? 'on';
        return view(baseview('checkout'),compact('locations','getways','request','order_method','order_settings','invoice_data','meta','page_data','pickup_order','pre_order','source_code'));
    }

    public function thanks(Request $request)
    {
        $orderno = Session::get('invoice_no') ?: $request->query('invoice_id');
        abort_if(empty($orderno), 404);

        $order = \App\Models\Order::where('invoice_no', $orderno)->with('orderitems')->first();
        $club_info = tenant_club_info();

        store_set_seo('Thanks');
        return view(baseview('thanks'), compact('orderno', 'order', 'club_info'));
    }

    public function contact()
    {
        store_set_seo('Contact');

        $club_id = (int) tenant('club_id');
        $form_id = $this->getBooostrClubContactFormId($club_id);
        $club_info = tenant_club_info();
        $formBaseUrl = rtrim(env("BOOOSTR_FORM_EMBED_URL") ?: 'https://booostr.co', '/');

        return view(baseview('contact'), compact('club_id', 'form_id', 'club_info', 'formBaseUrl'));
    }

    protected function getBooostrClubContactFormId($club_id)
    {
        if (!$club_id) {
            return null;
        }

        $url = env("WP_CLUB_URL");
        $baseUrl = !empty($url) ? rtrim($url, '/') . '/' : 'https://app.booostr.co/';
        $apiUrl = $baseUrl . 'wp-json/store-api/v1/get-store-form-contact-form/?club_id=' . $club_id;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            $response = curl_exec($ch);
            $hasError = curl_errno($ch);
            curl_close($ch);

            if (!$hasError && !empty($response)) {
                $responseData = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($responseData['forms'])) {
                    $rawForms = $responseData['forms'];
                    $forms = is_string($rawForms) ? json_decode($rawForms, true) : $rawForms;

                    if (is_array($forms) && count($forms) > 0) {
                        $firstForm = $forms[0];
                        $formId = is_array($firstForm) ? ($firstForm['id'] ?? null) : ($firstForm->id ?? null);
                        return $formId;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Booostr Contact Form fetch error: ' . $e->getMessage());
        }

        return null;
    }

    public function contact_send(Request $request)
    {
        try {
            $submittedFields = $request->except(['_token', 'form_id']);

            if (empty($submittedFields)) {
                return response()->json([
                    'errors' => ['error' => __('Please fill out the form before submitting.')]
                ], 422);
            }

            $club_id = (int) tenant('club_id');
            $form_id = $request->input('form_id');

            // Handle Booostr Form Builder sync
            if (!empty($form_id) && !empty($club_id)) {
                $wpUrl = env("WP_CLUB_URL");
                $syncUrl = !empty($wpUrl) 
                    ? rtrim($wpUrl, '/') . '/wp-json/store-api/v1/sync-store-form-data/' 
                    : 'https://app.booostr.co/wp-json/store-api/v1/sync-store-form-data/';

                $syncPayload = [
                    'form_id' => $form_id,
                    'data' => serialize($submittedFields),
                    'booostr_id' => $club_id,
                    'product_id' => 0,
                    'order_id' => 0,
                    'product_title' => 'Contact Us Form'
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $syncUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($syncPayload));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($syncPayload))
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                $syncResponse = curl_exec($ch);
                $curlError = curl_errno($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($curlError || ($httpCode >= 400 && $httpCode < 600)) {
                    \Log::warning('Booostr contact sync HTTP warning: ' . $httpCode . ' Response: ' . $syncResponse);
                }
            }

            return response()->json(__('Message Sent Successfully'));

        } catch (\Throwable $e) {
            \Log::error('Contact form submission error: ' . $e->getMessage());

            return response()->json([
                'errors' => ['error' => __('Failed to send message. Please try again later.')]
            ], 422);
        }
    }

}
