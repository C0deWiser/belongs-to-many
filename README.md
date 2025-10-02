# Extended BelongsToMany

As we know, `BelongsToMany` relation connects two models with a pivot table 
between. In simple cases, the pivot table has just two columns — foreign 
keys. In more complex cases, the pivot table has additional columns, 
and we want to constrain relation with pivot values.

**The problem is**, when we use `whereHas` method on `BelongsToMany` relation, 
the callback receives `Builder` instance, not `Relation` instance. So we 
can't use `wherePivot*` methods...

Take a look on examples.

Here we deal with a `Relation` instance:

```php
$user->organizations()->wherePivot('role', 'accountant');
```

But here we deal with a `Builder` instance:

```php
User::query()
    ->where('users.role', 'superuser')
    ->whereHas('organizations', fn(Builder $builder) => $builder
        ->where('organization_user.role', 'accountant')
    );
```

Here we enforced to use qualified column names to escape ambiguity.

**The solution is** to receive `Relation` instance into callback.

We introduce a `HasPivot` trait to use it with custom builders.

With that trait, all `*has` builder's methods, such as `whereHas`,
`whereDoesntHave`, etc., will send to a callback not a `Builder` instance, but 
`BelongsToMany` object, so you allowed to use any `wherePivot*` methods to 
constrain an intermediate query.

You may apply the trait to a custom builder:

```php
use Codewiser\Database\Eloquent\Traits\HasPivot;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<User>
 */
class UserBuilder extends Builder
{
    use HasPivot;
}
```

If you don't plan to use custom builder, anyway you should apply extended 
builder to a model. This extended builder already carries the trait.

```php
use Codewiser\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Model;

#[UseEloquentBuilder(Builder::class)]
class User extends Model
{
    //
}
```

> Trait fires only on `BelongsToMany` relations. All other relation types, 
> such as `BelongsTo`, `HasMany`, etc., keeps their original behaviour.

Also, this package provides a new method for `BelongsToMany` class (extended 
with a macro). The `pivot` method provides access to a pivot builder for you 
to build intermediate query.

Take a look on examples after applying `HasPivot` trait.

Here we deal with a `Relation` instance:

```php
$user->organizations()->wherePivot('role', 'accountant');

$user->organizations()->pivot(
    fn(Builder $pivotBuilder) => $pivotBuilder->where(
        $pivotBuilder->qualifyColumn('role'), 
        'accountant'
    )
);
```

And here we deal with a `Relation` instance too:

```php
Organization::query()->whereHas('users',
    fn(BelongsToMany $builder) => $builder->wherePivot('role', 'accountant')
);

// If you use pivot model with a custom builder:
Organization::query()->whereHas('users',
    fn(BelongsToMany $builder) => $builder->pivot(
        fn(MyPivotBuilder $pivotBuilder) => $pivotBuilder->where(
            $pivotBuilder->qualifyColumn('role'), 
            'accountant'
        )
    )
);
```

## Implementation

You either MUST use `Codewiser\Database\Eloquent\Builder` builder for both 
models that consists in `BelongsToMany` relation. 

Or you MUST use custom builders with 
`\Codewiser\Database\Eloquent\Traits\HasPivot` trait applied.

This is applicable to `MorphToMany` relations too.
