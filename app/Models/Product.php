<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'onec_raw' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function section()
    {
        return $this->belongsTo(\App\Models\ContentSection::class, 'section_id');
    }

    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class, 'product_id')->orderBy('position');
    }
}
