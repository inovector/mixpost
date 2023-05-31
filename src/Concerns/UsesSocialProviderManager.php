<?php

namespace Inovector\Mixpost\Concerns;

use Inovector\Mixpost\Contracts\SocialProvider;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Models\Account;

trait UsesSocialProviderManager
{
    public function connectProvider(Account $account): SocialProvider
    {
        return SocialProviderManager::connect($account->provider, $account->values())
            ->useAccessToken($account->access_token->toArray());
    }
}
