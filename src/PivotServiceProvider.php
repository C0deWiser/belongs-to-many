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

            // Get pivot class
            $using = $this->getPivotClass();

            // Instantiate pivot query builder, replacing base query
            $pivotBuilder = $using::query()->setQuery($this->getQuery());

            // If pivot is not customized...
            if ($using === Pivot::class) {
                // ... set proper intermediate table name
                $pivotBuilder->getModel()->setTable($this->getTable());
            }

            call_user_func($closure, $pivotBuilder);

            return $this;
        });
    }
}