<?php

namespace Inovector\Mixpost\SocialProviders\Mastodon;

use Illuminate\Http\Request;
use Inovector\Mixpost\Abstracts\SocialProvider;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesOAuth;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesRateLimit;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesResources;
use Inovector\Mixpost\Support\SocialProviderPostConfigs;
use Inovector\Mixpost\Util;

class MastodonProvider extends SocialProvider
{
    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion = 'v1';
    protected string $serverUrl;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->serverUrl = "https://{$values['data']['server']}";

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public static function service(): string
    {
        return 'mastodon';
    }

    public static function postConfigs(): SocialProviderPostConfigs
    {
        return SocialProviderPostConfigs::make()
            ->simultaneousPosting(Util::config('social_provider_options.mastodon.simultaneous_posting_on_multiple_accounts'))
            ->minTextChar(1)
            ->maxTextChar(Util::config('social_provider_options.mastodon.post_character_limit'))
            ->minPhotos(1)
            ->minVideos(1)
            ->minGifs(1)
            ->maxPhotos(Util::config('social_provider_options.mastodon.media_limit.photos'))
            ->maxVideos(Util::config('social_provider_options.mastodon.media_limit.videos'))
            ->maxGifs(Util::config('social_provider_options.mastodon.media_limit.gifs'))
            ->allowMixingMediaTypes(Util::config('social_provider_options.mastodon.allow_mixing'));
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $server = $accountResource->data['server'] ?? 'undefined';

        return "https://$server/@$accountResource->username/{$accountResource->pivot->provider_post_id}";
    }
}
