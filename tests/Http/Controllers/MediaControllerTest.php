<?php

use Illuminate\Http\UploadedFile;
use Inovector\Mixpost\Models\Media;
use Inovector\Mixpost\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create();
});

it('will delete media file', function () {
    $this->actingAs(test()->user);

    $file = UploadedFile::fake()->image('image.jpg', 1200, 1200);

    $response = $this->postJson(route('mixpost.media.upload'), [
        'file' => $file,
    ]);

    $response->assertStatus(201);

    $media = Media::find($response->json()['id']);

    $response = $this->deleteJson(route('mixpost.media.delete'), [
        'items' => [$media->id],
    ]);

    $response->assertStatus(204);

    $this->filesystem()->assertMissing($media->path);
    $this->filesystem()->assertMissing($media->getConversion('thumb')['path']);
});
