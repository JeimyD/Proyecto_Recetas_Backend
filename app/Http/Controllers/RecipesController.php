<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe;
use App\Models\Categories;
use App\Models\Labels;
use App\Models\Recipe_Label;
use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

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
        $search = request()->get('search');
        $query = Recipes::with(['ingredients', 'labels', 'categories']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tittle', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('labels', function ($labelQuery) use ($search) {
                        $labelQuery->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('categories', function ($categoriesQuery) use ($search) {
                        $categoriesQuery->where('name', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        $recipes = $query->get();

        if ($recipes->isEmpty()) {
            $data = [
                'message' => $search ? 'No se encontraron recetas que coincidan con: ' . $search : 'No se encontraron recetas',
                'status' => 404,
                'search_term' => $search
            ];
            return response()->json($data, 404);
        }

        $formattedRecipes = $recipes->map(function ($recipe) {
            return [
                'id' => $recipe->id,
                'tittle' => $recipe->tittle,
                'image' => $recipe->image,
                'video' => $recipe->video,
                'description' => $recipe->description,
                'instructions' => $recipe->instructions,
                'preparation_time' => $recipe->preparation_time,
                'categories' => $recipe->categories->pluck('name')->toArray(),
                'ingredients' => $recipe->ingredients,
                'labels' => $recipe->labels->pluck('name')->toArray(),
                'created_at' => $recipe->created_at,
                'updated_at' => $recipe->updated_at
            ];
        });

        return response()->json([
            'data' => $formattedRecipes,
            'total' => $recipes->count(),
            'search_term' => $search,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        #Validating data
        $validator = Validator::make($request->all(), [
            'tittle' => 'required|string|max:255',
            'image' => 'nullable|string',
            'video' => 'nullable|string',
            'description' => 'required|string',
            'instructions' => 'required|string',
            'preparation_time' => 'required|integer',
            'quantity_2' => 'nullable',
            'quantity_4' => 'nullable',
            'quantity_8' => 'nullable',
            'labels' => 'nullable|array',
            'labels.*' => 'string',
            'categories' => 'nullable|array',
            'categories.*' => 'string',
        ]);

        #Taking user
        $user = $request->user();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        #Create Recipe
        $recipes = Recipes::create([
            'tittle' => $request->tittle,
            'image' => $request->image,
            'video' => $request->video,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'preparation_time' => $request->preparation_time,
            'users_id' => $user->id,
        ]);

        #Manage Categories
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryIds = [];

            foreach ($request->categories as $categoryName) {
                $category = Categories::where('name', $categoryName)->first();

                if (!$category) {
                    $category = Categories::create(['name' => $categoryName]);
                }

                $categoryIds[] = $category->id;
            }

            $recipes->categories()->attach($categoryIds);
        }

        #Create Ingredients
        $recipes->ingredients()->create([
            'quantity_2' => $request->quantity_2,
            'quantity_4' => $request->quantity_4,
            'quantity_8' => $request->quantity_8,
        ]);

        #Search or create label
        if ($request->has('labels') && is_array($request->labels)) {
            foreach ($request->labels as $labelName) {
                $label = Labels::where('name', $labelName)->first();
                if ($label) {
                    Recipe_Label::create([
                        'recipes_id' => $recipes->id,
                        'labels_id' => $label->id
                    ]);
                } else {
                    $newLabel = Labels::create(['name' => $labelName]);
                    Recipe_Label::create([
                        'recipes_id' => $recipes->id,
                        'labels_id' => $newLabel->id
                    ]);
                }
            }
        }

        #Responses
        if (!$recipes) {
            return response()->json([
                'message' => 'Error al registrar receta',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => $recipes,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recipe = Recipes::with(['ingredients', 'labels', 'categories'])->find($id);

        if (!$recipe) {
            $data = [
                'message' => 'Receta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $formattedRecipe = [
            'id' => $recipe->id,
            'tittle' => $recipe->tittle,
            'image' => $recipe->image,
            'video' => $recipe->video,
            'description' => $recipe->description,
            'instructions' => $recipe->instructions,
            'preparation_time' => $recipe->preparation_time,
            'categories' => $recipe->categories->pluck('name')->toArray(),
            'ingredients' => $recipe->ingredients,
            'labels' => $recipe->labels->pluck('name')->toArray(),
            'created_at' => $recipe->created_at,
            'updated_at' => $recipe->updated_at
        ];

        return response()->json([
            'data' => $formattedRecipe,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $recipe = Recipes::find($id);

        if (!$recipe) {
            return response()->json([
                'message' => 'Receta no encontrada',
                'status' => 404
            ], 404);
        }

        $user = $request->user();
        if ($recipe->users_id !== $user->id) {
            return response()->json([
                'message' => 'No tienes permisos para editar esta receta',
                'status' => 403
            ], 403);
        }

        $rules = [];

        if ($request->has('tittle')) {
            $rules['tittle'] = 'required|string|max:255';
        }
        if ($request->has('image')) {
            $rules['image'] = 'nullable|string';
        }
        if ($request->has('video')) {
            $rules['video'] = 'nullable|string';
        }
        if ($request->has('description')) {
            $rules['description'] = 'required|string';
        }
        if ($request->has('instructions')) {
            $rules['instructions'] = 'required|string';
        }
        if ($request->has('preparation_time')) {
            $rules['preparation_time'] = 'required|integer';
        }
        if ($request->has('categories')) {
            $rules['categories'] = 'nullable|array';
            $rules['categories.*'] = 'string';
        }
        if ($request->has('labels')) {
            $rules['labels'] = 'nullable|array';
            $rules['labels.*'] = 'string';
        }
        if ($request->has('quantity_2')) {
            $rules['quantity_2'] = 'nullable';
        }
        if ($request->has('quantity_4')) {
            $rules['quantity_4'] = 'nullable';
        }
        if ($request->has('quantity_8')) {
            $rules['quantity_8'] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $updateData = [];

        if ($request->has('tittle')) {
            $updateData['tittle'] = $request->tittle;
        }
        if ($request->has('image')) {
            $updateData['image'] = $request->image;
        }
        if ($request->has('video')) {
            $updateData['video'] = $request->video;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        if ($request->has('instructions')) {
            $updateData['instructions'] = $request->instructions;
        }
        if ($request->has('preparation_time')) {
            $updateData['preparation_time'] = $request->preparation_time;
        }

        $recipe->update($updateData);

        if ($request->has('categories')) {
            if (!empty($request->categories)) {
                $categoryIds = [];

                foreach ($request->categories as $categoryName) {
                    $category = Categories::where('name', $categoryName)->first();
                    if (!$category) {
                        $category = Categories::create(['name' => $categoryName]);
                    }
                    $categoryIds[] = $category->id;
                }
                $recipe->categories()->sync($categoryIds);
            } else {
                $recipe->categories()->detach();
            }
        }

        if ($request->hasAny(['quantity_2', 'quantity_4', 'quantity_8'])) {
            $ingredientData = [];

            if ($request->has('quantity_2')) {
                $ingredientData['quantity_2'] = $request->quantity_2;
            }
            if ($request->has('quantity_4')) {
                $ingredientData['quantity_4'] = $request->quantity_4;
            }
            if ($request->has('quantity_8')) {
                $ingredientData['quantity_8'] = $request->quantity_8;
            }

            $recipe->ingredients()->updateOrCreate(
                ['recipes_id' => $recipe->id],
                $ingredientData
            );
        }

        if ($request->has('labels')) {
            if (!empty($request->labels)) {
                Recipe_Label::where('recipes_id', $recipe->id)->delete();
                foreach ($request->labels as $labelName) {
                    $label = Labels::where('name', $labelName)->first();

                    if (!$label) {
                        $label = Labels::create(['name' => $labelName]);
                    }

                    Recipe_Label::create([
                        'recipes_id' => $recipe->id,
                        'labels_id' => $label->id
                    ]);
                }
            } else {
                Recipe_Label::where('recipes_id', $recipe->id)->delete();
            }
        }

        $recipe->load('categories', 'labels', 'ingredients');

        return response()->json([
            'message' => 'Receta actualizada exitosamente',
            'data' => $recipe,
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
