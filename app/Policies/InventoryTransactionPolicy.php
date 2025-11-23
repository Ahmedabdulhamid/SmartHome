<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventoryTransactionPolicy
{

    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','store','admin']);
    }

    public function view(Admin $admin, InventoryTransaction $inventoryTransaction): bool
    {
        return $admin->hasRole(['Super Admin','store','admin']);
    }

    public function create(Admin $admin): bool
    {
        return false;
    }

    public function update(Admin $admin, InventoryTransaction $inventoryTransaction): bool
    {
        return false;
    }

    public function delete(Admin $admin, InventoryTransaction $inventoryTransaction): bool
    {
        return false;
    }

    public function restore(Admin $admin, InventoryTransaction $inventoryTransaction): bool
    {
        return false;
    }


    public function forceDelete(Admin $admin, InventoryTransaction $inventoryTransaction): bool
    {
        return false;
    }
}
