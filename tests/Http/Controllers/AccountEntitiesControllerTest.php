<?php

use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('redirect to the accounts page if the callback response is missing', function () {
    $this->actingAs(test()->user);

    $this->get(route('mixpost.accounts.entities.index', ['provider' => 'provider']))
        ->assertRedirectToRoute('mixpost.accounts.index');
});

it('will show validation errors on store entities', function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.accounts.entities.store', ['provider' => 'provider']))
        ->assertStatus(422)
        ->assertJsonValidationErrors('items');
});
