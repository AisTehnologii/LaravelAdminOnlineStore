<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\HasRevisions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentSection extends Model
{
    use SoftDeletes,HasRevisions;
    protected $fillable = ['type', 'title', 'slug', 'position', 'is_active'];

    public function banners(): HasMany   { return $this->hasMany(Banner::class); }
    public function sliders(): HasMany   { return $this->hasMany(Slider::class); }
    public function cards(): HasMany     { return $this->hasMany(Card::class); }
    public function projects(): HasMany  { return $this->hasMany(Project::class); }
    public function quotes(): HasMany    { return $this->hasMany(Quote::class); }
    public function blogCards(): HasMany { return $this->hasMany(BlogCard::class); }
}
