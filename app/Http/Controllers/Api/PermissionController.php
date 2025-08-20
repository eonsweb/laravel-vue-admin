<?php

namespace App\Http\Controllers\Api;

use App\Filters\PermissionFilter;
use App\Models\Permission;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;


class PermissionController extends ApiController
{

    public function index(PermissionFilter $filters)
    {
        $this->authorize('viewAny',Permission::class);
        $query = Permission::filter($filters);
        return new PermissionCollection($query->paginate());
    }


    public function store(PermissionStoreRequest $request)
    {
        $this->authorize('create',Permission::class);
        $permission = Permission::create($request->validated());
        return new PermissionResource($permission);
    }

    public function show(Permission $permission)
    {
        $this->authorize('view',$permission);
        return new PermissionResource($permission);
    }


    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $this->authorize('update',$permission);
        $permission->update($request->validated());
        return new PermissionResource($permission);

    }

     public function destroy(Permission $permission)
    {
        $this->authorize('delete',$permission);
        $permission->delete();
        return $this->ok('User Successfully deleted');
    }
}
