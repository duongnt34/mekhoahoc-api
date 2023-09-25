<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\UserStoreRequest;
use App\Http\Requests\Api\Users\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:users.create|users.edit|users.delete']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        if ($request->has('name')) {
            $users = User::where('name', 'like', '%'.$request->name.'%')->orWhere('email', 'like',
                '%'.$request->name.'%')->orderBy('created_at', 'desc')->get();
        } else {
            $users = User::orderBy('created_at', 'desc')->get();
        }
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->syncRoles($validated['roles']);
        $user->save();

        if (filled($validated['avatar'])) {
            $user->addMedia($validated['avatar'])->usingName($user->name.'_avatar')->toMediaCollection('avatar');
        }

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
    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $user = User::find($id);
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (filled($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $user->update($userData);

        if (filled($validated['avatar'])) {
            $user->clearMediaCollection('avatar');
            $user->addMedia($validated['avatar'])->usingName($user->name.'_avatar')->toMediaCollection('avatar');
        }

        return response()->json([
            'success' => true,
            'message' => 'Sửa tài khoản thành công'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        if ($id == '1') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa Super Admin',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::findOrFail($id);
        $user->removeRole($user->roles);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công',
        ], Response::HTTP_OK);
    }
}
