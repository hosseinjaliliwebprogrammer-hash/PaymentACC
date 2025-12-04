<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * دسترسی به پنل‌های Filament
     * - فقط ادمین اجازه ورود به پنل admin را دارد
     * - همه کاربران می‌توانند به پنل site وارد شوند
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return (bool) $this->is_admin;
        }

        if ($panel->getId() === 'site') {
            return true;
        }

        return false;
    }

    /**
     * رابطه با سفارش‌ها
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }
}
