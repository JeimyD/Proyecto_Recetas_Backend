<?php

namespace App\Http\Controllers;

use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'tittle' => ['required', 'max:500'],
            'image' => ['required', 'max:200'],
            'video' => ['required', 'max:300'],
            'description' => ['required'],
            'instructions' => ['required'],
            'preparation_time' => ['required'],
        ]);

        $recipe = $request->user()->recipes()->create($data);

        return $recipe;
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
