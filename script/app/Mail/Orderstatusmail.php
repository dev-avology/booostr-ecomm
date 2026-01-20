<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use App\Models\Category;



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
        $amount_refunded = $orderlasttrans->amount_refunded ?? 0;
        $lastdigit = $orderlasttrans->source->last4 ?? null;
        $card_number = str_pad($lastdigit, 16, "*", STR_PAD_LEFT);


        $ordermeta=json_decode($data['data']->ordermeta->value ?? '');
        $billing_name = $ordermeta->name;
        $billing_email = $ordermeta->email;
        $billing_phone = $ordermeta->phone;
        $invoice_info=$this->data['invoice_data'];

        $product_type = Category::where('type', 'product_type')->select('id','slug', 'name')->orderBy('id', 'ASC')->get();
       
        $selected_product_type = [];

        foreach ($data['data']->orderitems ?? [] as $row){
        $p_types = $product_type->pluck('id')->all();
        
        $selected_product_type[] = $row->term->termcategories
            ->pluck('category_id')
            ->intersect($p_types)
            ->values()
            ->all();
        }
     
        $selected_product_type = Arr::flatten($selected_product_type);

        $count = count($selected_product_type);

        $order_type = match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $product_type->firstWhere('id', $selected_product_type[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };


        return $this->from($data['from'])
        ->subject($subject)
        ->view('mail.seller.customerorder')->with(['order'=>$data['data'],'currency'=>$currency,'ordermeta'=>$ordermeta,'invoice_info'=>$invoice_info,'card_number'=>$card_number,'amount_refunded'=>$amount_refunded,'order_type'=>$order_type]);
    }
}
