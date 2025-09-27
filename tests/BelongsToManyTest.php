<?php

namespace Tests;

use Codewiser\Database\PivotServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Orchestra\Testbench\TestCase;
use Workbench\App\Builder\UserBuilder;
use Workbench\App\Models\Extended\Organization as OrganizationExt;
use Workbench\App\Models\Extended\User as UserExt;
use Workbench\App\Models\Organization;
use Workbench\App\Models\User;
use Workbench\App\Models\Using\Organization as OrganizationInt;
use Workbench\App\Models\Using\User as UserInt;

class BelongsToManyTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PivotServiceProvider::class,
        ];
    }

    public function testRelation()
    {
        $user = new User();
        $user->id = 1;

        $sql1 = $user->organizations()->wherePivot('role', 'accountant');
        dump($sql1->toSql());

        $user = new UserExt();
        $user->id = 1;

        $sql2 = $user->organizations()->pivot(
            fn(Builder $builder) => $builder->where($builder->qualifyColumn('role'), 'accountant')
        );
        dump($sql2->toSql());

        $user = new UserInt();
        $user->id = 1;

        $sql3 = $user->organizations()->pivot(
            fn(Builder $builder) => $builder->where('role', 'accountant')
        );
        dump($sql3->toSql());

        $this->assertEquals($sql1->toSql(), $sql2->toSql());
        $this->assertEquals($sql1->toSql(), $sql3->toSql());
    }

    public function testHas()
    {
        $sql1 = Organization::query()->whereHas('users',
            fn(Builder $builder) => $builder->where('organization_user.role', 'accountant')
        );
        dump($sql1->toSql());

        $sql2 = OrganizationExt::query()->whereHas('users',
            fn(BelongsToMany|UserBuilder $builder) => $builder->pivot(
                fn(Builder $builder) => $builder->where($builder->qualifyColumn('role'), 'accountant')
            )
        );
        dump($sql2->toSql());

        $sql3 = OrganizationExt::query()->whereHas('users',
            fn(BelongsToMany $builder) => $builder->wherePivot('role', 'accountant')
        );
        dump($sql3->toSql());

        $sql4 = OrganizationInt::query()->whereHas('users',
            fn(BelongsToMany|UserBuilder $builder) => $builder->pivot(
                fn(Builder $builder) => $builder->where('role', 'accountant')
            )
        );
        dump($sql4->toSql());

        $sql5 = OrganizationInt::query()->whereHas('users',
            fn(BelongsToMany $builder) => $builder->wherePivot('role', 'accountant')
        );
        dump($sql5->toSql());

        $this->assertEquals($sql1->toSql(), $sql2->toSql());
        $this->assertEquals($sql1->toSql(), $sql3->toSql());
        $this->assertEquals($sql1->toSql(), $sql4->toSql());
        $this->assertEquals($sql1->toSql(), $sql5->toSql());
    }
}