<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BrandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, Brand $brand): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can create models.
     */
    public function create(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can update the model.
     */
    public function update(Admin $admin, Brand $brand): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, Brand $brand): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, Brand $brand): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Brand $brand): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }
}
