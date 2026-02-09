
<?php

return [
    'page' => [
    'nav_label' => 'Orders',
    'label' => 'Order',
],

    'fields' => [
        'number' => 'Number',
        'external_id' => 'External ID',
        'ordered_at' => 'Date',
        'status' => 'Status',
        'currency' => 'Currency',
        'grand_total' => 'Total',
        'customer_name' => 'Name',
        'customer_phone' => 'Phone',
        'customer_email' => 'Email',
    ],

    'item_fields' => [
        'product' => 'Product',
        'quantity' => 'Qty',
        'unit_amount' => 'Price',
        'total_amount' => 'Amount',
    ],

    'statuses' => [
        'new' => 'New',
        'paid' => 'Paid',
        'done' => 'Done',
        'cancelled' => 'Cancelled',
    ],

    'view' => [
        'sections' => [
            'order' => 'Order',
            'customer' => 'Customer',
            'items' => 'Items',
            'raw' => 'RAW (tech)',
        ],
        'fallback' => [
            'product' => 'Product #:id',
        ],
    ],
    'actions' => [
  'view' => 'View',
],
];
