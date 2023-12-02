<?php

namespace Inovector\Mixpost\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mime_type' => $this->mime_type,
            'type' => $this->type(),
            'url' => $this->getUrl(),
            'thumb_url' => $this->isImageGif() ? $this->getUrl() : $this->getThumbUrl(),
            'is_video' => $this->isVideo(),
            'credit_url' => $this->credit_url ?? null,
            'download_data' => $this->download_data ?? null,
        ];
    }
}
