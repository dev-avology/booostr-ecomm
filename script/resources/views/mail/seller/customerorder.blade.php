<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            padding: 0;
            margin: 0;
        }

        .border-style:after {
            position: absolute;
            content: '';
            border-bottom: 1px solid #e5e5e5;
            width: 94%;
            transform: translateX(-50%);
            left: 50%;
        }

        .border-style {
            position: relative;
        }

        .spac-btm {
            padding-bottom: 24px;
        }

        .spac-top {
            padding-top: 24px;
        }

        tr.br-none:after {
            border: 0;
        }

        .add-shipping-color p {
            color: #3c3c3c;
            line-height: 1.6;
        }

        .add-shipping-color a {
            color: #08bff3;
            text-decoration: none;
        }

        #click_to_login {
            color: #08bff3 !important;
            text-decoration: underline !important;
        }
    </style>
</head>


<body style="background-color: #f4f6f9; margin: 0; padding: 24px 12px;">
    <div class="table-wrapper" style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);">

        <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff; border-collapse: collapse;">
            <tbody>
                @php
                    $clubLogo = tenant_club_logo();
                    $clubDisplayName = $invoice_info->store_legal_name ?? (tenant_club_info()['club_name'] ?? '');
                @endphp
                <tr style="background-color: #ffffff; width: 100%; border-bottom: 1px solid #e5e5e5;">
                    <td style="padding: 20px 24px; vertical-align: middle;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                            <tr>
                                <td align="left" style="vertical-align: middle;">
                                    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                        <tr>
                                            @if (!empty($clubLogo))
                                                <td style="vertical-align: middle;">
                                                    <img src="{{ $clubLogo }}"
                                                        width="90"
                                                        height="90"
                                                        alt="{{ $clubDisplayName }}"
                                                        style="border-radius: 50%; display: block;" />
                                                </td>
                                            @endif
                                            <td style="padding-left: {{ !empty($clubLogo) ? '15px' : '0' }}; vertical-align: middle; font-family: Arial, 'Segoe UI', sans-serif; font-size: 22px; font-weight: 700; color: #222222;">
                                                {{ $clubDisplayName }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" style="vertical-align: middle;">
                                    <a href="{{ env('WP_CLUB_URL') }}login"
                                        style="color: #9aa0a6; font-size: 16px; font-weight: 400; text-decoration: none; font-family: Arial, 'Segoe UI', sans-serif;">Login</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        @if ($data['data']['status_id'] == '3')
            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="background-color: #e8f9f1; padding: 20px 24px; text-align: center;">
                            <p
                                style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 18px; font-weight: 700; color: #2d2d2d;">
                                Thank you for your order and support!
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if ($data['data']['status_id'] == '1')
            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="background-color: #e8f9f1; padding: 20px 24px; text-align: center;">
                            <p
                                style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 18px; font-weight: 700; color: #2d2d2d;">
                                Your order has shipped!
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="width: 100%; padding: 20px 24px; font-size: 15px;">

                        @php

                            $fullName = $ordermeta->name ?? '';
                            $nameParts = explode(' ', $fullName);
                            $firstName = $nameParts[0];

                        @endphp

                        @if ($data['data']['status_id'] == '2')
                            <p
                                style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; line-height: 1.6;">
                                Hi {{ $firstName }}, we are sorry that your order had to be cancelled. We have
                                refunded your order. Your original order details are below for your records. You should
                                see the funds returned to the payment method used for the order in 3-5 business days.
                            </p>
                        @endif


                        @if ($data['data']['status_id'] == '1')
                            <p
                                style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; line-height: 1.6;">
                                Hi {{ $firstName }}, we are excited to let you know that your order from
                                {{ $invoice_info->store_legal_name ?? '' }} Store has shipped! Your shipping carrier and
                                tracking information are below.</p>
                        @endif


                        @if ($data['data']['status_id'] == '3')
                            <p
                                style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; line-height: 1.6;">
                                Thank you for your order from {{ $invoice_info->store_legal_name ?? '' }} Store. We have
                                included your order details below for your records. You should receive a shipping
                                confirmation email soon. We really appreciate the support!</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>


        @if ($data['data']['status_id'] == '1' && $order_type !== 'Digital' && !$isPickup)
            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tr>
                    <td colspan="2">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <h4
                            style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial; font-size: 17px; color: #3c3c3c; text-transform: uppercase; padding-left: 20px;">
                            SHIPPER:</h4>
                        <span
                            style="padding-left: 20px; margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-weight: 500;">
                            {{ $data['data']['shippingwithinfo']->shipping_driver ?? '' }}
                        </span>
                    </td>
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <h4
                            style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial; font-size: 17px; color: #3c3c3c; text-transform: uppercase; padding-left: 20px;">
                            TRACKING #:</h4>
                        <span
                            style="padding-left: 20px; margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-weight: 500;">{{ $data['data']['shippingwithinfo']->tracking_no ?? '' }}</span>
                    </td>
                </tr>
            </table>
        @endif

        @php

            if (!empty($data['data']->orderlasttrans->value) ?? '') {
                $jsonString = $data['data']->orderlasttrans->value ?? '';
                $decodedJsonLastTrans = json_decode($jsonString, true);
                $timestamp = $decodedJsonLastTrans['created'] ?? '';
                $createdAt = \Carbon\Carbon::createFromTimestamp($timestamp)->toDateTimeString();

                $cancelDate = date_create($createdAt);
                $cancel_date_format = date_format($cancelDate, 'm/d/Y');
            }
           
            $shippingWith = $data['data']['shippingwithinfo'] ?? null;  
            $shipping_info = json_decode($shippingWith->info ?? '');
            $isPickup = (($order->order_method ?? '') === 'pickup') || (($shipping_info->shipping_label ?? '') === 'In-Person Pick Up');



        @endphp

        @if ($data['data']['status_id'] == '2')
            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tr>
                    <td colspan="2">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <h4
                            style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial; font-size: 17px; color: #3c3c3c; text-transform: uppercase; padding-left: 20px;">
                            CANCELED & REFUNDED</h4>
                        <p
                            style="padding-left: 20px; margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-weight: 500;">
                            {{ $cancel_date_format ?? '' }}</p>
                    </td>
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <h4
                            style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial; font-size: 17px; color: #3c3c3c; text-transform: uppercase; padding-left: 20px;">
                            REFUND AMOUNT</h4>
                        <p
                            style="padding-left: 20px; margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-weight: 500;">
                            {{ currency_formate($amount_refunded/100 ?? 0) }}</p>
                    </td>
                </tr>
            </table>
        @endif

        <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tr>
                <td colspan="2">
                    <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                </td>
            </tr>
            <tr class="border-style">
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                    class="spac-top spac-btm">
                    <span
                        style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px;">
                        Order #: <span style="font-weight: 500;">{{ $data['data']['invoice_no'] ?? '' }}</span>
                    </span><br>
                    <span
                        style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px">Date
                        Placed:<span style="font-weight: 500;">
                            {{ \Carbon\Carbon::parse($order->placed_at)->format('m/d/Y h:i A') }}</span>
                    </span>
                </td>
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                    class="spac-top spac-btm">
                    <a id="click_to_login" href="{{ env('WP_CLUB_URL')}}dashboard/?ua=user-receipts"
                        style="font-size: 15px; color: #08bff3; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; text-decoration: underline;">Click
                        to Login and View Order</a>
                </td>
            </tr>
        </table>

        <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr>
                    <td colspan="2">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px; font-weight: bold; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px;">
                            Billing Address:
                        </h5>
                        <p class="add-shipping-color"
                            style="padding-left: 20px; font-weight: 500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-size: 16px;">
                            @php
                                $billing_name = $ordermeta->name;
                                $billing_email = $ordermeta->email;
                                $billing_phone = $ordermeta->phone;

                                $billing_address = $ordermeta->billing;

                                $billing_add = $billing_address->address;
                                $billing_city = $billing_address->city;
                                $billing_state = $billing_address->state;
                                $billing_country = $billing_address->country;
                                $billing_post_code = $billing_address->post_code;

                                $new_billing_address = $billing_name . '<br>' . $billing_add . '<br>' . $billing_city . ', ' . $billing_state . ' ' . $billing_post_code . '<br>' . $billing_country . '<br>' . $billing_phone . '<br><a href="mailto:' . $billing_email . '" style="color:#08bff3;text-decoration:none;">' . $billing_email . '</a>';
                            @endphp
                            {!! $new_billing_address !!}
                        </p>
                    </td>
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; padding-right: 15px;padding-bottom: 72px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px; font-weight: bold; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px;">
                            Payment Information:
                        </h5>
                        @php
                            if ($data['data']['payment_status'] == '2') {
                                $authorized = 'Pending';
                            } elseif ($data['data']['payment_status'] == '1') {
                                $authorized = 'Paid';
                            } elseif ($data['data']['payment_status'] == '3') {
                                $authorized = 'Incomplete';
                            } elseif ($data['data']['payment_status'] == '4') {
                                $authorized = 'Authorized';
                            } elseif ($data['data']['payment_status'] == '5') {
                                $authorized = 'Refunded';
                            }
                        @endphp
                        <span
                            style="padding-left: 20px; font-weight: 500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-size: 16px; text-transform: capitalize;">Status:
                            <span>{{ $authorized ?? '' }}</span>
                        </span>
                        <p
                            style="padding-left: 20px; font-weight: 500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-size: 16px; text-transform: capitalize;">
                            Card: <span>{{ $card_number ?? '' }}</span></p>
                        <p
                            style="padding-left: 20px; font-weight: 500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-size: 16px; text-transform: capitalize;">
                            Name: <span>{{ $ordermeta->name ?? '' }}</span></p>
                        <p
                            style="padding-left: 20px; font-weight: 500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; font-size: 16px; text-transform: capitalize;">
                            Amount: <span>{{ currency_formate($order->total) }}</span></p>
                    </td>
                </tr>
            </tbody>
        </table>
        @if($order_type !== 'Digital')
            <table style="width:100%;max-width:700px;margin:0 auto;background-color:#fff;">
            <tbody>
                <tr><td colspan="2"><hr width="94%" style="border-top:0px;" color="#e5e5e5" /></td></tr>

                <tr class="border-style">
                {{-- LEFT --}}
                <td style="width:50%;padding-left:15px;padding-right:15px;" class="spac-top spac-btm">

                    @if($isPickup)
                    <h5 style="padding-left:20px;font-weight:bold;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:16px;">
                        In-Person Pick Up Instructions
                    </h5>

                    @php
                        $opt = \App\Models\Option::where('key','inperson_pickup_details')->first();
                        $pickup = $opt && $opt->value ? json_decode($opt->value, true) : [];
                    @endphp

                    <p style="padding-left:20px;font-weight:500;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:16px;">
                        {{ $pickup['address_line1'] ?? '' }}<br>
                        {{ $pickup['address_line2'] ?? '' }}<br>
                        {{ ($pickup['city'] ?? '') }}{{ !empty($pickup['city']) ? ', ' : '' }}{{ $pickup['state'] ?? '' }} {{ $pickup['zip'] ?? '' }}
                    </p>

                    @if(!empty($pickup['instructions']))
                        <p style="padding-left:20px;font-weight:500;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:13px;white-space:pre-line;">
                        {{ $pickup['instructions'] }}
                        </p>
                    @endif
                    @else
                    <h5 style="padding-left:20px;font-weight:bold;font-family:Arial,'Segoe UI',sans-serif;color:#3c3c3c;font-size:16px;">
                        Shipping Address:
                    </h5>

                    @php
                        $ship_name = $ordermeta->shipping->name ?? '';
                        $ship_phone = $ordermeta->shipping->phone ?? '';
                        $ship_add = $ordermeta->shipping->address ?? '';
                        $ship_city = $ordermeta->shipping->city ?? '';
                        $ship_state = $ordermeta->shipping->state ?? '';
                        $ship_country = $ordermeta->shipping->country ?? '';
                        $ship_zip = $ordermeta->shipping->post_code ?? '';

                        $new_shiiping_address =
                        $ship_name . '<br>' .
                        $ship_add . '<br>' .
                        $ship_city . ', ' . $ship_state . ' ' . $ship_zip . '<br>' .
                        $ship_country . '<br>' .
                        $ship_phone . '<br>' .
                        ($billing_email ?? '');
                    @endphp

                    <p style="padding-left:20px;font-weight:500;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:16px;">
                        {!! $new_shiiping_address !!}
                    </p>
                    @endif

                </td>

                {{-- RIGHT --}}
                <td style="width:50%;padding-left:15px;padding-right:15px;padding-bottom: {{ $isPickup ? '30px' : '150px' }};"
                    class="spac-top spac-btm">

                    <h5 style="padding-left:20px;font-weight:bold;font-family:Arial,'Segoe UI',sans-serif;color:#3c3c3c;font-size:16px;">
                    Shipping Information:
                    </h5>

                    @if($isPickup)
                    <span style="padding-left:20px;font-weight:500;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:16px;">
                        In-Person Pick Up
                    </span>
                    @else
                    <span style="padding-left:20px;font-weight:500;font-family:'Nunito','Segoe UI',Arial;color:#3c3c3c;font-size:16px;text-transform:capitalize;">
                        {{ $shipping_info->shipping_label ?? '' }} Shipping
                    </span>
                    @endif

                </td>
                </tr>
            </tbody>
            </table>
        @endif

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr>
                    <td colspan="4">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="heading" style="background-color: #f5f5f5;">
                    <td class="text-left"
                        style="padding: 12px 0 12px 35px;
                    font-weight: bold;
                    font-family: Arial, 'Segoe UI', sans-serif;
                    color: #3c3c3c;
                    font-size: 16px;">
                        Product</td>

                    <td class="text-center"
                        style="padding: 12px 0; font-weight: bold; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px;">
                        Price</td>
                    <td class="text-center"
                        style="padding: 12px 0; font-weight: bold; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px;">
                        Qty</td>
                    <td class="text-right"
                        style="padding: 12px 35px 12px 0; font-weight: bold; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px;">
                        Totals</td>
                </tr>

                @php $subtotal = 0; @endphp

                @foreach ($order->orderitems ?? [] as $row)
                    @php
                        $variations = json_decode($row->info);

                        $options = $variations->options ?? [];
                       // $selected_variation = $options->varitions ?? [];
                       // $varition_options = $options->varition_options ?? [];

                    @endphp

                    <tr>
                        <td class="text-left"
                            style="padding-left: 35px;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ $row->term->title ?? '' }}
                            @foreach ($options ?? [] as $key => $item)
                              @php $product_options = $item->varition_options; @endphp
                            @foreach($item->varitions as $sel_val)
                                @php $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                                    return $x->id == $sel_val->pivot->productoption_id;
                                } );
                                @endphp

                             <br><strong>{{reset($cur_opt_name)->category->name}} : </strong>{{$sel_val->name}}
                            @endforeach
                                <hr>
                            @endforeach
                        </td>
                        <td class="text-center"
                            style="font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ currency_formate($row->amount) }}</td>
                        <td class="text-center"
                            style="font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ $row->qty }}</td>
                        <td class="text-right"
                            style="font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ currency_formate($row->amount * $row->qty) }}</td>
                    </tr>
                    @php $subtotal = $subtotal + $row->amount*$row->qty; @endphp
                @endforeach
            </tbody>
        </table>
        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr>
                    <td colspan="2">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="border-style">
                    <th style="text-align: right;width: 70%;" class="spac-top">
                        @php
                            $shipping_price = $shipping_price ?? 0;
                        @endphp
                        <h5
                            style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c; ">
                            Subtotal:</h5>
                    </th>
                    <td style="text-align: center;
                padding-right: 20px;width: 30%;" class="spac-top">
                        <p
                            style="padding-left: 20px;
                    font-family: 'Nunito', 'Segoe UI', Arial;
                    color: #3c3c3c;
                    font-size: 16px;font-weight: 500;">
                            {{ currency_formate($subtotal) }}</p>
                    </td>
                </tr>
                <tr>
                    <th style="text-align: right;width: 70%;">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Discount @isset($order->coupon_code) ({{ $order->coupon_code }}) @endisset:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;">
                        <p
                            style="padding-left: 20px;
                    font-family: 'Nunito', 'Segoe UI', Arial;
                    color: #3c3c3c;
                    font-size: 16px;font-weight: 500;">
                            -{{ currency_formate($order->discount) }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <th style="text-align: right;width: 70%;">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Sales Tax:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;">
                        <p
                            style="padding-left: 20px;
                    font-family: 'Nunito', 'Segoe UI', Arial;
                    color: #3c3c3c;
                    font-size: 16px;font-weight: 500;">
                            {{ currency_formate($order->tax) }}
                        </p>
                    </td>
                </tr>
                @if( $order_type !== 'Digital')
                <tr>
                    <th style="text-align: right;width: 70%;">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Shipping:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;">
                        <p
                            style="padding-left: 20px;
                    font-family: 'Nunito', 'Segoe UI', Arial;
                    color: #3c3c3c;
                    font-size: 16px;font-weight: 500;">
                    @php
                        $shipping_price = $isPickup ? 0 : (optional($order->shippingwithinfo)->shipping_price ?? 0);
                    @endphp
                    {{ currency_formate($shipping_price) }}
                        </p>
                    </td>
                </tr>
               @endif

                @if(isset($shipping_info->cover_fee) && $shipping_info->cover_fee !== '0')
                <tr>
                    <th style="text-align: right;width: 70%;" class="spac-btm">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Covered Fees:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;" class="spac-btm">
                        <p style="padding-left: 20px; font-family: 'Nunito', 'Segoe UI', Arial;color: #3c3c3c;font-size: 16px;font-weight: 500;">
                            {{ currency_formate($shipping_info->cover_fee) }}
                        </p>
                    </td>
                </tr>
                @endif



                <tr>
                    <th style="text-align: right;width: 70%;" class="spac-btm">
                        <h5
                            style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 18px; color: #3c3c3c;">
                            Total:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;" class="spac-btm">
                        <p
                            style="padding-left: 20px;
                    font-family: Arial, 'Segoe UI', sans-serif;
                    color: #3c3c3c;
                    font-size: 18px;font-weight: 700;">
                            {{ currency_formate($order->total) }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr>
                    <td>
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%; padding: 20px 24px; font-size: 15px;" class="spac-top spac-btm">
                        <p
                            style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; line-height: 1.6;">
                            If you have questions about your order, please don't hesitate to reach out. You will receive
                            an email confirmation once your order has shipped.</p>

                        <p
                            style="margin: 20px 0 0 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500;">
                            Thank You,
                        </p>
                        <p
                            style="margin: 4px 0 0 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500;">
                            {{ $invoice_info->store_legal_name ?? '' }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff; border-collapse: collapse;">
            <tbody>
                <tr>
                    <td style="padding: 20px 24px 28px 24px; text-align: center; border-top: 1px solid #e5e5e5;">
                        <p style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 12px; color: #999999; line-height: 1.6;">
                            Booostr's website is powered by
                            <a href="https://booostr.co/" style="color: #08bff3; text-decoration: none;">Booostr</a>.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
