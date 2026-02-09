<?php

return [
    'page' => [
    'nav_label' => 'Comenzi',
    'label' => 'Comandă',
],
    'fields' => [
        'number' => 'Număr',
        'external_id' => 'External ID',
        'ordered_at' => 'Data',
        'status' => 'Status',
        'currency' => 'Valută',
        'grand_total' => 'Sumă',
        'customer_name' => 'Nume',
        'customer_phone' => 'Telefon',
        'customer_email' => 'Email',
    ],

    'item_fields' => [
        'product' => 'Produs',
        'quantity' => 'Cant.',
        'unit_amount' => 'Preț',
        'total_amount' => 'Sumă',
    ],

    'statuses' => [
        'new' => 'Nou',
        'paid' => 'Achitat',
        'done' => 'Finalizat',
        'cancelled' => 'Anulat',
    ],

    'view' => [
        'sections' => [
            'order' => 'Comandă',
            'customer' => 'Client',
            'items' => 'Produse',
            'raw' => 'RAW (date tehnice)',
        ],
        'fallback' => [
            'product' => 'Produs #:id',
        ],
    ],
     'actions' => [
  'view' => 'Vizualizare',
],
];
