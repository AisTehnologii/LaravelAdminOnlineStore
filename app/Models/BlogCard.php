<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCard extends Model
{
    protected $fillable = [
        'locale',
        'date',
        'title',
        'url',
        'position',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
