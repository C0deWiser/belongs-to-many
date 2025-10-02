<?php

namespace Workbench\App\Models\Using;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Workbench\App\Builder\OrganizationBuilder;
use Workbench\App\Builder\TagBuilder;
use Workbench\App\Builder\UserBuilder;

#[UseEloquentBuilder(OrganizationBuilder::class)]
class Organization extends Model
{
    public function users(): BelongsToMany|UserBuilder
    {
        return $this->belongsToMany(User::class)
            ->using(Employee::class);
    }

    public function tags(): MorphToMany|TagBuilder
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->using(Taggable::class);
    }
}