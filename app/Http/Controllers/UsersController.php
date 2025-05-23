<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users::all();

        if ($users->isEmpty()) {
            $data = [
                'message' => 'No se encontraron usuarios',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        return response()->json($users, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $users = Users::where('email', $request->email)->first();

        if (!$users || !Hash::check($request->password, $users->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'status' => 401
            ], 401);
        }

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $users,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'max:20'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
            'role' => ['in:ADMIN,USER,CHEF']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validacion de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $users = Users::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);

        if (!$users) {
            return response()->json([
                'message' => 'Error al registrarse',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => $users,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
