<?php

namespace Inovector\Mixpost\SocialProviders\Meta;

use Illuminate\Http\Request;
use Inovector\Mixpost\Abstracts\SocialProvider;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Services\FacebookService;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\ManagesConfig;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\ManagesMetaResources;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\ManagesRateLimit;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\MetaOauth;
use Inovector\Mixpost\Support\SocialProviderPostConfigs;
use Inovector\Mixpost\Util;

class MetaProvider extends SocialProvider
{
    use ManagesRateLimit;
    use MetaOauth;
    use ManagesConfig;
    use ManagesMetaResources;

    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion;
    protected string $apiUrl = 'https://graph.facebook.com';

    protected string $scope;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->setApiVersion();

        $this->setScope();

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public static function service(): string
    {
        return FacebookService::class;
    }

    protected function setApiVersion(): void
    {
        $this->apiVersion = $this->getApiVersionConfig();
    }

    protected function setScope(): void
    {
        $this->scope = implode(',', $this->getSupportedScopeList());
    }

    public function getSupportedScopeList(): array
    {
        return match ($this->apiVersion) {
            'v16.0' => [
                'pages_show_list',
                'read_insights',
                'pages_manage_posts',
                'pages_read_engagement',
                'pages_manage_engagement',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_insights',
                'instagram_manage_comments',
            ],
            default => [
                'business_management',
                'pages_show_list',
                'read_insights',
                'pages_manage_posts',
                'pages_read_engagement',
                'pages_manage_engagement',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_insights',
                'instagram_manage_comments',
            ]
        };
    }

    public function getAuthUrl(): string
    {
        return '';
    }

    public static function postConfigs(): SocialProviderPostConfigs
    {
        return SocialProviderPostConfigs::make()
            ->simultaneousPosting(Util::config('social_provider_options.facebook_page.simultaneous_posting_on_multiple_accounts'))
            ->minTextChar(1)
            ->minPhotos(1)
            ->minVideos(1)
            ->minGifs(1)
            ->maxTextChar(Util::config('social_provider_options.facebook_page.post_character_limit'))
            ->maxPhotos(Util::config('social_provider_options.facebook_page.media_limit.photos'))
            ->maxVideos(Util::config('social_provider_options.facebook_page.media_limit.videos'))
            ->maxGifs(Util::config('social_provider_options.facebook_page.media_limit.gifs'))
            ->allowMixingMediaTypes(Util::config('social_provider_options.facebook_page.allow_mixing'));
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return '#';
    }
}
