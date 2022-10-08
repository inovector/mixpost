<?php

namespace Inovector\Mixpost\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Inovector\Mixpost\Model\Media;

class PostVersionResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        $mediaCollection = $this->mediaCollection();

        return [
            'post_id' => $this->post_id,
            'account_id' => $this->account_id,
            'is_original' => $this->is_original,
            'content' => collect($this->content)->map(function ($item) use ($mediaCollection) {
                return [
                    'body' => (string)$item['body'],
                    'media' => collect($item['media'])->map(function ($mediaId) use ($mediaCollection) {
                        $media = $mediaCollection->where('id', $mediaId)->first();

                        if (!$media) {
                            return null;
                        }

                        return new MediaResource($media);
                    })->filter()->values()
                ];
            })
        ];
    }

    protected function mediaCollection()
    {
        $mediaIds = [];

        foreach ($this->content as $item) {
            $mediaIds = array_merge($mediaIds, $item['media']);
        }

        return Media::whereIn('id', $mediaIds)->get();
    }
}
