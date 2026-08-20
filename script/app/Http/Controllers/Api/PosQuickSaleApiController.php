<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuickSaleDescriptor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Additive POS Quick Sale module — separate from regular POS product/inventory APIs.
 */
class PosQuickSaleApiController extends Controller
{
    /**
     * Add Descriptor API — create one or many Quick Sale descriptors for this club/tenant.
     *
     * Single add (unchanged): { "name", "price", "is_default", "sort_order" }
     * Bulk add (additive):     { "descriptors": [ { "name", ... }, ... ] }
     */
    public function addDescriptor(Request $request): JsonResponse
    {
        if ($request->has('descriptors') && is_array($request->input('descriptors'))) {
            return $this->addDescriptorsBulk($request);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($this->descriptorNameExists((string) $request->input('name'))) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => [
                    'name' => ['A descriptor with this name already exists.'],
                ],
            ], 422);
        }

        try {
            $descriptor = DB::transaction(function () use ($request) {
                return $this->createDescriptorFromPayload([
                    'name' => $request->input('name'),
                    'price' => $request->input('price', 0),
                    'is_default' => $request->boolean('is_default'),
                    'sort_order' => $request->filled('sort_order') ? (int) $request->input('sort_order') : null,
                ], true);
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptor added successfully.',
                'descriptor' => $this->formatDescriptor($descriptor),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'errors' => str_contains(strtolower($e->getMessage()), 'name')
                    ? ['name' => [$e->getMessage()]]
                    : [],
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale add descriptor failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to add Quick Sale descriptor.',
            ], 500);
        }
    }

    /**
     * Additive: bulk create descriptors via descriptors[] on the same add endpoint.
     */
    private function addDescriptorsBulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'descriptors' => 'required|array|min:1',
            'descriptors.*.name' => 'required|string|max:255',
            'descriptors.*.price' => 'nullable|numeric|min:0',
            'descriptors.*.is_default' => 'nullable|boolean',
            'descriptors.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $duplicateResponse = $this->validateBulkDescriptorNamesUnique($request->input('descriptors', []));
        if ($duplicateResponse instanceof JsonResponse) {
            return $duplicateResponse;
        }

        try {
            $descriptors = DB::transaction(function () use ($request) {
                $items = $request->input('descriptors', []);
                $created = [];
                $nextAutoSort = (int) QuickSaleDescriptor::max('sort_order');

                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $requestedSort = array_key_exists('sort_order', $item) && $item['sort_order'] !== null
                        ? max(0, (int) $item['sort_order'])
                        : null;

                    if ($requestedSort === null) {
                        $nextAutoSort++;
                        $requestedSort = $nextAutoSort;
                    } else {
                        $nextAutoSort = max($nextAutoSort, $requestedSort);
                    }

                    $created[] = $this->createDescriptorFromPayload([
                        'name' => $item['name'],
                        'price' => $item['price'] ?? 0,
                        'is_default' => filter_var($item['is_default'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'sort_order' => $requestedSort,
                    ], false);
                }

                if ($created === []) {
                    throw new \InvalidArgumentException('No valid descriptors provided.');
                }

                $this->ensureDefaultDescriptorExists();

                return collect($created)
                    ->map(fn (QuickSaleDescriptor $descriptor) => $this->formatDescriptor($descriptor->fresh()))
                    ->values()
                    ->all();
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptors added successfully.',
                'count' => count($descriptors),
                'descriptors' => $descriptors,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale bulk add descriptors failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to add Quick Sale descriptors.',
            ], 500);
        }
    }

    /**
     * @param  array{name: mixed, price?: mixed, is_default?: bool, sort_order?: int|null}  $payload
     */
    private function createDescriptorFromPayload(array $payload, bool $ensureDefaultWhenMissing): QuickSaleDescriptor
    {
        $isDefault = (bool) ($payload['is_default'] ?? false);
        $sortOrder = $payload['sort_order'] ?? null;
        if ($sortOrder === null) {
            $sortOrder = max(1, (int) QuickSaleDescriptor::max('sort_order') + 1);
        } else {
            $sortOrder = max(0, (int) $sortOrder);
        }

        if ($isDefault) {
            QuickSaleDescriptor::query()->update(['is_default' => false]);
        }

        if ($ensureDefaultWhenMissing) {
            $hasDefault = QuickSaleDescriptor::where('is_default', true)->exists();
            if (!$hasDefault) {
                $isDefault = true;
            }
        }

        $name = trim((string) ($payload['name'] ?? ''));
        if ($name === '') {
            throw new \InvalidArgumentException('Descriptor name is required.');
        }

        if ($this->descriptorNameExists($name)) {
            throw new \InvalidArgumentException('A descriptor with this name already exists.');
        }

        return QuickSaleDescriptor::create([
            'name' => $name,
            'price' => round((float) ($payload['price'] ?? 0), 2),
            'is_default' => $isDefault,
            'sort_order' => max(1, (int) $sortOrder),
        ]);
    }

    /**
     * Update Descriptor API — update one or many Quick Sale descriptors by id.
     *
     * Single update (unchanged): { "id", "name", "price", "is_default", "sort_order" }
     * Bulk update (additive):     { "descriptors": [ { "id", "name", ... }, ... ] }
     */
    public function updateDescriptor(Request $request): JsonResponse
    {
        if ($request->has('descriptors') && is_array($request->input('descriptors'))) {
            return $this->updateDescriptorsBulk($request);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $descriptor = QuickSaleDescriptor::find((int) $request->input('id'));

        if (!$descriptor) {
            return response()->json([
                'error' => true,
                'message' => 'Quick Sale descriptor not found.',
            ], 404);
        }

        if ($this->descriptorNameExists((string) $request->input('name'), (int) $descriptor->id)) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => [
                    'name' => ['A descriptor with this name already exists.'],
                ],
            ], 422);
        }

        try {
            $descriptor = DB::transaction(function () use ($request, $descriptor) {
                return $this->updateDescriptorFromPayload($descriptor, [
                    'name' => $request->input('name'),
                    'price' => $request->input('price', $descriptor->price ?? 0),
                    'is_default' => $request->has('is_default') ? $request->boolean('is_default') : (bool) $descriptor->is_default,
                    'sort_order' => $request->filled('sort_order') ? (int) $request->input('sort_order') : null,
                ]);
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptor updated successfully.',
                'descriptor' => $this->formatDescriptor($descriptor),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'errors' => str_contains(strtolower($e->getMessage()), 'name')
                    ? ['name' => [$e->getMessage()]]
                    : [],
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale update descriptor failed', [
                'descriptor_id' => $request->input('id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to update Quick Sale descriptor.',
            ], 500);
        }
    }

    /**
     * Additive: bulk update descriptors via descriptors[] on the same update endpoint.
     */
    private function updateDescriptorsBulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'descriptors' => 'required|array|min:1',
            'descriptors.*.id' => 'required|integer|min:1',
            'descriptors.*.name' => 'required|string|max:255',
            'descriptors.*.price' => 'nullable|numeric|min:0',
            'descriptors.*.is_default' => 'nullable|boolean',
            'descriptors.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $duplicateResponse = $this->validateBulkDescriptorNamesUniqueForUpdate($request->input('descriptors', []));
        if ($duplicateResponse instanceof JsonResponse) {
            return $duplicateResponse;
        }

        try {
            $descriptors = DB::transaction(function () use ($request) {
                $items = $request->input('descriptors', []);
                $updated = [];

                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $descriptor = QuickSaleDescriptor::find((int) ($item['id'] ?? 0));
                    if (!$descriptor) {
                        throw new \InvalidArgumentException('One or more Quick Sale descriptors were not found.');
                    }

                    $updated[] = $this->updateDescriptorFromPayload($descriptor, [
                        'name' => $item['name'],
                        'price' => $item['price'] ?? $descriptor->price ?? 0,
                        'is_default' => array_key_exists('is_default', $item)
                            ? filter_var($item['is_default'], FILTER_VALIDATE_BOOLEAN)
                            : (bool) $descriptor->is_default,
                        'sort_order' => array_key_exists('sort_order', $item) && $item['sort_order'] !== null
                            ? max(0, (int) $item['sort_order'])
                            : null,
                    ]);
                }

                if ($updated === []) {
                    throw new \InvalidArgumentException('No valid descriptors provided.');
                }

                $this->ensureDefaultDescriptorExists();

                return collect($updated)
                    ->map(fn (QuickSaleDescriptor $descriptor) => $this->formatDescriptor($descriptor->fresh()))
                    ->values()
                    ->all();
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptors updated successfully.',
                'count' => count($descriptors),
                'descriptors' => $descriptors,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale bulk update descriptors failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to update Quick Sale descriptors.',
            ], 500);
        }
    }

    /**
     * @param  array{name: mixed, price?: mixed, is_default?: bool, sort_order?: int|null}  $payload
     */
    private function updateDescriptorFromPayload(QuickSaleDescriptor $descriptor, array $payload): QuickSaleDescriptor
    {
        $isDefault = (bool) ($payload['is_default'] ?? $descriptor->is_default);

        if ($isDefault) {
            QuickSaleDescriptor::query()
                ->where('id', '!=', $descriptor->id)
                ->update(['is_default' => false]);
        }

        $name = trim((string) ($payload['name'] ?? $descriptor->name));
        if ($name === '') {
            throw new \InvalidArgumentException('Descriptor name is required.');
        }

        if ($this->descriptorNameExists($name, (int) $descriptor->id)) {
            throw new \InvalidArgumentException('A descriptor with this name already exists.');
        }

        $descriptor->name = $name;
        $descriptor->price = round((float) ($payload['price'] ?? $descriptor->price ?? 0), 2);

        if (array_key_exists('sort_order', $payload) && $payload['sort_order'] !== null) {
            $descriptor->sort_order = max(0, (int) $payload['sort_order']);
        }

        $descriptor->is_default = $isDefault;
        $descriptor->save();

        return $descriptor->fresh();
    }

    /**
     * Delete Descriptor API — delete one or many Quick Sale descriptors by id.
     *
     * Single delete (unchanged): { "id" }
     * Bulk delete (additive):    { "ids": [1, 2, 3] } or { "descriptors": [ { "id": 1 }, ... ] }
     */
    public function deleteDescriptor(Request $request): JsonResponse
    {
        $bulkIds = $this->resolveBulkDeleteIds($request);
        if ($bulkIds !== null) {
            return $this->deleteDescriptorsBulk($bulkIds);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $descriptor = QuickSaleDescriptor::find((int) $request->input('id'));

        if (!$descriptor) {
            return response()->json([
                'error' => true,
                'message' => 'Quick Sale descriptor not found.',
            ], 404);
        }

        if (QuickSaleDescriptor::count() <= 1) {
            return response()->json([
                'error' => true,
                'message' => 'At least one Quick Sale descriptor must remain.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($descriptor) {
                $wasDefault = (bool) $descriptor->is_default;
                $descriptor->delete();

                if ($wasDefault) {
                    $this->ensureDefaultDescriptorExists();
                }
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptor deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale delete descriptor failed', [
                'descriptor_id' => $request->input('id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to delete Quick Sale descriptor.',
            ], 500);
        }
    }

    /**
     * Get Descriptors API — list Quick Sale descriptors for POS settings screen.
     */
    public function getDescriptors(Request $request): JsonResponse
    {
        $descriptors = QuickSaleDescriptor::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (QuickSaleDescriptor $descriptor) => $this->formatDescriptor($descriptor))
            ->values();

        return response()->json([
            'error' => false,
            'message' => 'Quick Sale descriptors fetched successfully.',
            'descriptors' => $descriptors,
        ]);
    }

    private function formatDescriptor(QuickSaleDescriptor $descriptor): array
    {
        return [
            'id' => (int) $descriptor->id,
            'name' => $descriptor->name,
            'price' => round((float) $descriptor->price, 2),
            'is_default' => (bool) $descriptor->is_default,
            'sort_order' => (int) $descriptor->sort_order,
            'created_at' => optional($descriptor->created_at)->toDateTimeString(),
            'updated_at' => optional($descriptor->updated_at)->toDateTimeString(),
        ];
    }

    /** Ensure one descriptor is always marked default (lowest sort_order first). */
    private function ensureDefaultDescriptorExists(): void
    {
        if (QuickSaleDescriptor::where('is_default', true)->exists()) {
            return;
        }

        $fallback = QuickSaleDescriptor::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($fallback) {
            $fallback->is_default = true;
            $fallback->save();
        }
    }

    private function normalizeDescriptorName(string $name): string
    {
        return strtolower(trim($name));
    }

    private function descriptorNameExists(string $name, ?int $ignoreId = null): bool
    {
        $normalized = $this->normalizeDescriptorName($name);
        if ($normalized === '') {
            return false;
        }

        return QuickSaleDescriptor::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->exists();
    }

    /** @param  array<int, mixed>  $items */
    private function validateBulkDescriptorNamesUnique(array $items): ?JsonResponse
    {
        $seen = [];
        $errors = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $normalized = $this->normalizeDescriptorName($name);

            if ($normalized === '') {
                continue;
            }

            if (isset($seen[$normalized])) {
                $errors["descriptors.{$index}.name"] = ['Duplicate descriptor name in request.'];
                continue;
            }

            $seen[$normalized] = true;

            if ($this->descriptorNameExists($name)) {
                $errors["descriptors.{$index}.name"] = ['A descriptor with this name already exists.'];
            }
        }

        if ($errors !== []) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        return null;
    }

    /** @param  array<int, mixed>  $items */
    private function validateBulkDescriptorNamesUniqueForUpdate(array $items): ?JsonResponse
    {
        $seen = [];
        $errors = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $id = (int) ($item['id'] ?? 0);
            $name = trim((string) ($item['name'] ?? ''));
            $normalized = $this->normalizeDescriptorName($name);

            if ($id <= 0) {
                $errors["descriptors.{$index}.id"] = ['Descriptor id is required.'];
                continue;
            }

            if (!QuickSaleDescriptor::where('id', $id)->exists()) {
                $errors["descriptors.{$index}.id"] = ['Quick Sale descriptor not found.'];
                continue;
            }

            if ($normalized === '') {
                continue;
            }

            if (isset($seen[$normalized])) {
                $errors["descriptors.{$index}.name"] = ['Duplicate descriptor name in request.'];
                continue;
            }

            $seen[$normalized] = true;

            if ($this->descriptorNameExists($name, $id)) {
                $errors["descriptors.{$index}.name"] = ['A descriptor with this name already exists.'];
            }
        }

        if ($errors !== []) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], 422);
        }

        return null;
    }

    /** @return array<int, int>|null null = not bulk delete mode */
    private function resolveBulkDeleteIds(Request $request): ?array
    {
        if ($request->has('ids') && is_array($request->input('ids'))) {
            $ids = array_values(array_unique(array_filter(array_map(
                fn ($id) => (int) $id,
                $request->input('ids', [])
            ), fn ($id) => $id > 0)));

            return $ids === [] ? [] : $ids;
        }

        if ($request->has('descriptors') && is_array($request->input('descriptors'))) {
            $ids = [];

            foreach ($request->input('descriptors', []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $id = (int) ($item['id'] ?? 0);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }

            $ids = array_values(array_unique($ids));

            return $ids;
        }

        return null;
    }

    /** @param  array<int, int>  $ids */
    private function deleteDescriptorsBulk(array $ids): JsonResponse
    {
        if ($ids === []) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => [
                    'ids' => ['At least one descriptor id is required for bulk delete.'],
                ],
            ], 422);
        }

        $existingIds = QuickSaleDescriptor::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $missingIds = array_values(array_diff($ids, $existingIds));
        if ($missingIds !== []) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed.',
                'errors' => [
                    'ids' => ['One or more Quick Sale descriptors were not found.'],
                    'missing_ids' => $missingIds,
                ],
            ], 422);
        }

        $totalCount = QuickSaleDescriptor::count();
        if (($totalCount - count($ids)) < 1) {
            return response()->json([
                'error' => true,
                'message' => 'At least one Quick Sale descriptor must remain.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($ids) {
                $hadDefault = QuickSaleDescriptor::query()
                    ->whereIn('id', $ids)
                    ->where('is_default', true)
                    ->exists();

                QuickSaleDescriptor::query()->whereIn('id', $ids)->delete();

                if ($hadDefault) {
                    $this->ensureDefaultDescriptorExists();
                }
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptors deleted successfully.',
                'count' => count($ids),
                'deleted_ids' => $ids,
            ]);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale bulk delete descriptors failed', [
                'ids' => $ids,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Unable to delete Quick Sale descriptors.',
            ], 500);
        }
    }
}
