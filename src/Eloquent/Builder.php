<?php
namespace Codewiser\Database\Eloquent;

use Codewiser\Database\Eloquent\Traits\HasExtendedBelongsToMany;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    use HasExtendedBelongsToMany;
}
