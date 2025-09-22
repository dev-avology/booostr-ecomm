<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Invoice Print') }}</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/invoice.css') }}">
</head>
<body>


{{-- invoice area start --}}
<section>
    <div class="invoice-area" id="print_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="invoice-main-area">
                        <div class="invoice-bill-form-main">
                            <div class="row">
                                <div class="col-lg-6">
                                    @php
                                        $info = get_option('invoice_data',true) ?? '';
                                        $club_info = tenant_club_info();
                                        $shippingwithinfo = json_decode($order->shippingwithinfo->info,true);
                                    @endphp
                                       
                                    <div class="invoice-bill-form">
                                        <h5><strong>{{ __('BILL FROM:') }}</strong></h5>
                                        <div class="store-name">
                                            <h2>{{ $info->store_legal_name ?? '' }}</h2>
                                            <p>{{ $info->store_legal_house ?? '' }}, {{ $info->store_legal_address ?? '' }} <br>{{ $info->store_legal_city ?? '' }}</p>
                                            <p>Postal Code: {{ $info->post_code ?? '' }}</p>
                                            <p>{{ $info->country ?? '' }}</p>
                                        </div>
                                        <div class="store-email">
                                            <p>{{ $info->store_legal_email ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!--<div class="shopify-logo">
                                        <img class="img-fluid" src="{{ asset('uploads/'.tenant('uid').'/logo.png') }}" alt="">
                                    </div>-->
									@if($order->ordermeta->key == 'orderinfo')
									  <div class="invoice-bill-form">
                                        <h5><strong>{{ __('BILL TO:') }}</strong></h5>
                                        <div class="store-name">
            						      @php $orderinfo = json_decode($order->ordermeta->value) @endphp   
                                            <h2>{{ $orderinfo->name ?? '' }}</h2>
                                            <p>{{ $orderinfo->billing->address ?? '' }}<br>{{ $orderinfo->billing->city ?? '' }}, {{ $orderinfo->billing->state ?? '' }}</p>
                                            <p>Postal Code: {{ $orderinfo->billing->post_code ?? '' }}</p>
                                            <p>{{ $orderinfo->billing->country ?? '' }}</p>
                                        </div>
                                        <div class="store-email">
                                            <p>{{ $orderinfo->email ?? '' }}</p>
                                        </div>
                                      </div>
									@endif
                                </div>
                            </div>
                        </div>
                        <div class="invoice-bill-to">
                            <div class="row">
                                <div class="col-lg-6">
                                    @if($order_type !== 'Digital')
                                    <div class="invoice-bill-form">
                                        <h5><strong>SHIP TO:</strong></h5>
                                     
										<div class="store-name">
                                            <h2>{{ $orderinfo->shipping->name ?? '' }}</h2>
                                            <p>{{ $orderinfo->shipping->address ?? '' }}<br>{{ $orderinfo->shipping->city ?? '' }}, {{ $orderinfo->shipping->state ?? '' }}</p>
                                            <p>Postal Code: {{ $orderinfo->shipping->post_code ?? '' }}</p>
                                            <p>{{ $orderinfo->shipping->country ?? '' }}</p>
                                        </div>	
										<div class="store-email">
                                            <p>{{ $order->user->email ?? '' }}</p>
                                        </div>
                                    </div>
                                     @endif

                                </div>
                                <div class="col-lg-6">
                                    <div class="invoice-table-area">
                                        <table class="table">
                                            <tr>
                                                <td>INVOICE#</td>
                                                <td style="text-align: right;">{{ $order->invoice_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>INVOICE DATE</td>
                                                <td style="text-align: right;">{{ Carbon\Carbon::parse($order->created_at)->isoFormat('ll') }}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL AMOUNT</th>
                                                <th style="text-align: right;">{{currency_formate($order->total) }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-item-area">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th style="text-align: right;">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php $subtotal = 0 ; @endphp
                                    @foreach ($order->orderitems as $item)
                                    <tr>
                                        <td>{{ $item->term->title }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{currency_formate($item->amount) }}</td>
                                        <td style="text-align: right;">{{ currency_formate($item->amount * $item->qty) }}</td>
                                    </tr>
                                    @php $subtotal = $subtotal + $item->amount*$item->qty; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="invoice-note-area">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="note-section">
                                        <h4>NOTES/MEMO</h4>
                                        <p>{{ json_decode($order->ordermeta->value)->note ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="invoice-table-area">
                                        <table class="table">
                                            <tr>
                                                <td>SUBTOTAL</td>
                                                <td style="text-align: right;">{{ currency_formate($subtotal) }}</td>
                                            </tr>
                                            @if($order_type !== 'Digital')
                                            @php  $shipping_price = $order->shippingwithinfo->shipping_price ?? 0; @endphp
                                            <tr>
                                                <td>Shipping Fee</td>
                                                <td style="text-align: right;">{{ currency_formate($shipping_price) }}</td>
                                            </tr>
                                             @endif
                                            <tr>
                                                <td>TAX</td>
                                                <td style="text-align: right;">{{ currency_formate($order->tax) }}</td>
                                            </tr>
                                             
                                            @if(isset($shippingwithinfo['cover_fee']) && $shippingwithinfo['cover_fee'] !== '0')
                                            <tr>
                                                <td>Fee Covered</td>
                                                <td style="text-align: right;">{{ currency_formate($shippingwithinfo['cover_fee']) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>TOTAL</th>
                                                <th style="text-align: right;">{{ currency_formate($order->total) }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-footer-area">
                            <div class="text-center">
                                <div class="invoice-footer-content">
                                    <img src="{{ asset('uploads/'.tenant('uid').'/logo.png') }}" alt="">
                                    <p><a href="{{$club_info['club_url'] }}" target="_blank">{{ $info->store_legal_name ?? '' }}</a> store -  Powered by <a href="{{ explode('co/',$club_info['club_url'])[0] }}co" target="_blank">Booostr</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- invoice area end --}}
<input type="hidden" id="bootstrap_url" value="{{ asset('admin/assets/css/bootstrap.min.css') }}">
<input type="hidden" id="style_url" value="{{ asset('admin/assets/css/invoice.css') }}">

<script src="{{ asset('admin/js/vendor/jquery-3.5.1.min.js') }}"></script>
<script>
    $(window).on('load',function () {
    var bootstrap_url = $('#bootstrap_url').val();
    var style_url = $('#style_url').val();
    var contents = $("#print_area").html();
    var frame1 = $('<iframe />');
    frame1[0].name = "frame1";
    frame1.css({ "position": "absolute", "top": "-1000000px" });
    $("body").append(frame1);
    var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
    frameDoc.document.open();
    //Create a new HTML document.
    frameDoc.document.write('<html><head><title>Invoice Print</title>');
    frameDoc.document.write('</head><body>');
    //Append the external CSS file.
    frameDoc.document.write('<link href="'+bootstrap_url+'" rel="stylesheet"/>');
    frameDoc.document.write('<link href="'+style_url+'" rel="stylesheet"/>');
    //Append the DIV contents.
    frameDoc.document.write(contents);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();
    setTimeout(function () {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        frame1.remove();
    }, 500);
});
</script>

</body>
</html>
