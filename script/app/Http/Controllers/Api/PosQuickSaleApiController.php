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
     * Add Descriptor API — create a Quick Sale descriptor for this club/tenant.
     */
    public function addDescriptor(Request $request): JsonResponse
    {
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
                $isDefault = $request->boolean('is_default');
                $sortOrder = $request->filled('sort_order')
                    ? (int) $request->input('sort_order')
                    : ((int) QuickSaleDescriptor::max('sort_order') + 1);

                if ($isDefault) {
                    QuickSaleDescriptor::query()->update(['is_default' => false]);
                }

                $hasDefault = QuickSaleDescriptor::where('is_default', true)->exists();
                if (!$hasDefault) {
                    $isDefault = true;
                }

                return QuickSaleDescriptor::create([
                    'name' => trim((string) $request->input('name')),
                    'price' => round((float) $request->input('price', 0), 2),
                    'is_default' => $isDefault,
                    'sort_order' => max(1, $sortOrder),
                ]);
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
}
