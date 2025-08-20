<?php

namespace App\Http\Controllers\Api;

use App\Filters\RoleFilter;
use App\Models\Role;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;

class RoleController extends ApiController
{

    public function index(RoleFilter $filters)
    {
        $this->authorize('viewAny',Role::class);
        $query = Role::filter($filters);
        return new RoleCollection($query->paginate());
    }

    public function store(RoleStoreRequest $request)
    {
        $this->authorize('create',Role::class);

        $role = Role::create($request->validated());

        if($request->has('permissions'))
        {
            $role->syncPermissions($request->permissions);
            return new RoleResource($role->load('permissions'));
        }

        return new RoleResource($role);

    }

    public function show(Role $role)
    {
        $this->authorize('view',$role);
        return new RoleResource($role);
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        $this->authorize('update',$role);
        $role->update($request->validated());
        return new RoleResource($role);

    }

    public function destroy(Role $role)
    {
        $this->authorize('delete',$role);
        $role->syncPermission([]);
        $role->delete();
        return $this->ok('User Successfully deleted');
    }
}
