<?php

namespace Inovector\Mixpost\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Support\SocialProviderResponse;
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

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $responseEntities
         */
        $responseEntities = $provider->getEntities(withAccessToken: true);

        $entities = Arr::where($responseEntities->context(), function ($entity) use ($items) {
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

// @deprecated
// We will remove this feature soon
//    private function storeFacebookGroups(array $items): void
//    {
//        $provider = SocialProviderManager::connect('facebook_group');
//
//        /**
//         * Get entities with access token
//         *
//         * @var SocialProviderResponse $entities
//         */
//        $entities = $provider->getEntities();
//
//        $filterEntities = Arr::where($entities->context(), function ($entity) use ($items) {
//            return in_array($entity['id'], $items);
//        });
//
//        /**
//         * @var SocialProviderResponse $userAccount
//         */
//        $userAccount = $provider->getUserAccount();
//
//        $entities = Arr::map($filterEntities, function ($entity) use ($userAccount) {
//            return array_merge($entity, [
//                'data' => [
//                    'user' => [
//                        'id' => $userAccount->context()['id'],
//                        'name' => $userAccount->context()['name']
//                    ]
//                ],
//            ]);
//        });
//
//        $accessToken = $provider->getAccessToken();
//
//        foreach ($entities as $account) {
//            (new UpdateOrCreateAccount())(
//                providerName: 'facebook_group',
//                account: $account,
//                accessToken: $accessToken
//            );
//        }
//    }
}
