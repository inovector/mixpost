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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'image' => $this->image(),
            'provider' => $this->provider,
            'provider_name' => $this->providerName(),
            'post_configs' => $this->postConfigs(),
            'data' => $this->data,
            'authorized' => $this->authorized,
            'created_at' => $this->created_at->diffForHumans(),
            'external_url' => $this->whenPivotLoaded('mixpost_post_accounts', function () {
                if (!$this->pivot->provider_post_id) {
                    return null;
                }

                return $this->getExternalPostUrl();
            }),
            'errors' => $this->whenPivotLoaded('mixpost_post_accounts', function () {
                return $this->pivot->errors ? json_decode($this->pivot->errors) : [];
            })
        ];
    }

    protected function getExternalPostUrl(): ?string
    {
        if ($provider = $this->resource->getProviderClass()) {
            return $provider::externalPostUrl($this);
        }

        return '#';
    }
}
