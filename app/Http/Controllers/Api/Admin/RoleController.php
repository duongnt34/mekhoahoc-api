<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:roles.create|roles.edit|roles.delete']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('created_at', 'desc')->get();
        return RoleResource::collection($roles);
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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required'
        ]);

        $role = new Role();
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
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
        $validated = $request->validate([
            'name' => '|unique:roles,name,'.$id,
            'permissions' => ''
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
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
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công',
        ], Response::HTTP_OK);

    }

    public function getRoleOptions(): JsonResponse
    {
        $roles = Role::select('id', 'name')->orderBy('name')->get()->map(function ($role) {
            return [
                "value" => $role->name,
                "label" => $role->name
            ];
        });

        return response()->json([
            'success' => true,
            'roles' => $roles
        ], Response::HTTP_OK);
    }
}
