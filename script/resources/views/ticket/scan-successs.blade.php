<!DOCTYPE html>
<html>
<head>
    <title>Ticket Scan Result</title>
</head>
<body style="font-family: Arial; background:#f4f4f4; padding:30px;">

    <div style="max-width:600px; margin:auto; background:#fff; padding:25px; border-radius:10px;">
        <h2 style="color:green;">Entry Allowed</h2>

        <p><strong>Ticket ID:</strong> {{ $ticket->ticket_uuid }}</p>
        <p><strong>Name:</strong> {{ $ticket->attendee_name }}</p>
        <p><strong>Email:</strong> {{ $ticket->attendee_email }}</p>
        <p><strong>Phone:</strong> {{ $ticket->attendee_phone }}</p>
        <p><strong>Event:</strong> {{ $ticket->event_name }}</p>
        <p><strong>Date:</strong> {{ $ticket->event_date }}</p>
        <p><strong>Time:</strong> {{ $ticket->event_time }}</p>
        <p><strong>Status:</strong> {{ $ticket->status }}</p>
        <p><strong>Used At:</strong> {{ $ticket->used_at }}</p>
    </div>

</body>
</html>