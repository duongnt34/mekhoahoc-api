<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if(!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $cookie = cookie('jwt', $token);

        return response()->json([
            'success' => true
        ])->withCookie($cookie);

    }

    public function user() {
        return auth()->user();
    }

    public function logout(Request $request) {
        $cookie = Cookie::forget('jwt');
        auth()->logout(true);
        return response()->json([
            'success' => true
        ], Response::HTTP_OK)->withCookie($cookie);
    }
}
