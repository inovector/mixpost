<?php

use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Models\Service;

it('can store new service', function () {
    $name = 'facebook_page';
    $data = [
        'configuration' => ['client_id' => 'my-client-id', 'client_secret' => 'my-client-sercret'],
        'active' => true,
    ];

    $service = (new UpdateOrCreateService())($name, $data['configuration'], $data['active']);

    expect($service)->toBeObject()->and($service->name)->toEqual($name)
        ->and($service->active)->toBe($data['active'])
        ->and($service->configuration->toArray())->toBeArray()
        ->and($service->configuration->toArray()['client_id'])->toEqual($data['configuration']['client_id'])
        ->and($service->configuration->toArray()['client_secret'])->toEqual($data['configuration']['client_secret']);
});

it('can update a service', function () {
    $service = Service::factory()->create();

    $data = [
        'configuration' => ['client_id' => 'my-client-id', 'client_secret' => 'my-client-sercret'],
        'active' => true,
    ];

    $updatedService = (new UpdateOrCreateService())($service->name, $data['configuration'], $data['active']);

    $service->refresh();

    expect($updatedService)->toBeObject()->and($updatedService->name)->toEqual($service->name)
        ->and($service->active)->toBe($data['active'])
        ->and($service->configuration->toArray())->toBeArray()
        ->and($service->configuration->toArray()['client_id'])->toEqual($data['configuration']['client_id'])
        ->and($service->configuration->toArray()['client_secret'])->toEqual($data['configuration']['client_secret']);
});
