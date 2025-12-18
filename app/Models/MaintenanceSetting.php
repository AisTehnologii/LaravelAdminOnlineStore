<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    protected $fillable = [
        'id',           // ✅ важно!
        'enabled',
        'message',
        'until',
        'allow_ips',
        'updated_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'until' => 'datetime',
        'allow_ips' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'enabled' => false,
                'message' => 'Сайт временно на обслуживании. Попробуйте позже.',
                'allow_ips' => [],
            ]
        );
    }
}
