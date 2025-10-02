<?php

namespace Workbench\App\Models\Extended;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Codewiser\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Workbench\App\Builder\TagBuilder;
use Workbench\App\Builder\UserBuilder;

#[UseEloquentBuilder(UserBuilder::class)]
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organizations(): BelongsToMany|Builder
    {
        return $this->belongsToMany(Organization::class);
    }

    public function tags(): MorphToMany|TagBuilder
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
