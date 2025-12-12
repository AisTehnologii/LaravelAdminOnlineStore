<?php

// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'description',
        'image_path',
        'position',
    ];
}

