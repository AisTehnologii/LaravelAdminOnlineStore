<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentBlock extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = [
        'section',
        'key',
        'value',
        'locale',
    ];
}
