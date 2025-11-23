<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class RfqPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','sales']);
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, Rfq $rfq): bool
    {
           return $admin->hasRole(['Super Admin','sales']);
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
    public function update(Admin $admin, Rfq $rfq): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, Rfq $rfq): bool
    {
        return $admin->hasRole(['super-admin','sales']);
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, Rfq $rfq): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Rfq $rfq): bool
    {
        return false;
    }
}
