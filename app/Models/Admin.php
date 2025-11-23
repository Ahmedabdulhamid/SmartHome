<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable; // <-- ده المهم

class Admin extends Authenticatable implements FilamentUser
{
    use HasRoles, Notifiable; // <-- خليها هنا

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $guard_name = 'admin';

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
