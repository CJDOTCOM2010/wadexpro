<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemSettingResource extends JsonResource
{
    private bool $isSuperAdmin = true;

    /**
     * Convenience factory that marks encrypted values as masked for non-super-admins.
     */
    public static function collection($resource, bool $isSuperAdmin = true)
    {
        return $resource->map(fn ($item) => (new static($item))->withSuperAdmin($isSuperAdmin));
    }

    public function withSuperAdmin(bool $isSuperAdmin): static
    {
        $this->isSuperAdmin = $isSuperAdmin;
        return $this;
    }

    public function toArray(Request $request): array
    {
        $isEncrypted = $this->is_encrypted;

        return [
            'key'          => $this->key,
            'value'        => ($isEncrypted && !$this->isSuperAdmin) ? '••••••••' : $this->castValue(),
            'type'         => $this->type,
            'group'        => $this->group,
            'label'        => $this->label,
            'tooltip'      => $this->tooltip,
            'is_public'    => $this->is_public,
            'is_encrypted' => $isEncrypted,
            'updated_at'   => $this->updated_at?->toIso8601String(),
        ];
    }
}
