<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\WhatsappSetting;
use Illuminate\Auth\Access\Response;

class WhatsappSettingPolicy
{
    /**
     * Determine whether the Admin can view any models.
     */
    public function viewAny(Admin $Admin): bool
    {
        return true;
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $Admin, WhatsappSetting $whatsappSetting): bool
    {
        return true;
    }

    /**
     * Determine whether the Admin can create models.
     */
    public function create(Admin $Admin): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can update the model.
     */
    public function update(Admin $Admin, WhatsappSetting $whatsappSetting): bool
    {
        return true;
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $Admin, WhatsappSetting $whatsappSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $Admin, WhatsappSetting $whatsappSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $Admin, WhatsappSetting $whatsappSetting): bool
    {
        return false;
    }
}
