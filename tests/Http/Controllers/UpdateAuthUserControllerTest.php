<?php

use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can update the auth user', function () {
    $this->actingAs(test()->user);

    $data = [
        'name' => 'name',
        'email' => 'test@mail.com',
    ];

    $this->putJson(route('mixpost.profile.updateUser'), $data)->assertStatus(302);

    test()->user->refresh();

    expect(test()->user->name)->toBe('name')
        ->and(test()->user->email)->toBe('test@mail.com');
});

it('can show validation on update the auth user', function () {
    $this->actingAs(test()->user);

    $this->putJson(route('mixpost.profile.updateUser'))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('can prevent unauthorized users to update the auth user', function () {
    $this->putJson(route('mixpost.profile.updateUser'))->assertUnauthorized();
});
