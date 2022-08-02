<?php

namespace Lao9s\Mixpost;

use Lao9s\Mixpost\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use Lao9s\Mixpost\SocialProviders\TwitterProvider;

class SocialProviderManager extends SocialProviderManagerAbstract
{
    protected function connectTwitterProvider()
    {
        $config = $this->config->get('mixpost.credentials.twitter');

        return $this->buildConnectionProvider(TwitterProvider::class, $config);
    }

    protected function connectFacebookProvider()
    {
        // TODO: Build connection with Facebook provider
    }
}
