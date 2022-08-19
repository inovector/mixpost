<?php

namespace Inovector\Mixpost;

use Inovector\Mixpost\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use Inovector\Mixpost\SocialProviders\TwitterProvider;

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
