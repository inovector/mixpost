<?php

namespace Inovector\Mixpost;

use Inovector\Mixpost\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use Inovector\Mixpost\SocialProviders\FacebookGroupProvider;
use Inovector\Mixpost\SocialProviders\FacebookPageProvider;
use Inovector\Mixpost\SocialProviders\TwitterProvider;

class SocialProviderManager extends SocialProviderManagerAbstract
{
    protected function connectTwitterProvider()
    {
        $config = $this->config->get('mixpost.credentials.twitter');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'twitter']);

        return $this->buildConnectionProvider(TwitterProvider::class, $config);
    }

    protected function connectFacebookPageProvider()
    {
        $config = $this->config->get('mixpost.credentials.facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_page']);

        return $this->buildConnectionProvider(FacebookPageProvider::class, $config);
    }

    protected function connectFacebookGroupProvider()
    {
        $config = $this->config->get('mixpost.credentials.facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_group']);

        return $this->buildConnectionProvider(FacebookGroupProvider::class, $config);
    }
}
