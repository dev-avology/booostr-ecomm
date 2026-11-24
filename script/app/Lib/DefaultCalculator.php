<?php

namespace App\Lib;

use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Contracts\Calculator;

class DefaultCalculator implements Calculator
{
    public static function getAttribute(string $attribute, CartItem $cartItem)
    {
        $decimals = config('cart.format.decimals', 2);

        return match ($attribute) {
            'discount' => $cartItem->price * ($cartItem->getDiscountRate() / 100),
            'tax' => round($cartItem->priceTarget * ($cartItem->taxRate / 100), $decimals),
            'priceTax' => round($cartItem->priceTarget + $cartItem->tax, $decimals),
            'discountTotal' => round($cartItem->discount * (int)$cartItem->qty, $decimals),
            'priceTotal' => round($cartItem->price * (int)$cartItem->qty, $decimals),
            'subtotal' => max(round($cartItem->priceTotal - $cartItem->discountTotal, $decimals), 0),
            'priceTarget' => round(($cartItem->priceTotal - $cartItem->discountTotal) / (int)$cartItem->qty, $decimals),
            'taxTotal' => round($cartItem->subtotal * ($cartItem->taxRate / 100), $decimals),
            'total' => round($cartItem->subtotal + $cartItem->taxTotal, $decimals),
            default => null,
        };
    }
}
