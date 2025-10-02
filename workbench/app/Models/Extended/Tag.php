<?php

namespace Workbench\App\Models\Extended;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Workbench\App\Builder\TagBuilder;
use Workbench\App\Builder\UserBuilder;

#[UseEloquentBuilder(TagBuilder::class)]
class Tag extends Model
{
    public function users(): MorphToMany|UserBuilder
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
}