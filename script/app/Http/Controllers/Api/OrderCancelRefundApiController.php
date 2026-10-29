<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Seller\OrderController;
use App\Lib\NotifyToUser;
use App\Models\Getway;
use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\User;
use App\Services\RefundReceiptEmailService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;

/**
 * Additive Order Refund & Cancellation API.
 * Supports full refund and cancellation for both Cash and Stripe card orders,
 * matching the exact behavior of Order Details status update to 'Cancel'.
 * Completely isolated in a separate controller to avoid touching existing flows.
 */
class OrderCancelRefundApiController extends OrderController
{
    /**
     * Handle order full refund and cancellation request.
     *
     * Expected Payload:
     * {
     *   "paymentId": "0000031" // Or order ID (31), invoice_no, or Stripe transaction_id (pi_... / ch_...)
     * }
     */
    public function refundOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'paymentId' => 'required|string',
            'email'     => 'nullable|email|max:255',
            'reason'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $paymentId = trim((string) $request->input('paymentId'));
        $reason = (string) $request->input('reason', '');
        $customReceiptEmail = $request->filled('email') ? trim((string) $request->input('email')) : null;

        try {
            $order = $this->resolveOrderByPaymentId($paymentId);
            $this->assertOrderRefundableAndCancellable($order);

            if ($this->isCashOrder($order)) {
                $payload = $this->executeCashFullRefundAndCancel($order, $reason, $customReceiptEmail);
            } else {
                $payload = $this->executeStripeFullRefundAndCancel($order, $reason, $customReceiptEmail);
            }

            return response()->json($payload, 200);
        } catch (\Throwable $e) {
            Log::error('Order Cancel & Refund API failed', [
                'paymentId' => $paymentId,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Resolve order by paymentId, which may be:
     * - Formatted 7-digit invoice_no (e.g. "0000031", "0001163")
     * - Order ID integer string (e.g. "31", "1163")
     * - Stripe transaction ID (e.g. "ch_3Tr..." or "pi_3Tr...")
     * Supports both online web store orders and POS orders.
     */
    protected function resolveOrderByPaymentId(string $paymentId): Order
    {
        $paymentId = trim($paymentId);

        $query = Order::with([
            'orderstatus',
            'orderlasttrans',
            'orderitems.term.termcategories',
            'getway',
            'user',
            'shippingwithinfo',
            'ordermeta',
            'schedule',
        ]);

        // 1. Direct match on invoice_no (e.g. "0000031")
        $order = (clone $query)->where('invoice_no', $paymentId)->first();

        // 2. Direct match on transaction_id
        if (!$order) {
            $order = (clone $query)->where('transaction_id', $paymentId)->first();
        }

        // 3. Partial match on transaction_id for Stripe payment intent or charge prefix
        if (!$order && preg_match('/^(pi_|ch_)/', $paymentId)) {
            $order = (clone $query)->where('transaction_id', 'like', '%' . $paymentId . '%')->first();
        }

        // 4. Exact integer ID match (e.g. "31")
        if (!$order && ctype_digit($paymentId)) {
            $order = (clone $query)->where('id', (int) $paymentId)->first();
        }

        // 5. Stripped leading zeros match (e.g. "0000031" -> id 31)
        if (!$order) {
            $normalizedInvoice = ltrim($paymentId, '0');
            if ($normalizedInvoice !== '' && ctype_digit($normalizedInvoice)) {
                $order = (clone $query)->where('id', (int) $normalizedInvoice)->first();
            }
        }

        if (!$order) {
            throw new \Exception("Order not found for the provided paymentId: {$paymentId}");
        }

        return $order;
    }

    /**
     * Check if order can be cancelled and refunded.
     */
    protected function assertOrderRefundableAndCancellable(Order $order): void
    {
        // If already both cancelled (status_id = 2) AND fully refunded (payment_status = 5)
        if ((int) $order->payment_status === 5 && (int) $order->status_id === 2) {
            throw new \Exception('This order has already been cancelled and fully refunded.');
        }

        // If already fully refunded, ensure order is marked cancelled
        if ((int) $order->payment_status === 5) {
            $order->status_id = 2;
            $order->save();
            throw new \Exception('This order has already been fully refunded. Order status has been set to Cancelled.');
        }

        // For non-cash orders, transaction ID is required
        if (!$this->isCashOrder($order) && empty($order->transaction_id)) {
            throw new \Exception('No transaction ID found for this non-cash order.');
        }
    }

    /**
     * Determine if order is paid via cash / check.
     */
    protected function isCashOrder(Order $order): bool
    {
        $ordermetaArr = json_decode(optional($order->ordermeta)->value ?? '{}', true) ?: [];
        $pm = strtolower((string) ($ordermetaArr['payment_method'] ?? ''));
        $pml = strtolower((string) ($ordermetaArr['payment_method_label'] ?? ''));
        $gwName = strtolower((string) optional($order->getway)->name ?? '');

        return $pml === 'cash/check'
            || $pml === 'cash'
            || $pm === 'cash'
            || (int) $order->order_from === 5
            || $gwName === 'cash';
    }

    /**
     * Execute full refund and cancellation for Cash orders.
     * Mirrors OrderController lines 468-482.
     */
    protected function executeCashFullRefundAndCancel(Order $order, string $reason = '', ?string $customReceiptEmail = null): array
    {
        $ordermetaArr = json_decode(optional($order->ordermeta)->value ?? '{}', true) ?: [];
        $isCaptured = ((int) $order->payment_status === 1) || (!empty($ordermetaArr['cash_check_captured_at']));

        // If cash order is not yet captured, capture it first before cancelling
        if (!$isCaptured) {
            $this->captureCashCheckOrderWithoutStripe($order);
            $order = $order->fresh([
                'orderitems.term.termcategories',
                'ordermeta',
                'user',
                'orderlasttrans',
                'orderstatus',
                'getway',
                'shippingwithinfo',
                'schedule',
            ]);
        }

        $refundAmountDollars = round((float) $order->total, 2);
        $transactionLog = $this->buildCashRefundTransactionLog($order, $refundAmountDollars, $reason);

        // Finalize order in DB: payment_status = 5, status_id = 2, refunded_at, log, tickets
        $cancelledTickets = $this->finalizeRefundedAndCancelledOrder($order, $transactionLog);
        $refundId = $transactionLog['id'] ?? ('cash_rfd_' . $order->id . '_' . time());

        // Side-effects: Financial manager sync + email notifications
        $this->runRefundAndCancelSideEffects($order, $refundAmountDollars, $refundId, $cancelledTickets, $customReceiptEmail);

        return $this->formatSuccessResponse($order, $refundId, $refundAmountDollars, 'cash', $customReceiptEmail);
    }

    /**
     * Execute full refund and cancellation for Stripe card orders.
     * Mirrors OrderController lines 484-489 and 1465-1542.
     */
    protected function executeStripeFullRefundAndCancel(Order $order, string $reason = '', ?string $customReceiptEmail = null): array
    {
        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', '=', 'App\Lib\Stripe')
            ->first();

        if (!$gateway) {
            throw new \Exception('Stripe payment gateway is not configured.');
        }

        $gatewayDataInfo = json_decode($gateway->data ?? '{}');
        if (!$gatewayDataInfo) {
            throw new \Exception('Stripe gateway credentials are missing.');
        }

        Stripe::setApiKey($gateway->test_mode == 1 ? $gatewayDataInfo->test_secret_key : $gatewayDataInfo->secret_key);

        $transactionId = $order->transaction_id;
        if (!$transactionId) {
            throw new \Exception('No transaction ID provided for this Stripe order.');
        }

        $chargeId = null;
        if (str_starts_with($transactionId, 'pi_')) {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \Exception('Payment is not in a refundable state (status: ' . $paymentIntent->status . ').');
            }

            $chargeId = $paymentIntent->latest_charge;
        } elseif (str_starts_with($transactionId, 'ch_')) {
            $chargeId = $transactionId;
        } else {
            throw new \Exception('Invalid transaction ID format.');
        }

        if (!$chargeId) {
            throw new \Exception('Unable to resolve Stripe charge for refund.');
        }

        $charge = \Stripe\Charge::retrieve($chargeId);
        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $refundAmountDollars = $this->calculateRefundNetTotal($order, $ordermeta);

        if ($charge->refunded) {
            $cancelledTickets = $this->finalizeRefundedAndCancelledOrder($order, $charge->toArray());
            $refundId = $charge->refunds->data[0]->id ?? ('re_synced_' . time());
        } else {
            $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
            $netRefundRemaining = max(0.0, round($refundAmountDollars - $alreadyRefunded, 2));

            if ($alreadyRefunded > 0 && $netRefundRemaining <= 0) {
                $cancelledTickets = $this->finalizeRefundedAndCancelledOrder($order, $charge->toArray());
                $refundId = $charge->refunds->data[0]->id ?? ('re_synced_' . time());
            } else {
                $actualRefundDollars = $netRefundRemaining > 0 ? $netRefundRemaining : $refundAmountDollars;
                $coverFee = (float) ($ordermeta->cover_fee ?? 0);
                $refundAmountCents = max(1, (int) round($actualRefundDollars * 100));

                $refundParams = [
                    'charge'           => $chargeId,
                    'amount'           => $refundAmountCents,
                    'reverse_transfer' => true,
                ];

                if ($coverFee <= 0) {
                    $refundParams['refund_application_fee'] = true;
                }

                if ($reason !== '') {
                    $refundParams['reason'] = 'requested_by_customer';
                    $refundParams['metadata'] = [
                        'refund_reason' => $reason,
                        'order_id'      => (string) $order->id,
                        'invoice_no'    => (string) $order->invoice_no,
                    ];
                }

                $refund = \Stripe\Refund::create($refundParams);

                if (!in_array($refund->status, ['succeeded', 'pending'], true)) {
                    throw new \Exception('Stripe refund was not completed (status: ' . $refund->status . ').');
                }

                $cancelledTickets = $this->finalizeRefundedAndCancelledOrder($order, $refund->toArray());
                $refundId = $refund->id;
                $refundAmountDollars = round($refund->amount / 100, 2);
            }
        }

        // Side-effects: Financial manager sync + email notifications
        $this->runRefundAndCancelSideEffects($order, $refundAmountDollars, $refundId, $cancelledTickets, $customReceiptEmail);

        return $this->formatSuccessResponse($order, $refundId, $refundAmountDollars, 'stripe', $customReceiptEmail);
    }

    /**
     * Mark order as Refunded (payment_status = 5) and Cancelled (status_id = 2) in database,
     * persist refund transaction log, and cancel event tickets.
     */
    protected function finalizeRefundedAndCancelledOrder(Order $order, array $transactionLog): Collection
    {
        $order->payment_status = 5; // Refunded
        $order->status_id = 2;      // Cancelled
        $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));

        if (empty($order->captured_at)) {
            $order->captured_at = $order->created_at ?? Carbon::now()->setTimezone(config('app.timezone'));
        }

        $order->save();

        $this->persistRefundTransactionLog($order, $transactionLog);

        return $this->cancelEventTicketsForOrder($order);
    }

    /**
     * Execute all post-refund side-effects:
     * 1. Financial Manager sync (post_order_data_POS for POS orders, post_order_data for online web orders)
     * 2. Background tenant financial manager sync dispatch (trigger_tenant_financial_manager_sync_after_refund)
     * 3. Ticket cancellation emails (if any tickets were cancelled)
     * 4. Admin email notification
     * 5. Customer email notification (NotifyToUser)
     * 6. Customer refund receipt email (RefundReceiptEmailService)
     */
    protected function runRefundAndCancelSideEffects(
        Order $order,
        float $refundAmountDollars,
        ?string $refundId,
        ?Collection $cancelledTickets = null,
        ?string $customReceiptEmail = null
    ): void {
        $order = Order::with([
            'orderstatus',
            'orderlasttrans',
            'orderitems.term.termcategories',
            'getway',
            'user',
            'shippingwithinfo',
            'ordermeta',
            'schedule',
        ])->findOrFail($order->id);

        // 1. Financial Manager post_order_data
        try {
            if (in_array((int) $order->order_from, [4, 5])) {
                $this->post_order_data_POS($order, 'refund');
            } else {
                $this->post_order_data($order, 'refund');
            }
        } catch (\Throwable $e) {
            Log::error('post_order_data failed after order refund and cancel', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        // 2. Financial Manager background tenant sync trigger
        try {
            trigger_tenant_financial_manager_sync_after_refund((int) $order->id);
        } catch (\Throwable $e) {
            Log::error('tenant FM sync dispatch failed after order refund and cancel', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        // 3. Ticket cancellation emails (if any tickets were cancelled)
        try {
            $this->sendTicketCancelledRefundEmails(
                $order,
                $cancelledTickets ?? collect(),
                $refundAmountDollars,
                $refundId
            );
        } catch (\Throwable $e) {
            Log::error('Ticket cancel refund email failed after order refund and cancel', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        // 4. Admin email notification
        $adminDetails = User::where('role_id', 3)->first();
        $adminEmail = $adminDetails->email ?? '';
        if (!empty($adminEmail)) {
            try {
                NotifyToUser::sendEmail($order, $adminEmail, 'admin');
            } catch (\Throwable $e) {
                Log::error('Order cancel refund admin notification email failed', [
                    'order_id'    => $order->id,
                    'admin_email' => $adminEmail,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        // 5. Customer email notification
        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $customerEmail = trim((string) ($ordermeta->email ?? optional($order->user)->email ?? ''));
        if ($order->notify_driver == 'mail' || !empty($customerEmail)) {
            if (!empty($customerEmail)) {
                try {
                    NotifyToUser::sendEmail($order, $customerEmail, 'user');
                } catch (\Throwable $e) {
                    Log::error('Order cancel refund customer notification email failed', [
                        'order_id'       => $order->id,
                        'customer_email' => $customerEmail,
                        'error'          => $e->getMessage(),
                    ]);
                }
            }
        }

        // 6. Customer refund receipt email
        try {
            $receiptRecipient = !empty($customReceiptEmail) ? $customReceiptEmail : $customerEmail;
            app(RefundReceiptEmailService::class)->sendForRefund(
                $order,
                $refundAmountDollars,
                null,
                $this->buildRefundReferenceId($order, $refundId),
                'full',
                $receiptRecipient
            );
        } catch (\Throwable $e) {
            Log::error('Order cancel refund receipt email failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build cash refund transaction log for ordermeta.
     */
    protected function buildCashRefundTransactionLog(Order $order, float $refundAmountDollars, string $reason = ''): array
    {
        $refundAmountCents = max(1, (int) round($refundAmountDollars * 100));
        $cashRefundId = 'cash_rfd_' . $order->id . '_' . time();

        return [
            'id'              => $cashRefundId,
            'object'          => 'refund',
            'amount'          => $refundAmountCents,
            'amount_refunded' => $refundAmountCents,
            'currency'        => 'usd',
            'status'          => 'succeeded',
            'payment_method'  => 'cash',
            'reason'          => $reason !== '' ? $reason : 'cash_manual_refund_and_cancel',
            'created'         => time(),
        ];
    }

    /**
     * Format consistent JSON success response.
     */
    protected function formatSuccessResponse(
        Order $order,
        string $refundId,
        float $refundAmountDollars,
        string $paymentMethod,
        ?string $receiptEmail = null
    ): array {
        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}', true) ?: [];
        $recipientEmail = !empty($receiptEmail) ? $receiptEmail : ($ordermeta['email'] ?? optional($order->user)->email ?? '');

        return [
            'success'       => true,
            'message'       => 'Order refunded and cancelled successfully.',
            'paymentId'     => (string) ($order->transaction_id ?: $order->invoice_no ?: (string) $order->id),
            'refundId'      => $refundId,
            'amount'        => (int) round($refundAmountDollars * 100),
            'refundAmount'  => $refundAmountDollars,
            'status'        => 'succeeded',
            'currency'      => 'usd',
            'refundType'    => 'full',
            'orderId'       => (int) $order->id,
            'invoiceNo'     => (string) $order->invoice_no,
            'orderStatus'   => 'Cancelled',
            'paymentStatus' => 'Refunded',
            'order'         => [
                'id'                   => (int) $order->id,
                'invoice_no'           => (string) $order->invoice_no,
                'status_id'            => 2,
                'status'               => 'Cancel',
                'payment_status'       => 5,
                'payment_status_label' => 'Refunded',
                'payment_method'       => $paymentMethod,
                'total'                => (float) $order->total,
                'refunded_amount'      => $refundAmountDollars,
                'reference_id'         => $this->buildRefundReferenceId($order, $refundId),
                'email'                => $recipientEmail,
                'refunded_at'          => $order->refunded_at ? $order->refunded_at->toDateTimeString() : Carbon::now()->toDateTimeString(),
            ],
        ];
    }
}
