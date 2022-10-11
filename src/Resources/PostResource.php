<?php

namespace Inovector\Mixpost\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status->name,
            'accounts' => $this->accounts,
            'versions' => PostVersionResource::collection($this->whenLoaded('versions')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'scheduled_at' => [
                'date' => $this->scheduled_at?->toDateString(),
                'time' => $this->scheduled_at?->format('H:i'),
                'human' => $this->scheduled_at?->format("D, M j, H:i")
            ],
            'delivered_at' => [
                'human' => $this->delivered_at?->format("D, M j, H:i")
            ]
        ];
    }
}
