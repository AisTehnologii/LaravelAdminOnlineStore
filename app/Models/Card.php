<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['section_id','title','description','image_path','locale','position'];

    public function section()
    {
        return $this->belongsTo(ContentSection::class);
    }
}
