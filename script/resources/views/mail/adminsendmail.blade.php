@component('mail::message')

# Order No : {{$data['data']->invoice_no}}

@php

if($data['message'] == 'Order captured'){
    $order_status = $data['message'];
}elseif($data['message'] == 'Order Cancel & Refund'){
    $order_status = $data['message'];
}elseif($data['message'] == 'You have received a new order'){
    $order_status = $data['message'];
}else{
    if($data['data']['status_id'] == 1){
        $order_status = 'Order Complete';
    }elseif($data['data']['status_id'] == 2){
        $order_status = 'Order Cancel';
    }elseif($data['data']['status_id'] == 3){
        $order_status = 'Order Pending';
    }
}



@endphp

{{ $order_status }} <br>
<a href="{{$data['link']}}"># View Order</a>

Thanks,<br>
{{ config('app.name') }}
@endcomponent