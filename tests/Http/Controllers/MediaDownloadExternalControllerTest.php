<?php

use Illuminate\Support\Facades\Http;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it("requires items", function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.media.download'))->assertUnprocessable();
});

it("will not download media from internal url", function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.media.download'), [
        'items' => [
            [
                'url' => 'http://localhost:8000'
            ]
        ]
    ])->assertUnprocessable();
});

it("will start to download media file", function () {
    $this->actingAs(test()->user);

    $url = 'https://publicdomain.example/image.jpg';

    Http::fake();

    $this->postJson(route('mixpost.media.download'), [
        'items' => [
            [
                'url' => $url
            ]
        ]
    ])->assertOk();

    Http::assertSent(function ($request) use ($url) {
        return $request->url() == $url;
    });
});
