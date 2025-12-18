<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['section_id','locale','image_path','position'];

    public function section()
    {
        return $this->belongsTo(ContentSection::class);
    }
}
