<?php

namespace Inovector\Mixpost\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PostVersion extends Model
{
    public $table = 'mixpost_post_versions';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'is_original',
        'content'
    ];

    protected $casts = [
        'is_original' => 'boolean',
        'content' => 'array',
    ];

    public function scopeHasMedia(Builder $query, Media $media): Builder
    {
        return $query->whereRaw("JSON_SEARCH(content, 'all', ?, NULL, '$[*].media') is not null", [(string)$media->id]);
    }

    public function removeMedia(Media $media): void
    {
        $content = $this->content;

        foreach ($content as $i => $contentData) {
            $content[$i]['media'] = array_values(array_filter($contentData['media'], function ($val) use ($media) {
                return $val != (string)$media->id;
            }));
        }

        $this->content = $content;
        $this->save();
    }

}
