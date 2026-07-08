<?php
namespace App\Lib;
use Omnipay\Omnipay;
use Omnipay\Stripe\Message\Transfers;
use Session;
use Illuminate\Http\Request;
use Money\Currency;
use Money\Money;
use App\Models\Option;


class Stripe {
    public static function redirect_if_payment_success()
    {
         if(Session::has('fund_callback'))
         {
            return url(Session::get('fund_callback')['success_url']);
        }else{
            return url('partner/payment/success');
        }
    }

    public static function redirect_if_payment_faild()
    {
        if(Session::has('fund_callback'))
        {
            return url(Session::get('fund_callback')['cancel_url']);
        }else{
            return url('partner/payment/failed');
        }
    }

    public function view(){

        if(Session::has('stripe_credentials')){
            $Info=Session::get('stripe_credentials');
            if (tenant() != null) {
              return view(baseview('payments/stripe'),compact('Info'));
            }
           return view('merchant.plan.payment.stripe',compact('Info'));
        }
        abort(404);
    }

    public static function fallback()
    {
       return url('partner/payment/stripe');
    }

    public static function make_payment($array)
    {
        $publishable_key=($array['test_mode'] == 1) ? $array['test_publishable_key'] : $array['publishable_key'];//$array['publishable_key'];
        $secret_key=($array['test_mode'] == 1) ? $array['test_secret_key'] : $array['secret_key']; //$array['secret_key'];
        $currency=$array['currency'];
        $email=$array['email'];
        $amount=$array['amount'];
        $totalAmount=$array['pay_amount'];
        $name=$array['name'];
        $billName=$array['billName'];
        $test_mode=$array['test_mode'];
        $data['publishable_key']=$publishable_key;
        $data['secret_key']=$secret_key;
        $data['payment_mode']='stripe';
        $data['amount']=$totalAmount;
        $data['test_mode']=$test_mode;

        $data['charge']=$array['charge'];
        $data['main_amount']=$array['amount'];
        $data['getway_id']=$array['getway_id'];
        $data['is_fallback']=$array['is_fallback'] ?? 0;
        $data['payment_type']=$array['payment_type'] ?? '';
        $data['currency']=$array['currency'];


        Session::put('stripe_credentials',$data);

        if (tenant() != null) {
            return redirect()->route('order.stripe.view');
        }
        return redirect()->route('stripe.view');
    }

    public function status(Request $request)
    {
        abort_if(!Session::has('stripe_credentials'), 404);
        $credentials=Session::get('stripe_credentials');

        $stripe = Omnipay::create('Stripe');
        $token = $request->stripeToken;
        $gateway = $credentials['publishable_key'];
        $secret_key = $credentials['secret_key'];
        $main_amount = $credentials['amount'];

        $stripe->setApiKey($secret_key);

        if($token){
            $response = $stripe->purchase([
                'amount' => $main_amount,
                'currency' => $credentials['currency'],
                'token' => $token,
            ])->send();
        } else {
            $data['payment_status'] = 0;
            return $data;
        }


        if ($response->isSuccessful()) {
            $arr_body = $response->getData();
            $data['payment_id'] = $arr_body['id'];
            $data['payment_method'] = "stripe";
            $data['getway_id'] = $credentials['getway_id'];
            $data['payment_type'] = $credentials['payment_type'];

            $data['amount'] = $credentials['main_amount'];
            $data['charge'] = $credentials['charge'];
            $data['status'] = 1;
            $data['payment_status'] = 1;
            $data['is_fallback'] = $credentials['is_fallback'];
            Session::put('payment_info',$data);
            Session::forget('stripe_credentials');
            return redirect(Stripe::redirect_if_payment_success());
        }
        else{
            $data['payment_status'] = 0;
            Session::put('payment_info',$data);
           Session::forget('stripe_credentials');
           return redirect(Stripe::redirect_if_payment_faild());
        }
    }
    public static function isfraud($creds){
        $payment_id = $creds['payment_id'];
        $secret_key = $creds['secret_key'];

        try {
        $stripe = new \Stripe\StripeClient($secret_key);

        $response = $stripe->charges->retrieve(
            $payment_id,
            [],
        );
        return $response->status === "succeeded" ? 1 : 0;
        } catch (\Throwable $th) {
            return 0;
        }

    }

    public static function charge_payment($array)
    {
        $publishable_key= ($array['test_mode'] == 1) ? $array['test_publishable_key'] : $array['publishable_key'];
        $secret_key=($array['test_mode'] == 1) ? $array['test_secret_key'] : $array['secret_key'];
        $currency=$array['currency'];
        $amount=$array['amount'];
        $totalAmount=$array['pay_amount'];
        $test_mode=$array['test_mode'];
        $data['publishable_key']=$publishable_key;
        $data['secret_key']=$secret_key;
        $data['payment_mode']='stripe';
        $data['amount']=$totalAmount;
        $data['test_mode']=$test_mode;
        $application_fee_amount = $array['application_fee_amount'];
        $credit_card_fee = $array['credit_card_fee'];
        

        $paymentMethodId = $array['paymentMethodId'] ?? null;
        $token = $array['stripeToken'] ?? null;

        if (!empty($paymentMethodId)) {
            try {
                \Stripe\Stripe::setApiKey($secret_key);

                $applicarionfee = (int) round(($application_fee_amount + $credit_card_fee) * 100);
                $intentPayload = [
                    'amount' => (int) round(((float)$totalAmount) * 100),
                    'currency' => strtolower($currency),
                    'payment_method' => $paymentMethodId,
                    'confirm' => true,
                    'capture_method' => 'automatic',
                    'description' => $array['billName'] ?? 'Boostr Sale',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'metadata' => [
                        'customer_name' => (string)($array['name'] ?? ''),
                        'customer_phone' => (string)($array['phone'] ?? ''),
                        'customer_email' => (string)($array['email'] ?? ''),
                        'billing_address' => (string)($array['address'] ?? ''),
                        'billing_city' => (string)($array['city'] ?? ''),
                        'billing_state' => (string)($array['state'] ?? ''),
                        'billing_country' => (string)($array['country'] ?? ''),
                        'billing_zip' => (string)($array['zip'] ?? ''),
                    ],
                    'expand' => ['latest_charge'],
                ];

                if (!isset($array['pos']) && !empty($array['stripe_account_id'])) {
                    $intentPayload['transfer_data'] = ['destination' => $array['stripe_account_id']];
                    $intentPayload['on_behalf_of'] = $array['stripe_account_id'];
                    $intentPayload['application_fee_amount'] = $applicarionfee;
                }

                $intent = \Stripe\PaymentIntent::create($intentPayload);
                $latestCharge = is_object($intent->latest_charge) ? $intent->latest_charge : null;
                $riskLevel = $latestCharge->outcome->risk_level ?? 'normal';

                $isCaptured = ($intent->status ?? '') === 'succeeded';
                $data['payment_id'] = $intent->id;
                $data['transaction_log'] = $intent->toArray();
                $data['payment_method'] = "stripe";
                $data['getway_id'] = $array['getway_id'];
                $data['payment_type'] = $array['payment_type'] ?? '';
                $data['charge'] = $array['charge'];
                $data['risk_level'] = $riskLevel;
                $data['status'] = 1;
                $data['payment_status'] = $isCaptured ? 1 : 4;
                return $data;
            } catch (\Throwable $e) {
                $data['payment_status'] = 0;
                $data['error'] = $e->getMessage();
                return $data;
            }
        }

        $stripe = Omnipay::create('Stripe');
        $stripe->setApiKey($secret_key);
        if($token){

            $applicarionfee = ($application_fee_amount + $credit_card_fee)*100;

            $currency_obj = new Currency($currency);

            $applicarionfee = new Money($applicarionfee, $currency_obj);

            $authorizePayload = [
                'amount' => $totalAmount,
                'currency' =>  $currency_obj,
                'token' => $token,
                // Keep customer contact available in Stripe charge object/receipt.
                'email' => $array['email'] ?? null,
                'description' => $array['billName'] ?? 'Boostr Sale',
                'metadata' => [
                    'customer_name' => (string)($array['name'] ?? ''),
                    'customer_phone' => (string)($array['phone'] ?? ''),
                    'customer_email' => (string)($array['email'] ?? ''),
                    'billing_address' => (string)($array['address'] ?? ''),
                    'billing_city' => (string)($array['city'] ?? ''),
                    'billing_state' => (string)($array['state'] ?? ''),
                    'billing_country' => (string)($array['country'] ?? ''),
                    'billing_zip' => (string)($array['zip'] ?? ''),
                ],
            ];

            if( isset($array['pos']) ){
                $response = $stripe->authorize($authorizePayload)->send();
            }else{
                $response = $stripe->authorize(array_merge($authorizePayload, [
                    'onBehalfOf' => $array['stripe_account_id'],
                    'destination'   => $array['stripe_account_id'],
                    'applicationFee'=> $applicarionfee,
                ]))->send();
            }
            
        }
        if ($response->isSuccessful()) {
            $arr_body = $response->getData();

           // dd($arr_body);

            // $transaction = $stripe->transfer(array(
            //     'amount'        => $totalAmount,
            //     'currency'      => $currency,
            //     'sourceTransaction' => $arr_body['id'],
            //     'onBehalfOf' => $array['stripe_account_id'],
            //     'destination'   => $array['stripe_account_id'],
            // ));
            // $response1 = $transaction->send();

          //  dd($response,$response1);

            $data['payment_id'] = $arr_body['id'];
            $data['transaction_log'] = $arr_body;
            $data['payment_method'] = "stripe";
            $data['getway_id'] = $array['getway_id'];
            $data['payment_type'] = $array['payment_type']??'';
            $data['charge'] = $array['charge'];
            $data['risk_level'] = $arr_body['outcome']['risk_level'];
            $data['status'] = 1;
            $data['payment_status'] = 4;

        }else{
            $data['payment_status'] = 0;
        }
        return $data;
    }


    public static function capture_payment($array)
    {
        $publishable_key= ($array['test_mode'] == 1) ? $array['test_publishable_key'] : $array['publishable_key'];
        $secret_key=($array['test_mode'] == 1) ? $array['test_secret_key'] : $array['secret_key'];
        $currency=$array['currency'];
        $totalAmount=$array['amount'];
        $test_mode=$array['test_mode'];
        $data['publishable_key']=$publishable_key;
        $data['secret_key']=$secret_key;
        $data['payment_mode']='stripe';
        $data['amount']=$totalAmount;
        $data['test_mode']=$test_mode;
       // $application_fee_amount = $array['application_fee_amount'];

        if (!empty($array['transaction_id']) && strpos($array['transaction_id'], 'pi_') === 0) {
            try {
                \Stripe\Stripe::setApiKey($secret_key);
                $intent = \Stripe\PaymentIntent::capture($array['transaction_id']);
                $data['payment_id'] = $intent->id;
                $data['transaction_log'] = $intent->toArray();
                $data['payment_method'] = "stripe";
                $data['status'] = 1;
                $data['payment_status'] = 1;
                return $data;
            } catch (\Throwable $e) {
                $data['payment_status'] = 0;
                $data['error'] = $e->getMessage();
                return $data;
            }
        }

        $stripe = Omnipay::create('Stripe');
        $stripe->setApiKey($secret_key);
        $transaction = $stripe->capture();
        $transaction->setTransactionReference($array['transaction_id']);
        $response = $transaction->send();

        if ($response->isSuccessful()) {
            $arr_body = $response->getData();

            // $transaction = $stripe->transfer(array(
            //     'amount'        => $totalAmount,
            //     'currency'      => $currency,
            //     'sourceTransaction' => $arr_body['id'],
            //     'onBehalfOf' => $array['stripe_account_id'],
            //     'destination'   => $array['stripe_account_id'],
            //     'applicationFee'=>$application_fee_amount
            // ));
            // $response1 = $transaction->send();

            $data['payment_id'] = $arr_body['id'];
            $data['transaction_log'] = $arr_body;
            $data['payment_method'] = "stripe";
            $data['status'] = 1;
            $data['payment_status'] = 1;

        }else{
            $data['payment_status'] = 0;
        }
        return $data;
    }


    public static function refund_payment($array)
    {
        $publishable_key= ($array['test_mode'] == 1) ? $array['test_publishable_key'] : $array['publishable_key'];
        $secret_key=($array['test_mode'] == 1) ? $array['test_secret_key'] : $array['secret_key'];
        $currency=$array['currency'];
        $totalAmount=$array['amount'];
        $test_mode=$array['test_mode'];

        $data['publishable_key']=$publishable_key;
        $data['secret_key']=$secret_key;
        $data['payment_mode']='stripe';
        $data['amount']=$totalAmount;
        $data['test_mode']=$test_mode;

       // $application_fee_amount = $array['application_fee_amount'];


       $totalAmount = $totalAmount - $array['application_fee_amount'] -  $array['card_fee_amount'];


       if($array['refund_application_fee']){
         $totalAmount = $totalAmount + $array['application_fee_amount'];
       }


       if($array['refund_card_fee']){
         $totalAmount = $totalAmount + $array['card_fee_amount'];
       }



        $stripe = Omnipay::create('Stripe');
        $stripe->setApiKey($secret_key);


         $transaction = $stripe->refund(array(
                'amount'                   => $totalAmount,
                'transactionReference'     => $array['transaction_id'],
            ));

         if($array['refund_application_fee'] == true || $array['refund_card_fee'] == true){
            $transaction->setRefundApplicationFee(true)->setReverseTransfer(true);
         }

           $response = $transaction->send();
           if ($response->isSuccessful()) {

            $arr_body = $response->getData();
            $data['payment_id'] = $arr_body['id'];
            $data['transaction_log'] = $arr_body;
            $data['payment_method'] = "stripe";
            $data['status'] = 1;
            $data['payment_status'] = 1;

            }else{
                $data['payment_status'] = 0;
            }

        return $data;
    }



}


?>
