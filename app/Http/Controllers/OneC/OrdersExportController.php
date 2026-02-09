<?php

namespace App\Http\Controllers\OneC;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrdersExportController
{
    // куда складываем файлы для 1С
    private string $dir = '1c/outgoing';

    public function buildFile(): string
    {
        $order = Order::query()
            ->with(['items.product'])
            ->orderByDesc('ordered_at')
            ->first();

        $xml = $this->buildCommerceMl($order ? [$order] : []);

        $name = 'orders_' . Carbon::now()->format('Ymd_His') . '.xml';
        Storage::disk('local')->put($this->dir . '/' . $name, $xml);

        return $name;
    }

    public function buildXml(): string
    {
        $order = Order::query()
            ->with(['items.product'])
            ->orderByDesc('ordered_at')
            ->first();

        return $this->buildCommerceMl($order ? [$order] : []);
    }

    public function emptyXml(): string
    {
        $now = Carbon::now()->format('Y-m-d\TH:i:s');
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" ВерсияСхемы="2.08" ДатаФормирования="'.$now.'">' . "\n"
            . '</КоммерческаяИнформация>';
    }

    /**
     * ✅ Возвращает имя первого файла orders_*.xml из outgoing или null
     */
    public function nextOutgoingFilename(): ?string
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($this->dir)) {
            return null;
        }

        $paths = $disk->files($this->dir);

        $paths = array_values(array_filter($paths, function ($p) {
            $base = basename($p);
            return Str::startsWith($base, 'orders_') && Str::endsWith($base, '.xml');
        }));

        if (!$paths) {
            return null;
        }

        sort($paths); // берём самый “ранний” по имени
        return basename($paths[0]);
    }

    /**
     * ✅ Забрать XML из outgoing:
     * - нет файлов -> пустой XML
     * - есть файл -> вернуть содержимое и (по умолчанию) удалить, чтобы 1С не зациклилась
     */
    public function takeOutgoingXml(bool $deleteAfterSend = true): string
    {
        $disk = Storage::disk('local');
        $filename = $this->nextOutgoingFilename();

        if (!$filename) {
            return $this->emptyXml();
        }

        $path = $this->dir . '/' . $filename;

        if (!$disk->exists($path)) {
            return $this->emptyXml();
        }

        $xml = $disk->get($path);

        if ($deleteAfterSend) {
            $disk->delete($path);
        }

        return $xml;
    }

    public function downloadFile(string $filename)
    {
        if ($filename === '') {
            return response("failure\nfilename is empty\n", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }

        $path = $this->dir . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response("failure\nfile not found: {$filename}\n", 200)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }

        $bytes = Storage::disk('local')->get($path);

        \Log::channel('onec')->info('[1C] SALE_FILE_OUT', [
            'filename' => $filename,
            'path' => $path,
            'size' => strlen($bytes),
            'head' => substr($bytes, 0, 120),
        ]);

        return response($bytes, 200, [
            'Content-Type' => 'text/xml; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    private function buildCommerceMl($orders): string
    {
        $now = Carbon::now()->format('Y-m-d\TH:i:s');
        $order = $orders[0] ?? null;

        $time = $order
            ? Carbon::parse($order->ordered_at ?? $order->created_at)->format('H:i:s')
            : Carbon::now()->format('H:i:s');

        $date1c = $order
            ? Carbon::parse($order->ordered_at ?? $order->created_at)->format('Y-m-d')
            : Carbon::now()->format('Y-m-d');

        // ВАЖНО: Номер нужен, потому что 1С у тебя проверяет уникальность по ДокументXDTO.Номер
        $number = $order?->number ?: (string)($order?->id ?: '');

        $items = $order?->items ?? collect();

        $sumInt = 0;
        foreach ($items as $it) {
            $qty = (int) round((float)($it->qty ?? $it->quantity ?? 1));
            $price = (float)($it->unit_price ?? $it->unit_amount ?? 0);
            $priceInt = (int) round($price);
            $sumInt += $priceInt * $qty;
        }

        $out = [];
        $out[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $out[] = '<КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" ВерсияСхемы="2.08" ДатаФормирования="' . $now . '">';

        // ✅ Документ(ы) прямо под корнем (без <Документы>)
        if ($order) {
            $out[] = "\t<Документ>";
            $out[] = "\t\t<Ид>" . htmlspecialchars((string)$order->id, ENT_XML1) . "</Ид>";
            $out[] = "\t\t<Номер>" . htmlspecialchars((string)$number, ENT_XML1) . "</Номер>";
            $out[] = "\t\t<Дата>{$date1c}</Дата>";
            $out[] = "\t\t<Время>{$time}</Время>";
            $out[] = "\t\t<ХозОперация>Заказ товара</ХозОперация>";
            $out[] = "\t\t<Роль>Продавец</Роль>";
            $out[] = "\t\t<Валюта>MDL</Валюта>";
            $out[] = "\t\t<Курс>1</Курс>";
            $out[] = "\t\t<Сумма>{$sumInt}</Сумма>";

            $out[] = "\t\t<Товары>";
            foreach ($items as $it) {
                $product = $it->product;

                $productId = $product->external_id
                    ?? $product->base_external_id
                    ?? (string)($it->product_id ?? '');

                $name = $it->title
                    ?? $it->name
                    ?? $product?->title
                    ?? 'Товар';

                $qty = (int) round((float)($it->qty ?? $it->quantity ?? 1));
                $price = (float)($it->unit_price ?? $it->unit_amount ?? 0);
                if ($price <= 0 && $product) {
                    $price = (float)($product->sale_price ?: $product->price ?: 0);
                }
                $priceInt = (int) round($price);
                $lineSum = $priceInt * $qty;

                $out[] = "\t\t\t<Товар>";
                $out[] = "\t\t\t\t<Ид>" . htmlspecialchars((string)$productId, ENT_XML1) . "</Ид>";
                $out[] = "\t\t\t\t<Наименование>" . htmlspecialchars((string)$name, ENT_XML1) . "</Наименование>";
                $out[] = "\t\t\t\t<ЦенаЗаЕдиницу>{$priceInt}</ЦенаЗаЕдиницу>";
                $out[] = "\t\t\t\t<Количество>{$qty}</Количество>";
                $out[] = "\t\t\t\t<Сумма>{$lineSum}</Сумма>";
                $out[] = "\t\t\t</Товар>";
            }
            $out[] = "\t\t</Товары>";

            $out[] = "\t\t<ЗначенияРеквизитов/>";
            $out[] = "\t</Документ>";
        }

        $out[] = '</КоммерческаяИнформация>';

        return implode("\n", $out);
    }
}
