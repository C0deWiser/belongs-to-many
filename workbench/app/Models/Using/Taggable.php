<?php

namespace Workbench\App\Models\Using;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Workbench\App\Builder\TaggableBuilder;

#[UseEloquentBuilder(TaggableBuilder::class)]
class Taggable extends MorphPivot
{
    protected $table = 'taggables';
}