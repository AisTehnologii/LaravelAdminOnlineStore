<?php

namespace App\Support;

class Pricing
{
    public static function basePrice($product): float
    {
        $sale = (float)($product->sale_price ?? 0);
        $price = (float)($product->price ?? 0);
        return ($sale > 0 ? $sale : $price);
    }

    public static function applyPercent(float $amount, int $percent): float
    {
        $percent = max(0, min(90, $percent));
        return round($amount * (1 - $percent / 100), 2);
    }
}
