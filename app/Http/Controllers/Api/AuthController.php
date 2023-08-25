<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], Response::HTTP_UNAUTHORIZED);
        }

        $cookie = cookie('jwt', $token);

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $user,
        ])->withCookie($cookie);

    }

    public function user() {
        if(!auth()->check()){
            return response()->json(['error' => 'Người dùng chưa đăng nhập'], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $user = auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng'], Response::HTTP_FORBIDDEN);
        }
        return $user;
    }

    public function logout(Request $request) {
        $cookie = Cookie::forget('jwt');
        auth()->logout(true);
        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất'
        ], Response::HTTP_OK)->withCookie($cookie);
    }
}
