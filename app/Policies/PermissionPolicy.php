<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
   public function viewAny(Admin $admin): bool
{
    return $admin->hasRole('Super Admin');
}

public function view(Admin $admin, Permission $permission): bool
{
    return $admin->hasRole('Super Admin');
}

public function create(Admin $admin): bool
{
    return $admin->hasRole('Super Admin');
}

public function update(Admin $admin, Permission $permission): bool
{
    return false;
}

public function delete(Admin $admin, Permission $permission): bool
{
    return false;
}

public function restore(Admin $admin, Permission $permission): bool
{
    return $admin->hasRole('Super Admin');
}

public function forceDelete(Admin $admin, Permission $permission): bool
{
    return $admin->hasRole('Super Admin');
}

}
