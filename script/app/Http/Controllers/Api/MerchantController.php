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
use Auth;
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
class MerchantController extends Controller
{

  public function createstore(Request $request)
  {

    $request->validate([

      'email' => 'required|email',
      //'password' => 'required|min:8|max:50|confirmed',
      'store_name' => 'required|max:50|unique:tenants,id|regex:/^\S*$/u',
    ]);

    $name = Str::slug($request->store_name);

    $tenant = Tenant::where('id', $name)->first();

    if ($tenant) {
      $error['errors']['domain'] = 'Store URL is unavailable';
      return response()->json($error, 422);
    }
   
    $store_data = [
      'store_name' => $name,
      'email' => $request->email,
      'password' => $request->email, //$request->password,
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
      $order->user_id = 2;//Auth::id();
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
  }

  private function deposit(Request $request)
  {
    $plan = Plan::where('status', 1)->findOrFail($request->plan_id);

    if ($plan->is_trial == 1) {
      $order = Order::where([['plan_id', $request->plan_id], ['user_id', Auth::id()]])->first();
      if ($order == null) {
        $data['payment_status'] = 1;
        $data['payment_type'] = 'new_plan_enroll';
        $data['name'] = Str::slug(Session::get('store_data')['store_name']);
        $data['getway_id'] = Getway::where('name', 'free')->pluck('id')->first() ?? 13;
        $data['payment_id'] = $this->uniquetrx();
        Session::put('domain_data', $data);
        Session::put('payment_info', $data);
        Session::put('plan', $request->plan_id);
        return redirect()->route('merchant.payment.success');
      } else {
        return redirect()->route('merchant.plan.index')->with('message', 'Already enrolled in Trial Plan! Select Other Plan')->with('type', 'danger');
      }
    }

    $gateway = Getway::where([['status', 1], ['id', '!=', 13]])->findOrFail($request->id);
    $gateway_info = json_decode($gateway->data); //for creds

    $plan_data = json_decode($plan->data);
    $domain_id = Tenant::find(Str::slug($request->name));
    if (!empty($domain_id)) {
      Session::flash('error', 'The store name has already been taken.');
      return back();
    }

    if ($gateway->is_auto == 0 && $gateway->id != 14) {
      $request->validate([
        'screenshot' => 'required|image|max:800',
        'comment' => 'required|string|max:100',
      ]);
      $payment_data['comment'] = $request->comment;
      if ($request->hasFile('screenshot')) {


        $path = 'uploads/' . strtolower(env('APP_NAME')) . '/payments' . date('/y/m/');
        $name = uniqid() . date('dmy') . time() . "." . strtolower($request->screenshot->getClientOriginalExtension());

        Storage::disk(env('STORAGE_TYPE'))->put($path . $name, file_get_contents(Request()->file('screenshot')));

        $image = Storage::disk(env('STORAGE_TYPE'))->url($path . $name);

        $payment_data['screenshot'] = $image;
      }
    }
    $tax = Option::where('key', 'tax')->first();
    $payment_data['currency'] = $gateway->currency_name ?? 'USD';
    $payment_data['email'] = Auth::user()->email;
    $payment_data['name'] = Auth::user()->name;
    $payment_data['phone'] = $request->phone;
    $payment_data['billName'] = $plan->name;
    $payment_data['amount'] = $plan->price;
    $payment_data['test_mode'] = $gateway->test_mode;
    $payment_data['charge'] = $gateway->charge ?? 0;
    $payment_data['pay_amount'] = (round($plan->price + (($plan->price / 100) * $tax->value), 2) * $gateway->rate) + $gateway->charge ?? $request->session()->get('usd_amount');
    $payment_data['getway_id'] = $gateway->id;
    $payment_data['payment_type'] = 'new_plan_enroll';
    $payment_data['request'] = $request->except('_token');
    $payment_data['request_from'] = 'merchant';
    Session::put('plan', $request->plan_id);
    $domain['name'] = Str::slug($request->name);
    Session::put('domain_data', $domain);
    if (!empty($gateway_info)) {
      foreach ($gateway_info as $key => $info) {
        $payment_data[$key] = $info;
      };
    }


    return $gateway->namespace::make_payment($payment_data);
  }

  private function success(Request $request)
  {
    if (!session()->has('payment_info') && session()->get('payment_info')['payment_status'] != 1) {
      abort(404);
    }
    // abort_if(session()->get('payment_info')['payment_type'] != 'new_plan_enroll',404);
    $tax = Option::where('key', 'tax')->first();
    //if transaction successfull
    $plan_id = $request->session()->get('plan');
    $plan = Plan::findOrFail($plan_id);

    $getway_id = $request->session()->get('payment_info')['getway_id'];
    $gateway = Getway::findOrFail($getway_id);
    $trx = $request->session()->get('payment_info')['payment_id'];
    $payment_status = $request->session()->get('payment_info')['payment_status'] ?? 0;
    $status = $request->session()->get('payment_info')['status'] ?? 1;


    $tax_amount = ($plan->price / 100) * $tax->value;
    // Insert transaction data into order table

    DB::beginTransaction();
    try {

      $order = new Order;
      $order->plan_id = $plan_id;
      $order->user_id = Auth::id();
      $order->getway_id = $gateway->id;
      $order->trx = $trx;
      $order->tax = $tax_amount;
      $order->price = $plan->price;
      $order->status = $status;
      $order->payment_status = $payment_status;
      $order->will_expire = Carbon::now()->addDays($plan->duration);
      $order->save();

      Session::put('order_id', $order->id);

      //ordermeta
      if ($gateway->is_auto == 0) {
        $data = Session::get('payment_info')['meta'] ?? '';

        $order->ordermeta()->create([
          'key' => 'orderinfo',
          'value' => json_encode($data)
        ]);
      }

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollback();
      Session::forget('payment_info');
      Session::flash('message', 'Something wrong please contact with support..!');
      Session::flash('type', 'error');
      return redirect()->route('merchant.plan.index');
    }


    $status = Session::get('payment_info')['payment_status'];

    Session::put('order_status', $status);
    Session::flash('message', 'Transaction Successfully Complete!');
    Session::flash('type', 'success');
    Session::forget('payment_info');
    if ($status != 0) {
      return redirect()->route('merchant.plan.enroll');
    } else {
      return redirect()->route('merchant.plan.index');
    }
  }


  private function storePlan()
  {
    if (!Session::has('domain_data')) {
      $error['errors']['email'] = 'Domain already created!!';
      return response()->json(['data' => ['redirect_url' => route('merchant.domain.list'), 'store_status' => 0, 'response' => 'success']]);
    }
    $plan_id = Session::get('plan');
    $name = Str::slug(Session::get('store_data')['store_name']);
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
    $tenant->user_id = 2;//Auth::id();
    $tenant->will_expire = $expiry_date;

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
      return $th;
      $error['errors']['email'] = 'Error Occured';

      return response()->json($error, 422);
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
      return response()->json(['data' => ['redirect_url' => $redirect_url, 'store_status' => $tenant->status, 'response' => 'success']]);
    } else {
      return response()->json(['data' => ['redirect_url' => $redirect_url, 'store_status' => $tenant->status, 'response' => 'success_redirect']]);
    }
  }

  public function check(Request $request)
  {
    $request->validate([
      'domain' => 'required|max:20|unique:tenants,id|regex:/^\S*$/u',
    ]);
    $store_name = Str::slug($request->domain);
    $store_name = str_replace('-', '', $store_name);
    $tenant = Tenant::where('id', $store_name)->first();
    if ($tenant) {
      return response()->json(['errors' => 'Store URL is unavailable']);
    } else {
      return response()->json('success');
    }
  }



  public function domainConfig($id)
  {
    $info = Tenant::where('user_id', Auth::id())->with('subdomain', 'customdomain')->where('status', 1)->findorFail($id);
    $plan = json_decode($info->plan_info ?? '');

    $dns = Option::where('key', 'dns_settings')->first();
    $dns = json_decode($dns->value ?? '');
    return view('merchant.domain.config', compact('info', 'plan', 'dns'));
  }

  public function update(Request $request, $id)
  {
    $validatedData = $request->validate([
      'name' => 'required|string|max:50',
    ]);
    $check = Tenant::where([['id', Str::slug($request->name)], ['id', '!=', $id]])->where('status', 1)->first();
    if (!empty($check)) {
      $error['errors']['domain'] = 'Store already exists';
      return response()->json($error, 422);
    }

    $info = Tenant::where('user_id', Auth::id())->findorFail($id);
    $info->id = Str::slug($request->name);
    if ($request->auto_renew) {
      $info->auto_renew = 1;
    } else {
      $info->auto_renew = 0;
    }
    $info->save();

    return response()->json('Store Name Updated');
  }


  //add new subdomain
  public function addSubdomain(Request $request, $id)
  {
    $info = Tenant::where('user_id', Auth::id())->where('status', 1)->findorFail($id);
    $check_before = Domain::where([['tenant_id', $id], ['type', 2]])->first();
    if (!empty($check_before)) {
      $error['errors']['domain'] = 'Oops you already subdomain created....!!';
      return response()->json($error, 422);
    }



    if ($info->sub_domain == 'on') {
      $validatedData = $request->validate([
        'subdomain' => 'required|string|max:50',
      ]);

      $domain = strtolower($request->subdomain) . '.' . env('APP_PROTOCOLESS_URL');
      $input = trim($domain, '/');
      if (!preg_match('#^http(s)?://#', $input)) {
        $input = 'http://' . $input;
      }
      $urlParts = parse_url($input);
      $domain = preg_replace('/^www\./', '', $urlParts['host'] ?? $urlParts['path']);


      $check = Domain::where('domain', $domain)->first();
      if (!empty($check)) {
        $error['errors']['domain'] = 'Oops domain name already taken....!!';
        return response()->json($error, 422);
      }

      $subdomain = new Domain;
      $subdomain->domain = $domain;
      $subdomain->tenant_id = $id;
      if (env('AUTO_SUBDOMAIN_APPROVE') == true) {
        $subdomain->status = 1;
      } else {
        $subdomain->status = 2;
      }
      $subdomain->type = 2;
      $subdomain->save();

      return response()->json('Subdomain Created Successfully...!!');
    }

    $error['errors']['domain'] = 'Sorry subdomain modules not support in your plan....!!';
    return response()->json($error, 422);
  }


  //store custom domain
  public function addCustomDomain(Request $request, $id)
  {
    $checkisvalid = $this->is_valid_domain_name($request->domain);
    if ($checkisvalid == false) {
      $error['errors']['domain'] = 'Please enter valid domain....!!';
      return response()->json($error, 422);
    }



    $info = Tenant::where('user_id', Auth::id())->where('status', 1)->findorFail($id);
    $check_before = Domain::where([['tenant_id', $id], ['type', 3]])->first();
    if (!empty($check_before)) {
      $error['errors']['domain'] = 'Oops you already customdomain created....!!';
      return response()->json($error, 422);
    }


    if ($info->custom_domain == 'on') {
      $validatedData = $request->validate([
        'domain' => 'required|string|max:50',
      ]);

      $domain = strtolower($request->domain);
      $input = trim($domain, '/');
      if (!preg_match('#^http(s)?://#', $input)) {
        $input = 'http://' . $input;
      }
      $urlParts = parse_url($input);
      $domain = preg_replace('/^www\./', '', $urlParts['host']);

      $checkArecord = $this->dnscheckRecordA($domain);
      $checkCNAMErecord = $this->dnscheckRecordCNAME($domain);
      if ($checkArecord != true) {
        $error['errors']['domain'] = 'A record entered incorrectly.';
        return response()->json($error, 422);
      }

      if ($checkCNAMErecord != true) {
        $error['errors']['domain'] = 'CNAME record entered incorrectly.';
        return response()->json($error, 422);
      }

      $check = Domain::where('domain', $domain)->first();
      if (!empty($check)) {
        $error['errors']['domain'] = 'Oops domain name already taken....!!';
        return response()->json($error, 422);
      }

      $subdomain = new Domain;
      $subdomain->domain = $domain;
      $subdomain->tenant_id = $id;
      $subdomain->status = 2;
      $subdomain->type = 3;
      $subdomain->save();

      return response()->json('Custom Domain Created Successfully...!!');
    }

    $error['errors']['domain'] = 'Sorry customdomain modules not support in your plan....!!';
    return response()->json($error, 422);
  }

  //update subdomain
  public function updateSubdomain(Request $request, $id)
  {
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);


    if ($info->sub_domain == 'on') {
      $validatedData = $request->validate([
        'subdomain' => 'required|string|max:50',
      ]);

      $domain = strtolower($request->subdomain) . '.' . env('APP_PROTOCOLESS_URL');
      $input = trim($domain, '/');
      if (!preg_match('#^http(s)?://#', $input)) {
        $input = 'http://' . $input;
      }
      $urlParts = parse_url($input);
      $domain = preg_replace('/^www\./', '', $urlParts['host']);


      $check = Domain::where('domain', $domain)->where('tenant_id', '!=', $id)->first();
      if (!empty($check)) {
        $error['errors']['domain'] = 'Oops domain name already taken....!!';
        return response()->json($error, 422);
      }

      $subdomain = Domain::where([['tenant_id', $id], ['type', 2]])->first();
      $subdomain->domain = $domain;
      $subdomain->save();

      return response()->json('Subdomain Updated Successfully...!!');
    }

    $error['errors']['domain'] = 'Sorry subdomain modules not support in your plan....!!';
    return response()->json($error, 422);
  }

  //update custom domain
  public function updateCustomDomain(Request $request, $id)
  {

    $checkisvalid = $this->is_valid_domain_name($request->domain);
    if ($checkisvalid == false) {
      $error['errors']['domain'] = 'Please enter valid domain....!!';
      return response()->json($error, 422);
    }

    $info = Tenant::where('user_id', Auth::id())->findorFail($id);


    if ($info->custom_domain == 'on') {
      $validatedData = $request->validate([
        'domain' => 'required|string|max:50',
      ]);

      $domain = strtolower($request->domain);
      $input = trim($domain, '/');
      if (!preg_match('#^http(s)?://#', $input)) {
        $input = 'http://' . $input;
      }
      $urlParts = parse_url($input);
      $domain = preg_replace('/^www\./', '', $urlParts['host']);


      $check = Domain::where('domain', $domain)->where('tenant_id', '!=', $id)->first();
      if (!empty($check)) {
        $error['errors']['domain'] = 'Oops domain name already taken....!!';
        return response()->json($error, 422);
      }

      $custom_domain = Domain::where([['tenant_id', $id], ['type', 3]])->first();
      if ($custom_domain->domain != $domain) {
        $checkArecord = $this->dnscheckRecordA($domain);
        $checkCNAMErecord = $this->dnscheckRecordCNAME($domain);
        if ($checkArecord != true) {
          $error['errors']['domain'] = 'A record entered incorrectly.';
          return response()->json($error, 422);
        }

        if ($checkCNAMErecord != true) {
          $error['errors']['domain'] = 'CNAME record entered incorrectly.';
          return response()->json($error, 422);
        }
      }

      $custom_domain->domain = $domain;
      $custom_domain->save();

      return response()->json('Custom Domain Updated Successfully...!!');
    }

    $error['errors']['domain'] = 'Sorry subdomain modules not support in your plan....!!';
    return response()->json($error, 422);
  }

  //destroy subdomain
  public function destroy($id)
  {
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);
    $subdomain = Domain::where([['tenant_id', $id], ['type', 2]])->delete();

    return back();
  }

  //destroy custom domain

  public function destroyCustomdomain($id)
  {
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);
    $subdomain = Domain::where([['tenant_id', $id], ['type', 3]])->delete();
    return back();
  }

  //check is valid domain name
  public function is_valid_domain_name($domain_name)
  {
    if (filter_var(gethostbyname($domain_name), FILTER_VALIDATE_IP)) {
      return TRUE;
    }
    return false;
  }

  //check A record
  public function dnscheckRecordA($domain)
  {
    if (env('MOJODNS_AUTHORIZATION_TOKEN') != null  && env('VERIFY_IP') == true) {
      try {
        $response = Http::withHeaders(['Authorization' => env('MOJODNS_AUTHORIZATION_TOKEN')])->acceptJson()->get('https://api.mojodns.com/api/dns/' . $domain . '/A');
        $ip = $response['answerResourceRecords'][0]['ipAddress'];

        if ($ip == env('SERVER_IP')) {
          $ip = true;
        } else {
          $ip = false;
        }
      } catch (Exception $e) {
        $ip = false;
      }

      return $ip;
    }

    return true;
  }


  //check crecord name
  public function dnscheckRecordCNAME($domain)
  {
    if (env('MOJODNS_AUTHORIZATION_TOKEN') != null) {
      if (env('VERIFY_CNAME') === true) {
        try {
          $response = Http::withHeaders(['Authorization' => env('MOJODNS_AUTHORIZATION_TOKEN')])->acceptJson()->get('https://api.mojodns.com/api/dns/' . $domain . '/CNAME');
          if ($response->successful()) {
            $cname = $response['reportingNameServer'];

            if ($cname === env('CNAME_DOMAIN')) {
              $cname = true;
            } else {
              $cname = false;
            }
          } else {
            $cname = false;
          }
        } catch (Exception $e) {
          $cname = false;
        }


        return $cname;
      }
    }

    return true;
  }

  //domain transfer view
  public function transferView($id)
  {
    Session::forget('domain_transfer_info');
    $info = Tenant::where('user_id', Auth::id())->where('status', 1)->findorFail($id);
    return view('merchant.domain.transferview', compact('info'));
  }

  //send otp to the user
  public function sendOtp(Request $request, $id)
  {
    Session::forget('domain_transfer_info');
    $validatedData = $request->validate([
      'email' => 'required|email|max:50',
    ]);
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);

    $user = User::where([['email', $request->email], ['role_id', 2], ['status', 1]])->first();
    if (empty($user)) {
      $error['errors']['email'] = 'Opps invalid email...!!';
      return response()->json($error, 422);
    }

    $data = [
      'name'    => Auth::user()->name,
      'otp' => rand(10000, 30000),
      'tenant_id' => $id,
      'email' => $request->email,
      'type' => 'otp'
    ];

    Session::put('domain_transfer_info', $data);
    if (env('QUEUE_MAIL') == 'on') {
      dispatch(new SendEmailJob($data));
    } else {
      Mail::to(Auth::user()->email)->send(new DomaintransferOtp($data));
    }


    return response()->json('Successfully OTP sent to your email');
  }

  //verify otp and change the owner
  public function verifyOtp(Request $request, $id)
  {
    abort_if(!Session::has('domain_transfer_info'), 422);
    $validatedData = $request->validate([
      'otp' => 'required|numeric',
    ]);

    $info = Tenant::where('user_id', Auth::id())->findorFail($id);

    $data = Session::get('domain_transfer_info');

    if ($data['otp'] != $request->otp) {
      $error['errors']['otp'] = 'Opps invalid OTP';
      return response()->json($error, 422);
    }

    if ($data['tenant_id'] != $id) {
      $error['errors']['otp'] = 'Invalid request';
      return response()->json($error, 422);
    }

    $user = User::where([['email', $data['email']], ['role_id', 2], ['status', 1]])->first();

    if (empty($user)) {
      $error['errors']['email'] = 'Opps user not exists';
      return response()->json($error, 422);
    }
    $info->user_id = $user->id;
    $info->save();

    return response()->json('Store successfully transferred');
  }


  //developer view
  public function developerView($id)
  {
    $info = Tenant::where('user_id', Auth::id())->where('status', 1)->findorFail($id);

    $plan = json_decode($info->plan_info);
    $instruction = Option::where('key', 'developer_instruction')->first();
    $instruction = json_decode($instruction->value ?? '');
    return view('merchant.domain.dev', compact('info', 'plan', 'instruction'));
  }


  //database migration fresh
  public function migrateWithSeed($id)
  {
    $info = Tenant::where('user_id', Auth::id())->where('status', 1)->findorFail($id);
    \Config::set('app.env', 'local');
    Artisan::call('tenants:migrate-fresh --tenants=' . $id);
    Artisan::call('tenants:seed --tenants=' . $id);

    return response()->json('Database Reinstall Success');
  }

  //database new table migrate
  public function migrate($id)
  {
    \Config::set('app.env', 'local');
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);
    Artisan::call('tenants:migrate --tenants=' . $id);
    return response()->json('Database migrate success');
  }

  //cache clear for spesific tenant
  public function cacheClear($id)
  {
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);
    if (env('CACHE_DRIVER') == 'memcached' || env('CACHE_DRIVER') == 'redis') {
      \Config::set('app.env', 'local');
      Artisan::call('cache:clear --tags=' . $id);
    }
    $info->cache_version = rand(10, 20);
    $info->save();

    return response()->json('Store cache cleared');
  }

  //remove with storage directory
  public function removeStorage($id)
  {
    $info = Tenant::where('user_id', Auth::id())->findorFail($id);

    Storage::disk(env('STORAGE_TYPE'))->deleteDirectory('uploads/' . $info->uid);
    Storage::disk('public')->deleteDirectory('uploads/' . $info->uid);


    return response()->json('Storage cleared successfully');
  }

  public function login($id)
  {
    $data = Tenant::where([['user_id', Auth::id()], ['status', 1], ['will_expire', '>', now()]])->whereHas('active_domains')->with('active_domains')->findorFail($id);
    $data->auth_token = Str::random(40) . Auth::id();
    $data->save();

    $count = count($data->domains);
    $domain = '';
    if ($count > 0) {
      foreach ($data->domains as $key => $value) {
        if ($key + 1 == $count) {
          $domain = $value->domain;
        }
      }
    }



    return redirect(env('APP_PROTOCOL') . $domain . '/make-login/' . Crypt::encryptString($data->auth_token));
  }

  //login with real domain

  public function loginByDomain($id)
  {
    $domain = Domain::where('status', 1)->whereHas('tenant', function ($q) {
      return $q->where('user_id', Auth::id())->where('status', 1);
    })->findorFail($id);

    $data = Tenant::where([['user_id', Auth::id()], ['status', 1], ['will_expire', '>', now()]])->findorFail($domain->tenant_id);
    $data->auth_token = Str::random(40) . Auth::id();
    $data->save();
    return redirect(env('APP_PROTOCOL') . $domain->domain . '/make-login/' . Crypt::encryptString($data->auth_token));
  }
}
