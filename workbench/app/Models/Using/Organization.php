<?php

namespace Workbench\App\Models\Using;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workbench\App\Builder\OrganizationBuilder;
use Workbench\App\Builder\UserBuilder;
use Workbench\App\Models\User;

#[UseEloquentBuilder(OrganizationBuilder::class)]
class Organization extends Model
{
    public function users(): BelongsToMany|UserBuilder
    {
        return $this->belongsToMany(User::class)
            ->using(Employee::class);
    }
}