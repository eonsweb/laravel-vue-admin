<?php

namespace App\Http\Controllers\Api;

use App\Filters\UserFilter;
use App\Models\User;
use App\Traits\ApiResponses;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;


class UserController extends ApiController
{
    use ApiResponses;

    public function index(UserFilter $filters)
    {
        $this->authorize('viewAny',User::class);
        $query = User::filter($filters);
        return new UserCollection($query->with(['roles','permissions'])->paginate());
    }


    public function store(UserStoreRequest $request)
    {
        $this->authorize('create',User::class);

        $user = User::create($request->validated());
        if($request->has('role'))
        {
            $user->syncRoles($request->role);
            // return new UserResource($user->load(['roles','permissions']));
        }

        if ($request->has('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

         return new UserResource($user->load(['roles', 'permissions']));
    }


    public function show(User $user)
    {
        $this->authorize('view',$user);
        $user->load(['roles', 'permissions']);
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update',$user);

        $user->update($request->validated());

        if($request->has('role'))
        {
            $user->syncRoles($request->role);
                $user->syncPermissions([]);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

       return new UserResource($user->load(['roles', 'permissions']));

    }

    public function destroy(User $user)
    {
        $this->authorize('delete',$user);

        $user->syncRoles([]);
        $user->syncPermissions([]);

        $user->delete();
        return $this->ok('User Successfully deleted');
    }
}
