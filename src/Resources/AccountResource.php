<?php

namespace Lao9s\Mixpost\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'image' => $this->image,
            'provider' => $this->provider,
            'provider_options' => socialProviderOptions($this->provider),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
