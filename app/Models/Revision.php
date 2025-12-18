<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Revision extends Model
{
    protected $fillable = [
        'event',
        'old_values',
        'new_values',
        'user_id',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function revisionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rollback(): void
    {
        DB::transaction(function () {

            $modelClass = $this->revisionable_type;
            $modelId    = $this->revisionable_id;

            /** @var \Illuminate\Database\Eloquent\Model|null $model */
            $model = $modelClass::withTrashed()->find($modelId);

            match ($this->event) {

                // ✏️ UPDATE → вернуть старые значения
                'updated' => $model?->forceFill(
                    $this->old_values ?? []
                )->save(),

                // ➕ CREATED → удалить запись
                'created' => $model?->delete(),

                // 🗑 DELETED → восстановить и применить данные
                'deleted' => $modelClass::create(
                    $this->old_values ?? []
                ),

                default => null,
            };
        });
    }
}
