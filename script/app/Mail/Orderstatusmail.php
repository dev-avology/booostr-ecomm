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

        $currency=$this->data['currency_info'];
        $orderlasttrans=json_decode($data['data']->orderlasttrans->value ?? '');
        $amount_refunded = $orderlasttrans->amount_refunded;
        $lastdigit = $orderlasttrans->source->last4;
        $card_number = str_pad($lastdigit, 16, "*", STR_PAD_LEFT);

        $ordermeta=json_decode($data['data']->ordermeta->value ?? '');
        $billing_name = $ordermeta->name;
        $billing_email = $ordermeta->email;
        $billing_phone = $ordermeta->phone;
        $invoice_info=$this->data['invoice_data'];

        return $this->from($data['from'])
        ->subject($subject)
        ->view('mail.seller.customerorder')->with(['order'=>$data['data'],'currency'=>$currency,'ordermeta'=>$ordermeta,'invoice_info'=>$invoice_info,'card_number'=>$card_number,'amount_refunded'=>$amount_refunded]);
    }
}
