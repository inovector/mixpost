<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Inovector\Mixpost\Models\Media;

class EagerLoadPostVersionsMedia
{
    protected Collection $mediaCollection;

    public function __construct(readonly LengthAwarePaginator|Collection|Model $postResult)
    {
        $this->mediaCollection = $this->getMediaCollection();
    }

    public function handle(): void
    {
        $this->parseResult()->each(function ($post) {
            $post->versions->each(function ($version) {
                $content = $version->content;

                if (is_array($content)) {
                    foreach ($content as &$item) {
                        if (isset($item['media']) && is_array($item['media'])) {
                            foreach ($item['media'] as &$mediaId) {
                                if (isset($this->mediaCollection[$mediaId])) {
                                    $mediaId = $this->mediaCollection[$mediaId];
                                }
                            }
                        }
                    }
                }

                $version->setAttribute('content_with_relations', $content);
            });
        });
    }

    public static function apply(LengthAwarePaginator|Collection|Model $postResult): void
    {
        (new static($postResult))->handle();
    }

    protected function extractMediaIds(): array
    {
        $mediaIds = [];

        $this->parseResult()->each(function ($post) use (&$mediaIds) {
            $post->versions->each(function ($version) use (&$mediaIds) {
                if (is_array($version->content)) {
                    foreach ($version->content as $item) {
                        if (isset($item['media']) && is_array($item['media'])) {
                            $mediaIds = array_merge($mediaIds, $item['media']);
                        }
                    }
                }
            });
        });

        return array_unique($mediaIds);
    }

    protected function getMediaCollection(): Collection|EloquentCollection
    {
        $mediaIds = $this->extractMediaIds();

        if (empty($mediaIds)) {
            return collect();
        }

        $mediaItems = Media::findMany($mediaIds);

        return $mediaItems->keyBy('id');
    }

    protected function parseResult(): Model|Collection|EloquentCollection|LengthAwarePaginator
    {
        if ($this->postResult instanceof Model) {
            return collect([$this->postResult]);
        }

        return $this->postResult;
    }
}
