<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Automatically generates UUID primary keys for Eloquent models.
 * Wrapper around Laravel's native HasUuids concern for project-wide consistency.
 */
trait HasUuid
{
    use HasUuids;
}
