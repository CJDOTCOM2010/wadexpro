<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Transforms the User model into the API JSON response shape.
 * Sensitive fields (password, tokens, remember_token) are never exposed.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'user_type'    => $this->user_type,
            'avatar_url'   => $this->avatar_url,
            'is_active'    => $this->is_active,
            'is_verified'  => $this->is_verified,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),

            // Only include roles and permissions when loaded
            'roles'        => $this->whenLoaded('roles', fn () =>
                $this->roles->pluck('name')
            ),
            'permissions'  => $this->whenLoaded('roles', fn () =>
                $this->roles->flatMap->permissions->pluck('name')->unique()->values()
            ),

            // Only include wallet when loaded
            'wallet'       => $this->whenLoaded('wallet', fn () => [
                'balance'  => number_format((float) $this->wallet->balance, 2, '.', ''),
                'currency' => $this->wallet->currency,
                'is_frozen' => $this->wallet->is_frozen,
            ]),

            // Only include driver profile when loaded
            'driver_profile' => $this->whenLoaded('driver', fn () => [
                'id'           => $this->driver->id,
                'status'       => $this->driver->status,
                'is_online'    => (bool) $this->driver->is_online,
                'is_available' => (bool) $this->driver->is_available,
                'vehicle'      => $this->driver->activeVehicle ? [
                    'id'           => $this->driver->activeVehicle->id,
                    'make'         => $this->driver->activeVehicle->make,
                    'model'        => $this->driver->activeVehicle->model,
                    'plate_number' => $this->driver->activeVehicle->plate_number,
                    'color'        => $this->driver->activeVehicle->color,
                ] : null,
            ]),
        ];
    }
}
