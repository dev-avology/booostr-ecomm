<?php

namespace App\Observers;

use App\Models\EventTicket;
use App\Services\ProductSalesCrmSyncService;
use Illuminate\Support\Facades\Log;

class EventTicketObserver
{
    public function created(EventTicket $ticket): void
    {
        try {
            app(ProductSalesCrmSyncService::class)->handleEventTicketCreated($ticket);
        } catch (\Throwable $e) {
            Log::warning('Product sales CRM sync after ticket creation failed', [
                'ticket_id' => $ticket->id,
                'term_id' => $ticket->term_id,
                'order_id' => $ticket->order_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
