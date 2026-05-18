<!DOCTYPE html>
<html>
<head>
    <title>Event Ticket</title>
</head>
<body style="font-family: Arial;background:#f5f5f5;padding:20px;">

<div style="max-width:700px;margin:auto;background:white;padding:30px;">

    <h2 style="text-align:center;">
        Your Tickets are Below for:
        <br>
        {{ $item->term->title }}
    </h2>

    <p>
        Hello {{ $order->name }}
    </p>

    <p>
        Thank you for your ticket purchase.
    </p>

    <hr>

    <table width="100%">
        <tr>

        <td width="220">
    <img
        src="data:image/svg+xml;base64,{{ $qrImage }}"
        width="200">
</td>
            <td>

                <h2>
                    {{ $item->term->title }}
                </h2>

                <p>
                    Entry Valid For:
                </p>

                <p>
                    March 20, 2026
                </p>

                <p>
                    Reference #:
                    {{ $ticketUuid }}
                </p>

            </td>

        </tr>
    </table>

</div>

</body>
</html>