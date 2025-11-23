<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class StockItemPolicy
{
    /**
     * Determine whether the Admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','store']);
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, StockItem $stockItem): bool
    {
        return $admin->hasRole(['Super Admin','store']);
    }


    public function create(Admin $admin): bool
    {
        return false;
    }


    public function update(Admin $admin, StockItem $stockItem): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, StockItem $stockItem): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, StockItem $stockItem): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, StockItem $stockItem): bool
    {
        return false;
    }
}
