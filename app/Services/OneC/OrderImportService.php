<?php

namespace App\Services\OneC;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderImportService
{
    public function import(\SimpleXMLElement $xml, string $file = ''): void
    {
        $docs = $xml->xpath('//*[local-name()="Документ"]') ?: [];

        Log::channel('onec')->info('[1C] SALE_DOCS_FOUND', [
            'file' => $file,
            'docs' => count($docs),
        ]);

        foreach ($docs as $doc) {
            $this->importOneDocument($doc, $file);
        }
    }

    private function importOneDocument(\SimpleXMLElement $doc, string $file = ''): void
    {
        $id    = $this->text($doc, './*[local-name()="Ид"]');
        $num1c = $this->text($doc, './*[local-name()="Номер1С"]');
        $date1c = $this->text($doc, './*[local-name()="Дата1С"]');
        $time  = $this->text($doc, './*[local-name()="Время"]');

        $sum   = $this->float($this->text($doc, './*[local-name()="Сумма"]')) ?? 0;
        $cur   = $this->text($doc, './*[local-name()="Валюта"]') ?? 'MDL';

        // В твоём примере Ид = 0, поэтому делаем нормальный fallback
        $externalId = ($id && $id !== '0') ? $id : ($num1c ?: null);

        if (!$externalId) {
            Log::channel('onec')->warning('[1C] SALE_SKIP_NO_ID', [
                'file' => $file,
            ]);
            return;
        }

        $orderedAt = $this->parseOrderedAt($date1c, $time);

        // Клиентские данные иногда лежат в ЗначенияРеквизитов — если появятся, подхватим
        $customerName  = $this->findRequisite($doc, ['ФИО', 'Имя', 'Покупатель']);
        $customerPhone = $this->findRequisite($doc, ['Телефон', 'ТелефонПокупателя', 'НомерТелефона']);
        $customerEmail = $this->findRequisite($doc, ['Email', 'Почта', 'ЭлектроннаяПочта']);

        DB::transaction(function () use (
            $doc, $externalId, $num1c, $orderedAt, $sum, $cur,
            $customerName, $customerPhone, $customerEmail, $file
        ) {
            /** @var Order $order */
            $order = Order::query()->firstOrNew(['external_id' => $externalId]);

            // заполняем orders (под твою таблицу)
            $order->number = $num1c ?: $order->number ?: $externalId;
            $order->ordered_at = $orderedAt ?: $order->ordered_at ?: now();
            $order->customer_name = $customerName;
            $order->customer_phone = $customerPhone;
            $order->customer_email = $customerEmail;
            $order->grand_total = $sum;
            $order->currency = $cur;

            // статус можно хранить как пришлёт 1С (если пришлёт)
            $status = $this->text($doc, './*[local-name()="Статус"]');
            $order->status = $status ?: ($order->status ?: 'new');

            // raw (важно, чтобы в orders была json колонка raw)
            $order->raw = [
                'source' => '1c',
                'file' => $file,
                'doc' => json_decode(json_encode($doc), true),
            ];

            $order->save();

            // Позиции заказа
            // Удаляем старые (чтобы повторный импорт не плодил дубли)
            OrderItem::query()->where('order_id', $order->id)->delete();

            $items = $doc->xpath('.//*[local-name()="Товары"]/*[local-name()="Товар"]') ?: [];

            foreach ($items as $it) {
                $productExternalId = $this->text($it, './*[local-name()="Ид"]');
                $title = $this->text($it, './*[local-name()="Наименование"]') ?? 'Item';
                $qty   = $this->float($this->text($it, './*[local-name()="Количество"]')) ?? 0;
                $unit  = $this->float($this->text($it, './*[local-name()="ЦенаЗаЕдиницу"]')) ?? 0;
                $total = $this->float($this->text($it, './*[local-name()="Сумма"]')) ?? ($qty * $unit);

                $productId = null;
                if ($productExternalId) {
                    $productId = Product::query()->where('external_id', $productExternalId)->value('id');
                }

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_external_id' => $productExternalId,
                    'title' => $title,
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $total,
                    'raw' => json_decode(json_encode($it), true),
                ]);
            }

            Log::channel('onec')->info('[1C] SALE_IMPORTED_ONE', [
                'external_id' => $externalId,
                'items' => count($items),
                'sum' => $sum,
                'currency' => $cur,
            ]);
        });
    }

    private function parseOrderedAt(?string $date1c, ?string $time): ?string
    {
        $date1c = trim((string)$date1c);
        $time = trim((string)$time);

        if ($date1c === '') return null;

        // date like 2026-01-20
        $dt = $date1c . (($time !== '') ? (' ' . $time) : ' 00:00:00');

        try {
            return Carbon::parse($dt)->toDateTimeString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function findRequisite(\SimpleXMLElement $doc, array $names): ?string
    {
        $rows = $doc->xpath('.//*[local-name()="ЗначениеРеквизита"]') ?: [];
        foreach ($rows as $r) {
            $n = $this->text($r, './*[local-name()="Наименование"]');
            $v = $this->text($r, './*[local-name()="Значение"]');
            if (!$n || !$v) continue;

            foreach ($names as $wanted) {
                if (mb_strtolower($n) === mb_strtolower($wanted)) {
                    return $v;
                }
            }
        }
        return null;
    }

    private function text(\SimpleXMLElement $xml, string $expr): ?string
    {
        $nodes = $xml->xpath($expr);
        if (!is_array($nodes) || !isset($nodes[0])) return null;
        $v = trim((string)$nodes[0]);
        return $v !== '' ? $v : null;
    }

    private function float(?string $v): ?float
    {
        if ($v === null) return null;
        $raw = str_replace(["\xC2\xA0", ' ', "\t", "\n", "\r"], '', $v);
        $raw = str_replace(',', '.', $raw);
        return (is_numeric($raw)) ? (float)$raw : null;
    }
}
