<?php

namespace App\Services\OneC;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class OrderCommerceMlExportService
{
    // файл будет сохраняться в storage/app/1c/outgoing
    private string $dir = '1c/outgoing';

    // дефолты (как в твоём примере)
    private string $defaultOperation = 'Заказ товара';
    private string $defaultRole = 'Продавец';
    private string $defaultCurrency = 'MDL';
    private string $defaultRate = '1';

    private string $defaultContragentId = '19#adminAndrey#Гридасов Андрей';
    private string $defaultContragentFullName = 'Андрей Кудасов Тудасов Тест';
    private string $defaultContragentAddress = 'Test Adres 1';

    private string $defaultDelivery = 'Ridicare personală | Самовывоз';
    private string $defaultDeliveryId = '2';
    private string $defaultPayment = 'Plată în numerar | Наличный расчет';
    private string $defaultPaymentId = '5';
    private string $defaultSite = '[s6] shoponline2';

    // IMPORTANT: Тип лица — у тебя 1С это ждёт и ругается
    private string $defaultPersonType = '0';

    // ИдКаталога — оставляем как в примере, если из товара взять негде
    private string $defaultCatalogId = 'ru-dc7f2614-3ed2-11dd-abbc-001bfc10a554#';

    // Единица
    private string $defaultUnitCode = '796';
    private string $defaultUnitNameFull = 'Bucată';

    public function buildAndStore(Order $order): string
    {
        Storage::disk('local')->makeDirectory($this->dir);

        // Чтобы items точно были доступны (если не загружены)
        $order->loadMissing(['items.product']);

        $xml = $this->buildXml($order);

        $name = 'orders_' . $order->id . '_' . Carbon::now()->format('Ymd_His') . '.xml';
        Storage::disk('local')->put($this->dir . '/' . $name, $xml);

        return $name;
    }

    public function buildXml(Order $order): string
    {
        $orderedAt = $order->ordered_at ? Carbon::parse($order->ordered_at) : Carbon::now();

        $date = $orderedAt->format('Y-m-d');
        $time = $orderedAt->format('H:i:s');

        $dateForm = Carbon::now()->format('Y-m-d\TH:i:s');
        $dateUpdate = Carbon::now()->format('Y-m-d H:i:s');

        // 1С часто любит, чтобы Номер был "число/строка без пустоты"
        $number = (string)($order->number ?: $order->id);

        $sum = (float)($order->grand_total ?? 0);

        // Контрагент: берём из пользователя/заказа, иначе дефолт
        $fullName = (string)($order->customer_name ?? optional($order->user)->name ?? $this->defaultContragentFullName);
        $email    = (string)($order->customer_email ?? optional($order->user)->email ?? 'test@test.md');
        $phone    = (string)($order->customer_phone ?? '78005553535');
        $address  = (string)($order->customer_address ?? $this->defaultContragentAddress);

        // Форматирование как в примере (4 знака после запятой)
        $sum4 = number_format($sum, 4, '.', '');

        $out = [];
        $out[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $out[] = '<КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" ВерсияСхемы="2.08" ДатаФормирования="' . $dateForm . '">';
        $out[] = '';
        $out[] = '    <Документ>';
        $out[] = '        <Ид>' . $this->esc((string)$order->id) . '</Ид>';
        $out[] = '        <Номер>' . $this->esc($number) . '</Номер>';
        $out[] = '        <Дата>' . $date . '</Дата>';
        $out[] = '        <ХозОперация>' . $this->esc($this->defaultOperation) . '</ХозОперация>';
        $out[] = '        <Роль>' . $this->esc($this->defaultRole) . '</Роль>';
        $out[] = '        <Валюта>' . $this->esc($this->defaultCurrency) . '</Валюта>';
        $out[] = '        <Курс>' . $this->esc($this->defaultRate) . '</Курс>';
        $out[] = '        <Сумма>' . $sum4 . '</Сумма>';
        $out[] = '';
        $out[] = '        <НомерВерсии>0</НомерВерсии>';
        $out[] = '        <DateUpdate>' . $dateUpdate . '</DateUpdate>';
        $out[] = '';
        $out[] = '        <Контрагенты>';
        $out[] = '            <Контрагент>';
        $out[] = '                <Ид>' . $this->esc($this->defaultContragentId) . '</Ид>';
        $out[] = '                <Наименование></Наименование>';
        $out[] = '                <ПолноеНаименование>' . $this->esc($fullName) . '</ПолноеНаименование>';
        $out[] = '';
        $out[] = '                <АдресРегистрации>';
        $out[] = '                    <Представление>' . $this->esc($address) . '</Представление>';
        $out[] = '                </АдресРегистрации>';
        $out[] = '';
        $out[] = '                <Контакты>';
        $out[] = '                    <Контакт>';
        $out[] = '                        <Тип>Ф.И.О</Тип>';
        $out[] = '                        <Значение>' . $this->esc($fullName) . '</Значение>';
        $out[] = '                    </Контакт>';
        $out[] = '                    <Контакт>';
        $out[] = '                        <Тип>Электронный адрес</Тип>';
        $out[] = '                        <Значение>' . $this->esc($email) . '</Значение>';
        $out[] = '                    </Контакт>';
        $out[] = '                    <Контакт>';
        $out[] = '                        <Тип>Номер телефона</Тип>';
        $out[] = '                        <Значение>' . $this->esc($phone) . '</Значение>';
        $out[] = '                    </Контакт>';
        $out[] = '                </Контакты>';
        $out[] = '';
        $out[] = '                <Роль>Покупатель</Роль>';
        $out[] = '            </Контрагент>';
        $out[] = '        </Контрагенты>';
        $out[] = '';
        $out[] = '        <Время>' . $time . '</Время>';
        $out[] = '        <Комментарий></Комментарий>';
        $out[] = '';
        $out[] = '        <Товары>';

        $pos = 1;
        foreach ($order->items as $it) {
            $qty = (float)($it->quantity ?? 0);
            $price = (float)($it->unit_amount ?? 0);

            $qty4 = number_format($qty, 4, '.', '');
            $price4 = number_format($price, 4, '.', '');
            $lineSum = (float)($it->total_amount ?? ($qty * $price));
            $lineSumPlain = (string)((int)round($lineSum)); // как у тебя: 9000 без .0000

            $productExternalId =
                $it->product?->external_id
                ?? $it->product_external_id
                ?? 'TEST-PRODUCT';

            $productName =
                $it->product_title
                ?? $it->title
                ?? $it->product?->title
                ?? 'Товар';

            $out[] = '            <Товар>';
            $out[] = '                <Ид>' . $this->esc((string)$productExternalId) . '</Ид>';
            $out[] = '                <ИдКаталога>' . $this->esc($this->defaultCatalogId) . '</ИдКаталога>';
            $out[] = '                <Наименование>' . $this->esc((string)$productName) . '</Наименование>';
            $out[] = '';
            $out[] = '                <Единица>';
            $out[] = '                    <Код>' . $this->esc($this->defaultUnitCode) . '</Код>';
            $out[] = '                    <НаименованиеПолное>' . $this->esc($this->defaultUnitNameFull) . '</НаименованиеПолное>';
            $out[] = '                </Единица>';
            $out[] = '';
            $out[] = '                <Коэффициент>1</Коэффициент>';
            $out[] = '';
            $out[] = '                <ГруппаМаркировки>';
            $out[] = '                    <Код></Код>';
            $out[] = '                </ГруппаМаркировки>';
            $out[] = '';
            $out[] = '                <ЦенаЗаЕдиницу>' . $price4 . '</ЦенаЗаЕдиницу>';
            $out[] = '                <Количество>' . $qty4 . '</Количество>';
            $out[] = '                <Сумма>' . $lineSumPlain . '</Сумма>';
            $out[] = '';
            $out[] = '                <ЗначенияРеквизитов>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>ВидНоменклатуры</Наименование><Значение>Товар</Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>ТипНоменклатуры</Наименование><Значение>Товар</Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>НомерПозицииКорзины</Наименование><Значение>' . $pos . '</Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>СвойствоКорзины#NAIMENOVANIE_RU</Наименование><Значение>' . $this->esc((string)$productName) . '</Значение></ЗначениеРеквизита>';
            // если у тебя нет RAZMER/TSVET — оставляем дефолт пусто
            $out[] = '                    <ЗначениеРеквизита><Наименование>СвойствоКорзины#RAZMER</Наименование><Значение></Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>СвойствоКорзины#TSVET</Наименование><Значение></Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>СвойствоКорзины#CATALOG.XML_ID</Наименование><Значение>' . $this->esc($this->defaultCatalogId) . '</Значение></ЗначениеРеквизита>';
            $out[] = '                    <ЗначениеРеквизита><Наименование>СвойствоКорзины#PRODUCT.XML_ID</Наименование><Значение>' . $this->esc((string)$productExternalId) . '</Значение></ЗначениеРеквизита>';
            $out[] = '                </ЗначенияРеквизитов>';
            $out[] = '            </Товар>';

            $pos++;
        }

        $out[] = '        </Товары>';
        $out[] = '';
        $out[] = '        <ЗначенияРеквизитов>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Способ доставки</Наименование><Значение>' . $this->esc($this->defaultDelivery) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Метод доставки ИД</Наименование><Значение>' . $this->esc($this->defaultDeliveryId) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Метод оплаты</Наименование><Значение>' . $this->esc($this->defaultPayment) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Метод оплаты ИД</Наименование><Значение>' . $this->esc($this->defaultPaymentId) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Заказ оплачен</Наименование><Значение>0</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Доставка разрешена</Наименование><Значение>0</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Отменен</Наименование><Значение>0</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Финальный статус</Наименование><Значение>0</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Статус заказа</Наименование><Значение>[N] Новый</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Статуса заказа ИД</Наименование><Значение>N</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Дата изменения статуса</Наименование><Значение>' . $orderedAt->format('d.m.Y H:i:s') . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Сайт</Наименование><Значение>' . $this->esc($this->defaultSite) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Тип лица</Наименование><Значение>' . $this->esc($this->defaultPersonType) . '</Значение></ЗначениеРеквизита>';
        $out[] = '            <ЗначениеРеквизита><Наименование>Адрес доставки</Наименование><Значение>' . $this->esc($address) . '</Значение></ЗначениеРеквизита>';
        $out[] = '        </ЗначенияРеквизитов>';
        $out[] = '';
        $out[] = '    </Документ>';
        $out[] = '</КоммерческаяИнформация>';

        return implode("\n", $out);
    }

    private function esc(string $v): string
    {
        return htmlspecialchars($v, ENT_XML1);
    }
}
