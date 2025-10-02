<?php

namespace Workbench\App\Builder;

//use Codewiser\Database\Eloquent\Builder;

use Codewiser\Database\Eloquent\Traits\HasPivot;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    use HasPivot;
}