<?php
namespace Codewiser\Database\Eloquent;

/**
 * @method $this pivot(\Closure $closure) Constrain query with a pivot values.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModel>
 */
abstract class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Add a relationship count / exists condition to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Relations\Relation<*, *, *>|string  $relation
     * @param  string  $operator
     * @param  int  $count
     * @param  string  $boolean
     * @param  \Closure(static, \Illuminate\Database\Eloquent\Relations\Relation): static|null  $callback
     *
     * @return $this
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', ?\Closure $callback = null): static
    {
        if (is_string($relation)) {
            if (!str_contains($relation, '.')) {
                // Doesnt support nested relations
                $relation = $this->getRelationWithoutConstraints($relation);
            }
        }

        if ($callback && $relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {

            $callback = function ($builder) use ($callback, $relation) {

                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                    $pivot = $relation->getPivotClass();
                    $pivotBuilder = $pivot::query()->setQuery($builder->getQuery());

                    call_user_func($callback, (clone $relation)->setQuery($builder->getQuery()), $pivotBuilder);
                } else {
                    call_user_func($callback, $builder);
                }

                return $builder;
            };
        }

        return parent::has($relation, $operator, $count, $boolean, $callback);
    }
}
