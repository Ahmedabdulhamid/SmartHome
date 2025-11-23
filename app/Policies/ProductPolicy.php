<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the Admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, Product $product): bool
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
    public function update(Admin $admin, Product $product): bool
    {
       return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, Product $product): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, Product $product): bool
    {
       return $admin->hasRole(['Super Admin','admin']);
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Product $product): bool
    {
        return $admin->hasRole(['Super Admin','admin']);
    }
}
