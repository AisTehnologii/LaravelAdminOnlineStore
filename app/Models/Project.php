<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['section_id','locale','title','description','image_path','position'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }
}
