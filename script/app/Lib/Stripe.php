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

                $customerId = self::findOrCreateCustomer($array, $secret_key);

                self::syncPaymentMethodBillingDetails($paymentMethodId, $array, $secret_key);

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

                if (!empty($array['email'])) {
                    $intentPayload['receipt_email'] = trim((string)$array['email']);
                }

                if (!empty($customerId)) {
                    $intentPayload['customer'] = $customerId;
                    try {
                        \Stripe\PaymentMethod::attach($paymentMethodId, ['customer' => $customerId]);
                    } catch (\Throwable $attachError) {
                        // Already attached to this customer (or not attachable) — safe to continue.
                    }
                }

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

            $customerId = self::findOrCreateCustomer($array, $secret_key);
            $cardReference = null;

            // Always charge THIS request's card (from tok_), never the customer default.
            // If that card is already on the customer (same fingerprint), reuse it;
            // otherwise attach the token as a new source, then authorize that card_ id.
            if (!empty($customerId)) {
                try {
                    \Stripe\Stripe::setApiKey($secret_key);

                    $tokenObj = \Stripe\Token::retrieve($token);
                    $fingerprint = $tokenObj->card->fingerprint ?? null;

                    if (!empty($fingerprint)) {
                        $sources = \Stripe\Customer::allSources($customerId, [
                            'object' => 'card',
                            'limit' => 100,
                        ]);
                        foreach ($sources->data as $existingCard) {
                            if (($existingCard->fingerprint ?? null) === $fingerprint) {
                                $cardReference = $existingCard->id;
                                break;
                            }
                        }
                    }

                    if (empty($cardReference)) {
                        $card = \Stripe\Customer::createSource($customerId, [
                            'source' => $token,
                        ]);
                        $cardReference = $card->id ?? null;
                    }

                    if (empty($cardReference)) {
                        $data['payment_status'] = 0;
                        $data['error'] = 'Unable to link the provided card to the Stripe customer.';
                        return $data;
                    }

                    self::syncCardSourceBillingDetails($customerId, $cardReference, $array, $secret_key);
                } catch (\Throwable $e) {
                    $data['payment_status'] = 0;
                    $data['error'] = $e->getMessage();
                    return $data;
                }
            }

            $applicarionfee = ($application_fee_amount + $credit_card_fee)*100;

            $currency_obj = new Currency($currency);

            $applicarionfee = new Money($applicarionfee, $currency_obj);

            $authorizePayload = [
                'amount' => $totalAmount,
                'currency' =>  $currency_obj,
                // Keep customer contact available in Stripe charge object/receipt.
                'email' => $array['email'] ?? null,
                'receiptEmail' => $array['email'] ?? null,
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

            if (!empty($customerId) && !empty($cardReference)) {
                // Explicit card from this token (or matching fingerprint) — not default source.
                $authorizePayload['customerReference'] = $customerId;
                $authorizePayload['cardReference'] = $cardReference;
            } else {
                // No customer: one-off charge on this token only.
                $authorizePayload['token'] = $token;
            }

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
            if (isset($response)) {
                $data['error'] = method_exists($response, 'getMessage') ? $response->getMessage() : 'Stripe payment failed';
                $arr_body = method_exists($response, 'getData') ? $response->getData() : null;
                if (is_array($arr_body)) {
                    $data['transaction_log'] = $arr_body;
                    $data['error_type'] = $arr_body['error']['type'] ?? null;
                    $data['error_code'] = $arr_body['error']['code'] ?? null;
                    if (!empty($arr_body['error']['message'])) {
                        $data['error'] = $arr_body['error']['message'];
                    }
                }
            } else {
                $data['error'] = 'Missing stripeToken or paymentMethodId';
            }
        }
        return $data;
    }

    /**
     * Find an existing Stripe Customer by email, or create one.
     * Returns cus_... id, or null if email is missing / Stripe call fails.
     */
    protected static function findOrCreateCustomer(array $array, string $secret_key): ?string
    {
        $email = trim((string)($array['email'] ?? ''));
        if ($email === '') {
            return null;
        }

        try {
            \Stripe\Stripe::setApiKey($secret_key);

            $customerData = [
                'email' => $email,
                'name'  => $array['name'] ?? null,
                'phone' => $array['phone'] ?? null,
                'address' => self::buildStripeAddress($array),
                'metadata' => [
                    'customer_name'  => (string)($array['name'] ?? ''),
                    'customer_phone' => (string)($array['phone'] ?? ''),
                    'customer_email' => $email,
                ],
            ];

            $existing = \Stripe\Customer::all([
                'email' => $email,
                'limit' => 1,
            ]);

            if (!empty($existing->data[0])) {
                $customer = \Stripe\Customer::update($existing->data[0]->id, $customerData);
            } else {
                $customer = \Stripe\Customer::create($customerData);
            }

            return $customer->id ?? null;
        } catch (\Throwable $e) {
            // Do not block checkout if customer sync fails; charge can still proceed.
            return null;
        }
    }

    /**
     * Stripe expects ISO 3166-1 alpha-2 country codes (e.g. US, not USA).
     */
    protected static function normalizeStripeCountry(?string $country): ?string
    {
        $country = trim((string)$country);
        if ($country === '') {
            return null;
        }

        $upper = strtoupper($country);
        $map = [
            'USA' => 'US',
            'UNITED STATES' => 'US',
            'UNITED STATES OF AMERICA' => 'US',
            'UK' => 'GB',
            'UNITED KINGDOM' => 'GB',
            'GREAT BRITAIN' => 'GB',
        ];

        if (isset($map[$upper])) {
            return $map[$upper];
        }

        if (strlen($upper) === 2) {
            return $upper;
        }

        return $country;
    }

    protected static function buildStripeAddress(array $array): array
    {
        return array_filter([
            'line1'       => $array['address'] ?? null,
            'city'        => $array['city'] ?? null,
            'state'       => $array['state'] ?? null,
            'country'     => self::normalizeStripeCountry($array['country'] ?? null),
            'postal_code' => $array['zip'] ?? null,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });
    }

    protected static function buildPaymentMethodBillingDetails(array $array): array
    {
        $address = self::buildStripeAddress($array);

        return array_filter([
            'name'  => $array['name'] ?? null,
            'email' => !empty($array['email']) ? trim((string)$array['email']) : null,
            'phone' => $array['phone'] ?? null,
            'address' => !empty($address) ? $address : null,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Populate Payment Method owner / email / address in Stripe Dashboard.
     */
    protected static function syncPaymentMethodBillingDetails(string $paymentMethodId, array $array, string $secret_key): void
    {
        $billingDetails = self::buildPaymentMethodBillingDetails($array);
        if (empty($billingDetails)) {
            return;
        }

        try {
            \Stripe\Stripe::setApiKey($secret_key);
            \Stripe\PaymentMethod::update($paymentMethodId, [
                'billing_details' => $billingDetails,
            ]);
        } catch (\Throwable $e) {
            // Non-fatal: payment can still proceed without billing sync.
        }
    }

    /**
     * Populate legacy card source owner / address on the customer (token flow).
     */
    protected static function syncCardSourceBillingDetails(string $customerId, string $cardId, array $array, string $secret_key): void
    {
        $address = self::buildStripeAddress($array);
        $payload = array_filter([
            'name'            => $array['name'] ?? null,
            'address_line1'   => $address['line1'] ?? null,
            'address_city'    => $address['city'] ?? null,
            'address_state'   => $address['state'] ?? null,
            'address_zip'     => $address['postal_code'] ?? null,
            'address_country' => $address['country'] ?? null,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        if (empty($payload)) {
            return;
        }

        try {
            \Stripe\Stripe::setApiKey($secret_key);
            \Stripe\Customer::updateSource($customerId, $cardId, $payload);
        } catch (\Throwable $e) {
            // Non-fatal: payment can still proceed without billing sync.
        }
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
