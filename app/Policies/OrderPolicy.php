<?php

namespace App\Policies;

use App\Models\Order;

use App\Models\Admin;


class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, Order $order): bool
    {
        return true;
    }

    /**
     * Determine whether the Admin can create models.
     */
    public function create(Admin $admin): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can update the model.
     */
    public function update(Admin $admin, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Order $order): bool
    {
        return false;
    }
}
