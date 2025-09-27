# Extended BelongsToMany

As we know, `BelongsToMany` relation connects two models with a pivot table 
between. In simple cases, the pivot table has just two columns — foreign 
keys. In more complex cases, the pivot table has additional columns, 
and we want to constrain relation with pivot values.

We invite use to use custom builders, extended from 
`\Codewiser\Database\Eloquent\Builder` (that extends base eloquent 
builder).

With that builder all `*has` methods, such as `whereHas`,
`whereDoesntHave`, etc., will send to a callback not a builder instance, but 
`BelongsToMany` object, so you, at least, can use `wherePivot` method to 
constrain a query.

also, the package provides new method for `BelongsToMany` builder. The `pivot` 
method provides access to a pivot builder for you to build query.

_before_
```php
$user->organizations()->wherePivot('role', 'accountant');
```

_after_
```php
$user->organizations()->pivot(
    fn(EmployeeBuilder $builder) => $builder->whereRole('accountant')
);
```

Of course, this have sense only if you use custom builder for a pivot model. 
But what about next example? Here you have to qualify column name hardcoding?

_before_
```php
Organization::query()->whereHas('users',
    fn(Builder $builder) => $builder
        // HARDCODED
        ->where('organization_user.role', 'accountant')
)
```

_after_
```php
Organization::query()->whereHas('users',
    fn(BelongsToMany $builder) => $builder->wherePivot('role', 'accountant')
);
```

_if pivot has custom query builder_
```php
Organization::query()->whereHas('users',
    fn(BelongsToMany $builder) => $builder->pivot(
        fn(EmployeeBuilder $builder) => $builder->whereRole('accountant')
    )
);
```

## Implementation

You MUST use a custom builder for both models that consists in 
`BelongsToMany` relation. These custom builders MUST extend 
`\Codewiser\Database\Eloquent\Builder`.

```php
namespace App\Builders;

use Codewiser\Database\Eloquent\Builder as ExtendedBuilder;

class OrganizationBuilder extends ExtendedBuilder
{
    //
}
```

```php
namespace App\Models;

use App\Builders\OrganizationBuilder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Model;

#[UseEloquentBuilder(OrganizationBuilder::class)]
class Organization extends Model
{

}
```
