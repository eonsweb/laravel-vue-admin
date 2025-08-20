<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;


 // Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function(){

    Route::apiResource('users',UserController::class);
    Route::apiResource('roles',RoleController::class);
    Route::apiResource('permissions',PermissionController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});
