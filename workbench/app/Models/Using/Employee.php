<?php

namespace Workbench\App\Models\Using;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Workbench\App\Builder\EmployeeBuilder;

#[UseEloquentBuilder(EmployeeBuilder::class)]
class Employee extends Pivot
{
    protected $table = 'organization_user';
}
