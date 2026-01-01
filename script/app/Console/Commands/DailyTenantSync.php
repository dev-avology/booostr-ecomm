<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantSyncService;
use Illuminate\Support\Facades\Log;

class DailyTenantSync extends Command
{
    protected $signature = 'tenant:sync-daily';

    protected $description = 'Run daily synchronization for all tenants';

    public function handle()
    {
        Tenant::where('status', 1)
            ->chunk(50, function ($tenants) {

                foreach ($tenants as $tenant) {
                    try {
                        // 🔹 Switch tenant context
                        tenancy()->initialize($tenant);

                        app(TenantSyncService::class)->run($tenant);

                        $this->info("Synced tenant: {$tenant->id}");
                    } catch (\Throwable $e) {
                        Log::error('Tenant sync failed', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage()
                        ]);
                    } finally {
                        tenancy()->end();
                    }
                }
            });

        return Command::SUCCESS;
    }
}
