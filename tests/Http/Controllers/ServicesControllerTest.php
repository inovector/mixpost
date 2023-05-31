<?php

use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Service;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can update a service', function () {
    $this->actingAs(test()->user);

    $credentials = [
        'client_id' => 'my-client-id',
        'client_secret' => 'my-client-secret',
        'tier' => 'free'
    ];

    $this->putJson(route('mixpost.services.update', ['service' => 'twitter']), $credentials)->assertStatus(302);

    $model = Service::where('name', 'twitter')->first();

    expect($model !== null)->toBeTrue()
        ->and($model->credentials->toArray())->toBeArray()
        ->and($model->credentials->toArray())->toEqual($credentials)
        ->and(Services::getFromCache('twitter') !== null)->toBeTrue();
});

it('can show validation on update a service', function () {
    $this->actingAs(test()->user);

    $this->putJson(route('mixpost.services.update', ['service' => 'twitter']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['client_id', 'client_secret']);
});

it('can prevent unauthorized users to update a service', function () {
    $this->putJson(route('mixpost.services.update', ['service' => 'service']))->assertUnauthorized();
});

