<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        // Verificar credenciales y iniciar sesión
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $access_token = $user->createToken('authToken')->accessToken;

            return response()->json(['user' => $user, 'access_token' => $access_token]);
        } else {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json(['success' => true], 200);
    }


    public function auth(Request $request)
    {
        $token = $request->header('Authorization');
        $token = explode(' ', $token);
        $user = User::where('token', '=', $token[1])->first();
        if ($user !== null) {
            return response()->json([
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
