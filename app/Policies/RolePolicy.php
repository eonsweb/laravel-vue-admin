<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('view roles')
            ? Response::allow()
            : Response::deny('You do not have permission to view roles.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): Response
    {
        return $user->can('view roles')
            ? Response::allow()
            : Response::deny('You do not have permission to view role.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('create roles')
            ? Response::allow()
            : Response::deny('You do not have permission to create roles.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): Response
    {
        return $user->can('update roles')
        ? Response::allow()
        : Response::deny('You do not have permission to update roles.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): Response
    {
        return $user->can('delete roles')
        ? Response::allow()
        : Response::deny('You do not have permission to delete roles.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): Response
    {
        return $user->can('restore roles')
        ? Response::allow()
        : Response::deny('You do not have permission to restore roles.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): Response
    {
        return $user->can('force delete roles')
        ? Response::allow()
        : Response::deny('You do not have permission to delete roles.');
    }
}
