<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_name',
        'channel',
        'subject',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Parse the template and replace placeholders with actual data.
     * Example placeholders: {user_name}, {amount}, {ride_id}
     */
    public function render(array $data): string
    {
        $content = $this->content;
        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', (string) $value, $content);
        }
        return $content;
    }
}
