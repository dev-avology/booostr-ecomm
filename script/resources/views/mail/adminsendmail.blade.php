<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    table,
    th,
    td {
        border-collapse: collapse;
    }
    p {
        padding: 0;
        margin: 0;
    }
</style>

<body style="background-color: #f4f6f9;">
    <table
        style="position: fixed; width: 650px; background-color: #fff;transform: translate(-50%, -50%);left: 50%; top: 50%;border-radius: 20px; overflow: hidden;margin-left: 240px;">
        <tbody>
            <tr style="background-color:#00c0ff; width: 100%;">
                <th style="width: 50%; text-align: left; padding: 20px;">
                    @if (!empty(tenant()->logo))
                    <img src="{{ env('WP_URL') }}{{ tenant()->logo }}"alt="logo" style="width: 100%; max-width: 120px;" />
                    @endif
                </th>
                <th style="width: 50%; padding-right: 40px; text-align: right;">
                    <a href=""
                        style="color: #fff;font-size: 20px;font-weight: 100;text-transform: uppercase; text-decoration: none; font-family: 'Nunito', 'Segoe UI', arial;">login</a>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="font-family: 'Nunito', 'Segoe UI', Arial; padding-top: 39px; padding-bottom: 39px; font-size: 24px; font-weight: normal; text-align: center;     text-transform: capitalize;">
                    {{$data['data']->club_name ?? ''}} Store
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order #: {{$data['data']->invoice_no ?? ''}}</p>
                </td>
              
            </tr>
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order Total: ${{$data['data']->total ?? ''}}</p>
                </td>
            </tr>

            @php
            $date = date_create($data['data']->created_at);
            $date_format = date_format($date, "d/m/Y");

            $cancelDate = date_create($data['data']->cancel_date);
            $cancel_date_format = date_format($cancelDate, "d/m/Y");

            @endphp

            @php
            if ($data['message'] == 'Order captured') {
                $order_status = $data['message'];
                $main_message = 'This email to alert you that you have successfully captured the payment for order #: ' . $data['data']->invoice_no . ', and the funds will be transferred to your bank account within 1-3 business days. Please make sure the order is fulfilled as quickly as possible.';
            } elseif ($data['message'] == 'Order Cancel & Refund') {
                $order_status = $data['message'];
                $main_message = 'This email is to alert you that you have cancelled and refunded order #: ' . $data['data']->invoice_no . ' from ' . $data['data']->club_name . ' Store.';
            } elseif ($data['message'] == 'You have received a new order') {
                $order_status = $data['message'];
                $main_message = 'This email is to alert you that '. $data['data']->club_name .' has recieved a new order via their Booostr Store' ;
            } else {
                if ($data['data']['status_id'] == 1) {
                    $order_status = 'Order Complete';
                    $main_message = 'This email is to alert you that you have successfully completed & shipped order #: ' . $data['data']->invoice_no . '. The customer has been emailed their tracking information.';
                } elseif ($data['data']['status_id'] == 2) {
                    $order_status = 'Order Cancel';
                    $main_message = 'This email is to alert you that you have cancelled and refunded order #: ' . $data['data']->invoice_no . ' from ' . $data['data']->club_name . ' Store.';
                } elseif ($data['data']['status_id'] == 3) {
                    $order_status = 'Order Pending';
                    $main_message = 'This email is to alert you that you have a pending order #: ' . $data['data']->invoice_no . ' from ' . $data['data']->club_name . ' Store.';
                }
            }
            @endphp
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order Date: {{ $date_format  ?? ''}}</p>
                </td>
            </tr>

            @if (!empty($data['data']->cancel_date))
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0; margin: 0; padding-left: 30px; font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Cancel Date: {{ $cancel_date_format ?? ''}}</p>
                </td>
            </tr>
            @endif

            @if (!empty($data['data']->refund_amount))
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0; margin: 0; padding-left: 30px; font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Refund Amount: ${{$data['data']->refund_amount ?? ''}}</p>
                </td>
            </tr>
            @endif

           


            <tr>
                <td colspan="2" style="width: 100%; padding-top: 40px; padding-bottom: 50px; font-size: 15px;">
                    <p style="padding: 0;margin: 0;padding-left: 30px; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">{{ $main_message ?? '' }}</p>
                </td>
            </tr>
        
             <tr>
                <td colspan="2" style="width: 100%; padding-top: 15px; padding-bottom: 35px;padding-left: 30px">
                    <a href="{{$data['link']}}" style=" font-size: 15px; color: #00c0ffba; font-weight: 700;font-family: 'Nunito', 'Segoe UI', Arial;">Click to Login and View Order</a>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100%; ;padding-left: 30px">
                    <p style="font-family: 'Nunito', 'Segoe UI', Arial; font-size: 15px; color: #3c3c3c; padding-bottom: 30px;line-height: 23px;
                    ">From, <br>
                        The Booostr Team on behalf of {{$data['data']->club_name ?? ''}}
                    </p>
                </td>
            </tr>
            
         
        </tbody>
    </table>
</body>

</html>