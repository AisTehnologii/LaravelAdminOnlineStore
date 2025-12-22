<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id',
        'locale',
        'position',
        'is_active',
        'announce_title',
        'announce_description',
        'announce_image_path',
        'title',
        'description',
        'description_extra',
        'price',
        'sale_price',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    public function section()
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function getDisplayPriceAttribute(): ?string
    {
        $p = $this->sale_price ?? $this->price;
        return $p === null ? null : number_format((float) $p, 2, '.', ' ');
    }

    public function getDisplayOldPriceAttribute(): ?string
    {
        if ($this->sale_price !== null && $this->price !== null && $this->price > $this->sale_price) {
            return number_format((float) $this->price, 2, '.', ' ');
        }
        return null;
    }
}
