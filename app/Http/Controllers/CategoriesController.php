<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
     public function index()
    {
        $search = request()->get('search');
        $query = Categories::query();

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $categories = $query->get();

        if ($categories->isEmpty()) {
            return response()->json([
                'message' => $search ? 'No se encontraron categorías que coincidan con: ' . $search : 'No hay categorías disponibles',
                'status' => 404,
                'search_term' => $search
            ], 404);
        }

        return response()->json([
            'data' => $categories,
            'total' => $categories->count(),
            'search_term' => $search,
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $categories = Categories::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon
        ]);

        if (!$categories) {
            return response()->json([
                'message' => 'Error al crear la categoría',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'data' => $categories,
            'status' => 201
        ], 201);
    }

    public function show($id)
    {
        $categories = Categories::with(['recipes' => function($query) {
            $query->select('id', 'title', 'description', 'categories_id');
        }])->find($id);

        if (!$categories) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'data' => $categories,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $categories = Categories::find($id);

        if (!$categories) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $categories->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon
        ]);

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'data' => $categories,
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $categories = Categories::find($id);

        if (!$categories) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        $recipesCount = $categories->recipes()->count();

        if ($recipesCount > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene ' . $recipesCount . ' receta(s) asociada(s)',
                'status' => 400
            ], 400);
        }

        $categories->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente',
            'status' => 200
        ], 200);
    }

    public function recipes($id)
    {
        $categories = Categories::with(['recipes.ingredients', 'recipes.labels'])->find($id);

        if (!$categories) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        if ($categories->recipes->isEmpty()) {
            return response()->json([
                'message' => 'No hay recetas en esta categoría',
                'categories' => $categories->name,
                'status' => 404
            ], 404);
        }

        return response()->json([
            'categories' => $categories->name,
            'data' => $categories->recipes,
            'total' => $categories->recipes->count(),
            'status' => 200
        ], 200);
    }
}
