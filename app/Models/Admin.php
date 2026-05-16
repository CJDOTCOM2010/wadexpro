<?php

namespace App\Models;

use App\Modules\Admin\Models\AdminAuditLog;
use App\Modules\Admin\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'role',
        'level',
        'avatar_url',
        'is_active',
        'is_super_admin',
        'last_login_at',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true || $this->level === 'super_admin';
    }

    public function hasRole(string $roleName): bool
    {
        if ($roleName === 'super_admin') {
            return $this->isSuperAdmin();
        }

        return $this->role === $roleName;
    }

    public function canAccessModule(string $moduleSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return Module::where('slug', $moduleSlug)
            ->where('is_enabled', true)
            ->exists();
    }

    public function auditLogs()
    {
        return $this->hasMany(AdminAuditLog::class, 'admin_id');
    }
}
