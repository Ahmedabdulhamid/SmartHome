<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $adminPermissions = [
            'create_products',
            'edit_products',
            'delete_products',
            'view_products',
            'manage_users',
            'view_reports',
            'manage_roles',
            'manage_permissions',
            'manage_localization',
        ];
        foreach ($adminPermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}
