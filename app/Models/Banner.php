<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes,HasRevisions;

    protected $fillable = [
        'section_id',
        'position',
        'locale',
        'title',
        'text',
        'image_path',
    ];

    public function section()
    {
        return $this->belongsTo(ContentSection::class);
    }
}
