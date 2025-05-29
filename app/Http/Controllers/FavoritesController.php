<?php

namespace App\Http\Controllers;

use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{

    public function checkFavorite($recipeId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'is_favorite' => false,
                    'status' => 401
                ], 401);
            }

            $isFavorite = $user->hasFavorite::$recipeId;

            return response()->json([
                'is_favorite' => $isFavorite,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'is_favorite' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                    'status' => 401
                ], 401);
            }

            $favoriteRecipes = $user->favoriteRecipes
                ->with(['ingredients', 'labels', 'categories', 'rating'])
                ->get();

            if ($favoriteRecipes->isEmpty()) {
                return response()->json([
                    'message' => 'No tienes recetas favoritas',
                    'data' => [],
                    'total' => 0,
                    'status' => 200
                ], 200);
            }

            $formattedRecipes = $favoriteRecipes->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'image' => $recipe->image,
                    'video' => $recipe->video,
                    'description' => $recipe->description,
                    'instructions' => $recipe->instructions,
                    'preparation_time' => $recipe->preparation_time,
                    'categories' => $recipe->categories->pluck('name')->toArray(),
                    'ingredients' => $recipe->ingredients,
                    'labels' => $recipe->labels->pluck('name')->toArray(),
                    'average_rating' => round($recipe->average_rating, 1),
                    'total_ratings' => $recipe->total_ratings,
                    'total_favorites' => $recipe->total_favorites,
                    'is_favorite' => true,
                    'created_at' => $recipe->created_at,
                    'updated_at' => $recipe->updated_at,
                    'favorited_at' => $recipe->pivot->created_at
                ];
            });

            return response()->json([
                'data' => $formattedRecipes,
                'total' => $favoriteRecipes->count(),
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener favoritos',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $recipeId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                    'status' => 401
                ], 401);
            }

            $recipe = Recipes::find($recipeId);
            if (!$recipe) {
                return response()->json([
                    'message' => 'Receta no encontrada',
                    'status' => 404
                ], 404);
            }

            if ($user->hasFavorite::$recipeId) {
                return response()->json([
                    'message' => 'La receta ya está en favoritos',
                    'is_favorite' => true,
                    'status' => 200
                ], 200);
            }

            $user->favoriteRecipes->attach($recipeId);

            return response()->json([
                'message' => 'Receta agregada a favoritos',
                'is_favorite' => true,
                'total_favorites' => $recipe->total_favorites,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al agregar a favoritos',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $recipeId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                    'status' => 401
                ], 401);
            }

            $recipe = Recipes::find($recipeId);
            if (!$recipe) {
                return response()->json([
                    'message' => 'Receta no encontrada',
                    'status' => 404
                ], 404);
            }

            if ($user->hasFavorite::$recipeId) {
                $user->favoriteRecipes->detach($recipeId);
                $message = 'Receta removida de favoritos';
                $isFavorite = false;
            } else {
                $user->favoriteRecipes->attach($recipeId);
                $message = 'Receta agregada a favoritos';
                $isFavorite = true;
            }

            return response()->json([
                'message' => $message,
                'is_favorite' => $isFavorite,
                'total_favorites' => $recipe->total_favorites,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cambiar estado de favorito',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $recipeId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no autenticado',
                    'status' => 401
                ], 401);
            }

            $recipe = Recipes::find($recipeId);
            if (!$recipe) {
                return response()->json([
                    'message' => 'Receta no encontrada',
                    'status' => 404
                ], 404);
            }

            if (!$user->hasFavorite::$recipeId) {
                return response()->json([
                    'message' => 'La receta no está en favoritos',
                    'is_favorite' => false,
                    'status' => 200
                ], 200);
            }

            $user->favoriteRecipes->detach($recipeId);

            return response()->json([
                'message' => 'Receta removida de favoritos',
                'is_favorite' => false,
                'total_favorites' => $recipe->total_favorites,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al remover de favoritos',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
