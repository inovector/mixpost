<?php

namespace Lao9s\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Lao9s\Mixpost\Facades\SocialProviderManager;
use Lao9s\Mixpost\Model\Account;

class CallbackSocialProviderController extends Controller
{
    public function __invoke(string $providerName): \Illuminate\Http\RedirectResponse
    {
        $provider = SocialProviderManager::connect($providerName);

        $credentials = $provider->getAccessToken();

        $provider->setCredentials($credentials);

        $account = $provider->getAccount();

        Account::updateOrCreate(
            [
                'provider' => $providerName,
                'provider_id' => $account['id']
            ],
            [
                'name' => $account['name'],
                'username' => $account['username'],
                'image' => $account['image'],
                'credentials' => $credentials,
            ]
        );

        return redirect()->route('mixpost.accounts.index');
    }
}
