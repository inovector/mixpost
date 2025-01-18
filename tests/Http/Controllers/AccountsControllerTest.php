<?php

use Inertia\Testing\AssertableInertia as Assert;
use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();

    Account::factory()->count(3)->create();

    $this->publishAssets();
});

it('show list of accounts on Accounts page with service alerts', function () {
    $this->actingAs(test()->user);

    $this->get(route('mixpost.accounts.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Accounts/Accounts')
            ->has('accounts', 3)
            ->where('is_configured_service.twitter', false)
            ->where('is_configured_service.facebook', false)
        );
});

it('show list of accounts on Accounts page without service alerts', function () {
    $this->actingAs(test()->user);

    (new UpdateOrCreateService())('twitter', [
        'client_id' => '11',
        'client_secret' => 'secret-twitter',
        'tier' => 'free'
    ], true);

    (new UpdateOrCreateService())('facebook', [
        'client_id' => '222',
        'client_secret' => 'secret-fb',
        'api_version' => 'v21.0'
    ]);

    $this->get(route('mixpost.accounts.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Accounts/Accounts')
            ->has('accounts', 3)
            ->where('is_configured_service.twitter', true)
            ->where('is_configured_service.facebook', true)
            ->where('is_service_active.twitter', true)
            ->where('is_service_active.facebook', false)
        );
});
