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
            ->where('has_service.twitter', false)
            ->where('has_service.facebook', false)
        );
});

it('show list of accounts on Accounts page without service alerts', function () {
    $this->actingAs(test()->user);

    (new UpdateOrCreateService())('twitter', [
        'client_id' => 'my-tw-client-id',
        'client_secret' => 'my-tw-client-secret'
    ]);

    (new UpdateOrCreateService())('facebook', [
        'client_id' => 'my-fb-client-id',
        'client_secret' => 'my-fb-client-secret'
    ]);

    $this->get(route('mixpost.accounts.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Accounts/Accounts')
            ->has('accounts', 3)
            ->where('has_service.twitter', true)
            ->where('has_service.facebook', true)
        );
});
