<?php

namespace Codewiser\BelongsToMany;

/**
 * Extended BelongsToMany.
 *
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Relations\Relation<TRelatedModel, TDeclaringModel, \Illuminate\Database\Eloquent\Collection<int, TRelatedModel>>
 */
class BelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    /**
     * Get pivot query builder.
     */
    public function pivot(\Closure $closure): static
    {
        $using = $this->getPivotClass();

        $pivotBuilder = $using::query()->setQuery($this->getQuery());

        call_user_func($closure, $pivotBuilder);

        return $this;
    }
}
