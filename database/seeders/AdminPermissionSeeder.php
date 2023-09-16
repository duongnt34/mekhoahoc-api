<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [];

        // Tài khoản
        $permissions[] = Permission::create(
            ['name' => 'users.create',
                'description' => 'Tạo tài khoản'],
        );
        $permissions[] = Permission::create(
            ['name' => 'users.edit',
                'description' => 'Sửa tài khoản'],
        );
        $permissions[] = Permission::create(
            ['name' => 'users.delete',
                'description' => 'Xóa tài khoản'],
        );

        // Vai trò
        $permissions[] = Permission::create(
            ['name' => 'roles.create',
                'description' => 'Tạo vai trò'],
        );
        $permissions[] = Permission::create(
            ['name' => 'roles.edit',
                'description' => 'Sửa vai trò'],
        );
        $permissions[] = Permission::create(
            ['name' => 'roles.delete',
                'description' => 'Xóa vai trò'],
        );

        // Quyền
        $permissions[] = Permission::create(
            ['name' => 'permissions.create',
                'description' => 'Tạo quyền'],
        );
        $permissions[] = Permission::create(
            ['name' => 'permissions.edit',
                'description' => 'Sửa quyền'],
        );
        $permissions[] = Permission::create(
            ['name' => 'permissions.delete',
                'description' => 'Xóa quyền'],
        );

        $roleAdmin = Role::findByName('Admin');
        $roleAdmin->syncPermissions($permissions);
    }
}
