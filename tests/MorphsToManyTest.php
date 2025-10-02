<?php

namespace Tests;

use Codewiser\Database\Eloquent\Concerns\HasPivot;
use Codewiser\Database\PivotServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Orchestra\Testbench\TestCase;
use Workbench\App\Models\Extended\Organization as OrganizationExt;
use Workbench\App\Models\Extended\User as UserExt;
use Workbench\App\Models\Organization;
use Workbench\App\Models\Tag;
use Workbench\App\Models\User;
use Workbench\App\Models\Using\Organization as OrganizationInt;
use Workbench\App\Models\Using\User as UserInt;

class MorphsToManyTest extends TestCase
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

        $sql1 = $user->tags()->wherePivot('type', 'simple');
        dump($sql1->toSql());

        $user = new UserExt();
        $user->id = 1;

        $sql2 = $user->tags()->pivot(
            fn(Builder $builder) => $builder->where($builder->qualifyColumn('type'), 'simple')
        );
        dump($sql2->toSql());

        $user = new UserInt();
        $user->id = 1;

        $sql3 = $user->tags()->pivot(
            fn(Builder $builder) => $builder->where($builder->qualifyColumn('type'), 'simple')
        );
        dump($sql3->toSql());

        $this->assertEquals($sql1->toSql(), $sql2->toSql());
        $this->assertEquals($sql1->toSql(), $sql3->toSql());
    }

    public function testHas()
    {
        $sql1 = Organization::query()->whereHas('tags',
            fn(Builder $builder) => $builder->where('taggables.type', 'simple')
        );
        dump($sql1->toSql());

        $sql2 = OrganizationExt::query()->whereHas('tags',
            fn(MorphToMany|HasPivot $builder) => $builder->pivot(
                fn(Builder $builder) => $builder->where($builder->qualifyColumn('type'), 'simple')
            )
        );
        dump($sql2->toSql());

        $sql3 = OrganizationExt::query()->whereHas('tags',
            fn(MorphToMany $builder) => $builder->wherePivot('type', 'simple')
        );
        dump($sql3->toSql());

        $sql4 = OrganizationInt::query()->whereHas('tags',
            fn(MorphToMany|HasPivot $builder) => $builder->pivot(
                fn(Builder $builder) => $builder->where($builder->qualifyColumn('type'), 'simple')
            )
        );
        dump($sql4->toSql());

        $sql5 = OrganizationInt::query()->whereHas('tags',
            fn(MorphToMany $builder) => $builder->wherePivot('type', 'simple')
        );
        dump($sql5->toSql());

        $this->assertEquals($sql1->toSql(), $sql2->toSql());
        $this->assertEquals($sql1->toSql(), $sql3->toSql());
        $this->assertEquals($sql1->toSql(), $sql4->toSql());
        $this->assertEquals($sql1->toSql(), $sql5->toSql());
    }
}