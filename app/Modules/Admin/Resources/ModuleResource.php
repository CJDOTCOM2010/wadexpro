<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug'        => $this->slug,
            'name'        => $this->name,
            'description' => $this->description,
            'is_enabled'  => $this->is_enabled,
            'version'     => $this->version,
            'config'      => $this->config,
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
