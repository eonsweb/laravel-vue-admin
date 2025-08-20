<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $user->can('view users')
            ? Response::allow()
            : Response::deny('You do not have permission to view users.');
    }

    public function view(User $user, User $model): Response
    {
        // dd([
        //     'auth_user_id' => $user->id,
        //     'model_id' => $model->id,
        //     'equal' => $user->id === $model->id,
        //     'can_view_users' => $user->can('view users')
        // ]);
        return ($user->id === $model->id || $user->can('view users'))
            ? Response::allow()
            : Response::deny('You do not have permission to view this user.');
    }


    public function create(User $user): Response
    {
        return $user->can('create users')
            ? Response::allow()
            : Response::deny('You do not have permission to create users.');
    }

    public function update(User $user, User $model): Response
    {
        return ($user->can('update users') || $user->id === $model->id)
            ? Response::allow()
            : Response::deny('You do not have permission to edit this user.');
    }

    public function delete(User $user, User $model): Response
    {
        return ($user->can('delete users') && $user->id !== $model->id)
            ? Response::allow()
            : Response::deny('You do not have permission to delete this user.');
    }
}
