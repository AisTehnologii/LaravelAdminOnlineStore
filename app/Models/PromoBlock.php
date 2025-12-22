<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoBlock extends Model
{
    protected $fillable = [
        'section_id',
        'locale',
        'position',
        'title',
        'subtitle',
        'description',
        'description_2',
        'description_3',
        'image_path',
        'link',
    ];

    public function section()
    {
        return $this->belongsTo(ContentSection::class);
    }
}
