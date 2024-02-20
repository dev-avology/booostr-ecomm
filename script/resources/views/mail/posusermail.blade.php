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
                        <th style="width:15%;text-align:left;padding: 18px 0 7px 20px;border-collapse:collapse;">
                            <img src="https://booostr.co/wp-content/uploads/2022/03/booostr-logo-long-top-header.png" alt="logo"
                                    style="width: 120px;border-radius:100px;"/>
                        </th>
                        <th style="width: 85%; padding-right: 20px; border-collapse: collapse;">
                            <h2
                                style="font-family: 'Nunito', 'Segoe UI', Arial; font-size: 24px; font-weight: normal; text-align: left; text-transform: capitalize; color: #fff; padding-left: 50px;">
                                {{$data['club_name'] ?? ""}}
                            </h2>
                        </th>
                    </tr>
                </tbody>
            </table>

            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>
                    <tr class="border-style">
                        <td style="width: 100%; padding-left: 15px;font-size: 15px; padding-right: 15px;"
                            class="spac-top spac-btm">
                                <p
                                    style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;font-weight: 500;">
                                    Thank you for your order from Hello Tester Club Store. We have
                                    included your order details below for your records. We really appreciate the support!</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="width: 100%; max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;"
                        class="spac-top spac-btm">
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px;">
                            Name: <span style="font-weight: 500;">{{$data['client_name'] ?? "NA"}}</span>
                        </span>
                        <br>
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px">
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
                            Order #: <span style="font-weight: 500;">{{$data['orderid'] ?? "NA"}}</span>
                        </span>
                        <br>
                      
                        <span
                            style="font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c; margin: 0; padding-left: 20px">Date
                            Placed:<span style="font-weight: 500;">{{ \Carbon\Carbon::parse($data['created_at'])->format('m/d/Y h:i A') ?? ""}}</span>
                        </span>
                    </td>
                </tr>
            </table>

            <table style="width: 100%;max-width: 700px; margin: 0 auto; background-color: #fff;">
                <tbody>

                    <tr class="heading border-style">
                        <td class="text-left"
                            style="padding-left: 35px;
                        font-weight: bold;
                        font-family: 'Nunito','Segoe UI',Arial;
                        color: #3c3c3c;
                        font-size: 16px;">
                            Product</td>

                        <td class="text-center"
                            style="padding-left: 35px; text-align: right; font-weight: bold;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                            Price</td>
                        <td class="text-center"
                            style="text-align: right; font-weight: bold; font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                            Qty</td>
                        <td class="text-right"
                            style="padding-right: 35px; text-align: right; font-weight: bold;font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 16px;">
                            Totals</td>
                    </tr>

                    @foreach($data['items'] ?? [] as $item) 

                    @if($item['is_variation'] == 0)

                    <tr>
                        <td class="text-left"
                            style="padding-left: 35px; font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{ $item['title'] ?? ''}}
                        </td>
                        <td class="text-center"
                            style="text-align: right; font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            ${{ number_format($item['firstprice']['price'] ?? 0, 2) }}
                        </td>
                        <td class="text-center"
                            style="text-align: right; font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            {{$item['cart_quantity'] ?? 0}}
                        </td>
                        <td class="text-right"
                            style="padding-right: 35px; text-align: right; font-family: 'Nunito','Segoe UI',Arial;color: #3c3c3c;font-size: 15px;">
                            ${{ number_format($item['firstprice']['price']*$item['cart_quantity'] ?? 0, 2) }}
                        </td> 
                    </tr>

                    @else

                    <p>This is variation section</p>

                    @endif
                     
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
                        <td style="text-align: right;
                    padding-right: 35px;width: 30%;" class="spac-top">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['order_subtotal'] ?? 0, 2) }}</p>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;width: 70%;">
                            <h5
                                style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                                Sales Tax ({{$data['tax']}}):</h5>
                        </th>
                        <td style="text-align: right;padding-right: 35px;width: 30%;">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['order_tax'] ?? 0, 2) }}
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
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['order_total'] ?? 0, 2) }}
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
                                {{ $data['payment_method'] ?? ''}}</p>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;width: 70%;">
                            <h5
                                style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                                Amount Tendered:</h5>
                        </th>
                        <td style="text-align: right;padding-right: 35px;width: 30%;">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['payment_details']['tendered_amount'] ?? 0, 2) }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: right;width: 70%;" class="spac-btm">
                            <h5
                                style=" font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;font-size: 17px;color: #3c3c3c;">
                                Change Returned:</h5>
                        </th>
                        <td style="text-align: right;padding-right: 35px;width: 30%;" class="spac-btm">
                            <p
                                style="padding-left: 20px;
                        font-family: 'Nunito', 'Segoe UI', Arial;
                        color: #3c3c3c;
                        text-align: right;
                        font-size: 16px;font-weight: 500;">
                                ${{ number_format($data['payment_details']['tendered_amount']-$data['order_total'] ?? 0, 2) }}
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
                    <tr class="border-style">
                        <td style="width: 100%;padding-left: 15px;font-size: 15px; padding-right: 15px;"
                            class="spac-top spac-btm">
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;">
                                If you have questions about your order, please don't hesitate to reach out.
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;padding-top: 20px;
                                padding-bottom: 20px;">
                                Thank You,
                            </p>
                            <p
                                style="padding-left: 20px;margin: 0; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;    font-weight: 500;">
                                {{$data['club_name'] ?? ""}}
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
                            padding-left: 10px;">
                                Powered By:</h6>
                            <img src="https://booostr.co/wp-content/uploads/2022/03/booostr-logo-long-top-header.png"alt="logo"
                                style="width: 100%;max-width: 115px;"/>
                        </td>
                        <td>
                            <p
                                style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 13px;
                            color: #fff;
                            font-weight: 300;">
                                {{$data['club_name'] ?? ""}}</p>
                            <p
                                style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px;
                            color: #fff;
                            font-weight: 300;padding-bottom: 25px;font-size:12px;">
                                utilizes<a href="https://staging3.booostr.co/"
                                    style="font-family: 'Nunito', 'Segoe UI', Arial;font-size: 14px;
                            color: #fff;
                            font-weight: 300;text-decoration: none;">
                                    <span style="text-decoration:underline;cursor: pointer;">Booostr</span></a> to help them manage their organization, communicate with their team and
                                supporters and raise money online.&nbsp;&nbsp;<a id="learn_more" href="https://staging3.booostr.co/">Learn more here</a></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>

</html>