<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTicket;

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
    
        return view('ticket.scan-success', compact('ticket'));
    }
}
