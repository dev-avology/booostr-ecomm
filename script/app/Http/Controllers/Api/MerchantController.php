<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Domain;
use App\Models\Option;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\DomaintransferOtp;
use App\Jobs\SendEmailJob;
use Http;
use Artisan;
use File;
use App\Models\Order;
use App\Models\Plan;
use Crypt;
use Storage;
use App\Mail\PlanMail;
use App\Models\Ordermeta;
use App\Models\Tenantmeta;
use App\Models\Getway;
use App\Models\Tenantorder;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class MerchantController extends Controller
{


  public function createmerchant(Request $request)
  {
    
    $validator = Validator::make([
      'name' => 'required|max:50',
      'email' => 'required|max:100|email|unique:users',
      'password' => 'required|min:6',
    ]);
    if ($validator->fails()) {
      return response()->json(["status"=>0,"message"=>$validator->errors()], 422);
    }
    DB::beginTransaction();
    try {
      //write your code here
      $obj = new User();
      $obj->password = Hash::make($request->password);
      $obj->name = $request->name;
      $obj->email = $request->email;
      $obj->status = isset($request->status) ? $request->status : 1;
      $obj->role_id = 2;
      $obj->save();
      DB::commit();
      return response()->json(["status"=>1,"message"=>'Partner Created Successfully',"result"=>["partner_id"=>$obj->id]]);
    } catch (\Exception $e) {
      DB::rollback();
    }
    return response()->json(["status"=>0,"message"=>'Error Occured'],422);
  }

  public function login(Request $request){

    $credentials = $request->only(["email","password"]);
    if ($token = $this->guard()->attempt($credentials)) {
        return response()->json(["status"=>1,"message"=>'login success',"result"=>["token"=>$token]]);
    }
    return response()->json(["status"=>0,"message"=>'Sorry, email or password is wrong'],422);
}

  public function createstore(Request $request)
  {
    
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      //'password' => 'required|min:8|max:50|confirmed',
      'store_name' => 'required|max:150|unique:tenants,id|regex:/^\S*$/u',
      'club_id'=>'required|integer|min:1|max:200000',
    ]);
    if ($validator->fails()) {
      return response()->json(["status"=>0,"message"=>$validator->errors()], 422);
    }

    $name = Str::slug($request->store_name);
    $club_id=$request->club_id;
    $tenant = Tenant::where(function ($query) use ($name,$club_id) {
          $query->where('id', '=', $name)
                ->orWhere('club_id', '=', $club_id);
      })->first();
    if ($tenant) {
      $error = 'Store URL is unavailable';
      return response()->json(["status"=>0,"message"=>$error], 422);
    }
 
    //domain check
    $domain_name = $name . '.' . env('APP_PROTOCOLESS_URL');
    $domain=Domain::where('domain', $domain_name)->first();
    if ($domain) {
      $error = 'Store URL is unavailable';
      return response()->json(["status"=>0,"message"=>$error], 422);
    }
   
    $store_data = [
      'store_name' => $name,
      'email' => $request->email,
      'password' => $request->email, //$request->password,
      'club_id'=>$club_id
    ];

    Session::put('store_data',$store_data);
    return $this->gateways();
  }

  private function gateways($planid=1)
  {
    $plan = Plan::where([['status', 1]])->findOrFail($planid);
    $gateways = Getway::where('id', '!=', 13)->where('status', 1)->get();
    $plan = Plan::findOrFail($planid);
    $tax = Option::where('key', 'tax')->first();
    $plan_data = json_decode($plan->data);
    
    if ($plan->is_trial == 1) {
      $domain['name'] = Session::get('store_data')['store_name'];
      Session::put('domain_data', $domain);
      $tax_amount = ($plan->price / 100) * $tax->value;
      // Insert transaction data into order table

      $order = new Order;
      $order->plan_id = $plan->id;
      $order->user_id = Auth::id();
      $order->getway_id = 13;
      $order->tax = $tax_amount;
      $order->price = $plan->price;
      $order->status = 1;
      $order->payment_status = 1;
      $order->will_expire = Carbon::now()->addDays($plan->duration);
      $order->save();
      Session::put('order_id', $order->id);
      Session::put('plan', $plan->id);
      return $this->storePlan();
    } 
    return response()->json(["status"=>0,"message"=>'error']);
  }

  private function storePlan()
  {
    if (!Session::has('domain_data')) {
      $error= 'Domain already created!!';
      return response()->json(["status"=>0,"message"=>$error,"result"=>['redirect_url' => route('merchant.domain.list'), 'store_status' => 0]]);
    }
    $plan_id = Session::get('plan');
    $name = Str::slug(Session::get('store_data')['store_name']);
    $club_id = Session::get('store_data')['club_id'];
    $order_id = Session::get('order_id');
    abort_if(empty($order_id), 404);
    ini_set('max_execution_time', '0');
    $order = Order::findOrFail($order_id);
    $gateway = Getway::findOrFail($order->getway_id);
    $plan = Plan::findorFail($plan_id);
    $totalAmount = $plan->price * $gateway->rate;
    $exp_days =  $plan->duration;
    $expiry_date = \Carbon\Carbon::now()->addDays($exp_days)->format('Y-m-d');
    $domain = env('APP_URL_WITH_TENANT') . Str::slug($name);
    $status = env('AUTO_TENANT_APPROVE') == true ? 1 : 2;
    $plan_info = json_decode($plan->data ?? '');
    if (env('AUTO_DB_CREATE') == true) {
      if ($order->status == 1) {
        $tenant = new  Tenant;
        foreach ($plan_info ?? [] as $key => $value) {
          $tenant->$key = $value;
        }
        $tenant->status = $status;
      } else {
        $tenant = new \App\Tenant;
        $tenant->status = 2;
      }
    } else {
      $tenant = new \App\Tenant;
      $tenant->status = 2;
    }
    $tenant->id = Str::slug($name);
    $tenant->uid = \App\Tenant::count() + 1;
    $tenant->order_id = $order->id;
    $tenant->user_id = Auth::id();
    $tenant->will_expire = $expiry_date;
    $tenant->club_id=$club_id;
    $tenant->save();

    DB::beginTransaction();
    try {
      $tenant_id = Str::slug($name);
      $domain_name = $name . '.' . env('APP_PROTOCOLESS_URL');
      $type = 2;
      $status = env('AUTO_SUBDOMAIN_APPROVE') == true ? 1 : 2;
      if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
        $tenant->domains()->create(['domain' => $domain_name, 'tenant_id' => $tenant_id, 'type' => $type, 'status' => $status]);
        $tenant->tenantorderlog()->create(['order_id' => $order->id]);
      } else {
        $domain = new Domain;
        $domain->domain = $domain_name;
        $domain->tenant_id = $tenant_id;
        $domain->type = $type;
        $domain->status = $status;
        $domain->save();

        $log = new Tenantorder;
        $log->order_id = $order->id;
        $log->tenant_id = $tenant_id;
        $log->save();
      }

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollback();
      $error=$th->getMessage();
      return response()->json(["status"=>0,"message"=>$error], 422);
    }
    Session::forget('order_id');

    // $data = [
    //   'type'    => 'plan',
    //   'email'   => env('MAIL_TO'),
    //   'name'    => Auth::user()->name,
    //   'message' => "Successfully Paid " . round($totalAmount, 2) . " (charge included) for " . $plan->name . " plan",
    // ];

    // if (env('QUEUE_MAIL') == 'on') {
    //   dispatch(new SendEmailJob($data));
    // } else {
    //   Mail::to(env('MAIL_TO'))->send(new PlanMail($data));
    // }

    Session::forget('domain_data');
    Session::forget('order_id');

    if ($plan->is_trial == 1) {
      $store_lock = true;
    } else {
      $store_lock = false;
    }

    if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
      if (env('AUTO_SUBDOMAIN_APPROVE') == true) {
        $redirect_url = '//' . $name . '.' . env('APP_PROTOCOLESS_URL') . '/redirect/login?email=' . Session::get('store_data')['email'] . '&&password=' . Session::get('store_data')['password'];
      } else {
        $redirect_url = env('APP_URL_WITH_TENANT') . $name . '/redirect/login?email=' . Session::get('store_data')['email'] . '&&password=' . Session::get('store_data')['password'];
      }
    } else {

      $redirect_url = route('merchant.domain.list');
    }

    Session::forget('store_data');
    Session::forget('plan');
    if (env('AUTO_DB_CREATE') == true && $tenant->status == 1) {
      return response()->json(["status"=>1,"message"=>"","result"=>['redirect_url' => $redirect_url, 'store_status' => $tenant->status, 'response' => 'success',"store_id"=>$tenant_id]]);
    } else {
      return response()->json(["status"=>1,"message"=>"","result"=>['redirect_url' => $redirect_url, 'store_status' => $tenant->status, 'response' => 'success_redirect',"store_id"=>$tenant_id]]);
    }
  }



  

}
