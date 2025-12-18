<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCard extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['section_id','locale','date','title','url','position'];

    protected $casts = ['date' => 'date'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }
}
