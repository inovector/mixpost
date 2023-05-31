<?php

namespace Inovector\Mixpost\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Inovector\Mixpost\SocialProviders\Mastodon\MastodonProvider;
use Inovector\Mixpost\SocialProviders\Meta\FacebookGroupProvider;
use Inovector\Mixpost\SocialProviders\Meta\FacebookPageProvider;
use Inovector\Mixpost\SocialProviders\Twitter\TwitterProvider;

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
        return match ($this->provider) {
            'twitter' => TwitterProvider::externalPostUrl($this),
            'facebook_page' => FacebookPageProvider::externalPostUrl($this),
            'facebook_group' => FacebookGroupProvider::externalPostUrl($this),
            'mastodon' => MastodonProvider::externalPostUrl($this),
            default => '#'
        };
    }
}
