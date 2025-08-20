<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if(Auth::attempt($request->only("email","password")))
        {
            $user = User::firstWhere('email',$request->email);
            $token = $user->createToken('API token for '.$user->username)->plainTextToken;

            return $this->success(["user"=>$user,"token"=>$token],'Login successful');
        }

        return $this->error('Invalid email or password', 401);

    }


     public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return $this->ok('',"User Logged out");

    }


}
