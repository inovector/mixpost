<?php

namespace Inovector\Mixpost\SocialProviders\Pubky;

use Illuminate\Http\Request;
use Inovector\Mixpost\Abstracts\SocialProvider;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\SocialProviders\Pubky\Concerns\ManagesOAuth;
use Inovector\Mixpost\SocialProviders\Pubky\Concerns\ManagesResources;
use Inovector\Mixpost\Support\SocialProviderPostConfigs;
use Inovector\Mixpost\Util;

class PubkyProvider extends SocialProvider
{
    use ManagesOAuth;
    use ManagesResources;

    public array $callbackResponseKeys = ['secret'];

    protected string $homeserver;
    protected string $publicKey;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->homeserver = rtrim($values['data']['homeserver'] ?? '', '/');
        $this->publicKey = $values['data']['public_key'] ?? '';

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public static function service(): string
    {
        return 'pubky';
    }

    public static function name(): string
    {
        return 'Pubky';
    }

    public static function postConfigs(): SocialProviderPostConfigs
    {
        return SocialProviderPostConfigs::make()
            ->simultaneousPosting(Util::config('social_provider_options.pubky.simultaneous_posting_on_multiple_accounts'))
            ->minTextChar(1)
            ->maxTextChar(Util::config('social_provider_options.pubky.post_character_limit'))
            ->minPhotos(0)
            ->minVideos(0)
            ->minGifs(0)
            ->maxPhotos(0)
            ->maxVideos(0)
            ->maxGifs(0)
            ->allowMixingMediaTypes(false);
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $homeserver = $accountResource->data['homeserver'] ?? '';
        $postId = $accountResource->pivot->provider_post_id ?? '';

        if (!$homeserver || !$postId) {
            return '';
        }

        return "{$homeserver}/pub/mixpost/posts/{$postId}";
    }
}