<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use function Psy\debug;

class AuthController extends Controller
{
    public function login(ClientRequest $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if(Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'Authenticated',
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 'Unauthenticatedeeee',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
