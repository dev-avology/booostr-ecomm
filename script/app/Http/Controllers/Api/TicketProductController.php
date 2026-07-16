<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Term;
use App\Models\Termmeta;
use Illuminate\Http\Request;
use Throwable;

class TicketProductController extends Controller
{
    /**
     * GET /api/storedata/get-ticket-product?club_id=hello-tester-club
     *
     * Returns Online Ticket type products for the given club/tenant
     * (same Type column rule as /seller/product: is_variation = 2).
     */
    public function index(Request $request)
    {
        $clubId = trim((string) ($request->query('club_id') ?? $request->input('club_id') ?? ''));

        if ($clubId === '') {
            return response()->json([
                'status' => false,
                'message' => 'club_id is required.',
                'result' => [],
            ], 422);
        }

        // club_id param is the tenant id/slug (e.g. hello-tester-club).
        $tenant = Tenant::find($clubId);

        if (!$tenant) {
            return response()->json([
                'status' => false,
                'message' => 'Club/tenant not found for the given club_id.',
                'result' => [],
            ], 404);
        }

        $tenancyWasInitialized = tenancy()->initialized;

        try {
            if (!$tenancyWasInitialized) {
                tenancy()->initialize($tenant);
            }

            $products = Term::query()
                ->where('type', 'product')
                ->where('is_variation', 2)
                ->with(['media', 'price', 'formType'])
                ->withCount('orders')
                ->orderBy('order', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            $result = $products->map(function (Term $product) {
                $basePrice = (float) ($product->price->price ?? 0);
                $ticketFee = 0.75;
                $ticketFeeMeta = Termmeta::where('term_id', $product->id)
                    ->where('key', 'ticket_fee')
                    ->value('value');

                if ($ticketFeeMeta !== null && $ticketFeeMeta !== '') {
                    $ticketFee = (float) $ticketFeeMeta;
                }

                $preview = $product->media->value ?? null;

                return [
                    'id' => $product->id,
                    'full_id' => $product->full_id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'status' => (int) $product->status,
                    'status_label' => ((int) $product->status === 1) ? 'Active' : 'Disable',
                    'type' => 'Online Ticket',
                    'is_variation' => (int) $product->is_variation,
                    'list_type' => (int) ($product->list_type ?? 0),
                    'price' => round($basePrice, 2),
                    'ticket_fee' => round($ticketFee, 2),
                    'display_price' => round($basePrice + $ticketFee, 2),
                    'sales' => (int) ($product->orders_count ?? 0),
                    'preview' => $preview,
                    'preview_url' => $preview ? asset($preview) : asset('uploads/default.png'),
                    'has_linked_form' => !empty($product->formType),
                    'created_at' => optional($product->created_at)->toDateTimeString(),
                    'updated_at' => optional($product->updated_at)->toDateTimeString(),
                ];
            })->values();

            return response()->json([
                'status' => true,
                'message' => 'Online ticket products fetched successfully.',
                'club_id' => $clubId,
                'count' => $result->count(),
                'result' => $result,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch online ticket products.',
                'error' => $e->getMessage(),
                'result' => [],
            ], 500);
        } finally {
            if (!$tenancyWasInitialized && tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}
