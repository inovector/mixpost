<?php

use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('fetch uploaded media files', function () {
    $this->actingAs(test()->user);

    Media::factory()->count(10)->state([
        'disk' => config('mixpost.disk')
    ])->create();

    $response = $this->getJson(route('mixpost.media.fetchUploads'));

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data'
    ]);

    expect(count($response->json('data')))->toBe(10);
});

it('fetch uploaded media files with pagination', function () {
    $this->actingAs(test()->user);

    Media::factory()->count(50)->state([
        'disk' => config('mixpost.disk')
    ])->create();

    $response = $this->getJson(route('mixpost.media.fetchUploads'));

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data',
        'links',
        'meta'
    ]);

    expect(count($response->json('data')))->toBe(30)
    ->and($response->json('links')['next'] !== null)->toBeTrue();
});
