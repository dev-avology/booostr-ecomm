<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use App\Models\Category;

class PosUserEmail extends Mailable
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
        $data = $this->data;
        $subject = $this->subject;
        
        $currency=get_option('currency_data',true);
        $invoice_info=get_option('invoice_data',true);

        $orderId = $data['orderId'];
        
        $order = Order::with('orderstatus','orderlasttrans','orderitems','getway','user','shippingwithinfo','ordermeta','getway','schedule')->findOrFail($orderId);

        $data['data']=$order;
        $data['currency_info']=$currency;
        $data['invoice_data']=$invoice_info;
        

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
        
        $data['order_type'] = $order_type;

        $this->data = $data;

        $mailable = $this->markdown('mail.posusermail')->subject($subject)->with('data', $data);

        if (function_exists('apply_store_receipt_mail_identity')) {
            apply_store_receipt_mail_identity($mailable);
        }

        return $mailable;
    }
}
