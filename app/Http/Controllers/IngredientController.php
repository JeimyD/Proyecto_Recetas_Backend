<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ingredients;
use Illuminate\Http\JsonResponse;

class IngredientController extends Controller
{
    public function store(Ingredients $request)
    {
        $data = $request->validated();
        $ingredient = Ingredients::create($data);
        return response()->json($ingredient, 201);
    }
}
