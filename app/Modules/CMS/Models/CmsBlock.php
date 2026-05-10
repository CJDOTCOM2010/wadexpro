<?php

namespace App\Modules\CMS\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsBlock extends Model
{
    use HasUuids;

    protected $fillable = [
        'section_id', 'type', 'key', 'content', 'media_url',
        'link_url', 'link_text', 'sort_order', 'properties',
    ];

    protected function casts(): array
    {
        return [
            'sort_order'  => 'integer',
            'properties'  => 'array',
            'content'     => 'array',
            'link_text'   => 'array',
        ];
    }

    /**
     * Available block content types.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'heading'    => 'Heading',
        'paragraph'  => 'Paragraph',
        'image'      => 'Image',
        'button'     => 'Button / CTA',
        'icon_card'  => 'Icon Card',
        'step'       => 'Step (numbered)',
        'stat'       => 'Statistic Counter',
        'form'       => 'Embedded Form',
        'video'      => 'Video Embed',
        'spacer'     => 'Spacer',
        'divider'    => 'Divider',
        'rich_text'  => 'Rich Text',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CmsSection::class, 'section_id');
    }
}
