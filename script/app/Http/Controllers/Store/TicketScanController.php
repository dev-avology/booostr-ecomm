<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTicket;
use Illuminate\Support\Str;

class TicketScanController extends Controller
{
       public function scan($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->first();
    
        if (!$ticket) {
            return 'Invalid Ticket';
        }
    
        if ($ticket->status == 'used') {
            return 'Ticket Already Used';
        }
    
        if ($ticket->status == 'cancelled') {
            return 'Ticket Cancelled';
        }
    
        $ticket->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    
        return view('ticket.scan-successs', compact('ticket'));
    }
    
    public function calendar($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
    
        $title = $ticket->event_name ?? 'Event Ticket';
        $description = 'Ticket ID: ' . $ticket->ticket_uuid . '\nAttendee: ' . $ticket->attendee_name;
        $location = $ticket->event_location ?? 'Event Location';
    
        $start = $ticket->event_start_at
            ? \Carbon\Carbon::parse($ticket->event_start_at)->utc()->format('Ymd\THis\Z')
            : now()->utc()->format('Ymd\THis\Z');
    
        $end = $ticket->event_end_at
            ? \Carbon\Carbon::parse($ticket->event_end_at)->utc()->format('Ymd\THis\Z')
            : now()->addHours(2)->utc()->format('Ymd\THis\Z');
    
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Booostr//Ticket Calendar//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:ticket-{$ticket->ticket_uuid}@booostr\r\n";
        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics .= "SUMMARY:{$title}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        $ics .= "LOCATION:{$location}\r\n";
        $ics .= "DTSTART:{$start}\r\n";
        $ics .= "DTEND:{$end}\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
    
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ticket-' . $ticket->ticket_uuid . '.ics"',
        ]);
    }
    
    public function print($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
    
        $tickets = EventTicket::where('order_id', $ticket->order_id)->get();
    
        return view('ticket.print', compact('tickets'));
    }

}
