<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Tenant-wise Financial Manager backfill.
 * Uses the same sync path as /api/storedata/order/create → sync_order_to_financial_manager().
 * Does not modify existing sync helpers or controllers.
 */
class SyncTenantFinancialManager extends Command
{
    protected $signature = 'tenant:sync-financial-manager
                            {tenant : Tenant ID (example: hello-tester-club)}
                            {--order= : Sync only this order ID}
                            {--force : Re-sync capture orders even if already marked as synced}
                            {--dry-run : List eligible orders without calling WordPress}';

    protected $description = 'Sync eligible orders for one tenant to WordPress Financial Manager (same sync as order/create)';

    public function handle(): int
    {
        $tenantId = (string) $this->argument('tenant');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $onlyOrderId = $this->option('order') !== null && $this->option('order') !== ''
            ? (int) $this->option('order')
            : null;

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant not found: {$tenantId}");
            return Command::FAILURE;
        }

        $this->info("Financial Manager sync started for tenant: {$tenant->id}");
        if ($onlyOrderId) {
            $this->info("Scoped to order ID: {$onlyOrderId}");
        }
        if ($dryRun) {
            $this->warn('Dry-run mode: WordPress will not be called.');
        }
        if ($force) {
            $this->warn('Force mode: previously synced capture orders will be re-sent.');
        }

        $attempted = 0;
        $skipped = 0;
        $failed = 0;

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

                if (!is_order_syncable_to_financial_manager($order, $postType)) {
                    $this->line("Skip {$order->invoice_no}: not syncable as {$postType}");
                    $skipped++;
                    continue;
                }

                if (
                    $postType === 'capture'
                    && !$force
                    && has_financial_manager_capture_sync((int) $order->id)
                ) {
                    $this->line("Skip {$order->invoice_no}: already synced");
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->info("[dry-run] {$order->invoice_no} → {$postType}");
                    $attempted++;
                    continue;
                }

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
        } catch (\Throwable $e) {
            $this->error('Tenant sync failed: '.$e->getMessage());
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

        $this->newLine();
        $this->info("Completed for {$tenantId}. attempted={$attempted} skipped={$skipped} failed={$failed}");

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
