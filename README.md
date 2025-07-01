# Extended BelongsToMany

## Problem

Let's say we have two Models with belongs-to-many relationship and reach 
pivot between them.

For example: Organization, User with `role` attribute and organization-user 
pivot with `role` attribute too.

How would we get all organization `managers` and application `admins`?

```php
use Illuminate\Contracts\Database\Eloquent\Builder;

Organization::query()->whereHas('users', 
    fn(Builder $builder) => $builder
        ->where('users.role', 'admin')
        ->where('organization_user.role', 'manager')
);
```

> It is unsafe to use unqualified column name as `role` attribute is ambiguous.

What is annoying?

1. We should explicitly define table name.
2. The closure argument is an Eloquent Builder, not a Relation 
   instance: so we can't call `wherePivot` method.
3. If we have Custom Builder for the Pivot table — we can't use it here. 

## Solution

Solution is to extend `BelongToMany`.

Extend model with `\Codewiser\Database\Eloquent\Concerns\HasRelationships` trait that 
provides extended `BelongsToMany` object.

If you want to make custom Builder — use extended 
`\Codewiser\Database\Eloquent\Builder` too. Extended Builder overrides `has*` 
methods family.

All this has sense only for belongs-to-many relationships.

User model:

```php
use Codewiser\Database\Eloquent\Concerns\HasRelationships;
use Codewiser\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property string $role User role in application.
 */
class User extends Model
{
    /** @use HasBuilder<UserBuilder::class> */
    use HasBuilder;
    use HasRelationships;
    
    protected static string $builder = UserBuilder::class;
    
    public function organizations(): BelongsToMany|OrganizationBuilder
    {
        return $this
            ->belongsToMany(Organization::class, Participation::class)
            ->withPivot('role');
    }
}
```

User builder:

```php
use Codewiser\Database\Eloquent\Builder;

/**
 * @extends Builder<User::class>
 */
class UserBuilder extends Builder 
{
   public function whereRole($role): static 
   {
       $role = is_array($role) ? $role : func_get_args();
       
       return $this->whereIn($this->qualifyColumn('role'), $role);
   }
}
```

Organization model:

```php
use Codewiser\Database\Eloquent\Concerns\HasRelationships;
use Codewiser\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Organization extends Model
{
    /** @use HasBuilder<OrganizationBuilder::class> */
    use HasBuilder;
    use HasRelationships;
    
    protected static string $builder = OrganizationBuilder::class;
    
    public function users(): BelongsToMany|UserBuilder
    {
        return $this
            ->belongsToMany(User::class, Participation::class)
            ->withPivot('role');
    }
}
```

Organization builder:

```php
use Codewiser\Database\Eloquent\Builder;

/**
 * @extends Builder<Organization::class>
 */
class OrganizationBuilder extends Builder 
{
   //
}
```

Pivot model:

```php
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $role User role in organization.
 */
class Participation extends Pivot
{
    /** @use HasBuilder<ParticipationBuilder::class> */
    use HasBuilder;
    
    protected $table = 'organization_user';
    
    protected static string $builder = ParticipationBuilder::class;
}
```

```php
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Participation::class>
 */
class ParticipationBuilder extends Builder 
{
   public function whereRole($role): static 
   {
       $role = is_array($role) ? $role : func_get_args();
       
       return $this->whereIn($this->qualifyColumn('role'), $role);
   }
}
```

Now, build a query:

```php
use Codewiser\Database\Eloquent\Relations\BelongsToMany;

Organization::query()->whereHas('users', 
    fn(BelongsToMany|UserBuilder $builder) => $builder
        ->whereRole('admin')
        ->wherePivot('role', 'manager')
);
```

Or:

```php
use Codewiser\Database\Eloquent\Relations\BelongsToMany;

Organization::query()->whereHas('users', 
    fn(BelongsToMany|UserBuilder $builder) => $builder
        ->whereRole('admin')
        ->pivot(fn(ParticipationBuilder $builder) => $builder
            ->whereRole('manager')
        )
);
```

Or such:

```php
$organization
    ->users()
    ->whereRole('admin')
    ->pivot(fn(ParticipationBuilder $builder) => $builder
        ->whereRole('manager')
    )
 );
```

What is surprising?

1. The closure argument is a BelongsToMany object: so we can call `wherePivot` 
   method if we want to.
2. Extended BelongsToMany provides `pivot` method that is applied to a pivot 
   table.
3. The pivot closure argument is real pivot builder.