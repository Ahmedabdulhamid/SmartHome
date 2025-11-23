<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class QuotationPolicy
{
    /**
     * Determine whether the Admin can view any models.
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $admin, Quotation $quotation): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can create models.
     */
    public function create(Admin $admin): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can update the model.
     */
    public function update(Admin $admin, Quotation $quotation): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $admin, Quotation $quotation): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $admin, Quotation $quotation): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $admin, Quotation $quotation): bool
    {
        return $admin->hasRole(['Super Admin','sales','sales manager']);
    }
}
