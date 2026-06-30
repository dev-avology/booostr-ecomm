<?php

namespace App\Services;

use App\Models\EventTicket;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\ProductSalesCrmSync;
use App\Models\ProductSalesCrmSyncContact;
use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductSalesCrmSyncService
{
    public const CRM_SAVE_CONTACT_URL = 'https://app.booostr.co/wp-json/booostr/v1/save-contact';

    public const CRM_CREATE_CONTACT_GROUP_URL = 'https://app.booostr.co/wp-json/booostr/v1/create-contact-group';

    public const BATCH_SIZE = 10;

    public function getActiveContinuousSyncForProduct(int $productId): ?ProductSalesCrmSync
    {
        return ProductSalesCrmSync::query()
            ->where('product_id', $productId)
            ->where('sync_type', 'continuous')
            ->where('continuous_sync_enabled', true)
            ->whereIn('sync_status', ['active', 'syncing'])
            ->latest('id')
            ->first();
    }

    /**
     * Has the product ever had a sync (one-time or continuous, any status)?
     * Source of truth for Create vs Edit CRM Sync label.
     */
    public function hasAnySyncHistoryForProduct(int $productId): bool
    {
        return ProductSalesCrmSync::query()
            ->where('product_id', $productId)
            ->exists();
    }

    public function getLatestSyncForProduct(int $productId): ?ProductSalesCrmSync
    {
        return ProductSalesCrmSync::query()
            ->where('product_id', $productId)
            ->orderByRaw('COALESCE(last_synced_at, created_at) DESC')
            ->latest('id')
            ->first();
    }

    public function recordOneTimeSync(Term $product, array $options, ?int $userId = null): ProductSalesCrmSync
    {
        $isTicketProduct = (int) $product->is_variation === 2;

        $sync = ProductSalesCrmSync::create([
            'product_id' => $product->id,
            'sync_type' => 'one_time',
            'sync_mode' => $options['sync_mode'] ?? 'all_results',
            'continuous_sync_enabled' => false,
            'is_ticket_product' => $isTicketProduct,
            'contact_tags' => $options['contact_tags'] ?? '',
            'crm_list_name' => $options['crm_list_name'] ?? null,
            'crm_group_id' => $this->normalizeCrmGroupId($options['crm_group_id'] ?? null),
            'filter_state' => $options['filter_state'] ?? [],
            'sync_status' => 'completed',
            'last_synced_at' => now(),
            'last_processed_at' => now(),
            'total_synced_contacts' => (int) ($options['total_synced_contacts'] ?? 0),
            'created_by' => $userId,
        ]);

        $this->storeSyncedContacts($sync, $options['synced_contacts'] ?? []);

        return $sync->fresh();
    }

    public function hasContactGroupChanged(?ProductSalesCrmSync $sync, ?string $newListName, ?string $newGroupId = null): bool
    {
        if (!$sync) {
            return false;
        }

        if ($this->normalizeContactGroupName($sync->crm_list_name)
            !== $this->normalizeContactGroupName($newListName)) {
            return true;
        }

        $normalizedNewGroupId = $this->normalizeCrmGroupId($newGroupId);
        if ($normalizedNewGroupId === null) {
            return false;
        }

        return $this->normalizeCrmGroupId($sync->crm_group_id) !== $normalizedNewGroupId;
    }

    public function getEffectiveSyncConfigForProduct(int $productId): ?ProductSalesCrmSync
    {
        return $this->getActiveContinuousSyncForProduct($productId)
            ?? $this->getLatestSyncForProduct($productId);
    }

    public function scopeChangeRequiresNewContactGroup(
        ?ProductSalesCrmSync $previousSync,
        string $newSyncMode,
        ?string $newListName
    ): bool {
        if (!$previousSync || $previousSync->sync_mode !== 'all_results') {
            return false;
        }

        if ($newSyncMode !== 'current_page') {
            return false;
        }

        return !$this->hasContactGroupChanged($previousSync, $newListName);
    }

    public function hasSyncModeChanged(?ProductSalesCrmSync $sync, string $newSyncMode): bool
    {
        if (!$sync) {
            return false;
        }

        return $sync->sync_mode !== $newSyncMode;
    }

    public function isExpandingScopeToAllResults(?ProductSalesCrmSync $sync, string $newSyncMode): bool
    {
        return $sync
            && $sync->sync_mode === 'current_page'
            && $newSyncMode === 'all_results';
    }

    public function enableContinuousSync(Term $product, array $options, ?int $userId = null): ProductSalesCrmSync
    {
        $existing = $this->getActiveContinuousSyncForProduct($product->id);
        $newListName = $options['crm_list_name'] ?? null;
        $newGroupId = $options['crm_group_id'] ?? null;
        $newSyncMode = $options['sync_mode'] ?? 'all_results';
        $groupChanged = $this->hasContactGroupChanged($existing, $newListName, $newGroupId);
        $scopeChanged = $this->hasSyncModeChanged($existing, $newSyncMode);

        if ($existing && $existing->sync_status === 'syncing' && !$groupChanged && !$scopeChanged) {
            return $existing;
        }

        if ($existing && $groupChanged) {
            $this->resetSyncContactsForRestart($existing);
        }

        $isTicketProduct = (int) $product->is_variation === 2;

        $attributes = [
            'sync_mode' => $newSyncMode,
            'continuous_sync_enabled' => true,
            'is_ticket_product' => $isTicketProduct,
            'contact_tags' => $options['contact_tags'] ?? '',
            'crm_list_name' => $newListName,
            'crm_group_id' => $this->normalizeCrmGroupId($newGroupId),
            'filter_state' => $options['filter_state'] ?? [],
            'sync_status' => 'syncing',
            'created_by' => $userId,
        ];

        if ($groupChanged) {
            $attributes['last_processed_record_id'] = null;
            $attributes['total_synced_contacts'] = 0;
            $attributes['last_synced_at'] = null;
            $attributes['last_processed_at'] = null;
        }

        $sync = ProductSalesCrmSync::updateOrCreate(
            ['product_id' => $product->id, 'sync_type' => 'continuous'],
            $attributes
        );

        if (!$existing && !$groupChanged) {
            $this->inheritSyncedContactsFromProductHistory($sync->fresh());
        }

        return $sync->fresh();
    }

    protected function storeSyncedContacts(ProductSalesCrmSync $sync, array $contacts): void
    {
        $storedCount = 0;
        $maxSourceId = (int) $sync->last_processed_record_id;

        foreach ($contacts as $contact) {
            $sourceType = trim((string) ($contact['source_type'] ?? ''));
            $sourceId = (int) ($contact['source_id'] ?? 0);

            if ($sourceType === '' || $sourceId <= 0) {
                continue;
            }

            ProductSalesCrmSyncContact::firstOrCreate(
                [
                    'product_sales_crm_sync_id' => $sync->id,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                ],
                [
                    'product_id' => $sync->product_id,
                    'email' => $contact['email'] ?? null,
                    'synced_at' => now(),
                ]
            );

            $storedCount++;
            $maxSourceId = max($maxSourceId, $sourceId);
        }

        if ($storedCount > 0) {
            $sync->update([
                'total_synced_contacts' => $storedCount,
                'last_processed_record_id' => $maxSourceId > 0 ? $maxSourceId : $sync->last_processed_record_id,
            ]);
        }
    }

    protected function inheritSyncedContactsFromProductHistory(ProductSalesCrmSync $sync): int
    {
        $previousContacts = ProductSalesCrmSyncContact::query()
            ->where('product_id', $sync->product_id)
            ->where('product_sales_crm_sync_id', '!=', $sync->id)
            ->orderByDesc('synced_at')
            ->get()
            ->unique(function ($row) {
                return $row->source_type . ':' . $row->source_id;
            });

        if ($previousContacts->isEmpty()) {
            return 0;
        }

        $imported = 0;
        $maxSourceId = (int) $sync->last_processed_record_id;

        foreach ($previousContacts as $contact) {
            $record = ProductSalesCrmSyncContact::firstOrCreate(
                [
                    'product_sales_crm_sync_id' => $sync->id,
                    'source_type' => $contact->source_type,
                    'source_id' => $contact->source_id,
                ],
                [
                    'product_id' => $sync->product_id,
                    'email' => $contact->email,
                    'synced_at' => $contact->synced_at ?? now(),
                ]
            );

            if ($record->wasRecentlyCreated) {
                $imported++;
            }

            $maxSourceId = max($maxSourceId, (int) $contact->source_id);
        }

        if ($imported > 0) {
            $sync->update([
                'total_synced_contacts' => ProductSalesCrmSyncContact::query()
                    ->where('product_sales_crm_sync_id', $sync->id)
                    ->count(),
                'last_processed_record_id' => $maxSourceId > 0 ? $maxSourceId : $sync->last_processed_record_id,
                'last_synced_at' => $sync->last_synced_at ?? now(),
            ]);
        }

        return $imported;
    }

    protected function resetSyncContactsForRestart(ProductSalesCrmSync $sync): void
    {
        ProductSalesCrmSyncContact::query()
            ->where('product_sales_crm_sync_id', $sync->id)
            ->delete();
    }

    protected function normalizeContactGroupName(?string $name): string
    {
        return strtolower(trim((string) $name));
    }

    public function getPausedContinuousSyncForProduct(int $productId): ?ProductSalesCrmSync
    {
        return ProductSalesCrmSync::query()
            ->where('product_id', $productId)
            ->where('sync_type', 'continuous')
            ->where('sync_status', 'paused')
            ->latest('id')
            ->first();
    }

    public function pauseContinuousSync(int $productId): ?ProductSalesCrmSync
    {
        $sync = $this->getActiveContinuousSyncForProduct($productId);

        if (!$sync) {
            return null;
        }

        $sync->update([
            'continuous_sync_enabled' => false,
            'sync_status' => 'paused',
        ]);

        return $sync->fresh();
    }

    public function restartContinuousSync(int $productId): ?ProductSalesCrmSync
    {
        $sync = $this->getPausedContinuousSyncForProduct($productId);

        if (!$sync) {
            return null;
        }

        $sync->update([
            'continuous_sync_enabled' => true,
            'sync_status' => 'active',
        ]);

        return $sync->fresh();
    }

    public function stopContinuousSync(int $productId): ?ProductSalesCrmSync
    {
        $sync = $this->getActiveContinuousSyncForProduct($productId);

        if (!$sync) {
            return null;
        }

        $sync->update([
            'continuous_sync_enabled' => false,
            'sync_status' => 'stopped',
        ]);

        return $sync->fresh();
    }

    public function buildSalesHistoryQuery(int $productId, bool $isTicketProduct, array $filterState = []): Builder
    {
        $search = $filterState['src'] ?? null;

        if ($isTicketProduct) {
            $query = EventTicket::with(['order.ordermeta', 'orderItem'])
                ->where('term_id', $productId);
        } else {
            $query = Orderitem::with(['order.ordermeta', 'eventTicket'])
                ->where('term_id', $productId);
        }

        if (!empty($search)) {
            $this->applySearchFilter($query, $isTicketProduct, $search);
        }

        return $query->orderBy('id', 'desc');
    }

    public function getEligibleContacts(ProductSalesCrmSync $sync, bool $initialSync = false): Collection
    {
        $filterState = $sync->filter_state ?? [];
        $query = $this->buildSalesHistoryQuery(
            $sync->product_id,
            (bool) $sync->is_ticket_product,
            $filterState
        );

        if ($initialSync && $sync->sync_mode === 'current_page') {
            $page = max(1, (int) ($filterState['page'] ?? 1));
            $perPage = max(1, (int) ($filterState['per_page'] ?? 20));
            $offset = ($page - 1) * $perPage;

            $records = (clone $query)->skip($offset)->take($perPage)->get();
        } elseif (!$initialSync && $sync->last_processed_record_id) {
            $records = (clone $query)
                ->where('id', '>', $sync->last_processed_record_id)
                ->get();
        } else {
            $records = $query->get();
        }

        $syncedKeys = ProductSalesCrmSyncContact::query()
            ->where('product_sales_crm_sync_id', $sync->id)
            ->get(['source_type', 'source_id'])
            ->map(fn ($row) => $row->source_type . ':' . $row->source_id)
            ->flip();

        return $records
            ->map(fn ($sale) => $this->mapSaleToContact($sale, (bool) $sync->is_ticket_product))
            ->filter(function ($contact) use ($syncedKeys) {
                $key = $contact['source_type'] . ':' . $contact['source_id'];
                return !$syncedKeys->has($key);
            })
            ->values();
    }

    public function processBatch(ProductSalesCrmSync $sync, bool $initialSync = false): array
    {
        if ($initialSync) {
            return $this->runInitialSync($sync);
        }

        return $this->runIncrementalBatch($sync);
    }

    public function runInitialSync(ProductSalesCrmSync $sync): array
    {
        if ($sync->sync_status !== 'syncing') {
            $sync->refresh();

            return [
                'done' => true,
                'processed' => 0,
                'failed' => 0,
                'total_pending' => 0,
                'total_synced_contacts' => (int) $sync->total_synced_contacts,
                'last_synced_at' => optional($sync->last_synced_at)->format('m/d/Y'),
            ];
        }

        $contacts = $this->getEligibleContacts($sync, true);
        $boosterId = tenant('club_id');
        $contactTags = $sync->contact_tags ?? '';
        $groupIds = $this->resolveCrmGroupIdsForSync($sync);
        $processed = 0;
        $failed = 0;

        foreach ($contacts as $contact) {
            if ($this->postContactToCrm($contact, $contactTags, $boosterId, $groupIds)) {
                ProductSalesCrmSyncContact::firstOrCreate(
                    [
                        'product_sales_crm_sync_id' => $sync->id,
                        'source_type' => $contact['source_type'],
                        'source_id' => $contact['source_id'],
                    ],
                    [
                        'product_id' => $sync->product_id,
                        'email' => $contact['email'] ?? null,
                        'synced_at' => now(),
                    ]
                );

                $sync->last_processed_record_id = max(
                    (int) $sync->last_processed_record_id,
                    (int) $contact['source_id']
                );
                $sync->total_synced_contacts = (int) $sync->total_synced_contacts + 1;
                $processed++;
            } else {
                $failed++;
            }
        }

        $sync->last_synced_at = now();
        $sync->last_processed_at = now();
        $sync->save();

        $this->markInitialSyncComplete($sync->fresh());
        $sync->refresh();

        return [
            'done' => true,
            'processed' => $processed,
            'failed' => $failed,
            'total_pending' => 0,
            'total_synced_contacts' => (int) $sync->total_synced_contacts,
            'last_synced_at' => optional($sync->last_synced_at)->format('m/d/Y'),
        ];
    }

    protected function runIncrementalBatch(ProductSalesCrmSync $sync): array
    {
        $contacts = $this->getEligibleContacts($sync, false);
        $batch = $contacts->take(self::BATCH_SIZE);

        if ($batch->isEmpty()) {
            return [
                'done' => true,
                'processed' => 0,
                'failed' => 0,
                'total_pending' => 0,
                'total_synced_contacts' => (int) $sync->total_synced_contacts,
                'last_synced_at' => optional($sync->last_synced_at)->format('m/d/Y'),
            ];
        }

        $boosterId = tenant('club_id');
        $contactTags = $sync->contact_tags ?? '';
        $groupIds = $this->resolveCrmGroupIdsForSync($sync);
        $processed = 0;
        $failed = 0;

        foreach ($batch as $contact) {
            if ($this->postContactToCrm($contact, $contactTags, $boosterId, $groupIds)) {
                ProductSalesCrmSyncContact::firstOrCreate(
                    [
                        'product_sales_crm_sync_id' => $sync->id,
                        'source_type' => $contact['source_type'],
                        'source_id' => $contact['source_id'],
                    ],
                    [
                        'product_id' => $sync->product_id,
                        'email' => $contact['email'] ?? null,
                        'synced_at' => now(),
                    ]
                );

                $sync->last_processed_record_id = max(
                    (int) $sync->last_processed_record_id,
                    (int) $contact['source_id']
                );
                $sync->total_synced_contacts = (int) $sync->total_synced_contacts + 1;
                $processed++;
            } else {
                $failed++;
            }
        }

        $sync->last_synced_at = now();
        $sync->last_processed_at = now();
        $sync->save();
        $sync->refresh();

        $remaining = $this->getEligibleContacts($sync, false)->count();

        return [
            'done' => $remaining === 0,
            'processed' => $processed,
            'failed' => $failed,
            'total_pending' => $remaining,
            'total_synced_contacts' => (int) $sync->total_synced_contacts,
            'last_synced_at' => optional($sync->last_synced_at)->format('m/d/Y'),
        ];
    }

    public function handleOrderItemsCreated(int $orderId): void
    {
        if (!function_exists('tenancy') || !tenant()) {
            return;
        }

        $order = Order::with('orderitems')->find($orderId);

        if (!$order) {
            return;
        }

        $productIds = $order->orderitems
            ->pluck('term_id')
            ->filter()
            ->unique();

        foreach ($productIds as $productId) {
            $sync = $this->getActiveContinuousSyncForProduct((int) $productId);

            if (!$sync || $sync->sync_status !== 'active' || $sync->is_ticket_product) {
                continue;
            }

            $this->syncContinuousForProduct((int) $productId);
        }
    }

    public function handleEventTicketCreated(EventTicket $ticket): void
    {
        if (!function_exists('tenancy') || !tenant() || !$ticket->term_id) {
            return;
        }

        $sync = $this->getActiveContinuousSyncForProduct((int) $ticket->term_id);

        if (!$sync || $sync->sync_status !== 'active' || !$sync->is_ticket_product) {
            return;
        }

        $this->syncContinuousForProduct((int) $ticket->term_id);
    }

    public function syncContinuousForProduct(int $productId): int
    {
        $sync = ProductSalesCrmSync::query()
            ->where('product_id', $productId)
            ->where('sync_type', 'continuous')
            ->where('continuous_sync_enabled', true)
            ->where('sync_status', 'active')
            ->latest('id')
            ->first();

        if (!$sync) {
            return 0;
        }

        return $this->processContinuousSyncConfig($sync);
    }

    public function runScheduledSyncForTenant(): int
    {
        $configs = ProductSalesCrmSync::query()
            ->where('sync_type', 'continuous')
            ->where('continuous_sync_enabled', true)
            ->where('sync_status', 'active')
            ->get();

        $totalProcessed = 0;

        foreach ($configs as $sync) {
            try {
                $totalProcessed += $this->processContinuousSyncConfig($sync);
            } catch (\Throwable $e) {
                Log::error('Product sales CRM continuous sync failed', [
                    'sync_id' => $sync->id,
                    'product_id' => $sync->product_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $totalProcessed;
    }

    protected function processContinuousSyncConfig(ProductSalesCrmSync $sync): int
    {
        $contacts = $this->getEligibleContacts($sync, false);

        if ($contacts->isEmpty()) {
            return 0;
        }

        $boosterId = tenant('club_id');
        $contactTags = $sync->contact_tags ?? '';
        $groupIds = $this->resolveCrmGroupIdsForSync($sync);
        $totalProcessed = 0;

        foreach ($contacts as $contact) {
            if (!$this->postContactToCrm($contact, $contactTags, $boosterId, $groupIds)) {
                continue;
            }

            ProductSalesCrmSyncContact::firstOrCreate(
                [
                    'product_sales_crm_sync_id' => $sync->id,
                    'source_type' => $contact['source_type'],
                    'source_id' => $contact['source_id'],
                ],
                [
                    'product_id' => $sync->product_id,
                    'email' => $contact['email'] ?? null,
                    'synced_at' => now(),
                ]
            );

            $sync->last_processed_record_id = max(
                (int) $sync->last_processed_record_id,
                (int) $contact['source_id']
            );
            $sync->total_synced_contacts = (int) $sync->total_synced_contacts + 1;
            $totalProcessed++;
        }

        $sync->last_synced_at = now();
        $sync->last_processed_at = now();
        $sync->save();

        return $totalProcessed;
    }

    public function mapSaleToContact($sale, bool $isTicketProduct): array
    {
        if ($isTicketProduct) {
            $ticket = $sale;
            $order = $ticket->order ?? null;
            $ordermeta = json_decode($order->ordermeta->value ?? '{}');

            $fullName = $ticket->attendee_name ?? ($ordermeta->name ?? 'Guest User');
            $email = $ticket->attendee_email ?? ($ordermeta->email ?? '-');
            $phone = $ticket->attendee_phone ?? ($ordermeta->phone ?? '-');
            $city = $this->resolveCity($ordermeta);
            $nameParts = explode(' ', trim($fullName), 2);

            return [
                'source_type' => 'event_ticket',
                'source_id' => (int) $ticket->id,
                'first_name' => $this->sanitize($nameParts[0] ?? ''),
                'last_name' => $this->sanitize($nameParts[1] ?? ''),
                'email' => $this->sanitize($email),
                'phone_number' => $this->sanitize($phone),
                'city' => $this->sanitize($city),
            ];
        }

        $order = $sale->order ?? null;
        $ordermeta = json_decode($order->ordermeta->value ?? '{}');

        $fullName = $ordermeta->name ?? 'Guest User';
        $email = $ordermeta->email ?? '-';
        $phone = $ordermeta->phone ?? '-';
        $city = $this->resolveCity($ordermeta);
        $nameParts = explode(' ', trim($fullName), 2);

        return [
            'source_type' => 'orderitem',
            'source_id' => (int) $sale->id,
            'first_name' => $this->sanitize($nameParts[0] ?? ''),
            'last_name' => $this->sanitize($nameParts[1] ?? ''),
            'email' => $this->sanitize($email),
            'phone_number' => $this->sanitize($phone),
            'city' => $this->sanitize($city),
        ];
    }

    public function postContactToCrm(array $contact, string $contactTags, $boosterId, array $groupIds = []): bool
    {
        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post(self::CRM_SAVE_CONTACT_URL, [
                    'booster_id' => $boosterId,
                    'first_name' => $contact['first_name'] ?? '',
                    'last_name' => $contact['last_name'] ?? '',
                    'email' => $contact['email'] ?? '',
                    'phone_number' => $contact['phone_number'] ?? '',
                    'city' => $contact['city'] ?? '',
                    'contact_tags' => $contactTags,
                    'group_ids' => $this->normalizeGroupIds($groupIds),
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('CRM save-contact request failed', [
                'email' => $contact['email'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function resolveCrmGroupIdsForSync(ProductSalesCrmSync $sync): array
    {
        $storedGroupId = $this->normalizeCrmGroupId($sync->crm_group_id);
        if ($storedGroupId !== null) {
            return $this->normalizeGroupIds([$storedGroupId]);
        }

        $listName = trim((string) ($sync->crm_list_name ?? ''));
        if ($listName === '') {
            return [];
        }

        $clubId = (int) tenant('club_id');
        if ($clubId <= 0) {
            return [];
        }

        try {
            $group = DB::table('contact_groups')
                ->where('club_id', $clubId)
                ->where('is_active', true)
                ->whereRaw('LOWER(name) = ?', [strtolower($listName)])
                ->first(['group_id']);

            $resolvedGroupId = $this->normalizeCrmGroupId($group ? ($group->group_id ?? null) : null);
            if ($resolvedGroupId !== null) {
                return $this->normalizeGroupIds([$resolvedGroupId]);
            }
        } catch (\Throwable $e) {
            Log::warning('Unable to resolve CRM group ID for sync', [
                'sync_id' => $sync->id,
                'product_id' => $sync->product_id,
                'crm_list_name' => $listName,
                'error' => $e->getMessage(),
            ]);
        }

        if ($listName !== '') {
            Log::warning('CRM sync has no group_ids for configured contact group', [
                'sync_id' => $sync->id,
                'product_id' => $sync->product_id,
                'crm_list_name' => $listName,
            ]);
        }

        return [];
    }

    protected function normalizeCrmGroupId($groupId): ?string
    {
        $normalized = trim((string) $groupId);
        return $normalized === '' ? null : $normalized;
    }

    protected function normalizeGroupIds(array $groupIds): array
    {
        $normalized = [];

        foreach ($groupIds as $groupId) {
            $groupId = trim((string) $groupId);
            if ($groupId === '') {
                continue;
            }

            $normalized[] = ctype_digit($groupId) ? (int) $groupId : $groupId;
        }

        return array_values(array_unique($normalized, SORT_REGULAR));
    }

    public function createContactGroupInCrm(string $name, int $clubId): array
    {
        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->post(self::CRM_CREATE_CONTACT_GROUP_URL, [
                    'name' => $name,
                    'club_id' => $clubId,
                ]);

            $body = $response->json();
            if (!is_array($body)) {
                $body = [];
            }

            if (!$response->successful() || empty($body['success'])) {
                return [
                    'success' => false,
                    'message' => trim((string) ($body['message'] ?? '')) !== ''
                        ? (string) $body['message']
                        : 'Unable to create contact group in CRM.',
                ];
            }

            $groupId = trim((string) ($body['group_id'] ?? ''));
            if ($groupId === '') {
                return [
                    'success' => false,
                    'message' => 'Contact group was created but no group ID was returned.',
                ];
            }

            return [
                'success' => true,
                'message' => trim((string) ($body['message'] ?? '')) !== ''
                    ? (string) $body['message']
                    : 'Contact group created successfully.',
                'group_id' => $groupId,
            ];
        } catch (\Throwable $e) {
            Log::warning('CRM create-contact-group request failed', [
                'club_id' => $clubId,
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Unable to create contact group right now.',
            ];
        }
    }

    public function formatStatusPayload(?ProductSalesCrmSync $sync, ?int $productId = null): array
    {
        $productIdForHistory = $productId ?? ($sync ? $sync->product_id : null);
        $hasSyncHistory = $productIdForHistory
            ? $this->hasAnySyncHistoryForProduct((int) $productIdForHistory)
            : false;

        $latestSync = $productIdForHistory
            ? $this->getLatestSyncForProduct((int) $productIdForHistory)
            : null;

        $pausedSync = $productIdForHistory
            ? $this->getPausedContinuousSyncForProduct((int) $productIdForHistory)
            : null;

        $effectiveSync = $sync ?: ($pausedSync ?: $latestSync);
        $lastSyncedAt = $effectiveSync ? optional($effectiveSync->last_synced_at)->format('m/d/Y') : null;
        $lastSyncType = $effectiveSync ? $effectiveSync->sync_type : null;

        if (!$sync) {
            return [
                'continuous_active' => false,
                'continuous_paused' => (bool) $pausedSync,
                'sync_status' => $pausedSync ? $pausedSync->sync_status : null,
                'sync_mode' => $effectiveSync->sync_mode ?? null,
                'last_synced_at' => $lastSyncedAt,
                'last_sync_type' => $lastSyncType,
                'total_synced_contacts' => (int) ($effectiveSync->total_synced_contacts ?? 0),
                'initial_sync_in_progress' => false,
                'contact_tags' => $effectiveSync->contact_tags ?? '',
                'crm_list_name' => $effectiveSync->crm_list_name ?? '',
                'crm_group_id' => $effectiveSync->crm_group_id ?? '',
                'has_sync_history' => $hasSyncHistory,
            ];
        }

        return [
            'continuous_active' => $sync->isContinuousActive(),
            'continuous_paused' => false,
            'sync_status' => $sync->sync_status,
            'sync_mode' => $sync->sync_mode,
            'last_synced_at' => $lastSyncedAt,
            'last_sync_type' => $lastSyncType,
            'total_synced_contacts' => (int) $sync->total_synced_contacts,
            'initial_sync_in_progress' => $sync->sync_status === 'syncing',
            'contact_tags' => $sync->contact_tags ?? '',
            'crm_list_name' => $sync->crm_list_name ?? '',
            'crm_group_id' => $sync->crm_group_id ?? '',
            'has_sync_history' => $hasSyncHistory,
        ];
    }

    protected function markInitialSyncComplete(ProductSalesCrmSync $sync): void
    {
        if ($sync->sync_status !== 'syncing') {
            return;
        }

        $sync->update([
            'sync_status' => 'active',
            'last_synced_at' => now(),
            'last_processed_at' => now(),
        ]);
    }

    protected function applySearchFilter(Builder $query, bool $isTicketProduct, string $search): void
    {
        if ($isTicketProduct) {
            $query->where(function ($q) use ($search) {
                $q->where('attendee_name', 'LIKE', "%{$search}%")
                    ->orWhere('attendee_email', 'LIKE', "%{$search}%")
                    ->orWhere('attendee_phone', 'LIKE', "%{$search}%")
                    ->orWhere('ticket_uuid', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('invoice_no', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('order.ordermeta', function ($metaQuery) use ($search) {
                        $metaQuery->where('value', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('orderItem', function ($itemQuery) use ($search) {
                        $itemQuery->where('info', 'LIKE', "%{$search}%");
                    });
            });

            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('info', 'LIKE', "%{$search}%")
                ->orWhereHas('order', function ($orderQuery) use ($search) {
                    $orderQuery->where('invoice_no', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('order.ordermeta', function ($metaQuery) use ($search) {
                    $metaQuery->where('value', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('eventTicket', function ($ticketQuery) use ($search) {
                    $ticketQuery->where('attendee_name', 'LIKE', "%{$search}%")
                        ->orWhere('attendee_email', 'LIKE', "%{$search}%")
                        ->orWhere('attendee_phone', 'LIKE', "%{$search}%")
                        ->orWhere('ticket_uuid', 'LIKE', "%{$search}%")
                        ->orWhere('status', 'LIKE', "%{$search}%");
                });
        });
    }

    protected function sanitize($value): string
    {
        $value = trim((string) ($value ?? ''));

        return ($value === '-' || $value === '') ? '' : $value;
    }

    protected function resolveCity($ordermeta): string
    {
        if (!is_object($ordermeta)) {
            return '';
        }

        if (!empty($ordermeta->city)) {
            return (string) $ordermeta->city;
        }

        if (!empty($ordermeta->billing) && is_object($ordermeta->billing) && !empty($ordermeta->billing->city)) {
            return (string) $ordermeta->billing->city;
        }

        if (!empty($ordermeta->shipping) && is_object($ordermeta->shipping) && !empty($ordermeta->shipping->city)) {
            return (string) $ordermeta->shipping->city;
        }

        return '';
    }
}
