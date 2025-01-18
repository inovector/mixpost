<?php

use Inertia\Testing\AssertableInertia as Assert;
use Inovector\Mixpost\Facades\ServiceManager;
use Inovector\Mixpost\Models\User;
use Inovector\Mixpost\Models\Service;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('shows services page', function () {
    $this->actingAs(test()->user);

    $this->publishAssets();

    $this->get(route('mixpost.services.index'))
        ->assertInertia(fn(Assert $page) => $page
            ->component('Services')
            ->where('services', ServiceManager::all())
        );
});

it('can update a service', function () {
    $this->actingAs(test()->user);

    $data = [
        'configuration' => [
            'client_id' => 'my-client-id',
            'client_secret' => 'my-client-secret',
            'tier' => 'free',
        ],
        'active' => true,
    ];

    $this->putJson(route('mixpost.services.update', ['service' => 'twitter']), $data)->assertStatus(302);

    $model = Service::where('name', 'twitter')->first();

    expect($model !== null)->toBeTrue()
        ->and($model->configuration->toArray())->toBeArray()
        ->and($model->configuration->toArray())->toEqual($data['configuration'])
        ->and($model->active)->toBeTrue()
        ->and(ServiceManager::getFromCache('twitter') !== null)->toBeTrue();
});

it('can show validation on update a service', function () {
    $this->actingAs(test()->user);

    $this->putJson(route('mixpost.services.update', ['service' => 'twitter']))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['active', 'configuration.client_id', 'configuration.client_secret']);
});

it('can prevent unauthorized users to update a service', function () {
    $this->putJson(route('mixpost.services.update', ['service' => 'service']))->assertUnauthorized();
});


