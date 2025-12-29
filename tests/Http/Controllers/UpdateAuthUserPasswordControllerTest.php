<?php

use Illuminate\Support\Facades\Hash;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can update the auth user password', function () {
    $this->actingAs(test()->user);

    $data = [
        'current_password' => 'password',
        'password' => 'password!!1',
        'password_confirmation' => 'password!!1',
    ];

    $this->putJson(route('mixpost.profile.updatePassword'), $data)->assertStatus(302);

    test()->user->refresh();

    expect(Hash::check('password!!1', test()->user->password))->toBeTrue();
});

it('can show validation on update the auth user password', function () {
    $this->actingAs(test()->user);

    $this->putJson(route('mixpost.profile.updatePassword'))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('can prevent unauthorized users to update the auth user password', function () {
    $this->putJson(route('mixpost.profile.updatePassword'))->assertUnauthorized();
});
