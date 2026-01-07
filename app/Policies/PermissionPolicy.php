<?php

namespace App\Policies;

use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('view permission')
            ? Response::allow()
            : Response::deny('You do not have permission to view permissions.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): Response
    {
        return $user->can('view permissions')
            ? Response::allow()
            : Response::deny('You do not have permission to view permission.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('create Permission')
            ? Response::allow()
            : Response::deny('You do not have permission to create Permission.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): Response
    {
        return $user->can('update permissions')
        ? Response::allow()
        : Response::deny('You do not have permission to update permissions.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): Response
    {
        return $user->can('delete permissions')
        ? Response::allow()
        : Response::deny('You do not have permission to delete permissions.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): Response
    {
         return $user->can('restore permissions')
        ? Response::allow()
        : Response::deny('You do not have permission to restore permissions.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
         return $user->can('force delete permission')
        ? Response::allow()
        : Response::deny('You do not have permission to delete permission.');
    }
    }
}
