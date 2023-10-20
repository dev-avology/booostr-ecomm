<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Adminsendmail extends Mailable
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
        $data = $this->data;

        if ($this->data['type'] == 'tenant_order_notification') {
         $currency=$this->data['currency_info'];
         
         $ordermeta=json_decode($data['data']->ordermeta->value ?? '');
         $invoice_info=$this->data['invoice_data'];

         return $this->subject($invoice_info->invoice_subject ?? 'Order Mail')
         ->view('mail.seller.customerorder')->with(['order'=>$data['data'],'currency'=>$currency,'ordermeta'=>$ordermeta,'invoice_info'=>$invoice_info]);
        }
        elseif ($this->data['type'] == 'order_recived'){
           
            \Config::set('app.name', ucfirst($data['data']['tenantid']));
            $newData = $this->data['data'];
            $newData['club_name'] = $data['invoice_data']->store_legal_name;

            if($this->data['message'] == 'Order Cancel & Refund'){
                if(!empty($data['data']->orderlasttrans->value)){
                    $jsonString = $data['data']->orderlasttrans->value;
                    $decodedJsonLastTrans = json_decode($jsonString, true);
                    $timestamp = $decodedJsonLastTrans['created'] ?? '';
                    $createdAt = \Carbon\Carbon::createFromTimestamp($timestamp)->toDateTimeString();
                    $amountRefunded = $decodedJsonLastTrans['amount_refunded'] ?? '';
    
                    $newData['refund_amount'] = $amountRefunded;
                    $newData['cancel_date'] = $createdAt;
                }
            }

            $new_array = json_decode($newData, true);
		    $orderno = $new_array['invoice_no'];

            return $this->markdown('mail.adminsendmail')->subject('['.ucfirst($this->data['tenantid']).'] '.$this->data['message'].' ('.$orderno.')')->with('data', $new_array);
        }
    }
}
