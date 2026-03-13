<?php

use Illuminate\Http\UploadedFile;
use Inovector\Mixpost\Http\Resources\MediaResource;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('can upload an image', function () {
    $this->actingAs(test()->user);

    $file = UploadedFile::fake()->image('image.jpg', 1200, 1200);

    $response = $this->postJson(route('mixpost.media.upload'), [
        'file' => $file,
    ]);

    $response->assertStatus(201);

    $media = Media::latest('id')->first();

    $this->filesystem()->assertExists($media->path);

    expect($response->json())->toBe((new MediaResource($media))->resolve());
});

it('will resize image', function () {
    $this->actingAs(test()->user);

    $file = UploadedFile::fake()->image('image.jpg', 1200, 1200);

    $response = $this->postJson(route('mixpost.media.upload'), [
        'file' => $file,
    ]);

    $response->assertStatus(201);

    $media = Media::find($response->json()['id']);

    $this->filesystem()->assertExists($media->path);
    $this->filesystem()->assertExists($media->getConversion('thumb')['path']);
});

it('can show validation on upload a file', function () {
    $this->actingAs(test()->user);

    $this->postJson(route('mixpost.media.upload'))
        ->assertStatus(422)
        ->assertJsonValidationErrors('file');

    $file = UploadedFile::fake()->create('image.pdf');

    $this->postJson(route('mixpost.media.upload'), ['file' => $file])
        ->assertStatus(422)
        ->assertJsonValidationErrors('file');
});

it('can prevent unauthorized users to upload a file', function () {
    $this->postJson(route('mixpost.media.upload'))->assertUnauthorized();
});
