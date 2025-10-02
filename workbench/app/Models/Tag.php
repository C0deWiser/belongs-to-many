<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
}