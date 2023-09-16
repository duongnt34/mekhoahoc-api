<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Admin']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index() : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $permissions = Permission::orderBy('created_at', 'desc')->get();
        return PermissionResource::collection($permissions);
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
            'description' => 'required'
        ]);

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();

        $permissions = Permission::orderBy('created_at', 'desc')->get();
        return PermissionResource::collection($permissions);

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

    public function getPermissionOptions()
    {
        $permissions      = Permission::select('id', 'description', 'name')->orderBy('name')->get()->map(function ($role) {
            $explodedName = explode('.', $role->name);

            $groupName = $explodedName[0];

            return [
                'value'     => $role->name,
                'label'   => $role->description,
                'parent' => $groupName,
            ];
        });

        $groupNameMap = [
            'users' => 'Tài khoản',
            'roles' => 'Vai trò',
            'permissions' => 'Quyền'
        ];

        $treeData = [];
        foreach ($groupNameMap as $key => $group) {
            $filteredPermissions = $permissions->filter(function ($permission) use ($key) {
                return $permission['parent'] === $key;
            })
            ->map(function ($permission) {
                unset($permission['parent']);
                return $permission;
            })->toArray();
            $treeData[] = [
                'value' => $key,
                'label' => $group,
                'children' => array_values($filteredPermissions)
            ];
        }
        return response()->json([
        'data' => $treeData
        ]);
    }
}
