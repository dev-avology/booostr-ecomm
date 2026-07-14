<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Tenant-wise Financial Manager sync.
 * Uses the same sync path as /api/storedata/order/create → sync_order_to_financial_manager().
 *
 * Usage:
 *   php artisan tenant:sync-financial-manager
 *     → sync all clubs from tenants.id
 *   php artisan tenant:sync-financial-manager hello-tester-club
 *     → sync one club (same as before)
 */
class SyncTenantFinancialManager extends Command
{
    protected $signature = 'tenant:sync-financial-manager
                            {tenant? : Optional tenant ID from tenants.id. If omitted, syncs all clubs}
                            {--order= : Sync only this order ID (requires a tenant argument)}
                            {--force : Re-sync capture orders even if already marked as synced}
                            {--dry-run : List eligible orders without calling WordPress}';

    protected $description = 'Sync eligible orders to WordPress Financial Manager for one club or all clubs from tenants.id';

    public function handle(): int
    {
        $tenantArg = $this->argument('tenant');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $onlyOrderId = $this->option('order') !== null && $this->option('order') !== ''
            ? (int) $this->option('order')
            : null;

        if ($dryRun) {
            $this->warn('Dry-run mode: WordPress will not be called.');
        }
        if ($force) {
            $this->warn('Force mode: previously synced capture orders will be re-sent.');
        }

        // Single club (existing behavior — used by refund afterResponse sync too)
        if (!empty($tenantArg)) {
            $tenant = Tenant::find((string) $tenantArg);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantArg}");
                return Command::FAILURE;
            }

            return $this->syncTenantOrders($tenant, $force, $dryRun, $onlyOrderId);
        }

        // All clubs dynamically from tenants.id
        if ($onlyOrderId) {
            $this->warn('--order is ignored when syncing all clubs. Pass a tenant ID to sync a single order.');
            $onlyOrderId = null;
        }

        $this->info('Financial Manager sync started for all clubs from tenants.id');

        $processed = 0;
        $tenantFailures = 0;

        Tenant::query()
            ->orderBy('id')
            ->chunk(50, function ($tenants) use ($force, $dryRun, $onlyOrderId, &$processed, &$tenantFailures) {
                foreach ($tenants as $tenant) {
                    $processed++;
                    $result = $this->syncTenantOrders($tenant, $force, $dryRun, $onlyOrderId);
                    if ($result !== Command::SUCCESS) {
                        $tenantFailures++;
                    }
                }
            });

        $this->newLine();
        $this->info("All-clubs sync completed. tenants_processed={$processed} tenant_failures={$tenantFailures}");

        return $tenantFailures > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Sync eligible orders for one tenant. Sync rules unchanged from previous command behavior.
     */
    private function syncTenantOrders(Tenant $tenant, bool $force, bool $dryRun, ?int $onlyOrderId): int
    {
        $tenantId = (string) $tenant->id;

        $this->info("Financial Manager sync started for tenant: {$tenantId}");
        if ($onlyOrderId) {
            $this->info("Scoped to order ID: {$onlyOrderId}");
        }

        $attempted = 0;
        $skipped = 0;
        $failed = 0;
        $partialAttempted = 0;
        $partialSkipped = 0;
        $partialFailed = 0;

        // Avoid ending an already-active tenancy context (e.g. when called from HTTP refund flow).
        $tenancyWasInitialized = tenancy()->initialized;

        try {
            if (!$tenancyWasInitialized) {
                tenancy()->initialize($tenant);
            }

            $ordersQuery = Order::query()
                ->with(['orderitems', 'ordermeta', 'shippingwithinfo', 'getway', 'user'])
                ->whereNotNull('captured_at')
                ->whereIn('payment_status', [1, 5])
                ->orderBy('id');

            if ($onlyOrderId) {
                $ordersQuery->where('id', $onlyOrderId);
            }

            $orders = $ordersQuery->get();

            $this->info('Eligible candidates found: '.$orders->count());

            foreach ($orders as $order) {
                if ($order->orderitems->isEmpty()) {
                    $this->line("Skip {$order->invoice_no}: no order items");
                    $skipped++;
                    continue;
                }

                $postType = $this->resolvePostType($order);
                $shouldSyncCaptureOrFullRefund = is_order_syncable_to_financial_manager($order, $postType);
                $captureAlreadySynced = (
                    $postType === 'capture'
                    && !$force
                    && has_financial_manager_capture_sync((int) $order->id)
                );

                // Capture / full-refund sync (existing behavior), without blocking partial-refund sync below.
                if (!$shouldSyncCaptureOrFullRefund) {
                    $this->line("Skip {$order->invoice_no}: not syncable as {$postType}");
                    $skipped++;
                } elseif ($captureAlreadySynced) {
                    $this->line("Skip {$order->invoice_no}: capture already synced");
                    $skipped++;
                } elseif ($dryRun) {
                    $this->info("[dry-run] {$order->invoice_no} → {$postType}");
                    $attempted++;
                } else {
                    if ($force && $postType === 'capture') {
                        Ordermeta::where('order_id', $order->id)
                            ->where('key', 'financial_manager_synced')
                            ->delete();
                    }

                    try {
                        // Same helper used by /api/storedata/order/create
                        sync_order_to_financial_manager($order->id, $postType);
                        $attempted++;
                        $this->info("Synced {$order->invoice_no} ({$postType})");
                    } catch (\Throwable $e) {
                        $failed++;
                        $this->error("Failed {$order->invoice_no}: {$e->getMessage()}");
                        Log::error('tenant:sync-financial-manager order failed', [
                            'tenant_id' => $tenantId,
                            'order_id' => $order->id,
                            'invoice_no' => $order->invoice_no,
                            'post_type' => $postType,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Partial refund logs → /financial-manager (always run when present,
                // even if capture was already synced / skipped).
                if ((int) $order->payment_status === 1) {
                    $partialEntries = get_order_partial_refund_log_entries((int) $order->id);
                    if (!empty($partialEntries)) {
                        $syncedPartial = get_financial_manager_partial_refund_synced_fingerprints((int) $order->id);

                        foreach ($partialEntries as $partialEntry) {
                            $fingerprint = $partialEntry['fingerprint'];
                            if (!$force && in_array($fingerprint, $syncedPartial, true)) {
                                $this->line("Skip {$order->invoice_no} partial refund (already synced)");
                                $partialSkipped++;
                                continue;
                            }

                            if ($dryRun) {
                                $partialLabel = ($partialEntry['type'] ?? '') === 'dollar' ? 'partial_dollar_refund' : 'partial_item_refund';
                                $this->info("[dry-run] {$order->invoice_no} → {$partialLabel} ({$partialEntry['grand_total']})");
                                $partialAttempted++;
                                continue;
                            }

                            try {
                                post_partial_refund_to_financial_manager($order, $partialEntry);
                                mark_financial_manager_partial_refund_synced((int) $order->id, $fingerprint);
                                $partialAttempted++;
                                $partialLabel = ($partialEntry['type'] ?? '') === 'dollar' ? 'partial_dollar_refund' : 'partial_item_refund';
                                $this->info("Synced {$order->invoice_no} ({$partialLabel}: {$partialEntry['grand_total']})");
                            } catch (\Throwable $e) {
                                $partialFailed++;
                                $this->error("Partial refund sync failed {$order->invoice_no}: {$e->getMessage()}");
                                Log::error('tenant:sync-financial-manager partial refund failed', [
                                    'tenant_id' => $tenantId,
                                    'order_id' => $order->id,
                                    'invoice_no' => $order->invoice_no,
                                    'fingerprint' => $fingerprint,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->error("Tenant sync failed for {$tenantId}: ".$e->getMessage());
            Log::error('tenant:sync-financial-manager failed', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        } finally {
            if (!$tenancyWasInitialized) {
                tenancy()->end();
            }
        }

        $this->info("Completed for {$tenantId}. attempted={$attempted} skipped={$skipped} failed={$failed} partial_attempted={$partialAttempted} partial_skipped={$partialSkipped} partial_failed={$partialFailed}");

        return Command::SUCCESS;
    }

    private function resolvePostType(Order $order): string
    {
        if (!empty($order->refunded_at) && (int) $order->payment_status === 5) {
            return 'refund';
        }

        return 'capture';
    }
}
