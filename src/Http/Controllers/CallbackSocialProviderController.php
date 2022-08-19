<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Routing\Controller;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Model\Account;

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
