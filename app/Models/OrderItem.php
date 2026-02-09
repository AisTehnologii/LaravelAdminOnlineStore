<?php

namespace App\Models;

    use App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_external_id',
        'product_id',
        'name',
        'quantity',
        'unit_amount',
        'total_amount',
        'raw',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'unit_amount'  => 'decimal:2',
        'total_amount' => 'decimal:2',
        'raw'          => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

}
