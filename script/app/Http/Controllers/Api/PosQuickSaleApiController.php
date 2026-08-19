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

        return QuickSaleDescriptor::create([
            'name' => trim((string) ($payload['name'] ?? '')),
            'price' => round((float) ($payload['price'] ?? 0), 2),
            'is_default' => $isDefault,
            'sort_order' => max(1, (int) $sortOrder),
        ]);
    }

    /**
     * Update Descriptor API — update Quick Sale descriptor by id.
     */
    public function updateDescriptor(Request $request): JsonResponse
    {
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

        try {
            $descriptor = DB::transaction(function () use ($request, $descriptor) {
                $isDefault = $request->has('is_default')
                    ? $request->boolean('is_default')
                    : (bool) $descriptor->is_default;

                if ($isDefault) {
                    QuickSaleDescriptor::query()
                        ->where('id', '!=', $descriptor->id)
                        ->update(['is_default' => false]);
                }

                $descriptor->name = trim((string) $request->input('name'));
                $descriptor->price = round((float) $request->input('price', $descriptor->price ?? 0), 2);

                if ($request->filled('sort_order')) {
                    $descriptor->sort_order = max(0, (int) $request->input('sort_order'));
                }

                $descriptor->is_default = $isDefault;
                $descriptor->save();

                $this->ensureDefaultDescriptorExists();

                return $descriptor->fresh();
            });

            return response()->json([
                'error' => false,
                'message' => 'Quick Sale descriptor updated successfully.',
                'descriptor' => $this->formatDescriptor($descriptor),
            ]);
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
     * Delete Descriptor API — remove Quick Sale descriptor by id.
     */
    public function deleteDescriptor(Request $request): JsonResponse
    {
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
}
