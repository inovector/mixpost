<?php

namespace Inovector\Mixpost\Actions;

use Inovector\Mixpost\Models\Account;

class UpdateOrCreateAccount
{
    public function __invoke(string $providerName, array $account, array $accessToken): void
    {
        Account::updateOrCreate(
            [
                'provider' => $providerName,
                'provider_id' => $account['id']
            ],
            [
                'name' => $account['name'],
                'username' => $account['username'] ?? null,
                'image' => $account['image'],
                'data' => $account['data'] ?? null,
                'access_token' => $accessToken,
            ]
        );
    }
}
