<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'locale',
        'image_path',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];
}
