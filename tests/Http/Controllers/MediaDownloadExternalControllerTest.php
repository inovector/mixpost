<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Inovector\Mixpost\Integrations\Unsplash\Jobs\TriggerDownloadJob;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});


it("shows validation error", function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.media.download'), [
        'items' => [[]]
    ])->assertUnprocessable()->assertJsonValidationErrors([
        'items',
        'from'
    ]);
});

it("will not download media from internal url", function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.media.download'), [
        'from' => 'stock',
        'items' => [
            [
                'id' => '123',
                'url' => 'http://localhost:8000',
                'download_data' => [
                    'download_location' => 'https://publicdomain.example/image.jpg?download=1'
                ]
            ]
        ]
    ])->assertUnprocessable();
});

it("will start to download media file", function () {
    $this->actingAs(test()->user);

    Queue::fake();
    Http::fake();

    $url = 'https://publicdomain.example/image.jpg';
    $downloadLocation = 'https://publicdomain.example/image.jpg?download=1';

    $this->postJson(route('mixpost.media.download'), [
        'from' => 'stock',
        'items' => [
            [
                'id' => '123',
                'url' => $url,
                'download_data' => [
                    'download_location' => $downloadLocation
                ]
            ]
        ]
    ])->assertOk();

    Http::assertSent(function ($request) use ($url) {
        return $request->url() == $url;
    });

    Queue::assertPushed(TriggerDownloadJob::class, function ($job) use ($downloadLocation) {
        return $job->downloadLocation == $downloadLocation;
    });
});
