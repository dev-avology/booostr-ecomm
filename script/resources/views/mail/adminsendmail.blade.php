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
        style="position: fixed; width: 650px; background-color: #fff;transform: translate(-50%, -50%);left: 50%; top: 50%;border-radius: 20px; overflow: hidden;margin-left: 161px;">
        <tbody>
            <tr style="background-color:#00c0ff; width: 100%;">
                <th style="width: 50%; text-align: left; padding: 20px;">
                    <img src="{{ env('WP_URL') }}{{'uploads/2022/03/booostr-logo-long-top-header.png'}}" alt="logo" style="width: 100%; max-width: 120px;" />
                </th>
                <th style="width: 50%; padding-right: 40px; text-align: right;">
                    <a href=""
                        style="color: #fff;font-size: 20px;font-weight: 100;text-transform: uppercase; text-decoration: none; font-family: 'Nunito', 'Segoe UI', arial;">login</a>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="font-family: 'Nunito', 'Segoe UI', Arial; padding-top: 39px; padding-bottom: 39px; font-size: 24px; font-weight: normal; text-align: center;     text-transform: capitalize;">
                    {{ $data['invoice_data']->store_legal_name ?? '' }} Store
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order #: {{ $data['invoice_no'] ?? '' }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order Total: {{ currency_formate($data['total']) ?? 0 }}</p>
                </td>
            </tr>

            @php

            $date = date_create($data['created_at']);
            $date_format = date_format($date, "m/d/Y");

            $jsonString = $data->orderlasttrans->value;
            $decodedJsonLastTrans = json_decode($jsonString, true);
            $timestamp = $decodedJsonLastTrans['created'] ?? '';
            $cancelDate = \Carbon\Carbon::createFromTimestamp($timestamp)->toDateTimeString();
            $cancel_date = date_create($cancelDate);
            $cancel_date_format = date_format($cancel_date, "m/d/Y");
            $amountRefunded = $decodedJsonLastTrans['amount_refunded']/100 ?? '';

            @endphp

            @php
                if ($data['status_id'] == 1) {

                    $main_message = 'This email is to alert you that you have successfully completed & shipped order #: ' . $data['invoice_no'] . '. The customer has been emailed their tracking information.';

                } elseif ($data['status_id'] == 2) {

                    $main_message = 'This email is to alert you that you have cancelled and refunded order #: ' . $data['invoice_no'] . ' from ' . $data['invoice_data']->store_legal_name . ' Store.';

                } elseif ($data['status_id'] == 3) {

                    $main_message = 'This email is to alert you that '. $data['invoice_data']->store_legal_name .' has received a new order via their Booostr Store.' ;

                }elseif ($data['status_id'] == 4){

                    $main_message = 'This email is to alert you that you have successfully captured the payment for order #: ' . $data['invoice_no'] . ', and the funds will be transferred to your bank account within 1-3 business days. Please make sure the order is fulfilled as quickly as possible.';

                }
            @endphp
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0;margin: 0;padding-left: 30px;font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Order Date: {{ $date_format  ?? ''}}</p>
                </td>
            </tr>

            @if ($data['status_id'] == 2)
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0; margin: 0; padding-left: 30px; font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Cancel Date: {{ $cancel_date_format ?? ''}}</p>
                </td>
            </tr>
            @endif

            @if ($data['status_id'] == 2)
            <tr>
                <td colspan="2" style="width: 100%;">
                    <p style="padding: 0; margin: 0; padding-left: 30px; font-weight: bold; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">Refund Amount: {{currency_formate($amountRefunded ?? 0)}}</p>
                </td>
            </tr>
            @endif

            <tr>
                <td colspan="2" style="width: 100%; padding-top: 40px; padding-bottom: 50px; font-size: 15px;">
                    <p style="padding: 0;margin: 0;padding-left: 30px; font-family: 'Nunito', 'Segoe UI', Arial; color: #3c3c3c;">{{ $main_message ?? '' }}</p>
                </td>
            </tr>
        
             <tr>
                <td colspan="2" style="width: 100%; padding-top: 15px; padding-bottom: 35px; padding-left: 30px;">
                    <a href="{{ env('WP_CLUB_URL')}}dashboard/?ua=storemanager&item=<?php echo tenant()->club_id; ?>" style="font-size: 15px; color: #00c0ffba; font-weight: 700; font-family: 'Nunito', 'Segoe UI', Arial;">Click to Login and View Order</a>
                </td>                
            </tr>
            <tr>
                <td colspan="2" style="width: 100%; ;padding-left: 30px">
                    <p style="font-family: 'Nunito', 'Segoe UI', Arial; font-size: 15px; color: #3c3c3c; padding-bottom: 30px;line-height: 23px;
                    ">From, <br>
                        The Booostr Team on behalf of {{ $data['invoice_data']->store_legal_name ?? '' }}
                    </p>
                </td>
            </tr>
            
        </tbody>
    </table>
</body>

</html>