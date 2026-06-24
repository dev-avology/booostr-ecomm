<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Ticket Cancelled' }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f3f3;font-family:Arial,Helvetica,sans-serif;color:#3c3c3c;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f3f3;">
    <tr>
        <td align="center" style="padding:24px 16px;">

            <table width="700" cellpadding="0" cellspacing="0" style="max-width:700px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;">

                <tr>
                    <td style="padding:20px 24px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="left" style="vertical-align:middle;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            @if(!empty($club_logo))
                                            <td style="padding-right:12px;">
                                                <img src="{{ $club_logo }}" width="56" height="56" alt="{{ $club_name }}" style="border-radius:50%;display:block;">
                                            </td>
                                            @endif
                                            <td style="font-size:22px;font-weight:bold;color:#000;">
                                                {{ $club_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <a href="{{ $login_url }}" style="color:#888;font-size:14px;text-decoration:none;">Login</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 20px 24px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#d8f8e6;border-radius:6px;">
                            <tr>
                                <td style="padding:18px 20px;font-size:18px;font-weight:700;color:#222;text-align:center;line-height:1.45;">
                                    Your Ticket <span style="font-weight:700;">{{ $ticket_title }}</span> is Canceled and will be Refunded
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 28px 24px 28px;font-size:14px;line-height:1.7;color:#444;">

                        <p style="margin:0 0 18px 0;">Hello <strong>{{ $first_name }}</strong></p>

                        <p style="margin:0 0 18px 0;">
                            We are letting you know that your ticket &ldquo;{{ $ticket_title }}&rdquo; has been cancelled and the base amount will be automatically refunded.
                            If you feel this cancellation and refund was completed in error please contact us at:
                            @if(!empty($club_email))
                                <a href="mailto:{{ $club_email }}" style="color:#08bff3;text-decoration:underline;">{{ $club_email }}</a>
                            @else
                                our club support email
                            @endif
                        </p>

                        <p style="margin:0 0 8px 0;"><strong>Ticket ID:</strong> {{ $ticket_id }}</p>

                        <p style="margin:0 0 8px 0;">
                            <strong>Ticket Amount:</strong> {{ $ticket_amount }}
                            <span style="color:#888;font-size:12px;"> (including service fee)</span>
                        </p>

                        <p style="margin:0 0 18px 0;">
                            <strong>Refund Amount:</strong> {{ $refund_amount }}
                            <span style="color:#888;font-size:12px;"> (service fees are not refundable)</span>
                        </p>

                        @if(!empty($refund_tax))
                        <p style="margin:0 0 18px 0;">
                            <strong>Refund Tax:</strong> {{ $refund_tax }}
                        </p>
                        @endif

                        @if(!empty($order_refund_total))
                        <p style="margin:0 0 18px 0;">
                            <strong>Total Order Refund Amount:</strong> {{ $order_refund_total }}
                        </p>
                        @endif

                        @if(!empty($refund_reference_id))
                        <p style="margin:0 0 18px 0;">
                            <strong>Refund Reference ID:</strong> {{ $refund_reference_id }}
                        </p>
                        @endif

                        <p style="margin:0 0 18px 0;">
                            This ticket was purchased as part of order:
                            <a href="{{ $order_url }}" style="color:#08bff3;text-decoration:underline;font-weight:700;">{{ $order_number }}</a>
                        </p>

                        <p style="margin:0 0 18px 0;">
                            You can login or create an account with this email on our website at any time to view your order history with {{ $club_name }}:
                            <a href="{{ $login_url }}" style="color:#08bff3;text-decoration:underline;font-weight:700;">LOG IN</a>
                        </p>

                        <p style="margin:0 0 18px 0;">
                            Please note that refunds can take up to 7 days to process depending on the day and time the refund is processed. If you have any questions regarding your refund, please contact us.
                        </p>

                        <p style="margin:0;">
                            Thank you!<br>
                            The <strong>{{ $club_name }}</strong> Team
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 24px 24px 24px;text-align:center;font-size:12px;color:#999;border-top:1px solid #eee;">
                        Booostr&rsquo;s website is powered by <a href="https://booostr.co/" style="color:#08bff3;text-decoration:underline;">Booostr</a>.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
