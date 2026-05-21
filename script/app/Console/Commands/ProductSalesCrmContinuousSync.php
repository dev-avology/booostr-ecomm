<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\ProductSalesCrmSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProductSalesCrmContinuousSync extends Command
{
    protected $signature = 'tenant:product-sales-crm-sync';

    protected $description = 'Run continuous CRM sync for product sales history contacts (manual / all tenants)';

    public function handle(ProductSalesCrmSyncService $syncService): int
    {
        $this->info('Product sales CRM continuous sync started');

        $totalProcessed = 0;

        Tenant::where('status', 1)
            ->chunk(50, function ($tenants) use ($syncService, &$totalProcessed) {
                foreach ($tenants as $tenant) {
                    try {
                        tenancy()->initialize($tenant);

                        $processed = $syncService->runScheduledSyncForTenant();
                        $totalProcessed += $processed;

                        if ($processed > 0) {
                            $this->info("Tenant {$tenant->id}: synced {$processed} contact(s)");
                        }
                    } catch (\Throwable $e) {
                        Log::error('Product sales CRM tenant sync failed', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage(),
                        ]);
                    } finally {
                        tenancy()->end();
                    }
                }
            });

        $this->info("Product sales CRM continuous sync completed. Total contacts synced: {$totalProcessed}");

        return Command::SUCCESS;
    }
}
