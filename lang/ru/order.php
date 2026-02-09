<?php

return [
    'page' => [
        'nav_label' => 'Заказы',
        'label' => 'Заказ',
    ],
    'fields' => [
        'number' => 'Номер',
        'external_id' => 'External ID',
        'ordered_at' => 'Дата',
        'status' => 'Статус',
        'currency' => 'Валюта',
        'grand_total' => 'Сумма',
        'customer_name' => 'Имя',
        'customer_phone' => 'Телефон',
        'customer_email' => 'Email',
    ],

    'item_fields' => [
        'product' => 'Товар',
        'quantity' => 'Кол-во',
        'unit_amount' => 'Цена',
        'total_amount' => 'Сумма',
    ],

    'statuses' => [
        'new' => 'Новый',
        'paid' => 'Оплачен',
        'done' => 'Выполнен',
        'cancelled' => 'Отменён',
    ],

    'view' => [
        'sections' => [
            'order' => 'Заказ',
            'customer' => 'Клиент',
            'items' => 'Позиции',
            'raw' => 'RAW (тех. данные)',
        ],
        'fallback' => [
            'product' => 'Товар #:id',
        ],
    ],
     'actions' => [
  'view' => 'Просмотр',
],
];
