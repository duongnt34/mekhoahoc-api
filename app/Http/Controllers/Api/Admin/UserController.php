<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'avatar' => 'image|mimes:jpeg,png,jpg,webp,avif|max:2048'

        ]);
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');;
        $user->password = $request->input('password');;
        $user->save();

        $user->addMedia($request->file('avatar'))->toMediaCollection('avatar');

        return response()->json([
            'success' => true,
            'message' => 'Tạo tài khoản thành công'
        ], Response::HTTP_CREATED);
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
        if ($id == '1') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa Super Admin',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công',
        ], Response::HTTP_OK);
    }
}
