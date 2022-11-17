<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inovector\Mixpost\Facades\SocialProviderManager;
use InvalidArgumentException;

class StoreProviderEntitiesAsAccounts
{
    public function __invoke(string $provider, array $items)
    {
        $method = 'store' . Str::studly(Str::plural($provider));

        if (method_exists($this, $method)) {
            return $this->$method($items);
        }

        throw new InvalidArgumentException("Provider [$provider] not supported entities.");
    }

    private function storeFacebookPages(array $items): void
    {
        $provider = SocialProviderManager::connect('facebook_page');

        // Get entities with access token
        $getEntities = $provider->getEntities(withAccessToken: true);

        $entities = Arr::where($getEntities, function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'facebook_page',
                account: $account,
                accessToken: array_merge($provider->getAccessToken(), ['page_access_token' => $account['access_token']['access_token']])
            );
        }
    }

    private function storeFacebookGroups(array $items): void
    {
        $provider = SocialProviderManager::connect('facebook_group');

        $getEntities = $provider->getEntities();

        $filterEntities = Arr::where($getEntities, function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        $account = $provider->getAccount();
        $accessToken = $provider->getAccessToken();

        $entities = Arr::map($filterEntities, function ($entity) use ($account, $accessToken) {
            return array_merge($entity, [
                'data' => [
                    'posting_as_user' => [
                        'id' => $account['id'],
                        'name' => $account['name']
                    ]
                ],
                'access_token' => $accessToken
            ]);
        });

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'facebook_group',
                account: $account,
                accessToken: $account['access_token']
            );
        }
    }
}
