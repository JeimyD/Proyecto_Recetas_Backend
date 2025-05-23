<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#Users
Route::get('/users', [UsersController::class, 'index']);
// Route::post('/register', [UsersController::class, 'register']);
// Route::post('/login', [UsersController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('recipes', RecipesController::class);
