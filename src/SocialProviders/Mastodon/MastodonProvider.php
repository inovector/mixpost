<?php

namespace Inovector\Mixpost\SocialProviders\Mastodon;

use Illuminate\Http\Request;
use Inovector\Mixpost\Abstracts\SocialProvider;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesOAuth;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesRateLimit;
use Inovector\Mixpost\SocialProviders\Mastodon\Concerns\ManagesResources;

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

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        $server = $accountResource->data['server'] ?? 'undefined';

        return "https://$server/@$accountResource->username/{$accountResource->pivot->provider_post_id}";
    }
}
