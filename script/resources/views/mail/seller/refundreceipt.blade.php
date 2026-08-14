<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Receipt</title>

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

        body {
            margin: 0;
            padding: 24px 12px;
            background-color: #efefef;
            font-family: Arial, 'Segoe UI', sans-serif;
        }

        .mail-shell {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
        }

        .section-table {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            background-color: #ffffff;
            border-collapse: collapse;
        }

        .section-padding {
            padding-left: 40px !important;
            padding-right: 40px !important;
        }

        .muted-copy {
            margin: 0;
            font-family: Arial, 'Segoe UI', sans-serif;
            color: #4d4d4d;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
        }

        .border-style:after {
            position: absolute;
            content: '';
            border-bottom: 1px solid #ececec;
            width: 88%;
            transform: translateX(-50%);
            left: 50%;
        }

        .border-style {
            position: relative;
        }

        .spac-btm {
            padding-bottom: 26px;
        }

        .spac-top {
            padding-top: 26px;
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
            color: #77c7ef !important;
            text-decoration: underline !important;
        }
    </style>
</head>

<body>
    <div class="mail-shell">

        <table class="section-table">
            <tbody>
                @php
                    $clubLogo = tenant_club_logo();
                    $clubDisplayName = $invoice_info->store_legal_name ?? (tenant_club_info()['club_name'] ?? '');
                    $fullName = $ordermeta->name ?? '';
                    $nameParts = explode(' ', $fullName);
                    $firstName = $nameParts[0] ?? 'there';
                    $shippingWith = $order->shippingwithinfo ?? null;
                    $shipping_info = json_decode($shippingWith->info ?? '');
                    $isPickup = (($order->order_method ?? '') === 'pickup') || (($shipping_info->shipping_label ?? '') === 'In-Person Pick Up');
                @endphp
                <tr style="background-color: #ffffff; width: 100%;">
                    <td class="section-padding" style="padding-top: 26px; padding-bottom: 18px; vertical-align: middle;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                            <tr>
                                <td align="left" style="vertical-align: middle;">
                                    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                        <tr>
                                            @if (!empty($clubLogo))
                                                <td style="vertical-align: middle;">
                                                    <img src="{{ $clubLogo }}"
                                                        width="58"
                                                        height="58"
                                                        alt="{{ $clubDisplayName }}"
                                                        style="display: block;" />
                                                </td>
                                            @endif
                                            <td style="padding-left: {{ !empty($clubLogo) ? '14px' : '0' }}; vertical-align: middle; font-family: Arial, 'Segoe UI', sans-serif; font-size: 15px; font-weight: 700; color: #161616;">
                                                {{ $clubDisplayName }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" style="vertical-align: middle;">
                                    <a href="{{ env('WP_CLUB_URL') }}login"
                                        style="color: #9e9e9e; font-size: 12px; font-weight: 500; text-decoration: none; font-family: Arial, 'Segoe UI', sans-serif;">Login</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td class="section-padding" style="padding-top: 12px; padding-bottom: 12px;">
                        <div style="background-color: #dff8ec; padding: 24px 20px; text-align: center;">
                            <p style="margin: 0 0 6px 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 12px; font-weight: 700; color: #1a7a52; letter-spacing: 0.04em; text-transform: uppercase;">
                                Refund Receipt
                            </p>
                            <p style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 17px; font-weight: 400; color: #171717; line-height: 1.35;">
                                Your refund has been processed
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td class="section-padding" style="width: 100%; padding-top: 14px; padding-bottom: 16px; font-size: 15px;">
                        <p class="muted-copy">
                            Hi {{ $firstName }}, we have processed a refund for your order from
                            {{ $invoice_info->store_legal_name ?? '' }} Store. Your refund details are below for your
                            records. Depending on your payment method, you should see the funds returned in 3–5
                            business days.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="section-table">
            <tr>
                <td colspan="2">
                    <hr width="88%" style="border-top: 0px;" color="#ececec" />
                </td>
            </tr>
            <tr class="border-style">
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                    <h4 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 14px; color: #3c3c3c; text-transform: uppercase; padding-left: 24px;">
                        Refund Date</h4>
                    <p style="padding-left: 24px; margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; font-size: 14px;">
                        {{ $refund['date'] ?? '' }}</p>
                </td>
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                    <h4 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 14px; color: #3c3c3c; text-transform: uppercase; padding-left: 24px;">
                        Refund Amount</h4>
                    <p style="padding-left: 24px; margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 700; font-size: 16px;">
                        {{ currency_formate($refund['grand_total'] ?? 0) }}</p>
                </td>
            </tr>
        </table>

        @if (!empty($refund['reference_id']))
            <table class="section-table">
                <tr>
                    <td colspan="2">
                        <hr width="88%" style="border-top: 0px;" color="#ececec" />
                    </td>
                </tr>
                <tr class="border-style">
                    <td colspan="2" style="padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                        <h4 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 14px; color: #3c3c3c; text-transform: uppercase; padding-left: 24px;">
                            Refund Reference #</h4>
                        <p style="padding-left: 24px; margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-weight: 500; font-size: 14px;">
                            {{ $refund['reference_id'] }}</p>
                    </td>
                </tr>
            </table>
        @endif

        <table class="section-table">
            <tr>
                <td colspan="2">
                    <hr width="88%" style="border-top: 0px;" color="#ececec" />
                </td>
            </tr>
            <tr class="border-style">
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                    <span style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; margin: 0; padding-left: 24px; font-size: 14px;">
                        Order #: <span style="font-weight: 500;">{{ $order->invoice_no ?? '' }}</span>
                    </span><br>
                    <span style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; margin: 0; padding-left: 24px; font-size: 14px;">
                        Date Placed:<span style="font-weight: 500;">
                            {{ \Carbon\Carbon::parse($order->placed_at)->format('m/d/Y h:i A') }}</span>
                    </span>
                </td>
                <td style="width: 50%; padding-left: 15px; font-size: 15px; text-align: left;" class="spac-top spac-btm">
                    <a id="click_to_login" href="{{ env('WP_CLUB_URL') }}dashboard/?ua=user-receipts"
                        style="font-size: 12px; color: #77c7ef; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; text-decoration: underline;">Click
                        to Login and View Order</a>
                </td>
            </tr>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td colspan="2">
                        <hr width="88%" style="border-top: 0px;" color="#ececec" />
                    </td>
                </tr>
                <tr class="border-style">
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; padding-right: 15px;" class="spac-top spac-btm">
                        <h5 style="padding-left: 24px; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 15px;">
                            Billing Address:
                        </h5>
                        <p class="add-shipping-color" style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                            @php
                                $billing_address = $ordermeta->billing ?? null;
                                $billing_add = $billing_address->address ?? '';
                                $billing_city = $billing_address->city ?? '';
                                $billing_state = $billing_address->state ?? '';
                                $billing_country = $billing_address->country ?? '';
                                $billing_post_code = $billing_address->post_code ?? '';
                                $billing_email = $ordermeta->email ?? '';
                                $billing_phone = $ordermeta->phone ?? '';
                                $new_billing_address = ($ordermeta->name ?? '') . '<br>' . $billing_add . '<br>' . $billing_city . ', ' . $billing_state . ' ' . $billing_post_code . '<br>' . $billing_country . '<br>' . $billing_phone . '<br><a href="mailto:' . $billing_email . '" style="color:#08bff3;text-decoration:none;">' . $billing_email . '</a>';
                            @endphp
                            {!! $new_billing_address !!}
                        </p>
                    </td>
                    <td style="width: 50%; padding-left: 15px; font-size: 15px; padding-right: 15px; padding-bottom: 72px;" class="spac-top spac-btm">
                        <h5 style="padding-left: 24px; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 15px;">
                            Payment Information:
                        </h5>
                        <span style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                            Status: <span>Refunded</span>
                        </span>
                        <p style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                            Method: <span>{{ $payment_method_label ?? 'Card' }}</span></p>
                        @if (!empty($card_number))
                            <p style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                                Card: <span>{{ $card_number }}</span></p>
                        @endif
                        <p style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                            Name: <span>{{ $ordermeta->name ?? '' }}</span></p>
                        <p style="padding-left: 24px; font-weight: 500; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; line-height: 1.55;">
                            Original Order Total: <span>{{ currency_formate($order->total) }}</span></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td colspan="4">
                        <hr width="88%" style="border-top: 0px;" color="#ececec" />
                    </td>
                </tr>
                <tr class="heading" style="background-color: #ffffff;">
                    <td class="text-left" style="padding: 12px 0 12px 35px; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px;">
                        Refunded Item(s)</td>
                    <td class="text-center" style="padding: 12px 0; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px;">
                        Price</td>
                    <td class="text-center" style="padding: 12px 0; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px;">
                        Qty</td>
                    <td class="text-right" style="padding: 12px 35px 12px 0; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px;">
                        Refund</td>
                </tr>

                @foreach ($refund['items'] ?? [] as $row)
                    <tr>
                        <td class="text-left" style="padding: 6px 0 6px 35px; font-family: Arial,'Segoe UI',sans-serif; color: #3c3c3c; font-size: 13px; line-height: 1.55;">
                            {{ $row['label'] ?? 'Item' }}
                        </td>
                        <td class="text-center" style="font-family: Arial,'Segoe UI',sans-serif; color: #3c3c3c; font-size: 13px;">
                            {{ currency_formate($row['amount'] ?? 0) }}</td>
                        <td class="text-center" style="font-family: Arial,'Segoe UI',sans-serif; color: #3c3c3c; font-size: 13px;">
                            {{ $row['qty'] ?? 1 }}</td>
                        <td class="text-right" style="padding-right: 35px; font-family: Arial,'Segoe UI',sans-serif; color: #3c3c3c; font-size: 13px;">
                            {{ currency_formate($row['line_total'] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td colspan="2">
                        <hr width="88%" style="border-top: 0px;" color="#ececec" />
                    </td>
                </tr>
                <tr class="border-style">
                    <th style="text-align: right; width: 70%;" class="spac-top">
                        <h5 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 14px; color: #3c3c3c;">
                            Item Refund Subtotal:</h5>
                    </th>
                    <td style="text-align: center; padding-right: 20px; width: 30%;" class="spac-top">
                        <p style="padding-left: 20px; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; font-weight: 500;">
                            {{ currency_formate($refund['item_total'] ?? 0) }}</p>
                    </td>
                </tr>
                @if (($refund['tax_total'] ?? 0) > 0)
                    <tr>
                        <th style="text-align: right; width: 70%;">
                            <h5 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 14px; color: #3c3c3c;">
                                Related Tax Adjustment Refund:</h5>
                        </th>
                        <td style="text-align: center; padding-right: 20px; width: 30%;">
                            <p style="padding-left: 20px; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px; font-weight: 500;">
                                {{ currency_formate($refund['tax_total'] ?? 0) }}</p>
                        </td>
                    </tr>
                @endif
                <tr>
                    <th style="text-align: right; width: 70%;" class="spac-btm">
                        <h5 style="font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; font-size: 16px; color: #3c3c3c;">
                            Total Refunded:</h5>
                    </th>
                    <td style="text-align: center; padding-right: 20px; width: 30%;" class="spac-btm">
                        <p style="padding-left: 20px; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 16px; font-weight: 700;">
                            {{ currency_formate($refund['grand_total'] ?? 0) }}</p>
                    </td>
                </tr>
            </tbody>
        </table>

        @if (!empty($refund['lines']))
            <table class="section-table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <hr width="88%" style="border-top: 0px;" color="#ececec" />
                        </td>
                    </tr>
                    <tr>
                        <td class="section-padding" style="padding-top: 10px; padding-bottom: 20px;">
                            <h5 style="padding-left: 24px; font-weight: 700; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 14px;">
                                Refund Summary</h5>
                            @foreach ($refund['lines'] as $line)
                                <p style="padding-left: 24px; margin: 4px 0 0 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 13px; font-weight: 500; line-height: 1.55;">
                                    {{ $line }}</p>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <table class="section-table">
            <tbody>
                <tr>
                    <td>
                        <hr width="88%" style="border-top: 0px;" color="#ececec" />
                    </td>
                </tr>
                <tr>
                    <td class="section-padding" style="width: 100%; padding-top: 20px; padding-bottom: 28px; font-size: 15px;">
                        <p style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; color: #4d4d4d; font-size: 13px; font-weight: 500; line-height: 1.65;">
                            If you have questions about this refund, please contact us. This email serves as your
                            refund receipt for your records.</p>

                        <p style="margin: 16px 0 0 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 13px; font-weight: 500;">
                            Thank You,
                        </p>
                        <p style="margin: 10px 0 0 0; font-family: Arial, 'Segoe UI', sans-serif; color: #3c3c3c; font-size: 13px; font-weight: 500;">
                            {{ $invoice_info->store_legal_name ?? '' }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="section-table">
            <tbody>
                <tr>
                    <td style="padding: 20px 24px 30px 24px; text-align: center; border-top: 1px solid #ececec;">
                        <p style="margin: 0; font-family: Arial, 'Segoe UI', sans-serif; font-size: 11px; color: #8f8f8f; line-height: 1.6;">
                            Booostr's website is powered by
                            <a href="https://booostr.co/" style="color: #8f8f8f; text-decoration: underline;">Booostr</a>.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
