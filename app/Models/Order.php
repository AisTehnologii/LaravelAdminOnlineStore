<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Order extends Model
{
  protected $fillable = [
    'user_id',
    'external_id','number','ordered_at','status','comment',
    'grand_total','currency','customer_name','customer_phone','customer_email','raw'
];

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}


    protected $casts = [
        'ordered_at'  => 'datetime',
        'raw'         => 'array',
        'grand_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    
}

