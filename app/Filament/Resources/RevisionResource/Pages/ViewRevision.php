<?php

namespace App\Filament\Resources\RevisionResource\Pages;

use App\Filament\Resources\RevisionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRevision extends ViewRecord
{
    protected static string $resource = RevisionResource::class;

    public function getView(): string
    {
        return 'filament.revisions.view';
    }

    protected function getViewData(): array
    {
        $old = $this->record->old_values ?? [];
        $new = $this->record->new_values ?? [];

        $fields = array_unique(array_merge(array_keys($old), array_keys($new)));

        $changes = [];

        foreach ($fields as $field) {
            $ov = $old[$field] ?? null;
            $nv = $new[$field] ?? null;

            if ($ov !== $nv) {
                $changes[$field] = [
                    'old' => is_array($ov) ? json_encode($ov, JSON_UNESCAPED_UNICODE) : $ov,
                    'new' => is_array($nv) ? json_encode($nv, JSON_UNESCAPED_UNICODE) : $nv,
                ];
            }
        }

        return [
            'record'  => $this->record,
            'changes' => $changes,
        ];
    }
}
