<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe;
use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RecipesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recipes::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Recipe $request)
    {
        $data = $request->validated();

        $data['users_id'] = auth()->id;

        $recipe = Recipe::create($data);

        foreach ($data['ingredients'] as $ingredient) {
        $recipe->ingredients()->attach(
            $ingredient['id'],
            [
                'quantity_2' => $ingredient['quantity_2'] ?? null,
                'quantity_4' => $ingredient['quantity_4'] ?? null,
                'quantity_8' => $ingredient['quantity_8'] ?? null,
            ]
        );

        return new Recipe($recipe->load(['ingredients', 'ratings.user']));
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipes $recipe)
    {
        return $recipe;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
