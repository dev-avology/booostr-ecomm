<!DOCTYPE html>
<html>
<head>
    <title>Print Ticket</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f5f5f5;
            padding:20px;
        }

        .ticket-box{
            max-width:700px;
            margin:auto;
            background:#fff;
            padding:30px;
            border-radius:10px;
        }

.ticket-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 30px;
    gap:10px;
}

        .ticket-details{
            text-align:center;
            width:100%;
        }

  .ticket-instructions {
    border-radius: 8px;
    margin-top: 20px;
    padding: 15px 25px;
    font-size: 13px;
    color: #444;
    line-height: 22px;
    background: #efefef;
    border-radius: 6px;
}
        .print-btn{
            text-align:center;
            margin-top:20px;
        }
        .ticket-box{
    page-break-inside: avoid;
    break-inside: avoid;
    margin-bottom: 25px;
}

.scn-code svg {
width: 180px;
height: 180px;
}

.ticket-details h2 {
    margin: 9px 0 5px 0;
    font-size: 20px;
    color: #000;
    font-family: "Roboto";
}

.ticket-details h3 {
font-weight: 400;
font-size: 20px;
color: #000;
margin: 12px 0 10px 0;
font-family: "Roboto";
}
.scn-code p {
font-size: 11px;
color: #777;
font-family: "Roboto";
text-align: center;
}

.scn-code p strong {
display: inline;
}

.scn-code p br {
display: none;
}
.ticket-details p strong {
    font-weight: 500;
    display: inline-block;
    color: #3b3b3b;
    margin: 0;
    font-size: 20px;
    font-family: "Roboto";
    padding-bottom: 12px;
    padding-top: 13px;
}
.ticket-details p  {color: #3b3b3b;margin: 5px 0 0 0;font-size: 18px;font-weight: 400;}

.ticket-instructions h3 {
margin: 0;
font-size: 18px;
margin: 0;
color: #000;
font-family: "Roboto";
}

.ticket-instructions p {
font-size: 15px;
color: #000;
font-family: "Roboto";
}
.pur-box {
display: flex;
justify-content: center;
max-width: 580px;
align-items: center;
margin: 0 auto;
gap: 20px;
}

.pur-box p strong {
font-size: 18px;
font-weight: 500;
color: #000;
padding-right: 20px;
font-family: "Roboto";
}

.pur-box p {
font-size: 14px;
color: #000;
font-family: "Roboto";
}

.scn-code{
    width:180px;
}

.qr-wrapper{
    position: relative;
    width: 180px;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.qr-wrapper svg{
    width:180px !important;
    height:180px !important;
    display:block;
}

/* WHITE CENTER CIRCLE */

.qr-logo-circle{
    position:absolute;

    width:50px;
    height:50px;

    background:#fff;
    border-radius:50%;

    top:50%;
    left:50%;

    transform:translate(-50%, -50%);

    display:flex;
    align-items:center;
    justify-content:center;

    z-index:99;

    box-shadow:0 0 0 6px #fff;
}

/* LOGO */

.qr-logo-img{
    width:34px;
    height:34px;
    object-fit:contain;
    display:block;
}

@media print{
    .ticket-box{
        page-break-inside: avoid;
        break-inside: avoid;
    }

    br, hr{
        display:none;
    }
}

        @media print{
            .print-btn{
                display:none;
            }

            body{
                background:#fff;
            }
            
            * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

.ticket-instructions {
    background: #efefef !important;
    border-radius: 8px !important;
    padding: 15px 25px !important;
}
        }
    </style>
</head>

<body>

@foreach($tickets as $ticket)

<div class="ticket-box">

    <div class="ticket-row">

        <div class="scn-code">

    @php
        $clubLogo = tenant_club_logo();
    @endphp

    <div class="qr-wrapper">

        {!! QrCode::size(180)
            ->margin(1)
            ->generate(url('/ticket/scan/' . $ticket->ticket_uuid)) !!}

        @if($clubLogo)

            <div class="qr-logo-circle">
                <img src="{{ $clubLogo }}" class="qr-logo-img">
            </div>

        @endif

    </div>

    <p>
        Ticket ID:
        {{ $ticket->ticket_uuid }}
    </p>

</div>

        <div class="ticket-details">

            <h2>Ticket For: {{ $ticket->event_name }}</h2>

            <h3>{{ $ticket->event_name }} Ticket</h3>

            <p>
                <strong>Entry Valid For:</strong><br>
                {{ $ticket->event_date }}
            </p>



        </div>

    </div>

    <div class="ticket-instructions">

        <h3>Ticket Instructions:</h3>

        <p>
           Valid for a single admission for dates on ticket. Not valid for re-entry. VIP entry gives holder full access to entire venue, backstage access and special VIP area.
        </p>

        <p>
            *Please arrive before the last hour of the event.
        </p>

    </div>

    <div class="pur-center">
        <div class="pur-box">       
        <p><strong>Purchase Info:</strong>
        {{ $ticket->created_at }}</p>
        <p>Reference #:
            {{ substr($ticket->ticket_uuid, 0, 7) }}</p>
        </div>

    </div>



</div>


@endforeach

    <div class="print-btn">
        <button onclick="window.print()">
            Print Ticket
        </button>
    </div>



</body>
<script>
window.onload = function () {
    setTimeout(function () {
        window.print();
    }, 500);
};

window.onafterprint = function () {
     window.close();
};
</script>

</html>
