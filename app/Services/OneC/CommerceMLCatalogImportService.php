<?php

namespace App\Services\OneC;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CommerceMLCatalogImportService
{
    public function import(string $xmlPath): Response
    {
        $xml = CommerceMLXmlHelper::load($xmlPath);

        $locale = config('onec.default_locale', 'ru');

        // флаг частичной выгрузки: СодержитТолькоИзменения="true"
        $onlyChanges = false;
        if (isset($xml['СодержитТолькоИзменения'])) {
            $onlyChanges = (string)$xml['СодержитТолькоИзменения'] === 'true';
        }

        DB::transaction(function () use ($xml, $locale, $onlyChanges) {

            // 1) Группы
            // Ищем: Классификатор->Группы->Группа (структура бывает разная)
            $groups = $xml->xpath('//Классификатор//Группы//Группа') ?: [];

            foreach ($groups as $g) {
                $id = CommerceMLXmlHelper::str($g->Ид);
                $name = CommerceMLXmlHelper::str($g->Наименование) ?? '—';
                if (!$id) continue;

                DB::table('onec_groups')->updateOrInsert(
                    ['external_id' => $id],
                    [
                        'name' => $name,
                        'parent_external_id' => null,
                        'locale' => $locale,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // 2) Товары
            $products = $xml->xpath('//Каталог//Товары//Товар') ?: [];

            foreach ($products as $p) {
                $externalId = CommerceMLXmlHelper::str($p->Ид);
                if (!$externalId) continue;

                $title = CommerceMLXmlHelper::str($p->Наименование);
                $sku   = CommerceMLXmlHelper::str($p->Артикул);
                $unit  = CommerceMLXmlHelper::str($p->БазоваяЕдиница) ?: null;

                // Картинки: <Картинка>import_files/..</Картинка>
                $pictures = [];
                if (isset($p->Картинка)) {
                    foreach ($p->Картинка as $pic) {
                        $val = trim((string)$pic);
                        if ($val !== '') {
                            $val = ltrim($val, '/');
                            $pictures[] = '1c/' . $val; // потом: asset('storage/'.$path)
                        }
                    }
                }

                $model = Product::query()->where('external_id', $externalId)->first();
                if (!$model) {
                    $model = new Product();
                    $model->external_id = $externalId;
                    $model->locale = $model->locale ?? $locale; // если есть поле locale
                }

                // обновляем только то, что пришло
                if ($title !== null) $model->title = $title;
                if ($sku !== null)   $model->sku = $sku;
                if ($unit !== null)  $model->unit = $unit;

                // Ключевой момент:
                // Если частичная выгрузка и картинок нет — НЕ ТРОГАЕМ картинки
                if (!$onlyChanges) {
                    // при полной выгрузке можно обновлять картинки
                    if (!empty($pictures)) {
                        // если у тебя есть product_images таблица — тут надо писать туда
                        // иначе можешь хранить основную картинку в announce_image_path
                        $model->announce_image_path = $pictures[0];
                    }
                } else {
                    // partial:
                    // обновляем картинки только если реально пришли
                    if (!empty($pictures)) {
                        $model->announce_image_path = $pictures[0];
                    }
                }

                $model->onec_raw = [
                    'imported_at' => now()->toDateTimeString(),
                ];

                $model->save();
            }
        });

        return response("success");
    }
}
