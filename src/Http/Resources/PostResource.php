<?php

namespace Inovector\Mixpost\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Util;

class PostResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'status' => $this->status(),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'versions' => PostVersionResource::collection($this->whenLoaded('versions')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'scheduled_at' => [
                'date' => $this->scheduled_at?->tz(Settings::get('timezone'))->toDateString(),
                'time' => $this->scheduled_at?->tz(Settings::get('timezone'))->format('H:i'),
                'human' => $this->scheduled_at ? Util::dateTimeFormat($this->scheduled_at, Settings::get('timezone')) : null,
            ],
            'published_at' => [
                'human' => $this->published_at?->tz(Settings::get('timezone'))->format("D, M j, " . Util::timeFormat())
            ]
        ];
    }

    protected function status()
    {
        if ($this->isScheduleProcessing()) {
            return 'PUBLISHING';
        }

        return $this->status->name;
    }
}
