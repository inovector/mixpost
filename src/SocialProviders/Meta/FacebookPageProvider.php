<?php

namespace Inovector\Mixpost\SocialProviders\Meta;

use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\ManagesFacebookOAuth;
use Inovector\Mixpost\SocialProviders\Meta\Concerns\ManagesFacebookPageResources;

class FacebookPageProvider extends MetaProvider
{
    use ManagesFacebookOAuth;
    use ManagesFacebookPageResources;

    public bool $onlyUserAccount = false;

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://www.facebook.com/{$accountResource->pivot->provider_post_id}";
    }
}
