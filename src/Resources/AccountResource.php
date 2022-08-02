<?php

namespace Lao9s\Mixpost\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AccountResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'provider' => $this->provider,
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
