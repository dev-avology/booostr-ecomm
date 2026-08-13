<?php

namespace App\Services;

use App\Models\EventTicket;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EventTicketEmailService
{
    /**
     * Send event ticket email(s) for an order — one email per ticket line item,
     * with qty × individual QR tickets (same as /checkout/payment processPayment).
     */
    public function sendForOrder(Order $order, string $email): bool
    {
        try {
            $order->loadMissing(['orderitems.term', 'ordermeta']);

            foreach ($order->orderitems as $item) {
                $info = json_decode($item->info ?? '{}', true);

                if (($info['product_kind'] ?? '') !== 'event_ticket') {
                    continue;
                }

                $tickets = [];

                $orderInfo = optional($order->ordermeta)->value
                    ? json_decode($order->ordermeta->value, true)
                    : [];

                $termMeta = DB::table('termmetas')
                    ->where('term_id', $item->term_id)
                    ->whereIn('key', ['ticket_sale_start', 'ticket_sale_end'])
                    ->pluck('value', 'key');

                $eventStart = $termMeta['ticket_sale_start'] ?? null;
                $eventEnd = $termMeta['ticket_sale_end'] ?? null;

                $clubLogo = tenant_club_logo();

                for ($i = 1; $i <= (int) $item->qty; $i++) {
                    $ticketUuid = Str::uuid()->toString();

                    EventTicket::create([
                        'ticket_uuid' => $ticketUuid,
                        'order_id' => $order->id,
                        'order_item_id' => $item->id ?? null,
                        'term_id' => $item->term_id ?? null,
                        'attendee_name' => $orderInfo['name'] ?? null,
                        'attendee_email' => $email,
                        'attendee_phone' => $orderInfo['phone'] ?? null,
                        'event_name' => $item->term->title ?? 'Event Ticket',
                        'event_start_at' => $eventStart ? date('Y-m-d H:i:s', strtotime($eventStart)) : null,
                        'event_end_at' => $eventEnd ? date('Y-m-d H:i:s', strtotime($eventEnd)) : null,
                        'event_date' => $eventStart ? date('Y-m-d', strtotime($eventStart)) : null,
                        'event_time' => $eventStart ? date('h:i A', strtotime($eventStart)) : null,
                        'event_location' => null,
                        'status' => 'active',
                    ]);

                    $scanUrl = url('/ticket/scan/' . $ticketUuid);
                    $qrPng = ticket_email_qr_png_base64($scanUrl, $clubLogo);
                    ticket_email_qr_save_file($ticketUuid, $qrPng);

                    $tickets[] = [
                        'ticketUuid' => $ticketUuid,
                        'qrPng' => $qrPng,
                        'qrImageUrl' => ticket_email_qr_public_url($ticketUuid),
                    ];
                }

                if ($tickets === []) {
                    continue;
                }

                $clubInfo = tenant_club_info();
                $clubName = $clubInfo['club_name'] ?? 'Club';
                $clubLogo = tenant_club_logo();
                $clubEmail = $clubInfo['email']
                    ?? $clubInfo['club_email']
                    ?? $clubInfo['default_email']
                    ?? $clubInfo['contact_email']
                    ?? '';

                $mailViewData = [
                    'order' => $order,
                    'item' => $item,
                    'tickets' => $tickets,
                    'clubName' => $clubName,
                    'clubLogo' => $clubLogo,
                    'clubEmail' => $clubEmail,
                    'qrEmbedSrc' => [],
                ];

                Mail::send(
                    'email.event-ticket-template',
                    $mailViewData,
                    function ($message) use ($email, $item, $clubName, &$mailViewData, $tickets) {
                        $ticketTitle = $item->term->title ?? 'Event';

                        $message->to($email)
                            ->subject('Your ' . $clubName . ' - ' . $ticketTitle . ' Tickets are inside');

                        if (function_exists('apply_store_receipt_mail_identity')) {
                            apply_store_receipt_mail_identity($message);
                        }

                        foreach ($tickets as $index => $ticket) {
                            if (empty($ticket['qrPng'])) {
                                continue;
                            }

                            $pngBinary = base64_decode($ticket['qrPng'], true);
                            if ($pngBinary === false || $pngBinary === '') {
                                continue;
                            }

                            $mailViewData['qrEmbedSrc'][$ticket['ticketUuid']] = $message->embedData(
                                $pngBinary,
                                'ticket-qr-' . $index . '.png',
                                'image/png'
                            );
                        }
                    }
                );
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Event ticket mail failed', [
                'email' => $email,
                'order_id' => $order->id ?? null,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
