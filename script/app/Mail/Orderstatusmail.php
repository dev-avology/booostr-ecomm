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
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data=$this->data;
        $subject=$this->subject;

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
        
         return $this->from($data['from'])
         ->subject($subject)
         ->view('mail.seller.customerorder')->with(['order'=>$data['data'],'currency'=>$currency,'ordermeta'=>$ordermeta,'invoice_info'=>$invoice_info,'card_number'=>$card_number]);
        }
        elseif ($this->data['type'] == 'order_recived'){
            \Config::set('app.name', ucfirst($data['data']['tenantid']));
            return $this->markdown('mail.orderrecived')->from($data['from'])->subject('['.ucfirst($data['data']['tenantid']).'] You have received a new order ('.$data['data']['orderno'].')')->with('data',$data);
        }
        

    }
}
