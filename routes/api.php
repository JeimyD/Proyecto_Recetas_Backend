<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
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
Route::post('/recipes/{id}/rating', [RecipesController::class, 'addRating']);

#Labels
Route::apiResource('labels', LabelController::class);

#Categories
Route::get('/categories', [CategoriesController::class, 'index']);
Route::post('/categories', [CategoriesController::class, 'store'])->middleware('auth:sanctum');
Route::get('/categories/{id}', [CategoriesController::class, 'show']);
Route::put('/categories/{id}', [CategoriesController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/categories/{id}', [CategoriesController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/categories/{id}/recipes', [CategoriesController::class, 'recipes']);
