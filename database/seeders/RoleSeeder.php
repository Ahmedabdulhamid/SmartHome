<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     $adminRole = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'admin',
        ]);
        $adminRole->givePermissionTo(Permission::where('guard_name', 'admin')->get());


          $productManagerRole = Role::create([
            'name' => 'Product Manager',
            'guard_name' => 'admin',
        ]);

        // Assign فقط Permissions المتعلقة بالمنتجات
        $productManagerRole->givePermissionTo([
            'create_products',
            'edit_products',
            'delete_products',
            'view_products',
        ]);
    }
}
