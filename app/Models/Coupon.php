<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'percent', 'is_active',
        'starts_at', 'ends_at', 'max_uses', 'used_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function scopeActive($q)
    {
        $now = now();

        return $q->where('is_active', 1)
            ->where(function ($qq) use ($now) {
                $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($qq) use ($now) {
                $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where(function ($qq) {
                $qq->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    public static function normalize(string $code): string
    {
        return mb_strtoupper(trim($code));
    }
}
