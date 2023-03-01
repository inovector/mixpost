<?php

use Inertia\Testing\AssertableInertia as Assert;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
    Account::factory()->count(3)->create();
});

test('render dashboard page', function () {
    $this->actingAs(test()->user);

    $this->publishAssets();

    $this->get(route('mixpost.dashboard'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Dashboard')
            ->has('accounts', 3)
        );
});
