<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Orderstatusmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data=$this->data;

        if ($this->data['type'] == 'tenant_order_notification') {
         $currency=$this->data['currency_info'];
         
         $orderlasttrans=json_decode($data['data']->orderlasttrans->value ?? '');

         $lastdigit = $orderlasttrans->source->last4;

         $card_number = str_pad($lastdigit, 16, "*", STR_PAD_LEFT);
         $ordermeta=json_decode($data['data']->ordermeta->value ?? '');
         $billing_name = $ordermeta->name;
         $billing_email = $ordermeta->email;
         $billing_phone = $ordermeta->phone;
         $invoice_info=$this->data['invoice_data'];

         $billing_address = $ordermeta->billing;

         $billing_add= $billing_address->address;
         $billing_city= $billing_address->city;
         $billing_state= $billing_address->state;
         $billing_country= $billing_address->country;
         $billing_post_code= $billing_address->post_code;

         $new_billing_address = $billing_name.','.$billing_email.','.$billing_phone.','.$billing_add.','.$billing_city.','.$billing_state.','.$billing_country.','.$billing_post_code;

         $shippping_address = $ordermeta->shipping;
         $shippping_name = $ordermeta->shipping->name;
         $shippping_phone = $ordermeta->shipping->phone;
         $shippping_address = $ordermeta->shipping->address;
         $shippping_city = $ordermeta->shipping->city;
         $shippping_state = $ordermeta->shipping->state;
         $shippping_country = $ordermeta->shipping->country;
         $shippping_post_code = $ordermeta->shipping->post_code;

         $new_shiiping_address = $shippping_name.','.$shippping_phone.','.$shippping_address.','.$shippping_city.','.$shippping_state.',' .$shippping_country.','.$shippping_post_code;

        //$data['billing_address'] =  $new_billing_address;
       // $data['shipping_address'] = $new_shiiping_address;
       // $data['card_number'] = $card_number;

        
         return $this->from($data['from'])
         ->subject($invoice_info->invoice_subject ?? 'Order Mail')
         ->view('mail.seller.customerorder')->with(['order'=>$data['data'],'currency'=>$currency,'ordermeta'=>$ordermeta,'invoice_info'=>$invoice_info]);
        }
        elseif ($this->data['type'] == 'order_recived'){
            \Config::set('app.name', ucfirst($data['data']['tenantid']));
            return $this->markdown('mail.orderrecived')->from($data['from'])->subject('['.ucfirst($data['data']['tenantid']).'] You have received a new order ('.$data['data']['orderno'].')')->with('data',$data);
        }
        

    }
}
