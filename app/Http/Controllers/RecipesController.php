<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe;
use App\Models\Recipes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

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
        $query = Recipes::with(['ingredients']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('tittle', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
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

        return response()->json([
            'data' => $recipes,
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
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $recipes = Recipes::create([
            'tittle' => $request->tittle,
            'image' => $request->image,
            'video' => $request->video,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'preparation_time' => $request->preparation_time,
            'users_id' => $user->id,
        ]);

        $recipes->ingredients()->create([
            'quantity_2' => $request->quantity_2,
            'quantity_4' => $request->quantity_4,
            'quantity_8' => $request->quantity_8,
        ]);

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
        $recipes = Recipes::with('ingredients')->get()->find($id);

        if (!$recipes) {
            $data = [
                'message' => 'Receta no encontrada',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        return response()->json([
            'data' => $recipes,
        ], 200);
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
