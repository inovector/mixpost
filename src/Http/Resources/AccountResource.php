<?php

namespace Inovector\Mixpost\Http\Resources;

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
            'image' => $this->image(),
            'provider' => $this->provider,
            'provider_options' => $this->providerOptions(),
            'created_at' => $this->created_at->diffForHumans(),
            'errors' => $this->whenPivotLoaded('mixpost_post_accounts', function () {
                return $this->pivot->errors ? json_decode($this->pivot->errors) : [];
            })
        ];
    }
}
