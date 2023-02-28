<?php

use function Pest\Faker\faker;
use Illuminate\Support\Str;
use Inovector\Mixpost\Actions\UpdateOrCreateAccount;
use Inovector\Mixpost\Models\Account;

it('can create new account', function () {
    $providerId = Str::random();

    $account = [
        'id' => $providerId,
        'name' => 'Name of Account',
        'username' => 'username',
        'image' => faker()->imageUrl()
    ];

    (new UpdateOrCreateAccount())('twitter', $account, ['access_token' => ['auth_token' => 'my-token']]);

    $account = Account::where('provider_id', $providerId)->first();

    expect($account)->toBeObject()->and($account->image())->toBeString();
});

it('can update the account', function () {
    $account = Account::factory()->create();

    $data = [
        'id' => $account->provider_id,
        'name' => 'Updated name',
        'username' => 'updated_username',
        'image' => '',
    ];

    (new UpdateOrCreateAccount())($account->provider, $data, ['access_token' => ['auth_token' => 'my-token']]);

    $account->refresh();

    expect($account->name)->toEqual($data['name'])
        ->and($account->username)->toEqual($data['username']);
});
