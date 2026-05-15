<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    use HasUuid;

    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public static function log(string $action, string $description, array $metadata = []): self
    {
        $adminId = null;
        if (auth('admin')->check()) {
            $adminId = auth('admin')->id();
        }

        return static::create([
            'admin_id' => $adminId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logSuperAdminAction(string $action, string $description, array $metadata = []): self
    {
        return static::log($action, $description, array_merge($metadata, ['super_admin_action' => true]));
    }
}
