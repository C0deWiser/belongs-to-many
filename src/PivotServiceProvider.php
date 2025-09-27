<?php

namespace Codewiser\Database;

use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\ServiceProvider;

class PivotServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        BelongsToMany::macro('pivot', function (Closure $closure) {

            /** @var BelongsToMany $this */

            $using = $this->getPivotClass();

            // Instantiate pivot model
            $pivot = new $using;

            // Create pivot query builder
            $pivotBuilder = $using::query()->setQuery($this->getQuery());

            // If pivot is not customized...
            if ($pivot->getTable() !== $this->getTable()) {
                // ... set proper intermediate table name
                $pivotBuilder->getModel()->setTable($this->getTable());
            }

            call_user_func($closure, $pivotBuilder);

            return $this;
        });
    }
}