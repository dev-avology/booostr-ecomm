<?php

namespace App\Services;

use App\Mail\TicketCancelledRefundMail;
use App\Models\EventTicket;
use App\Models\Getway;
use App\Models\Orderitem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Refund;
use Stripe\Stripe;

class TicketCancelRefundService
{
    public function handle(EventTicket $ticket): void
    {
        if ($ticket->status === 'cancelled') {
            throw new \RuntimeException('This ticket is already cancelled.');
        }

        $ticket->loadMissing(['order.ordermeta', 'order.getway']);

        $order = $ticket->order;
        if (!$order) {
            throw new \RuntimeException('Order not found for this ticket.');
        }

        $orderItem = Orderitem::with('term')->find($ticket->order_item_id);
        if (!$orderItem) {
            throw new \RuntimeException('Order item not found for this ticket.');
        }

        $amounts = $this->resolveTicketAmounts($orderItem);
        $this->processStripeRefund($order, $amounts['refund_amount']);
        $this->sendCustomerEmail($ticket, $order, $orderItem, $amounts);
    }

    protected function resolveTicketAmounts(Orderitem $orderItem): array
    {
        $itemInfo = json_decode($orderItem->info ?? '{}', true);
        $ticketFee = (float) ($itemInfo['ticket_fee'] ?? 0.75);
        $ticketAmount = max(0, (float) $orderItem->amount);
        $refundAmount = max(0, round($ticketAmount - $ticketFee, 2));

        return [
            'ticket_amount' => $ticketAmount,
            'refund_amount' => $refundAmount,
            'ticket_fee' => $ticketFee,
        ];
    }

    protected function processStripeRefund($order, float $refundAmount): void
    {
        if ($refundAmount <= 0) {
            throw new \RuntimeException('Invalid ticket refund amount.');
        }

        if (empty($order->transaction_id)) {
            throw new \RuntimeException('No Stripe transaction found for this order.');
        }

        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', 'App\Lib\Stripe')
            ->first();

        if (!$gateway) {
            throw new \RuntimeException('Stripe payment gateway is not configured.');
        }

        $gatewayData = json_decode($gateway->data ?? '{}');
        $secretKey = $gateway->test_mode == 1
            ? ($gatewayData->test_secret_key ?? null)
            : ($gatewayData->secret_key ?? null);

        if (empty($secretKey)) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        Stripe::setApiKey($secretKey);

        $chargeId = $this->resolveChargeId($order->transaction_id);

        Refund::create([
            'charge' => $chargeId,
            'amount' => (int) round($refundAmount * 100),
            'refund_application_fee' => false,
            'reverse_transfer' => true,
        ]);
    }

    protected function resolveChargeId(string $transactionId): string
    {
        if (str_starts_with($transactionId, 'pi_')) {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);

            if (($paymentIntent->status ?? '') !== 'succeeded') {
                throw new \RuntimeException('Payment is not in a refundable state.');
            }

            if (empty($paymentIntent->latest_charge)) {
                throw new \RuntimeException('No charge found for this payment.');
            }

            return $paymentIntent->latest_charge;
        }

        if (str_starts_with($transactionId, 'ch_')) {
            return $transactionId;
        }

        throw new \RuntimeException('Invalid Stripe transaction ID format.');
    }

    protected function sendCustomerEmail(EventTicket $ticket, $order, Orderitem $orderItem, array $amounts): void
    {
        $email = trim((string) ($ticket->attendee_email ?? ''));
        if ($email === '' || $email === '-') {
            $orderMeta = json_decode(optional($order->ordermeta)->value ?? '{}');
            $email = trim((string) ($orderMeta->email ?? ''));
        }

        if ($email === '' || $email === '-') {
            Log::warning('Ticket cancel email skipped: missing customer email.', [
                'ticket_id' => $ticket->id,
                'order_id' => $order->id,
            ]);
            return;
        }

        $fullName = trim((string) ($ticket->attendee_name ?? 'Customer'));
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? 'Customer';

        $clubInfo = function_exists('tenant_club_info') ? tenant_club_info() : [];
        $invoiceData = get_option('invoice_data', true);
        $clubName = $clubInfo['club_name']
            ?? ($invoiceData->store_legal_name ?? 'Club');
        $clubEmail = $clubInfo['club_email']
            ?? $clubInfo['email']
            ?? ($invoiceData->store_legal_email ?? '');
        $clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : null;
        $ticketTitle = $orderItem->term->title ?? ($ticket->event_name ?? 'Event Ticket');

        $mailData = [
            'subject' => 'Your Ticket ' . $ticketTitle . ' is Canceled and will be Refunded',
            'first_name' => $firstName,
            'ticket_title' => $ticketTitle,
            'ticket_id' => $ticket->ticket_uuid,
            'ticket_amount' => $this->formatMoney($amounts['ticket_amount']),
            'refund_amount' => $this->formatMoney($amounts['refund_amount']),
            'order_number' => $order->invoice_no ?? str_pad($order->id, 7, '0', STR_PAD_LEFT),
            'order_url' => url('/customer/order/' . $order->id),
            'login_url' => 'https://booostr.co/main-login/',
            'club_name' => $clubName,
            'club_email' => $clubEmail,
            'club_logo' => $clubLogo,
        ];

        try {
            Mail::to($email)->send(new TicketCancelledRefundMail($mailData));
        } catch (\Throwable $e) {
            Log::error('Ticket cancel refund email failed.', [
                'ticket_id' => $ticket->id,
                'order_id' => $order->id,
                'email' => $email,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function formatMoney(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }
}
