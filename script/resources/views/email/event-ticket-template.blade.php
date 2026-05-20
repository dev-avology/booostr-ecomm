<!DOCTYPE html>
<html>

<head>
    <title>Event Ticket</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

@php
$orderInfo = optional($order->ordermeta)->value
? json_decode($order->ordermeta->value, true)
: [];

$customerName = $orderInfo['name'] ?? 'Customer';

$productTitle = $item->term->title ?? 'Event Ticket';

$purchaseDate = optional($order->created_at)->format('m/d/Y') ?? date('m/d/Y');

$invoiceNo = $order->invoice_no ?? $ticketUuid;

$eventStart = DB::table('termmetas')
    ->where('term_id', $item->term_id)
    ->where('key', 'ticket_sale_start')
    ->value('value');
@endphp

<body style="margin:0; padding:0; background:#f3f3f3; font-family:Roboto, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f3f3;">
        <tr>
            <td align="center" style="padding:20px;">

                <!-- MAIN CARD -->
                <table width="700" cellpadding="0" cellspacing="0"
                    style="max-width:700px; width:100%; background:#ffffff; border-radius:12px;">

                    <!-- HEADER -->
                    <tr>
                        <td style="padding:20px 25px;">
                            <table width="100%">
                                <tr>

                                        <td align="left">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                        
                                                    <td>
                                                        <img src="{{ $clubLogo }}"
                                                            width="90"
                                                            height="90"
                                                            alt="{{ $clubName }}"
                                                            style="border-radius:50%;">
                                                    </td>
                                        
                                                    <td style="padding-left:15px; font-size:22px; font-weight:bold; color:#000;">
                                                        {{ $clubName }}
                                                    </td>
                                        
                                                </tr>
                                            </table>
                                        </td>

                                    <td align="right">
                                        <a href="https://booostr.co/main-login/"
                                            style="text-decoration:none; color:#888; font-size:14px;">
                                            Login
                                        </a>
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- BLUE TITLE BAR -->
                    <tr>
                        <td style="padding:0 25px 20px 25px;">

                            <table width="100%" style="background:#e0f9ff; border-radius:6px;">

                                <tr>
                                    <td align="center" style="padding:20px;">

                                        <p style="margin:0; font-size:20px;color:#000;">
                                            Your Tickets are Below for:
                                        </p>

                                        <h2 style="margin:5px 0 0 0; font-size:22px;">
                                            {{ $productTitle }}
                                        </h2>

                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- GREETING -->
                    <tr>
                        <td style="padding:0 25px;">

                            <h4 style="margin-bottom:5px; font-size:14px; color:#000;">
                                Hello {{ $customerName }}
                            </h4>

                            <p style="font-size:14px; color:#000; margin-bottom:0;">
                                Thank you for your ticket purchase for <strong>{{ $productTitle }}</strong>.
                                We are really excited to have you at our event.
                                Your printable tickets are below.
                                Please reach out to us at
                                <a href="mailto:{{ $clubEmail }}" style="color:#0a8ddf;">
                                    {{ $clubEmail }}
                                </a>
                                with any questions or issues.
                            </p>
                            
                            <p style="font-size:14px; color:#000; margin:0;">Thank you again,</p>

                            <h4 style="margin-top:10px; font-size:14px; color:#000;">
                                The {{ $clubName }} Team
                            </h4>

                        </td>
                    </tr>

                    <!-- DIVIDER -->
                    <tr>
                        <td style="padding:15px 25px;">
                            <hr style="border:none; border-top:2px solid #ddd;">
                        </td>
                    </tr>

                    
                    <!-- TICKET BLOCK -->
                    @foreach($tickets as $ticket)
                    <tr>
                        <td style="padding:0 25px 20px 25px;">
                            <table width="100%">
                                <tr>
                                    <td width="220" align="center" valign="middle">
                                    <img src="{{ $ticket['qrUrl'] }}"
                                        width="180"
                                        height="180"
                                        style="display:block;border:0;"
                                        alt="Ticket QR Code">
                    
                                        <p style="font-size:11px; color:#777;">
                                            Ticket ID: {{ $ticket['ticketUuid'] }}
                                        </p>
                                    </td>
                    
                                    <td valign="top" style="padding-left:10px; text-align:center;">
                                        <h3 style="margin:0 0 5px 0; font-size:20px; color:#000;">
                                            Ticket For: {{ $productTitle }}
                                        </h3>
                    
                                        <h4 style="font-weight:400; font-size:20px; color:#000; margin:0 0 10px 0;">
                                            {{ $productTitle }} Ticket #{{ $loop->iteration }}
                                        </h4>
                    
                                        <span style="font-weight:500; display:inline-block; color:#3b3b3b; margin:0; font-size:20px;">
                                            Entry Valid For:
                                        </span>
                    
                                        <p style="margin:5px 0 0 0; font-size:18px; font-weight:400;">
                                            {{ !empty($eventStart) ? date('F d, Y', strtotime($eventStart)) : '' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                                        <!-- INSTRUCTIONS BOX -->
                    <tr>
                        <td style="padding:10px 25px;">

                            <table width="100%" style="background:#efefef; border-radius:6px;">

                                <tr>
                                    <td style="padding:15px 25px; font-size:13px; color:#444; line-height:22px;">

                                        <h3 style="font-size:18px; margin:0; color:#000;">Ticket Instructions:</h3>
                                    <p style="font-size:15px; color:#000;">Valid for a single admission for dates on ticket.  Not valid for re-entry.  VIP entry gives holder full
                                        access to entire venue, backstage access and special VIP area.
                                        <br>
                                        <br>
                                        *Please arrive before the last hour of the event.
                                        </p>

                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- PURCHASE INFO -->
                    <tr>
                        <td style="padding:0 25px; font-size:14px; color:#000;">

                            <p style="padding:0 40px; text-align:center;margin:0;">

                                <strong style="font-size:18px;
                               font-weight:500;
                               color:#000;
                               padding-right:20px;">
                                    Purchase Info:
                                </strong>

                                <span style="padding-right:20px;">
                                    {{ $purchaseDate }} by {{ $customerName }}
                                </span>

                                <span style="padding-right:20px;">
                                   Reference #: {{ substr($ticket['ticketUuid'], 0, 8) }}
                                </span>

                            </p>

                        </td>
                    </tr>
                    
                     <!-- DIVIDER -->
                    <tr>
                        <td style="padding:15px 25px;">
                            <hr style="border:none; border-top:2px solid #ddd;">
                        </td>
                    </tr>
                  


                    <!-- ACTION BUTTONS -->
                    <tr>
                        <td align="center" style="padding:20px; overflow: hidden;">
                            <table style="width:100%;">
                    <tr>
                        <td>
                             <a href="{{ url('/ticket/' . $ticket['ticketUuid'] . '/print') }}"
                                style="display:inline-block; padding:5px 30px; border:1px solid #0a8ddf; color:#0a8ddf; text-decoration:none; border-radius:4px; overflow: hidden; cursor: pointer; font-size:14px;">
                                <img src="https://booostr.site/assets/landlord/uploads/media-uploader/thumb/print-icon.png" width="25px">
                                <span
                                    style="display: inline-block; float: right; margin-left: 10px; margin-top: 5px;">Print</span>
                            </a>
                        </td>
                        

                        <td style="text-align: right;">
                            <div>
                            <a href="{{ url('/apple-wallet/' . $ticket['ticketUuid']) }}"
                                    style="display:inline-block; margin-right:20px;">
                                    <img src="https://booostr.site/assets/landlord/uploads/media-uploader/thumb/apple-wallet.png" style="max-width:130px;">
                                </a>

                                <a href="{{ url('/ticket/google-wallet/' . $ticket['ticketUuid']) }}"
                                    style="display:inline-block;">
                                    <img src="https://booostr.site/assets/landlord/uploads/media-uploader/thumb/goggle-wallet.png"
                                        style="max-width:140px;">
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
                @endforeach
        <!-- DIVIDER -->
        <tr>
            <td style="padding:0px 25px;">
                <hr style="border:none; border-top:2px solid #ddd;">
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td align="center" style="padding:20px; font-size:12px; color:#aaa;">
                powered by <a href="#" style="text-decoration:underline; color: inherit;">Booostr</a>
            </td>
        </tr>

    </table>

    </td>
    </tr>
    </table>

</body>

</html>