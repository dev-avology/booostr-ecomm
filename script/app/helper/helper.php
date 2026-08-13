<?php
use App\Terms;
use App\Models\Option;
use App\Models\Menu;
use Amcoders\Lpress\Lphelper;
use App\Models\Category;
use App\Models\Term;
use App\Models\Coupon;
use App\Models\AppSequires;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;



function setUSDateFormate($date = ''){
	return Carbon::parse($date)->format('m-d-Y');
}


function getCouponCode(){
	$model = new Coupon;
    $uuid = Str::random(10);
    while($model->where('code','=',$uuid)->count() > 0 ){
        $uuid = Str::random(10);
    }
    return $uuid;
}



/**
 * 
 * Get Timezone from latitude & Longitude
 * 
 */

 function getClubTimeZone(){
	
	$club_info = tenant_club_info();

	$lat_lang = explode(',',$club_info['lat_lang']);

	$response = Http::get('https://maps.googleapis.com/maps/api/timezone/json', [
        'location' => $lat_lang[0].','.$lat_lang[1],
        'timestamp' => time(),
        'key' => config('services.google_maps.key'),
    ]);

	if($response->successful()){
		$info = $response->json();
	}else{
		$info = [];
	}
	return $info;
 }


/*
replace image name via $name from $url
*/
function ImageSize($url,$name){
	$img_arr=explode('.', $url);
	$ext='.'.end($img_arr);
	$newName=str_replace($ext, $name.$ext, $url);
	return $newName;
}

function getTaxRate(){
	$tax = get_option('tax');
	return  $tax != '' ? (float)str_replace('%','',$tax) : 0;
}

function get_planinfo($key)
{
	$plan_info=json_decode(tenant('plan_info'));
    return $plan_info->$key ?? null;
}

function get_option($key,$decode=false)
{
	$option=\App\Models\Option::where('key',$key)->first();
	return $decode == false ? $option->value ?? '' : json_decode($option->value ?? '');
    
}

function load_whatsapp(){
	return view('components.whatsapp');
}

function load_header(){
	return view('components.load_header');
}

function load_footer(){
	return view('components.load_footer');
}

function getautoloadquery()
{
	if (env('CACHE_DRIVER') == 'memcached' || env('CACHE_DRIVER') == 'redis') {
		return Cache::remember('autoload', 420, function (){
			$queries=Option::where('autoload',1)->get();

			foreach ($queries as $key => $row) {
				$data[$row->key]=$row->value;
			}

			return $data ?? [];
		});
	}
	else{
		$queries=Option::where('autoload',1)->get();

		foreach ($queries as $key => $row) {
			$data[$row->key]=$row->value;
		}

		return $data ?? [];
	}
	 
	 
}

function optionfromcache($key)
{
	if (env('CACHE_DRIVER') == 'memcached' || env('CACHE_DRIVER') == 'redis'){
		return Cache::remember($key, 420, function () use ($key) {
			$option=\App\Models\Option::where('key',$key)->first();
			return json_decode($option->value ?? '');
		});
		
	}
	else{
		$option=\App\Models\Option::where('key',$key)->first();
		return json_decode($option->value ?? '');
	}
	
	
}

function baseview($page){
	
	if (tenant('theme') != null) {
		if (file_exists(base_path('resources/views/'.tenant('theme').'/'.$page.'.blade.php'))) {
			return str_replace('/','.',tenant('theme')).'.'.$page;
		}
		return '404';
	}
	return 'theme.resto.'.$page;
}

function theme_trans($key)
{
	return $key;
}

function amount_format($number)
{
	return number_format($number,2);
}

function TenantCacheClear($key)
{
	return env('CACHE_DRIVER') == 'memcached' || env('CACHE_DRIVER') == 'redis'  ? \Cache::forget($key) : true;
}

/** Bust Redis caches for POS API product/category/store read endpoints (current tenant). */
function bust_pos_api_cache(): void
{
    try {
        app(\App\Services\PosApiCacheService::class)->bump();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('bust_pos_api_cache failed', [
            'error' => $e->getMessage(),
        ]);
    }
}


function imageSizes()
{
	$sizes='[{"key":"small","height":"80","width":"80"}]';
	return $sizes;
}

function amount_admin_format($value=0)
{
	return number_format($value,2);
}

function folderSize($dir){
    $file_size = 0;
    if (!file_exists($dir)) {
        return $file_size;
    }

    foreach(\File::allFiles($dir) as $file)
    {
        $file_size += $file->getSize();
    }

    
    return $file_size = str_replace(',', '', number_format($file_size / 1048576,2));
    
}

function ThemeMenu($position,$path){
	$locale=\Session::get('locale');

	$menus=cache()->remember($position.$locale, 300, function () use ($position,$locale) {
			
			$menus=Menu::where('position',$position)->where('lang',$locale)->first();
			$data['data'] = json_decode($menus->data ?? '');
			$data['name'] = $menus->name ?? '';
			return $data;
		});
	
	return view($path.'.parent',compact('menus'));
}
function ThemeFooterMenu($position,$path){
	
	
	$locale=\Session::get('locale');

	$menus=cache()->remember($position.$locale, 300, function () use ($position,$locale) {
			
			$menus=Menu::where('position',$position)->where('lang',$locale)->first();
			$data['data'] = json_decode($menus->data ?? '');
			$data['name'] = $menus->name ?? '';
			return $data;
		});
	return view($path.'.parent',compact('menus'));
}

function getpermission($role)
{
	$permissions=Auth::user()->permissions;
	$permissions=json_decode($permissions ?? '');

	$arr=[];

	foreach($permissions as $row){
		array_push($arr,$row);
	}
	

	if (in_array($role,$arr)) {
		return true;
	}
	return false;



}


 /**
 * genarate frontend menu.
 *
 * @param $position=menu position
 * @param $ul=ul class
 * @param $li=li class
 * @param $a=a class
 * @param $icon= position left/right
 * @param $lang= translate true or false
 */

function Menu($position,$ul='',$li='',$a='',$icon_position='top',$lang=false)
{
	return Lphelper::Menu($position,$ul,$li,$a,$icon_position,$lang);
}

 /**
 * genarate frontend menu.
 *
 * @param $position=menu position
 * @param $ul=ul class
 * @param $li=li class
 * @param $a=a class
 * @param $icon= position left/right
 * @param $lang= translate true or false
 */

function MenuCustom($position,$ul='',$li='',$a='',$icon_position='top',$lang=false)
{
	return Lphelper::MenuCustom($position,$ul,$li,$a,$icon_position,$lang);
}


function NastedCategoryList($type,$selected = [],$ignore_id=null){
	$categories=\App\Models\Category::where('type',$type)
				->whereNull('category_id')
				->select('id','name','category_id')
				->where('type',$type)
			    ->with('childrenCategories')
			    ->latest()
			    ->get();

	return parentCategory($categories,$selected,$ignore_id);

}

function parentCategory($categories, $selected=[],$ignore_id=null){
	$i=0;
	foreach ($categories as $key => $category) {
		
			$disabled= $ignore_id == $category->id ? "disabled" : '';
			$confirm='';
			if (is_array($selected)) {
				if (in_array($category->id, $selected)) {
					$confirm="selected";
				}
			}
			elseif(!is_array($selected)){
				if ($category->id == $selected) {
					$confirm="selected";
				}
			}

		echo "<option ".$confirm." value=".$category->id." ".$disabled.">".$category->name."</option>";
		if (!empty($category->childrenCategories)) {
			foreach($category->childrenCategories as   $childCategory){
				echo childCategory($childCategory,$selected,$i,$ignore_id);
			}
			
		}
	}
}

function childCategory($child_category, $select=[],$i=0,$ignore_id=null)
{
	$i++;

	$confirm='';
	if (is_array($select)) {
		if (in_array($child_category->id, $select)) {
			$confirm="selected";
		}
	}
	elseif(!is_array($select)){
		if ($child_category->id == $select) {
			$confirm="selected";
		}
	}
	$nbsp='';
	for($j=0; $j < $i ; $j++){
		$nbsp .='¦– ';
	} 
	
	$disabled= $ignore_id == $child_category->id ? "disabled" : '';
	

	echo $html="<option ".$disabled." ".$confirm." value=".$child_category->id." > ".$nbsp."
    ".$child_category->name."</option>";

    if ($child_category->categories){
    	foreach ($child_category->categories as $key => $childCategory){
    		return childCategory($childCategory,$select,$i,$ignore_id);
    	}
    }

   
}

/*
return total active language
*/
function adminLang($c='')
{
	return Lphelper::AdminLang($c);
}




function mediasingle()
{
  return view('components.media.mediamodal');
}

function input($array = [])
{
	$title = $array['title'] ?? 'title';
	$type = $array['type'] ?? 'text';
	$placeholder = $array['placeholder'] ?? '';
	$name = $array['name'] ?? 'name';
	$id = $array['id'] ?? '';
	$value = $array['value'] ?? '';
	$min_input = $array['min_input'] ?? '';
	$max_input = $array['max_input'] ?? '';
	$step = $array['step'] ?? '';
	if (isset($array['is_required'])) {
		$required = $array['is_required'];
	}
	else{
		$required = false;
	}
	return view('components.input',compact('title','step','max_input','min_input','type','placeholder','name','id','value','required'));
}

function textarea($array = [])
{
	$title=$array['title'] ?? '';
	$id=$array['id'] ?? '';
	$name=$array['name'] ?? '';
	$placeholder=$array['placeholder'] ?? '';
	$maxlength=$array['maxlength'] ?? '';
	$cols=$array['cols'] ?? 30;
	$rows=$array['rows'] ?? 3;
	$class=$array['class'] ?? '';
	$value=$array['value'] ?? '';
	$is_required=$array['is_required'] ?? false;
	return view('components.textarea',compact('title','placeholder','name','id','value','is_required','class','cols','rows','maxlength'));
}

function editor($array = [])
{
	$title=$array['title'] ?? '';
	$id=$array['id'] ?? 'content';
	$name=$array['name'] ?? '';
	$cols=$array['cols'] ?? 30;
	$rows=$array['rows'] ?? 10;
	$class=$array['class'] ?? '';
	$value=$array['value'] ?? '';

	return view('components.editor',compact('title','name','id','value','class','cols','rows'));
}

function publish($array = [])
{
	$title=$array['title'] ?? 'Publish';
	$button_text=$array['button_text'] ?? 'Save';
	$class=$array['class'] ?? '';
	$id=$array['id'] ?? '';
	return view('components.publish',compact('title','button_text','class','id'));
}

function mediasection($array = [],$blade_name="section1")
{
	$title=$array['title'] ?? 'Image';
	$preview_class=$array['preview_class'] ?? 'input_preview';
	$preview=$array['preview'] ?? 'admin/img/img/placeholder.png';
	$input_id=$array['input_id'] ?? 'preview';
	$input_class=$array['input_class'] ?? 'input_image';
	$input_name=$array['input_name'] ?? 'preview';
	$value=$array['value'] ?? '';
	return view('components.media.'.$blade_name,compact('title','preview_class','preview','input_id','input_class','input_name','value'));
}

function mediasectionmulti($array = [],$blade_name="multimediasection1")
{
	$title=$array['title'] ?? 'Image';
	$preview_id=$array['preview_id'] ?? 'preview';
	$preview=$array['preview'] ?? 'admin/img/img/placeholder.png';
	$input_id=$array['input_id'] ?? 'preview_input';
	$input_class=$array['input_class'] ?? 'input_image';
	$input_name=$array['input_name'] ?? 'preview';
	$area_id=$array['area_id'] ?? 'gallary-img';
	$value=$array['value'] ?? [];
	$preview_class=$array['preview_class'] ?? 'multi_gallery';
	return view('components.media.'.$blade_name,compact('title','preview_class','preview_id','preview','input_id','input_class','input_name','value','area_id'));
}



function mediamulti()
{
	return view('components.media.multiplemediamodel');
}




/*
return admin category
*/

function  AdminCategory($type)
{
	 return Lphelper::LPAdminCategory($type);
}

/*
return category selected
*/

function AdminCategoryUpdate($type,$arr = []){

	 return Lphelper::LPAdminCategoryUpdate($type,$arr);
}




function content_format($data){
	return view('components.content',compact('data'));
}




function put($content,$root)
{
	$content=file_get_contents($content);
	File::put($root,$content);
}

function id(){
	return "36396789";
}

function currency_symbol()
{
	$symbol = Option::where('key','currency_symbol')->first();
	
	return $symbol->value ?? '$';
}

function currency()
{
	return $currency=get_option('currency');
	
}



function currency_formate($price){
	
	$currency=get_option('currency_data',true);

    $price = number_format($price,2);
  
	return $currency->currency_icon.''.$price;

}

function tenant_club_info(){
	$club_info = Tenant('club_info');
	$club_info = json_decode($club_info,true);
  return $club_info;
}

function credit_card_fee($total){
  return number_format($total * 0.029 + 0.30,2);
}

function credit_card_fee_for_pos($total,$payment_identifiers){
	if($payment_identifiers == 'card'){
		return number_format($total * 0.029 + 0.30,2);
	}else{
		return number_format($total * 0.027 + 0.05,2); // 2.7% + $0.05 for POS/terminal
	}
}


function tenant_club_is_pro(){

	$club_id =tenant('club_id');

	$response = Http::withOptions([
		'verify' => false,
	])->post(env('WP_API_URL').'/get-store-club-info', [
		'club_id' => $club_id
	]);

	if ($response->successful()) {
	    $result = $response->json();
		$club_info = isset($result['data']['is_pro']) ? $result['data']['is_pro']: false;
	} else {
		$club_info = false;
	}

	return $club_info;
}


function tenant_club_pro_live_info(){

	$club_id =tenant('club_id');

	$response = Http::withOptions([
		'verify' => false,
	])->post(env('WP_API_URL').'/get-store-club-info', [
		'club_id' => $club_id
	]);

	if ($response->successful()) {
	    $result = $response->json();
		$club_info = $result['data'];
	} else {
		$club_info = false;
	}

	return $club_info;
}


function booster_club_chagre($total){
    
  //$club_info = tenant_club_info();

  //return number_format( ($club_info['is_pro'] == 1) ? $total *0.0175 : $total *0.035,2);
  
  $pro_club = tenant_club_is_pro();

  return number_format( ($pro_club) ? $total *0.0175 : $total *0.035,2);

}


function postlimitcheck($type = true){
	if ($type == true) {
		if ((int)tenant('post_limit') != -1) {
			$category=Category::count();
		    $term=Term::count();
		    $total_count=$category+$term;

		    (int)tenant('post_limit') <= $total_count ? $status= false : $status= true;

		    return $status;
		}
		return true;
   }
	
   	if ((int)tenant('post_limit') == -1) {
   		return 99999999;
   	}

	if ($type == false) {
		$category=Category::count();
		$term=Term::count();
		$total_count=$category+$term;
		return $total_count;
	}

}

 function showAddressError(){

	$address = [];
	$club_address=Option::where('key','invoice_data')->first();

	$decode_address=json_decode($club_address->value ?? '');

	$address['store_legal_name'] = $decode_address->store_legal_name ?? '';
	$address['store_legal_phone'] = $decode_address->store_legal_phone ?? '';
	$address['store_legal_house'] = $decode_address->store_legal_house ?? '';
	$address['store_legal_address'] = $decode_address->store_legal_address ?? '';

	$address['store_legal_city'] = $decode_address->store_legal_city ?? '';
	$address['country'] = $decode_address->country ?? '';
	$address['state'] = $decode_address->state ?? '';
	$address['post_code'] = $decode_address->post_code ?? '';
	$address['store_legal_email'] = $decode_address->store_legal_email ?? '';

	if(empty($address['store_legal_address']) || empty($address['store_legal_city']) || empty($address['country']) || empty($address['state']) ||empty($address['post_code'])){
		return true;
	}else{
		return false;
	}
}

function showAddressTaxError(){

	$address = [];
	$club_address=Option::where('key','invoice_data')->first();

	$decode_address=json_decode($club_address->value ?? '');

	$address['store_legal_name'] = $decode_address->store_legal_name ?? '';
	$address['store_legal_phone'] = $decode_address->store_legal_phone ?? '';
	$address['store_legal_house'] = $decode_address->store_legal_house ?? '';
	$address['store_legal_address'] = $decode_address->store_legal_address ?? '';

	$address['store_legal_city'] = $decode_address->store_legal_city ?? '';
	$address['country'] = $decode_address->country ?? '';
	$address['state'] = $decode_address->state ?? '';
	$address['post_code'] = $decode_address->post_code ?? '';
	$address['store_legal_email'] = $decode_address->store_legal_email ?? '';

	$tax=Option::where('key','tax')->first();
    $tax = $tax->value ?? '';

	if (
		(empty($address['store_legal_address']) || empty($address['store_legal_city']) || empty($address['country']) || empty($address['state']) || empty($address['post_code']) || empty($address['store_legal_phone']))
		|| ((empty($tax) || ($tax=='0') || ($tax == '0.000%') || ($tax == '0.') || $tax == null))
	) {
		return true;
	} else {
		return false;
	}

	// && (empty($tax) || ($tax == '0.000%') || ($tax == null)
}

function userChecklist(){

	$checkList = [];

	// address checklist

	$address = [];
	$club_address=Option::where('key','invoice_data')->first();

	$decode_address=json_decode($club_address->value ?? '');

	$address['store_legal_name'] = $decode_address->store_legal_name ?? '';
	$address['store_legal_phone'] = $decode_address->store_legal_phone ?? '';
	$address['store_legal_house'] = $decode_address->store_legal_house ?? '';
	$address['store_legal_address'] = $decode_address->store_legal_address ?? '';

	$address['store_legal_city'] = $decode_address->store_legal_city ?? '';
	$address['country'] = $decode_address->country ?? '';
	$address['state'] = $decode_address->state ?? '';
	$address['post_code'] = $decode_address->post_code ?? '';
	$address['store_legal_email'] = $decode_address->store_legal_email ?? '';

	if(empty($address['store_legal_address']) || empty($address['store_legal_city']) || empty($address['country']) || empty($address['state']) || empty($address['post_code'])){

		$checkList['address'] = 0;
	}else{

		$checkList['address'] = 1;
	}

	// tax checklist code

	$tax=Option::where('key','tax')->first();
    $tax = $tax->value ?? '';

	if (empty($tax) || ($tax == '') || $tax == null) {
		$checkList['tax'] = 0;
	} else {
		$checkList['tax'] = 1;
	}

	// store banner

	$banner_logo=Option::where('key','banner_logo')->first();
	$banner_logo=$banner_logo->value ?? '';

	if (empty($banner_logo)) {
		$checkList['banner_logo'] = 0;
	} else {
		$checkList['banner_logo'] = 1;
	}

	$bannerUrls=Option::where('key','banner_url')->first();
	$bannerUrls=$bannerUrls->value ?? '';

	if (empty($bannerUrls)) {
		$checkList['banner_url'] = 0;
	} else {
		$checkList['banner_url'] = 1;
	}

	$shipping_method=Option::where('key','shipping_method')->first();

	if (empty($shipping_method->value)) {
		$checkList['shipping_method'] = 0;
	} else {
		$checkList['shipping_method'] = 1;
	}

	$free_shipping=Option::where('key','free_shipping')->first() ;
    $free_shipping = $free_shipping ?? '';

	if (empty($shipping_method)) {
		$checkList['free_shipping'] = 0;
	} else {
		$checkList['free_shipping'] = 1;
	}

	$category=Category::where('type','category')->first();

	if (empty($category)) {
		$checkList['category'] = 0;
	} else {
		$checkList['category'] = 1;
	}

	$simple_product = Term::where('type','product')->first();
	if (empty($simple_product)) {
		$checkList['simple_product'] = 0;
	} else {
		$checkList['simple_product'] = 1;
	}

	$variation_product = Category::where('type','parent_attribute')->first();
	if (empty($variation_product)) {
		$checkList['variation_product'] = 0;
	} else {
		$checkList['variation_product'] = 1;
	}

	return $checkList;
}

function settingLinks(){
	$terms_page_id = [];
	$termCondition = Term::where('type', 'page')->where('slug','terms-and-conditions')->first();
	$privacyPolicy = Term::where('type', 'page')->where('slug','privacy-policy')->first();
	$returnPolicy = Term::where('type', 'page')->where('slug','return-policy')->first();
	$terms_page_id['term_condition_id'] = $termCondition['id'] ?? '';
	$terms_page_id['privacy_policy_id'] = $privacyPolicy['id'] ?? '';
	$terms_page_id['return_policy_id'] = $returnPolicy['id'] ?? '';
	return $terms_page_id;
}

function checkListOkVal(){
	$checklistval=Option::where('key','okCheckListValue')->first() ;
    $checklistval = $checklistval->value ?? '';
	return $checklistval;
}

function storeLaunch(){
	$checklistval=Option::where('key','checkListStoreLaunch')->first() ;
    $checklistval = $checklistval->value ?? '';
	return $checklistval;
}


// 	if($footerLink){
// 		return response()->json(["status" => 'true', "message" => 'Footer link get successfully','data' =>$footerLink]);
// 	  }else{
// 		return response()->json(["status" => 'false', "message" => 'Something went wrong']);
// 	}
// }


if (!function_exists('get_secret')) {
    function get_secret($key, $default = null)
    {
        $secret = AppSequires::where('key', $key)->first();
        return $secret ? $secret->value : $default;
    }
}


if (!function_exists('tenant_club_logo')) {

    function tenant_club_logo()
    {
        if (!function_exists('tenant') || empty(tenant()->club_id)) {
            return null;
        }

        $url = env("WP_API_URL");

        $url = ($url != '')
            ? $url . '/logo-tenant?tenant=' . tenant()->club_id
            : 'https://staging3.booostr.co/wp-json/store-api/v1/logo-tenant?tenant=' . tenant()->club_id;

        try {

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            $result = json_decode($response, true);

            return $result['data'] ?? null;

        } catch (\Throwable $e) {

            \Log::warning('Tenant logo fetch failed', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

if (!function_exists('store_receipt_mail_from')) {
    /**
     * From address/name for store product checkout receipt emails.
     * Matches newsletter-style domain, with +receipt instead of +updates.
     * Example: Hello Tester Club (via Booostr) <hello-tester-club+receipt@mktg.booostr.co>
     * Reply-To uses club Sender email from /seller/site-settings/general (store_sender_email).
     */
    function store_receipt_mail_from(): array
    {
        $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
        $invoiceInfo = get_option('invoice_data', true);

        $clubName = trim((string) ($clubInfo['club_name'] ?? ''));
        if ($clubName === '') {
            $clubName = trim((string) ($invoiceInfo->store_legal_name ?? ''));
        }
        if ($clubName === '') {
            $clubName = trim((string) (tenant('store_name') ?? 'Booostr'));
        }
        if ($clubName === '') {
            $clubName = 'Booostr';
        }

        $slug = strtolower(trim((string) (tenant('id') ?? '')));
        $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'booostr';
        }

        // Sender email from site settings (/seller/site-settings/general).
        $replyTo = trim((string) get_option('store_sender_email'));
        if ($replyTo === '' || !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $replyTo = trim((string) ($invoiceInfo->store_legal_email ?? ''));
        }
        if ($replyTo === '' || !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $replyTo = null;
        }

        return [
            'address' => $slug . '+receipt@mktg.booostr.co',
            'name' => $clubName . ' (via Booostr)',
            'reply_to' => $replyTo,
        ];
    }
}

if (!function_exists('apply_store_receipt_mail_identity')) {
    /**
     * Apply formatted From + Reply-To to customer receipt emails.
     * Works with Mailable instances and Mail::send() message closures.
     * No-op when store_receipt_mail_from() is unavailable or returns empty values.
     */
    function apply_store_receipt_mail_identity($mail)
    {
        if (!function_exists('store_receipt_mail_from') || !is_object($mail)) {
            return $mail;
        }

        $receiptFrom = store_receipt_mail_from();

        if (!empty($receiptFrom['address']) && method_exists($mail, 'from')) {
            $mail->from($receiptFrom['address'], $receiptFrom['name'] ?? null);
        }

        if (!empty($receiptFrom['reply_to']) && method_exists($mail, 'replyTo')) {
            $mail->replyTo($receiptFrom['reply_to'], $receiptFrom['name'] ?? null);
        }

        return $mail;
    }
}

if (!function_exists('trigger_product_sales_crm_sync_after_order')) {
    function trigger_product_sales_crm_sync_after_order(int $orderId): void
    {
        try {
            app(\App\Services\ProductSalesCrmSyncService::class)->handleOrderItemsCreated($orderId);
        } catch (\Throwable $e) {
            \Log::warning('Product sales CRM sync after order creation failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

if (!function_exists('is_order_syncable_to_financial_manager')) {
    /**
     * Financial Manager should only receive captured payments (not authorized-only).
     * payment_status 1 = captured, 4 = authorized, 5 = refunded.
     */
    function is_order_syncable_to_financial_manager($order, ?string $post_type = null): bool
    {
        if (empty($order->captured_at)) {
            return false;
        }

        if ($post_type === 'refund') {
            return (int) $order->payment_status === 5;
        }

        if ($post_type === 'capture') {
            return (int) $order->payment_status === 1;
        }

        if (!empty($order->refunded_at) && (int) $order->payment_status === 5) {
            return true;
        }

        return (int) $order->payment_status === 1;
    }
}

if (!function_exists('has_financial_manager_capture_sync')) {
    function has_financial_manager_capture_sync(int $orderId): bool
    {
        return \App\Models\Ordermeta::where('order_id', $orderId)
            ->where('key', 'financial_manager_synced')
            ->exists();
    }
}

if (!function_exists('mark_financial_manager_capture_synced')) {
    function mark_financial_manager_capture_synced(int $orderId): void
    {
        \App\Models\Ordermeta::updateOrCreate(
            ['order_id' => $orderId, 'key' => 'financial_manager_synced'],
            ['value' => \Carbon\Carbon::now()->setTimezone(config('app.timezone'))->toDateTimeString()]
        );
    }
}

if (!function_exists('sync_order_to_financial_manager')) {
  /**
   * Real-time Financial Manager sync for captured/refund-eligible orders.
   * Safe to call after order placement; skips authorized-only orders.
   */
    function sync_order_to_financial_manager($orderId, string $post_type = 'capture'): void
    {
        try {
            $order = \App\Models\Order::with('orderitems', 'ordermeta', 'shippingwithinfo', 'getway', 'user')->find($orderId);
            if (!$order) {
                return;
            }

            if (!is_order_syncable_to_financial_manager($order, $post_type)) {
                return;
            }

            $controller = app(\App\Http\Controllers\Seller\OrderController::class);

            if (in_array((int) $order->order_from, [4, 5], true)) {
                $controller->post_order_data_POS($order, $post_type);
            } else {
                $controller->post_order_data($order, $post_type);
            }
        } catch (\Throwable $e) {
            \Log::error('Financial Manager sync failed', [
                'order_id' => $orderId,
                'post_type' => $post_type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

if (!function_exists('trigger_tenant_financial_manager_sync_after_refund')) {
    /**
     * After refund success, run tenant:sync-financial-manager for the logged-in club/tenant.
     * Does not alter existing post_order_data refund sync; additive only.
     */
    function trigger_tenant_financial_manager_sync_after_refund(?int $orderId = null): void
    {
        try {
            $tenantId = (string) (tenant('id') ?? '');
            if ($tenantId === '') {
                \Log::warning('tenant:sync-financial-manager skipped after refund: tenant id missing', [
                    'order_id' => $orderId,
                ]);
                return;
            }

            // --auto keeps the safe dedup behavior for automatic after-refund sync
            // (only the new refund is sent; already-synced sale/refunds are not duplicated).
            $params = ['tenant' => $tenantId, '--auto' => true];
            if (!empty($orderId)) {
                $params['--order'] = $orderId;
            }

            dispatch(function () use ($params, $tenantId, $orderId) {
                try {
                    \Artisan::call('tenant:sync-financial-manager', $params);
                } catch (\Throwable $e) {
                    \Log::error('tenant:sync-financial-manager failed after refund', [
                        'tenant_id' => $tenantId,
                        'order_id' => $orderId,
                        'error' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
        } catch (\Throwable $e) {
            \Log::error('Failed to dispatch tenant FM sync after refund', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

if (!function_exists('resolve_order_fee_breakdown')) {
    /**
     * Processing fee breakdown for an order (matches seller order details / FM sync).
     */
    function resolve_order_fee_breakdown($order, $ordermeta = null): array
    {
        $creditCardFee = 0.0;
        $boosterPlatformFee = 0.0;
        $coverFee = 0.0;

        if (is_string($ordermeta) || is_object($ordermeta)) {
            $ordermeta = json_decode(json_encode($ordermeta), true) ?: [];
        }
        if (!is_array($ordermeta)) {
            $ordermeta = [];
        }

        $shippingWithInfo = $order->shippingwithinfo ?? null;
        if ($shippingWithInfo && !empty($shippingWithInfo->info)) {
            $shippingData = json_decode($shippingWithInfo->info, true) ?: [];
            if (($shippingWithInfo->shipping_driver ?? '') === 'local') {
                $creditCardFee = (float) ($shippingData['credit_card_fee'] ?? 0);
                $boosterPlatformFee = (float) ($shippingData['booster_platform_fee'] ?? 0);
            }
        }

        if ($creditCardFee == 0 && $boosterPlatformFee == 0 && !empty($ordermeta)) {
            $creditCardFee = (float) ($ordermeta['credit_card_fee'] ?? 0);
            $boosterPlatformFee = (float) ($ordermeta['booster_platform_fee'] ?? 0);
            $coverFee = (float) ($ordermeta['cover_fee'] ?? 0);
        }

        return [
            'credit_card_fee' => $creditCardFee,
            'booster_platform_fee' => $boosterPlatformFee,
            'cover_fee' => $coverFee,
            'processing_fees' => $creditCardFee + $boosterPlatformFee,
        ];
    }
}

if (!function_exists('order_has_sales_tax')) {
    function order_has_sales_tax($order): bool
    {
        return round((float) ($order->tax ?? 0), 2) > 0;
    }
}

if (!function_exists('sanitize_refund_tax_for_order')) {
    function sanitize_refund_tax_for_order($order, float $taxAmount): float
    {
        return order_has_sales_tax($order) ? round(max(0, $taxAmount), 2) : 0.0;
    }
}

if (!function_exists('financial_manager_partial_refund_fingerprint')) {
    function financial_manager_partial_refund_fingerprint(array $refundLog): string
    {
        $items = [];
        foreach ($refundLog['items'] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            $items[] = [
                'item_id' => (int) ($item['item_id'] ?? 0),
                'amount' => round((float) ($item['amount'] ?? 0), 2),
                'tax' => round((float) ($item['tax'] ?? 0), 2),
                'qty' => (int) ($item['qty'] ?? 0),
                'label' => (string) ($item['label'] ?? ''),
            ];
        }

        return md5(json_encode([
            'amount' => round((float) ($refundLog['amount'] ?? 0), 2),
            'type' => (string) ($refundLog['type'] ?? ''),
            'stripe_refund_id' => (string) ($refundLog['stripe_refund_id'] ?? ''),
            'items' => $items,
        ]));
    }
}

if (!function_exists('get_financial_manager_partial_refund_synced_fingerprints')) {
    function get_financial_manager_partial_refund_synced_fingerprints(int $orderId): array
    {
        $meta = \App\Models\Ordermeta::where('order_id', $orderId)
            ->where('key', 'financial_manager_partial_refund_synced')
            ->value('value');

        $decoded = json_decode($meta ?? '[]', true);

        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('mark_financial_manager_partial_refund_synced')) {
    function mark_financial_manager_partial_refund_synced(int $orderId, string $fingerprint): void
    {
        $synced = get_financial_manager_partial_refund_synced_fingerprints($orderId);
        if (!in_array($fingerprint, $synced, true)) {
            $synced[] = $fingerprint;
        }

        \App\Models\Ordermeta::updateOrCreate(
            ['order_id' => $orderId, 'key' => 'financial_manager_partial_refund_synced'],
            ['value' => json_encode(array_values($synced))]
        );
    }
}

if (!function_exists('get_order_partial_refund_log_entries')) {
    /**
     * Normalized partial refund log rows (same source as order details / list pages).
     */
    function get_order_partial_refund_log_entries(int $orderId): array
    {
        $raw = \App\Models\Ordermeta::where('order_id', $orderId)
            ->where('key', 'partial_refund_logs')
            ->value('value');

        $logs = json_decode($raw ?? '[]', true);
        if (!is_array($logs)) {
            return [];
        }

        $entries = [];
        $seen = [];

        foreach ($logs as $log) {
            if (!is_array($log)) {
                continue;
            }

            $fingerprint = financial_manager_partial_refund_fingerprint($log);
            if (isset($seen[$fingerprint])) {
                continue;
            }
            $seen[$fingerprint] = true;

            $itemAmount = 0.0;
            $taxAmount = 0.0;
            foreach ($log['items'] ?? [] as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $itemAmount += (float) ($item['amount'] ?? 0);
                $taxAmount += (float) ($item['tax'] ?? 0);
            }

            if ($itemAmount <= 0 && empty($log['items'])) {
                $grandTotal = (float) ($log['amount'] ?? 0);
                $itemAmount = $grandTotal;
                $taxAmount = 0.0;
            }

            $entries[] = [
                'fingerprint' => $fingerprint,
                'type' => (string) ($log['type'] ?? ''),
                'refunded_at' => $log['refunded_at'] ?? null,
                'stripe_refund_id' => (string) ($log['stripe_refund_id'] ?? ''),
                'item_amount' => round($itemAmount, 2),
                'tax_amount' => round($taxAmount, 2),
                'grand_total' => round((float) ($log['amount'] ?? ($itemAmount + $taxAmount)), 2),
                'items' => $log['items'] ?? [],
            ];
        }

        return $entries;
    }
}

if (!function_exists('get_order_cumulative_partial_refund_amounts')) {
    function get_order_cumulative_partial_refund_amounts(int $orderId): array
    {
        $itemTotal = 0.0;
        $taxTotal = 0.0;

        foreach (get_order_partial_refund_log_entries($orderId) as $entry) {
            $itemTotal += (float) ($entry['item_amount'] ?? 0);
            $taxTotal += (float) ($entry['tax_amount'] ?? 0);
        }

        return [
            'item_total' => round($itemTotal, 2),
            'tax_total' => round($taxTotal, 2),
        ];
    }
}

if (!function_exists('calculate_order_total_after_partial_refunds')) {
    /**
     * Remaining order total for list/details display after partial refund(s).
     * Formula: Order Total - refunded amount - refunded tax.
     */
    function calculate_order_total_after_partial_refunds($order, $ordermeta = null): float
    {
        $refunded = get_order_cumulative_partial_refund_amounts((int) $order->id);
        $refundedItemTotal = (float) ($refunded['item_total'] ?? 0);
        $refundedTaxTotal = sanitize_refund_tax_for_order($order, (float) ($refunded['tax_total'] ?? 0));

        return max(0, round((float) $order->total - $refundedItemTotal - $refundedTaxTotal, 2));
    }
}

if (!function_exists('calculate_order_remaining_after_partial_refunds')) {
    /**
     * Remaining amounts after partial refund(s).
     * order.total excludes processing fees unless supporter covered fees.
     * remaining_total adds processing fees back for display / Financial Manager sync.
     */
    function calculate_order_remaining_after_partial_refunds($order, $ordermeta = null): array
    {
        if (is_array($ordermeta) || is_object($ordermeta)) {
            $ordermetaArray = json_decode(json_encode($ordermeta), true) ?: [];
        } else {
            $ordermetaArray = json_decode(optional($order->ordermeta)->value ?? '', true) ?: [];
        }

        $fees = resolve_order_fee_breakdown($order, $ordermetaArray);
        $refunded = get_order_cumulative_partial_refund_amounts((int) $order->id);
        $refundedItemTotal = (float) ($refunded['item_total'] ?? 0);
        $refundedTaxTotal = sanitize_refund_tax_for_order($order, (float) ($refunded['tax_total'] ?? 0));

        $remainingSubtotal = max(0, round((float) $order->total - $refundedItemTotal, 2));
        $processingFees = (float) ($fees['processing_fees'] ?? 0);
        $coverFee = (float) ($fees['cover_fee'] ?? 0);

        $remainingNet = $remainingSubtotal;
        $remainingTotal = $coverFee > 0
            ? $remainingSubtotal
            : round($remainingSubtotal + $processingFees, 2);

        $remainingSalesTax = order_has_sales_tax($order)
            ? max(0, round((float) $order->tax - $refundedTaxTotal, 2))
            : 0.0;

        $remainingNetRevenue = max(0, round($remainingTotal - $processingFees - $remainingSalesTax, 2));

        return [
            'refunded_item_total' => $refundedItemTotal,
            'refunded_tax_total' => $refundedTaxTotal,
            'remaining_net' => $remainingNet,
            'remaining_total' => $remainingTotal,
            'remaining_sales_tax' => $remainingSalesTax,
            'remaining_net_revenue' => $remainingNetRevenue,
            'processing_fees' => $processingFees,
            'cover_fee' => $coverFee,
        ];
    }
}

if (!function_exists('financial_manager_refund_detail_lines')) {
    /**
     * Build the human-readable refund detail lines exactly as shown on the order
     * details page, e.g.:
     *   "07/13/2026 - Refund & Cancel Partial Order By Dollar Amount"
     *   "NY Islanders Trip Ticket Example"
     *   "Related Tax Adjustment Refund"
     */
    function financial_manager_refund_detail_lines(string $date, string $title, array $itemLabels, float $taxAmount): array
    {
        $lines = [];
        $lines[] = trim(($date !== '' ? $date . ' - ' : '') . $title);

        foreach ($itemLabels as $label) {
            $label = trim((string) $label);
            if ($label !== '') {
                $lines[] = $label;
            }
        }

        if ($taxAmount > 0) {
            $lines[] = 'Related Tax Adjustment Refund';
        }

        return $lines;
    }
}

if (!function_exists('financial_manager_partial_refund_detail')) {
    /**
     * Structured refund detail for a single partial refund log entry.
     * Sent to /financial-manager under the additive "refund_details" key.
     */
    function financial_manager_partial_refund_detail(array $entry, $order = null): array
    {
        $isDollar = ($entry['type'] ?? '') === 'dollar';
        $title = $isDollar
            ? 'Refund & Cancel Partial Order By Dollar Amount'
            : 'Refund & Cancel Partial Order By Item';

        $date = !empty($entry['refunded_at'])
            ? \Carbon\Carbon::parse($entry['refunded_at'])->format('m/d/Y')
            : \Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('m/d/Y');

        $labels = [];
        foreach ($entry['items'] ?? [] as $it) {
            $label = trim((string) ($it['label'] ?? ''));
            if ($label !== '') {
                $labels[] = $label;
            }
        }

        $refundAmount = round((float) ($entry['item_amount'] ?? 0), 2);
        $taxAmount = $order
            ? sanitize_refund_tax_for_order($order, (float) ($entry['tax_amount'] ?? 0))
            : round((float) ($entry['tax_amount'] ?? 0), 2);

        // Match order-details UI: item refunds wrap labels as "(1 x Product)", dollar refunds show plain title(s).
        $displayLabels = $labels;
        if (!$isDollar && !empty($labels)) {
            $displayLabels = ['(' . implode(', ', $labels) . ')'];
        }

        $lines = financial_manager_refund_detail_lines($date, $title, $displayLabels, $taxAmount);

        return [
            'type' => $isDollar ? 'partial_dollar' : 'partial_item',
            'date' => $date,
            'title' => $title,
            'items' => $labels,
            'refund_amount' => $refundAmount,
            'tax_label' => 'Related Tax Adjustment Refund',
            'tax_amount' => $taxAmount,
            'grand_total' => round((float) ($entry['grand_total'] ?? ($refundAmount + $taxAmount)), 2),
            'lines' => $lines,
            'text' => implode("\n", $lines),
        ];
    }
}

if (!function_exists('financial_manager_full_refund_detail')) {
    /**
     * Structured refund detail for a full-order refund (mirrors order-details UI).
     * Sent to /financial-manager under the additive "refund_details" key.
     */
    function financial_manager_full_refund_detail($order): array
    {
        $itemTotal = 0.0;
        foreach ($order->orderitems ?? [] as $orderItem) {
            $variations = json_decode($orderItem->info ?? '');
            $options = $variations->options ?? [];
            $unit = (float) $orderItem->amount;
            if (!is_array($options) && is_object($options) && isset($options->varition_options)) {
                $unit = (float) $options->price;
            }
            $itemTotal += $unit * (int) $orderItem->qty;
        }

        $taxAmount = sanitize_refund_tax_for_order($order, round((float) ($order->tax ?? 0), 2));
        $dateSource = $order->refunded_at ?: $order->updated_at;
        $date = $dateSource
            ? \Carbon\Carbon::parse($dateSource)->format('m/d/Y')
            : \Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('m/d/Y');

        $title = 'Refund & Cancel Full Order (All Items)';
        $lines = financial_manager_refund_detail_lines($date, $title, [], $taxAmount);

        return [
            'type' => 'full',
            'date' => $date,
            'title' => $title,
            'items' => [],
            'refund_amount' => round($itemTotal, 2),
            'tax_label' => 'Related Tax Adjustment Refund',
            'tax_amount' => $taxAmount,
            'grand_total' => round($itemTotal + $taxAmount, 2),
            'lines' => $lines,
            'text' => implode("\n", $lines),
        ];
    }
}

if (!function_exists('financial_manager_refund_detail_to_memo')) {
    /**
     * Convert a refund_details structure into a memo text block with amounts.
     * Reuses refund_details['lines'] (display text) and appends the refund amount
     * to the header line and the tax amount to the tax line, e.g.:
     *   "07/13/2026 - Refund & Cancel Partial Order By Dollar Amount - $40.00"
     *   "NY Islanders Trip Ticket Example"
     *   "Related Tax Adjustment Refund - $2.40"
     */
    function financial_manager_refund_detail_to_memo(array $detail): string
    {
        $lines = $detail['lines'] ?? [];
        if (empty($lines)) {
            return '';
        }

        $refundAmount = (float) ($detail['refund_amount'] ?? 0);
        $taxAmount = (float) ($detail['tax_amount'] ?? 0);

        $memoLines = [];
        foreach ($lines as $index => $line) {
            $line = (string) $line;
            if ($index === 0) {
                // Header (date - title) → append the refunded amount.
                $memoLines[] = $line . ' - $' . number_format($refundAmount, 2);
            } elseif (trim($line) === 'Related Tax Adjustment Refund' && $taxAmount > 0) {
                $memoLines[] = $line . ' - $' . number_format($taxAmount, 2);
            } else {
                $memoLines[] = $line;
            }
        }

        return implode("\n", $memoLines);
    }
}

if (!function_exists('financial_manager_partial_refunds_memo')) {
    /**
     * Combined memo text (with amounts) of ALL partial refund entries for an order.
     * Ensures that when an order has multiple (e.g. double) partial refunds,
     * every refund detail + amount is present in the memo. Deduped by fingerprint.
     */
    function financial_manager_partial_refunds_memo($order): string
    {
        $blocks = [];
        foreach (get_order_partial_refund_log_entries((int) $order->id) as $entry) {
            $detail = financial_manager_partial_refund_detail($entry, $order);
            $block = financial_manager_refund_detail_to_memo($detail);
            if (trim($block) !== '') {
                $blocks[] = $block;
            }
        }

        return implode("\n", $blocks);
    }
}

if (!function_exists('post_partial_refund_to_financial_manager')) {
    /**
     * Send one partial refund log entry to WordPress /financial-manager.
     * Additive path used by tenant:sync-financial-manager only.
     */
    function post_partial_refund_to_financial_manager($order, array $refundEntry): ?string
    {
        if (empty($order->captured_at) || (int) $order->payment_status !== 1) {
            return null;
        }

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '', true) ?: [];
        $name = explode(' ', $ordermeta['name'] ?? '');
        $gateway = \App\Models\Getway::find($order->getway_id);

        $fees = resolve_order_fee_breakdown($order, $ordermeta);
        $creditCardFee = (float) ($fees['credit_card_fee'] ?? 0);
        $boosterPlatformFee = (float) ($fees['booster_platform_fee'] ?? 0);
        $processingFees = (float) ($fees['processing_fees'] ?? 0);

        $remaining = calculate_order_remaining_after_partial_refunds($order, $ordermeta);
        $orderTotal = (float) $order->total;
        $remainingSalesTax = (float) ($remaining['remaining_sales_tax'] ?? 0);
        $salesTax = (float) ($order->tax ?? 0);

        // Partial refund FM amounts (cumulative refunded amount + refunded tax deducted):
        //   transaction_amount = Order Total - Refunded Amount - Refunded Tax
        //   net_revenue        = transaction_amount - Sales Tax - Processing Fees
        $refundedItemTotal = (float) ($remaining['refunded_item_total'] ?? 0);
        $refundedTaxTotal = (float) ($remaining['refunded_tax_total'] ?? 0);
        $transactionAmount = max(0, round($orderTotal - $refundedItemTotal - $refundedTaxTotal, 2));
        $netRevenue = round($transactionAmount - ($salesTax + $processingFees), 2);

        $refundDate = !empty($refundEntry['refunded_at'])
            ? \Carbon\Carbon::parse($refundEntry['refunded_at'])->setTimezone(config('app.timezone'))
            : \Carbon\Carbon::now()->setTimezone(config('app.timezone'));

        $isPos = in_array((int) $order->order_from, [4, 5], true);
        $donorSuffix = $isPos ? ' (POS Order)' : ' (Online Order)';
        $refundLabel = ($refundEntry['type'] ?? '') === 'dollar'
            ? 'Partial Dollar Refund'
            : 'Partial Item Refund';

        $basePayload = [
            'category_type' => 'Booostr Ecommerce',
            'booster_id' => Tenant('club_id'),
            'coaid' => 41,
            'contactname' => $ordermeta['name'] ?? ($isPos ? 'Guest User' : ''),
            'user_id' => $ordermeta['wpuid'] ?? 0,
            'revenue_name' => '4-850 Booostr Ecommerce',
            'transaction_type' => 'I',
            'sales_tax_collected' => $remainingSalesTax > 0 ? 'Yes' : 'No',
            'net_revenue' => $netRevenue,
            'transaction_amount' => $transactionAmount,
            'expense_category' => 'Revenue',
            'receipts_issued' => 'Yes',
            'status' => 1,
            'donor_name' => ($ordermeta['name'] ?? 'Guest User') . $donorSuffix,
            'created' => $order->placed_at,
            'modified' => \Carbon\Carbon::now()->setTimezone(config('app.timezone')),
            'payement_method' => ($gateway && $gateway->name === 'cash') ? 0 : 3,
            'invoicenumber' => $order->invoice_no,
            'invoicreatedate' => $order->placed_at,
            'invoiceprocessingfee' => round($processingFees, 2),
            'invoicesalestax' => $remainingSalesTax,
            'invoiceopt' => $order->invoice_no,
            'deposite_date' => $order->captured_at,
            'transfer_refund_date' => $refundDate->toDateTimeString(),
            'record_type' => 'refund',
            // Additive: same refund text shown on the order details page (partial refund).
            // NOTE: 'refund_details' key commented out on request; kept for future use.
            // 'refund_details' => financial_manager_partial_refund_detail($refundEntry),
            // Additive: refund text + amount(s) as memo. Includes every partial refund
            // on this order, so a double refund shows both details with their amounts.
            'memo' => financial_manager_partial_refunds_memo($order),
            'store_t_type' => 'refund',
        ];

        if ($isPos) {
            $postData = json_encode($basePayload);
        } else {
            $contactManagerData = [
                'first_name' => $name[0] ?? '',
                'last_name' => $name[1] ?? '',
                'user_id' => $ordermeta['wpuid'] ?? 0,
                'phone_number' => $ordermeta['phone'] ?? '',
                'booster_name' => $name[0] ?? '',
                'country' => $ordermeta['billing']['country'] ?? '',
                'address_1' => $ordermeta['billing']['address'] ?? '',
                'address_2' => '',
                'city' => $ordermeta['billing']['city'] ?? '',
                'state' => $ordermeta['billing']['state'] ?? '',
                'zip' => $ordermeta['billing']['post_code'] ?? '',
                'email' => $ordermeta['email'] ?? '',
                'booster_id' => Tenant('club_id'),
                'booster_level_id' => 1,
                'contact_tags' => '',
                'customer_tag' => 'online store customer',
                'addedsource' => 'storetool',
            ];
            $postData = json_encode(array_merge(['contact_mgr_data' => $contactManagerData], $basePayload));
        }

        $url = env('WP_API_URL');
        $url = ($url != '') ? $url . '/financial-manager' : 'https://staging3.booostr.co/wp-json/store-api/v1/financial-manager';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Tantent store');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            \Log::error('Partial refund FM sync cURL error', [
                'order_id' => $order->id,
                'error' => curl_error($ch),
            ]);
        }
        curl_close($ch);

        return is_string($response) ? $response : null;
    }
}

if (!function_exists('sync_order_partial_refunds_to_financial_manager')) {
    /**
     * Sync all unsynced partial refund log entries for one order.
     * Used by tenant:sync-financial-manager (does not alter capture/full refund helpers).
     */
    function sync_order_partial_refunds_to_financial_manager(int $orderId, bool $force = false): int
    {
        $order = \App\Models\Order::with('orderitems', 'ordermeta', 'shippingwithinfo', 'getway', 'user')->find($orderId);
        if (!$order || empty($order->captured_at) || (int) $order->payment_status !== 1) {
            return 0;
        }

        $entries = get_order_partial_refund_log_entries($orderId);
        if (empty($entries)) {
            return 0;
        }

        $syncedFingerprints = get_financial_manager_partial_refund_synced_fingerprints($orderId);
        $syncedCount = 0;

        foreach ($entries as $entry) {
            $fingerprint = $entry['fingerprint'];
            if (!$force && in_array($fingerprint, $syncedFingerprints, true)) {
                continue;
            }

            try {
                post_partial_refund_to_financial_manager($order, $entry);
                mark_financial_manager_partial_refund_synced($orderId, $fingerprint);
                $syncedFingerprints[] = $fingerprint;
                $syncedCount++;
            } catch (\Throwable $e) {
                \Log::error('Partial refund FM sync failed', [
                    'order_id' => $orderId,
                    'fingerprint' => $fingerprint,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $syncedCount;
    }
}

if (!function_exists('ticket_email_qr_apply_logo_overlay')) {
    /**
     * Merge club logo into center of QR PNG (print-style), returns raw PNG bytes.
     */
    function ticket_email_qr_apply_logo_overlay(string $pngBinary, ?string $clubLogo): string
    {
        if (empty($clubLogo) || !function_exists('imagecreatefromstring')) {
            return $pngBinary;
        }

        try {
            $qrImg = @imagecreatefromstring($pngBinary);
            if ($qrImg === false) {
                return $pngBinary;
            }

            $logoData = @file_get_contents($clubLogo);
            if ($logoData === false) {
                imagedestroy($qrImg);
                return $pngBinary;
            }

            $logoSrc = @imagecreatefromstring($logoData);
            if ($logoSrc === false) {
                imagedestroy($qrImg);
                return $pngBinary;
            }

            $qrW = imagesx($qrImg);
            $qrH = imagesy($qrImg);
            $circleSize = (int) round(min($qrW, $qrH) * 0.28);
            $centerX = (int) ($qrW / 2);
            $centerY = (int) ($qrH / 2);

            $white = imagecolorallocate($qrImg, 255, 255, 255);
            imagefilledellipse($qrImg, $centerX, $centerY, $circleSize, $circleSize, $white);

            $logoSize = (int) round($circleSize * 0.68);
            $srcW = imagesx($logoSrc);
            $srcH = imagesy($logoSrc);
            $crop = min($srcW, $srcH);

            imagecopyresampled(
                $qrImg,
                $logoSrc,
                $centerX - (int) ($logoSize / 2),
                $centerY - (int) ($logoSize / 2),
                (int) (($srcW - $crop) / 2),
                (int) (($srcH - $crop) / 2),
                $logoSize,
                $logoSize,
                $crop,
                $crop
            );

            imagedestroy($logoSrc);

            ob_start();
            imagepng($qrImg);
            $merged = ob_get_clean();
            imagedestroy($qrImg);

            return $merged ?: $pngBinary;
        } catch (\Throwable $e) {
            \Log::warning('ticket_email_qr_apply_logo_overlay failed', ['error' => $e->getMessage()]);

            return $pngBinary;
        }
    }
}

if (!function_exists('ticket_email_qr_public_url')) {
    function ticket_email_qr_public_url(string $ticketUuid): string
    {
        return url('/tickets/qr_' . $ticketUuid . '.png');
    }
}

if (!function_exists('ticket_email_qr_storage_dir')) {
    /** tickets/ next to script/ (project root), not public/tickets */
    function ticket_email_qr_storage_dir(): string
    {
        return dirname(base_path()) . DIRECTORY_SEPARATOR . 'tickets';
    }
}

if (!function_exists('ticket_email_qr_disk_path')) {
    function ticket_email_qr_disk_path(string $ticketUuid): string
    {
        return ticket_email_qr_storage_dir() . DIRECTORY_SEPARATOR . 'qr_' . $ticketUuid . '.png';
    }
}

if (!function_exists('ticket_email_qr_save_file')) {
    /**
     * Save ticket QR PNG to tickets/qr_{uuid}.png (legacy folder beside script/).
     */
    function ticket_email_qr_save_file(string $ticketUuid, string $qrBase64): bool
    {
        if ($qrBase64 === '') {
            return false;
        }

        $binary = base64_decode($qrBase64, true);
        if ($binary === false || $binary === '') {
            return false;
        }

        $dir = ticket_email_qr_storage_dir();
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents(ticket_email_qr_disk_path($ticketUuid), $binary) !== false;
    }
}

if (!function_exists('ticket_email_qr_png_base64')) {
    /**
     * PNG QR + logo for ticket emails (imagick when available, else DNS2D/GD).
     */
    function ticket_email_qr_png_base64(string $scanUrl, ?string $clubLogo = null): string
    {
        if (extension_loaded('imagick')) {
            try {
                $pngBinary = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(320)
                    ->margin(2)
                    ->errorCorrection('H')
                    ->generate($scanUrl);

                if (!empty($pngBinary)) {
                    $pngBinary = ticket_email_qr_apply_logo_overlay($pngBinary, $clubLogo);

                    return base64_encode($pngBinary);
                }
            } catch (\Throwable $e) {
                \Log::warning('ticket_email_qr_png imagick failed', ['error' => $e->getMessage()]);
            }
        }

        $qrBase64 = \DNS2D::getBarcodePNG($scanUrl, 'QRCODE', 80, 80);
        if (empty($qrBase64)) {
            return '';
        }

        if (empty($clubLogo)) {
            return $qrBase64;
        }

        $merged = ticket_email_qr_apply_logo_overlay(base64_decode($qrBase64), $clubLogo);

        return base64_encode($merged);
    }
}

if (!function_exists('default_ticket_service_fee')) {
    function default_ticket_service_fee(): float
    {
        return 0.75;
    }
}

if (!function_exists('ticket_service_fee_for_term')) {
    function ticket_service_fee_for_term(?int $termId): float
    {
        if (!$termId) {
            return default_ticket_service_fee();
        }

        $fee = \App\Models\Termmeta::where('term_id', $termId)
            ->where('key', 'ticket_fee')
            ->value('value');

        if ($fee === null || $fee === '') {
            return default_ticket_service_fee();
        }

        return (float) $fee;
    }
}

if (!function_exists('is_ticket_product_term')) {
    function is_ticket_product_term($term): bool
    {
        if (!$term) {
            return false;
        }

        return (int) ($term->is_variation ?? 0) === 2;
    }
}

/** Stored prices.price (base + service fee) → seller base ticket price input. */
if (!function_exists('ticket_stored_price_to_seller_base')) {
    function ticket_stored_price_to_seller_base(float $storedPrice, ?float $fee = null): float
    {
        $fee = $fee ?? default_ticket_service_fee();
        $base = $storedPrice - $fee;

        return $base < 0 ? 0.0 : round($base, 2);
    }
}

/** Seller base ticket price input → stored prices.price (base + service fee). */
if (!function_exists('ticket_seller_base_to_stored_price')) {
    function ticket_seller_base_to_stored_price(float $basePrice, ?float $fee = null): float
    {
        $fee = $fee ?? default_ticket_service_fee();

        return round($basePrice + $fee, 2);
    }
}