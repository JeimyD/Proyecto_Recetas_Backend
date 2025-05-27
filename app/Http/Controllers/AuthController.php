<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => ['required', 'max:20'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],['confirmed'],
            'role' => ['in:ADMIN,USER,CHEF']
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = Users::create($data);

        $token = $user->createToken($request->name);

        return response()->json([
            'message' => 'Credenciales creadas',
            'user' => $user,
            'token' => $token->plainTextToken,
            'status' => 200
        ], 200);
    }

    public function login(Request $request){
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required']
        ]);

        $user = Users::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Credenciales incorrectas',
                'status' => 401
            ], 401);
        }

        $token = $user->createToken($user->name);

        return response()->json([
            'message' => 'conexion existosa',
            'user' => $user,
            'token' => $token->plainTextToken,
            'status' => 200
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return [
            'message' => 'te deslogueaste'
        ];
    }
}
