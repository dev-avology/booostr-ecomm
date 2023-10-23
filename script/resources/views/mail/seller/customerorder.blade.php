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
            padding-bottom: 30px;
        }

        .spac-top {
            padding-top: 30px;
        }

        tr.br-none:after {
            border: 0;
        }
    </style>
</head>


<body style="background-color: #f4f6f9;">
    <div class="table-wrapper" style="width: 100%;max-width: 700px;margin: 0 auto;border-radius: 20px;overflow: hidden;">

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;border-collapse: collapse;">
            <tbody>
                <tr style="background-color: #535353; width: 100%;" class="border-style br-none">
                    <th style="width: 15%; text-align: left; padding: 40px 0 0 20px;border-collapse: collapse;">
                        {{-- <img src="./img/Champs-Sports-Logo.png" alt="logo"
                            style="width: 100%; max-width: 120px; margin-bottom: -15px;position: relative;z-index: 9;"> --}}

                        @if (!empty(tenant()->logo))
                            <img src="{{ env('WP_URL') }}{{ tenant()->logo }}" alt="logo"
                                style="width: 100%; max-width: 120px; margin-bottom: -15px;position: relative;z-index: 9;" />
                        @endif
                    </th>
                    <th style="width: 70%;border-collapse: collapse;">
                        <h2
                            style="font-family: 'Nunito', 'Segoe UI', Arial; font-size: 24px; font-weight: normal; text-align: left; text-transform: capitalize; color: #fff; padding-left: 50px;">
                            {{ $invoice_info->store_legal_name ?? '' }} Store
                        </h2>
                    </th>
                    <th style="width: 15%; padding-right: 20px; text-align: right;border-collapse: collapse;">
                        <a href=""
                            style="color: #fff; font-size: 20px; font-weight: 100; text-transform: uppercase; text-decoration: none; font-family: 'Nunito', 'Segoe UI', Arial;">login</a>
                    </th>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr class="border-style">
                    <td style="width: 100%; padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">

                        @php

                            $fullName = $ordermeta->name ?? '';
                            $nameParts = explode(' ', $fullName);
                            $firstName = $nameParts[0];

                        @endphp

                        @if ($data['data']['status_id'] == '2')
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                Hi {{ $firstName }} we are sorry that your order had to be cancelled. We have
                                refunded your order. Your orginal order details are below for your records. You should
                                see the funds returned to the payment method used for the order in 3-5 business days.
                            </p>
                        @endif


                        @if ($data['data']['status_id'] == '1')
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                Hi {{ $firstName }} ,we are excited to let you know that you order from
                                {{ $invoice_info->store_legal_name ?? '' }} Store has shipped! Your shipping carrier and
                                tracking information are below.</p>
                        @endif


                        @if ($data['data']['status_id'] == '3')
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                Thank you for your order from {{ $invoice_info->store_legal_name ?? '' }} Store. We have
                                included your order details below for your records. You should recieve a shipping
                                confirmation email soon. We really appreciete the support!</p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>


        @if ($data['data']['status_id'] == '1')
            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr class="border-style">
                        <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                            class="spac-top spac-btm">
                            <h4
                                style="font-weight: 700;font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;text-transform: uppercase;padding-left: 20px;">
                                SHIPPER:</h4>
                            <span
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                {{ $data['data']['shippingwithinfo']->shipping_driver ?? '' }}</span>
                        </td>
                        <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                            class="spac-top spac-btm">
                            <h4
                                style="font-weight: 700;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        font-size: 17px;
                        color: #3c3c3c;
                        text-transform: uppercase;padding-left: 20px;">
                                TRACKING #:</h4>
                            <span
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">{{ $data['data']['shippingwithinfo']->tracking_no ?? '' }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        @php

            if (!empty($data['data']->orderlasttrans->value) ?? '') {
                $jsonString = $data['data']->orderlasttrans->value ?? '';
                $decodedJsonLastTrans = json_decode($jsonString, true);
                $timestamp = $decodedJsonLastTrans['created'] ?? '';
                $createdAt = \Carbon\Carbon::createFromTimestamp($timestamp)->toDateTimeString();
                $amountRefunded = $decodedJsonLastTrans['amount_refunded'] ?? '';

                $cancelDate = date_create($createdAt);
                $cancel_date_format = date_format($cancelDate, 'd/m/Y');
            }

        @endphp

        @if ($data['data']['status_id'] == '2')
            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr class="border-style">
                        <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                            class="spac-top spac-btm">
                            <h4
                                style="font-weight: 700;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        font-size: 17px;
                        color: #3c3c3c;
                        text-transform: uppercase;padding-left: 20px;">
                                CANCELED & REFUNDED</h4>
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                {{ $cancel_date_format ?? '' }}</p>
                        </td>
                        <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                            class="spac-top spac-btm">
                            <h4
                                style="font-weight: 700;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        font-size: 17px;
                        color: #3c3c3c;
                        text-transform: uppercase;padding-left: 20px;">
                                REFUND AMOUNT</h4>
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                ${{ $amountRefunded ?? '' }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr class="border-style">
                    <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0;padding-left: 20px;">
                            Order #: <span style="font-weight: 500;">{{ $data['data']['invoice_no'] ?? '' }}</span>
                        </span><br>
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0;padding-left: 20px">
                            Date Placed:<span style="font-weight: 500;">
                                {{ $order->created_at->format('d-m-Y') ?? '' }}</span>
                        </span>
                    </td>
                    <td style="width: 50%;padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <a href="https://staging3.booostr.co/"
                            style="font-size: 15px; color: #00c0ffba; font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial; text-decoration: none;">Click to Login and View Order</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr class="border-style">
                    <td style="width: 50%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;
                            ">
                            Billing Address:</h5>
                        <p
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;">
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

                                $new_billing_address = $billing_name . ',<Br>' . $billing_email . ',<Br>' . $billing_phone . ',<Br>' . $billing_add . ',<Br>' . $billing_city . ',<Br>' . $billing_state . ',<Br>' . $billing_country . ',<Br>' . $billing_post_code;
                            @endphp
                            {!! $new_billing_address !!}
                        </p>

                    </td>
                    <td style="width: 50%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;
                        ">
                            Payment Information:</h5>
                        <span
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;text-transform: capitalize;
                        ">Status:
                            <span>Authorized</span></span>
                        <p
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;text-transform: capitalize;
                        ">
                            Card: <span>{{ $card_number ?? '' }}</span></p>
                        <p
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;text-transform: capitalize;
                        ">
                            Name: <span>{{ $ordermeta->name ?? '' }}</span></p>
                        <p
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;text-transform: capitalize;
                        ">
                            Amount: <span>{{ currency_formate($order->total) }}</span></p>
                    </td>

                </tr>
            </tbody>

        </table>
        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr class="border-style">
                    <td style="width: 50%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;
                            ">
                            Shipping Address:</h5>
                        <span
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;
                            ">
                            @php
                                $shippping_name = $ordermeta->shipping->name;
                                $shippping_phone = $ordermeta->shipping->phone;
                                $shippping_address = $ordermeta->shipping->address;
                                $shippping_city = $ordermeta->shipping->city;
                                $shippping_state = $ordermeta->shipping->state;
                                $shippping_country = $ordermeta->shipping->country;
                                $shippping_post_code = $ordermeta->shipping->post_code;

                                $new_shiiping_address = $shippping_name . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_phone . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_address . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_city . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_state . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_country . ',<br/>&nbsp;&nbsp;&nbsp;&nbsp;' . $shippping_post_code;
                            @endphp
                            {!! $new_shiiping_address !!}
                        </span>
                    </td>
                    <td style="width: 50%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <h5
                            style="padding-left: 20px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;">
                            Shipping Information:</h5>

                        <span
                            style="padding-left: 20px;font-weight:500; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-size: 16px;text-transform: capitalize;
                        ">{!! $data['data']['shippingwithinfo']->shipping_driver ?? '' !!}
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{!! $data['data']['shippingwithinfo']->tracking_no ?? '' !!}</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>

                <tr class="border-style">
                <tr class="heading">
                    <td class="text-left">Product</td>

                    <td class="text-center">Price</td>
                    <td class="text-center">Qty</td>
                    <td class="text-right">Totals</td>
                </tr>

                @php $subtotal = 0; @endphp

                @foreach ($order->orderitems ?? [] as $row)
                    @php
                        $variations = json_decode($row->info);

                        $options = $variations->options ?? [];

                    @endphp
                    </tr>

                    <tr>
                        <td class="text-left">{{ $row->term->title ?? '' }}
                            @foreach ($options ?? [] as $key => $item)
                                <br>
                                <span>{{ $key }}:</span><br>

                                @foreach ($item ?? [] as $r)
                                    <span>{{ __('Name:') }} {{ $r->name ?? '' }},</span>
                                    <span>{{ __('Price:') }} {{ currency_formate($r->price ?? 0) }},</span>
                                @endforeach
                                <hr>
                            @endforeach
                        </td>
                        <td class="text-center">{{ $row->amount }}</td>
                        <td class="text-center">{{ $row->qty }}</td>
                        <td class="text-right">{{ currency_formate($row->amount * $row->qty) }}</td>
                    </tr>
                    @php $subtotal = $subtotal + $row->amount*$row->qty; @endphp
                @endforeach
            </tbody>
        </table>
        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
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
                            {{ number_format($order->total - $shipping_price, 2) }}</p>
                    </td>
                </tr>
                <tr>
                    <th style="text-align: right;width: 70%;">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Discount:</h5>
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
                            @php $shipping_price=$order->shippingwithinfo->shipping_price ?? 0; @endphp
                            {{ currency_formate($shipping_price) }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <th style="text-align: right;width: 70%;" class="spac-btm">
                        <h5
                            style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                            Total:</h5>
                    </th>
                    <td style="text-align: center;padding-right: 20px;width: 30%;" class="spac-btm">
                        <p
                            style="padding-left: 20px;
                    font-family: 'Nunito', 'Segoe UI', Arial;
                    color: #3c3c3c;
                    font-size: 16px;font-weight: 500;">
                            {{ currency_formate($order->total) }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr class="border-style">
                    <td style="width: 100%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                        class="spac-top spac-btm">
                        <p
                            style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;">
                            If you have questions about your order ,please don't hesitate to reach out. You will recieve
                            an email confirmation once your order has shipped.</p>

                        <p
                            style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;padding-top: 20px;
                            padding-bottom: 20px;">
                            Thank You,
                        </p>
                        <p
                            style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;">
                            Hello Testor Club
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr
                    style="display: inline-block;
                background: #13c3fd;
                margin: 0 35px;
                padding: 20px 30px 15px 30px;
                border-radius: 15px 15px 0 0;">
                    <td style="width: 30%;">
                        <h6
                            style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;
                        color: #fff;
                        padding-bottom: 10px;">
                            Powered By:</h6>
                        <img src="./img/Champs-Sports-Logo.png" alt="logo" style="width: 100%;max-width: 115px;">
                    </td>
                    <td>
                        <p
                            style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 13px;
                        color: #fff;
                        font-weight: 300;">
                            {{ $invoice_info->store_legal_name ?? '' }} Store</p>
                        <p
                            style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px;
                        color: #fff;
                        font-weight: 300;">
                            Lorem <a href=""
                                style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px;
                        color: #fff;
                        font-weight: 300;">utilizes
                                Booostr</a>to help them manage their organization, communication with their team and
                            supporters and raise money online.<a>Learn more here</a></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
