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

            .add-shipping-color p {
                color: #3c3c3c;
            }

            .add-shipping-color a {
                color: #3c3c3c;
                text-decoration: none;
            }

            #click_to_login {
                text-decoration: underline !important;
            }

            #learn_more {
                color: #fff;
                text-decoration: underline !important;
            }
        </style>
    </head>


    <body style="background-color: #f4f6f9;">
        <div class="table-wrapper" style="width: 100%;max-width: 700px;margin: 0 auto;border-radius: 20px;overflow: hidden;">

        <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;border-collapse: collapse;">
            <tbody>

                <tr style="background-color: #535353; width: 100%;" class="border-style br-none">
                    <th style="width:25%;text-align:left;padding: 18px 0 7px 20px;border-collapse:collapse;">
                        {{-- <img src="./img/Champs-Sports-Logo.png" alt="logo"
                            style="width: 100%; max-width: 120px; margin-bottom: -15px;position: relative;z-index: 9;"> --}}

                        @if (!empty(tenant()->logo))
                            <img src="{{ env('WP_URL') }}{{ tenant()->logo }}" alt="logo"
                                style="width: 100%;max-width: 120px;min-height:84px;border-radius:100px;"/>
                        @endif
                    </th>
                    <th style="width: 75%;border-collapse: collapse;">
                        <h2
                            style="font-family: 'Nunito', 'Segoe UI', Arial; font-size: 24px; font-weight: normal; text-align: left; text-transform: capitalize; color: #fff; padding-left: 50px;">
                            {{ $data['invoice_data']->store_legal_name ?? '' }} Store
                        </h2>
                    </th>
                </tr>
            </tbody>
        </table>


            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr class="border-style">
                        <td style="width: 100%; padding-left: 15px;font-size: 15px; padding-right: 15px;" class="spac-top spac-btm">
                        <p style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;"> Thank you for your order from {{$data['club_name']}} Store. We have included your order details below for your records. We really appreciate the support!</p>
                        </td>
                    </tr>
                </tbody>
            </table>


            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                        <span style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px;">
                            Name: <span style="font-weight: 500;">{{$data['client_name'] ?? "NA"}}</span>
                        </span>
                        <br>
                        <span style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px">
                            Email: <span style="font-weight: 500;">{{$data['client_email'] ?? "NA"}}</span>
                        </span>
                    </td>
                </tr>
            </table>


            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px;">
                            Order #: <span style="font-weight: 500;">{{$data['orderId'] ?? "NA"}}</span>
                        </span>
                        <br>
                      
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px">Date
                            Placed:<span style="font-weight: 500;">{{ \Carbon\Carbon::parse($data['data']->created_at)->format('m/d/Y h:i A') ?? ""}}</span>
                        </span>
                    </td>
                </tr>
            </table>

            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
            <tbody>
                <tr>
                    <td colspan="4">
                        <hr width="94%" style="border-top: 0px;" color="#e5e5e5" />
                    </td>
                </tr>
                <tr class="border-style">
                <tr class="heading">
                    <td class="text-left"
                        style="padding-left: 35px;
                    font-weight: bold;
                    font-family: 'Nunito','Segoe UI',Arial;
                    color: #3c3c3c;
                    font-size: 16px;">
                        Product</td>

                    <td class="text-center"
                        style="font-weight: bold;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                        Price</td>
                    <td class="text-center"
                        style="font-weight: bold;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                        Qty</td>
                    <td class="text-right"
                        style="font-weight: bold;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                        Totals</td>
                </tr>

                @php $subtotal = 0; @endphp

                @foreach ($order->orderitems ?? [] as $row)
                    @php

                        $variations = json_decode($row->info);
                        $options = $variations->options ?? [];

                    @endphp
                    </tr>

                    <tr>
                        <td class="text-left"
                            style="padding-left: 35px;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ $row->term->title ?? '' }}
                            @foreach ($options ?? [] as $key => $item)
                              @php $product_options = $item->varition_options; @endphp
                            @foreach($item->varitions as $sel_val)
                                @php $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                                    return $x->id == $sel_val->pivot->productoption_id;
                                });
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
                    <tr class="border-style">
                        <th style="text-align: right;width: 70%;" class="spac-top">
                            <h5
                                style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c; ">
                                Subtotal:</h5>
                        </th>
                        <td style="text-align: right; padding-right: 35px;width: 30%;" class="spac-top">
                            <p style="padding-left: 20px; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; text-align: right;font-size: 16px;font-weight: 500;">
                                ${{ number_format($subtotal ?? 0, 2) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;width: 70%;">
                            <h5
                                style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                                Sales Tax :</h5>
                        </th>
                        <td style="text-align: right;padding-right: 35px;width: 30%;">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['data']->tax ?? 0, 2) }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;width: 70%;" class="spac-btm">
                            <h5
                                style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                                Total:</h5>
                        </th>
                        <td style="text-align: right;padding-right: 35px;width: 30%;" class="spac-btm">
                            <p style="padding-left: 20px; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['data']->total ?? 0, 2) }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr class="border-style">
                        <th style="text-align: right;width: 70%;" class="spac-top">
                            <h5
                                style="font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c; ">
                                Payment Method:</h5>
                        </th>
                        <td style="text-align: right;
                    padding-right: 35px;width: 30%;" class="spac-top">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                {{ $data['data']->getway->name ?? ''}}</p>
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
                    <tr class="border-style">
                        <td style="width: 100%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                            class="spac-top spac-btm">
                            <p style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;"> If you have questions about your order, please don't hesitate to reach out.
                            <p style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;padding-top: 20px; padding-bottom: 20px;"> Thank You, </p>
                            <p style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;"> {{$data['club_name'] ?? ""}} </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr style="display: inline-block; background: #13c3fd; margin: 0 35px; padding: 20px 30px 15px 30px; border-radius: 15px 15px 0 0;">
                        <td style="width: 30%;">
                            <h6 style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px; color: #fff; padding-left: 10px;">
                                Powered By:</h6>
                            <img src="https://booostr.co/wp-content/uploads/2022/03/booostr-logo-long-top-header.png"alt="logo"
                                style="width: 100%;max-width: 115px;"/>
                        </td>
                        <td>
                            <p style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 13px; color: #fff; font-weight: 300;">
                                {{$data['club_name'] ?? ""}}</p>
                            <p style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px; color: #fff; font-weight: 300;padding-bottom: 25px;font-size:12px;"> utilizes <a href="https://booostr.co/" style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px;color: #fff;font-weight: 300;text-decoration: none;">
                                    <span style="text-decoration:underline;cursor: pointer;">Booostr</span></a> to help them manage their organization, communicate with their team and supporters and raise money online.&nbsp;&nbsp;<a id="learn_more" href="https://booostr.co/">Learn more here</a></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>

</html>