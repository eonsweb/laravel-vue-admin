<?php

namespace App\Providers;

use Spatie\Permission\PermissionRegistrar;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)->setRoleClass(Role::class);
        app(PermissionRegistrar::class)->setPermissionClass(Permission::class);

        Gate::policy(User::class,UserPolicy::class);
        Gate::policy(Role::class,RolePolicy::class);
        Gate::policy(Permission::class,PermissionPolicy::class);

        Gate::before(function(User $user, string $ability): ?true {
                return $user->hasRole('admin') ? true :null;
        });
    }
}
