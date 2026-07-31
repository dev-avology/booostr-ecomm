<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Term;
use Carbon\Carbon;
use Cart;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Storedata API coupon endpoints (additive).
 * Applies coupon discount onto an existing cartId so /order/create
 * can read Cart::discount() / Cart::total() without Stripe/checkout changes.
 */
class CouponApiController extends Controller
{
    private const CART_COUPON_CACHE_PREFIX = 'api_cart_coupon_';
    private const CART_COUPON_CACHE_TTL_MINUTES = 120;

    /**
     * POST /api/storedata/coupon/apply
     */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'cartId'      => 'required|string',
            'coupon_code' => 'required|string|max:100',
            'store_name'  => 'nullable|string|max:150',
        ]);

        $cartId = $data['cartId'];
        $couponCode = trim($data['coupon_code']);

        Cart::instance($cartId);
        Cart::restore($cartId);

        if (Cart::content()->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty or expired. Please add items again.',
                'error'   => 'count_error',
                'result'  => $this->emptyCartTotals($cartId),
            ], 400);
        }

        $this->clearCartDiscounts();

        $today = Carbon::now();
        $error = false;
        $errorPayload = [];

        $coupon = Coupon::where('code', $couponCode)->first();

        if (empty($coupon)) {
            $error = true;
            $errorPayload = [
                'error'   => 'not_exit_error',
                'message' => 'Oops this coupon is not available...',
            ];
        }

        if (!$error && !Carbon::parse($coupon->start_from)->lessThanOrEqualTo($today)) {
            $error = true;
            $errorPayload = [
                'error'   => 'not_exit_error',
                'message' => 'Oops this coupon is not available...',
            ];
        }

        if (!$error && $coupon->will_expire != null && Carbon::parse($coupon->will_expire)->lessThan($today)) {
            $error = true;
            $errorPayload = [
                'error'   => 'not_exit_error',
                'message' => 'Oops this coupon is expired...',
            ];
        }

        if (!$error && $coupon->max_use > 0 && $coupon->max_use <= $coupon->used_count) {
            $error = true;
            $errorPayload = [
                'error'   => 'count_error',
                'message' => 'Coupon code is max used',
            ];
        }

        $scope = 'cart';
        $discountResult = null;

        if (!$error) {
            $couponType = $coupon->coupon_for_name;

            switch ($couponType) {
                case 'product':
                    $discountResult = $this->discountCartProduct($coupon);
                    $scope = 'item';
                    break;
                case 'category':
                    $discountResult = $this->discountCartCategoryProduct($coupon);
                    $scope = 'item';
                    break;
                default:
                    $discountResult = $this->discountCart($coupon);
                    $scope = 'cart';
                    break;
            }

            if (($discountResult['status'] ?? 422) == 422) {
                $error = true;
                $errorPayload = [
                    'error'   => $discountResult['error'] ?? 'coupon_error',
                    'message' => $discountResult['msg'] ?? 'Coupon could not be applied',
                ];
                $this->clearCartDiscounts();
            }
        }

        $this->persistCart($cartId);

        $totals = $this->buildTotals($cartId, $scope);

        if ($error) {
            Cache::forget(self::CART_COUPON_CACHE_PREFIX . $cartId);

            return response()->json([
                'success' => false,
                'message' => $errorPayload['message'] ?? 'Coupon could not be applied',
                'error'   => $errorPayload['error'] ?? 'coupon_error',
                'result'  => $totals,
            ], 422);
        }

        Cache::put(
            self::CART_COUPON_CACHE_PREFIX . $cartId,
            $coupon->code,
            now()->addMinutes(self::CART_COUPON_CACHE_TTL_MINUTES)
        );

        $totals['coupon_code'] = $coupon->code;
        $totals['scope'] = $scope;

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'result'  => $totals,
        ]);
    }

    /**
     * POST /api/storedata/coupon/remove
     */
    public function remove(Request $request)
    {
        $data = $request->validate([
            'cartId'     => 'required|string',
            'store_name' => 'nullable|string|max:150',
        ]);

        $cartId = $data['cartId'];

        Cart::instance($cartId);
        Cart::restore($cartId);

        if (Cart::content()->isEmpty()) {
            Cache::forget(self::CART_COUPON_CACHE_PREFIX . $cartId);

            return response()->json([
                'success' => false,
                'message' => 'Cart is empty or expired. Please add items again.',
                'error'   => 'count_error',
                'result'  => $this->emptyCartTotals($cartId),
            ], 400);
        }

        $this->clearCartDiscounts();
        $this->persistCart($cartId);
        Cache::forget(self::CART_COUPON_CACHE_PREFIX . $cartId);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'result'  => $this->buildTotals($cartId, null),
        ]);
    }

    private function clearCartDiscounts(): void
    {
        Cart::content()->each(function ($item) {
            $item->setDiscountRate(0);
        });
        Cart::setGlobalDiscount(0);
    }

    private function persistCart(string $cartId): void
    {
        try {
            Cart::store($cartId);
        } catch (Exception $e) {
            Cart::updatestore($cartId);
        }
    }

    private function discountCart(Coupon $coupon): array
    {
        $subTotal = (float) str_replace(',', '', Cart::subtotal());

        if ($coupon->min_amount_option == 1 && $subTotal < $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order amount is ' . number_format($coupon->min_amount, 2) . ' for this coupon',
            ];
        }

        if ($coupon->min_amount_option == 2 && Cart::count() < $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order item count is ' . number_format($coupon->min_amount, 2) . ' for this coupon',
            ];
        }

        // Persist via item discount rates so Cart::store survives across API calls.
        if ((int) $coupon->is_percentage === 1) {
            $percent = (float) $coupon->value;
        } else {
            $discountAmount = min((float) $coupon->value, $subTotal);
            $percent = $subTotal > 0 ? ($discountAmount * 100) / $subTotal : 0;
        }

        Cart::setGlobalDiscount(min(100, max(0, $percent)));

        return ['status' => 200, 'discount' => (float) str_replace(',', '', Cart::discount())];
    }

    private function discountCartProduct(Coupon $coupon): array
    {
        $productIds = json_decode($coupon->coupon_for_id);
        $filteredCart = Cart::content()->filter(function ($item) use ($productIds) {
            return in_array($item->id, (array) $productIds);
        });

        if ($filteredCart->count() == 0) {
            return [
                'status' => 422,
                'error'  => 'count_error',
                'msg'    => 'Coupon code is not valid for your cart',
            ];
        }

        $filteredSubTotal = $filteredCart->sum(function ($item) {
            return $item->price * (int) $item->qty;
        });
        $filteredCount = $filteredCart->sum(function ($item) {
            return (int) $item->qty;
        });

        if ($coupon->min_amount_option == 1 && $filteredSubTotal < $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order amount is ' . number_format($coupon->min_amount, 2) . ' for this coupon, cart product subtotal:' . number_format($filteredSubTotal),
            ];
        }

        if ($coupon->min_amount_option == 2 && (int) $filteredCount < (int) $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order item count is ' . (int) $coupon->min_amount . ' for this coupon',
            ];
        }

        $filteredCart->each(function ($item) use ($coupon) {
            if ((int) $coupon->is_percentage === 1) {
                $percent = $coupon->value;
            } else {
                $total = (float) $item->price;
                $percent = $total > 0 ? (($coupon->value * 100) / $total) : 0;
            }
            $item->setDiscountRate($percent);
        });

        return ['status' => 200, 'discount' => Cart::discount()];
    }

    private function discountCartCategoryProduct(Coupon $coupon): array
    {
        $catId = json_decode($coupon->coupon_for_id);
        $termData = Term::where('type', 'product')->whereHas('category', function ($query) use ($catId) {
            $query->whereIn('id', (array) $catId);
        })->pluck('id')->toArray();

        $filteredCart = Cart::content()->filter(function ($item) use ($termData) {
            return in_array($item->id, $termData);
        });

        if ($filteredCart->count() == 0) {
            return [
                'status' => 422,
                'error'  => 'count_error',
                'msg'    => 'Coupon code is not valid for your cart',
            ];
        }

        $filteredSubTotal = $filteredCart->sum(function ($item) {
            return $item->price * $item->qty;
        });
        $filteredCount = $filteredCart->sum(function ($item) {
            return (int) $item->qty;
        });

        if ($coupon->min_amount_option == 1 && $filteredSubTotal < $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order amount is ' . number_format($coupon->min_amount, 2) . ' for this coupon',
            ];
        }

        if ($coupon->min_amount_option == 2 && (int) $filteredCount < (int) $coupon->min_amount) {
            return [
                'status' => 422,
                'error'  => 'min_amount_error',
                'msg'    => 'The minumum order item count is ' . (int) $coupon->min_amount . ' for this coupon',
            ];
        }

        $filteredCart->each(function ($item) use ($coupon) {
            if ((int) $coupon->is_percentage === 1) {
                $percent = $coupon->value;
            } else {
                $total = (float) $item->price;
                $percent = $total > 0 ? (($coupon->value * 100) / $total) : 0;
            }
            $item->setDiscountRate($percent);
        });

        return ['status' => 200, 'discount' => Cart::discount()];
    }

    private function buildTotals(string $cartId, ?string $scope): array
    {
        // Cart::subtotal() is post-discount (priceTotal - discountTotal); keep the
        // response subtotal equal to the cart's original Subtotal (price x qty).
        $subtotal = (float) Cart::content()->sum(function ($item) {
            return (float) $item->price * (int) $item->qty;
        });
        $tax = (float) str_replace(',', '', Cart::tax());
        $discount = (float) str_replace(',', '', Cart::discount());
        $total = (float) str_replace(',', '', Cart::total());

        return [
            'cartId'      => $cartId,
            'coupon_code' => Cache::get(self::CART_COUPON_CACHE_PREFIX . $cartId),
            'scope'       => $scope,
            // Two-decimal formatted so JSON always shows e.g. "20.00" (plain 20.00 would render as 20).
            'subtotal'    => number_format($subtotal, 2, '.', ''),
            'discount'    => number_format($discount, 2, '.', ''),
            'tax'         => number_format($tax, 2, '.', ''),
            'total'       => number_format($total, 2, '.', ''),
            'cart_count'  => Cart::count(),
        ];
    }

    private function emptyCartTotals(string $cartId): array
    {
        return [
            'cartId'      => $cartId,
            'coupon_code' => null,
            'scope'       => null,
            'subtotal'    => number_format(0, 2, '.', ''),
            'discount'    => number_format(0, 2, '.', ''),
            'tax'         => number_format(0, 2, '.', ''),
            'total'       => number_format(0, 2, '.', ''),
            'cart_count'  => 0,
        ];
    }
}
