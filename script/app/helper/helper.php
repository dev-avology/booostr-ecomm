<?php
use App\Terms;
use App\Models\Option;
use App\Models\Menu;
use Amcoders\Lpress\Lphelper;
use App\Models\Category;
use App\Models\Term;
use Illuminate\Support\Facades\Http;



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
        'key' => 'AIzaSyCmimJcxCmMIgBR0G0UKmQAgfr7RSS8pDg',
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
  return  $tax != '' ? (float)$tax : 0; 
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


function booster_club_chagre($total){
    
	$club_info = tenant_club_info();

  return number_format( ($club_info['is_pro'] == 1) ? $total *0.0175 : $total *0.035,2);
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

	$decode_address=json_decode($club_address->value);

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

	$decode_address=json_decode($club_address->value);

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
    $tax = $tax->value;

	if (
		(empty($address['store_legal_address']) || empty($address['store_legal_city']) || empty($address['country']) || empty($address['state']) || empty($address['post_code']))
		|| ((empty($tax) || ($tax=='0') || ($tax == '0.000%') || ($tax == '0.') || $tax == null))
	) {
		return true;
	} else {
		return false;
	}

	// && (empty($tax) || ($tax == '0.000%') || ($tax == null)
}
