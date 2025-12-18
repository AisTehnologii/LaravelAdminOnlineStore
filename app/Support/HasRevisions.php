<?php

namespace App\Support;

use App\Models\Revision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait HasRevisions
{
    // временно держим old-values тут, чтобы не попадало в SQL update
    protected static array $revisionOld = [];

    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable')->latest();
    }

    protected static function bootHasRevisions(): void
    {
        static::created(function ($model) {
            $model->storeRevision('created', null, $model->revisionSnapshot());
        });

        static::updating(function ($model) {
            // ключ на модель
            $key = static::class . ':' . ($model->getKey() ?? 'new');
            static::$revisionOld[$key] = $model->revisionSnapshot(fromOriginal: true);
        });

        static::updated(function ($model) {
            $key = static::class . ':' . ($model->getKey() ?? 'new');
            $old = static::$revisionOld[$key] ?? null;
            unset(static::$revisionOld[$key]);

            $new = $model->revisionSnapshot();

            if ($old === $new) {
                return;
            }

            $model->storeRevision('updated', $old, $new);
        });

        static::deleted(function ($model) {
            $model->storeRevision('deleted', $model->revisionSnapshot(fromOriginal: true), null);
        });
    }

    protected function revisionSnapshot(bool $fromOriginal = false): array
    {
        $data = $fromOriginal ? $this->getOriginal() : $this->getAttributes();

        $fillable = $this->getFillable();
        if (!empty($fillable)) {
            $data = array_intersect_key($data, array_flip($fillable));
        }

        ksort($data);
        return $data;
    }

    protected function storeRevision(string $event, ?array $old, ?array $new): void
    {
        $this->revisions()->create([
            'event'      => $event,
            'old_values' => $old,
            'new_values' => $new,
            'user_id'    => Auth::id(),
            'ip'         => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 1000),
        ]);
    }
}
