<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'module', 'label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
