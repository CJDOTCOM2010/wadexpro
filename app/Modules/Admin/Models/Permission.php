<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'module', 'label', 'guard_name'];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
