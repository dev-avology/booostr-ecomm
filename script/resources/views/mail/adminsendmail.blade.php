<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Store Alert' }}</title>
</head>
<body style="margin: 0; padding: 35px 15px; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #333333; -webkit-font-smoothing: antialiased;">
@php
    $orderModel = null;
    $orderData = [];

    if ($data instanceof \App\Models\Order) {
        $orderModel = $data;
        $orderData = $data->toArray();
    } elseif (is_array($data)) {
        if (isset($data['data']) && $data['data'] instanceof \App\Models\Order) {
            $orderModel = $data['data'];
            $orderData = $orderModel->toArray();
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $orderData = $data['data'];
        } else {
            $orderData = $data;
        }

        $orderId = (int) ($orderData['id'] ?? 0);
        if ($orderId > 0 && !$orderModel) {
            $orderModel = \App\Models\Order::with(['ordermeta', 'user', 'orderlasttrans', 'orderstatus'])->find($orderId);
        }
    }

    $invoiceNo = $orderData['invoice_no'] ?? ($orderModel->invoice_no ?? '');
    $statusId = (int) ($orderData['status_id'] ?? ($orderModel->status_id ?? 0));
    $paymentStatus = (int) ($orderData['payment_status'] ?? ($orderModel->payment_status ?? 0));

    // Determine mailType if not provided
    if (empty($mailType)) {
        if ($statusId === 2 || $paymentStatus === 5) {
            $mailType = 'cancelled';
        } elseif ($paymentStatus === 4) {
            $mailType = 'authorized_not_captured';
        } else {
            $mailType = 'captured';
        }
    }

    // Resolve Club / Store name
    $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
    $invoiceInfo = $data['invoice_data'] ?? get_option('invoice_data', true);
    $clubDisplayName = trim((string) ($clubInfo['club_name'] ?? ''));
    if ($clubDisplayName === '') {
        $clubDisplayName = trim((string) ($invoiceInfo->store_legal_name ?? ''));
    }
    if ($clubDisplayName === '') {
        $clubDisplayName = trim((string) (tenant('store_name') ?? 'Booostr'));
    }
    if ($clubDisplayName === '') {
        $clubDisplayName = 'Booostr';
    }

    // Dates
    $placedAt = $orderData['placed_at'] ?? ($orderModel->placed_at ?? ($orderData['created_at'] ?? ($orderModel->created_at ?? now())));
    $orderDate = \Carbon\Carbon::parse($placedAt)->setTimezone(config('app.timezone'))->format('m/d/Y');

    $refundedAt = $orderModel->refunded_at ?? ($orderData['refunded_at'] ?? ($orderModel->updated_at ?? ($orderData['updated_at'] ?? now())));
    $cancelDate = \Carbon\Carbon::parse($refundedAt)->setTimezone(config('app.timezone'))->format('m/d/Y');

    // Customer Name & Email
    $ordermetaArr = [];
    if ($orderModel && $orderModel->ordermeta) {
        $ordermetaArr = json_decode($orderModel->ordermeta->value ?? '{}', true) ?: [];
    } elseif (!empty($orderData['ordermeta'])) {
        $metaVal = is_array($orderData['ordermeta']) ? ($orderData['ordermeta']['value'] ?? '{}') : (optional($orderData['ordermeta'])->value ?? '{}');
        $ordermetaArr = json_decode((string) $metaVal, true) ?: [];
    }
    $customerName = trim((string) ($ordermetaArr['name'] ?? ($orderModel->user->name ?? ($orderData['user']['name'] ?? 'Guest User'))));
    if ($customerName === '') {
        $customerName = 'Guest User';
    }
    $customerEmail = trim((string) ($ordermetaArr['email'] ?? ($orderModel->user->email ?? ($orderData['user']['email'] ?? ''))));

    // Totals
    $totalAmount = (float) ($orderData['total'] ?? ($orderModel->total ?? 0));
    $refundTotal = $totalAmount;
    if ($orderModel) {
        $lastTransVal = optional($orderModel->orderlasttrans)->value;
        if (!empty($lastTransVal)) {
            $decodedTrans = json_decode($lastTransVal, true) ?: [];
            $refundCents = $decodedTrans['amount_refunded'] ?? ($decodedTrans['amount'] ?? 0);
            if ($refundCents > 0) {
                $refundTotal = (float) ($refundCents / 100);
            }
        }
    }

    // Header Logo & URLs
    $clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : null;
    if (empty($clubLogo) && function_exists('tenant') && !empty(tenant('logo'))) {
        $clubLogo = asset(tenant('logo'));
    }
    if (empty($clubLogo)) {
        $clubLogo = 'https://staging3.booostr.co/wp-content/uploads/2022/03/booostr-logo-long-top-header.png';
    }

    $loginUrl = env('WP_CLUB_URL', 'https://app.booostr.co/') . 'login';
    $clubId = function_exists('tenant') ? (tenant('club_id') ?? '') : '';
    $manageOrdersUrl = !empty($clubId)
        ? (env('WP_CLUB_URL', 'https://app.booostr.co/') . 'dashboard/?ua=storemanager&item=' . $clubId)
        : (env('WP_CLUB_URL', 'https://app.booostr.co/') . 'dashboard/?ua=storemanager');
@endphp

<table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 580px; width: 100%; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
    <tr>
        <td style="padding: 28px 36px 36px 36px;">
            <!-- Header: Logo & Login Link -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
                <tr>
                    <td align="left" style="vertical-align: middle;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 10px;">
                                    <img src="{{ $clubLogo }}" alt="{{ $clubDisplayName }}" style="max-height: 42px; max-width: 140px; display: block; border: 0;" />
                                </td>
                                <td style="vertical-align: middle; font-size: 18px; font-weight: 700; color: #111111; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                    {{ $clubDisplayName }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td align="right" style="vertical-align: middle;">
                        <a href="{{ $loginUrl }}" target="_blank" style="color: #777777; font-size: 13px; font-weight: 500; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">Login</a>
                    </td>
                </tr>
            </table>

            <hr style="border: 0; border-top: 1px solid #e8e8e8; margin: 12px 0 24px 0;" />

            <!-- Hero Heading -->
            @if ($mailType === 'cancelled')
                <h2 style="font-size: 21px; font-weight: 700; color: #111111; text-align: center; margin: 0 0 24px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    Order #{{ $invoiceNo }} Has Been Cancelled
                </h2>
            @elseif ($mailType === 'authorized_not_captured')
                <h2 style="font-size: 21px; font-weight: 700; color: #111111; text-align: center; margin: 0 0 24px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    We Received A New Order - Please CAPTURE Payment
                </h2>
            @else
                <h2 style="font-size: 21px; font-weight: 700; color: #111111; text-align: center; margin: 0 0 24px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    We Received A New Order - Payment is Captured
                </h2>
            @endif

            <hr style="border: 0; border-top: 1px solid #e8e8e8; margin: 0 0 24px 0;" />

            <!-- Salutation -->
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #333333; line-height: 1.5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                Hello,
            </p>

            <!-- Body Message -->
            @if ($mailType === 'cancelled')
                <p style="margin: 0 0 16px 0; font-size: 14px; color: #333333; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    This is an automated alert to confirm you have successfully cancelled and refunded order #{{ $invoiceNo }} from your store. If the order was purchased with a credit card via Booostr/Stripe - the funds will automatically be refunded within 8-10 business days. If the purchase was made with Cash/Check or other method offline from Booostr - you will need to refund the order manually.
                </p>
                <p style="margin: 0 0 24px 0; font-size: 14px; color: #333333; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    To view the order details or work with additional orders, <a href="{{ $manageOrdersUrl }}" target="_blank" style="color: #0088cc; text-decoration: underline;">login to your account to here</a>.
                </p>
            @elseif ($mailType === 'authorized_not_captured')
                <p style="margin: 0 0 16px 0; font-size: 14px; color: #333333; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    This is an automated alert to let you know you have received an new order. This order has it's payment Authorized, but NOT CAPTURED. This could be because you have automatic capture turned off, OR the order has an elevated fraud risk score.
                </p>
                <p style="margin: 0 0 24px 0; font-size: 14px; color: #333333; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    You will need to capture the payment for the order within 48 hours as the authorization could drop off after that and the payment will not be able to be captured. To capture payment and manage the order, <a href="{{ $manageOrdersUrl }}" target="_blank" style="color: #0088cc; text-decoration: underline;">login to your account to here</a>.
                </p>
            @else
                <p style="margin: 0 0 24px 0; font-size: 14px; color: #333333; line-height: 1.6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                    This is an automated alert to let you know you have received an new order. Because you have automatic processing turned ON, the order was low fraud risk and payment has been captured. If you need to fulfill the order, <a href="{{ $manageOrdersUrl }}" target="_blank" style="color: #0088cc; text-decoration: underline;">login to your account to manage the order</a>.
                </p>
            @endif

            <!-- Subheading -->
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #333333; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                The basic order details are below.
            </p>

            <!-- Order Details List -->
            <table cellpadding="0" cellspacing="0" style="margin-bottom: 30px; font-size: 14px; color: #333333; line-height: 1.85; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                <tr>
                    <td style="font-weight: 700; padding-right: 6px;">Order #:</td>
                    <td>{{ $invoiceNo }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; padding-right: 6px;">Order Date:</td>
                    <td>{{ $orderDate }}</td>
                </tr>
                @if ($mailType === 'cancelled')
                    <tr>
                        <td style="font-weight: 700; padding-right: 6px;">Cancel Date:</td>
                        <td>{{ $cancelDate }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; padding-right: 6px;">Payment Status:</td>
                        <td>CANCELLED &amp; REFUNDED</td>
                    </tr>
                @elseif ($mailType === 'authorized_not_captured')
                    <tr>
                        <td style="font-weight: 700; padding-right: 6px;">Payment Status:</td>
                        <td>AUTHORIZED, NOT CAPTURED</td>
                    </tr>
                @else
                    <tr>
                        <td style="font-weight: 700; padding-right: 6px;">Payment Status:</td>
                        <td>AUTHORIZED, AND CAPTURED</td>
                    </tr>
                @endif
                <tr>
                    <td style="font-weight: 700; padding-right: 6px;">Ordered by:</td>
                    <td>{{ $customerName }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; padding-right: 6px;">Order Email:</td>
                    <td>{{ $customerEmail }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700; padding-right: 6px;">Order Total:</td>
                    <td>${{ number_format($totalAmount, 2) }}</td>
                </tr>
                @if ($mailType === 'cancelled')
                    <tr>
                        <td style="font-weight: 700; padding-right: 6px;">Refund Total:</td>
                        <td>${{ number_format($refundTotal, 2) }}</td>
                    </tr>
                @endif
            </table>

            <!-- Center Button -->
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 30px auto;">
                <tr>
                    <td align="center" style="background-color: #0088cc; border-radius: 5px;">
                        <a href="{{ $manageOrdersUrl }}" target="_blank" style="display: inline-block; padding: 12px 28px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 5px;">
                            Login &amp; Manage Orders
                        </a>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <p style="text-align: center; font-size: 11px; color: #999999; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                powered by <a href="https://booostr.co" target="_blank" style="color: #999999; text-decoration: underline;">Booostr</a>
            </p>
        </td>
    </tr>
</table>
</body>
</html>