<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['section_id','locale','text','author','role','position'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }
}
