<?php

namespace Codewiser\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;

/**
 * Extend pivot builder from this. It qualifies pivot columns names.
 */
abstract class PivotBuilder extends Builder
{
    public function whereIn($column, $values, $boolean = 'and', $not = false): static
    {
        return parent::whereIn($this->qualifyColumn($column), $values, $boolean, $not);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and'): static
    {
        return parent::where($this->qualifyColumn($column), $operator, $value, $boolean);
    }
}