<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\RecipesController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#Users
Route::get('/user', function(Request $request){
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [UsersController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

#Recipes
Route::get('/recipes', [RecipesController::class, 'index']);
Route::get('/recipes/{id}', [RecipesController::class, 'show']);
Route::post('/recipes', [RecipesController::class, 'store']);
Route::put('/recipes/{id}', [RecipesController::class, 'update']);
Route::delete('/recipes/{id}', [RecipesController::class, 'destroy']);

#Labels
Route::apiResource('labels', LabelController::class);
