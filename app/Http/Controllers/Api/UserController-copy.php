<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Filters\UserFilter;
use App\Models\User;
use App\Traits\ApiResponses;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;


class UserController extends ApiController
{
    use ApiResponses;

    public function index(UserFilter $filters)
    {
        try {
            $this->authorize('viewAny',User::class);

            $query = User::filter($filters);

            return new UserCollection($query->paginate());

        } catch (ModelNotFoundException $e) {

            return $this->error("Customers cannot be found.",404);

        }catch(AuthorizationException $e){

            return $this->error('You are not authorized to view this resource',403);
        }
    }


    public function store(UserStoreRequest $request)
    {
        try {
            $this->authorize('create',User::class);

            $user = User::create($request->validated());
            $user->assignRole('user');

            return new UserResource($user);

        } catch (ModelNotFoundException $e) {
            return $this->error("User cannot be found.");
        }catch(AuthorizationException $e){
            return $this->error('You are not authorized to update this resource',403);
        }
    }


    public function show(User $user)
    {

        try{
            $this->authorize('view',$user);

            return new UserResource($user);

        }catch (ModelNotFoundException $e) {
            return $this->error("User cannot be found.");
        }catch(AuthorizationException $e){
            return $this->error('You are not authorized to update this resource',403);
        }
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try{
            $this->authorize('update',$user);

            $user->update($request->validated());


            return new UserResource($user);
        }catch (ModelNotFoundException $e) {
            return $this->error("User cannot be found.");
        }catch(AuthorizationException $e){
            return $this->error('You are not authorized to update this resource',403);
        }
    }

    public function destroy(User $user)
    {
        try{
            $this->authorize('delete',$user);
            $user->delete();
            return $this->ok('User Successfully deleted');
        }catch (ModelNotFoundException $e) {
            return $this->error("User cannot be found.");
        }catch(AuthorizationException $e){
            return $this->error('You are not authorized to update this resource',403);
        }
    }
}
