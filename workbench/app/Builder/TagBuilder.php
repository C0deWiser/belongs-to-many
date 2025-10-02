<?php

namespace Workbench\App\Builder;

use Codewiser\Database\Eloquent\Traits\HasPivot;
use Illuminate\Database\Eloquent\Builder;

class TagBuilder extends Builder
{
    use HasPivot;
}