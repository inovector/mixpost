<?php

namespace Inovector\Mixpost;

use Composer\InstalledVersions;
use Inovector\Mixpost\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use Inovector\Mixpost\Actions\CreateMastodonApp;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\SocialProviders\FacebookGroupProvider;
use Inovector\Mixpost\SocialProviders\FacebookPageProvider;
use Inovector\Mixpost\SocialProviders\TwitterProvider;
use Inovector\Mixpost\SocialProviders\MastodonProvider;

class SocialProviderManager extends SocialProviderManagerAbstract
{
    protected function connectTwitterProvider()
    {
        $config = Services::get('twitter');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'twitter']);

        return $this->buildConnectionProvider(TwitterProvider::class, $config);
    }

    protected function connectFacebookPageProvider()
    {
        $config = Services::get('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_page']);

        return $this->buildConnectionProvider(FacebookPageProvider::class, $config);
    }

    protected function connectFacebookGroupProvider()
    {
        $config = Services::get('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_group']);

        return $this->buildConnectionProvider(FacebookGroupProvider::class, $config);
    }

    protected function connectMastodonProvider()
    {
        $request = $this->container->request;
        $sessionServerKey = 'mixpost_mastodon_server';

        $intendAddAccount = $request->route() && $request->route()->getName() === 'mixpost.accounts.add';
        $intendCallback = $request->route() && $request->route()->getName() === 'mixpost.callbackSocialProvider';

        if ($intendAddAccount) {
            $serverName = $this->container->request->input('server');

            // We keep the server name in the session. We'll need it in the callback
            $request->session()->put($sessionServerKey, $serverName);
        } else if ($intendCallback) {
            $serverName = $request->session()->get($sessionServerKey);
        } else {
            // Get options which was registered on SocialProviderManager::connect($provider)
            $account = Account::find($this->options['account_id']);
//            $serverName = ;
        }

        $serviceName = "mastodon.$serverName";

        $config = Services::get($serviceName);

        if (!$config) {
            $credentials = (new CreateMastodonApp())($serverName);
            $config = (new UpdateOrCreateService())($serviceName, $credentials)->credentials->toArray();
        }

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']);
        $config['options'] = [
            'server' => $serverName
        ];

        return $this->buildConnectionProvider(MastodonProvider::class, $config);
    }
}
