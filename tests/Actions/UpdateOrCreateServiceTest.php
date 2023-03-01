<?php

use Inovector\Mixpost\Actions\UpdateOrCreateService;
use Inovector\Mixpost\Models\Service;

it('can store new service', function () {
    $name = 'facebook_page';
    $credentials = ['client_id' => 'my-client-id', 'client_secret' => 'my-client-sercret'];

    $service = (new UpdateOrCreateService())($name, $credentials);

    expect($service)->toBeObject()->and($service->name)->toEqual($name)
        ->and($service->credentials->toArray())->toBeArray()
        ->and($service->credentials->toArray()['client_id'])->toEqual($credentials['client_id'])
        ->and($service->credentials->toArray()['client_secret'])->toEqual($credentials['client_secret']);
});

it('can update a service', function () {
    $service = Service::factory()->create();

    $credentials = ['client_id' => 'my-client-id', 'client_secret' => 'my-client-sercret'];

    $updatedService = (new UpdateOrCreateService())($service->name, $credentials);

    expect($updatedService)->toBeObject()->and($updatedService->name)->toEqual($service->name)
        ->and($updatedService->credentials->toArray())->toBeArray()
        ->and($updatedService->credentials->toArray()['client_id'])->toEqual($credentials['client_id'])
        ->and($updatedService->credentials->toArray()['client_secret'])->toEqual($credentials['client_secret']);
});
